<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class LoginOtp extends Model
{
    protected $fillable = [
        'email',
        'otp',
        'user_type',
        'user_id',
        'ip_address',
        'expires_at',
        'used',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used' => 'boolean',
    ];

    /**
     * Generate a 6-digit OTP
     */
    public static function generate(): string
    {
        return str_pad((string) rand(100000, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Create a new OTP for email
     */
    public static function createForEmail(string $email, string $userType, ?int $userId, string $ipAddress): self
    {
        // Invalidate any existing unused OTPs for this email
        self::where('email', $email)
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->update(['used' => true]);

        return self::create([
            'email' => $email,
            'otp' => self::generate(),
            'user_type' => $userType,
            'user_id' => $userId,
            'ip_address' => $ipAddress,
            'expires_at' => now()->addMinutes(10), // OTP valid for 10 minutes
            'used' => false,
        ]);
    }

    /**
     * Verify OTP
     */
    public static function verify(string $email, string $otp, string $ipAddress): ?self
    {
        // First try with IP address match (more secure)
        $loginOtp = self::where('email', $email)
            ->where('otp', $otp)
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->where('ip_address', $ipAddress)
            ->first();

        // If not found with IP match, try without IP check (for cases where IP might change)
        if (!$loginOtp) {
            $loginOtp = self::where('email', $email)
                ->where('otp', $otp)
                ->where('used', false)
                ->where('expires_at', '>', now())
                ->first();
        }

        if ($loginOtp) {
            $loginOtp->update(['used' => true]);
            return $loginOtp;
        }

        return null;
    }

    /**
     * Clean up expired OTPs
     */
    public static function cleanupExpired(): void
    {
        self::where('expires_at', '<', now())
            ->orWhere(function ($query) {
                $query->where('used', true)
                      ->where('created_at', '<', now()->subDays(1));
            })
            ->delete();
    }
}
