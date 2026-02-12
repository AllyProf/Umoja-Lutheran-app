<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockReceipt extends Model
{
    protected $fillable = [
        'product_id',
        'product_variant_id',
        'supplier_id',
        'quantity_received_packages',
        'buying_price_per_bottle',
        'selling_price_per_bottle',
        'discount_type',
        'discount_amount',
        'received_date',
        'expiry_date',
        'notes',
        'received_by',
    ];

    protected $casts = [
        'quantity_received_packages' => 'integer',
        'buying_price_per_bottle' => 'decimal:2',
        'selling_price_per_bottle' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'received_date' => 'date',
        'expiry_date' => 'date',
    ];

    // Relationships
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function receivedBy()
    {
        return $this->belongsTo(\App\Models\Staff::class, 'received_by');
    }

    // Accessors - Calculate totals dynamically
    public function getTotalBottlesAttribute()
    {
        return $this->quantity_received_packages * ($this->productVariant->items_per_package ?? 0);
    }

    public function getProfitPerBottleAttribute()
    {
        return $this->selling_price_per_bottle - $this->buying_price_per_bottle;
    }

    public function getTotalBuyingCostAttribute()
    {
        return $this->total_bottles * $this->buying_price_per_bottle;
    }

    public function getTotalProfitAttribute()
    {
        return $this->total_bottles * $this->profit_per_bottle;
    }

    public function getDiscountTypeNameAttribute()
    {
        return match($this->discount_type) {
            'percentage' => 'Percentage',
            'fixed' => 'Fixed Amount',
            'none' => 'None',
            default => 'None',
        };
    }
}
