<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class NewDeal extends Notification implements ShouldQueue
{
    use Queueable;

    public Order $order;
    public bool $sendMail;

    public function __construct(Order $order, bool $sendMail = false)
    {
        $this->order = $order;
        $this->sendMail = $sendMail;
    }

    public function via($notifiable): array
    {
        $channels = ['database', 'broadcast'];

        if (class_exists(WebPushChannel::class)) {
            $channels[] = WebPushChannel::class;
        }

        if ($this->sendMail) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail($notifiable): MailMessage
    {
        $buyerName = $this->order->buyer ? $this->order->buyer->name : __('A Buyer');
        $productName = $this->order->listing?->product?->variety ?? ($this->order->listing?->product?->type === 'olive' ? __('Olives') : __('Olive Oil'));
        $totalPrice = $this->order->total;
        $unitPrice = $this->order->price_unit;
        $qty = $this->order->qty;
        $unit = $this->order->unit;
        $otherUserId = $notifiable->id === $this->order->seller_id ? $this->order->buyer_id : $this->order->seller_id;
        $locale = $notifiable->locale ?? app()->getLocale() ?: 'ar';

        if ($locale === 'fr') {
            return (new MailMessage)
                ->subject("🫒 Nouvelle offre de deal ({$totalPrice} TND) - ZinToop")
                ->greeting("Bonjour {$notifiable->name},")
                ->line("{$buyerName} vous a proposé une offre commerciale pour {$qty} {$unit} de {$productName}.")
                ->line("💰 **Prix unitaire :** {$unitPrice} TND / {$unit}")
                ->line("💵 **Montant total :** {$totalPrice} TND")
                ->action("Consulter et répondre à l'offre", route('messages.show', ['locale' => $locale, 'user' => $otherUserId]))
                ->line("Vous pouvez accepter l'offre, proposer un nouveau prix ou négocier directement.")
                ->salutation("L'équipe ZinToop");
        }

        if ($locale === 'en') {
            return (new MailMessage)
                ->subject("🫒 New Deal Proposal ({$totalPrice} TND) - ZinToop")
                ->greeting("Hello {$notifiable->name},")
                ->line("{$buyerName} proposed a new deal for {$qty} {$unit} of {$productName}.")
                ->line("💰 **Unit Price:** {$unitPrice} TND / {$unit}")
                ->line("💵 **Total Amount:** {$totalPrice} TND")
                ->action("View & Respond to Deal", route('messages.show', ['locale' => $locale, 'user' => $otherUserId]))
                ->line("You can accept the deal, propose a counter-offer, or chat directly.")
                ->salutation("ZinToop Team");
        }

        // Default Arabic
        return (new MailMessage)
            ->subject("🫒 طلب صفقة جديد بقيمة ({$totalPrice} د.ت) - منصة الزين")
            ->greeting("مرحباً {$notifiable->name}،")
            ->line("لقد وصلك طلب صفقة جديد من ({$buyerName}) لشراء {$qty} {$unit} من ({$productName}).")
            ->line("💰 **السعر المقترح للوحدة:** {$unitPrice} د.ت / {$unit}")
            ->line("💵 **الإجمالي:** {$totalPrice} دينار تونسي")
            ->action("عرض الصفقة والموافقة عليها", route('messages.show', ['locale' => $locale, 'user' => $otherUserId]))
            ->line("يمكنك قبول الصفقة مباشرة، أو اقتراح تعديل على السعر من داخل المحادثة.")
            ->salutation("فريق منصة الزين (ZinToop)");
    }

    public function toArray($notifiable): array
    {
        $productType = $this->order->listing?->product?->type === 'olive' ? __('Olives') : __('Olive Oil');
        $otherUserId = $notifiable->id === $this->order->seller_id ? $this->order->buyer_id : $this->order->seller_id;
        
        return [
            'order_id' => $this->order->id,
            'buyer_name' => $this->order->buyer?->name,
            'body' => __('New deal proposed for ') . $this->order->qty . ' ' . __($this->order->unit) . ' ' . __('of') . ' ' . $productType,
            'type' => 'deal',
            'url' => route('messages.show', $otherUserId)
        ];
    }

    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }

    public function toWebPush($notifiable, $notification)
    {
        $productType = $this->order->listing?->product?->type === 'olive' ? __('Olives') : __('Olive Oil');
        $bodyText = __('New deal proposed for ') . $this->order->qty . ' ' . __($this->order->unit) . ' ' . __('of') . ' ' . $productType;

        return (new WebPushMessage)
            ->title(__('New Deal Proposal') . ' - ' . ($this->order->buyer?->name ?? 'ZinToop'))
            ->icon('/images/zintoop-logo.png')
            ->body($bodyText)
            ->action(__('View Deal'), 'view_deal')
            ->options(['TTL' => 1000]);
    }
}
