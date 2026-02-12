<x-mail::message>
# Check-In Notification

@if(\Carbon\Carbon::parse($booking->check_in)->isToday())
**⚠️ URGENT: Check-In Today!** Guest is expected to check in today.
@else
**📅 Upcoming Check-In**

@if($timeRemaining)
{{ $timeRemaining }}
@else
Check-in scheduled for {{ \Carbon\Carbon::parse($booking->check_in)->format('M d, Y') }}
@endif
@endif

## Guest Information

**Booking Reference:** {{ $booking->booking_reference }}

**Guest Name:** {{ $booking->guest_name }}

**Email:** {{ $booking->guest_email }}

**Phone:** {{ $booking->guest_phone ?? 'N/A' }}

**Number of Guests:** {{ $booking->number_of_guests ?? 1 }}

## Booking Details

**Room:** {{ $booking->room->room_type ?? 'N/A' }} ({{ $booking->room->room_number ?? 'N/A' }})

**Check-in Date:** {{ \Carbon\Carbon::parse($booking->check_in)->format('M d, Y') }}

**Check-out Date:** {{ \Carbon\Carbon::parse($booking->check_out)->format('M d, Y') }}

**Nights:** {{ \Carbon\Carbon::parse($booking->check_in)->diffInDays(\Carbon\Carbon::parse($booking->check_out)) }} night(s)

**Total Price:** ${{ number_format($booking->total_price, 2) }}

**Payment Status:** 
@if($booking->payment_status === 'paid')
Paid
@elseif($booking->payment_status === 'partial')
Partial (${{ number_format($booking->amount_paid ?? 0, 2) }})
@else
Pending
@endif

@if($booking->special_requests)
## Special Requests

{{ $booking->special_requests }}
@endif

<x-mail::button :url="route('reception.reservations.check-in') . '?ref=' . $booking->booking_reference">
View Check-In Page
</x-mail::button>

**Note:** Please ensure the room is ready and prepare for guest arrival.

Best regards,  
PrimeLand Hotel Team
</x-mail::message>


