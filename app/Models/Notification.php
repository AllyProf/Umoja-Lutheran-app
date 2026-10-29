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
        
        return $query->where(function ($q) use ($user, $userRole, $isStaff) {
            // 1. Specific user match (with safety check for staff/guest ID overlap)
            $q->where(function ($qPrivate) use ($user, $isStaff) {
                $qPrivate->where('user_id', $user->id);

                if (!$isStaff) {
                    // Guests: only customer/guest (or null role) personal notifications
                    $qPrivate->where(function ($roleQ) {
                        $roleQ->whereIn('role', ['customer', 'guest'])
                            ->orWhereNull('role');
                    });
                } else {
                    // Staff: personal notifications, including those with null role
                    // (e.g. stock request status updates). Exclude guest/customer roles.
                    $qPrivate->where(function ($roleQ) {
                        $roleQ->whereNull('role')
                            ->orWhereNotIn('role', ['customer', 'guest']);
                    });
                }
            })
            // 2. Role-based match (broadcast notifications with no specific user)
            ->orWhere(function ($qRole) use ($userRole) {
                $qRole->whereNull('user_id')
                    ->where(function ($qSub) use ($userRole) {
                        $rolesToShow = [$userRole];

                        if (in_array($userRole, ['manager', 'super_admin'], true)) {
                            $rolesToShow[] = 'manager';
                            $rolesToShow[] = 'super_admin';
                            $rolesToShow[] = 'reception';
                            $rolesToShow[] = 'housekeeper';
                        }

                        if (in_array($userRole, ['customer', 'guest'], true)) {
                            $rolesToShow = ['customer', 'guest'];
                        }

                        $qSub->whereIn('role', array_values(array_unique($rolesToShow)));
                    });
            });
        });
    }
}
