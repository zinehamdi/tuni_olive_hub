<?php
/**
 * حدث شحن عقد التصدير
 * Export Contract Shipping Event
 */
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ExportContractShipping implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;
    public function __construct(public int $contract_id) {}
    public function broadcastOn(): Channel { return new Channel('exports'); }
}
