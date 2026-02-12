<?php

namespace App\Mail;

use App\Models\Company;
use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class CompanyInvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $company;
    public $bookings;
    public $companyCharges;
    public $selfPaidCharges;
    public $totalCompanyPaid;
    public $checkIn;
    public $checkOut;
    public $generalNotes;

    /**
     * Create a new message instance.
     */
    public function __construct(Company $company, $bookings, $companyCharges, $selfPaidCharges, $totalCompanyPaid, $checkIn, $checkOut, $generalNotes = null)
    {
        $this->company = $company;
        $this->bookings = $bookings;
        $this->companyCharges = $companyCharges;
        $this->selfPaidCharges = $selfPaidCharges;
        $this->totalCompanyPaid = $totalCompanyPaid;
        $this->checkIn = $checkIn;
        $this->checkOut = $checkOut;
        $this->generalNotes = $generalNotes;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Corporate Booking Invoice - PrimeLand Hotel',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.company-invoice',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
