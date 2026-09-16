<?php

namespace App\Jobs;

use App\Mail\UnreadMessageNotificationMail;
use App\Models\Message;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendUnreadMessageEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $messageId;
    public int $senderId;
    public int $recipientId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $messageId, int $senderId, int $recipientId)
    {
        $this->messageId = $messageId;
        $this->senderId = $senderId;
        $this->recipientId = $recipientId;
    }

    /**
     * Execute the job after 5 minutes delay.
     */
    public function handle(): void
    {
        $message = Message::with(['sender', 'thread'])->find($this->messageId);

        // If message was deleted, hidden, or does not exist, abort silently
        if (!$message || $message->is_hidden || $message->is_deleted) {
            Log::info("SendUnreadMessageEmailJob: Message #{$this->messageId} is deleted or hidden. Skipping email.");
            return;
        }

        // Check if message was ALREADY READ by the recipient
        if ($message->read_at !== null) {
            Log::info("SendUnreadMessageEmailJob: Message #{$this->messageId} was already read at {$message->read_at}. Email skipped.");
            return;
        }

        $recipient = User::find($this->recipientId);
        if (!$recipient || empty($recipient->email)) {
            Log::info("SendUnreadMessageEmailJob: Recipient #{$this->recipientId} has no valid email. Skipping.");
            return;
        }

        $sender = $message->sender ?? User::find($this->senderId);
        if (!$sender) {
            Log::info("SendUnreadMessageEmailJob: Sender #{$this->senderId} not found. Skipping.");
            return;
        }

        // Anti-spam throttling: 1 unread notification email per 10 minutes between the same pair
        $throttleKey = "notif_unread_email_{$this->recipientId}_{$this->senderId}";
        if (!Cache::add($throttleKey, 1, now()->addMinutes(10))) {
            Log::info("SendUnreadMessageEmailJob: Email alert for pair ({$this->senderId} -> {$this->recipientId}) throttled within 10 minutes.");
            return;
        }

        try {
            Mail::to($recipient->email)->send(new UnreadMessageNotificationMail($message, $sender, $recipient));
            Log::info("SendUnreadMessageEmailJob: Sent unread email alert for Message #{$this->messageId} to {$recipient->email}.");
        } catch (\Throwable $e) {
            Log::error("SendUnreadMessageEmailJob: Failed to send unread email to {$recipient->email}: " . $e->getMessage());
        }
    }
}
