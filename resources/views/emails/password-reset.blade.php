<x-mail::message>
# Password Reset Request

Hello {{ $user->name }},

Your password has been reset successfully.

## Your New Password

**{{ $newPassword }}**

<x-mail::button :url="route('login')">
Login Now
</x-mail::button>

**Note:** Please change your password after logging in.

If you did not request this, please contact us immediately.

Best regards,  
PrimeLand Hotel Team
</x-mail::message>
