<x-mail::message>
# Swimming Service Confirmation

Hello {{ $dayService->guest_name }},

Thank you for choosing PrimeLand Hotel for your swimming pool access! We are pleased to confirm your booking.

## Service Details

**Service Reference:** {{ $dayService->service_reference }}

**Service Type:** {{ $dayService->service_type_name }}

**Date:** {{ $dayService->service_date->format('F d, Y') }}

**Time:** {{ \Carbon\Carbon::parse($dayService->service_time)->format('h:i A') }}

**Number of People:** {{ $dayService->number_of_people }}
@if($dayService->adult_quantity || $dayService->child_quantity)
@if($dayService->adult_quantity)
- Adults: {{ $dayService->adult_quantity }}
@endif
@if($dayService->child_quantity)
- Children: {{ $dayService->child_quantity }}
@endif
@endif

## Payment Information

@if($dayService->discount_amount > 0)
**Subtotal:** {{ number_format($dayService->amount + $dayService->discount_amount, 2) }} TZS

**Discount:** -{{ number_format($dayService->discount_amount, 2) }} TZS @if($dayService->discount_type === 'percentage')({{ $dayService->discount_value }}% off)@endif
@endif

**Total Amount:** {{ number_format($dayService->amount, 2) }} TZS

**Payment Status:** {{ ucfirst($dayService->payment_status) }}

**Payment Method:** {{ $dayService->payment_method_name }}
@if($dayService->payment_provider)
**Provider:** {{ $dayService->payment_provider }}
@endif
@if($dayService->payment_reference)
**Reference:** {{ $dayService->payment_reference }}
@endif

**Amount Paid:** {{ number_format($dayService->amount_paid, 2) }} TZS

@php
    $remainingAmount = $dayService->amount - $dayService->amount_paid;
@endphp
@if($remainingAmount > 0)
**Remaining Amount:** {{ number_format($remainingAmount, 2) }} TZS
@elseif($remainingAmount < 0)
**Overpaid Amount:** {{ number_format(abs($remainingAmount), 2) }} TZS
@endif

@if($dayService->paid_at)
**Payment Date:** {{ \Carbon\Carbon::parse($dayService->paid_at)->format('F d, Y h:i A') }}
@endif

@if($dayService->notes)
## Additional Notes

{{ $dayService->notes }}
@endif

If you have any questions or need to make changes, please contact us.

Best regards,  
PrimeLand Hotel Team
</x-mail::message>


