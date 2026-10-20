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

    /**
     * Get the day services associated with this shift closure.
     */
    public function dayServices(): HasMany
    {
        return $this->hasMany(DayService::class, 'shift_closure_id');
    }

    /**
     * Get the bookings associated with this shift closure.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'shift_closure_id');
    }

    /**
     * Normalize payment method into cash | mpesa | other.
     */
    public static function normalizePaymentMethod(?string $method): string
    {
        $m = strtolower(trim((string) $method));
        if ($m === 'cash') {
            return 'cash';
        }
        if (in_array($m, ['mpesa', 'm-pesa', 'mobile', 'mobile_money', 'mixx', 'tigopesa', 'airtel'], true)) {
            return 'mpesa';
        }

        return 'other';
    }

    /**
     * Reception shift collections split: room bookings vs restaurant vs day/other services.
     * Restaurant = food/bar settled at reception (paid_at_reception), not double-counted in bookings.
     *
     * @return array{
     *   bookings: array{count:int,total:float,cash:float,mpesa:float,other:float},
     *   restaurant: array{count:int,total:float,cash:float,mpesa:float,other:float},
     *   day_services: array{count:int,total:float,cash:float,mpesa:float,other:float},
     *   other_services: array{count:int,total:float,cash:float,mpesa:float,other:float},
     *   total_cash: float,
     *   total_mpesa: float,
     *   total_other: float,
     *   grand_total: float
     * }
     */
    public function receptionCollectionsBreakdown(): array
    {
        $empty = fn () => ['count' => 0, 'total' => 0.0, 'cash' => 0.0, 'mpesa' => 0.0, 'other' => 0.0];

        $bookings = Booking::where('shift_closure_id', $this->id)
            ->whereIn('payment_status', ['paid', 'partial'])
            ->get();

        $dayServices = DayService::where('shift_closure_id', $this->id)
            ->where('payment_status', 'paid')
            ->get();

        $walkInServices = ServiceRequest::where('shift_closure_id', $this->id)
            ->where('payment_status', 'paid')
            ->where(function ($q) {
                $q->whereNull('service_specific_data->paid_at_reception')
                    ->orWhere('service_specific_data->paid_at_reception', false);
            })
            ->get();

        // Restaurant / bar collected at reception for this shift
        $restaurantOrders = ServiceRequest::with(['booking.room', 'service'])
            ->where('payment_status', 'paid')
            ->where(function ($q) {
                $q->where('service_specific_data->reception_shift_closure_id', $this->id)
                    ->orWhere(function ($inner) {
                        $inner->where('service_specific_data->paid_at_reception', true)
                            ->whereIn('booking_id', Booking::where('shift_closure_id', $this->id)->select('id'));
                    });
            })
            ->get()
            ->unique('id')
            ->values()
            ->filter(fn (ServiceRequest $sr) => $sr->wasPaidAtReception());

        $bucketAdd = function (array &$bucket, float $amount, ?string $method) {
            $bucket['count']++;
            $bucket['total'] += $amount;
            $key = self::normalizePaymentMethod($method);
            $bucket[$key] += $amount;
        };

        $bookingsBucket = $empty();
        $restaurantByBooking = $restaurantOrders->groupBy('booking_id');

        foreach ($bookings as $booking) {
            $restaurantForBooking = (float) ($restaurantByBooking->get($booking->id)?->sum('total_price_tsh') ?? 0);
            // Room/booking portion only — restaurant settled on the bill is shown separately
            $roomPortion = max(0, (float) ($booking->amount_paid ?? 0) - $restaurantForBooking);
            if ($roomPortion < 50) {
                continue;
            }
            $bucketAdd($bookingsBucket, $roomPortion, $booking->payment_method);
        }

        $restaurantBucket = $empty();
        foreach ($restaurantOrders as $sr) {
            $bucketAdd($restaurantBucket, (float) $sr->total_price_tsh, $sr->payment_method);
        }

        $dayBucket = $empty();
        foreach ($dayServices as $ds) {
            $bucketAdd($dayBucket, (float) $ds->amount_paid, $ds->payment_method);
        }

        $otherBucket = $empty();
        foreach ($walkInServices as $sr) {
            $bucketAdd($otherBucket, (float) $sr->total_price_tsh, $sr->payment_method);
        }

        $totalCash = $bookingsBucket['cash'] + $restaurantBucket['cash'] + $dayBucket['cash'] + $otherBucket['cash'];
        $totalMpesa = $bookingsBucket['mpesa'] + $restaurantBucket['mpesa'] + $dayBucket['mpesa'] + $otherBucket['mpesa'];
        $totalOther = $bookingsBucket['other'] + $restaurantBucket['other'] + $dayBucket['other'] + $otherBucket['other'];

        return [
            'bookings' => $bookingsBucket,
            'restaurant' => $restaurantBucket,
            'day_services' => $dayBucket,
            'other_services' => $otherBucket,
            'total_cash' => $totalCash,
            'total_mpesa' => $totalMpesa,
            'total_other' => $totalOther,
            'grand_total' => $totalCash + $totalMpesa + $totalOther,
            'restaurant_orders' => $restaurantOrders,
        ];
    }
}
