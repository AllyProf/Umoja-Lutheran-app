<x-mail::message>
# Service Request Submitted

Hello {{ $serviceRequest->booking->guest_name }},

Your service request has been successfully submitted and is pending approval from reception.

## Service Request Details

**Service:** {{ $serviceRequest->service->name }}

**Quantity:** {{ $serviceRequest->quantity }} {{ $serviceRequest->service->unit }}

**Unit Price:** {{ number_format($serviceRequest->unit_price_tsh, 2) }} TZS

**Total Price:** {{ number_format($serviceRequest->total_price_tsh, 2) }} TZS

**Status:** Pending Approval

@if($serviceRequest->guest_request)
**Your Notes:** {{ $serviceRequest->guest_request }}
@endif

## Booking Information

**Booking Reference:** {{ $serviceRequest->booking->booking_reference }}

**Room:** {{ $serviceRequest->booking->room->room_number }} ({{ $serviceRequest->booking->room->room_type }})

**Note:** Reception will review your request and you'll receive an email notification when the status changes.

If you have any questions, please contact reception.

Best regards,  
PrimeLand Hotel Team
</x-mail::message>

