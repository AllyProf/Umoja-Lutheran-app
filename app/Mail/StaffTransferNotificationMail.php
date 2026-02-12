<?php

namespace App\Mail;

use App\Models\ShoppingListItem;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StaffTransferNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $items;
    public $department;
    public $transferredBy;

    /**
     * Create a new message instance.
     */
    public function __construct($items, $department, $transferredBy)
    {
        $this->items = $items;
        $this->department = $department;
        $this->transferredBy = $transferredBy;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Items Transferred to ' . $this->department . ' Department - PrimeLand Hotel',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.staff-transfer-notification',
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
