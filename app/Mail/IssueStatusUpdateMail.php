<?php

namespace App\Mail;

use App\Models\IssueReport;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class IssueStatusUpdateMail extends Mailable
{
    use Queueable, SerializesModels;

    public $issueReport;
    public $status;
    public $adminNotes;

    public function __construct(IssueReport $issueReport, string $status, ?string $adminNotes = null)
    {
        $this->issueReport = $issueReport->load(['booking.room', 'room']);
        $this->status = $status;
        $this->adminNotes = $adminNotes;
    }

    public function envelope(): Envelope
    {
        $subjects = [
            'pending' => 'Issue Report Update - PrimeLand Hotel',
            'in_progress' => 'Issue Report In Progress - PrimeLand Hotel',
            'resolved' => 'Issue Report Resolved - PrimeLand Hotel',
        ];

        return new Envelope(
            subject: $subjects[$this->status] ?? 'Issue Report Update - PrimeLand Hotel',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.issue-status-update',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}








