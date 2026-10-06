<?php

namespace App\Services;

use App\Models\Load;
use App\Models\Trip;
use App\Models\User;
use App\Models\Message;
use App\Models\Thread;
use App\Events\TripDelivered;
use App\Events\TripStarted;
use App\Services\Bot\WhatsAppCloudApiService;
use Illuminate\Support\Facades\Log;

class TransportWhatsAppHandler
{
    /**
     * Handle incoming interactive button clicks (e.g. ACCEPT_LOAD_123, DECLINE_LOAD_123)
     */
    public function handleButtonReply(string $fromPhone, string $buttonId): array
    {
        $waService = app(WhatsAppCloudApiService::class);
        $cleanPhone = $waService->formatPhone($fromPhone);

        Log::info("TransportWhatsAppHandler: button [{$buttonId}] received from {$cleanPhone}");

        if (str_starts_with($buttonId, 'ACCEPT_LOAD_')) {
            $loadId = (int) str_replace('ACCEPT_LOAD_', '', $buttonId);
            return $this->acceptLoad($cleanPhone, $loadId);
        }

        if (str_starts_with($buttonId, 'DECLINE_LOAD_')) {
            $loadId = (int) str_replace('DECLINE_LOAD_', '', $buttonId);
            return $this->declineLoad($cleanPhone, $loadId);
        }

        if (str_starts_with($buttonId, 'ACTIVATE_CARRIER_')) {
            $carrierId = (int) str_replace('ACTIVATE_CARRIER_', '', $buttonId);
            return $this->activateCarrier($cleanPhone, $carrierId);
        }

        if (str_starts_with($buttonId, 'ACCEPT_TREQ_')) {
            $reqId = (int) str_replace('ACCEPT_TREQ_', '', $buttonId);
            return $this->acceptTransporterRequest($cleanPhone, $reqId);
        }

        if (str_starts_with($buttonId, 'DECLINE_TREQ_')) {
            $reqId = (int) str_replace('DECLINE_TREQ_', '', $buttonId);
            return $this->declineTransporterRequest($cleanPhone, $reqId);
        }

        return ['handled' => false];
    }

    /**
     * Handle text replies (e.g. PIN 4829, or natural Arabic words like "موافق", "نقبل", "تفعيل")
     */
    public function handleTextReply(string $fromPhone, string $text): bool
    {
        $waService = app(WhatsAppCloudApiService::class);
        $cleanPhone = $waService->formatPhone($fromPhone);
        $trimmed = trim($text);

        // 1. Check for 4-6 digit PIN code
        if (preg_match('/\b(\d{4,6})\b/', $trimmed, $matches)) {
            $pin = $matches[1];
            if ($this->verifyDeliveryPin($cleanPhone, $pin)) {
                return true; // Successfully handled as PIN verification
            }
        }

        // 2. Check for carrier registration activation keywords ("تفعيل", "تفعيل الكورسات", "تفعيل حسابي")
        if (mb_stripos($trimmed, 'تفعيل') !== false) {
            $carrier = $this->findCarrierByPhone($cleanPhone);
            if ($carrier && $carrier->role === 'carrier') {
                $this->activateCarrier($cleanPhone, $carrier->id);
                return true;
            }
        }

        // 3. Check for quick Arabic acceptance keywords
        $acceptKeywords = ['موافق', 'نقبل', 'موافقة', 'جايك', 'اوكي', 'oui', 'accept', 'ok'];
        foreach ($acceptKeywords as $keyword) {
            if (mb_stripos($trimmed, $keyword) !== false) {
                // Find latest pending load for this carrier
                $carrier = $this->findCarrierByPhone($cleanPhone);
                if ($carrier) {
                    $pendingLoad = Load::where('carrier_id', $carrier->id)
                        ->where('status', Load::ST_MATCHED)
                        ->latest()
                        ->first();

                    if ($pendingLoad) {
                        $this->acceptLoad($cleanPhone, $pendingLoad->id);
                        return true;
                    }
                }
            }
        }

        return false; // Not a transport command, pass to AI chatbot
    }

    /**
     * Activate a carrier's WhatsApp channel upon onboarding
     */
    public function activateCarrier(string $cleanPhone, int $carrierId): array
    {
        $carrier = User::find($carrierId) ?? $this->findCarrierByPhone($cleanPhone);
        if ($carrier) {
            $metaData = $carrier->meta_data ?? [];
            $metaData['whatsapp_verified'] = true;
            $metaData['whatsapp_verified_at'] = now()->toDateTimeString();
            $carrier->meta_data = $metaData;
            $carrier->save();
        }

        $waService = app(WhatsAppCloudApiService::class);
        $reply = "🎉 *تم تفعيل رقمك واستقبال الكورسات بنجاح!*\n\n"
            . "شكراً لك سي *{$carrier?->name}*، تم اعتمادك في شبكة النقل الفلاحي التونسي على ZinToop.\n\n"
            . "📦 سنقوم بإرسال كل مهمة نقل جديدة تتوفر في منطقتك هنا مباشرة وبـ 0% عمولة (خلاصك صافي في يدك).\n\n"
            . "بالتوفيق وموسم مبارك! 🚚🫒";

        $waService->sendTextMessage($cleanPhone, $reply);
        return ['handled' => true, 'success' => true];
    }

    /**
     * Accept a transport load
     */
    public function acceptLoad(string $cleanPhone, int $loadId): array
    {
        $load = Load::with(['order.buyer', 'order.seller', 'carrier', 'pickupAddress', 'dropoffAddress'])->find($loadId);

        if (!$load) {
            return ['handled' => true, 'success' => false, 'message' => 'الكورسة غير موجودة.'];
        }

        $carrier = $this->findCarrierByPhone($cleanPhone) ?? $load->carrier;
        $waService = app(WhatsAppCloudApiService::class);

        // Update load status
        $load->update([
            'carrier_id' => $carrier?->id ?? $load->carrier_id,
            'status' => Load::ST_CONFIRMED,
        ]);

        // Start or update trip
        $trip = Trip::whereJsonContains('load_ids', $load->id)->first();
        if ($trip) {
            $trip->update(['start_at' => $trip->start_at ?? now()]);
            event(new TripStarted($trip->id, $trip->carrier_id, $trip->sr_code ?? ''));
        }

        $trackingUrl = url('/ar/mobile/trip?id=' . $load->id);

        // 1. Send confirmation message to Carrier
        $carrierMsg = "✅ *تم تأكيد قبول الكورسة بنجاح! (#{$load->id})*\n\n"
            . "شكراً لك سي *{$carrier?->name}*، تم تثبيت المهمة باسمك في المنصة.\n\n"
            . "📍 *رابط المسار والخريطة الحية:*\n{$trackingUrl}\n\n"
            . "⚠️ *تذكير:* عند وصولك وتسليم الشحنة للمستلم، اطلب منه *كود الـ PIN* وأرسله هنا في الواتساب لتأكيد انتهاء التوصيل.";

        $waService->sendTextMessage($cleanPhone, $carrierMsg);

        // 2. Send secret PIN code & tracking link to Buyer
        $buyer = $load->order?->buyer;
        $pinCode = $load->meta['pin_code'] ?? ($trip?->pin_token ?? '4829');

        if ($buyer && $buyer->phone) {
            $waService->sendDeliveryPin($buyer->phone, $load, (string) $pinCode, $carrier);
        }

        // 3. Post confirmation in chat thread
        $this->notifyThread($load, "🚚 قام الناقل **{$carrier?->name}** بقبول مهمة النقل وتأكيدها عبر WhatsApp. الشحنة في الطريق الآن.");

        return ['handled' => true, 'success' => true];
    }

    /**
     * Decline a transport load
     */
    public function declineLoad(string $cleanPhone, int $loadId): array
    {
        $load = Load::with(['order.seller', 'carrier'])->find($loadId);

        if (!$load) {
            return ['handled' => true, 'success' => false];
        }

        $carrier = $this->findCarrierByPhone($cleanPhone) ?? $load->carrier;
        $waService = app(WhatsAppCloudApiService::class);

        $load->update([
            'carrier_id' => null,
            'status' => Load::ST_OPEN,
        ]);

        $waService->sendTextMessage($cleanPhone, "تم تسجيل اعتذارك عن هذه الكورسة. شكراً لك وسنوافيك بكورسات أخرى قريبة منك قريباً! 🫒");

        $this->notifyThread($load, "⚠️ اعتذر الناقل **{$carrier?->name}** عن المهمة. تم فتح طلب النقل لناقلين آخرين في المنطقة.");

        return ['handled' => true, 'success' => true];
    }

    /**
     * Verify delivery PIN entered by the carrier
     */
    public function verifyDeliveryPin(string $cleanPhone, string $pin): bool
    {
        $carrier = $this->findCarrierByPhone($cleanPhone);
        if (!$carrier) {
            return false;
        }

        // Find active in-transit or confirmed load for this carrier
        $load = Load::where('carrier_id', $carrier->id)
            ->whereIn('status', [Load::ST_CONFIRMED, Load::ST_MATCHED, Load::ST_IN_TRANSIT, Load::ST_OPEN])
            ->whereJsonContains('meta->pin_code', (string) $pin)
            ->latest()
            ->first();

        // Also check by Trip record pin_token
        if (!$load) {
            $trip = Trip::where('carrier_id', $carrier->id)
                ->where(function($q) use ($pin) {
                    $q->where('pin_token', $pin)
                      ->orWhere('pin_token', (int) $pin);
                })
                ->latest()
                ->first();

            if ($trip && !empty($trip->load_ids)) {
                $load = Load::whereIn('id', $trip->load_ids)->first();
            }
        }

        if (!$load) {
            return false;
        }

        // Complete delivery
        $load->update(['status' => Load::ST_DELIVERED]);

        $trip = Trip::whereJsonContains('load_ids', $load->id)->first();
        if ($trip) {
            $trip->update(['delivered_at' => now()]);
            event(new TripDelivered($trip->id, $trip->carrier_id));
        }

        $waService = app(WhatsAppCloudApiService::class);

        // 1. Congratulate carrier on WhatsApp
        $carrierMsg = "🎉 *تم تأكيد تسليم الشحنة بنجاح! (#{$load->id})*\n\n"
            . "كود الـ PIN صحيح (✅ {$pin}). تم توثيق انتهاء مهمة النقل واستلام المستلم للشحنة.\n\n"
            . "💰 *0% عمولة - خلاصك صافي 100% في يدك.* يعطيك الصحة ومرحباً بك في أي كورسة جديدة مع ZinToop!";

        $waService->sendTextMessage($cleanPhone, $carrierMsg);

        // 2. Notify Buyer on WhatsApp
        $buyer = $load->order?->buyer;
        if ($buyer && $buyer->phone) {
            $buyerMsg = "✅ *تم تأكيد استلام شحنتك بنجاح | ZinToop*\n\n"
                . "تم توثيق استلامك لشحنة زيت الزيتون عبر كود الـ PIN مع الناقل *{$carrier->name}*.\n\n"
                . "نتمنى لك تجربة ممتازة ونشكرك على ثقتك في منصة ZinToop! 🫒";
            $waService->sendTextMessage($buyer->phone, $buyerMsg);
        }

        // 3. Notify Chat thread
        $this->notifyThread($load, "🎉 **تم تسليم الشحنة بنجاح!** تم التحقق من كود الـ PIN وإغلاق المهمة.");

        Log::info("PIN verified successfully via WhatsApp for Load #{$load->id}");
        return true;
    }

    /**
     * Find user by phone number variations
     */
    protected function findCarrierByPhone(string $cleanPhone): ?User
    {
        $raw8 = substr($cleanPhone, -8);

        return User::where('phone', 'like', "%{$raw8}")
            ->orWhere('phone', $cleanPhone)
            ->orWhere('phone', "+{$cleanPhone}")
            ->first();
    }

    /**
     * Post a notification to the chat thread
     */
    protected function notifyThread(Load $load, string $text): void
    {
        try {
            $order = $load->order;
            if (!$order) return;

            $sellerId = $order->seller_id;
            $buyerId = $order->buyer_id;

            $thread = Thread::where('object_type', 'direct_message')
                ->whereJsonContains('participants', (int)$sellerId)
                ->whereJsonContains('participants', (int)$buyerId)
                ->first();

            if ($thread) {
                Message::create([
                    'thread_id' => $thread->id,
                    'sender_id' => $sellerId,
                    'body' => $text,
                    'meta' => ['type' => 'transport_update', 'load_id' => $load->id]
                ]);
            }
        } catch (\Throwable $e) {
            Log::error("Failed to notify thread for load update: {$e->getMessage()}");
        }
    }

    /**
     * Accept direct TransporterRequest from WhatsApp interactive button
     */
    public function acceptTransporterRequest(string $cleanPhone, int $reqId): array
    {
        $req = \App\Models\TransporterRequest::with(['user', 'carrier'])->find($reqId);
        if (!$req) {
            return ['handled' => true, 'success' => false, 'message' => 'الطلب غير موجود.'];
        }

        if ($req->status !== \App\Models\TransporterRequest::STATUS_PENDING) {
            $statusText = ($req->status === \App\Models\TransporterRequest::STATUS_ACCEPTED) ? 'تم قبوله مسبقاً.' : 'لم يعد متاحاً.';
            $waService = app(WhatsAppCloudApiService::class);
            $waService->sendTextMessage($cleanPhone, "ℹ️ هذا الطلب {$statusText}");
            return ['handled' => true, 'success' => false];
        }

        // Accept request
        $req->update([
            'status' => \App\Models\TransporterRequest::STATUS_ACCEPTED,
            'accepted_at' => now(),
        ]);

        // Cancel other pending requests from that user in this window
        \App\Models\TransporterRequest::where('user_id', $req->user_id)
            ->where('id', '!=', $req->id)
            ->where('status', \App\Models\TransporterRequest::STATUS_PENDING)
            ->update(['status' => \App\Models\TransporterRequest::STATUS_CANCELLED]);

        // Ensure direct chat thread exists
        $customer = $req->user;
        $carrier = $req->carrier;
        $thread = \App\Services\Chat::ensureThread('direct_message', 0, [$customer->id, $carrier->id]);
        $customerPhone = $customer->phone ?: ($customer->profile?->phone ?: 'غير مسجل');

        // Post official system message
        $systemBody = "📢 **[نظام الزين - تأكيد مهمة النقل 🚚]**\n\n"
            . "✅ **تم قبول وتأكيد المهمة بنجاح من طرف الناقل:** {$carrier->name}\n\n"
            . "📞 **رقم هاتف الحريف:** `{$customerPhone}`\n"
            . "👉 **إشعار للناقل:** يمكنك الآن الاتصال بالحريف مباشرة عبر الهاتف لتنسيق مسار النقل والتوقيت الدقيق.\n\n"
            . "📋 **تفاصيل الكورسة:**\n"
            . "• الحمولة: {$req->weight} {$req->unit}\n"
            . "• مسار النقل: من **{$req->pickup_governorate}** إلى **{$req->dropoff_governorate}**\n"
            . "• المسافة المقدرة: ~{$req->distance_km} كم\n"
            . "• السعر التقديري العادل: ~{$req->estimated_cost} د.ت\n\n"
            . "📍 **ملاحظة هامة للحريف:** نرجو النقر على زر **«📍 إرسال موقعي الدقيق للناقل»** أدناه حتى يصل الناقل مباشرة إلى مكان التحميل بدون أن يضيع.";

        $msg = \App\Models\Message::create([
            'thread_id' => $thread->id,
            'sender_id' => $carrier->id,
            'body' => $systemBody,
            'attachments' => [
                'type' => 'transporter_request_accepted',
                'request_id' => $req->id,
                'customer_id' => $customer->id,
                'customer_name' => $customer->name,
                'customer_phone' => $customerPhone,
                'carrier_id' => $carrier->id,
                'carrier_name' => $carrier->name,
                'weight' => $req->weight,
                'unit' => $req->unit,
                'pickup' => $req->pickup_governorate,
                'dropoff' => $req->dropoff_governorate,
                'distance_km' => $req->distance_km,
                'estimated_cost' => $req->estimated_cost,
            ],
            'is_flagged' => false,
            'is_deleted' => false,
            'is_hidden' => false,
        ]);
        $thread->touch();

        // Broadcast if available
        try {
            broadcast(new \App\Events\MessageSent($msg))->toOthers();
        } catch (\Throwable $e) {}

        // Send WhatsApp reply to Carrier with customer's phone
        $waService = app(WhatsAppCloudApiService::class);
        $reply = "✅ *تم تأكيد وقبول الكورسة بنجاح! (#{$req->id})*\n\n"
            . "👤 *الحريف:* {$customer->name}\n"
            . "📞 *رقم هاتف الحريف:* {$customerPhone}\n"
            . "📍 *المسار:* من {$req->pickup_governorate} إلى {$req->dropoff_governorate}\n"
            . "💰 *السعر المقدر:* ~{$req->estimated_cost} د.ت\n\n"
            . "👉 يمكنك الاتصال بالحريف الآن مباشرة لتنسيق ساعة التحميل والمكان.";

        $waService->sendTextMessage($cleanPhone, $reply);

        // Also if customer has phone, notify customer on WhatsApp
        if (!empty($customer->phone)) {
            $custReply = "🚚 *مبروك! تم قبول طلب النقل الخاص بك*\n\n"
                . "قام الناقل *{$carrier->name}* بقبول طلبك لنقل شحنتك من {$req->pickup_governorate} إلى {$req->dropoff_governorate}.\n"
                . "📞 *رقم هاتف الناقل:* {$carrier->phone}\n\n"
                . "سيتصل بك الناقل قريباً للتنسيق، أو يمكنك الاتصال به مباشرة.";
            $waService->sendTextMessage($customer->phone, $custReply);
        }

        return ['handled' => true, 'success' => true];
    }

    /**
     * Decline direct TransporterRequest from WhatsApp interactive button
     */
    public function declineTransporterRequest(string $cleanPhone, int $reqId): array
    {
        $req = \App\Models\TransporterRequest::find($reqId);
        if ($req && $req->status === \App\Models\TransporterRequest::STATUS_PENDING) {
            $req->update(['status' => \App\Models\TransporterRequest::STATUS_DECLINED]);
        }
        $waService = app(WhatsAppCloudApiService::class);
        $waService->sendTextMessage($cleanPhone, "تم تسجيل اعتذارك عن هذه الكورسة. شكراً لك! 🚚");
        return ['handled' => true, 'success' => true];
    }
}
