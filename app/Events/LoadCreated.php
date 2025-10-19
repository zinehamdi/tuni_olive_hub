<?php

declare(strict_types=1);

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LoadCreated implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;
    public function __construct(public int $load_id, public int $owner_id) {}
    public function broadcastOn(): Channel { return new Channel('loads'); }
}
