<x-mail::message>
# Service Request {{ ucfirst($status) }}

Hello {{ $serviceRequest->booking->guest_name }},

@if($status === 'approved')
Great news! Your service request has been **approved** and added to your bill.
@elseif($status === 'completed')
Your service request has been **completed**.
@elseif($status === 'cancelled')
Your service request has been **cancelled**.
@endif

## Service Request Details

**Service:** {{ $serviceRequest->service->name }}

**Quantity:** {{ $serviceRequest->quantity }} {{ $serviceRequest->service->unit }}

**Unit Price:** {{ number_format($serviceRequest->unit_price_tsh, 2) }} TZS

**Total Price:** {{ number_format($serviceRequest->total_price_tsh, 2) }} TZS

**Status:** {{ ucfirst($status) }}

@if($status === 'approved')
**Approved At:** {{ $serviceRequest->approved_at ? \Carbon\Carbon::parse($serviceRequest->approved_at)->format('F d, Y \a\t g:i A') : 'N/A' }}
@endif

## Booking Information

**Booking Reference:** {{ $serviceRequest->booking->booking_reference }}

**Room:** {{ $serviceRequest->booking->room->room_number }} ({{ $serviceRequest->booking->room->room_type }})

@if($status === 'approved')
**Note:** This charge has been added to your bill and will be included in your final checkout amount.
@endif

<x-mail::button :url="route('customer.dashboard')">
View My Bookings
</x-mail::button>

If you have any questions, please contact reception.

Best regards,  
PrimeLand Hotel Team
</x-mail::message>

