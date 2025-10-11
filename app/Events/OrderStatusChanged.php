<?php

declare(strict_types=1);

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderStatusChanged implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;
    public function __construct(public int $order_id, public string $from, public string $to) {}
    public function broadcastOn(): Channel { return new Channel('orders'); }
}
