<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Mail\CheckInReminderMail;
use App\Mail\CheckOutReminderMail;
use App\Services\MailConfigService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendBookingReminders extends Command
{
    protected $signature = 'bookings:send-reminders';
    protected $description = 'Send check-in and check-out reminder emails';

    public function handle()
    {
        $this->info('Sending booking reminders...');

        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();
        $threeDaysLater = Carbon::today()->addDays(3);
        $sevenDaysLater = Carbon::today()->addDays(7);

        // Check-in reminders (7 days, 3 days, 1 day before, and today)
        $checkInBookings = Booking::where('status', 'confirmed')
            ->whereIn('payment_status', ['paid', 'partial']) // Include partial payments
            ->where('check_in_status', '!=', 'checked_in')
            ->whereDate('check_in', '>=', $today)
            ->whereDate('check_in', '<=', $sevenDaysLater)
            ->with('room')
            ->get();

        foreach ($checkInBookings as $booking) {
            $checkInDate = Carbon::parse($booking->check_in);
            $daysUntil = $today->diffInDays($checkInDate, false);

            // Send reminders at 7 days, 3 days, 1 day before, and on the day
            if ($daysUntil == 7 || $daysUntil == 3 || $daysUntil == 1 || $daysUntil == 0) {
                try {
                    MailConfigService::configure();
                    Mail::to($booking->guest_email)
                        ->send(new CheckInReminderMail($booking, $daysUntil));
                    $this->info("Check-in reminder email sent to {$booking->guest_email} (Booking: {$booking->booking_reference}, {$daysUntil} day(s) until check-in)");
                } catch (\Exception $e) {
                    $this->error("Failed to send check-in reminder email to {$booking->guest_email}: " . $e->getMessage());
                }
            }
        }

        // Check-out reminders (1 day before and today)
        $checkOutBookings = Booking::where('status', 'confirmed')
            ->whereIn('payment_status', ['paid', 'partial']) // Include partial payments
            ->where('check_in_status', 'checked_in')
            ->where(function($query) use ($today, $tomorrow) {
                $query->whereDate('check_out', $today)
                      ->orWhereDate('check_out', $tomorrow);
            })
            ->with('room')
            ->get();

        foreach ($checkOutBookings as $booking) {
            $checkOutDate = Carbon::parse($booking->check_out);
            $daysUntil = $today->diffInDays($checkOutDate, false);

            // Only send if check-out is today or tomorrow
            if ($daysUntil >= 0 && $daysUntil <= 1) {
                try {
                    MailConfigService::configure();
                    Mail::to($booking->guest_email)
                        ->send(new CheckOutReminderMail($booking, $daysUntil));
                    $this->info("Check-out reminder email sent to {$booking->guest_email} (Booking: {$booking->booking_reference}, {$daysUntil} day(s) until check-out)");
                } catch (\Exception $e) {
                    $this->error("Failed to send check-out reminder email to {$booking->guest_email}: " . $e->getMessage());
                }
            }
        }

        // Payment reminders for bookings with pending or partial payment
        $paymentReminderBookings = Booking::where('status', 'confirmed')
            ->whereIn('payment_status', ['pending', 'partial'])
            ->where('check_in_status', '!=', 'checked_out')
            ->whereDate('check_in', '>=', $today)
            ->whereDate('check_in', '<=', $sevenDaysLater)
            ->with('room')
            ->get();

        foreach ($paymentReminderBookings as $booking) {
            $checkInDate = Carbon::parse($booking->check_in);
            $daysUntil = $today->diffInDays($checkInDate, false);

            // Send payment reminders at 7 days, 3 days, and 1 day before check-in
            if ($daysUntil == 7 || $daysUntil == 3 || $daysUntil == 1) {
                try {
                    MailConfigService::configure();
                    Mail::to($booking->guest_email)
                        ->send(new \App\Mail\ExpirationWarningMail($booking, null, '24h'));
                    $this->info("Payment reminder email sent to {$booking->guest_email} (Booking: {$booking->booking_reference}, {$daysUntil} day(s) until check-in)");
                } catch (\Exception $e) {
                    $this->error("Failed to send payment reminder email to {$booking->guest_email}: " . $e->getMessage());
                }
            }
        }

        $this->info('Booking reminders sent successfully!');
        return 0;
    }
}


