<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Room;
use App\Models\Booking;
use Carbon\Carbon;

class NewGuestDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a new demo guest user
        $guest = User::firstOrCreate(
            ['email' => 'newguest@primeland.com'],
            [
                'name' => 'John Smith',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'is_active' => true,
            ]
        );

        // Get or create a room for this guest
        $room = Room::where('room_number', '102')->first();
        if (!$room) {
            $room = Room::create([
                'room_number' => '102',
                'room_type' => 'Double',
                'capacity' => 2,
                'bed_type' => 'Double',
                'price_per_night' => 75.00,
                'status' => 'occupied',
            ]);
        } else {
            // Ensure room is occupied
            $room->update(['status' => 'occupied']);
        }

        // Create a booking that's checked in and ready for checkout today
        $checkIn = Carbon::today()->subDays(2);
        $checkOut = Carbon::today(); // Today is checkout day

        $booking = Booking::firstOrCreate(
            ['booking_reference' => 'NEWGUEST' . strtoupper(uniqid())],
            [
                'guest_name' => $guest->name,
                'guest_email' => $guest->email,
                'guest_phone' => '+255712345679',
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

        $this->command->info('========================================');
        $this->command->info('NEW GUEST DEMO ACCOUNT CREATED');
        $this->command->info('========================================');
        $this->command->info('');
        $this->command->info('Guest Account Details:');
        $this->command->info('  Email: ' . $guest->email);
        $this->command->info('  Password: password');
        $this->command->info('  Name: ' . $guest->name);
        $this->command->info('  Status: Active');
        $this->command->info('');
        $this->command->info('Booking Details:');
        $this->command->info('  Booking Reference: ' . $booking->booking_reference);
        $this->command->info('  Room: ' . $room->room_number . ' (' . $room->room_type . ')');
        $this->command->info('  Check-in: ' . $checkIn->format('M d, Y'));
        $this->command->info('  Check-out: ' . $checkOut->format('M d, Y') . ' (TODAY)');
        $this->command->info('  Status: Checked In');
        $this->command->info('  Payment Status: Pending');
        $this->command->info('');
        $this->command->info('========================================');
        $this->command->info('TESTING WORKFLOW:');
        $this->command->info('========================================');
        $this->command->info('');
        $this->command->info('1. LOGIN AS GUEST:');
        $this->command->info('   - Email: ' . $guest->email);
        $this->command->info('   - Password: password');
        $this->command->info('   - URL: http://127.0.0.1:8000/customer/dashboard');
        $this->command->info('');
        $this->command->info('2. LOGIN AS RECEPTION:');
        $this->command->info('   - Email: reception@primeland.com');
        $this->command->info('   - Password: password');
        $this->command->info('   - URL: http://127.0.0.1:8000/reception/dashboard');
        $this->command->info('');
        $this->command->info('3. CHECKOUT WORKFLOW:');
        $this->command->info('   a. Reception > Check Out');
        $this->command->info('   b. Click "Check Out" for booking ' . $booking->booking_reference);
        $this->command->info('   c. Room status changes to "Needs Cleaning"');
        $this->command->info('   d. Reception > Rooms Cleaning');
        $this->command->info('   e. Click "Done" to mark room as cleaned');
        $this->command->info('   f. Reception > Check Out > Click "Payment"');
        $this->command->info('   g. Process payment (Cash or PayPal)');
        $this->command->info('   h. Guest account will be deactivated');
        $this->command->info('');
        $this->command->info('========================================');
    }
}



