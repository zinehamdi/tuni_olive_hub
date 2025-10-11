<?php
/**
 * حدث فتح طلب عرض تصدير
 * Export RFQ Opened Event
 */
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ExportRfqOpened implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;
    public function __construct(public int $offer_id, public int $user_id) {}
    public function broadcastOn(): Channel { return new Channel('exports'); }
}
