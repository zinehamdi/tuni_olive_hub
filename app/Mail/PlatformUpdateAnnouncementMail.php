<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PlatformUpdateAnnouncementMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subjectTitle;

    /**
     * Create a new message instance.
     */
    public function __construct($subjectTitle = null)
    {
        $this->subjectTitle = $subjectTitle ?? '🚀 إطلاق التحديث الجديد لمنصة زين توب | ZinToop New Update';
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subjectTitle,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.platform_update_announcement',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
