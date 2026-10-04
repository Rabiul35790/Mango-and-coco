<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BriefDeliveredMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $name,
        public string $product,
        public string $trackingId,
        public ?string $deliveryUrl,
        public string $reviewUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: "Your personalised piece is ready! 🎉 ({$this->trackingId})");
    }

    public function content(): Content
    {
        return new Content(view: 'emails.brief-delivered');
    }
}
