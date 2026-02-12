<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ExtensionRequestSubmittedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking->load('room');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Extension Request Submitted - PrimeLand Hotel',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.extension-request-submitted',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}


