<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Load;
use App\Models\Trip;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TripPublicTrackingController extends Controller
{
    /**
     * Show the public, read-only live tracking page for a delivery trip.
     */
    public function show(Request $request, string $token): View
    {
        $trip = Trip::with('carrier')
            ->where('sr_code', $token)
            ->orWhere('id', is_numeric($token) ? (int)$token : 0)
            ->firstOrFail();

        $load = null;
        if (!empty($trip->load_ids) && is_array($trip->load_ids)) {
            $load = Load::with(['order.listing.product', 'order.buyer', 'order.seller', 'pickupAddress', 'dropoffAddress'])
                ->find($trip->load_ids[0]);
        }

        $pickupLat = $load ? $load->pickup_lat : 34.7406;
        $pickupLng = $load ? $load->pickup_lng : 10.7603;
        $dropoffLat = $load ? $load->dropoff_lat : 36.8065;
        $dropoffLng = $load ? $load->dropoff_lng : 10.1815;

        $currentLat = $trip->current_lat ?: $pickupLat;
        $currentLng = $trip->current_lng ?: $pickupLng;

        $status = $trip->delivered_at ? 'delivered' : ($trip->start_at ? 'in_transit' : 'pending');

        return view('public.tracking', [
            'trip' => $trip,
            'load' => $load,
            'carrier' => $trip->carrier,
            'pickupLat' => $pickupLat,
            'pickupLng' => $pickupLng,
            'dropoffLat' => $dropoffLat,
            'dropoffLng' => $dropoffLng,
            'currentLat' => $currentLat,
            'currentLng' => $currentLng,
            'status' => $status,
            'token' => $token,
        ]);
    }

    /**
     * JSON polling endpoint for live map coordinates updates.
     */
    public function liveLocation(string $token): JsonResponse
    {
        $trip = Trip::where('sr_code', $token)
            ->orWhere('id', is_numeric($token) ? (int)$token : 0)
            ->first();

        if (!$trip) {
            return response()->json(['success' => false, 'message' => 'Trip not found'], 404);
        }

        $status = $trip->delivered_at ? 'delivered' : ($trip->start_at ? 'in_transit' : 'pending');

        return response()->json([
            'success' => true,
            'current_lat' => $trip->current_lat,
            'current_lng' => $trip->current_lng,
            'status' => $status,
            'delivered_at' => $trip->delivered_at?->toIso8601String(),
            'start_at' => $trip->start_at?->toIso8601String(),
            'distance_km' => $trip->distance_km,
            'updated_at' => $trip->updated_at?->diffForHumans(),
        ]);
    }
}
