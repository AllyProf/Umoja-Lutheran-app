<x-mail::message>
# Corporate Booking Invoice

Hello {{ $company->name }},

Thank you for your corporate booking at Umoja Lutheran Hostel. This email contains your booking invoice and summary.

## Company Information

**Company:** {{ $company->name }}  
**Email:** {{ $company->email }}  
@if($company->phone)
**Phone:** {{ $company->phone }}  
@endif

## Booking Summary

**Check-in:** {{ \Carbon\Carbon::parse($checkIn)->format('F d, Y') }}  
**Check-out:** {{ \Carbon\Carbon::parse($checkOut)->format('F d, Y') }}  
**Number of Nights:** {{ \Carbon\Carbon::parse($checkIn)->diffInDays($checkOut) }} night(s)  
**Total Guests:** {{ count($bookings) }} guest(s)

## Cost Breakdown

**Company Charges:** TSh {{ number_format($companyCharges, 0) }}  
**Self-Paid Charges:** TSh {{ number_format($selfPaidCharges, 0) }}  
**Total Booking Value:** TSh {{ number_format($companyCharges + $selfPaidCharges, 0) }}  
**Total Company Amount Paid:** TSh {{ number_format($totalCompanyPaid, 0) }}  
**Company Balance Due:** TSh {{ number_format($companyCharges - $totalCompanyPaid, 0) }}

## Guest Details

@foreach($bookings as $booking)
**{{ $booking->guest_name }}**
- Room: {{ $booking->room->room_number }} ({{ $booking->room->room_type }})
- Email: {{ $booking->guest_email }}
- Payment Responsibility: {{ $booking->payment_responsibility === 'company' ? 'Company Paid' : 'Self-Paid' }}
- Amount: TSh {{ number_format($booking->total_price, 0) }}
- Amount Paid: TSh {{ number_format($booking->amount_paid ?? 0, 0) }}
- Booking Reference: {{ $booking->booking_reference }}
@if($booking->special_requests)
- Special Requests: {{ $booking->special_requests }}
@endif

@endforeach

@if($generalNotes)
## General Notes

{{ $generalNotes }}

@endif

## Payment Information

All company charges will be invoiced separately. Individual guests with self-paid responsibility will handle their own payments.

## Next Steps

1. All guests have been sent their booking confirmations with login credentials
2. Guests can log in to their accounts to request services during their stay
3. Company invoice will be sent separately for payment processing
4. For any questions or modifications, please contact our reception team

## Booking Policy

All bookings will be confirmed by receiving 50% of deposit before guests check in, remaining balance to be cleared on arrival or before guests check out. Full pre-payment will be required within 7 days of booking time.

## Cancellation & No Show Policy

All cancellations must be received with written confirmation.

- **30+ days before arrival:** Free cancellation (no charges)
- **14-30 days before arrival:** 50% cancellation fee of total deposit
- **7-14 days before arrival:** 100% cancellation fee of total deposit
- **1 day before check-in or No Show:** Hotel reserves the right to charge the guest the total amount of the booking as penalty

If you have any questions, please contact us at your earliest convenience.

Thank you for choosing Umoja Lutheran Hostel!

Best regards,  
Umoja Lutheran Hostel Team
</x-mail::message>
