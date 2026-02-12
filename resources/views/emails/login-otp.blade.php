<x-mail::message>
# Login Verification Code

Hello {{ $userName }},

You have requested to login to your PrimeLand Hotel account. Please use the verification code below to complete your login.

## Your Verification Code

<div style="text-align: center; margin: 30px 0;">
    <div style="display: inline-block; background-color: #e77a3a; color: #ffffff; padding: 20px 40px; border-radius: 8px; font-size: 32px; font-weight: bold; letter-spacing: 8px; font-family: 'Courier New', monospace;">
        {{ $otp }}
    </div>
</div>

**This code will expire in {{ $expiresInMinutes }} minutes.**

**Security Notice:** If you did not request this login verification code, please ignore this email or contact us immediately if you suspect unauthorized access to your account.

<x-mail::button :url="route('login')">
Go to Login Page
</x-mail::button>

Best regards,  
**PrimeLand Hotel Team**

---

**PrimeLand Hotel**  
Mobile/WhatsApp: 0677-155-156 / +255 677-155-157  
Email: info@primelandhotel.co.tz / infoprimelandhotel@gmail.com

© {{ date('Y') }} PrimeLand Hotel. All rights reserved.
</x-mail::message>
