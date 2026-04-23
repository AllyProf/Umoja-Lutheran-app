<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierWeeklyOrderItem extends Model
{
    protected $fillable = [
        'supplier_weekly_order_id',
        'product_variant_id',
        'item_name',
        'quantity',
        'qty_received',
        'unit',
        'unit_price',
        'total_price',
        'received_at',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'qty_received' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'received_at' => 'datetime',
    ];

    public function weeklyOrder()
    {
        return $this->belongsTo(SupplierWeeklyOrder::class, 'supplier_weekly_order_id');
    }

    // Alias used for whereHas queries in LocalPurchaseOrderItem
    public function supplierWeeklyOrder()
    {
        return $this->belongsTo(SupplierWeeklyOrder::class, 'supplier_weekly_order_id');
    }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function getSpentAmountAttribute()
    {
        return $this->qty_received * $this->unit_price;
    }

    public function getRemainingAmountAttribute()
    {
        return $this->total_price - $this->spent_amount;
    }
}
