<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    protected $fillable = [
        'room_id',
        'guest_name',
        'first_name',
        'last_name',
        'guest_email',
        'country',
        'guest_type',
        'guest_phone',
        'country_code',
        'check_in',
        'check_out',
        'number_of_guests',
        'special_requests',
        'arrival_time',
        'booking_for',
        'guest_first_name',
        'guest_last_name',
        'main_guest_name',
        'total_price',
        'recommended_price',
        'status',
        'booking_reference',
        'guest_id',
        'company_id',
        'payment_responsibility',
        'is_corporate_booking',
        'payment_status',
        'payment_method',
        'payment_provider',
        'payment_transaction_id',
        'amount_paid',
        'payment_percentage',
        'paid_at',
        'cancellation_reason',
        'expires_at',
        'cancelled_at',
        'admin_notes',
        'check_in_status',
        'checked_in_at',
        'checked_out_at',
        'airport_pickup_required',
        'flight_number',
        'airline',
        'arrival_time_pickup',
        'pickup_passengers',
        'luggage_info',
        'pickup_contact_number',
        'total_service_charges_tsh',
        'total_bill_tsh',
        'extension_requested_to',
        'shift_closure_id',
        'extension_status',
        'extension_requested_at',
        'extension_approved_at',
        'extension_reason',
        'extension_admin_notes',
        'extension_type',
        'original_check_out',
        'locked_exchange_rate',
        'cancellation_fee',
        'cancellation_fee_percentage',
        'payment_deadline',
    ];

    protected $casts = [
        'check_in' => 'date',
        'check_out' => 'date',
        'extension_requested_to' => 'date',
        'extension_requested_at' => 'datetime',
        'extension_approved_at' => 'datetime',
        'original_check_out' => 'date',
        'total_price' => 'decimal:2',
        'recommended_price' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'paid_at' => 'datetime',
        'expires_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'checked_in_at' => 'datetime',
        'checked_out_at' => 'datetime',
        'arrival_time_pickup' => 'datetime',
        'airport_pickup_required' => 'boolean',
        'total_service_charges_tsh' => 'decimal:2',
        'total_bill_tsh' => 'decimal:2',
        'cancellation_fee' => 'decimal:2',
        'cancellation_fee_percentage' => 'decimal:2',
        'payment_deadline' => 'datetime',
    ];

    /**
     * System is TSh-only — never multiply amounts by a stored FX rate.
     */
    public function getLockedExchangeRateAttribute($value): float
    {
        return 1.0;
    }

    /**
     * Get the company that this booking belongs to.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the room that this booking belongs to.
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get all service requests for this booking
     */
    public function serviceRequests(): HasMany
    {
        return $this->hasMany(ServiceRequest::class);
    }

    /**
     * Approved/completed service lines still unpaid (matches counter: pending + room_charge).
     */
    public function unpaidServiceChargesTsh($serviceRequests = null): float
    {
        $serviceRequests = $serviceRequests ?? $this->serviceRequests()
            ->whereIn('status', ['approved', 'completed'])
            ->get();

        return (float) $serviceRequests
            ->filter(fn ($sr) => in_array($sr->payment_status ?? 'pending', ['pending', 'room_charge'], true))
            ->sum('total_price_tsh');
    }

    /**
     * Paid service line total (payment_status = paid only — not room_charge).
     */
    public function paidServiceChargesTsh($serviceRequests = null): float
    {
        $serviceRequests = $serviceRequests ?? $this->serviceRequests()
            ->whereIn('status', ['approved', 'completed'])
            ->get();

        return (float) $serviceRequests
            ->where('payment_status', 'paid')
            ->sum('total_price_tsh');
    }

    /**
     * Guest outstanding in TSh aligned with counter food bills.
     * - Unpaid services = pending + room_charge (line-based)
     * - Room/extension debt uses booking.amount_paid after attributing paid service lines
     *   (avoids double-counting bar cash already stored on amount_paid)
     *
     * @param  bool  $includeRoom  false for corporate self-paid (guest owes services only)
     */
    public function computeOutstandingBalanceTsh($serviceRequests = null, bool $includeRoom = true): float
    {
        $serviceRequests = $serviceRequests ?? $this->serviceRequests()
            ->whereIn('status', ['approved', 'completed'])
            ->get();

        $unpaidServicesTsh = $this->unpaidServiceChargesTsh($serviceRequests);
        $paidServicesTsh = $this->paidServiceChargesTsh($serviceRequests);
        $amountPaidPool = (float) ($this->amount_paid ?? 0);

        if (!$includeRoom) {
            return max(0, $unpaidServicesTsh);
        }

        // booking.total_price already includes approved extension in this app
        $roomBillTsh = (float) ($this->total_price ?? 0);
        $remainingAfterServices = max(0, $amountPaidPool - $paidServicesTsh);
        $roomOutstandingTsh = max(0, $roomBillTsh - $remainingAfterServices);

        return max(0, $roomOutstandingTsh + $unpaidServicesTsh);
    }
}
