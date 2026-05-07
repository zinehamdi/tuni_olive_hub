<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Models\Load;
use App\Models\Pod;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MobileController extends ApiController
{
    protected function ensureCarrier(Request $request): void
    {
        if ($request->user()->role !== 'carrier' && $request->user()->role !== 'admin') {
            abort(403, trans('auth.forbidden_action'));
        }
    }
    public function loadTrip(Request $request, Load $load)
    {
        $trip = $load->activeTrip();
        if (!$trip) return $this->ok(null);
        return $this->showTrip($request, $trip);
    }

    public function showTrip(Request $request, Trip $trip)
    {
        $loads = [];
        foreach ((array) $trip->load_ids as $lid) {
            $ld = Load::with('order.listing.product')->find($lid);
            if (!$ld) continue;
            $loads[] = [
                'id' => $ld->id,
                'pickup' => [ 'lat' => $ld->pickup_lat, 'lng' => $ld->pickup_lng, 'address' => $ld->pickup_address ],
                'dropoff' => [ 'lat' => $ld->dropoff_lat, 'lng' => $ld->dropoff_lng, 'address' => $ld->dropoff_address ],
                'qty' => (float) $ld->qty,
                'unit' => $ld->unit,
                'type' => $ld->order?->listing?->product?->type ?? 'olive',
            ];
        }

        $pin = (string) ($trip->pin_token ?? '');
        $mask = strlen($pin) > 4 ? substr($pin, 0, 4).str_repeat('*', max(0, strlen($pin)-4)) : $pin;

        return $this->ok([
            'id' => $trip->id,
            'sr_code' => $trip->sr_code,
            'loads' => $loads,
            'pin_mask' => $mask,
            'started_at' => optional($trip->start_at)->toISOString(),
            'current_location' => [
                'lat' => $trip->current_lat,
                'lng' => $trip->current_lng,
            ],
            'status' => $trip->delivered_at ? 'delivered' : ($trip->start_at ? 'in_transit' : 'pending'),
        ]);
    }

    public function activeTrip(Request $request)
    {
        $this->ensureCarrier($request);
        $uid = $request->user()->id;
        $trip = Trip::query()
            ->where('carrier_id', $uid)
            ->whereNotNull('start_at')
            ->whereNull('delivered_at')
            ->latest('start_at')->first();
        if (!$trip) return $this->ok(null);

        // Build loads
        $loads = [];
        foreach ((array) $trip->load_ids as $lid) {
            $ld = Load::with('order.listing.product')->find($lid);
            if (!$ld) continue;
            $loads[] = [
                'id'      => $ld->id,
                'pickup'  => ['lat' => $ld->pickup_lat,  'lng' => $ld->pickup_lng,  'address' => $ld->pickup_address],
                'dropoff' => ['lat' => $ld->dropoff_lat, 'lng' => $ld->dropoff_lng, 'address' => $ld->dropoff_address],
                'qty'     => (float) $ld->qty,
                'unit'    => $ld->unit,
                'kind'    => $ld->order?->listing?->product?->type ?? 'olive',
            ];
        }
        $pin  = (string) ($trip->pin_token ?? '');
        $mask = strlen($pin) > 4 ? substr($pin, 0, 4) . str_repeat('*', max(0, strlen($pin) - 4)) : $pin;

        return $this->ok([
            'id'               => $trip->id,
            'sr_code'          => $trip->sr_code,
            'loads'            => $loads,
            'pin_mask'         => $mask,
            'pin_full'         => $pin,   // ← shown only to the authenticated carrier
            'started_at'       => optional($trip->start_at)->toISOString(),
            'current_location' => ['lat' => $trip->current_lat, 'lng' => $trip->current_lng],
            'status'           => $trip->delivered_at ? 'delivered' : ($trip->start_at ? 'in_transit' : 'pending'),
        ]);
    }


    public function podPhoto(Request $request, Trip $trip)
    {
        $this->ensureCarrier($request);
        $user = $request->user();
        if ($user->role !== 'admin' && (int)$trip->carrier_id !== (int)$user->id) abort(403, trans('auth.forbidden_action'));
        $request->validate(['photo' => ['required','file','mimes:jpg,jpeg,png','max:5120']]);
        $photo = $request->file('photo');
    $path = $photo->store('pod/'.date('Y/m/d').'/trip-'.$trip->id, 'public');

        $pod = $trip->pods()->latest('id')->first();
        if (!$pod) { $pod = Pod::create(['trip_id' => $trip->id, 'pickup_photos' => [], 'dropoff_photos' => [] ]); }

        $isFinalLeg = (bool) $trip->delivered_at || $trip->inferredStatus() !== Trip::ST_STARTED; // heuristic
        $arr = $isFinalLeg ? (array) ($pod->dropoff_photos ?? []) : (array) ($pod->pickup_photos ?? []);
        if (count($arr) >= 6) abort(429, 'Too many photos for this trip.');
    $base = rtrim(config('filesystems.disks.public.url'), '/');
    $arr[] = $base.'/'.ltrim($path,'/');
        if ($isFinalLeg) { $pod->dropoff_photos = $arr; } else { $pod->pickup_photos = $arr; }
        $pod->save();
        $this->audit('trip.pod.photo', 'trip', $trip->id);
        $count = count((array) $pod->pickup_photos) + count((array) $pod->dropoff_photos);
        return $this->ok(['count' => $count]);
    }
    public function pendingLoads(Request $request)
    {
        $this->ensureCarrier($request);
        $loads = Load::with(['owner', 'pickup', 'dropoffAddress'])
            ->where('carrier_id', $request->user()->id)
            ->where('status', Load::ST_MATCHED)
            ->get();
        
        $formatted = $loads->map(fn($ld) => [
            'id' => $ld->id,
            'kind' => $ld->kind,
            'qty' => (float) $ld->qty,
            'unit' => $ld->unit,
            'owner_name' => $ld->owner->name,
            'pickup' => $ld->pickup?->governorate . ', ' . $ld->pickup?->delegation,
            'dropoff' => $ld->dropoffAddress?->governorate . ', ' . $ld->dropoffAddress?->delegation,
        ]);

        return $this->ok($formatted);
    }

    public function acceptLoad(Request $request, Load $load)
    {
        $this->ensureCarrier($request);
        if ($load->carrier_id !== $request->user()->id) abort(403);
        if ($load->status !== Load::ST_MATCHED) abort(400, 'Load already processed.');

        $load->update(['status' => Load::ST_IN_TRANSIT]);

        // Create a Trip for this load
        $trip = Trip::create([
            'carrier_id' => $request->user()->id,
            'load_ids' => [$load->id],
            'sr_code' => 'SR-' . strtoupper(bin2hex(random_bytes(4))),
            'pin_token' => Trip::generatePin(),
            'start_at' => now(),
        ]);

        return $this->ok(['trip_id' => $trip->id]);
    }

    public function rejectLoad(Request $request, Load $load)
    {
        $this->ensureCarrier($request);
        if ($load->carrier_id !== $request->user()->id) abort(403);
        if ($load->status !== Load::ST_MATCHED) abort(400, 'Load already processed.');

        $load->update([
            'status' => Load::ST_NEW,
            'carrier_id' => null
        ]);

        return $this->ok(true);
    }

    public function updateLocation(Request $request, Trip $trip)
    {
        $this->ensureCarrier($request);
        if ((int)$trip->carrier_id !== (int)$request->user()->id) abort(403);
        
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        $trip->update([
            'current_lat' => $request->lat,
            'current_lng' => $request->lng,
        ]);

        return $this->ok(true);
    }
}
