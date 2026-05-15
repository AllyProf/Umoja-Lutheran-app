<?php

namespace App\Mail;

use App\Models\ServiceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StaffServiceRequestStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $serviceRequest;
    public $status;

    public function __construct(ServiceRequest $serviceRequest, string $status)
    {
        $this->serviceRequest = $serviceRequest->load(['booking.room', 'service', 'booking']);
        $this->status = $status;
    }

    public function envelope(): Envelope
    {
        $subjects = [
            'approved' => 'Service Request Approved - ' . $this->serviceRequest->service->name . ' - Umoja Lutheran Hostel',
            'completed' => 'Service Request Completed - ' . $this->serviceRequest->service->name . ' - Umoja Lutheran Hostel',
            'cancelled' => 'Service Request Cancelled - ' . $this->serviceRequest->service->name . ' - Umoja Lutheran Hostel',
        ];

        return new Envelope(
            subject: $subjects[$this->status] ?? 'Service Request Status Update - Umoja Lutheran Hostel',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.staff-service-request-status',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}



