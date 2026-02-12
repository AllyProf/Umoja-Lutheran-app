<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Booking;

$booking = Booking::where('booking_reference', 'CBKXWCHY4DJ')->with('room')->first();

if ($booking) {
    echo "Booking Reference: {$booking->booking_reference}\n";
    echo "Guest: {$booking->guest_name}\n";
    echo "Check-in Status: {$booking->check_in_status}\n";
    echo "Checked Out At: " . ($booking->checked_out_at ?? 'Not checked out') . "\n";
    
    if ($booking->room) {
        echo "\nRoom Number: {$booking->room->room_number}\n";
        echo "Room Status: {$booking->room->status}\n";
    } else {
        echo "\nNo room assigned to this booking.\n";
    }
} else {
    echo "Booking with reference 'CBKXWCHY4DJ' not found.\n";
}
