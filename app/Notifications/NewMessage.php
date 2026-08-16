<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Cache;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

class NewMessage extends Notification implements ShouldQueue
{
    use Queueable;

    public $message;
    public $sender;

    public function __construct($message, $sender)
    {
        $this->message = $message;
        $this->sender  = $sender;
    }

    public function via(object $notifiable): array
    {
        $channels = [WebPushChannel::class, 'database', 'broadcast'];

        // Add mail channel — throttled: max 1 email per 5 min per sender-recipient pair
        $throttleKey = "notif_mail_message_{$notifiable->id}_{$this->sender->id}";
        if (Cache::add($throttleKey, 1, now()->addMinutes(5))) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $locale   = $notifiable->locale ?? app()->getLocale();
        $ctaUrl   = route('messages.show', ['locale' => $locale, 'user' => $this->sender->id]);
        $subject  = "💬 رسالة جديدة من {$this->sender->name} | Nouveau message de {$this->sender->name}";

        return (new MailMessage)
            ->subject($subject)
            ->view('emails.notification', [
                'locale'         => $locale,
                'subject'        => $subject,
                'actor'          => $this->sender,
                'badge'          => '💬 رسالة جديدة | Nouveau message',
                'badgeBg'        => '#EFF6FF',
                'badgeColor'     => '#1D4ED8',
                'badgeBorder'    => '#BFDBFE',
                'accentColor'    => '#3B5998',
                'accentColorDark'=> '#2d4275',
                'headline'       => "رسالة جديدة من {$this->sender->name}",
                'bodyText'       => "لديك رسالة جديدة على منصة زينتوب. انقر لعرضها والرد عليها مباشرة.",
                'previewText'    => $this->message->body,
                'ctaUrl'         => $ctaUrl,
                'ctaLabel'       => '💬 عرض الرسالة والرد',
            ]);
    }

    public function toArray($notifiable): array
    {
        return [
            'body'        => $this->message->body,
            'sender_id'   => $this->sender->id,
            'sender_name' => $this->sender->name,
            'type'        => 'message',
            'url'         => route('messages.show', $this->sender->id),
        ];
    }

    public function toWebPush($notifiable, $notification): WebPushMessage
    {
        return (new WebPushMessage)
            ->title('New Message from ' . $this->sender->name)
            ->icon('/images/zintoop-logo.png')
            ->body(str()->limit($this->message->body, 100))
            ->action('View Message', 'view_message')
            ->options(['TTL' => 1000]);
    }
}

