<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CarrierDocumentsSubmittedNotification extends Notification implements ShouldQueue
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

        return (new MailMessage)
            ->subject("🛡️ وثائق توثيق جديدة لناقل: {$name} - ZinToop Admin")
            ->greeting("مرحباً {$notifiable->name} (إدارة المنصة)،")
            ->line("قام الناقل ({$name}) برفع وثائق التحقق (البطاقة الرمادية، رخصة السياقة، بطاقة التعريف الوطنية) للحصول على شارة ناقل موثق 🛡️.")
            ->line("📞 **الهاتف :** {$phone}")
            ->line("📧 **البريد :** {$this->carrier->email}")
            ->action("فحص الوثائق واعتماد التوثيق", route('admin.carrier_verifications', ['locale' => 'ar']))
            ->salutation("نظام إدارة ZinToop");
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => '🛡️ وثائق توثيق ناقل جديدة للمراجعة',
            'message' => "رفع الناقل {$this->carrier->name} وثائقه للحصول على شارة ناقل موثق.",
            'icon' => '🛡️',
            'carrier_id' => $this->carrier->id,
            'type' => 'carrier_documents_submitted',
            'url' => route('admin.carrier_verifications', ['locale' => 'ar']),
        ];
    }
}
