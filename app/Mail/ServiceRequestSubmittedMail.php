<?php

namespace App\Mail;

use App\Models\ServiceRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ServiceRequestSubmittedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $serviceRequest;

    public function __construct(ServiceRequest $serviceRequest)
    {
        $this->serviceRequest = $serviceRequest->load(['booking.room', 'service']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Service Request Submitted - PrimeLand Hotel',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.service-request-submitted',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}


