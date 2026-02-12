<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CheckOutReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $daysUntil;

    public function __construct(Booking $booking, int $daysUntil = 0)
    {
        $this->booking = $booking->load('room');
        $this->daysUntil = $daysUntil;
    }

    public function envelope(): Envelope
    {
        $subject = $this->daysUntil == 0 
            ? 'Check-Out Today - PrimeLand Hotel' 
            : "Check-Out Reminder - {$this->daysUntil} Day(s) Remaining - PrimeLand Hotel";

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.check-out-reminder',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}


