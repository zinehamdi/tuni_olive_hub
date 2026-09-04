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

        // Generate Contextual AI Response
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

        // Apply Natural Human Delay
        $minDelay = (int) BotSetting::get('comment_delay_min', '15');
        $maxDelay = (int) BotSetting::get('comment_delay_max', '45');
        $delaySeconds = rand($minDelay, $maxDelay);

        if ($this->channel === 'facebook_comment') {
            // Sleep for human delay before posting comment reply
            sleep($delaySeconds);
            $fbService->replyToComment($this->externalId, $reply);
        } elseif ($this->channel === 'facebook_dm') {
            sleep(rand(3, 8)); // Short typing delay for Messenger
            $fbService->sendMessengerText($this->externalId, $reply);
        } elseif ($this->channel === 'whatsapp') {
            sleep(rand(3, 8)); // Short typing delay for WhatsApp
            $waService->sendTextMessage($this->externalId, $reply);
        }
    }
}
