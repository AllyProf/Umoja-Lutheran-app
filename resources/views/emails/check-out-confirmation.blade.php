<x-mail::message>
# Check-Out Confirmation

Hello {{ $booking->guest_name }},

Thank you for staying with us at Umoja Lutheran Hostel! We hope you enjoyed your stay.

## Check-Out Summary

**Check-out Date:** {{ \Carbon\Carbon::parse($booking->check_out)->format('F d, Y') }}

**Check-out Time:** {{ $booking->checked_out_at ? \Carbon\Carbon::parse($booking->checked_out_at)->format('g:i A') : 'N/A' }}

**Room:** {{ $booking->room->room_number ?? 'N/A' }} ({{ $booking->room->room_type ?? 'N/A' }})

## Payment Summary

**Total Bill:** TSh {{ number_format($booking->total_price, 0) }}

**Amount Paid:** TSh {{ number_format($booking->amount_paid ?? $booking->total_price, 0) }}

**Payment Status:** {{ ucfirst($booking->payment_status ?? 'paid') }}

@if($booking->paid_at)
**Payment Date:** {{ \Carbon\Carbon::parse($booking->paid_at)->format('F d, Y \a\t g:i A') }}
@endif

## Download Your Receipt

<x-mail::button :url="route('payment.receipt.download', $booking) . '?download=1'">
Download Receipt
</x-mail::button>

## Share Your Feedback

We would love to hear about your experience! Your feedback helps us improve our services.

<x-mail::button :url="route('customer.dashboard')">
Leave Feedback
</x-mail::button>

## Thank You

Thank you for choosing Umoja Lutheran Hostel. We hope to welcome you back soon!

If you have any questions about your stay or billing, please contact us.

Best regards,  
Umoja Lutheran Hostel Team
</x-mail::message>

