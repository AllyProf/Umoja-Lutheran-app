<x-mail::message>
# Issue Report Update

Hello {{ $issueReport->getReporter()->name ?? 'Guest' }},

@if($status === 'pending')
Your issue report is currently **pending** review by our team.
@elseif($status === 'in_progress')
Great news! Your issue report is now **in progress** and our team is working on resolving it.
@elseif($status === 'resolved')
Excellent! Your issue report has been **resolved**.
@endif

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

@if($issueReport->booking)
## Booking Information

**Booking Reference:** {{ $issueReport->booking->booking_reference }}

@if($issueReport->room)
**Room:** {{ $issueReport->room->room_number }} ({{ $issueReport->room->room_type }})
@endif
@endif

<x-mail::button :url="route('customer.issues.show', $issueReport)">
View Issue Details
</x-mail::button>

@if($status === 'resolved')
Thank you for bringing this to our attention. We hope your stay is now more comfortable.
@else
We appreciate your patience as we work to resolve this issue.
@endif

If you have any questions or concerns, please contact reception.

Best regards,  
PrimeLand Hotel Team
</x-mail::message>

