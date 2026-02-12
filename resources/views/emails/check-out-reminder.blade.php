<x-mail::message>
# Check-Out Reminder

Hello {{ $booking->guest_name }},

@if($daysUntil == 0)
**Your check-out is today!** We hope you enjoyed your stay at PrimeLand Hotel.
@else
Your check-out is in **{{ $daysUntil }} day(s)**. We hope you're enjoying your stay!
@endif

## Check-Out Details

**Check-Out Date:** {{ \Carbon\Carbon::parse($booking->check_out)->format('F d, Y') }}

**Check-Out Time:** {{ \App\Models\HotelSetting::getValue('default_checkout_time', '12:00') }}

@if($daysUntil == 0)
**Please ensure you check out by {{ \App\Models\HotelSetting::getValue('default_checkout_time', '12:00') }} today.**
@endif

## Booking Information

**Booking Reference:** {{ $booking->booking_reference }}

**Room:** {{ $booking->room->room_number }} ({{ $booking->room->room_type }})

**Check-In Date:** {{ \Carbon\Carbon::parse($booking->check_in)->format('F d, Y') }}

**Number of Nights:** {{ \Carbon\Carbon::parse($booking->check_in)->diffInDays($booking->check_out) }} night(s)

@if($booking->id)
<x-mail::button :url="route('customer.bookings.checkout-bill', $booking)">
View Checkout Bill
</x-mail::button>
@endif

**Note:** Please settle any outstanding charges at the front desk before departure.

If you'd like to extend your stay, please contact reception as soon as possible.

Thank you for choosing PrimeLand Hotel. We hope to see you again soon!

Best regards,  
PrimeLand Hotel Team
</x-mail::message>

