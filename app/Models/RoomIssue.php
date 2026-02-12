<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomIssue extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'reported_by',
        'issue_type',
        'description',
        'priority',
        'status',
        'assigned_to',
        'resolved_at',
        'resolution_notes',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    /**
     * Get the room for this issue
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get the staff member who reported this issue
     */
    public function reportedBy(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'reported_by');
    }

    /**
     * Get the staff member assigned to fix this issue
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'assigned_to');
    }
}
