<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Order;
use App\Models\User;
use App\Notifications\NewDeal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendUnreadDealEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $orderId;
    public int $recipientId;

    public function __construct(int $orderId, int $recipientId)
    {
        $this->orderId = $orderId;
        $this->recipientId = $recipientId;
    }

    public function handle(): void
    {
        $order = Order::with(['buyer', 'seller', 'listing.product'])->find($this->orderId);
        $recipient = User::find($this->recipientId);

        if (!$order || !$recipient) {
            return;
        }

        // Only send if the order is STILL pending and not yet confirmed/cancelled
        if ($order->status !== Order::STATUS_PENDING) {
            Log::info("Delayed Deal email skipped: Order #{$order->id} is already {$order->status}.");
            return;
        }

        // Send high-priority deal email notification
        try {
            $recipient->notify(new NewDeal($order, true));
            Log::info("Delayed Deal email sent to {$recipient->email} for Order #{$order->id}.");
        } catch (\Throwable $e) {
            Log::error("Failed to send delayed deal email: " . $e->getMessage());
        }
    }
}
