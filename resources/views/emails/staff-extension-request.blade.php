<x-mail::message>
# Extension Request {{ ucfirst($status) }}

@if($status === 'submitted')
A new extension request has been submitted and requires your review.
@elseif($status === 'approved')
An extension request has been approved.
@elseif($status === 'rejected')
An extension request has been rejected.
@endif

## Extension Request Details

**Status:** {{ ucfirst($status) }}

@if($status === 'submitted')
**Requested At:** {{ now()->format('F d, Y \a\t g:i A') }}
@elseif($status === 'approved')
**Approved At:** {{ now()->format('F d, Y \a\t g:i A') }}
@elseif($status === 'rejected')
**Rejected At:** {{ now()->format('F d, Y \a\t g:i A') }}
@endif

## Booking Information

**Booking Reference:** {{ $booking->booking_reference }}

**Guest Name:** {{ $booking->guest_name }}

**Guest Email:** {{ $booking->guest_email }}

**Room:** {{ $booking->room->room_number }} ({{ $booking->room->room_type }})

**Original Check-out Date:** {{ \Carbon\Carbon::parse($booking->check_out)->format('F d, Y') }}

@if($booking->extension_requested_checkout)
**Requested New Check-out Date:** {{ \Carbon\Carbon::parse($booking->extension_requested_checkout)->format('F d, Y') }}

**Additional Nights:** {{ \Carbon\Carbon::parse($booking->check_out)->diffInDays(\Carbon\Carbon::parse($booking->extension_requested_checkout)) }}
@endif

@if($booking->extension_additional_cost)
**Additional Cost:** {{ number_format($booking->extension_additional_cost, 2) }} TZS
@endif

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



