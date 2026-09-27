<?php

namespace App\Mail;

use App\Models\Listing;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ListingUnderReviewMail extends Mailable
{
    use Queueable, SerializesModels;

    public Listing $listing;

    /**
     * Create a new message instance.
     */
    public function __construct(Listing $listing)
    {
        $this->listing = $listing;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $locale = $this->listing->seller?->locale ?? app()->getLocale();
        $subject = match ($locale) {
            'fr' => '⏳ Votre annonce est en cours de révision - ZinToop',
            'en' => '⏳ Your listing is under review - ZinToop',
            default => '⏳ عرضك قيد المراجعة حالياً - منصة زينتوب',
        };

        return new Envelope(
            subject: $subject,
            replyTo: [
                new \Illuminate\Mail\Mailables\Address('contact@zintoop.com', 'ZinToop Support'),
            ],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.listing_under_review',
            with: [
                'listing' => $this->listing,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
