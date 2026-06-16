<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MetaWebhookController extends Controller
{
    /**
     * Handle Webhook Verification from Meta
     */
    public function verify(Request $request)
    {
        $verifyToken = env('META_WEBHOOK_VERIFY_TOKEN');

        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        if ($mode && $token) {
            if ($mode === 'subscribe' && $token === $verifyToken) {
                Log::info('Meta Webhook Verified.');
                return response($challenge, 200);
            } else {
                Log::warning('Meta Webhook Verification Failed.', ['token' => $token]);
                return response('Forbidden', 403);
            }
        }

        return response('Bad Request', 400);
    }

    /**
     * Handle Incoming Messages from Meta
     */
    public function handle(Request $request)
    {
        $body = $request->all();

        if ($body['object'] === 'page') {
            foreach ($body['entry'] as $entry) {
                $webhookEvent = $entry['messaging'][0] ?? null;

                if ($webhookEvent) {
                    $senderPsid = $webhookEvent['sender']['id'];

                    // Check for referral (e.g. m.me/PageID?ref=VALUE)
                    $ref = null;
                    if (isset($webhookEvent['referral']['ref'])) {
                        $ref = $webhookEvent['referral']['ref'];
                    } elseif (isset($webhookEvent['postback']['referral']['ref'])) {
                        $ref = $webhookEvent['postback']['referral']['ref'];
                    }

                    if ($ref) {
                        Log::info("Received referral from $senderPsid: $ref");
                        // Here you can trigger Gemini AI or specific logic based on $ref
                    }

                    if (isset($webhookEvent['message'])) {
                        Log::info("Received message from $senderPsid", ['message' => $webhookEvent['message']]);
                        // Pass this to ChatbotController or Gemini
                    } else if (isset($webhookEvent['postback'])) {
                        Log::info("Received postback from $senderPsid", ['postback' => $webhookEvent['postback']]);
                    }
                }
            }
            return response('EVENT_RECEIVED', 200);
        }

        return response('Not Found', 404);
    }
}
