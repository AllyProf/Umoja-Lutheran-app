<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Mail\ExpirationWarningMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendExpirationWarnings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:send-expiration-warnings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send expiration warning emails to guests before their booking expires';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        
        // Find bookings that are pending payment and haven't been cancelled
        $pendingBookings = Booking::where('status', 'pending')
            ->where('payment_status', 'pending')
            ->whereNotNull('expires_at')
            ->whereNull('cancelled_at')
            ->with('room')
            ->get();

        $warningsSent = 0;
        $remindersSent = 0;

        foreach ($pendingBookings as $booking) {
            if (!$booking->expires_at) {
                continue;
            }

            $expiresAt = Carbon::parse($booking->expires_at);
            $timeUntilExpiration = $now->diffInMinutes($expiresAt);
            $hoursUntilExpiration = $now->diffInHours($expiresAt);
            $daysUntilExpiration = $now->diffInDays($expiresAt);

            // Check if we need to send a warning (1 hour or 15 minutes before expiration)
            // Use precise time windows to avoid duplicate emails (command runs every 15 minutes)
            $shouldSendWarning = false;
            $warningType = null;

            if ($timeUntilExpiration <= 15 && $timeUntilExpiration > 0) {
                // 15 minutes before expiration - Final warning
                // Only send if we're within the last 15 minutes window
                $shouldSendWarning = true;
                $warningType = 'final'; // 15 minutes
            } elseif ($timeUntilExpiration <= 60 && $timeUntilExpiration > 45) {
                // 1 hour before expiration - First warning
                // Send between 60-45 minutes to avoid duplicates
                $shouldSendWarning = true;
                $warningType = 'hour'; // 1 hour
            }

            // Check if we need to send a reminder (24h, 12h, 1h before expiration)
            // Use precise time windows to avoid duplicate emails (command runs every 15 minutes)
            $shouldSendReminder = false;
            $reminderType = null;

            // 1 hour reminder (if not already sending 1h warning)
            if ($timeUntilExpiration <= 60 && $timeUntilExpiration > 45 && !$shouldSendWarning) {
                // 1 hour before expiration - Reminder
                $shouldSendReminder = true;
                $reminderType = '1h';
            } elseif ($hoursUntilExpiration <= 12 && $hoursUntilExpiration >= 11 && $timeUntilExpiration > 60) {
                // 12 hours before expiration - Reminder (send between 12-11 hours)
                $shouldSendReminder = true;
                $reminderType = '12h';
            } elseif ($hoursUntilExpiration <= 24 && $hoursUntilExpiration >= 23 && $timeUntilExpiration > 720) {
                // 24 hours before expiration - Reminder (send between 24-23 hours)
                $shouldSendReminder = true;
                $reminderType = '24h';
            }

            // Send warning email
            if ($shouldSendWarning) {
                try {
                    \App\Services\MailConfigService::configure();
                    Mail::to($booking->guest_email)->send(
                        new ExpirationWarningMail($booking, $warningType)
                    );
                    $warningsSent++;
                    $this->info("Sent {$warningType} warning to booking: {$booking->booking_reference}");
                } catch (\Exception $e) {
                    $this->error("Failed to send warning email for booking {$booking->booking_reference}: " . $e->getMessage());
                    \Log::error('Failed to send expiration warning email: ' . $e->getMessage());
                }
            }

            // Send reminder email
            if ($shouldSendReminder) {
                try {
                    \App\Services\MailConfigService::configure();
                    Mail::to($booking->guest_email)->send(
                        new ExpirationWarningMail($booking, 'reminder', $reminderType)
                    );
                    $remindersSent++;
                    $this->info("Sent {$reminderType} reminder to booking: {$booking->booking_reference}");
                } catch (\Exception $e) {
                    $this->error("Failed to send reminder email for booking {$booking->booking_reference}: " . $e->getMessage());
                    \Log::error('Failed to send payment reminder email: ' . $e->getMessage());
                }
            }

        }

        if ($warningsSent > 0 || $remindersSent > 0) {
            $this->info("Successfully sent {$warningsSent} warning(s) and {$remindersSent} reminder(s).");
        } else {
            $this->info("No warnings or reminders to send.");
        }

        return Command::SUCCESS;
    }
}

