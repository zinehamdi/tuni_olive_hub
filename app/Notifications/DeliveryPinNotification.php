<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Load;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DeliveryPinNotification extends Notification
{
    use Queueable;

    public Load $load;
    public string $pinCode;
    public ?string $tripCode;

    public function __construct(Load $load, string $pinCode, ?User $carrier = null, ?string $tripCode = null)
    {
        $this->load = $load;
        $this->pinCode = $pinCode;
        $this->carrier = $carrier;
        $this->tripCode = $tripCode;
    }

    protected function resolveTrackUrl(string $locale): string
    {
        $token = $this->tripCode;
        if (!$token) {
            $trip = \App\Models\Trip::whereJsonContains('load_ids', (int)$this->load->id)
                ->orWhereJsonContains('load_ids', (string)$this->load->id)
                ->latest('id')->first();
            $token = $trip?->sr_code ?? (string)$this->load->id;
        }

        return route('trip.publicTrack', ['locale' => $locale, 'token' => $token]);
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $carrierName = $this->carrier ? $this->carrier->name : __('Assigned Carrier');
        $carrierPhone = $this->carrier ? ($this->carrier->phone ?? __('Not provided')) : '';

        $locale = $notifiable->locale ?? app()->getLocale() ?: 'ar';
        $trackUrl = $this->resolveTrackUrl($locale);

        if ($locale === 'fr') {
            return (new MailMessage)
                ->subject("🔐 Code PIN de livraison : {$this->pinCode} - ZinToop")
                ->greeting("Bonjour {$notifiable->name},")
                ->line("Un transporteur ({$carrierName}) a été assigné pour acheminer votre commande.")
                ->line("Voici votre code secret de confirmation de livraison :")
                ->line("👉 **CODE PIN : {$this->pinCode}**")
                ->line("⚠️ **Consigne de sécurité :** Ne donnez ce code au transporteur qu'après avoir reçu et vérifié votre marchandise.")
                ->action("Suivre la livraison en direct", $trackUrl)
                ->salutation("L'équipe ZinToop");
        }

        if ($locale === 'en') {
            return (new MailMessage)
                ->subject("🔐 Your Delivery PIN Code: {$this->pinCode} - ZinToop")
                ->greeting("Hello {$notifiable->name},")
                ->line("A carrier ({$carrierName}) has been assigned to transport your order.")
                ->line("Here is your secret delivery confirmation PIN code:")
                ->line("👉 **PIN CODE: {$this->pinCode}**")
                ->line("⚠️ **Safety Notice:** Do not give this PIN to the carrier until your shipment arrives and is inspected.")
                ->action("Live Track Delivery", $trackUrl)
                ->salutation("ZinToop Team");
        }

        // Default Arabic
        return (new MailMessage)
            ->subject("🔐 رمز تأكيد استلام الشحنة: {$this->pinCode} - منصة الزين")
            ->greeting("مرحباً {$notifiable->name}،")
            ->line("تم تكليف الناقل ({$carrierName}) بنقل شحنتك بنجاح.")
            ->line("إليك رمز التأكيد السري الخاص باستلام الشحنة:")
            ->line("👉 **رمز الاستلام (PIN): {$this->pinCode}**")
            ->line("⚠️ **تنبيه أمان هام:** احتفظ بهذا الرمز ولا تسلمه للناقل إلا عند وصول الشحنة وفحصها والتأكد من سلامتها.")
            ->action("تتبع مسار الشحنة مباشرة", $trackUrl)
            ->salutation("فريق منصة الزين (ZinToop)");
    }

    public function toArray($notifiable): array
    {
        $locale = $notifiable->locale ?? app()->getLocale() ?: 'ar';
        $trackUrl = $this->resolveTrackUrl($locale);

        return [
            'title' => __('Delivery PIN: :pin', ['pin' => $this->pinCode]),
            'message' => __('Carrier :name was assigned to your order. Delivery PIN: :pin', ['name' => $this->carrier?->name ?? __('Carrier'), 'pin' => $this->pinCode]),
            'icon' => '🔐',
            'load_id' => $this->load->id,
            'pin_code' => $this->pinCode,
            'carrier_name' => $this->carrier?->name,
            'body' => __('Your delivery PIN code is:') . ' ' . $this->pinCode,
            'type' => 'delivery_pin',
            'url' => $trackUrl
        ];
    }
}
