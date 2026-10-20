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
        'cancellation_reason',
        'cancelled_by',
        'cancelled_at',
        'approved_by',
        'shift_closure_id',
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
        'cancelled_at' => 'datetime',
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

    /**
     * Get the shift closure that this request was processed in.
     */
    public function shiftClosure(): BelongsTo
    {
        return $this->belongsTo(ShiftClosure::class, 'shift_closure_id');
    }

    /**
     * Get the staff member who cancelled this request.
     */
    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'cancelled_by');
    }

    /**
     * Whether this paid sale was a collection of an older unpaid/room-charge debt.
     */
    public function wasPaidFromPriorDebt(): bool
    {
        return (bool) data_get($this->service_specific_data, 'paid_from_prior_debt');
    }

    /**
     * Paid at reception (room bill) — visible on counter with PAID status,
     * but must never count as counter cash / shift revenue.
     */
    public function wasPaidAtReception(): bool
    {
        return (bool) data_get($this->service_specific_data, 'paid_at_reception');
    }

    /**
     * Money the counter actually collected (excludes reception-paid room charges).
     */
    public function countsAsCounterCash(): bool
    {
        return $this->payment_status === 'paid' && !$this->wasPaidAtReception();
    }

    /**
     * Build service_specific_data marking payment as prior-debt collection when applicable.
     * Counts in the receiving (current) shift, but keeps audit trail of the original order.
     */
    public function withPriorDebtPaymentMeta(?ShiftClosure $activeShift = null): array
    {
        $data = is_array($this->service_specific_data) ? $this->service_specific_data : [];
        $orderAt = $this->requested_at ?? $this->created_at;
        $shiftOpened = $activeShift?->opened_at ?? $activeShift?->created_at;

        $wasUnpaid = in_array($this->payment_status, ['pending', 'room_charge'], true);
        $fromEarlierShift = $wasUnpaid && $orderAt && $shiftOpened && $orderAt->lt($shiftOpened);
        $fromOtherShiftLink = $wasUnpaid
            && $activeShift
            && $this->shift_closure_id
            && (int) $this->shift_closure_id !== (int) $activeShift->id;

        if ($fromEarlierShift || $fromOtherShiftLink) {
            $data['paid_from_prior_debt'] = true;
            $data['original_order_at'] = optional($orderAt)->toDateTimeString();
            if ($this->shift_closure_id) {
                $data['original_shift_closure_id'] = (int) $this->shift_closure_id;
            }
        }

        return $data;
    }
}
