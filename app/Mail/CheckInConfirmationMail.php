<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CheckInConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $wifiPassword;
    public $wifiNetworkName;

    /**
     * Create a new message instance.
     */
    public function __construct($booking, $wifiPassword = null, $wifiNetworkName = null)
    {
        $this->booking = $booking;
        $this->wifiPassword = $wifiPassword;
        $this->wifiNetworkName = $wifiNetworkName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Check-In Confirmation - PrimeLand Hotel',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.check-in-confirmation',
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








