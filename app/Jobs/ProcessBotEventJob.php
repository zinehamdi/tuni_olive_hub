<?php

namespace App\Jobs;

use App\Services\Bot\EzzitouniSocialEngine;
use App\Services\Bot\MetaGraphApiService;
use App\Services\Bot\WhatsAppCloudApiService;
use App\Models\BotSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessBotEventJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $channel; // facebook_comment, facebook_dm, whatsapp
    public string $externalId; // comment_id, psid, phone
    public string $messageText;
    public ?string $userName;
    public ?string $postId;
    public ?string $postText;

    /**
     * Create a new job instance.
     */
    public function __construct(
        string $channel,
        string $externalId,
        string $messageText,
        ?string $userName = null,
        ?string $postId = null,
        ?string $postText = null
    ) {
        $this->channel = $channel;
        $this->externalId = $externalId;
        $this->messageText = $messageText;
        $this->userName = $userName;
        $this->postId = $postId;
        $this->postText = $postText;
    }

    /**
     * Execute the job.
     */
    public function handle(
        EzzitouniSocialEngine $engine,
        MetaGraphApiService $fbService,
        WhatsAppCloudApiService $waService
    ): void {
        Log::info("Processing Bot Event: {$this->channel} for ID: {$this->externalId}");

        // Atomic lock to prevent duplicate concurrent processing from Meta webhook retries
        $lockKey = "bot_reply_lock_" . md5($this->channel . '_' . $this->externalId);
        $lock = \Illuminate\Support\Facades\Cache::lock($lockKey, 120);

        if (!$lock->get()) {
            Log::info("Duplicate execution skipped for {$this->externalId} (Lock already acquired).");
            return;
        }

        try {
            if ($this->channel === 'facebook_comment') {
                $existingConv = \App\Models\BotConversation::firstOrCreate(
                    ['channel' => 'facebook_comment', 'external_id' => $this->externalId],
                    ['user_name' => $this->userName, 'status' => 'automated']
                );

                if (!empty($existingConv->metadata['replied_publicly'])) {
                    Log::info("Comment {$this->externalId} already replied publicly, skipping duplicate event.");
                    return;
                }

                // Immediately mark as replied to prevent race condition before sleep
                $meta = $existingConv->metadata ?? [];
                $meta['replied_publicly'] = true;
                $existingConv->update(['metadata' => $meta]);

                // Like the comment first
                $fbService->likeComment($this->externalId);

                // Fetch post text from Meta Graph API or Post Directive if postId exists
                $postContent = $this->postText;
                if (empty($postContent) && !empty($this->postId)) {
                    $postContent = $fbService->fetchPostContent($this->postId);
                    if (empty($postContent)) {
                        $directive = \App\Models\FacebookPostDirective::where('post_id', $this->postId)
                            ->orWhere('post_url', 'like', "%{$this->postId}%")
                            ->first();
                        if ($directive) {
                            $postContent = $directive->hook_goal;
                        }
                    }
                }

                // Generate Contextual AI Response
                $result = $engine->generateResponse(
                    $this->messageText,
                    $this->channel,
                    $this->externalId,
                    $this->userName,
                    $this->postId,
                    $postContent
                );

                $reply = $result['reply'];
                if (empty($reply)) {
                    Log::info("No reply generated or bot silenced for {$this->externalId}");
                    return;
                }

                // Apply Natural Human Delay
                $minDelay = (int) BotSetting::get('comment_delay_min', '15');
                $maxDelay = (int) BotSetting::get('comment_delay_max', '45');
                $delaySeconds = rand($minDelay, $maxDelay);

                if ($delaySeconds > 0) {
                    sleep($delaySeconds);
                }

                // 1. Post dynamic, contextual public comment reply (1 line)
                $publicText = $engine->generatePublicCommentReply($this->messageText);
                $replyRes = $fbService->replyToComment($this->externalId, $publicText);
                Log::info("Facebook comment reply result", ['res' => $replyRes]);

                // 2. Also send full consultative private Messenger message
                $privateRes = $fbService->sendPrivateReply($this->externalId, $reply);
                Log::info("Facebook private reply result", ['res' => $privateRes]);
            } else {
                // Direct Messages (Messenger / WhatsApp)
                $result = $engine->generateResponse(
                    $this->messageText,
                    $this->channel,
                    $this->externalId,
                    $this->userName,
                    $this->postId,
                    $this->postText
                );

                $reply = $result['reply'];
                if (empty($reply)) {
                    Log::info("No reply generated or bot silenced for {$this->externalId}");
                    return;
                }

                if ($this->channel === 'facebook_dm') {
                    sleep(rand(2, 4)); // Short typing delay for Messenger
                    $fbService->sendMessengerText($this->externalId, $reply);
                } elseif ($this->channel === 'whatsapp') {
                    sleep(rand(2, 4)); // Short typing delay for WhatsApp
                    $waService->sendTextMessage($this->externalId, $reply);
                }
            }
        } finally {
            // Keep lock active for a short buffer to avoid immediate re-entry
            // Lock will automatically expire after TTL
        }
    }
}
