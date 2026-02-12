<x-mail::message>
# New Booking Received

A new booking has been created and requires your attention.

## Booking Details

**Booking Reference:** {{ $booking->booking_reference }}

**Guest Name:** {{ $booking->guest_name }}

**Guest Email:** {{ $booking->guest_email }}

@if($booking->guest_phone)
**Guest Phone:** {{ $booking->guest_phone }}
@endif

**Check-in Date:** {{ \Carbon\Carbon::parse($booking->check_in)->format('F d, Y') }}

**Check-out Date:** {{ \Carbon\Carbon::parse($booking->check_out)->format('F d, Y') }}

**Number of Nights:** {{ \Carbon\Carbon::parse($booking->check_in)->diffInDays(\Carbon\Carbon::parse($booking->check_out)) }}

**Number of Guests:** {{ $booking->number_of_guests }}

## Room Information

**Room:** {{ $booking->room->room_number }} ({{ $booking->room->room_type }})

**Total Price:** ${{ number_format($booking->total_price, 2) }} USD

**Payment Status:** {{ ucfirst($booking->payment_status) }}

@if($booking->amount_paid)
**Amount Paid:** {{ number_format($booking->amount_paid, 2) }} TZS
@endif

**Status:** {{ ucfirst($booking->status) }}

@if($booking->special_requests)
## Special Requests

{{ $booking->special_requests }}
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



