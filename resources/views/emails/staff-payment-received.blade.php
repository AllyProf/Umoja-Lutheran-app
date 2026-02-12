<x-mail::message>
# Payment Received

A payment has been received for a booking.

## Payment Details

**Amount Paid:** {{ number_format($amountPaid, 2) }} TZS

**Payment Method:** {{ ucfirst(str_replace('_', ' ', $paymentMethod ?? 'paypal')) }}

**Payment Date:** {{ now()->format('F d, Y \a\t g:i A') }}

## Booking Information

**Booking Reference:** {{ $booking->booking_reference }}

**Guest Name:** {{ $booking->guest_name }}

**Guest Email:** {{ $booking->guest_email }}

**Room:** {{ $booking->room->room_number }} ({{ $booking->room->room_type }})

**Total Booking Price:** ${{ number_format($booking->total_price, 2) }} USD

**Payment Status:** {{ ucfirst($booking->payment_status) }}

**Total Amount Paid:** {{ number_format($booking->amount_paid ?? 0, 2) }} TZS

@if($booking->payment_status === 'partial')
**Outstanding Balance:** {{ number_format(($booking->total_price * ($booking->locked_exchange_rate ?? 2300)) - ($booking->amount_paid ?? 0), 2) }} TZS
@endif

<x-mail::button :url="route('admin.bookings.show', $booking)">
View Booking Details
</x-mail::button>

Best regards,  
**PrimeLand Hotel System**

---

**PrimeLand Hotel**  
Mobile/WhatsApp: 0677-155-156 / +255 677-155-157  
Email: info@primelandhotel.co.tz / infoprimelandhotel@gmail.com

© {{ date('Y') }} PrimeLand Hotel. All rights reserved.
</x-mail::message>



