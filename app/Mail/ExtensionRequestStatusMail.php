<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ExtensionRequestStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $status;

    public function __construct(Booking $booking, string $status)
    {
        $this->booking = $booking->load('room');
        $this->status = $status;
    }

    public function envelope(): Envelope
    {
        $subjects = [
            'approved' => 'Extension Request Approved - Umoja Lutheran Hostel',
            'rejected' => 'Extension Request Rejected - Umoja Lutheran Hostel',
        ];

        return new Envelope(
            subject: $subjects[$this->status] ?? 'Extension Request Update - Umoja Lutheran Hostel',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.extension-request-status',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}


