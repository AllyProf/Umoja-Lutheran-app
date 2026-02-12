<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StaffExtensionRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $status;

    public function __construct(Booking $booking, string $status = 'submitted')
    {
        $this->booking = $booking->load(['room']);
        $this->status = $status;
    }

    public function envelope(): Envelope
    {
        if ($this->status === 'submitted') {
            return new Envelope(
                subject: 'New Extension Request - ' . $this->booking->guest_name . ' - ' . $this->booking->booking_reference . ' - PrimeLand Hotel',
            );
        }

        $subjects = [
            'approved' => 'Extension Request Approved - ' . $this->booking->booking_reference . ' - PrimeLand Hotel',
            'rejected' => 'Extension Request Rejected - ' . $this->booking->booking_reference . ' - PrimeLand Hotel',
        ];

        return new Envelope(
            subject: $subjects[$this->status] ?? 'Extension Request Update - PrimeLand Hotel',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.staff-extension-request',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}



