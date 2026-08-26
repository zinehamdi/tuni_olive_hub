<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;
use App\Models\Load;
use App\Models\Trip;
use App\Models\Address;
use App\Models\Thread;
use App\Models\Message;
use App\Services\Chat;
use App\Services\TransportPricingService;
use App\Services\EzZitouniDealMediator;
use App\Notifications\NewTransportDeal;
use App\Notifications\DeliveryPinNotification;

class LoadController extends Controller
{
    /**
     * Assign a Carrier to an Order (Create a Load and generate PIN)
     */
    public function summon(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'carrier_id' => 'required|exists:users,id',
        ]);

        $order = Order::with('listing.product', 'seller.addresses', 'buyer.addresses')->findOrFail($request->order_id);
        $carrier = User::findOrFail($request->carrier_id);
        $user = auth()->user();

        // Ensure the auth user is buyer or seller
        if (!in_array((int)$user->id, [(int)$order->buyer_id, (int)$order->seller_id], true) && $user->role !== 'admin') {
            return response()->json(['success' => false, 'message' => __('Unauthorized')], 403);
        }

        // Auto-assign addresses (or create placeholders)
        $pickup = $order->seller->addresses->first() ?? Address::create([
            'user_id' => $order->seller_id,
            'governorate' => $order->seller->governorate ?? 'Sfax',
            'delegation' => $order->seller->delegation ?? null,
            'label' => 'Default Pickup',
            'lat' => null,
            'lng' => null,
        ]);

        $dropoff = $order->buyer->addresses->first() ?? Address::create([
            'user_id' => $order->buyer_id,
            'governorate' => $order->buyer->governorate ?? 'Tunis',
            'delegation' => $order->buyer->delegation ?? null,
            'label' => 'Default Dropoff',
            'lat' => null,
            'lng' => null,
        ]);

        // Calculate fair transport pricing estimate
        $estimate = TransportPricingService::estimateCost(
            (float) $order->qty,
            $pickup->lat ? (float) $pickup->lat : null,
            $pickup->lng ? (float) $pickup->lng : null,
            $dropoff->lat ? (float) $dropoff->lat : null,
            $dropoff->lng ? (float) $dropoff->lng : null,
            $order->listing?->product?->type ?? 'oil'
        );

        // Generate 4-digit numeric verification PIN
        $pinCode = sprintf("%04d", mt_rand(1000, 9999));

        // Create Load
        $load = Load::create([
            'owner_id' => $user->id,
            'kind' => $order->listing?->product?->type ?? 'oil',
            'qty' => $order->qty,
            'unit' => $order->unit,
            'pickup_addr_id' => $pickup->id,
            'dropoff_addr_id' => $dropoff->id,
            'status' => Load::ST_MATCHED,
            'order_id' => $order->id,
            'carrier_id' => $carrier->id,
            'price_floor' => $estimate['cost_range']['min'],
            'price_ceiling' => $estimate['cost_range']['max'],
            'meta' => [
                'pin_code' => $pinCode,
                'estimated_cost' => $estimate['total_cost'],
                'distance_km' => $estimate['distance_km'],
                'tier_name' => $estimate['tier']['name_ar']
            ]
        ]);

        // Create initial Trip record
        $trip = Trip::create([
            'carrier_id' => $carrier->id,
            'load_ids' => [$load->id],
            'sr_code' => 'SR-' . strtoupper(bin2hex(random_bytes(4))),
            'pin_token' => $pinCode,
            'start_at' => now(),
        ]);

        // 1. Send immediate push & in-app database notification to Carrier
        $carrier->notify(new NewTransportDeal($load, $user));

        // 2. Send PIN verification email & in-app database notification to Buyer
        if ($order->buyer) {
            $order->buyer->notify(new DeliveryPinNotification($load, $pinCode, $carrier));
        }

        // 3. Ensure direct chat thread exists between client ($user) and carrier ($carrier)
        $carrierThread = Thread::where('object_type', 'direct_message')
            ->whereJsonContains('participants', (int)$user->id)
            ->whereJsonContains('participants', (int)$carrier->id)
            ->first();

        if (!$carrierThread) {
            $carrierThread = Thread::create([
                'object_type' => 'direct_message',
                'object_id' => $user->id,
                'participants' => [(int)$user->id, (int)$carrier->id],
            ]);
        }

        $pickupGov = $pickup->governorate ?? __('Pickup Location');
        $dropoffGov = $dropoff->governorate ?? __('Dropoff Location');
        $productName = $order->listing?->product?->name ?? ($load->kind === 'olive' ? __('Olives') : __('Olive Oil'));

        // Post automated assignment message in carrier's direct chat
        Message::create([
            'thread_id' => $carrierThread->id,
            'sender_id' => $user->id,
            'body' => "🚚 **" . __('New Transport Mission Assigned') . " (#{$load->id})**\n\n" .
                      __('Hello') . " {$carrier->name}، " . __('you have been assigned a new transport mission for') . " **{$load->qty} " . __($load->unit) . " " . __('of') . " {$productName}** " .
                      __('from') . " **{$pickupGov}** " . __('to') . " **{$dropoffGov}**.\n" .
                      "💰 " . __('Estimated Transport Cost:') . " **~{$estimate['total_cost']} " . __('TND') . "**.\n\n" .
                      "🤝 " . __('Please reply here to coordinate timing and pickup details.'),
            'meta' => [
                'type' => 'transport_assignment',
                'load_id' => $load->id,
                'order_id' => $order->id,
                'trip_id' => $trip->id,
            ]
        ]);

        // 4. Post Ez-Zitouni AI mediation & PIN guide in the deal chat thread
        $thread = Chat::ensureThread('order', $order->id, [$order->buyer_id, $order->seller_id]);
        EzZitouniDealMediator::onTransporterSummoned($thread, $load, $carrier->name, $pinCode, $estimate['total_cost']);

        return response()->json([
            'success' => true,
            'message' => __('Transporter summoned successfully.'),
            'load_id' => $load->id,
            'pin_code' => $pinCode,
            'estimated_cost' => $estimate['total_cost'],
            'distance_km' => $estimate['distance_km']
        ]);
    }
}
