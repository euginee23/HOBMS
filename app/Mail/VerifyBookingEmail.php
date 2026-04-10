<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerifyBookingEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $guestName,
        public readonly string $code,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Verify Your Email — HOBMS',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.verify-booking',
        );
    }
}
