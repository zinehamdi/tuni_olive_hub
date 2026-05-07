<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class NewDeal extends Notification
{
    use Queueable;

    protected $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable)
    {
        $productType = $this->order->listing->product->type === 'olive' ? __('Olives') : __('Olive Oil');
        return [
            'order_id' => $this->order->id,
            'buyer_name' => $this->order->buyer->name,
            'body' => __('New deal proposed for ') . $this->order->qty . ' ' . $this->order->unit . ' ' . __('of') . ' ' . $productType,
            'type' => 'deal',
            'url' => route('messages.show', $this->order->buyer_id)
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}
