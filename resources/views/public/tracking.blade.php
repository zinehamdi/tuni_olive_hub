@extends('layouts.app')

@php
    $isRTL = app()->getLocale() === 'ar';
    $productName = $load?->order?->listing?->product?->variety ?? ($load?->kind === 'olive' ? __('Olives') : __('Olive Oil'));
    $qty = $load ? "{$load->qty} " . __($load->unit) : '';
    $pickupLabel = $load?->pickup_address ?? ($load?->pickup?->governorate . ' - ' . $load?->pickup?->delegation);
    $dropoffLabel = $load?->dropoff_address ?? ($load?->dropoffAddress?->governorate . ' - ' . $load?->dropoffAddress?->delegation);
@endphp

@section('title', __('Live Shipment Tracking') . ' - ' . $trip->sr_code)

@section('content')
<div dir="{{ $isRTL ? 'rtl' : 'ltr' }}" class="min-h-screen bg-gradient-to-b from-gray-50 via-white to-gray-100 py-6 px-3 sm:px-6">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <div class="max-w-3xl mx-auto space-y-6">

        {{-- Top Header --}}
        <div class="bg-white rounded-3xl p-5 shadow-sm border border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-[#6A8F3B] to-[#55732f] text-white flex items-center justify-center text-2xl shadow-md">
                    🚚
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-lg sm:text-xl font-black text-gray-900">{{ __('Live Shipment Tracking') }}</h1>
                        <span class="px-2.5 py-0.5 bg-blue-50 text-blue-700 border border-blue-200 rounded-full text-xs font-mono font-bold">{{ $trip->sr_code }}</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-0.5">{{ __('Real-time olive oil & harvest delivery monitoring') }}</p>
                </div>
            </div>

            {{-- Live Status Badge --}}
            <div class="flex items-center gap-2">
                @if($status === 'delivered')
                    <span class="px-4 py-2 bg-emerald-100 text-emerald-800 rounded-2xl font-bold text-xs sm:text-sm flex items-center gap-2 border border-emerald-200 shadow-sm">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                        {{ __('Delivered Successfully') }} ✅
                    </span>
                @elseif($status === 'in_transit')
                    <span class="px-4 py-2 bg-blue-100 text-blue-800 rounded-2xl font-bold text-xs sm:text-sm flex items-center gap-2 border border-blue-200 shadow-sm animate-pulse">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                        {{ __('In Transit - On the Way') }} 🚛
                    </span>
                @else
                    <span class="px-4 py-2 bg-amber-100 text-amber-800 rounded-2xl font-bold text-xs sm:text-sm flex items-center gap-2 border border-amber-200 shadow-sm">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-600"></span>
                        {{ __('Carrier Summoned / Preparing') }} ⏳
                    </span>
                @endif
            </div>
        </div>

        {{-- Live Leaflet Map --}}
        <div class="bg-white rounded-3xl p-4 shadow-lg border border-gray-100 overflow-hidden relative">
            <div class="flex items-center justify-between mb-3 px-2">
                <div class="flex items-center gap-2 text-xs font-bold text-gray-700">
                    <span class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                    </span>
                    <span>{{ __('Live GPS Broadcast') }}</span>
                </div>
                <span id="last-sync-time" class="text-[11px] text-gray-400">{{ __('Syncing location...') }}</span>
            </div>

            <div id="live-map" class="h-72 sm:h-96 w-full rounded-2xl z-10 border border-gray-100"></div>
        </div>

        {{-- Journey Stepper --}}
        <div class="bg-white rounded-3xl p-5 shadow-sm border border-gray-100">
            <div class="grid grid-cols-3 gap-2 text-center">
                {{-- Step 1 --}}
                <div class="flex flex-col items-center">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-sm mb-1.5 {{ $status !== 'pending' ? 'bg-emerald-500 text-white' : 'bg-blue-600 text-white' }}">
                        ✓
                    </div>
                    <span class="text-xs font-bold text-gray-800">{{ __('Carrier Assigned') }}</span>
                    <span class="text-[10px] text-gray-400">{{ $trip->created_at->translatedFormat('H:i') }}</span>
                </div>

                {{-- Step 2 --}}
                <div class="flex flex-col items-center">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-sm mb-1.5 {{ in_array($status, ['in_transit', 'delivered']) ? 'bg-emerald-500 text-white ring-4 ring-emerald-100' : 'bg-gray-200 text-gray-500' }}">
                        🚛
                    </div>
                    <span class="text-xs font-bold {{ in_array($status, ['in_transit', 'delivered']) ? 'text-gray-800' : 'text-gray-400' }}">{{ __('In Transit') }}</span>
                    <span class="text-[10px] text-gray-400">{{ $trip->start_at ? $trip->start_at->translatedFormat('H:i') : '--:--' }}</span>
                </div>

                {{-- Step 3 --}}
                <div class="flex flex-col items-center">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-sm mb-1.5 {{ $status === 'delivered' ? 'bg-emerald-500 text-white ring-4 ring-emerald-100' : 'bg-gray-200 text-gray-500' }}">
                        🏁
                    </div>
                    <span class="text-xs font-bold {{ $status === 'delivered' ? 'text-emerald-700' : 'text-gray-400' }}">{{ __('Delivered') }}</span>
                    <span class="text-[10px] text-gray-400">{{ $trip->delivered_at ? $trip->delivered_at->translatedFormat('H:i') : '--:--' }}</span>
                </div>
            </div>
        </div>

        {{-- Cargo & Route Details Card --}}
        <div class="bg-white rounded-3xl p-5 shadow-sm border border-gray-100 space-y-4">
            <h3 class="font-bold text-gray-900 text-sm flex items-center gap-2 border-b border-gray-100 pb-3">
                <span>📦</span>
                <span>{{ __('Shipment Details') }}</span>
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Cargo Info --}}
                <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100">
                    <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block mb-1">{{ __('Cargo & Quantity') }}</span>
                    <div class="font-black text-gray-900 text-base">{{ $qty }} — {{ $productName }}</div>
                    @if($load?->order)
                        <div class="text-xs text-gray-500 mt-1">{{ __('Order #') }}{{ $load->order->id }}</div>
                    @endif
                </div>

                {{-- Distance & Route Info --}}
                <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100">
                    <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block mb-1">{{ __('Route Distance') }}</span>
                    <div class="font-black text-gray-900 text-base">~{{ $trip->distance_km ?? ($load?->meta['distance_km'] ?? '---') }} km</div>
                    <div class="text-xs text-[#6A8F3B] font-bold mt-1">🇹🇳 {{ __('Direct transport across Tunisia') }}</div>
                </div>
            </div>

            {{-- Pickup & Dropoff Labels --}}
            <div class="space-y-3 pt-2">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold flex-shrink-0 text-sm border border-emerald-200">
                        📍
                    </div>
                    <div>
                        <span class="text-[11px] font-bold text-gray-400 block">{{ __('Pickup Location (Departure)') }}</span>
                        <p class="text-xs sm:text-sm font-bold text-gray-800">{{ $pickupLabel ?: __('Origin Point') }}</p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-xl bg-red-50 text-red-600 flex items-center justify-center font-bold flex-shrink-0 text-sm border border-red-200">
                        🚩
                    </div>
                    <div>
                        <span class="text-[11px] font-bold text-gray-400 block">{{ __('Dropoff Location (Destination)') }}</span>
                        <p class="text-xs sm:text-sm font-bold text-gray-800">{{ $dropoffLabel ?: __('Destination Point') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Assigned Carrier Card --}}
        @if($carrier)
        <div class="bg-white rounded-3xl p-5 shadow-sm border border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3 w-full sm:w-auto">
                @if($carrier->profile_picture)
                    <img src="{{ Storage::url($carrier->profile_picture) }}" class="w-14 h-14 rounded-2xl object-cover shadow-sm">
                @else
                    <div class="w-14 h-14 rounded-2xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-2xl shadow-sm">
                        {{ strtoupper(substr($carrier->name, 0, 1)) }}
                    </div>
                @endif
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h4 class="font-bold text-gray-900 text-base">{{ $carrier->name }}</h4>
                        @if($carrier->is_verified)
                            <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full text-[11px] font-bold flex items-center gap-1 shadow-sm">
                                <span>✓</span>
                                <span>{{ __('Verified Carrier') }} 🛡️</span>
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-500 mt-0.5">{{ __('Authorized transporter for this mission') }}</p>
                </div>
            </div>

            {{-- Carrier Contacts --}}
            <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                @if($carrier->phone)
                    <a href="tel:{{ $carrier->phone }}" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs sm:text-sm font-bold flex items-center gap-1.5 transition shadow-sm">
                        <span>📞</span>
                        <span>{{ __('Call Driver') }}</span>
                    </a>

                    @php
                        $cleanPhone = preg_replace('/[^0-9]/', '', (string)$carrier->phone);
                        if (strlen($cleanPhone) === 8) $cleanPhone = '216' . $cleanPhone;
                        $waText = rawurlencode("سلام سي {$carrier->name}، نتبع في مسار الشحنة عبر ZinToop (كود {$trip->sr_code}). وين وصلت؟");
                    @endphp
                    <a href="https://wa.me/{{ $cleanPhone }}?text={{ $waText }}" target="_blank" class="px-4 py-2.5 bg-green-500 hover:bg-green-600 text-white rounded-xl text-xs sm:text-sm font-bold flex items-center gap-1.5 transition shadow-sm">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.664-.699c.971.53 1.771.815 2.796.815 3.183 0 5.768-2.587 5.769-5.767.001-3.182-2.585-5.77-5.769-5.77zm3.377 8.219c-.144.405-.837.774-1.17.824-.312.045-.698.077-1.118-.057-.267-.085-.609-.204-1.053-.396-1.859-.806-3.076-2.695-3.169-2.819-.093-.124-.755-1.004-.755-1.916s.478-1.359.648-1.545c.17-.186.371-.233.494-.233.124 0 .248.001.356.006.114.005.267-.043.417.319.155.371.528 1.285.574 1.378.046.093.077.202.015.326-.062.124-.093.202-.186.31-.093.109-.196.243-.28.326-.093.093-.191.196-.082.382.109.186.483.797 1.036 1.29 1.135.708 1.488.928 1.7.99.144.041.289.02.392-.041.134-.082.573-.667.727-.895.155-.227.309-.191.52-.113.211.077 1.336.63 1.564.743.227.114.378.17.433.267.055.097.055.563-.089.968z"/></svg>
                        <span>WhatsApp</span>
                    </a>
                @endif
            </div>
        </div>
        @endif

        {{-- Buyer Safety PIN Notice --}}
        <div class="bg-gradient-to-r from-amber-50 to-orange-50 rounded-3xl p-5 border border-amber-200 text-amber-900 shadow-sm flex items-start gap-3">
            <span class="text-2xl flex-shrink-0">🔐</span>
            <div class="text-xs sm:text-sm space-y-1">
                <h5 class="font-bold text-amber-900">{{ __('Buyer Protection Guarantee') }}</h5>
                <p class="text-amber-800 leading-relaxed">
                    {{ __('Keep your secret Delivery PIN safe. Only give this code to the driver AFTER inspecting and accepting your merchandise in person.') }}
                </p>
                <p class="text-[11px] text-amber-700 font-medium">
                    {{ __('Your PIN code was sent to your registered email and notifications in your ZinToop account.') }}
                </p>
            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const pickupCoords = [{{ $pickupLat }}, {{ $pickupLng }}];
    const dropoffCoords = [{{ $dropoffLat }}, {{ $dropoffLng }}];
    let truckCoords = [{{ $currentLat }}, {{ $currentLng }}];

    const map = L.map('live-map').setView(truckCoords, 8);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18,
        attribution: '© OpenStreetMap contributors | ZinToop Logistics'
    }).addTo(map);

    // Custom Icon Helpers
    function createIcon(emoji, bgClass) {
        return L.divIcon({
            html: `<div style="background: white; border: 2px solid ${bgClass}; border-radius: 50%; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; font-size: 18px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">${emoji}</div>`,
            className: 'custom-map-pin',
            iconSize: [36, 36],
            iconAnchor: [18, 18],
        });
    }

    // Pickup Marker
    const pickupMarker = L.marker(pickupCoords, { icon: createIcon('📍', '#10B981') })
        .addTo(map)
        .bindPopup('<b>{{ __("Pickup Location") }}</b><br>{{ $pickupLabel }}');

    // Dropoff Marker
    const dropoffMarker = L.marker(dropoffCoords, { icon: createIcon('🚩', '#EF4444') })
        .addTo(map)
        .bindPopup('<b>{{ __("Delivery Location") }}</b><br>{{ $dropoffLabel }}');

    // Truck Marker
    const truckIcon = L.divIcon({
        html: `<div style="background: #2563EB; border: 3px solid white; border-radius: 50%; width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; font-size: 22px; box-shadow: 0 8px 15px rgba(37, 99, 235, 0.4); animation: pulse 2s infinite;">🚚</div>`,
        className: 'custom-truck-pin',
        iconSize: [44, 44],
        iconAnchor: [22, 22],
    });

    const truckMarker = L.marker(truckCoords, { icon: truckIcon })
        .addTo(map)
        .bindPopup('<b>{{ __("Carrier Live Location") }}</b><br>{{ $carrier?->name }}');

    // Route Polyline
    const routeLine = L.polyline([pickupCoords, truckCoords, dropoffCoords], {
        color: '#6A8F3B',
        weight: 4,
        opacity: 0.8,
        dashArray: '8, 8'
    }).addTo(map);

    // Fit bounds to show entire journey
    const bounds = L.latLngBounds([pickupCoords, dropoffCoords, truckCoords]);
    map.fitBounds(bounds, { padding: [50, 50] });

    // Live Polling every 12 seconds
    const token = '{{ $token }}';
    const syncLabel = document.getElementById('last-sync-time');

    async function pollLiveLocation() {
        try {
            const res = await fetch(`/api/track/${token}/live`);
            if (!res.ok) return;
            const data = await res.json();
            if (data.success && data.current_lat && data.current_lng) {
                const newLat = parseFloat(data.current_lat);
                const newLng = parseFloat(data.current_lng);

                truckCoords = [newLat, newLng];
                truckMarker.setLatLng(truckCoords);

                // Update polyline
                routeLine.setLatLngs([pickupCoords, truckCoords, dropoffCoords]);

                if (syncLabel) {
                    const now = new Date();
                    syncLabel.textContent = '{{ __("Updated at") }} ' + now.toLocaleTimeString();
                }
            }
        } catch (e) {
            console.warn('Live tracking poll failed', e);
        }
    }

    setInterval(pollLiveLocation, 12000);
});
</script>
@endsection
