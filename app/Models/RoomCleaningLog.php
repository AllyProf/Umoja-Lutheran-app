<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomCleaningLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'cleaned_by',
        'status',
        'cleaned_at',
        'inspected_at',
        'notes',
    ];

    protected $casts = [
        'cleaned_at' => 'datetime',
        'inspected_at' => 'datetime',
    ];

    /**
     * Get the room for this cleaning log
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get the staff member who cleaned the room
     */
    public function cleanedBy(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'cleaned_by');
    }
}
