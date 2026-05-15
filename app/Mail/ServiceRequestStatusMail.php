<?php

namespace App\Mail;

use App\Models\ServiceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ServiceRequestStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $serviceRequest;
    public $status;

    public function __construct(ServiceRequest $serviceRequest, string $status)
    {
        $this->serviceRequest = $serviceRequest->load(['booking.room', 'service']);
        $this->status = $status;
    }

    public function envelope(): Envelope
    {
        $subjects = [
            'approved' => 'Service Request Approved - Umoja Lutheran Hostel',
            'completed' => 'Service Request Completed - Umoja Lutheran Hostel',
            'cancelled' => 'Service Request Cancelled - Umoja Lutheran Hostel',
        ];

        return new Envelope(
            subject: $subjects[$this->status] ?? 'Service Request Update - Umoja Lutheran Hostel',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.service-request-status',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}


