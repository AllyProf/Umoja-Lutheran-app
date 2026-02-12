<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReceptionCheckInNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $timeRemaining;

    /**
     * Create a new message instance.
     */
    public function __construct(Booking $booking, $timeRemaining = null)
    {
        $this->booking = $booking;
        $this->timeRemaining = $timeRemaining;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $checkInDate = \Carbon\Carbon::parse($this->booking->check_in);
        $now = \Carbon\Carbon::now();
        
        $subject = "Check-In Alert: Guest {$this->booking->guest_name} - {$checkInDate->format('M d, Y')}";
        
        if ($checkInDate->isToday()) {
            $subject = "URGENT: Check-In Today - {$this->booking->guest_name}";
        }

        return $this->subject($subject)
                    ->view('emails.reception-check-in-notification')
                    ->with([
                        'booking' => $this->booking,
                        'checkInDate' => $checkInDate,
                        'timeRemaining' => $this->timeRemaining,
                    ]);
    }
}









