<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class TemporaryPasswordMail extends Mailable
{
    public function __construct(
        public string $fullName,
        public string $username,
        public string $tempPassword,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: '[DENR-4A RDAC] Password Reset Request');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.temporary-password');
    }
}