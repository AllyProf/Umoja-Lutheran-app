<x-mail::message>
# Guest Checked Out

A guest has successfully checked out.

## Check-out Details

**Check-out Time:** {{ $booking->checked_out_at ? $booking->checked_out_at->format('F d, Y \a\t g:i A') : now()->format('F d, Y \a\t g:i A') }}

## Guest Information

**Guest Name:** {{ $booking->guest_name }}

**Guest Email:** {{ $booking->guest_email }}

## Booking Information

**Booking Reference:** {{ $booking->booking_reference }}

**Room:** {{ $booking->room->room_number }} ({{ $booking->room->room_type }})

**Check-in Date:** {{ \Carbon\Carbon::parse($booking->check_in)->format('F d, Y') }}

**Check-out Date:** {{ \Carbon\Carbon::parse($booking->check_out)->format('F d, Y') }}

**Total Amount Paid:** {{ number_format($booking->amount_paid ?? 0, 2) }} TZS

**Payment Status:** {{ ucfirst($booking->payment_status) }}

**Note:** Room status has been changed to "Needs Cleaning".

<x-mail::button :url="route('admin.rooms.cleaning')">
View Rooms Cleaning
</x-mail::button>

Best regards,  
**PrimeLand Hotel System**

---

**PrimeLand Hotel**  
Mobile/WhatsApp: 0677-155-156 / +255 677-155-157  
Email: info@primelandhotel.co.tz / infoprimelandhotel@gmail.com

© {{ date('Y') }} PrimeLand Hotel. All rights reserved.
</x-mail::message>



