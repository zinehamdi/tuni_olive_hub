<?php
/**
 * حدث توقيع عقد التصدير
 * Export Contract Signed Event
 */
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ExportContractSigned implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;
    public function __construct(public int $contract_id, public int $buyer_id) {}
    public function broadcastOn(): Channel { return new Channel('exports'); }
}
