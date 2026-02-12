<?php

namespace App\Mail;

use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class GuiderConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $guiderName;
    public $company;
    public $bookings;
    public $checkIn;
    public $checkOut;
    public $generalNotes;

    /**
     * Create a new message instance.
     */
    public function __construct($guiderName, Company $company, $bookings, $checkIn, $checkOut, $generalNotes = null)
    {
        $this->guiderName = $guiderName;
        $this->company = $company;
        $this->bookings = $bookings;
        $this->checkIn = $checkIn;
        $this->checkOut = $checkOut;
        $this->generalNotes = $generalNotes;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Corporate Booking Confirmation - PrimeLand Hotel',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.guider-confirmation',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
