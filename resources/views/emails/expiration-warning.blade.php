<x-mail::message>
@if($warningType === 'final')
# Payment Reminder - Urgent

Hello {{ $booking->guest_name }},

**Your booking at PrimeLand Hotel will expire in 15 minutes!** Please complete your payment immediately to secure your reservation.

@elseif($warningType === 'hour')
# Payment Reminder - Urgent

Hello {{ $booking->guest_name }},

**Your booking at PrimeLand Hotel will expire in 1 hour!** Please complete your payment as soon as possible to secure your reservation.

@elseif($reminderType)
# Payment Reminder

Hello {{ $booking->guest_name }},

This is a friendly reminder that your booking payment is still pending. Please complete your payment to confirm your reservation.

@if($reminderType === '24h')
**Time Remaining:** 24 hours until expiration
@elseif($reminderType === '12h')
**Time Remaining:** 12 hours until expiration
@elseif($reminderType === '1h')
**Time Remaining:** 1 hour until expiration
@endif

@endif

## Booking Details

**Booking Reference:** {{ $booking->booking_reference }}

**Room:** {{ $booking->room->room_number ?? 'N/A' }} ({{ $booking->room->room_type ?? 'N/A' }})

**Check-in:** {{ \Carbon\Carbon::parse($booking->check_in)->format('F d, Y') }}

**Check-out:** {{ \Carbon\Carbon::parse($booking->check_out)->format('F d, Y') }}

**Number of Nights:** {{ \Carbon\Carbon::parse($booking->check_in)->diffInDays($booking->check_out) }} night(s)

**Total Amount:** ${{ number_format($booking->total_price, 2) }}

@if($booking->expires_at)
**Expires At:** {{ \Carbon\Carbon::parse($booking->expires_at)->setTimezone('Africa/Nairobi')->format('F d, Y \a\t g:i A') }} (East Africa Time)
@endif

<x-mail::button :url="route('payment.create', ['booking_id' => $booking->id])">
Pay Now via PayPal
</x-mail::button>

@if($warningType === 'final')
**Note:** Your booking will be automatically cancelled in 15 minutes if payment is not completed.
@elseif($warningType === 'hour')
**Note:** Your booking will expire in 1 hour. Please complete your payment as soon as possible.
@else
**Note:** Your booking will expire automatically if payment is not completed. Once expired, your booking will be cancelled.
@endif

If you have any questions or need assistance with payment, please contact us immediately.

Best regards,  
PrimeLand Hotel Team
</x-mail::message>


