<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Room;
use App\Models\Booking;
use Carbon\Carbon;

class CheckoutWorkflowDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create demo guest user
        $guest = User::firstOrCreate(
            ['email' => 'demo.guest@primeland.com'],
            [
                'name' => 'Demo Guest',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'is_active' => true,
            ]
        );

        // Get or create a room
        $room = Room::where('room_number', '101')->first();
        if (!$room) {
            $room = Room::create([
                'room_number' => '101',
                'room_type' => 'Single',
                'capacity' => 1,
                'bed_type' => 'Single',
                'price_per_night' => 50.00,
                'status' => 'occupied',
            ]);
        }

        // Create a booking that's checked in and ready for checkout today
        $checkIn = Carbon::today()->subDays(3);
        $checkOut = Carbon::today(); // Today is checkout day

        $booking = Booking::firstOrCreate(
            ['booking_reference' => 'DEMO' . strtoupper(uniqid())],
            [
                'guest_name' => $guest->name,
                'guest_email' => $guest->email,
                'guest_phone' => '+255712345678',
                'room_id' => $room->id,
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'status' => 'confirmed',
                'payment_status' => 'pending', // Not paid yet - will pay at checkout
                'check_in_status' => 'checked_in',
                'checked_in_at' => $checkIn->copy()->setTime(14, 0),
                'total_price' => $room->price_per_night * $checkIn->diffInDays($checkOut),
            ]
        );

        // Update room to occupied
        $room->update(['status' => 'occupied']);

        $this->command->info('Demo data created:');
        $this->command->info('- Guest: ' . $guest->email . ' (password: password)');
        $this->command->info('- Booking: ' . $booking->booking_reference);
        $this->command->info('- Room: ' . $room->room_number);
        $this->command->info('- Check-out Date: ' . $checkOut->format('Y-m-d'));
        $this->command->info('');
        $this->command->info('Workflow:');
        $this->command->info('1. Go to Reception > Check Out');
        $this->command->info('2. Click "Check Out" for booking ' . $booking->booking_reference);
        $this->command->info('3. Room will change to "Needs Cleaning"');
        $this->command->info('4. Go to Reception > Rooms Cleaning');
        $this->command->info('5. Click "Done" to mark room as cleaned');
        $this->command->info('6. Go back to Check Out and click "Payment"');
        $this->command->info('7. Process payment (Cash or PayPal)');
        $this->command->info('8. Guest account will be deactivated');
    }
}



