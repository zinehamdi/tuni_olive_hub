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
            'pickup_governorate' => 'nullable|string|max:100',
            'pickup_delegation' => 'nullable|string|max:100',
            'pickup_lat' => 'nullable|numeric',
            'pickup_lng' => 'nullable|numeric',
            'dropoff_governorate' => 'nullable|string|max:100',
            'dropoff_delegation' => 'nullable|string|max:100',
            'dropoff_lat' => 'nullable|numeric',
            'dropoff_lng' => 'nullable|numeric',
        ]);

        $order = Order::with('listing.product', 'seller.addresses', 'buyer.addresses')->findOrFail($request->order_id);
        $carrier = User::findOrFail($request->carrier_id);
        $user = auth()->user();

        // Ensure the auth user is buyer or seller
        if (!in_array((int)$user->id, [(int)$order->buyer_id, (int)$order->seller_id], true) && $user->role !== 'admin') {
            return response()->json(['success' => false, 'message' => __('Unauthorized')], 403);
        }

        // 1. Resolve Pickup Coordinates & Address
        $sellerAddr = $order->seller?->addresses->first();
        $pGov = $request->input('pickup_governorate') ?: ($sellerAddr?->governorate ?: ($order->seller?->governorate ?: 'صفاقس'));
        $pDel = $request->input('pickup_delegation') ?: ($sellerAddr?->delegation ?: ($order->seller?->delegation ?: ''));
        $pLatInput = $request->filled('pickup_lat') ? (float)$request->input('pickup_lat') : (float)($sellerAddr?->lat ?? 0);
        $pLngInput = $request->filled('pickup_lng') ? (float)$request->input('pickup_lng') : (float)($sellerAddr?->lng ?? 0);
        $pCoords = TransportPricingService::resolveCoordinates($pGov, $pLatInput, $pLngInput);

        $pickupLabel = $pDel ? "{$pGov} ({$pDel})" : $pGov;
        $pickup = Address::create([
            'user_id' => $order->seller_id,
            'governorate' => $pGov,
            'delegation' => $pDel ?: null,
            'label' => $pickupLabel,
            'lat' => $pCoords['lat'],
            'lng' => $pCoords['lng'],
        ]);

        // 2. Resolve Dropoff Coordinates & Address
        $buyerAddr = $order->buyer?->addresses->first();
        $dGov = $request->input('dropoff_governorate') ?: ($buyerAddr?->governorate ?: ($order->buyer?->governorate ?: 'تونس'));
        $dDel = $request->input('dropoff_delegation') ?: ($buyerAddr?->delegation ?: ($order->buyer?->delegation ?: ''));
        $dLatInput = $request->filled('dropoff_lat') ? (float)$request->input('dropoff_lat') : (float)($buyerAddr?->lat ?? 0);
        $dLngInput = $request->filled('dropoff_lng') ? (float)$request->input('dropoff_lng') : (float)($buyerAddr?->lng ?? 0);
        $dCoords = TransportPricingService::resolveCoordinates($dGov, $dLatInput, $dLngInput);

        $dropoffLabel = $dDel ? "{$dGov} ({$dDel})" : $dGov;
        $dropoff = Address::create([
            'user_id' => $order->buyer_id,
            'governorate' => $dGov,
            'delegation' => $dDel ?: null,
            'label' => $dropoffLabel,
            'lat' => $dCoords['lat'],
            'lng' => $dCoords['lng'],
        ]);

        // Calculate fair transport pricing estimate using accurate coordinates
        $estimate = TransportPricingService::estimateCost(
            (float) $order->qty,
            $pCoords['lat'],
            $pCoords['lng'],
            $dCoords['lat'],
            $dCoords['lng'],
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
                'tier_name' => $estimate['tier']['name_ar'] ?? 'شاحنة',
                'pickup_label' => $pickupLabel,
                'dropoff_label' => $dropoffLabel,
            ]
        ]);

        // Create initial Trip record
        $trip = Trip::create([
            'carrier_id' => $carrier->id,
            'load_ids' => [$load->id],
            'sr_code' => 'SR-' . strtoupper(bin2hex(random_bytes(4))),
            'pin_token' => $pinCode,
            'start_at' => now(),
            'current_lat' => $pCoords['lat'],
            'current_lng' => $pCoords['lng'],
        ]);

        // 1. Send immediate push & in-app database notification to Carrier
        $carrier->notify(new NewTransportDeal($load, $user));

        // 2. Send PIN verification email & in-app database notification to Buyer
        if ($order->buyer) {
            $order->buyer->notify(new DeliveryPinNotification($load, $pinCode, $carrier, $trip->sr_code));
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

        $productName = $order->listing?->product?->variety ?? ($load->kind === 'olive' ? 'زيتون' : 'زيت زيتون');
        $cLocale = $carrier->locale ?? 'ar';
        $body = match($cLocale) {
            'fr' => "🚚 **Nouvelle mission de transport (#{$load->id})**\n\n" .
                    "Bonjour {$carrier->name}, une nouvelle mission vous a été confiée pour transporter **{$load->qty} {$load->unit} de {$productName}**.\n" .
                    "📍 **De :** {$pickupLabel} ➡️ **À :** {$dropoffLabel} (~{$estimate['distance_km']} km)\n" .
                    "💰 **Tarif estimé :** ~{$estimate['total_cost']} TND\n\n" .
                    "👉 Veuillez confirmer votre accord ci-dessous.",
            'en' => "🚚 **New Transport Mission Assigned (#{$load->id})**\n\n" .
                    "Hello {$carrier->name}, you have been assigned a new transport mission for **{$load->qty} {$load->unit} of {$productName}**.\n" .
                    "📍 **From:** {$pickupLabel} ➡️ **To:** {$dropoffLabel} (~{$estimate['distance_km']} km)\n" .
                    "💰 **Estimated Cost:** ~{$estimate['total_cost']} TND\n\n" .
                    "👉 Please confirm your acceptance below.",
            default => "🚚 **مهمة نقل جديدة (#{$load->id})**\n\n" .
                    "مرحباً سي {$carrier->name}، تم اختيارك لكورسة نقل **{$load->qty} {$load->unit} من {$productName}**.\n" .
                    "📍 **المسار:** من **{$pickupLabel}** ➡️ إلى **{$dropoffLabel}** (~{$estimate['distance_km']} كم)\n" .
                    "💰 **السعر التقديري:** ~{$estimate['total_cost']} د.ت\n\n" .
                    "👉 يُرجى تأكيد قبول المهمة أدناه.",
        };

        // Post interactive mission assignment message in carrier's direct chat
        Message::create([
            'thread_id' => $carrierThread->id,
            'sender_id' => $user->id,
            'body' => $body,
            'attachments' => [
                'type' => 'transport_assignment',
                'load_id' => $load->id,
                'order_id' => $order->id,
                'trip_id' => $trip->id,
                'status' => 'pending',
                'estimated_cost' => $estimate['total_cost'],
                'distance_km' => $estimate['distance_km'],
                'pickup' => $pickupLabel,
                'dropoff' => $dropoffLabel,
                'qty' => (float)$load->qty,
                'unit' => $load->unit,
                'product_name' => $productName,
            ],
            'is_flagged' => false,
            'is_deleted' => false,
        ]);

        // 4. Post Ez-Zitouni AI mediation & PIN guide in the deal chat thread
        $thread = Chat::ensureThread('order', $order->id, [$order->buyer_id, $order->seller_id]);
        EzZitouniDealMediator::onTransporterSummoned($thread, $load, $carrier->name, $pinCode, $estimate['total_cost']);

        // 5. Send automated WhatsApp Mission Dispatch to Carrier
        if (!empty($carrier->phone)) {
            try {
                $waService = app(\App\Services\Bot\WhatsAppCloudApiService::class);
                $waService->sendTransportMission($carrier->phone, $load, $carrier, $user, $estimate);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error("Failed to send WhatsApp mission to carrier #{$carrier->id}: " . $e->getMessage());
            }
        }

        // Build WhatsApp Direct Fallback Link
        $cleanPhone = preg_replace('/[^0-9]/', '', (string)$carrier->phone);
        if (strlen($cleanPhone) === 8) {
            $cleanPhone = '216' . $cleanPhone;
        }
        $waText = rawurlencode("سلام سي {$carrier->name}، تم اختيارك لكورسة نقل زيت زيتون من {$pickupLabel} إلى {$dropoffLabel} عبر منصة ZinToop. السعر التقديري: ~{$estimate['total_cost']} د.ت. كلمني للتنسيق.");
        $waDirectUrl = !empty($cleanPhone) ? "https://wa.me/{$cleanPhone}?text={$waText}" : null;

        return response()->json([
            'success' => true,
            'message' => __('Transporter summoned successfully.'),
            'load_id' => $load->id,
            'pin_code' => $pinCode,
            'estimated_cost' => $estimate['total_cost'],
            'distance_km' => $estimate['distance_km'],
            'whatsapp_url' => $waDirectUrl,
        ]);
    }

    /**
     * Carrier accepts the assigned load
     */
    public function accept(Request $request, Load $load)
    {
        $user = auth()->user();
        if ((int)$user->id !== (int)$load->carrier_id && $user->role !== 'admin') {
            return response()->json(['success' => false, 'message' => __('Unauthorized')], 403);
        }

        $load->update(['status' => Load::ST_CONFIRMED]);

        // Update active trip
        $trip = $load->activeTrip();
        if ($trip) {
            $trip->update(['start_at' => now()]);
        }

        // Update mission message attachment in chat
        $messages = Message::where('attachments->load_id', $load->id)
            ->where('attachments->type', 'transport_assignment')
            ->get();

        foreach ($messages as $msg) {
            $att = $msg->attachments ?? [];
            $att['status'] = 'accepted';
            $msg->attachments = $att;
            $msg->save();
        }

        $locale = $load->owner?->locale ?? 'ar';
        $acceptText = match($locale) {
            'fr' => "✅ **Mission acceptée par le transporteur ({$user->name})** ! La course est confirmée et prête pour le départ.",
            'en' => "✅ **Mission accepted by carrier ({$user->name})**! The transport is confirmed and ready for dispatch.",
            default => "✅ **قام الناقل ({$user->name}) بتأكيد وقبول مهمة النقل!** الكورسة مؤكدة وجاهزة للانطلاق.",
        };

        if ($messages->first()) {
            Message::create([
                'thread_id' => $messages->first()->thread_id,
                'sender_id' => $user->id,
                'body' => $acceptText,
                'attachments' => ['is_system' => true, 'type' => 'mission_accepted', 'load_id' => $load->id],
                'is_flagged' => false,
                'is_deleted' => false,
            ]);
        }

        if ($load->order) {
            $orderThread = Chat::ensureThread('order', $load->order->id, [$load->order->buyer_id, $load->order->seller_id]);
            Message::create([
                'thread_id' => $orderThread->id,
                'sender_id' => $user->id,
                'body' => $acceptText,
                'attachments' => ['is_system' => true, 'type' => 'mission_accepted', 'load_id' => $load->id],
                'is_flagged' => false,
                'is_deleted' => false,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => __('Mission accepted successfully! 🚚'),
            'trip_url' => route('mobile.trip', ['locale' => app()->getLocale(), 'id' => $load->id]),
        ]);
    }

    /**
     * Carrier declines the assigned load
     */
    public function deny(Request $request, Load $load)
    {
        $user = auth()->user();
        if ((int)$user->id !== (int)$load->carrier_id && $user->role !== 'admin') {
            return response()->json(['success' => false, 'message' => __('Unauthorized')], 403);
        }

        $load->update(['status' => 'new', 'carrier_id' => null]);

        $messages = Message::where('attachments->load_id', $load->id)
            ->where('attachments->type', 'transport_assignment')
            ->get();

        foreach ($messages as $msg) {
            $att = $msg->attachments ?? [];
            $att['status'] = 'rejected';
            $msg->attachments = $att;
            $msg->save();
        }

        $locale = $load->owner?->locale ?? 'ar';
        $denyText = match($locale) {
            'fr' => "❌ **Le transporteur ({$user->name}) ne peut pas assurer cette course.** Vous pouvez choisir un autre transporteur depuis le chat.",
            'en' => "❌ **Carrier ({$user->name}) declined this mission.** You can assign another carrier from the chat.",
            default => "❌ **اعتذر الناقل ({$user->name}) عن قبول هذه الكورسة.** يمكنكم استدعاء ناقل آخر من قائمة الناقلين.",
        };

        if ($messages->first()) {
            Message::create([
                'thread_id' => $messages->first()->thread_id,
                'sender_id' => $user->id,
                'body' => $denyText,
                'attachments' => ['is_system' => true, 'type' => 'mission_rejected', 'load_id' => $load->id],
                'is_flagged' => false,
                'is_deleted' => false,
            ]);
        }

        if ($load->order) {
            $orderThread = Chat::ensureThread('order', $load->order->id, [$load->order->buyer_id, $load->order->seller_id]);
            Message::create([
                'thread_id' => $orderThread->id,
                'sender_id' => $user->id,
                'body' => $denyText,
                'attachments' => ['is_system' => true, 'type' => 'mission_rejected', 'load_id' => $load->id],
                'is_flagged' => false,
                'is_deleted' => false,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => __('Mission declined.'),
        ]);
    }

    /**
     * Search and sort carriers by proximity to target coordinates or governorate,
     * and match their truck capacity against the required deal quantity.
     */
    public function searchCarriers(Request $request)
    {
        $lat = $request->filled('lat') ? (float)$request->input('lat') : null;
        $lng = $request->filled('lng') ? (float)$request->input('lng') : null;
        $governorate = $request->filled('governorate') ? mb_convert_encoding(trim((string)$request->input('governorate')), 'UTF-8', 'UTF-8') : null;
        $requiredQty = $request->filled('required_qty') ? (float)$request->input('required_qty') : 0.0;
        $unit = strtolower((string)$request->input('unit', 'l'));
        $onlySuitable = $request->boolean('only_suitable', false);

        // If lat/lng not provided directly, resolve from governorate if available
        if ((!$lat || !$lng || ($lat == 0.0 && $lng == 0.0)) && $governorate) {
            $resolved = TransportPricingService::resolveCoordinates($governorate);
            $lat = $resolved['lat'];
            $lng = $resolved['lng'];
        }

        // Convert required deal quantity to Kg (1 ton = 1000 kg, 1 L ~= 1 kg)
        $requiredKg = 0.0;
        if ($requiredQty > 0) {
            if ($unit === 'ton') {
                $requiredKg = $requiredQty * 1000.0;
            } else {
                $requiredKg = $requiredQty;
            }
        }

        $locale = app()->getLocale();

        $carriers = User::where('role', 'carrier')
            ->with('addresses')
            ->get();

        $results = $carriers->map(function ($carrier) use ($lat, $lng, $governorate, $requiredKg, $locale) {
            $cAddr = $carrier->addresses->first();
            $cLat = $cAddr?->lat ? (float)$cAddr->lat : null;
            $cLng = $cAddr?->lng ? (float)$cAddr->lng : null;
            $cGov = $cAddr?->governorate ?: ($carrier->location ?: null);

            // Resolve carrier coordinates if missing
            if (!$cLat || !$cLng || ($cLat == 0.0 && $cLng == 0.0)) {
                $resolvedCoords = TransportPricingService::resolveCoordinates($cGov ?: 'صفاقس');
                $cLat = $resolvedCoords['lat'];
                $cLng = $resolvedCoords['lng'];
            }

            // Calculate distance in KM
            if ($lat && $lng && $cLat && $cLng) {
                $distanceKm = TransportPricingService::calculateDistance($lat, $lng, $cLat, $cLng);
            } elseif ($governorate && $cGov && mb_strtolower(trim($governorate)) === mb_strtolower(trim($cGov))) {
                $distanceKm = 12.0; // Same governorate estimate
            } else {
                $distanceKm = 60.0; // Default regional estimate
            }

            // Carrier Capacity in Kg & Tons
            $rawCap = (float)($carrier->camion_capacity ?? 0);
            $capacityKg = 0.0;
            $capacityTons = 0.0;
            if ($rawCap > 0) {
                if ($rawCap < 100) {
                    $capacityTons = $rawCap;
                    $capacityKg = $rawCap * 1000.0;
                } else {
                    $capacityKg = $rawCap;
                    $capacityTons = round($rawCap / 1000.0, 1);
                }
            }

            // Capacity fit evaluation
            $fitsCapacity = true;
            if ($requiredKg > 0) {
                $fitsCapacity = ($capacityKg > 0 && $capacityKg >= $requiredKg);
            }

            // Vehicle type label
            $vType = $carrier->meta_data['vehicle_type'] ?? 'light_truck';
            $vTypeLabel = match ($vType) {
                'fourgonnette' => $locale === 'ar' ? '🚐 فورغونات (بارتنر / كادي)' : '🚐 Fourgonnette (Partner/Caddy)',
                'pickup', 'light_commercial' => $locale === 'ar' ? '🛻 شاحنة خفيفة (ديماكس / ستافات)' : '🛻 Camionnette légère / Stafette',
                'heavy_truck' => $locale === 'ar' ? '🚚 شاحنة ثقيلة' : '🚚 Poids Lourd',
                'tanker' => $locale === 'ar' ? '🛢️ صهريج غذائي Inox' : '🛢️ Citerne Inox',
                'fleet' => $locale === 'ar' ? '🚚 أسطول شاحنات' : '🚚 Flotte de camions',
                default => $locale === 'ar' ? '🚛 شاحنة متوسطة' : '🚛 Camionnette',
            };

            // WhatsApp link
            $cPhoneClean = preg_replace('/[^0-9]/', '', (string)$carrier->phone);
            $waMsg = match ($locale) {
                'fr' => "Bonjour {$carrier->name}, j'ai vu votre profil sur ZinToop et je souhaite coordonner un transport d'huile d'olive.",
                'en' => "Hello {$carrier->name}, I saw your profile on ZinToop and want to arrange olive oil transport.",
                default => "سلام سي {$carrier->name}، شفت بروفيلك في منصة ZinToop ونحب ننسق معاك على كورسة نقل زيت زيتون.",
            };
            $waUrl = !empty($cPhoneClean) ? "https://wa.me/{$cPhoneClean}?text=" . rawurlencode($waMsg) : null;

            // Profile picture URL
            $avatarUrl = $carrier->profile_picture ? \Illuminate\Support\Facades\Storage::url($carrier->profile_picture) : null;

            $capacityLabel = $capacityTons > 0 
                ? "{$capacityTons} " . ($locale === 'ar' ? 'طن' : 'T')
                : ($capacityKg > 0 ? "{$capacityKg} " . ($locale === 'ar' ? 'كغ' : 'Kg') : ($locale === 'ar' ? 'غير محدد' : 'Non précisé'));

            return [
                'id' => $carrier->id,
                'name' => $carrier->name,
                'avatar' => $avatarUrl,
                'is_verified' => (bool)$carrier->is_verified,
                'phone' => $carrier->phone,
                'phone_clean' => $cPhoneClean,
                'whatsapp_url' => $waUrl,
                'chat_url' => route('messages.show', ['locale' => $locale, 'user' => $carrier->id]),
                'governorate' => $cGov ?? ($locale === 'ar' ? 'تونس' : 'Tunisie'),
                'vehicle_type' => $vType,
                'vehicle_type_label' => $vTypeLabel,
                'capacity_raw' => $rawCap,
                'capacity_kg' => $capacityKg,
                'capacity_tons' => $capacityTons,
                'capacity_label' => $capacityLabel,
                'distance_km' => round($distanceKm, 1),
                'fits_capacity' => $fitsCapacity,
            ];
        });

        if ($onlySuitable && $requiredKg > 0) {
            $results = $results->filter(fn($c) => $c['fits_capacity']);
        }

        // Sort: if requiredKg > 0, suitable capacity first, then distance_km ascending, then is_verified
        $sorted = $results->sort(function ($a, $b) use ($requiredKg) {
            if ($requiredKg > 0) {
                if ($a['fits_capacity'] !== $b['fits_capacity']) {
                    return $a['fits_capacity'] ? -1 : 1;
                }
            }
            if ($a['distance_km'] !== $b['distance_km']) {
                return $a['distance_km'] <=> $b['distance_km'];
            }
            return ($b['is_verified'] ? 1 : 0) <=> ($a['is_verified'] ? 1 : 0);
        })->values();

        return response()->json([
            'success' => true,
            'count' => $sorted->count(),
            'target_location' => [
                'lat' => $lat,
                'lng' => $lng,
                'governorate' => $governorate,
            ],
            'required_load' => [
                'qty' => $requiredQty,
                'unit' => $unit,
                'required_kg' => $requiredKg,
            ],
            'carriers' => $sorted,
        ]);
    }
}
