<?php

namespace App\Mail;

use App\Models\ServiceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StaffNewServiceRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $serviceRequest;

    public function __construct(ServiceRequest $serviceRequest)
    {
        $this->serviceRequest = $serviceRequest->load(['booking.room', 'service', 'booking']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Service Request - ' . $this->serviceRequest->service->name . ' - PrimeLand Hotel',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.staff-new-service-request',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}



