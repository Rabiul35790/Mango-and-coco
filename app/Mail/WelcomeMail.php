<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $name,
        public string $accountUrl,
        public string $shopUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Welcome to Mango&Coco 🐣');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.welcome');
    }
}
