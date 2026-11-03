<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreAnnouncement extends Model
{
    protected $fillable = [
        'message',
        'target_role', // head_chef, bar_keeper, both, all
        'is_active',
        'created_by',
        'expires_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
    ];

    /**
     * Get the staff member who created the announcement.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'created_by');
    }

    /**
     * Scope to get only active announcements.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    /**
     * Scope to get announcements for a specific role.
     */
    public function scopeForRole($query, $role)
    {
        $normalized = strtolower(str_replace([' ', '-'], '_', trim((string) $role)));
        $targets = [$normalized, 'both', 'all'];

        // Counter staff also see notices aimed at legacy bar_keeper / waiter
        if (in_array($normalized, ['counter', 'bar_keeper', 'waiter'], true)) {
            $targets = array_merge($targets, ['counter', 'bar_keeper', 'waiter']);
        }

        return $query->whereIn('target_role', array_values(array_unique($targets)));
    }
}
