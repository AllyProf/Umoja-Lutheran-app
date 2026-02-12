<x-mail::message>
# Issue Report Status Update

An issue report status has been updated.

## Issue Report Details

**Subject:** {{ $issueReport->subject }}

**Issue Type:** {{ ucfirst(str_replace('_', ' ', $issueReport->issue_type)) }}

**Priority:** {{ ucfirst($issueReport->priority) }}

**Status:** {{ ucfirst(str_replace('_', ' ', $status)) }}

@if($status === 'resolved' && $issueReport->resolved_at)
**Resolved At:** {{ $issueReport->resolved_at->format('F d, Y \a\t g:i A') }}
@endif

@if($adminNotes)
## Staff Notes

{{ $adminNotes }}
@endif

## Reporter Information

**Reporter:** {{ $issueReport->getReporter()->name ?? 'Guest' }}

@if($issueReport->booking)
## Booking Information

**Booking Reference:** {{ $issueReport->booking->booking_reference }}

@if($issueReport->room)
**Room:** {{ $issueReport->room->room_number }} ({{ $issueReport->room->room_type }})
@endif
@endif

<x-mail::button :url="route('admin.issues.show', $issueReport)">
View Issue Details
</x-mail::button>

Best regards,  
**PrimeLand Hotel System**

---

**PrimeLand Hotel**  
Mobile/WhatsApp: 0677-155-156 / +255 677-155-157  
Email: info@primelandhotel.co.tz / infoprimelandhotel@gmail.com

© {{ date('Y') }} PrimeLand Hotel. All rights reserved.
</x-mail::message>



