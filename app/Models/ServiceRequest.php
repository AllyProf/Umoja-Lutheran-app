<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'day_service_id',
        'service_id',
        'guest_request',
        'service_specific_data',
        'quantity',
        'unit_price_tsh',
        'total_price_tsh',
        'status',
        'reception_notes',
        'approved_by',
        'is_walk_in',
        'walk_in_name',
        'payment_status',
        'payment_method',
        'payment_reference',
        'requested_at',
        'approved_at',
        'preparation_started_at',
        'completed_at',
    ];

    protected $casts = [
        'unit_price_tsh' => 'decimal:2',
        'total_price_tsh' => 'decimal:2',
        'quantity' => 'integer',
        'service_specific_data' => 'array',
        'requested_at' => 'datetime',
        'approved_at' => 'datetime',
        'preparation_started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the booking that this service request belongs to
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the day service (ceremony) that this request belongs to
     */
    public function dayService(): BelongsTo
    {
        return $this->belongsTo(DayService::class);
    }

    /**
     * Get the service for this request
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the user who approved this request
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'approved_by');
    }
}
