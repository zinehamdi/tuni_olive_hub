<?php

namespace App\Notifications;

use App\Models\DealRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class NewDealRequest extends Notification
{
    use Queueable;

    protected $dealRequest;

    public function __construct(DealRequest $dealRequest)
    {
        $this->dealRequest = $dealRequest;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable)
    {
        $dealTitle = $this->dealRequest->deal->title[app()->getLocale()] ?? $this->dealRequest->deal->title['ar'] ?? 'N/A';
        return [
            'deal_request_id' => $this->dealRequest->id,
            'deal_id' => $this->dealRequest->deal_id,
            'buyer_name' => $this->dealRequest->name,
            'buyer_phone' => $this->dealRequest->phone,
            'body' => __('New request submitted for: ') . $dealTitle . ' - ' . __('From: ') . $this->dealRequest->name,
            'type' => 'deal_request',
            'url' => $notifiable->role === 'admin' ? route('admin.deals.requests.index') : '#'
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}
