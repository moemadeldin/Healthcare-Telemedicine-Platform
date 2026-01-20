<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

final class VerificationCodeMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public string $code,
        public string $firstName
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Email Verification Code',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.verification-code',
        );
    }
}
