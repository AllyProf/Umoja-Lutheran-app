<?php

namespace App\Mail;

use App\Models\IssueReport;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StaffIssueStatusUpdateMail extends Mailable
{
    use Queueable, SerializesModels;

    public $issueReport;
    public $status;
    public $adminNotes;

    public function __construct(IssueReport $issueReport, string $status, ?string $adminNotes = null)
    {
        $this->issueReport = $issueReport->load(['booking.room', 'room', 'user']);
        $this->status = $status;
        $this->adminNotes = $adminNotes;
    }

    public function envelope(): Envelope
    {
        $subjects = [
            'pending' => 'Issue Report Status Update - ' . $this->issueReport->subject . ' - Umoja Lutheran Hostel',
            'in_progress' => 'Issue Report In Progress - ' . $this->issueReport->subject . ' - Umoja Lutheran Hostel',
            'resolved' => 'Issue Report Resolved - ' . $this->issueReport->subject . ' - Umoja Lutheran Hostel',
        ];

        return new Envelope(
            subject: $subjects[$this->status] ?? 'Issue Report Status Update - Umoja Lutheran Hostel',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.staff-issue-status-update',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}



