<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CheckExpiredBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:check-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and cancel bookings that have expired (10 minutes without payment)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        
        // Find bookings that are pending payment and have expired
        $expiredBookings = Booking::where('status', 'pending')
            ->where('payment_status', 'pending')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', $now)
            ->whereNull('cancelled_at')
            ->get();

        $count = 0;
        foreach ($expiredBookings as $booking) {
            $booking->update([
                'status' => 'cancelled',
                'payment_status' => 'cancelled',
                'cancellation_reason' => 'Booking expired automatically: Payment was not completed within 10 minutes of booking creation.',
                'cancelled_at' => $now,
            ]);
            
            $count++;
            $this->info("Cancelled expired booking: {$booking->booking_reference}");
        }

        if ($count > 0) {
            $this->info("Successfully cancelled {$count} expired booking(s).");
        } else {
            $this->info("No expired bookings found.");
        }

        return Command::SUCCESS;
    }
}
