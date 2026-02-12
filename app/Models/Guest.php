<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Guest extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'guests';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_photo',
        'phone',
        'address',
        'city',
        'country',
        'date_of_birth',
        'gender',
        'nationality',
        'passport_number',
        'room_preferences',
        'dietary_restrictions',
        'special_occasions',
        'is_active',
        'notification_preferences',
        'session_token',
        'last_session_id',
    ];

    protected $casts = [
        'room_preferences' => 'array',
        'date_of_birth' => 'date',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'notification_preferences' => 'array',
        ];
    }

    /**
     * Check if email notifications are enabled for this user
     */
    public function emailNotificationsEnabled(): bool
    {
        $prefs = $this->notification_preferences ?? [];
        return $prefs['email_notifications_enabled'] ?? true; // Default to true
    }

    /**
     * Check if a specific notification type is enabled
     */
    public function isNotificationEnabled(string $type): bool
    {
        if (!$this->emailNotificationsEnabled()) {
            return false;
        }
        
        $prefs = $this->notification_preferences ?? [];
        $key = $type . '_notifications';
        return $prefs[$key] ?? true; // Default to true
    }

    /**
     * Get all bookings for this guest (by email)
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'guest_email', 'email');
    }

    /**
     * Get activity logs for this guest
     */
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class, 'user_id')->where('user_type', 'guest');
    }

    /**
     * Check if guest is active
     */
    public function isActive(): bool
    {
        return $this->is_active ?? true;
    }
}
