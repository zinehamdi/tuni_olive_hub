<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CarrierVerificationStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public string $status;
    public ?string $reason;

    public function __construct(string $status, ?string $reason = null)
    {
        $this->status = $status;
        $this->reason = $reason;
    }

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $locale = $notifiable->locale ?? app()->getLocale() ?: 'ar';

        if ($this->status === 'approved') {
            if ($locale === 'fr') {
                return (new MailMessage)
                    ->subject("🛡️ Félicitations ! Votre compte transporteur est certifié - ZinToop")
                    ->greeting("Bonjour {$notifiable->name},")
                    ->line("Nous avons le plaisir de vous informer que vos documents ont été validés avec succès.")
                    ->line("👉 **Votre profil arbore désormais le badge exclusif : ✓ Transporteur Vérifié 🛡️**")
                    ->line("Ce badge renforce la confiance des producteurs et moulins et vous donne la priorité sur les courses de transport.")
                    ->action("Accéder à mon tableau de bord", route('dashboard', ['locale' => $locale]))
                    ->salutation("L'équipe ZinToop");
            }

            return (new MailMessage)
                ->subject("🛡️ تهانينا! تم توثيق حسابك كناقل رسمي معتمد - ZinToop")
                ->greeting("مرحباً {$notifiable->name}،")
                ->line("يسر إدارة منصة ZinToop إعلامك بأنه تم فحص وثائقك وقبولها بنجاح.")
                ->line("👉 **تم منح حسابك رسمياً شارة: ✓ ناقل موثق 🛡️**")
                ->line("تمنحك هذه الشارة أولوية كاملة في استقبال مهام نقل الزيتون وزيت الزيتون، وثقة مضاعفة من الفلاحين والمعاصر.")
                ->action("فتح لوحة التحكم الخاصة بي", route('dashboard', ['locale' => $locale]))
                ->salutation("فريق منصة ZinToop");
        }

        // Rejected
        $reasonText = $this->reason ?: __('Please ensure the documents are clear, readable, and matching your profile details.');

        if ($locale === 'fr') {
            return (new MailMessage)
                ->subject("⚠️ Mise à jour concernant la vérification de vos documents - ZinToop")
                ->greeting("Bonjour {$notifiable->name},")
                ->line("Votre demande de vérification de documents pour le compte transporteur nécessite une attention particulière.")
                ->line("📝 **Motif :** {$reasonText}")
                ->line("Vous pouvez soumettre de nouvelles photos nettes depuis votre tableau de bord.")
                ->action("Mettre à jour mes documents", route('dashboard', ['locale' => $locale]))
                ->salutation("L'équipe ZinToop");
        }

        return (new MailMessage)
            ->subject("⚠️ تنبيه بخصوص طلب توثيق وثائق الناقل - ZinToop")
            ->greeting("مرحباً {$notifiable->name}،")
            ->line("نود إعلامك بأن طلب توثيق حسابك كناقل يحتاج إلى مراجعة بعض الوثائق.")
            ->line("📝 **السبب :** {$reasonText}")
            ->line("بإمكانك إعادة رفع صور واضحة ومقروءة للوثائق من خلال لوحة التحكم الخاصة بك.")
            ->action("تحديث وثائقي الآن", route('dashboard', ['locale' => $locale]))
            ->salutation("فريق منصة ZinToop");
    }

    public function toArray($notifiable): array
    {
        $isApproved = $this->status === 'approved';
        return [
            'title' => $isApproved ? '🎉 تم توثيق حسابك كناقل موثق 🛡️' : '⚠️ تحديث بخصوص وثائق التوثيق',
            'message' => $isApproved 
                ? 'تهانينا! تم التحقق من وثائقك وحصلت على شارة ناقل موثق 🛡️.' 
                : 'طلب التوثيق يحتاج تعديل: ' . ($this->reason ?? 'يرجى إعادة رفع الوثائق بشكل أوضح.'),
            'icon' => $isApproved ? '🛡️' : '⚠️',
            'status' => $this->status,
            'type' => 'carrier_verification_status',
            'url' => route('dashboard', ['locale' => $notifiable->locale ?? 'ar']),
        ];
    }
}
