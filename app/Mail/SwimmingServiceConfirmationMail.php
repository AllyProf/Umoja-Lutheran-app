<?php

namespace App\Mail;

use App\Models\DayService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SwimmingServiceConfirmationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $dayService;

    /**
     * Create a new message instance.
     */
    public function __construct(DayService $dayService)
    {
        $this->dayService = $dayService->load('registeredBy');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Swimming Service Confirmation - PrimeLand Hotel',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.swimming-service-confirmation',
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


