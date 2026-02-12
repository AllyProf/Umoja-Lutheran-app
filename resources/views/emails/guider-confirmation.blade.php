<x-mail::message>
# Corporate Booking Confirmation

Hello {{ $guiderName }},

Your corporate booking for **{{ $company->name }}** has been successfully created at PrimeLand Hotel.

## Booking Summary

**Company:** {{ $company->name }}  
**Check-in:** {{ \Carbon\Carbon::parse($checkIn)->format('F d, Y') }}  
**Check-out:** {{ \Carbon\Carbon::parse($checkOut)->format('F d, Y') }}  
**Number of Nights:** {{ \Carbon\Carbon::parse($checkIn)->diffInDays($checkOut) }} night(s)  
**Total Guests:** {{ count($bookings) }} guest(s)

## Guest Assignments

@foreach($bookings as $booking)
**{{ $booking->guest_name }}**
- Room: {{ $booking->room->room_number }} ({{ $booking->room->room_type }})
- Email: {{ $booking->guest_email }}
- Payment: {{ $booking->payment_responsibility === 'company' ? 'Company Paid' : 'Self-Paid' }}
- Booking Reference: {{ $booking->booking_reference }}
@if($booking->special_requests)
- Special Requests: {{ $booking->special_requests }}
@endif

@endforeach

@if($generalNotes)
## General Notes

{{ $generalNotes }}

@endif

## Important Information

- All guests have been sent their booking confirmations with login credentials
- Guests can log in to their accounts to request services during their stay
- Each guest has their own account under the company booking
- Company charges will be invoiced separately to {{ $company->email }}

## Contact Information

If you need to make any changes or have questions, please contact our reception team.

## Booking Policy

All bookings will be confirmed by receiving 50% of deposit before guests check in, remaining balance to be cleared on arrival or before guests check out. Full pre-payment will be required within 7 days of booking time.

## Cancellation & No Show Policy

All cancellations must be received with written confirmation.

- **30+ days before arrival:** Free cancellation (no charges)
- **14-30 days before arrival:** 50% cancellation fee of total deposit
- **7-14 days before arrival:** 100% cancellation fee of total deposit
- **1 day before check-in or No Show:** Hotel reserves the right to charge the guest the total amount of the booking as penalty

If you have any questions, please contact us at your earliest convenience.

Thank you for choosing PrimeLand Hotel!

Best regards,  
PrimeLand Hotel Team
</x-mail::message>
