<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessBotEventJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MetaWebhookController extends Controller
{
    /**
     * Handle Webhook Verification from Meta (Facebook & WhatsApp)
     */
    public function verify(Request $request)
    {
        $verifyToken = config('services.meta.webhook_verify_token', env('META_WEBHOOK_VERIFY_TOKEN', 'zintoop_meta_webhook_secure_2026'));

        $mode = $request->query('hub_mode', $request->query('hub.mode'));
        $token = $request->query('hub_verify_token', $request->query('hub.verify_token'));
        $challenge = $request->query('hub_challenge', $request->query('hub.challenge'));

        if ($mode && $token) {
            if ($mode === 'subscribe' && $token === $verifyToken) {
                Log::info('Meta Webhook Verified Successfully.');
                return response($challenge, 200)->header('Content-Type', 'text/plain');
            } else {
                Log::warning('Meta Webhook Verification Failed.', ['provided_token' => $token]);
                return response('Forbidden', 403);
            }
        }

        return response('Bad Request', 400);
    }

    /**
     * Handle Incoming Events from Meta (Facebook Comments, Messenger DMs, and WhatsApp Messages)
     */
    public function handle(Request $request)
    {
        $body = $request->all();
        $object = $body['object'] ?? null;

        Log::info('Meta Webhook Event Received', ['object' => $object]);

        // 1. Handle Facebook Page Events (Comments & Messenger)
        if ($object === 'page') {
            $pageId = config('services.meta.page_id', env('META_PAGE_ID', '828942590302317'));

            foreach ($body['entry'] ?? [] as $entry) {
                // A. Direct Messenger DMs
                if (!empty($entry['messaging'])) {
                    foreach ($entry['messaging'] as $messagingEvent) {
                        $senderId = $messagingEvent['sender']['id'] ?? null;
                        if (!$senderId || $senderId === $pageId) continue;

                        $text = $messagingEvent['message']['text'] ?? null;
                        if (!empty($text)) {
                            Log::info("Dispatching Messenger Event for {$senderId}");
                            ProcessBotEventJob::dispatch(
                                'facebook_dm',
                                (string) $senderId,
                                (string) $text
                            );
                        }
                    }
                }

                // B. Facebook Feed Comments (when someone comments on a post)
                if (!empty($entry['changes'])) {
                    foreach ($entry['changes'] as $change) {
                        if (($change['field'] ?? '') === 'feed') {
                            $val = $change['value'] ?? [];
                            $item = $val['item'] ?? '';
                            $verb = $val['verb'] ?? '';

                            // Only process new comments not created by the page itself
                            if ($item === 'comment' && $verb === 'add') {
                                $fromId = $val['from']['id'] ?? '';
                                if ($fromId === $pageId) continue; // Don't reply to our own comments

                                $commentId = $val['comment_id'] ?? null;
                                $commentMessage = $val['message'] ?? '';
                                $commentAuthor = $val['from']['name'] ?? 'متابع';
                                $postId = $val['post_id'] ?? null;

                                if ($commentId && !empty($commentMessage)) {
                                    Log::info("Dispatching Facebook Comment Event for {$commentId}");
                                    ProcessBotEventJob::dispatch(
                                        'facebook_comment',
                                        (string) $commentId,
                                        (string) $commentMessage,
                                        (string) $commentAuthor,
                                        (string) $postId
                                    );
                                }
                            }
                        }
                    }
                }
            }

            return response('EVENT_RECEIVED', 200);
        }

        // 2. Handle WhatsApp Cloud API Events
        if ($object === 'whatsapp_business_account') {
            foreach ($body['entry'] ?? [] as $entry) {
                foreach ($entry['changes'] ?? [] as $change) {
                    $val = $change['value'] ?? [];
                    $messages = $val['messages'] ?? [];
                    $contacts = $val['contacts'] ?? [];

                    $contactName = $contacts[0]['profile']['name'] ?? null;

                    foreach ($messages as $msg) {
                        $fromPhone = $msg['from'] ?? null;
                        $type = $msg['type'] ?? null;

                        if ($fromPhone && $type === 'text') {
                            $textBody = $msg['text']['body'] ?? '';
                            if (!empty($textBody)) {
                                Log::info("Dispatching WhatsApp Message Event for {$fromPhone}");
                                ProcessBotEventJob::dispatch(
                                    'whatsapp',
                                    (string) $fromPhone,
                                    (string) $textBody,
                                    (string) $contactName
                                );
                            }
                        }
                    }
                }
            }

            return response('EVENT_RECEIVED', 200);
        }

        return response('EVENT_RECEIVED', 200);
    }
}
