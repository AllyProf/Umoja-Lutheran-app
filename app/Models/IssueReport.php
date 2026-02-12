<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IssueReport extends Model
{
    protected $fillable = [
        'user_id',
        'booking_id',
        'room_id',
        'issue_type',
        'priority',
        'subject',
        'description',
        'status',
        'admin_notes',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    /**
     * Get the user who reported the issue (can be User or Guest)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the guest who reported the issue (if reported by a guest)
     */
    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class, 'user_id');
    }

    /**
     * Get the reporter (either Guest or User)
     * This method checks both tables to find the reporter
     */
    public function getReporter()
    {
        // First try to get as User
        $user = $this->user;
        if ($user) {
            return $user;
        }
        
        // If not found, try to get as Guest
        return $this->guest;
    }

    /**
     * Get the booking associated with the issue
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the room associated with the issue
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
}
