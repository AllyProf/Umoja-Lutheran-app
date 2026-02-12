<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\Staff;
use App\Mail\ReceptionCheckInNotificationMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendReceptionCheckInNotifications extends Command
{
    protected $signature = 'reception:send-checkin-notifications';
    protected $description = 'Send check-in notification emails to reception staff when check-in time arrives';

    public function handle()
    {
        $this->info('Sending reception check-in notifications...');

        $now = Carbon::now();
        $today = Carbon::today();
        
        // Find bookings that are ready for check-in (check-in date is today)
        // Only send notification once per day for each booking
        $bookings = Booking::where('status', 'confirmed')
            ->whereIn('payment_status', ['paid', 'partial'])
            ->where(function($q) {
                $q->where('payment_status', 'paid')
                  ->orWhere(function($subQ) {
                      $subQ->where('payment_status', 'partial')
                           ->whereNotNull('amount_paid')
                           ->where('amount_paid', '>', 0);
                  });
            })
            ->where('check_in_status', 'pending')
            ->whereDate('check_in', $today) // Only send on check-in day
            ->with('room')
            ->get();

        if ($bookings->isEmpty()) {
            $this->info('No bookings ready for check-in notifications.');
            return 0;
        }

        // Get all reception staff emails
        $receptionStaff = Staff::where('role', 'reception')
            ->where('is_active', true)
            ->pluck('email')
            ->filter()
            ->toArray();

        if (empty($receptionStaff)) {
            $this->warn('No reception staff found. Please add reception staff emails.');
            return 1;
        }

        $sentCount = 0;
        $errorCount = 0;

        foreach ($bookings as $booking) {
            $checkInDate = Carbon::parse($booking->check_in);
            
            // Calculate time remaining
            $diffInHours = $now->diffInHours($checkInDate, false);
            $diffInMinutes = $now->diffInMinutes($checkInDate, false);
            
            $timeRemaining = null;
            if ($checkInDate->isToday()) {
                if ($diffInHours > 0) {
                    $timeRemaining = "{$diffInHours} hour(s) until check-in";
                } elseif ($diffInMinutes > 0) {
                    $timeRemaining = "{$diffInMinutes} minute(s) until check-in";
                } else {
                    $timeRemaining = "Check-in time has arrived!";
                }
            } else {
                $diffInDays = $now->diffInDays($checkInDate, false);
                if ($diffInDays == 1) {
                    $timeRemaining = "Tomorrow";
                } elseif ($diffInDays > 1) {
                    $timeRemaining = "{$diffInDays} day(s) remaining";
                }
            }

            // Send email to all reception staff
            foreach ($receptionStaff as $email) {
                try {
                    Mail::to($email)->send(new ReceptionCheckInNotificationMail($booking, $timeRemaining));
                    $this->info("✓ Check-in notification sent to {$email} for booking {$booking->booking_reference}");
                    $sentCount++;
                } catch (\Exception $e) {
                    $this->error("✗ Failed to send notification to {$email} for booking {$booking->booking_reference}: " . $e->getMessage());
                    $errorCount++;
                }
            }

            // Note: We could add a notification tracking field here if needed
            // For now, notifications are sent once per day when command runs
        }

        $this->info("\nSummary:");
        $this->info("  Sent: {$sentCount} notification(s)");
        if ($errorCount > 0) {
            $this->warn("  Errors: {$errorCount}");
        }

        return 0;
    }
}

