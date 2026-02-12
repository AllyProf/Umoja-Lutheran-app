<x-mail::message>
# Ceremony Service Confirmation

Hello {{ $dayService->guest_name }},

Thank you for choosing PrimeLand Hotel for your ceremony/birthday celebration! We're excited to host your special event.

## Service Details

**Service Reference:** {{ $dayService->service_reference }}

**Service Type:** Ceremony/Birthday Package

**Service Date:** {{ \Carbon\Carbon::parse($dayService->service_date)->format('F d, Y') }}

**Service Time:** {{ \Carbon\Carbon::parse($dayService->service_time)->format('h:i A') }}

**Number of People:** {{ $dayService->number_of_people }}

## Package Items

@php
    $packageItems = $dayService->package_items ?? [];
    $packageItemLabels = [
        'food' => 'Food',
        'swimming' => 'Swimming',
        'drinks' => 'Drinks',
        'photos' => 'Photos',
        'decoration' => 'Decoration'
    ];
@endphp

@if(!empty($packageItems))
@foreach($packageItems as $key => $price)
@if(is_numeric($price) && $price > 0)
- **{{ $packageItemLabels[$key] ?? ucfirst(str_replace('_', ' ', $key)) }}:** {{ number_format($price, 2) }} TZS
@endif
@endforeach
@endif

## Payment Information

@if($dayService->discount_amount > 0)
**Subtotal:** {{ number_format($dayService->amount + $dayService->discount_amount, 2) }} TZS

**Discount:** -{{ number_format($dayService->discount_amount, 2) }} TZS @if($dayService->discount_type === 'percentage')({{ $dayService->discount_value }}% off)@endif
@endif

**Total Amount:** {{ number_format($dayService->amount, 2) }} TZS

**Payment Status:** {{ ucfirst($dayService->payment_status) }}

**Payment Method:** {{ $dayService->payment_method_name ?? 'N/A' }}

@if($dayService->amount_paid)
**Amount Paid:** {{ number_format($dayService->amount_paid, 2) }} TZS

@php
    $remainingAmount = $dayService->amount - $dayService->amount_paid;
@endphp
@if($remainingAmount > 0)
**Remaining Amount:** {{ number_format($remainingAmount, 2) }} TZS
@elseif($remainingAmount < 0)
**Overpaid:** {{ number_format(abs($remainingAmount), 2) }} TZS
@endif
@endif

@if($dayService->paid_at)
**Payment Date:** {{ \Carbon\Carbon::parse($dayService->paid_at)->format('F d, Y h:i A') }}
@endif

@if($dayService->notes)
## Additional Notes

{{ $dayService->notes }}
@endif

## Contact Information

If you have any questions or need to make changes to your ceremony service, please contact us:

**Phone:** 0677-155-156 / +255 677-155-157

**Email:** info@primelandhotel.co.tz | infoprimelandhotel@gmail.com

**Location:** Sokoine Road - Moshi Kilimanjaro - Tanzania

We look forward to making your celebration memorable!

Best regards,  
PrimeLand Hotel Team
</x-mail::message>


