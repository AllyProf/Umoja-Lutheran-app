<?php

namespace App\Mail;

use App\Models\IssueReport;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StaffNewIssueReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $issueReport;

    public function __construct(IssueReport $issueReport)
    {
        $this->issueReport = $issueReport->load(['booking.room', 'room', 'user']);
    }

    public function envelope(): Envelope
    {
        $priority = ucfirst($this->issueReport->priority);
        $subject = "New Issue Report - {$priority} Priority - {$this->issueReport->subject} - PrimeLand Hotel";
        
        if ($this->issueReport->priority === 'urgent') {
            $subject = "URGENT: New Issue Report - {$this->issueReport->subject} - PrimeLand Hotel";
        }

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.staff-new-issue-report',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}



