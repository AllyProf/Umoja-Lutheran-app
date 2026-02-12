<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StaffNewBookingMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking->load(['room']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Booking - ' . $this->booking->guest_name . ' - ' . $this->booking->booking_reference . ' - PrimeLand Hotel',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.staff-new-booking',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}



