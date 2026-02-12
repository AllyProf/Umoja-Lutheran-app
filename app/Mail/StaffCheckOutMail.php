<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StaffCheckOutMail extends Mailable
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
            subject: 'Guest Checked Out - ' . $this->booking->guest_name . ' - Room ' . $this->booking->room->room_number . ' - PrimeLand Hotel',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.staff-check-out',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}



