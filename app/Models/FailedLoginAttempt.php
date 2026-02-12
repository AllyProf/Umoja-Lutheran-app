<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FailedLoginAttempt extends Model
{
    protected $fillable = [
        'email',
        'ip_address',
        'user_agent',
        'reason',
        'blocked',
        'blocked_until',
    ];

    protected $casts = [
        'blocked' => 'boolean',
        'blocked_until' => 'datetime',
    ];

    /**
     * Record a failed login attempt
     */
    public static function record($email, $reason = 'wrong_password', $ipAddress = null, $userAgent = null)
    {
        return self::create([
            'email' => $email,
            'ip_address' => $ipAddress ?? request()->ip(),
            'user_agent' => $userAgent ?? request()->userAgent(),
            'reason' => $reason,
        ]);
    }

    /**
     * Check if IP should be blocked
     */
    public static function shouldBlockIp($ipAddress, $maxAttempts = 5, $timeWindow = 15)
    {
        $attempts = self::where('ip_address', $ipAddress)
            ->where('created_at', '>=', now()->subMinutes($timeWindow))
            ->count();
        
        return $attempts >= $maxAttempts;
    }

    /**
     * Check if email should be blocked
     */
    public static function shouldBlockEmail($email, $maxAttempts = 5, $timeWindow = 15)
    {
        $attempts = self::where('email', $email)
            ->where('created_at', '>=', now()->subMinutes($timeWindow))
            ->count();
        
        return $attempts >= $maxAttempts;
    }

    /**
     * Check if an IP address is currently blocked
     */
    public static function isIpBlocked($ipAddress)
    {
        $blocked = self::where('ip_address', $ipAddress)
            ->where('blocked', true)
            ->where(function($query) {
                $query->whereNull('blocked_until')
                      ->orWhere('blocked_until', '>', now());
            })
            ->exists();
        
        return $blocked;
    }

    /**
     * Get blocking information for an IP address
     */
    public static function getIpBlockInfo($ipAddress)
    {
        return self::where('ip_address', $ipAddress)
            ->where('blocked', true)
            ->where(function($query) {
                $query->whereNull('blocked_until')
                      ->orWhere('blocked_until', '>', now());
            })
            ->orderBy('blocked_until', 'desc')
            ->first();
    }
}
