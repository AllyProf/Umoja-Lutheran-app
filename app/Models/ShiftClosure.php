<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShiftClosure extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id',
        'opened_at',
        'closed_at',
        'total_cash_tzs',
        'total_mpesa_tzs',
        'total_other_tzs',
        'amount_submitted_tzs',
        'difference_tzs',
        'status',
        'receiver_id',
        'notes',
    ];

    protected $casts = [
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
        'total_cash_tzs' => 'decimal:2',
        'total_mpesa_tzs' => 'decimal:2',
        'total_other_tzs' => 'decimal:2',
        'amount_submitted_tzs' => 'decimal:2',
        'difference_tzs' => 'decimal:2',
    ];

    /**
     * Get the staff (counter) who closed this shift.
     */
    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    /**
     * Get the staff (receptionist) who received this shift's cash.
     */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'receiver_id');
    }

    /**
     * Get the service requests associated with this shift closure.
     */
    public function serviceRequests(): HasMany
    {
        return $this->hasMany(ServiceRequest::class, 'shift_closure_id');
    }
}
