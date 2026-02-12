<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class StockTransfer extends Model
{
    protected $fillable = [
        'transfer_reference',
        'product_id',
        'product_variant_id',
        'quantity_transferred',
        'quantity_unit',
        'transferred_by',
        'received_by',
        'status',
        'transfer_date',
        'received_at',
        'notes',
        // PIC-based revenue tracking
        'unit_cost',
        'total_cost',
        'selling_price_per_pic',
        'selling_price_per_serving',
        'servings_per_pic',
        'expected_revenue_pic_sale',
        'expected_revenue_serving_sale',
        'expected_profit_serving_sale',
        'expiry_date',
    ];

    protected $appends = ['expected_profit', 'total_bottles', 'selling_price', 'expected_revenue', 'buying_price'];

    /**
     * Get the buying price per bottle from the latest receipt
     */
    public function getBuyingPriceAttribute()
    {
        $receipt = $this->getLatestReceipt();
        return $receipt ? $receipt->buying_price_per_bottle : 0;
    }

    /**
     * Calculate expected total revenue (collection) for this transfer
     */
    public function getExpectedRevenueAttribute()
    {
        return $this->total_bottles * $this->selling_price;
    }

    /**
     * Get the selling price per bottle from the latest receipt
     */
    public function getSellingPriceAttribute()
    {
        $receipt = $this->getLatestReceipt();
        return $receipt ? $receipt->selling_price_per_bottle : 0;
    }

    protected $casts = [
        'quantity_transferred' => 'integer',
        'transfer_date' => 'date',
        'received_at' => 'datetime',
        'expiry_date' => 'date',
        // PIC-based revenue casts
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'selling_price_per_pic' => 'decimal:2',
        'selling_price_per_serving' => 'decimal:2',
        'servings_per_pic' => 'integer',
        'expected_revenue_pic_sale' => 'decimal:2',
        'expected_revenue_serving_sale' => 'decimal:2',
        'expected_profit_pic_sale' => 'decimal:2',
        'expected_profit_serving_sale' => 'decimal:2',
    ];



    /**
     * Calculate total quantity in bottles
     */
    public function getTotalBottlesAttribute()
    {
        $total = $this->quantity_transferred;
        if ($this->quantity_unit === 'packages') {
            $total *= ($this->productVariant->items_per_package ?? 0);
        }
        return $total;
    }

    // Relationships
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function transferredBy()
    {
        return $this->belongsTo(Staff::class, 'transferred_by');
    }

    public function receivedBy()
    {
        return $this->belongsTo(Staff::class, 'received_by');
    }

    // Accessors
    public function getStatusNameAttribute()
    {
        return match($this->status) {
            'pending' => 'Pending',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            default => 'Unknown',
        };
    }

    public function getQuantityUnitNameAttribute()
    {
        return match($this->quantity_unit) {
            'packages' => $this->productVariant->packaging_name ?? 'Packages',
            'bottles' => 'Bottles',
            default => 'Packages',
        };
    }

    // Static methods
    public static function generateReference()
    {
        do {
            $reference = 'TRF' . strtoupper(substr(uniqid(), -8));
        } while (self::where('transfer_reference', $reference)->exists());

        return $reference;
    }

    /**
     * Get the latest stock receipt for this variant to determine prices
     */
    public function getLatestReceipt()
    {
        return StockReceipt::where('product_variant_id', $this->product_variant_id)
            ->orderBy('received_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->first();
    }

    /**
     * Calculate expected profit for this transfer
     */
    public function getExpectedProfitAttribute()
    {
        $receipt = $this->getLatestReceipt();
        if (!$receipt) return 0;

        $profitPerBottle = $receipt->selling_price_per_bottle - $receipt->buying_price_per_bottle;
        
        $totalItems = $this->quantity_transferred;
        if ($this->quantity_unit === 'packages') {
            $totalItems *= ($this->productVariant->items_per_package ?? 0);
        }

        return $totalItems * $profitPerBottle;
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeForBarKeeper($query, $staffId)
    {
        return $query->where('received_by', $staffId);
    }

    // PIC-based Revenue Calculation Methods
    public function calculateRevenueProjections()
    {
        if (!$this->productVariant) {
            return;
        }

        // Calculate total cost
        $this->total_cost = $this->quantity_transferred * ($this->unit_cost ?? 0);

        // Get prices from variant if not set
        $this->selling_price_per_pic = $this->selling_price_per_pic ?? $this->productVariant->selling_price_per_pic ?? 0;
        $this->selling_price_per_serving = $this->selling_price_per_serving ?? $this->productVariant->selling_price_per_serving ?? 0;
        $this->servings_per_pic = $this->servings_per_pic ?? $this->productVariant->servings_per_pic ?? 1;

        // Calculate expected revenue for PIC sale
        $this->expected_revenue_pic_sale = $this->quantity_transferred * $this->selling_price_per_pic;

        // Calculate expected revenue for serving sale
        $totalServings = $this->quantity_transferred * $this->servings_per_pic;
        $this->expected_revenue_serving_sale = $totalServings * $this->selling_price_per_serving;

        // Calculate profits
        $this->expected_profit_pic_sale = $this->expected_revenue_pic_sale - $this->total_cost;
        $this->expected_profit_serving_sale = $this->expected_revenue_serving_sale - $this->total_cost;
    }

    public function getProfitDifferenceAttribute()
    {
        return ($this->expected_profit_serving_sale ?? 0) - ($this->expected_profit_pic_sale ?? 0);
    }

    public function getProfitMarginPicAttribute()
    {
        if ($this->expected_revenue_pic_sale > 0) {
            return ($this->expected_profit_pic_sale / $this->expected_revenue_pic_sale) * 100;
        }
        return 0;
    }

    public function getProfitMarginServingAttribute()
    {
        if ($this->expected_revenue_serving_sale > 0) {
            return ($this->expected_profit_serving_sale / $this->expected_revenue_serving_sale) * 100;
        }
        return 0;
    }

    public function getRecommendedSellingMethodAttribute()
    {
        return $this->expected_profit_serving_sale > $this->expected_profit_pic_sale ? 'serving' : 'pic';
    }
}
