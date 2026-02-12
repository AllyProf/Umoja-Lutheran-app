<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $password;
    public $paymentPercentage;
    public $remainingAmount;
    public $generalNotes;

    /**
     * Create a new message instance.
     */
    public function __construct(Booking $booking, $password, $paymentPercentage = null, $remainingAmount = null, $generalNotes = null)
    {
        $this->booking = $booking->load('room');
        $this->password = $password;
        $this->paymentPercentage = $paymentPercentage ?? $booking->payment_percentage;
        $this->remainingAmount = $remainingAmount ?? ($booking->total_price - ($booking->amount_paid ?? 0));
        $this->generalNotes = $generalNotes;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Booking Confirmation - PrimeLand Hotel',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.booking-confirmation',
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
