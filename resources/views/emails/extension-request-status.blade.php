<x-mail::message>
# Extension Request {{ ucfirst($status) }}

Hello {{ $booking->guest_name }},

@if($status === 'approved')
Great news! Your extension request has been **approved**.
@else
Unfortunately, your extension request has been **rejected**.
@endif

## Extension Request Details

**Current Check-out Date:** {{ \Carbon\Carbon::parse($booking->check_out)->format('F d, Y') }}

@if($status === 'approved')
**New Check-out Date:** {{ \Carbon\Carbon::parse($booking->extension_requested_to)->format('F d, Y') }}

**Additional Nights:** {{ \Carbon\Carbon::parse($booking->check_out)->diffInDays($booking->extension_requested_to) }} night(s)

@php
    $additionalNights = \Carbon\Carbon::parse($booking->check_out)->diffInDays($booking->extension_requested_to);
    $additionalCost = $booking->room->price_per_night * $additionalNights;
@endphp

**Additional Cost:** ${{ number_format($additionalCost, 2) }} ({{ number_format($additionalCost * 2500, 2) }} TZS)

**Approved At:** {{ $booking->extension_approved_at ? \Carbon\Carbon::parse($booking->extension_approved_at)->format('F d, Y \a\t g:i A') : 'N/A' }}

@if($booking->extension_admin_notes)
**Admin Notes:** {{ $booking->extension_admin_notes }}
@endif

**Note:** The extension charge will be added to your final bill at checkout.

<x-mail::button :url="route('customer.dashboard')">
View My Booking
</x-mail::button>
@else
**Requested Check-out Date:** {{ \Carbon\Carbon::parse($booking->extension_requested_to)->format('F d, Y') }}

@if($booking->extension_admin_notes)
**Reason for Rejection:** {{ $booking->extension_admin_notes }}
@endif

**Note:** Your original check-out date remains: **{{ \Carbon\Carbon::parse($booking->check_out)->format('F d, Y') }}**

If you have any questions, please contact reception.
@endif

## Booking Information

**Booking Reference:** {{ $booking->booking_reference }}

**Room:** {{ $booking->room->room_number }} ({{ $booking->room->room_type }})

Best regards,  
PrimeLand Hotel Team
</x-mail::message>

