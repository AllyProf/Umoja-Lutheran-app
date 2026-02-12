<x-mail::message>
# New Issue Report

@if($issueReport->priority === 'urgent')
## ⚠️ URGENT PRIORITY
@endif

A new issue has been reported and requires your attention.

## Issue Report Details

**Subject:** {{ $issueReport->subject }}

**Issue Type:** {{ ucfirst(str_replace('_', ' ', $issueReport->issue_type)) }}

**Priority:** {{ ucfirst($issueReport->priority) }}

**Status:** Pending

**Reported At:** {{ $issueReport->created_at->format('F d, Y \a\t g:i A') }}

**Description:**  
{{ $issueReport->description }}

## Reporter Information

**Reporter:** {{ $issueReport->getReporter()->name ?? 'Guest' }}

@if($issueReport->getReporter()->email ?? null)
**Email:** {{ $issueReport->getReporter()->email }}
@endif

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

Please review and take appropriate action on this issue.

Best regards,  
**PrimeLand Hotel System**

---

**PrimeLand Hotel**  
Mobile/WhatsApp: 0677-155-156 / +255 677-155-157  
Email: info@primelandhotel.co.tz / infoprimelandhotel@gmail.com

© {{ date('Y') }} PrimeLand Hotel. All rights reserved.
</x-mail::message>



