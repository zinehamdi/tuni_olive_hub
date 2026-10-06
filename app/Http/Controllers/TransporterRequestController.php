<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Thread;
use App\Models\TransporterRequest;
use App\Models\User;
use App\Services\Chat;
use App\Services\TransportPricingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransporterRequestController extends Controller
{
    /**
     * Calculate route distance and fair transport pricing estimate
     */
    public function estimate(Request $request): JsonResponse
    {
        $weight = (float) $request->input('weight', 5.0);
        $unit = strtolower((string) $request->input('unit', 'ton'));
        $pickupGov = (string) $request->input('pickup_governorate', 'صفاقس');
        $dropoffGov = (string) $request->input('dropoff_governorate', 'تونس');
        $cargoType = (string) $request->input('cargo_type', 'oil');

        $pLat = $request->filled('pickup_lat') ? (float) $request->input('pickup_lat') : null;
        $pLng = $request->filled('pickup_lng') ? (float) $request->input('pickup_lng') : null;
        $dLat = $request->filled('dropoff_lat') ? (float) $request->input('dropoff_lat') : null;
        $dLng = $request->filled('dropoff_lng') ? (float) $request->input('dropoff_lng') : null;

        $pCoords = TransportPricingService::resolveCoordinates($pickupGov, $pLat, $pLng);
        $dCoords = TransportPricingService::resolveCoordinates($dropoffGov, $dLat, $dLng);

        $quantityInKg = ($unit === 'ton') ? ($weight * 1000.0) : $weight;

        $estimate = TransportPricingService::estimateCost(
            $quantityInKg,
            $pCoords['lat'],
            $pCoords['lng'],
            $dCoords['lat'],
            $dCoords['lng'],
            $cargoType
        );

        return response()->json([
            'success' => true,
            'distance_km' => $estimate['distance_km'],
            'estimated_cost' => $estimate['total_cost'],
            'tier' => $estimate['tier'],
            'pickup' => $pCoords,
            'dropoff' => $dCoords,
        ]);
    }

    /**
     * Submit a transporter request.
     * Enforces the rule: Max 3 pending requests in a 10-minute window per user.
     */
    public function store(Request $request): JsonResponse
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => __('Please sign in to send requests.')], 401);
        }

        $validated = $request->validate([
            'carrier_id' => 'required|exists:users,id',
            'weight' => 'required|numeric|min:0.1',
            'unit' => 'nullable|string|in:ton,kg,l',
            'cargo_type' => 'nullable|string|in:oil,olive',
            'pickup_governorate' => 'required|string',
            'pickup_delegation' => 'nullable|string',
            'dropoff_governorate' => 'required|string',
            'dropoff_delegation' => 'nullable|string',
            'pickup_lat' => 'nullable|numeric',
            'pickup_lng' => 'nullable|numeric',
            'dropoff_lat' => 'nullable|numeric',
            'dropoff_lng' => 'nullable|numeric',
        ]);

        $carrier = User::findOrFail($validated['carrier_id']);
        if ($carrier->role !== 'carrier' && $carrier->role !== 'admin') {
            return response()->json(['success' => false, 'message' => __('Selected user is not a transporter.')], 422);
        }

        if ((int) $user->id === (int) $carrier->id) {
            return response()->json(['success' => false, 'message' => __('You cannot send a transport request to yourself.')], 422);
        }

        // Check 10-minute window quota: Max 3 active pending requests
        $activePendingCount = TransporterRequest::where('user_id', $user->id)
            ->where('status', TransporterRequest::STATUS_PENDING)
            ->where('created_at', '>=', now()->subMinutes(10))
            ->count();

        if ($activePendingCount >= 3) {
            return response()->json([
                'success' => false,
                'message' => __('You have reached the maximum limit of 3 pending requests in 10 minutes. Please wait for a carrier to accept.'),
                'remaining_quota' => 0,
            ], 429);
        }

        // Check if request already exists for this carrier in the 10-minute window
        $existing = TransporterRequest::where('user_id', $user->id)
            ->where('carrier_id', $carrier->id)
            ->where('status', TransporterRequest::STATUS_PENDING)
            ->where('created_at', '>=', now()->subMinutes(10))
            ->first();

        if ($existing) {
            return response()->json([
                'success' => true,
                'already_sent' => true,
                'message' => __('Request already sent to this transporter.'),
                'request_id' => $existing->id,
                'remaining_quota' => max(0, 3 - $activePendingCount),
            ]);
        }

        // Coordinates & pricing calculation
        $unit = $validated['unit'] ?? 'ton';
        $weight = (float) $validated['weight'];
        $pCoords = TransportPricingService::resolveCoordinates(
            $validated['pickup_governorate'],
            isset($validated['pickup_lat']) ? (float)$validated['pickup_lat'] : null,
            isset($validated['pickup_lng']) ? (float)$validated['pickup_lng'] : null
        );
        $dCoords = TransportPricingService::resolveCoordinates(
            $validated['dropoff_governorate'],
            isset($validated['dropoff_lat']) ? (float)$validated['dropoff_lat'] : null,
            isset($validated['dropoff_lng']) ? (float)$validated['dropoff_lng'] : null
        );

        $cargoType = $validated['cargo_type'] ?? 'oil';
        $qtyInKg = ($unit === 'ton') ? ($weight * 1000.0) : $weight;
        $estimate = TransportPricingService::estimateCost(
            $qtyInKg,
            $pCoords['lat'],
            $pCoords['lng'],
            $dCoords['lat'],
            $dCoords['lng'],
            $cargoType
        );

        $transporterRequest = TransporterRequest::create([
            'user_id' => $user->id,
            'carrier_id' => $carrier->id,
            'weight' => $weight,
            'unit' => $unit,
            'cargo_type' => $cargoType,
            'pickup_governorate' => $validated['pickup_governorate'],
            'pickup_delegation' => $validated['pickup_delegation'] ?? null,
            'pickup_lat' => $pCoords['lat'],
            'pickup_lng' => $pCoords['lng'],
            'dropoff_governorate' => $validated['dropoff_governorate'],
            'dropoff_delegation' => $validated['dropoff_delegation'] ?? null,
            'dropoff_lat' => $dCoords['lat'],
            'dropoff_lng' => $dCoords['lng'],
            'distance_km' => $estimate['distance_km'],
            'estimated_cost' => $estimate['total_cost'],
            'status' => TransporterRequest::STATUS_PENDING,
            'expires_at' => now()->addMinutes(10),
        ]);

        // 1. Notify carrier in-app and email
        try {
            $carrier->notify(new \App\Notifications\DirectTransporterRequestReceived($transporterRequest, $user));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("Failed to notify carrier #{$carrier->id} of TransporterRequest: " . $e->getMessage());
        }

        // 2. Automated WhatsApp Cloud API dispatch to carrier
        if (!empty($carrier->phone)) {
            try {
                $waService = app(\App\Services\Bot\WhatsAppCloudApiService::class);
                $waService->sendDirectTransporterMission($carrier->phone, $transporterRequest, $carrier, $user);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error("Failed to send WhatsApp direct mission to carrier #{$carrier->id}: " . $e->getMessage());
            }
        }

        // 3. Fallback direct WhatsApp link for client
        $cleanPhone = preg_replace('/[^0-9]/', '', (string)$carrier->phone);
        if (strlen($cleanPhone) === 8) {
            $cleanPhone = '216' . $cleanPhone;
        }
        $waUnit = ($transporterRequest->unit === 'kg') ? 'كغ' : 'طن';
        $waText = rawurlencode("سلام سي {$carrier->name}، بعثتلك طلب نقل فوري عبر منصة ZinToop من {$transporterRequest->pickup_governorate} إلى {$transporterRequest->dropoff_governorate} بحمولة {$transporterRequest->weight} {$waUnit}. السعر التقديري: ~{$transporterRequest->estimated_cost} د.ت. كلمني للتنسيق.");
        $waDirectUrl = !empty($cleanPhone) ? "https://wa.me/{$cleanPhone}?text={$waText}" : null;

        $remaining = max(0, 3 - ($activePendingCount + 1));

        return response()->json([
            'success' => true,
            'message' => __('Request sent successfully to transporter.'),
            'request_id' => $transporterRequest->id,
            'carrier_id' => $carrier->id,
            'carrier_name' => $carrier->name,
            'remaining_quota' => $remaining,
            'whatsapp_url' => $waDirectUrl,
        ]);
    }

    /**
     * Check status of requests for the authenticated user.
     * Detects if ANY carrier accepted the request to trigger instant redirection to chat.
     */
    public function status(): JsonResponse
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'active' => false]);
        }

        // Check if any request was accepted in the last 2 hours
        $accepted = TransporterRequest::where('user_id', $user->id)
            ->where('status', TransporterRequest::STATUS_ACCEPTED)
            ->where('accepted_at', '>=', now()->subHours(2))
            ->with('carrier')
            ->latest('accepted_at')
            ->first();

        if ($accepted && $accepted->carrier) {
            $locale = app()->getLocale();
            return response()->json([
                'success' => true,
                'accepted' => true,
                'request_id' => $accepted->id,
                'carrier_id' => $accepted->carrier_id,
                'carrier_name' => $accepted->carrier->name,
                'carrier_phone' => $accepted->carrier->phone,
                'chat_url' => route('messages.show', ['locale' => $locale, 'user' => $accepted->carrier_id]),
                'pickup' => $accepted->pickup_governorate,
                'pickup_delegation' => $accepted->pickup_delegation,
                'dropoff' => $accepted->dropoff_governorate,
                'dropoff_delegation' => $accepted->dropoff_delegation,
            ]);
        }

        // Active pending requests
        $pendingList = TransporterRequest::where('user_id', $user->id)
            ->where('status', TransporterRequest::STATUS_PENDING)
            ->where('created_at', '>=', now()->subMinutes(10))
            ->with('carrier:id,name,phone,avatar,rating')
            ->latest()
            ->get();

        $pendingCarrierIds = $pendingList->pluck('carrier_id')->toArray();

        $activeRoute = null;
        if ($pendingList->isNotEmpty()) {
            $first = $pendingList->first();
            $activeRoute = [
                'pickup_gov' => $first->pickup_governorate,
                'pickup_del' => $first->pickup_delegation,
                'dropoff_gov' => $first->dropoff_governorate,
                'dropoff_del' => $first->dropoff_delegation,
                'weight' => $first->weight,
                'distance_km' => $first->distance_km,
                'cost' => $first->estimated_cost,
            ];
        }

        return response()->json([
            'success' => true,
            'accepted' => false,
            'pending_carrier_ids' => $pendingCarrierIds,
            'pending_requests' => $pendingList->map(function ($req) {
                return [
                    'id' => $req->id,
                    'carrier_id' => $req->carrier_id,
                    'carrier_name' => $req->carrier?->name ?? 'ناقل',
                    'weight' => $req->weight,
                    'cost' => $req->estimated_cost,
                    'created_at' => $req->created_at->toIso8601String(),
                ];
            }),
            'active_route' => $activeRoute,
            'active_count' => count($pendingCarrierIds),
            'remaining_quota' => max(0, 3 - count($pendingCarrierIds)),
        ]);
    }

    /**
     * User cancels a pending transporter request
     */
    public function cancel(Request $request, ?TransporterRequest $transporterRequest = null): JsonResponse
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => __('Unauthorized')], 403);
        }

        if (!$transporterRequest && $request->filled('carrier_id')) {
            $transporterRequest = TransporterRequest::where('user_id', $user->id)
                ->where('carrier_id', $request->input('carrier_id'))
                ->where('status', TransporterRequest::STATUS_PENDING)
                ->latest()
                ->first();
        }

        if (!$transporterRequest) {
            return response()->json(['success' => false, 'message' => __('Request not found.')], 404);
        }

        if ((int) $user->id !== (int) $transporterRequest->user_id && $user->role !== 'admin') {
            return response()->json(['success' => false, 'message' => __('Unauthorized')], 403);
        }

        $transporterRequest->update([
            'status' => TransporterRequest::STATUS_CANCELLED,
        ]);

        $activePendingCount = TransporterRequest::where('user_id', $user->id)
            ->where('status', TransporterRequest::STATUS_PENDING)
            ->where('created_at', '>=', now()->subMinutes(10))
            ->count();

        return response()->json([
            'success' => true,
            'message' => __('تم إلغاء الطلب بنجاح.'),
            'carrier_id' => $transporterRequest->carrier_id,
            'remaining_quota' => max(0, 3 - $activePendingCount),
        ]);
    }

    /**
     * Carrier accepts the request.
     * Locks in the deal, cancels other pending requests, creates chat thread,
     * and sends system authorization message.
     */
    public function accept(Request $request, TransporterRequest $transporterRequest): JsonResponse
    {
        $user = auth()->user();
        if (!$user || ((int) $user->id !== (int) $transporterRequest->carrier_id && $user->role !== 'admin')) {
            return response()->json(['success' => false, 'message' => __('Unauthorized')], 403);
        }

        if ($transporterRequest->status === TransporterRequest::STATUS_ACCEPTED) {
            $locale = app()->getLocale();
            return response()->json([
                'success' => true,
                'message' => __('Already accepted.'),
                'chat_url' => route('messages.show', ['locale' => $locale, 'user' => $transporterRequest->user_id]),
            ]);
        }

        if ($transporterRequest->status !== TransporterRequest::STATUS_PENDING) {
            return response()->json(['success' => false, 'message' => __('This request is no longer available.')], 422);
        }

        // Update request to accepted
        $transporterRequest->update([
            'status' => TransporterRequest::STATUS_ACCEPTED,
            'accepted_at' => now(),
        ]);

        // Cancel other pending requests from that user in this window
        TransporterRequest::where('user_id', $transporterRequest->user_id)
            ->where('id', '!=', $transporterRequest->id)
            ->where('status', TransporterRequest::STATUS_PENDING)
            ->update(['status' => TransporterRequest::STATUS_CANCELLED]);

        // Ensure direct chat thread exists
        $customer = $transporterRequest->user;
        $carrier = $transporterRequest->carrier;
        $thread = Chat::ensureThread('direct_message', 0, [$customer->id, $carrier->id]);

        $customerPhone = $customer->phone ?: ($customer->profile?->phone ?: 'غير مسجل');
        $cargoLabel = ($transporterRequest->cargo_type === 'olive')
            ? '🫒 زيتون فلاحي (حب / صناديق)'
            : '🫙 صهريج زيت زيتون (Citerne / براميل)';

        // Official system dispatch message in thread
        $systemBody = "📢 **[نظام الزين - تأكيد مهمة النقل 🚚]**\n\n"
            . "✅ **تم قبول وتأكيد المهمة بنجاح من طرف الناقل:** {$carrier->name}\n\n"
            . "📞 **رقم هاتف الحريف:** `{$customerPhone}`\n"
            . "👉 **إشعار للناقل:** يمكنك الآن الاتصال بالحريف مباشرة عبر الهاتف لتنسيق مسار النقل والتوقيت الدقيق.\n\n"
            . "📋 **تفاصيل الكورسة:**\n"
            . "• نوع الحمولة: **{$cargoLabel}**\n"
            . "• الوزن / الكمية: {$transporterRequest->weight} {$transporterRequest->unit}\n"
            . "• مسار النقل: من **{$transporterRequest->pickup_governorate}** إلى **{$transporterRequest->dropoff_governorate}**\n"
            . "• المسافة المقدرة: ~{$transporterRequest->distance_km} كم\n"
            . "• السعر التقديري العادل: ~{$transporterRequest->estimated_cost} د.ت\n\n"
            . "📍 **ملاحظة هامة للحريف:** نرجو النقر على زر **«📍 إرسال موقعي الدقيق للناقل»** أدناه حتى يصل الناقل مباشرة إلى مكان التحميل بدون أن يضيع.";

        $msg = Message::create([
            'thread_id' => $thread->id,
            'sender_id' => $carrier->id,
            'body' => $systemBody,
            'attachments' => [
                'type' => 'transporter_request_accepted',
                'request_id' => $transporterRequest->id,
                'cargo_type' => $transporterRequest->cargo_type,
                'cargo_label' => $cargoLabel,
                'customer_id' => $customer->id,
                'customer_name' => $customer->name,
                'customer_phone' => $customerPhone,
                'carrier_id' => $carrier->id,
                'carrier_name' => $carrier->name,
                'weight' => $transporterRequest->weight,
                'unit' => $transporterRequest->unit,
                'pickup' => $transporterRequest->pickup_governorate,
                'dropoff' => $transporterRequest->dropoff_governorate,
                'distance_km' => $transporterRequest->distance_km,
                'estimated_cost' => $transporterRequest->estimated_cost,
            ],
            'is_flagged' => false,
            'is_deleted' => false,
            'is_hidden' => false,
        ]);

        $thread->touch();

        // Broadcast if websocket available
        try {
            broadcast(new \App\Events\MessageSent($msg))->toOthers();
        } catch (\Throwable $e) {}

        $locale = app()->getLocale();
        return response()->json([
            'success' => true,
            'message' => __('Mission accepted successfully! You can now call the customer.'),
            'chat_url' => route('messages.show', ['locale' => $locale, 'user' => $customer->id]),
        ]);
    }

    /**
     * Carrier declines the request
     */
    public function decline(Request $request, TransporterRequest $transporterRequest): JsonResponse
    {
        $user = auth()->user();
        if (!$user || ((int) $user->id !== (int) $transporterRequest->carrier_id && $user->role !== 'admin')) {
            return response()->json(['success' => false, 'message' => __('Unauthorized')], 403);
        }

        $transporterRequest->update([
            'status' => TransporterRequest::STATUS_DECLINED,
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Request declined.'),
        ]);
    }
}
