<?php

declare(strict_types=1);

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TripDelivered implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;
    public function __construct(public int $trip_id, public int $carrier_id) {}
    public function broadcastOn(): Channel { return new Channel('trips'); }
}
