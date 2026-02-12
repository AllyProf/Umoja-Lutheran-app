<?php

namespace App\Mail;

use App\Models\RoomIssue;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RoomIssueReportedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $issue;

    /**
     * Create a new message instance.
     */
    public function __construct(RoomIssue $issue)
    {
        $this->issue = $issue->load(['room', 'reportedBy']);
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Room Issue Reported - ' . $this->issue->room->room_number)
                    ->view('emails.room-issue-reported');
    }
}
