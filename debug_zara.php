<?php
$user = \App\Models\Guest::where('email', 'like', '%zara%')->first();
if (!$user) {
    echo "User Zara not found.\n";
    exit;
}
echo "User found: " . $user->email . "\n";

$bookings = \App\Models\Booking::where('guest_email', $user->email)->get();
echo "Count: " . $bookings->count() . "\n";

foreach($bookings as $b) {
    echo "Ref: " . $b->booking_reference . 
         " | Corporate: " . ($b->is_corporate_booking ? 'Yes' : 'No') . 
         " | Resp: " . ($b->payment_responsibility ?? 'N/A') . 
         " | Status: " . $b->status . "\n";
}
