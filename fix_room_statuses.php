<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Booking;
use App\Models\Room;
use App\Models\RoomCleaningLog;

echo "Checking for inconsistencies between Booking checkout status and Room status...\n";

// Find bookings that are checked out but the room is still marked as 'occupied'
// We look at bookings checked out in the last 7 days to be safe
$checkedOutBookings = Booking::where('check_in_status', 'checked_out')
    ->where('checked_out_at', '>=', now()->subDays(7))
    ->get();

$count = 0;

foreach ($checkedOutBookings as $booking) {
    if (!$booking->room_id) continue;

    $room = Room::find($booking->room_id);
    if (!$room) continue;

    // specific check for the user's report (corporate booking) or generally any inconsistency
    // If room status is 'occupied', but booking is checked out -> FIX IT
    // If room status is 'available', maybe they already marked it clean? careful.
    
    // We only fix if it looks "stuck" in occupied state
    if ($room->status === 'occupied') {
        echo "Found Mismatch: Room {$room->room_number} is 'occupied' but Booking #{$booking->booking_reference} is checked out.\n";
        
        $room->update(['status' => 'needs_cleaning']);
        
        RoomCleaningLog::create([
            'room_id' => $room->id,
            'status' => 'needs_cleaning',
            'notes' => 'System Auto-Correction: Booking checked out but room status not updated.',
        ]);
        
        echo " -> Fixed: Marked Room {$room->room_number} as 'needs_cleaning'.\n";
        $count++;
    }
}

echo "Done. Fixed $count rooms.\n";
