<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewCarrierRegisteredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public User $carrier;

    public function __construct(User $carrier)
    {
        $this->carrier = $carrier;
    }

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $name = $this->carrier->name;
        $phone = $this->carrier->phone ?? __('Not provided');
        $capacity = $this->carrier->camion_capacity ? "{$this->carrier->camion_capacity} Kg" : __('Not specified');

        return (new MailMessage)
            ->subject("🚚 ناقل جديد انضم للمنصة: {$name} - ZinToop Admin")
            ->greeting("مرحباً {$notifiable->name} (إدارة المنصة)،")
            ->line("تم تسجيل ناقل جديد في منصة ZinToop وينتظر التفعيل وتوثيق الحساب:")
            ->line("👤 **اسم الناقل :** {$name}")
            ->line("📞 **الهاتف :** {$phone}")
            ->line("⚖️ **حمولة الشاحنة :** {$capacity}")
            ->line("📧 **البريد :** {$this->carrier->email}")
            ->action("مراجعة الحساب في لوحة الإدارة", route('admin.carrier_verifications', ['locale' => 'ar']))
            ->salutation("نظام إدارة ZinToop");
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => '🚚 ناقل جديد انضم للمنصة',
            'message' => "انضم الناقل {$this->carrier->name} ({$this->carrier->phone}) إلى المنصة.",
            'icon' => '🚚',
            'carrier_id' => $this->carrier->id,
            'type' => 'new_carrier_registered',
            'url' => route('admin.carrier_verifications', ['locale' => 'ar']),
        ];
    }
}
