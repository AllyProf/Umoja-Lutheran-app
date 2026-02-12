<x-mail::message>
# We'd Love Your Feedback

Hello {{ $booking->guest_name }},

Thank you for staying with us at PrimeLand Hotel! We hope you had a wonderful experience.

Your feedback is invaluable to us and helps us improve our services for future guests.

## Share Your Experience

Please take a moment to share your thoughts about your recent stay:

<x-mail::button :url="route('customer.dashboard')">
Leave Feedback
</x-mail::button>

## Your Stay Details

**Booking Reference:** {{ $booking->booking_reference }}

**Room:** {{ $booking->room->room_number ?? 'N/A' }} ({{ $booking->room->room_type ?? 'N/A' }})

**Check-in:** {{ \Carbon\Carbon::parse($booking->check_in)->format('F d, Y') }}

**Check-out:** {{ \Carbon\Carbon::parse($booking->check_out)->format('F d, Y') }}

## Special Offers

As a valued guest, we'd like to offer you special rates for your next visit. Book your next stay and enjoy exclusive discounts!

<x-mail::button :url="route('customer.dashboard')">
Book Your Next Stay
</x-mail::button>

We look forward to welcoming you back soon!

Best regards,  
PrimeLand Hotel Team
</x-mail::message>








