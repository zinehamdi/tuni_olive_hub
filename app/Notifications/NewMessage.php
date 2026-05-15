<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

class NewMessage extends Notification
{
    use Queueable;

    public $message;
    public $sender;

    public function __construct($message, $sender)
    {
        $this->message = $message;
        $this->sender = $sender;
    }

    public function via(object $notifiable): array
    {
        return [WebPushChannel::class, 'database', 'broadcast'];
    }

    public function toArray($notifiable)
    {
        return [
            'body' => $this->message->body,
            'sender_id' => $this->sender->id,
            'sender_name' => $this->sender->name,
            'type' => 'message',
            'url' => route('messages.show', $this->sender->id)
        ];
    }

    public function toWebPush($notifiable, $notification)
    {
        return (new WebPushMessage)
            ->title('New Message from ' . $this->sender->name)
            ->icon('/images/zintooplogo3d.jpg')
            ->body(str()->limit($this->message->body, 100))
            ->action('View Message', 'view_message')
            ->options(['TTL' => 1000]);
    }
}
