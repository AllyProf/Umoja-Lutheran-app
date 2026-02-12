<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StaffPaymentReceivedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $amountPaid;
    public $paymentMethod;

    public function __construct(Booking $booking, $amountPaid, $paymentMethod = null)
    {
        $this->booking = $booking->load(['room']);
        $this->amountPaid = $amountPaid;
        $this->paymentMethod = $paymentMethod;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Payment Received - ' . number_format($this->amountPaid, 2) . ' TZS - ' . $this->booking->booking_reference . ' - PrimeLand Hotel',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.staff-payment-received',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}



