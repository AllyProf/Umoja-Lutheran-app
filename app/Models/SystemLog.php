<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemLog extends Model
{
    protected $fillable = [
        'level',
        'channel',
        'message',
        'context',
        'user_id',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'context' => 'array',
    ];

    /**
     * Log a system event
     */
    public static function log($level, $message, $channel = null, $context = [])
    {
        // Try to get user from any guard (staff, guest, or web)
        $user = auth()->guard('staff')->user() 
            ?? auth()->guard('guest')->user() 
            ?? auth()->user();
        
        return self::create([
            'level' => $level,
            'channel' => $channel ?? 'system',
            'message' => $message,
            'context' => $context,
            'user_id' => $user?->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Scope for filtering by level
     */
    public function scopeLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    /**
     * Scope for filtering by channel
     */
    public function scopeChannel($query, $channel)
    {
        return $query->where('channel', $channel);
    }
}
