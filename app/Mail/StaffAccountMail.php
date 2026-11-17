<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class StaffAccountMail extends Mailable
{
    public function __construct(
        public string $fullName,
        public string $roleLabel,
        public string $officeName,
        public string $username,
        public string $email,
        public string $tempPassword,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: '[DENR-4A RDAC] Staff Account Created - Temporary Credentials');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.staff-account');
    }
}