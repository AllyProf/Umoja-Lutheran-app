<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ExpirationWarningMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $warningType;
    public $reminderType;

    /**
     * Create a new message instance.
     */
    public function __construct(Booking $booking, $warningType = null, $reminderType = null)
    {
        $this->booking = $booking->load('room');
        $this->warningType = $warningType; // 'final' (15 min) or 'hour' (1 hour)
        $this->reminderType = $reminderType; // '24h', '12h', '1h'
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = 'Payment Reminder - PrimeLand Hotel';
        
        if ($this->warningType === 'final') {
            $subject = 'URGENT: Your Booking Expires in 15 Minutes - PrimeLand Hotel';
        } elseif ($this->warningType === 'hour') {
            $subject = 'URGENT: Your Booking Expires in 1 Hour - PrimeLand Hotel';
        } elseif ($this->reminderType) {
            $timeText = $this->reminderType === '1h' ? '1 Hour' : ($this->reminderType === '12h' ? '12 Hours' : '24 Hours');
            $subject = "Payment Reminder: {$timeText} Until Booking Expires - PrimeLand Hotel";
        }

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.expiration-warning',
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
