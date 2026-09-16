<?php

namespace App\Mail;

use App\Models\Message;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UnreadMessageNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public Message $messageModel;
    public User $sender;
    public User $recipient;

    public function __construct(Message $messageModel, User $sender, User $recipient)
    {
        $this->messageModel = $messageModel;
        $this->sender = $sender;
        $this->recipient = $recipient;
    }

    public function envelope(): Envelope
    {
        $senderName = $this->sender->display_name ?: $this->sender->name;
        $subject = "💬 لديك رسالة غير مقروءة من {$senderName} | Message non lu de {$senderName}";

        return new Envelope(
            subject: $subject,
            replyTo: [
                new \Illuminate\Mail\Mailables\Address('contact@zintoop.com', 'ZinToop Messages'),
            ],
        );
    }

    public function content(): Content
    {
        $locale = $this->recipient->locale ?? app()->getLocale();
        $senderName = $this->sender->display_name ?: $this->sender->name;
        $ctaUrl = route('messages.show', ['locale' => $locale, 'user' => $this->sender->id]);
        $subject = "💬 لديك رسالة غير مقروءة من {$senderName} | Message non lu de {$senderName}";

        return new Content(
            view: 'emails.notification',
            with: [
                'locale' => $locale,
                'subject' => $subject,
                'actor' => $this->sender,
                'badge' => '💬 رسالة غير مقروءة | Message non lu',
                'badgeBg' => '#EFF6FF',
                'badgeColor' => '#1D4ED8',
                'badgeBorder' => '#BFDBFE',
                'accentColor' => '#3B5998',
                'accentColorDark' => '#2d4275',
                'headline' => "لديك رسالة غير مقروءة من {$senderName}",
                'bodyText' => "لديك رسالة جديدة على منصة زينتوب لم تقرأها بعد. انقر لعرضها والرد عليها مباشرة.",
                'previewText' => $this->messageModel->body,
                'ctaUrl' => $ctaUrl,
                'ctaLabel' => '💬 عرض الرسالة والرد',
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
