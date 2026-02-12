<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LoginOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;
    public $userName;
    public $expiresInMinutes;

    /**
     * Create a new message instance.
     */
    public function __construct(string $otp, string $userName, int $expiresInMinutes = 10)
    {
        $this->otp = $otp;
        $this->userName = $userName;
        $this->expiresInMinutes = $expiresInMinutes;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Login Verification Code - PrimeLand Hotel',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.login-otp',
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
