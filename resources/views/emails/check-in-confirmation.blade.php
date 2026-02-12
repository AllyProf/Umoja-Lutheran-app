<x-mail::message>
# Check-In Confirmation

Hello {{ $booking->guest_name }},

Welcome! You have successfully checked in to PrimeLand Hotel.

## Your Room Details

**Room Number:** {{ $booking->room->room_number ?? 'N/A' }}

**Room Type:** {{ $booking->room->room_type ?? 'N/A' }}

**Check-in Date:** {{ \Carbon\Carbon::parse($booking->check_in)->format('F d, Y') }}

**Check-in Time:** {{ $booking->checked_in_at ? \Carbon\Carbon::parse($booking->checked_in_at)->setTimezone('Africa/Dar_es_Salaam')->format('g:i A') : (\App\Models\HotelSetting::getValue('default_checkin_time', '14:00')) }}

**Check-out Date:** {{ \Carbon\Carbon::parse($booking->check_out)->format('F d, Y') }}

## WiFi Information

**Network Name:** {{ $wifiNetworkName ?? 'PrimeLand Hotel WiFi' }}

**Password:** {{ $wifiPassword ?? 'Please ask at reception' }}

## Hotel Amenities & Services

- 24-hour front desk assistance
- Room service available
- Laundry services
- Airport pickup (if requested)
- Restaurant and bar
- Free WiFi throughout the hotel

## Important Information

- **Check-out Time:** 12:00
- **Emergency Contact:** Available at front desk
- **Room Service:** Available 24/7

If you need anything during your stay, please contact reception or use your dashboard to request services.

<x-mail::button :url="route('customer.dashboard')">
Access Your Dashboard
</x-mail::button>

We hope you enjoy your stay with us!

Best regards,  
PrimeLand Hotel Team
</x-mail::message>

