<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Models\Staff;
use App\Models\Guest;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'icon',
        'color',
        'role',
        'notifiable_id',
        'notifiable_type',
        'link',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    /**
     * Get the user that owns the notification (can be Staff or Guest)
     * Note: This relationship is simplified - we'll handle Staff/Guest lookup in scopeForUser
     */
    public function user()
    {
        // Try Staff first, then Guest
        $staff = Staff::find($this->user_id);
        if ($staff) {
            return $staff;
        }
        return Guest::find($this->user_id);
    }
    
    /**
     * Get staff user (if notification belongs to staff)
     */
    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'user_id');
    }
    
    /**
     * Get guest user (if notification belongs to guest)
     */
    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class, 'user_id');
    }

    /**
     * Get the parent notifiable model (booking, service request, etc.)
     */
    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Scope to get unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope to get notifications for a specific role
     */
    public function scopeForRole($query, string $role)
    {
        return $query->where(function($q) use ($role) {
            $q->where('role', $role)
              ->orWhereNull('role');
        });
    }

    /**
     * Scope to get notifications for a specific user or their role
     * Accepts both Staff and Guest models
     */
    public function scopeForUser($query, $user)
    {
        // Determine user role and type
        if ($user instanceof \App\Models\Guest) {
            $userRole = 'customer'; 
            $isStaff = false;
        } else {
            $userRole = $user->role ?? 'guest';
            $isStaff = true;
        }
        
        return $query->where(function($q) use ($user, $userRole, $isStaff) {
            // 1. Specific User Match (with safety check for staff/guest ID overlap)
            $q->where(function($q_private) use ($user, $isStaff) {
                $q_private->where('user_id', $user->id);
                // If we're a guest, don't show notifications meant for staff roles with same ID
                if (!$isStaff) {
                    $q_private->whereIn('role', ['customer', 'guest', null]);
                } else {
                    // If we're staff, don't show notifications meant for guest/customer roles with same ID
                    $q_private->whereNotIn('role', ['customer', 'guest']);
                }
            })
            // 2. Role-based Match (where user_id is null)
            ->orWhere(function($q_role) use ($userRole) {
                $q_role->whereNull('user_id')
                       ->where(function($q_sub) use ($userRole) {
                           // Define role hierarchy/visibility
                           $rolesToShow = [$userRole];
                           
                           // Managers and Super Admins see their own and reception notifications
                           if (in_array($userRole, ['manager', 'super_admin'])) {
                               $rolesToShow[] = 'manager';
                               $rolesToShow[] = 'super_admin';
                               $rolesToShow[] = 'reception';
                               $rolesToShow[] = 'housekeeper'; // High-level staff also see housekeeping alerts
                           }
                           
                           // Normalize guest/customer roles
                           if (in_array($userRole, ['customer', 'guest'])) {
                               $rolesToShow = ['customer', 'guest'];
                           }
                           
                           $q_sub->whereIn('role', array_unique($rolesToShow));
                       });
            });
        });
    }
}
