<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Mail\FeedbackRequestMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendFeedbackRequests extends Command
{
    protected $signature = 'bookings:send-feedback-requests';
    protected $description = 'Send feedback request emails to guests 1-2 days after check-out';

    public function handle()
    {
        $this->info('Sending feedback request emails...');

        $now = Carbon::now();
        
        // Find bookings that were checked out 1-2 days ago
        // Only send to completed bookings that are checked out
        $bookings = Booking::where('status', 'completed')
            ->where('check_in_status', 'checked_out')
            ->whereNotNull('checked_out_at')
            ->whereDate('checked_out_at', '>=', $now->copy()->subDays(2))
            ->whereDate('checked_out_at', '<=', $now->copy()->subDay())
            ->with('room')
            ->get();

        if ($bookings->isEmpty()) {
            $this->info('No bookings ready for feedback requests.');
            return 0;
        }

        $sentCount = 0;
        $errorCount = 0;

        foreach ($bookings as $booking) {
            try {
                // Check if feedback email was already sent (you might want to add a flag to bookings table)
                // For now, we'll send it once per booking
                
                Mail::to($booking->guest_email)->send(new FeedbackRequestMail($booking));
                $sentCount++;
                
                $this->info("Feedback request sent to: {$booking->guest_email} (Booking: {$booking->booking_reference})");
            } catch (\Exception $e) {
                $errorCount++;
                $this->error("Failed to send feedback request to {$booking->guest_email}: " . $e->getMessage());
                \Log::error('Failed to send feedback request email', [
                    'booking_id' => $booking->id,
                    'booking_reference' => $booking->booking_reference,
                    'guest_email' => $booking->guest_email,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->info("Feedback request emails sent: {$sentCount} successful, {$errorCount} failed.");

        return 0;
    }
}








