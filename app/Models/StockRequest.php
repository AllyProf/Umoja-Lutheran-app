<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'requested_by',
        'batch_id',
        'batch_reference',
        'product_variant_id',
        'quantity',
        'unit',
        'status',
        'accountant_id',
        'accountant_approved_at',
        'manager_id',
        'manager_approved_at',
        'storekeeper_id',
        'distributed_at',
        'stock_transfer_id',
        'notes',
        'rejection_reason',
        'unit_cost',
        'total_cost',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'accountant_approved_at' => 'datetime',
        'manager_approved_at' => 'datetime',
        'distributed_at' => 'datetime',
    ];

    /**
     * Get the staff member who requested this
     */
    public function requester(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'requested_by');
    }

    /**
     * Get the product variant being requested
     */
    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    /**
     * Get the accountant who reviewed this
     */
    public function accountant(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'accountant_id');
    }

    /**
     * Get the manager who approved this
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'manager_id');
    }

    /**
     * Get the storekeeper who distributed this
     */
    public function storekeeper(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'storekeeper_id');
    }

    /**
     * Get the associated stock transfer
     */
    public function stockTransfer(): BelongsTo
    {
        return $this->belongsTo(StockTransfer::class, 'stock_transfer_id');
    }

    /**
     * Helper to get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending_accountant' => 'Pending Accountant',
            'pending_manager' => 'Pending Manager',
            'approved' => 'Approved (Pending Store)',
            'rejected' => 'Rejected',
            'completed' => 'Completed',
            default => ucfirst($this->status),
        };
    }

    /**
     * Helper to get status color class
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending_accountant' => 'warning',
            'pending_manager' => 'info',
            'approved' => 'primary',
            'rejected' => 'danger',
            'completed' => 'success',
            default => 'secondary',
        };
    }

    /**
     * Get the projected or actual buying price per bottle
     */
    public function getBuyingPriceAttribute()
    {
        if ($this->unit_cost > 0) {
            // If the unit requested was packages/crates, the unit_cost stored is for that package.
            // Normalize it back to per-bottle for calculations.
            if ($this->unit === 'packages' && ($this->productVariant->items_per_package ?? 0) > 0) {
                return (float) ($this->unit_cost / $this->productVariant->items_per_package);
            }
            return (float) $this->unit_cost;
        }

        if ($this->stockTransfer && $this->stockTransfer->buying_price > 0) {
            return $this->stockTransfer->buying_price;
        }

        $receipt = \App\Models\StockReceipt::where('product_variant_id', $this->product_variant_id)
            ->orderBy('received_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$receipt)
            return 0;

        $raw_bp = $receipt->buying_price_per_bottle ?: 0;
        $sp = $this->selling_price;
        // If raw_bp appears to be a crate price (legacy data protection)
        if ($this->productVariant && ($this->productVariant->items_per_package ?? 0) > 1 && $raw_bp > ($sp * 2) && $sp > 0) {
            return $raw_bp / $this->productVariant->items_per_package;
        }

        return $raw_bp;
    }

    /**
     * Get the projected or actual selling price per bottle
     */
    public function getSellingPriceAttribute()
    {
        if ($this->stockTransfer) {
            return $this->stockTransfer->selling_price;
        }
        if ($this->productVariant && $this->productVariant->selling_price_per_pic > 0) {
            return $this->productVariant->selling_price_per_pic;
        }
        return 0;
    }

    public function getTotalBottlesAttribute()
    {
        $total = $this->quantity;
        if ($this->unit === 'packages') {
            $total *= ($this->productVariant->items_per_package ?? 0);
        }
        return $total;
    }

    /**
     * Get the projected or actual expected revenue
     */
    public function getExpectedRevenueAttribute()
    {
        if ($this->stockTransfer) {
            return $this->stockTransfer->expected_revenue;
        }
        return $this->total_bottles * $this->selling_price;
    }

    /**
     * Get the projected or actual expected profit
     */
    public function getExpectedProfitAttribute()
    {
        if ($this->stockTransfer) {
            return $this->stockTransfer->expected_profit;
        }
        return ($this->selling_price - $this->buying_price) * $this->total_bottles;
    }
}
