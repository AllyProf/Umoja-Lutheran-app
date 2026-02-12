<x-mail::message>
# New Service Request

A new service request has been submitted and requires your attention.

## Service Request Details

**Service:** {{ $serviceRequest->service->name }}

**Quantity:** {{ $serviceRequest->quantity }} {{ $serviceRequest->service->unit }}

**Unit Price:** {{ number_format($serviceRequest->unit_price_tsh, 2) }} TZS

**Total Price:** {{ number_format($serviceRequest->total_price_tsh, 2) }} TZS

**Status:** Pending Approval

**Requested At:** {{ $serviceRequest->requested_at ? \Carbon\Carbon::parse($serviceRequest->requested_at)->format('F d, Y \a\t g:i A') : 'N/A' }}

@if($serviceRequest->guest_request)
**Guest Request:** {{ $serviceRequest->guest_request }}
@endif

## Booking Information

**Guest Name:** {{ $serviceRequest->booking->guest_name }}

**Guest Email:** {{ $serviceRequest->booking->guest_email }}

**Booking Reference:** {{ $serviceRequest->booking->booking_reference }}

**Room:** {{ $serviceRequest->booking->room->room_number }} ({{ $serviceRequest->booking->room->room_type }})

**Check-in:** {{ \Carbon\Carbon::parse($serviceRequest->booking->check_in)->format('F d, Y') }}

**Check-out:** {{ \Carbon\Carbon::parse($serviceRequest->booking->check_out)->format('F d, Y') }}

<x-mail::button :url="route('admin.service-requests')">
View Service Requests
</x-mail::button>

Please review and approve or reject this service request.

Best regards,  
**PrimeLand Hotel System**

---

**PrimeLand Hotel**  
Mobile/WhatsApp: 0677-155-156 / +255 677-155-157  
Email: info@primelandhotel.co.tz / infoprimelandhotel@gmail.com

© {{ date('Y') }} PrimeLand Hotel. All rights reserved.
</x-mail::message>



