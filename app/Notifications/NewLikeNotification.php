<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Cache;

class NewLikeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public User $liker;

    public function __construct(User $liker)
    {
        $this->liker = $liker;
    }

    public function via(object $notifiable): array
    {
        $channels = ['database', 'broadcast'];

        // Throttle: max 1 like email per user per 5 minutes
        $throttleKey = "notif_mail_like_{$notifiable->id}";
        if (Cache::add($throttleKey, 1, now()->addMinutes(5))) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $locale  = $notifiable->locale ?? app()->getLocale();
        $ctaUrl  = route('user.profile', ['locale' => $locale, 'user' => $this->liker->id]);
        $subject = "❤️ {$this->liker->name} أعجب بملفك | aime votre profil";

        return (new MailMessage)
            ->subject($subject)
            ->view('emails.notification', [
                'locale'         => $locale,
                'subject'        => $subject,
                'actor'          => $this->liker,
                'badge'          => '❤️ إعجاب جديد | Nouveau like',
                'badgeBg'        => '#FFF1F2',
                'badgeColor'     => '#9F1239',
                'badgeBorder'    => '#FECDD3',
                'accentColor'    => '#E11D48',
                'accentColorDark'=> '#BE123C',
                'headline'       => "{$this->liker->name} أعجب بملفك الشخصي",
                'bodyText'       => "أبدى {$this->liker->name} إعجابه بملفك الشخصي على منصة زينتوب. يمكنك عرض ملفه والتواصل معه مباشرة.",
                'previewText'    => null,
                'ctaUrl'         => $ctaUrl,
                'ctaLabel'       => '❤️ عرض الملف الشخصي',
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'       => 'like',
            'actor_id'   => $this->liker->id,
            'actor_name' => $this->liker->name,
            'url'        => route('user.profile', $this->liker->id),
        ];
    }
}
