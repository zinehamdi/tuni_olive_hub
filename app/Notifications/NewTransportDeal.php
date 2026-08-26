<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Load;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class NewTransportDeal extends Notification
{
    use Queueable;

    public Load $load;
    public User $sender;

    public function __construct(Load $load, User $sender)
    {
        $this->load = $load;
        $this->sender = $sender;
    }

    public function via($notifiable): array
    {
        $channels = ['database', 'mail'];

        if (class_exists(WebPushChannel::class) && config('webpush.vapid.public_key')) {
            $channels[] = WebPushChannel::class;
        }

        return $channels;
    }

    public function toMail($notifiable): MailMessage
    {
        $senderName = $this->sender->name;
        $senderPhone = $this->sender->phone ?: __('Not specified');
        $kind = $this->load->kind === 'olive' ? __('Olives') : __('Olive Oil');
        $qty = $this->load->qty . ' ' . __($this->load->unit);
        $pickupLoc = ($this->load->pickup?->governorate ?? __('Seller Location')) . ($this->load->pickup?->delegation ? ' - ' . $this->load->pickup->delegation : '');
        $dropoffLoc = ($this->load->dropoffAddress?->governorate ?? __('Buyer Location')) . ($this->load->dropoffAddress?->delegation ? ' - ' . $this->load->dropoffAddress->delegation : '');
        $locale = $notifiable->locale ?? app()->getLocale() ?: 'ar';

        if ($locale === 'fr') {
            return (new MailMessage)
                ->subject("🚚 Nouvelle mission de transport assignée (#{$this->load->id}) - ZinToop")
                ->greeting("Bonjour {$notifiable->name},")
                ->line("{$senderName} vous a assigné une nouvelle mission de transport.")
                ->line("📦 **Marchandise :** {$qty} de {$kind}")
                ->line("📍 **Lieu de chargement :** {$pickupLoc}")
                ->line("🚩 **Lieu de livraison :** {$dropoffLoc}")
                ->line("📞 **Téléphone du client :** {$senderPhone}")
                ->action("Voir et accepter la mission", route('mobile.trip', ['locale' => $locale, 'id' => $this->load->id]))
                ->line("Veuillez vous connecter pour accepter la mission et démarrer le trajet.")
                ->salutation("L'équipe ZinToop Logistique");
        }

        if ($locale === 'en') {
            return (new MailMessage)
                ->subject("🚚 New Transport Mission Assigned (#{$this->load->id}) - ZinToop")
                ->greeting("Hello {$notifiable->name},")
                ->line("{$senderName} has assigned you a new transport task.")
                ->line("📦 **Cargo :** {$qty} of {$kind}")
                ->line("📍 **Pickup Location :** {$pickupLoc}")
                ->line("🚩 **Delivery Location :** {$dropoffLoc}")
                ->line("📞 **Client Phone :** {$senderPhone}")
                ->action("View and Accept Mission", route('mobile.trip', ['locale' => $locale, 'id' => $this->load->id]))
                ->line("Please sign in to accept the task and start tracking.")
                ->salutation("ZinToop Logistics Team");
        }

        // Default Arabic
        return (new MailMessage)
            ->subject("🚚 مهمة نقل جديدة مخصصة لك (#{$this->load->id}) - منصة الزين")
            ->greeting("مرحباً {$notifiable->name}،")
            ->line("قام العميل ({$senderName}) باختيارك وتكليفك بمهمة نقل جديدة عبر منصة الزين.")
            ->line("📦 **نوع الحمولة والكمية:** {$qty} ({$kind})")
            ->line("📍 **مكان التحميل (الانطلاق):** {$pickupLoc}")
            ->line("🚩 **مكان التوصيل (الوصول):** {$dropoffLoc}")
            ->line("📞 **رقم هاتف العميل للتنسيق:** {$senderPhone}")
            ->action("عرض المهمة والموافقة عليها", route('mobile.trip', ['locale' => $locale, 'id' => $this->load->id]))
            ->line("يُرجى فتح الرابط لتأكيد قبول الحمولة وبدء الرحلة.")
            ->salutation("فريق منصة الزين (ZinToop)");
    }

    public function toArray($notifiable): array
    {
        $locale = $notifiable->locale ?? app()->getLocale() ?: 'ar';
        return [
            'title' => __('New Transport Mission'),
            'message' => __('You have been assigned a new transport mission (#:id) from :name', ['id' => $this->load->id, 'name' => $this->sender->name]),
            'icon' => '🚚',
            'load_id' => $this->load->id,
            'sender_id' => $this->sender->id,
            'sender_name' => $this->sender->name,
            'body' => __('You have a new transport deal for') . ' ' . $this->load->qty . ' ' . __($this->load->unit) . ' ' . __('of') . ' ' . __($this->load->kind),
            'type' => 'transport_deal',
            'url' => route('messages.show', ['locale' => $locale, 'user' => $this->sender->id])
        ];
    }

    public function toWebPush($notifiable, $notification)
    {
        $bodyText = __('You have a new transport deal for') . ' ' . $this->load->qty . ' ' . __($this->load->unit) . ' ' . __('of') . ' ' . __($this->load->kind);

        return (new WebPushMessage)
            ->title(__('New Transport Deal') . ' - ' . $this->sender->name)
            ->icon('/images/zintoop-logo.png')
            ->body($bodyText)
            ->action(__('View Deal'), 'view_deal')
            ->options(['TTL' => 1000]);
    }
}
