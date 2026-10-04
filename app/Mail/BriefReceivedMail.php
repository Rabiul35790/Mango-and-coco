<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BriefReceivedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $name,
        public string $product,
        public string $trackingId,
        public string $briefMessage,
        public string $trackUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: "Brief received — {$this->trackingId} 💌");
    }

    public function content(): Content
    {
        return new Content(view: 'emails.brief-received');
    }
}
