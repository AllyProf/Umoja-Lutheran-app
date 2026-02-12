<x-mail::message>
# Guest Checked In

A guest has successfully checked in.

## Check-in Details

**Check-in Time:** {{ now()->format('F d, Y \a\t g:i A') }}

## Guest Information

**Guest Name:** {{ $booking->guest_name }}

**Guest Email:** {{ $booking->guest_email }}

@if($booking->guest_phone)
**Guest Phone:** {{ $booking->guest_phone }}
@endif

## Booking Information

**Booking Reference:** {{ $booking->booking_reference }}

**Room:** {{ $booking->room->room_number }} ({{ $booking->room->room_type }})

**Check-in Date:** {{ \Carbon\Carbon::parse($booking->check_in)->format('F d, Y') }}

**Check-out Date:** {{ \Carbon\Carbon::parse($booking->check_out)->format('F d, Y') }}

**Number of Nights:** {{ \Carbon\Carbon::parse($booking->check_in)->diffInDays(\Carbon\Carbon::parse($booking->check_out)) }}

**Number of Guests:** {{ $booking->number_of_guests }}

**Payment Status:** {{ ucfirst($booking->payment_status) }}

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



