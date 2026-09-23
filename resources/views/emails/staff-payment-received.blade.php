<x-mail::message>
# Payment Received

A payment has been received for a booking.

## Payment Details

**Amount Paid:** TSh {{ number_format($amountPaid, 0) }}

**Payment Method:** {{ ucfirst(str_replace('_', ' ', $paymentMethod ?? 'paypal')) }}

**Payment Date:** {{ now()->format('F d, Y \a\t g:i A') }}

## Booking Information

**Booking Reference:** {{ $booking->booking_reference }}

**Guest Name:** {{ $booking->guest_name }}

**Guest Email:** {{ $booking->guest_email }}

**Room:** {{ $booking->room->room_number }} ({{ $booking->room->room_type }})

**Total Booking Price:** TSh {{ number_format($booking->total_price, 0) }}

**Payment Status:** {{ ucfirst($booking->payment_status) }}

**Total Amount Paid:** TSh {{ number_format($booking->amount_paid ?? 0, 0) }}

@if($booking->payment_status === 'partial')
**Outstanding Balance:** TSh {{ number_format(($booking->total_price) - ($booking->amount_paid ?? 0), 0) }}
@endif

<x-mail::button :url="route('admin.bookings.show', $booking)">
View Booking Details
</x-mail::button>

Best regards,  
**Umoja Lutheran Hostel System**

---

**Umoja Lutheran Hostel**  
Mobile/WhatsApp: 0677-155-156 / +255 677-155-157  
Email: info@umojahostel.com / infoprimelandhotel@gmail.com

© {{ date('Y') }} Umoja Lutheran Hostel. All rights reserved.
</x-mail::message>



