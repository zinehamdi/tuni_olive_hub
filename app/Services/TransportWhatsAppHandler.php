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
}
