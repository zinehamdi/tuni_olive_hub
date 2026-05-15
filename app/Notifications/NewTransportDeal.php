<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Load;
use App\Models\User;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

class NewTransportDeal extends Notification
{
    use Queueable;

    public $load;
    public $sender;

    public function __construct(Load $load, User $sender)
    {
        $this->load = $load;
        $this->sender = $sender;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast', WebPushChannel::class];
    }

    public function toArray($notifiable)
    {
        return [
            'load_id' => $this->load->id,
            'sender_name' => $this->sender->name,
            'body' => __('You have a new transport deal for') . ' ' . $this->load->qty . ' ' . __($this->load->unit) . ' ' . __('of') . ' ' . __($this->load->kind),
            'type' => 'transport_deal',
            'url' => route('mobile.trip', ['id' => $this->load->id])
        ];
    }

    public function toWebPush($notifiable, $notification)
    {
        $bodyText = __('You have a new transport deal for') . ' ' . $this->load->qty . ' ' . __($this->load->unit) . ' ' . __('of') . ' ' . __($this->load->kind);
        
        return (new WebPushMessage)
            ->title(__('New Transport Deal') . ' - ' . $this->sender->name)
            ->icon('/images/zintooplogo3d.jpg')
            ->body($bodyText)
            ->action(__('View Deal'), 'view_deal')
            ->options(['TTL' => 1000]);
    }
}
