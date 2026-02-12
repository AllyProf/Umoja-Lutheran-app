<x-mail::message>
# Payment Confirmed

Hello {{ $booking->guest_name }},

Great news! Your payment has been successfully processed and your booking is now confirmed.

## Booking Details

**Booking Reference:** {{ $booking->booking_reference }}

**Room:** {{ $booking->room->room_number }} ({{ $booking->room->room_type }})

**Check-in:** {{ \Carbon\Carbon::parse($booking->check_in)->format('F d, Y') }}

**Check-out:** {{ \Carbon\Carbon::parse($booking->check_out)->format('F d, Y') }}

**Number of Nights:** {{ \Carbon\Carbon::parse($booking->check_in)->diffInDays($booking->check_out) }} night(s)

**Total Paid:** ${{ number_format($booking->amount_paid ?? $booking->total_price, 2) }}

**Payment Method:** {{ ucfirst($booking->payment_method ?? 'PayPal') }}

@if($booking->paid_at)
**Payment Date:** {{ \Carbon\Carbon::parse($booking->paid_at)->format('F d, Y \a\t g:i A') }}
@endif

## Check-In Options

You can check in using either method:

<x-mail::button :url="route('check-in.index') . '?ref=' . $booking->booking_reference">
Online Check-In Now
</x-mail::button>

Or log in to your account:

<x-mail::button :url="route('customer.login')">
Login to Your Account
</x-mail::button>

**Note:** Check-in time is 14:00 - 23:00. Check-out time is 12:00.

If you have any questions or special requests, please don't hesitate to contact us.

Best regards,  
PrimeLand Hotel Team
</x-mail::message>
