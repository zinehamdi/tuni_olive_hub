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
}
