<?php
$user = \App\Models\Guest::where('email', 'like', '%zara%')->first();
if (!$user) {
    echo "User Zara not found\n";
    exit;
}
$bookings = \App\Models\Booking::where('guest_email', $user->email)->with('serviceRequests')->get();
foreach($bookings as $b) {
    echo "Ref: " . $b->booking_reference . 
         " | Corp: " . ($b->is_corporate_booking ? '1' : '0') . 
         " | Resp: " . ($b->payment_responsibility ?? 'NULL') . 
         " | Status: " . $b->status . 
         " | Services: " . $b->serviceRequests->count() . "\n";
    foreach($b->serviceRequests as $s) {
        echo "   - Svc: " . $s->id . 
             " | Status: " . $s->status . 
             " | PayStatus: " . $s->payment_status . 
             " | Price: " . $s->total_price_tsh . "\n";
    }
}
