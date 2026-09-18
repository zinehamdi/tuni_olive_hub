<?php

namespace App\Services\Bot;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppCloudApiService
{
    protected string $token;
    protected string $phoneNumberId;
    protected string $graphApiUrl = 'https://graph.facebook.com/v21.0';

    public function __construct(?string $token = null, ?string $phoneId = null)
    {
        $this->token = $token ?? (string) config('services.whatsapp.token', env('WHATSAPP_TOKEN', ''));
        $this->phoneNumberId = $phoneId ?? (string) config('services.whatsapp.phone_number_id', env('WHATSAPP_PHONE_NUMBER_ID', ''));
    }

    /**
     * Clean and format phone number for WhatsApp Cloud API (e.g. +21625777926 -> 21625777926)
     */
    public function formatPhone(string $phone): string
    {
        $cleaned = preg_replace('/[^0-9]/', '', $phone);
        
        // If starts with 00 (e.g. 00216), strip leading 00
        if (str_starts_with($cleaned, '00')) {
            $cleaned = substr($cleaned, 2);
        }

        // If 8 digits (Tunisian local format: 25777926), prepend 216
        if (strlen($cleaned) === 8) {
            $cleaned = '216' . $cleaned;
        }

        return $cleaned;
    }

    /**
     * Send standard text message on WhatsApp
     * Endpoint: POST /{phone_number_id}/messages
     */
    public function sendTextMessage(string $toPhone, string $message): array
    {
        $formattedTo = $this->formatPhone($toPhone);

        try {
            $response = Http::withToken($this->token)->post("{$this->graphApiUrl}/{$this->phoneNumberId}/messages", [
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => $formattedTo,
                'type' => 'text',
                'text' => [
                    'preview_url' => true,
                    'body' => $message,
                ],
            ]);

            if ($response->successful()) {
                Log::info("WhatsApp message sent successfully to {$formattedTo}");
                return ['success' => true, 'data' => $response->json()];
            }

            Log::error("Failed to send WhatsApp message to {$formattedTo}", [
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            return ['success' => false, 'error' => $response->body()];
        } catch (\Throwable $e) {
            Log::error("Exception sending WhatsApp message to {$formattedTo}: {$e->getMessage()}");
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Send Outbound Welcome Template to a newly registered user
     * Utility template with dynamic parameters: {{1}}=Name, {{2}}=Role, {{3}}=Custom Greeting
     */
    public function sendWelcomeTemplate(
        string $toPhone,
        string $userName,
        string $role = 'عضو',
        string $customWish = 'إن شاء الله بالتوفيق وموسم مبارك'
    , string $templateName = 'zintoop_welcome_user'): array
    {
        $formattedTo = $this->formatPhone($toPhone);

        try {
            $response = Http::withToken($this->token)->post("{$this->graphApiUrl}/{$this->phoneNumberId}/messages", [
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => $formattedTo,
                'type' => 'template',
                'template' => [
                    'name' => $templateName,
                    'language' => [
                        'code' => 'ar',
                    ],
                    'components' => [
                        [
                            'type' => 'body',
                            'parameters' => [
                                ['type' => 'text', 'text' => $userName],
                                ['type' => 'text', 'text' => $role],
                                ['type' => 'text', 'text' => $customWish],
                            ],
                        ],
                    ],
                ],
            ]);

            if ($response->successful()) {
                Log::info("WhatsApp welcome template sent successfully to {$formattedTo}");
                return ['success' => true, 'data' => $response->json()];
            }

            Log::error("Failed to send WhatsApp welcome template to {$formattedTo}", [
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            return ['success' => false, 'error' => $response->body()];
        } catch (\Throwable $e) {
            Log::error("Exception sending WhatsApp template to {$formattedTo}: {$e->getMessage()}");
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Send urgent notification to Platform Admin (+21625777926) when human intervention is needed
     */
    public function notifyAdmin(string $clientName, string $clientPhone, string $summary): array
    {
        $adminPhone = config('services.admin_phone', env('ADMIN_PHONE_ALERT', '21625777926'));

        $alertMessage = "🚨 *تنبيه تدخل بشري - منصة ZinToop*\n\n"
            . "👤 *العميل:* {$clientName}\n"
            . "📱 *الهاتف:* {$clientPhone}\n"
            . "📋 *موضوع الطلب:* {$summary}\n\n"
            . "⚡ *تم إيقاف الرد الآلي مؤقتاً لهذه المحادثة لتتمكن من متابعته مباشرة.*";

        return $this->sendTextMessage($adminPhone, $alertMessage);
    }

    /**
     * Send Interactive Quick Reply Buttons on WhatsApp with automatic text fallback
     */
    public function sendInteractiveButtons(string $toPhone, string $bodyText, array $buttons, ?string $headerText = null, ?string $footerText = null): array
    {
        $formattedTo = $this->formatPhone($toPhone);

        $interactivePayload = [
            'type' => 'button',
            'body' => ['text' => $bodyText],
            'action' => [
                'buttons' => array_map(function ($btn) {
                    return [
                        'type' => 'reply',
                        'reply' => [
                            'id' => (string) $btn['id'],
                            'title' => mb_substr((string) $btn['title'], 0, 20),
                        ],
                    ];
                }, array_slice($buttons, 0, 3)),
            ],
        ];

        if (!empty($headerText)) {
            $interactivePayload['header'] = ['type' => 'text', 'text' => $headerText];
        }

        if (!empty($footerText)) {
            $interactivePayload['footer'] = ['text' => $footerText];
        }

        try {
            $response = Http::withToken($this->token)->post("{$this->graphApiUrl}/{$this->phoneNumberId}/messages", [
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => $formattedTo,
                'type' => 'interactive',
                'interactive' => $interactivePayload,
            ]);

            if ($response->successful()) {
                Log::info("WhatsApp interactive buttons sent successfully to {$formattedTo}");
                return ['success' => true, 'data' => $response->json()];
            }

            Log::warning("Interactive buttons not accepted by WhatsApp for {$formattedTo} (status {$response->status()}), falling back to text message.");
            
            // Build clear text fallback
            $fallbackText = $bodyText . "\n\n";
            foreach ($buttons as $idx => $btn) {
                $fallbackText .= ($idx + 1) . "️⃣ " . $btn['title'] . "\n";
            }
            $fallbackText .= "\n👉 أجب برقم الخيار أو بالموافقة لتأكيد الكورسة.";

            return $this->sendTextMessage($formattedTo, $fallbackText);
        } catch (\Throwable $e) {
            Log::error("Exception sending WhatsApp interactive message: {$e->getMessage()}");
            return $this->sendTextMessage($formattedTo, $bodyText);
        }
    }

    /**
     * Send a comprehensive Transport Mission notification to a Carrier
     */
    public function sendTransportMission(string $toPhone, $load, $carrier, $client, array $estimate): array
    {
        $pickupGov = $load->pickupAddress->governorate ?? 'نقطة الانطلاق';
        $dropoffGov = $load->dropoffAddress->governorate ?? 'نقطة الوصول';
        $cleanQty = (float) $load->qty;
        $unitLabel = ($load->unit === 'liter' || $load->unit === 'l') ? 'لتر' : 'كغ';
        $qtyUnit = "{$cleanQty} {$unitLabel}";
        $cost = round($estimate['total_cost'] ?? 0);
        $dist = round($estimate['distance_km'] ?? 0);
        $tierName = $estimate['tier']['name_ar'] ?? 'شاحنة نقل';
        $baseUrl = config('app.url', 'https://zintoop.com');
        $trackingUrl = rtrim($baseUrl, '/') . '/ar/mobile/trip?id=' . $load->id;

        $bodyText = "🚚 *عرض مهمة نقل جديدة | ZinToop*\n\n"
            . "سلام سي *{$carrier->name}*، تم اختيارك لنقل شحنة زيت زيتون:\n\n"
            . "📍 *من:* {$pickupGov}\n"
            . "🏁 *إلى:* {$dropoffGov} (~{$dist} كم)\n"
            . "📦 *الحمولة:* {$qtyUnit} ({$tierName})\n"
            . "💰 *السعر التقديري الصافي:* *~{$cost} د.ت* (0% عمولة)\n\n"
            . "🗺️ *رابط المسار والخريطة الحية:*\n{$trackingUrl}\n\n"
            . "هل أنت متوفر وموافق على هذه الكورسة؟";

        $buttons = [
            ['id' => 'ACCEPT_LOAD_' . $load->id, 'title' => '✅ قبول الكورسة'],
            ['id' => 'DECLINE_LOAD_' . $load->id, 'title' => '❌ غير متوفر'],
        ];

        return $this->sendInteractiveButtons($toPhone, $bodyText, $buttons, "🫒 منصة ZinToop", "خلاص فوري 100% في يد الناقل");
    }

    /**
     * Send the Delivery PIN code and live tracking link to Buyer
     */
    public function sendDeliveryPin(string $toPhone, $load, string $pinCode, $carrier): array
    {
        $baseUrl = config('app.url', 'https://zintoop.com');
        $trackingUrl = rtrim($baseUrl, '/') . '/ar/mobile/trip?id=' . $load->id;
        $carrierName = $carrier->name ?? 'الناقل';
        $carrierPhone = $carrier->phone ?? '';

        $message = "🚚 *شحنتك في الطريق مع ZinToop*\n\n"
            . "مرحباً بك، شحنة زيت الزيتون الخاصة بك في طريقها إليك مع الناقل *{$carrierName}*" . ($carrierPhone ? " ({$carrierPhone})" : "") . ".\n\n"
            . "🔑 *كود تأكيد الاستلام السري الخاص بك:*\n"
            . "👉 *PIN: {$pinCode}*\n\n"
            . "⚠️ *ملاحظة هامة:* لا تسلّم هذا الكود للناقل إلا بعد وصوله والتأكد التام من استلام الشحنة ومطابقتها.\n\n"
            . "📍 *تتبع مسار الشاحنة مباشرة على الخريطة:*\n{$trackingUrl}";

        return $this->sendTextMessage($toPhone, $message);
    }

    /**
     * Send Carrier Welcome & WhatsApp Activation Handshake with Interactive Button
     */
    public function sendCarrierWelcomeHandshake(string $toPhone, $carrier): array
    {
        $carrierName = $carrier->name ?? 'الناقل';
        $capacity = !empty($carrier->camion_capacity) ? " (حمولة: {$carrier->camion_capacity} طن)" : "";

        $bodyText = "🚚 *مرحباً بك كناقل معتمد في ZinToop*\n\n"
            . "سلام سي *{$carrierName}*، تم تسجيل شاحنتك بنجاح{$capacity}.\n\n"
            . "📍 ستصلك هنا مباشرة كورسات نقل زيت الزيتون والزيتون القريبة من منطقتك.\n"
            . "💰 *0% عمولة - خلاصك صافي 100% في يدك مباشرة من العميل.*\n\n"
            . "👉 يرجى الضغط على الزر أدناه لتأكيد رقمك وتفعيل استقبال الكورسات فوراً:";

        $buttons = [
            ['id' => 'ACTIVATE_CARRIER_' . $carrier->id, 'title' => '✅ تفعيل الكورسات'],
        ];

        return $this->sendInteractiveButtons($toPhone, $bodyText, $buttons, "🫒 منصة ZinToop", "شبكة النقل الفلاحي التونسي");
    }
}

