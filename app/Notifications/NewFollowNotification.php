<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Cache;

class NewFollowNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public User $follower;

    public function __construct(User $follower)
    {
        $this->follower = $follower;
    }

    public function via(object $notifiable): array
    {
        $channels = ['database', 'broadcast'];

        // Throttle: max 1 follow email per user per 5 minutes
        $throttleKey = "notif_mail_follow_{$notifiable->id}";
        if (Cache::add($throttleKey, 1, now()->addMinutes(5))) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $locale  = $notifiable->locale ?? app()->getLocale();
        $ctaUrl  = route('user.profile', ['locale' => $locale, 'user' => $this->follower->id]);
        $subject = "👤 {$this->follower->name} بدأ يتابعك | vous suit maintenant";

        return (new MailMessage)
            ->subject($subject)
            ->view('emails.notification', [
                'locale'         => $locale,
                'subject'        => $subject,
                'actor'          => $this->follower,
                'badge'          => '👤 متابع جديد | Nouveau abonné',
                'badgeBg'        => '#F0FDF4',
                'badgeColor'     => '#065F46',
                'badgeBorder'    => '#A7F3D0',
                'accentColor'    => '#6A8F3B',
                'accentColorDark'=> '#5a7a2f',
                'headline'       => "{$this->follower->name} بدأ يتابع ملفك الشخصي",
                'bodyText'       => "انضم {$this->follower->name} إلى قائمة متابعيك على منصة زينتوب. يمكنك عرض ملفه الشخصي والتواصل معه مباشرة.",
                'previewText'    => null,
                'ctaUrl'         => $ctaUrl,
                'ctaLabel'       => '👤 عرض الملف الشخصي',
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'          => 'follow',
            'actor_id'      => $this->follower->id,
            'actor_name'    => $this->follower->name,
            'url'           => route('user.profile', $this->follower->id),
        ];
    }
}
