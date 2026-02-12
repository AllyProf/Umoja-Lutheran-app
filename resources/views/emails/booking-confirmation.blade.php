<x-mail::message>
# Booking Confirmation

Hello {{ $booking->guest_name }},

Thank you for your booking at PrimeLand Hotel! We're excited to host you.

## Booking Details

**Booking Reference:** {{ $booking->booking_reference }}

**Room:** {{ $booking->room->room_number }} ({{ $booking->room->room_type }})

**Check-in:** {{ \Carbon\Carbon::parse($booking->check_in)->format('F d, Y') }}

**Check-out:** {{ \Carbon\Carbon::parse($booking->check_out)->format('F d, Y') }}

**Number of Nights:** {{ \Carbon\Carbon::parse($booking->check_in)->diffInDays($booking->check_out) }} night(s)

**Total Amount:** ${{ number_format($booking->total_price, 2) }}

@if(isset($paymentPercentage) && $paymentPercentage)
**Payment Status:** {{ $paymentPercentage }}% Paid
**Amount Paid:** ${{ number_format($booking->amount_paid ?? ($booking->total_price * $paymentPercentage / 100), 2) }}
**Remaining Amount:** ${{ number_format($remainingAmount ?? ($booking->total_price - ($booking->amount_paid ?? 0)), 2) }}
@else
**Payment Status:** {{ $booking->payment_status === 'paid' ? 'Paid' : ($booking->payment_status === 'partial' ? 'Partially Paid' : 'Pending Payment') }}
@if($booking->amount_paid)
**Amount Paid:** ${{ number_format($booking->amount_paid, 2) }}
**Remaining Amount:** ${{ number_format($booking->total_price - $booking->amount_paid, 2) }}
@endif
@endif

## Your Account Credentials

Your guest account has been created. You can log in to view your booking and request services during your stay:

**Username (Email):** {{ $booking->guest_email }}

**Password:** {{ $password }}

@if($booking->is_corporate_booking && $booking->company)
**Company Booking:** This booking is part of a corporate booking for {{ $booking->company->name }}.  
@if($booking->payment_responsibility === 'company')
**Payment:** Your room charges are being handled by {{ $booking->company->name }}. Service charges (food, drinks, etc.) will be billed according to your payment responsibility.
@else
**Payment:** Your room charges are being handled by {{ $booking->company->name }}. You are responsible for your own service charges (food, drinks, etc.).
@endif
@endif

@if(isset($generalNotes) && $generalNotes)
## General Notes

{{ $generalNotes }}

@endif

@if($booking->special_requests)
## Your Special Requests

{{ $booking->special_requests }}

@endif

<x-mail::button :url="route('customer.login')">
Login to Your Account
</x-mail::button>

## Booking Policy

All bookings will be confirmed by receiving 50% of deposit before guests check in, remaining balance to be cleared on arrival or before guests check out. Full pre-payment will be required within 7 days of booking time.

## Cancellation & No Show Policy

All cancellations must be received with written confirmation.

- **30+ days before arrival:** Free cancellation (no charges)
- **14-30 days before arrival:** 50% cancellation fee of total deposit
- **7-14 days before arrival:** 100% cancellation fee of total deposit
- **1 day before check-in or No Show:** Hotel reserves the right to charge the guest the total amount of the booking as penalty

If you have any questions, please contact us at your earliest convenience.

Best regards,  
PrimeLand Hotel Team
</x-mail::message>
