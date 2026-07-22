<?php

namespace App\Mail;

use App\Models\Listing;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewListingNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public Listing $listing;

    public function __construct(Listing $listing)
    {
        $this->listing = $listing;
    }

    public function envelope(): Envelope
    {
        $variety  = $this->listing->product->variety ?? 'زيت زيتون';
        $type     = $this->listing->product->type === 'oil' ? '🫙 زيت زيتون' : '🫒 زيتون';
        $location = $this->listing->governorate ?? '';

        return new Envelope(
            subject: "🆕 عرض جديد في السوق: {$variety} {$type}" . ($location ? " — {$location}" : '') . " | ZinToop",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.new_listing_notification',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
