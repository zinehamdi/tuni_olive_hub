<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\TransporterRequest;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;

class DirectTransporterRequestReceived extends Notification
{
    use Queueable;

    public TransporterRequest $transporterRequest;
    public User $sender;

    public function __construct(TransporterRequest $transporterRequest, User $sender)
    {
        $this->transporterRequest = $transporterRequest;
        $this->sender = $sender;
    }

    public function via($notifiable): array
    {
        $channels = ['database'];

        // If email is configured
        if (!empty($notifiable->email)) {
            $channels[] = 'mail';
        }

        if (class_exists(WebPushChannel::class) && config('webpush.vapid.public_key')) {
            $channels[] = WebPushChannel::class;
        }

        return $channels;
    }

    public function toMail($notifiable): MailMessage
    {
        $locale = $notifiable->locale ?? app()->getLocale() ?: 'ar';
        $senderName = $this->sender->name;
        $senderPhone = $this->sender->phone ?: __('Not specified');
        $weight = "{$this->transporterRequest->weight} " . __($this->transporterRequest->unit ?: 'ton');
        $pickup = $this->transporterRequest->pickup_governorate . ($this->transporterRequest->pickup_delegation ? ' - ' . $this->transporterRequest->pickup_delegation : '');
        $dropoff = $this->transporterRequest->dropoff_governorate . ($this->transporterRequest->dropoff_delegation ? ' - ' . $this->transporterRequest->dropoff_delegation : '');
        $cost = round((float) $this->transporterRequest->estimated_cost);
        $dashboardUrl = route('dashboard', ['locale' => $locale]) . '#assigned-tasks';

        $isOil = ($this->transporterRequest->cargo_type ?? 'oil') === 'oil';
        $cargoLabelFr = $isOil ? "Huile d'olive (Citerne / Fûts) 🫙" : "Olives (Caisses / Vrac) 🫒";
        $cargoLabelEn = $isOil ? "Olive Oil (Cistern / Drums) 🫙" : "Olives (Crates / Bulk) 🫒";
        $cargoLabelAr = $isOil ? "صهريج زيت زيتون (Citerne / براميل) 🫙" : "زيتون فلاحي (حب / صناديق) 🫒";

        if ($locale === 'fr') {
            return (new MailMessage)
                ->subject("🚚 Nouvelle demande de transport urgente (#{$this->transporterRequest->id}) - ZinToop")
                ->greeting("Bonjour {$notifiable->name},")
                ->line("Le client **{$senderName}** vous a envoyé une demande directe de transport de marchandises.")
                ->line("🏷️ **Type de marchandise :** {$cargoLabelFr}")
                ->line("📦 **Poids / Volume :** {$weight}")
                ->line("📍 **Départ :** {$pickup}")
                ->line("🚩 **Arrivée :** {$dropoff} (~{$this->transporterRequest->distance_km} km)")
                ->line("💰 **Prix équitable estimé :** ~{$cost} TND")
                ->line("📞 **Téléphone du client :** {$senderPhone}")
                ->action("Accepter la course maintenant", $dashboardUrl)
                ->line("Veuillez répondre rapidement car cette demande expire dans quelques minutes.")
                ->salutation("L'équipe ZinToop Logistique");
        }

        if ($locale === 'en') {
            return (new MailMessage)
                ->subject("🚚 New Urgent Direct Transport Request (#{$this->transporterRequest->id}) - ZinToop")
                ->greeting("Hello {$notifiable->name},")
                ->line("Client **{$senderName}** has sent you a direct cargo transport request.")
                ->line("🏷️ **Cargo Type :** {$cargoLabelEn}")
                ->line("📦 **Weight / Volume :** {$weight}")
                ->line("📍 **Pickup :** {$pickup}")
                ->line("🚩 **Dropoff :** {$dropoff} (~{$this->transporterRequest->distance_km} km)")
                ->line("💰 **Estimated Fair Price :** ~{$cost} TND")
                ->line("📞 **Client Phone :** {$senderPhone}")
                ->action("Accept Mission Now", $dashboardUrl)
                ->line("Please respond promptly as this request expires shortly.")
                ->salutation("ZinToop Logistics Team");
        }

        // Default Arabic
        return (new MailMessage)
            ->subject("🚚 طلب نقل مباشر وعاجل جديد (#{$this->transporterRequest->id}) - منصة الزين")
            ->greeting("مرحباً سي {$notifiable->name}،")
            ->line("أرسل لك الحريف **{$senderName}** طلباً مباشراً لكورسة نقل عبر منصة الزين.")
            ->line("🏷️ **نوع الحمولة:** {$cargoLabelAr}")
            ->line("📦 **الوزن / الكمية:** {$weight}")
            ->line("📍 **مكان الانطلاق:** {$pickup}")
            ->line("🚩 **مكان الوصول:** {$dropoff} (~{$this->transporterRequest->distance_km} كم)")
            ->line("💰 **السعر التقديري الصافي:** ~{$cost} د.ت")
            ->line("📞 **رقم هاتف الحريف:** {$senderPhone}")
            ->action("قبول الكورسة والانطلاق", $dashboardUrl)
            ->line("نرجو الإسراع بالرد والقبول حتى لا تنتهي مهلة الطلب.")
            ->salutation("فريق منصة الزين اللوجستية (ZinToop)");
    }

    public function toArray($notifiable): array
    {
        $locale = $notifiable->locale ?? app()->getLocale() ?: 'ar';
        $senderName = $this->sender->name;
        $weight = "{$this->transporterRequest->weight} " . ($this->transporterRequest->unit === 'kg' ? 'كغ' : 'طن');
        $pickup = $this->transporterRequest->pickup_governorate;
        $dropoff = $this->transporterRequest->dropoff_governorate;
        $cost = round((float) $this->transporterRequest->estimated_cost);
        $isOil = ($this->transporterRequest->cargo_type ?? 'oil') === 'oil';
        $itemIcon = $isOil ? '🫙' : '🫒';

        $body = match($locale) {
            'fr' => "🚚 Demande directe de {$senderName} : {$itemIcon} {$weight} de {$pickup} à {$dropoff} (~{$cost} TND)",
            'en' => "🚚 Direct request from {$senderName}: {$itemIcon} {$weight} from {$pickup} to {$dropoff} (~{$cost} TND)",
            default => "🚚 طلب نقل مباشر من {$senderName}: حمولة {$itemIcon} {$weight} من {$pickup} إلى {$dropoff} (~{$cost} د.ت)",
        };

        return [
            'type' => 'direct_transporter_request',
            'transporter_request_id' => $this->transporterRequest->id,
            'cargo_type' => $this->transporterRequest->cargo_type ?? 'oil',
            'sender_id' => $this->sender->id,
            'sender_name' => $this->sender->name,
            'sender_phone' => $this->sender->phone,
            'weight' => $this->transporterRequest->weight,
            'unit' => $this->transporterRequest->unit,
            'pickup' => $pickup,
            'dropoff' => $dropoff,
            'distance_km' => $this->transporterRequest->distance_km,
            'estimated_cost' => $cost,
            'title' => __('New Direct Transport Request'),
            'body' => $body,
            'message' => $body,
            'icon' => '🚚',
            'url' => route('dashboard', ['locale' => $locale]) . '#assigned-tasks',
        ];
    }
}
