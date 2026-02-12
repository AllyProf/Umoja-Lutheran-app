<x-mail::message>
# Check-In Reminder

Hello {{ $booking->guest_name }},

@if($daysUntil == 0)
**Your check-in is today!** We're excited to welcome you to PrimeLand Hotel.
@else
Your check-in is in **{{ $daysUntil }} day(s)**. We're looking forward to your arrival!
@endif

## Check-In Details

**Check-In Date:** {{ \Carbon\Carbon::parse($booking->check_in)->format('F d, Y') }}

**Check-In Time:** {{ \App\Models\HotelSetting::getValue('default_checkin_time', '14:00') }}

**Check-Out Date:** {{ \Carbon\Carbon::parse($booking->check_out)->format('F d, Y') }}

**Number of Nights:** {{ \Carbon\Carbon::parse($booking->check_in)->diffInDays($booking->check_out) }} night(s)

## Booking Information

**Booking Reference:** {{ $booking->booking_reference }}

**Room:** {{ $booking->room->room_number }} ({{ $booking->room->room_type }})

**Total Amount:** ${{ number_format($booking->total_price, 2) }}

<x-mail::button :url="route('check-in.index') . '?ref=' . $booking->booking_reference">
Online Check-In Now
</x-mail::button>

**Note:** Check-in time is 14:00 - 23:00. Check-out time is 12:00.

We can't wait to welcome you!

Best regards,  
PrimeLand Hotel Team
</x-mail::message>

