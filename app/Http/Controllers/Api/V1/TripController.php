<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\TripResource;
use App\Models\Load;
use App\Models\Pod;
use App\Models\Trip;
use Illuminate\Http\Request;
use App\Http\Requests\V1\TripStoreRequest;
use App\Http\Requests\V1\TripPodRequest;
use App\Http\Requests\V1\TripCompleteRequest;
use App\Events\TripStarted;
use App\Events\TripDelivered;
use App\Events\OrderPaid;
use App\Services\Chat;
use App\Models\MillStorageMovement;
use App\Services\Trust\TrustEngine;
use App\Models\Financing;

class TripController extends ApiController
{
    public function store(TripStoreRequest $request)
    {
        $this->authorize('create', Trip::class);
        $data = $request->validated();
        $trip = Trip::create([
            'carrier_id' => $data['carrier_id'],
            'load_ids' => $data['load_ids'] ?? [],
            'sr_code' => \App\Services\SrCode::make('TRP'),
        ]);
        $this->audit('trip.created', 'trip', $trip->id);
        $thread = Chat::ensureThread('trip', $trip->id, [$trip->carrier_id]);
        Chat::system($thread, 'تم إنشاء رحلة جديدة.');
        return $this->ok(new TripResource($trip), 201);
    }

    public function start(Request $request, Trip $trip)
    {
        $this->authorize('update', $trip);
        if ((int)$request->user()->id !== (int)$trip->carrier_id && $request->user()->role !== 'admin') {
            abort(403, trans('auth.forbidden_action'));
        }
        if ($trip->start_at) {
            abort(422, 'Trip already started.');
        }
    $trip->start_at = now();
    // Generate pin and QR token
    $pin = Trip::generatePin();
    $trip->pin_hash = Trip::hashPin($pin);
    $trip->pin_qr = bin2hex(random_bytes(8));
    $trip->pin_token = substr($pin, 0, 5).'****'; // masked for UI only; raw pin not stored
    $trip->save();
        // move loads to in_transit
        foreach ((array) $trip->load_ids as $id) {
            if ($load = Load::find($id)) {
                if ($load->status === Load::ST_MATCHED) {
                    $load->moveTo(Load::ST_IN_TRANSIT);
                }
            }
        }
        $this->audit('trip.started', 'trip', $trip->id);
        event(new TripStarted($trip->id, $trip->carrier_id, $trip->sr_code));
        $thread = Chat::ensureThread('trip', $trip->id, [$trip->carrier_id]);
        Chat::system($thread, '🚛 انطلقت الرحلة SR:'.$trip->sr_code.' | PIN: '.$pin);
        return $this->ok(new TripResource($trip->fresh()));
    }

    public function submitPod(TripPodRequest $request, Trip $trip)
    {
        $this->authorize('update', $trip);
        $data = $request->validated();
        $pod = Pod::create([
            'trip_id' => $trip->id,
            'pickup_photos' => $data['pickup_photos'] ?? null,
            'dropoff_photos' => $data['dropoff_photos'] ?? null,
            'signed_name' => $data['signed_name'] ?? null,
            'signed_pin' => $data['signed_pin'] ?? null,
            'qr_token' => $data['qr_token'] ?? null,
        ]);
        // Verify pin or QR
        $ok = false;
        if (!empty($data['signed_pin'])) {
            $ok = hash_equals($trip->pin_hash ?? '', Trip::hashPin((string) $data['signed_pin']));
        } elseif (!empty($data['qr_token'])) {
            $ok = hash_equals((string) $trip->pin_qr, (string) $data['qr_token']);
        }
        if ($ok) {
            $pod->verified_at = now();
            $pod->save();
            $thread = Chat::ensureThread('trip', $trip->id, [$trip->carrier_id]);
            Chat::system($thread, '🧾 تم التحقق من POD');
        }
        $this->audit('pod.submitted', 'pod', $pod->id);
        return $this->ok($pod, 201);
    }

    public function complete(TripCompleteRequest $request, Trip $trip)
    {
        $this->authorize('update', $trip);
        if ($trip->delivered_at) {
            abort(422, 'Trip already completed.');
        }
        $pod = $trip->pods()->latest('id')->first();
        if (!$pod || !$pod->verified_at) {
            abort(422, trans('micro.pod_required'));
        }
        $trip->delivered_at = now();
        $trip->distance_km = $request->validated()['distance_km'] ?? $trip->distance_km;
        $trip->save();
        foreach ((array) $trip->load_ids as $id) {
            if ($load = Load::find($id)) {
                if ($load->status === Load::ST_IN_TRANSIT) {
                    $load->moveTo(Load::ST_DELIVERED);
                }
                // Storage movements if delivered to a mill address
                $millId = optional($load->dropoffAddress)->user_id ?? null;
                if (!$millId && $load->dropoff_addr_id) {
                    // Fallback: find a mill that has this address as default_mill_addr_id
                    $millId = optional(\App\Models\User::where('default_mill_addr_id', $load->dropoff_addr_id)->first())->id;
                }
                if ($millId && $load->kind === 'olive') {
                    // in olive
                    MillStorageMovement::create([
                        'mill_id' => $millId,
                        'product' => 'olive',
                        'type' => 'in',
                        'qty' => $load->qty,
                        'unit' => 'kg',
                        'ref_object_type' => 'trip',
                        'ref_object_id' => $trip->id,
                        'note' => 'Load delivered',
                        'created_at' => now(),
                    ]);
                    // simulate processing: out olive & in oil
                    $oilQty = round((float)$load->qty * 0.22, 3);
                    MillStorageMovement::insert([
                        [
                            'mill_id' => $millId,
                            'product' => 'olive',
                            'type' => 'out',
                            'qty' => $load->qty,
                            'unit' => 'kg',
                            'ref_object_type' => 'process',
                            'ref_object_id' => $trip->id,
                            'note' => 'Processing',
                            'created_at' => now(),
                        ],[
                            'mill_id' => $millId,
                            'product' => 'oil',
                            'type' => 'in',
                            'qty' => $oilQty,
                            'unit' => 'l',
                            'ref_object_type' => 'process',
                            'ref_object_id' => $trip->id,
                            'note' => 'Processing',
                            'created_at' => now(),
                        ],
                    ]);
                    // Auto-accumulate financing delivered qty (latest active for pair)
                    $fin = Financing::query()
                        ->where('financer_id', $millId)
                        ->where('carrier_id', $trip->carrier_id)
                        ->where('status', 'active')
                        ->latest('id')->first();
                    if ($fin) {
                        $fin->delivered_qty = (float)$fin->delivered_qty + (float)$load->qty;
                        $fin->save();
                    }
                }
                // If load is attached to an order with COD, mark as collected on delivery
                if ($load->order && $load->order->payment_method === 'cod') {
                    $order = $load->order;
                    if ($order->payment_status !== \App\Models\Order::PAY_COLLECTED) {
                        $order->payment_status = \App\Models\Order::PAY_COLLECTED;
                        $order->save();
                        event(new OrderPaid($order->id, 'cod:trip_complete'));
                        // Trust: completed_order for buyer and seller
                        app(TrustEngine::class)->record($order->buyer_id, 'completed_order');
                        app(TrustEngine::class)->record($order->seller_id, 'completed_order');
                    }
                }
            }
        }
        // On-time delivery trust for carrier if ETA met (TODO: get ETA from trip/meta; assume on-time for now)
        app(TrustEngine::class)->record($trip->carrier_id, 'on_time_delivery');
        $this->audit('trip.completed', 'trip', $trip->id);
        event(new TripDelivered($trip->id, $trip->carrier_id));
        $thread = Chat::ensureThread('trip', $trip->id, [$trip->carrier_id]);
        Chat::system($thread, '✅ تم التسليم وتثبيت POD');
        return $this->ok(new TripResource($trip->fresh()));
    }
}
