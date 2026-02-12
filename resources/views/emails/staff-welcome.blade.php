<x-mail::message>
# Welcome to PrimeLand Hotel Staff Portal

Hello {{ $user->name }},

Your staff account has been successfully created at PrimeLand Hotel. We're excited to have you as part of our team!

## Your Account Credentials

**Email:** {{ $user->email }}

**Password:** {{ $password }}

**Role:** {{ ucfirst(str_replace('_', ' ', $role)) }}

**Important:** Please change your password after your first login for security purposes.

<x-mail::button :url="route('login')">
Login to Your Account
</x-mail::button>

## What You Can Do

@if($role === 'super_admin')
- Manage all system settings and configurations
- View and manage all users and roles
- Access system logs and monitoring
- Manage hotel settings and configurations
@elseif($role === 'manager')
- View and manage all bookings
- Manage rooms and availability
- View reports and analytics
- Manage staff and guests
- Handle payments and billing
@elseif($role === 'reception')
- Check-in and check-out guests
- View and manage bookings
- Handle service requests
- Process payments
- Manage guest extensions
@endif

## Getting Started

1. Login using your credentials above
2. Change your password to something secure
3. Explore your dashboard and familiarize yourself with the system
4. If you have any questions, please contact the system administrator

## Security Reminder

For your account security, please:
- Change your password immediately after first login
- Use a strong, unique password
- Never share your credentials with anyone
- Log out when you're done using the system

If you have any questions or need assistance, please don't hesitate to contact the system administrator.

Welcome aboard!

Best regards,  
**PrimeLand Hotel Management Team**

---

**PrimeLand Hotel**  
Mobile/WhatsApp: 0677-155-156 / +255 677-155-157  
Email: info@primelandhotel.co.tz / infoprimelandhotel@gmail.com

© {{ date('Y') }} PrimeLand Hotel. All rights reserved.
</x-mail::message>



