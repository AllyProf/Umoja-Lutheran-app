<x-mail::message>
# Service Request Status Update

A service request status has been updated.

## Service Request Details

**Service:** {{ $serviceRequest->service->name }}

**Quantity:** {{ $serviceRequest->quantity }} {{ $serviceRequest->service->unit }}

**Total Price:** {{ number_format($serviceRequest->total_price_tsh, 2) }} TZS

**Status:** {{ ucfirst($status) }}

@if($status === 'approved' && $serviceRequest->approved_at)
**Approved At:** {{ \Carbon\Carbon::parse($serviceRequest->approved_at)->format('F d, Y \a\t g:i A') }}
@endif

## Booking Information

**Guest Name:** {{ $serviceRequest->booking->guest_name }}

**Booking Reference:** {{ $serviceRequest->booking->booking_reference }}

**Room:** {{ $serviceRequest->booking->room->room_number }} ({{ $serviceRequest->booking->room->room_type }})

<x-mail::button :url="route('admin.service-requests')">
View Service Requests
</x-mail::button>

Best regards,  
**PrimeLand Hotel System**

---

**PrimeLand Hotel**  
Mobile/WhatsApp: 0677-155-156 / +255 677-155-157  
Email: info@primelandhotel.co.tz / infoprimelandhotel@gmail.com

© {{ date('Y') }} PrimeLand Hotel. All rights reserved.
</x-mail::message>



