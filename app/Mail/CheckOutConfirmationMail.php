<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CheckOutConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $totalBillUsd;
    public $totalBillTsh;
    public $amountPaidTsh;

    /**
     * Create a new message instance.
     */
    public function __construct($booking, $totalBillUsd = null, $totalBillTsh = null, $amountPaidTsh = null)
    {
        $this->booking = $booking;
        $this->totalBillUsd = $totalBillUsd;
        $this->totalBillTsh = $totalBillTsh;
        $this->amountPaidTsh = $amountPaidTsh;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Check-Out Confirmation - PrimeLand Hotel',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.check-out-confirmation',
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








