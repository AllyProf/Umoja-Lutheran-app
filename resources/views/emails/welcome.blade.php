<x-mail::message>
# Welcome to PrimeLand Hotel

Hello {{ $user->name }},

Welcome to PrimeLand Hotel! We're thrilled to have you as our guest.

Your guest account has been successfully created. You can now access your dashboard to manage your bookings and stay connected with us.

## Your Account Information

**Email:** {{ $user->email }}

**Password:** {{ $password }}

<x-mail::button :url="route('customer.login')">
Login to Your Account
</x-mail::button>

## What You Can Do

- View and manage your bookings
- Request services and extensions
- View your payment history
- Report any issues during your stay
- Access your booking documents

## Hotel Information

- **Check-in Time:** 14:00 - 23:00
- **Check-out Time:** 12:00
- **24-hour Front Desk** available for assistance

If you have any questions or need assistance, please don't hesitate to contact us.

We look forward to welcoming you!

Best regards,  
PrimeLand Hotel Team
</x-mail::message>

