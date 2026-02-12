<x-mail::message>
# Extension Request Submitted

Hello {{ $booking->guest_name }},

Your checkout extension request has been submitted and is pending approval from reception.

## Extension Request Details

**Current Check-out Date:** {{ \Carbon\Carbon::parse($booking->check_out)->format('F d, Y') }}

**Requested Check-out Date:** {{ \Carbon\Carbon::parse($booking->extension_requested_to)->format('F d, Y') }}

**Additional Nights:** {{ \Carbon\Carbon::parse($booking->check_out)->diffInDays($booking->extension_requested_to) }} night(s)

@php
    $additionalNights = \Carbon\Carbon::parse($booking->check_out)->diffInDays($booking->extension_requested_to);
    $additionalCost = $booking->room->price_per_night * $additionalNights;
@endphp

**Additional Cost:** ${{ number_format($additionalCost, 2) }} ({{ number_format($additionalCost * 2500, 2) }} TZS)

**Status:** Pending Approval

@if($booking->extension_reason)
**Reason:** {{ $booking->extension_reason }}
@endif

## Booking Information

**Booking Reference:** {{ $booking->booking_reference }}

**Room:** {{ $booking->room->room_number }} ({{ $booking->room->room_type }})

**Note:** Reception will review your extension request and you'll receive an email notification when the status changes.

If you have any questions, please contact reception.

Best regards,  
PrimeLand Hotel Team
</x-mail::message>

