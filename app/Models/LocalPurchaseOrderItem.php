<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LocalPurchaseOrderItem extends Model
{
    protected $fillable = [
        'lpo_id',
        'item_name',
        'product_variant_id',
        'quantity',
        'qty_received',
        'unit_price',
        'total_price',
        'unit',
    ];

    protected $appends = ['qty_ordered', 'qty_remaining', 'actual_spent_amount', 'actual_balance'];

    public function lpo()
    {
        return $this->belongsTo(LocalPurchaseOrder::class, 'lpo_id');
    }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function getQtyOrderedAttribute()
    {
        if (!$this->product_variant_id)
            return 0;

        // 1. Sum quantities from Weekly Orders linked to this LPO
        $weeklyQty = SupplierWeeklyOrderItem::whereHas('supplierWeeklyOrder', function ($query) {
            $query->where('lpo_id', $this->lpo_id);
        })->where('product_variant_id', $this->product_variant_id)->sum('quantity');

        // 2. Sum quantities from Shopping Lists linked to this LPO
        $dailyQty = ShoppingListItem::whereHas('shoppingList', function ($query) {
            $query->where('lpo_id', $this->lpo_id);
        })->where('product_variant_id', $this->product_variant_id)->sum('quantity');

        return $weeklyQty + $dailyQty;
    }

    public function getQtyRemainingAttribute()
    {
        return $this->quantity - $this->qty_ordered;
    }

    public function getQtyReceivedAttribute()
    {
        // Direct receives into this LPO item (stock receipt / LPO receive form)
        $directReceived = (float) ($this->attributes['qty_received'] ?? 0);

        if (!$this->product_variant_id) {
            return $directReceived;
        }

        // 1. Sum received quantities from Weekly Orders linked to this LPO
        $weeklyReceived = SupplierWeeklyOrderItem::whereHas('supplierWeeklyOrder', function ($query) {
            $query->where('lpo_id', $this->lpo_id);
        })->where('product_variant_id', $this->product_variant_id)->sum('qty_received');

        // 2. Sum purchased quantities from Shopping Lists linked to this LPO
        $dailyReceived = ShoppingListItem::whereHas('shoppingList', function ($query) {
            $query->where('lpo_id', $this->lpo_id);
        })->where('product_variant_id', $this->product_variant_id)->where('is_found', true)->sum('purchased_quantity');

        return (float) ($directReceived + $weeklyReceived + $dailyReceived);
    }

    public function getActualSpentAmountAttribute()
    {
        // Direct LPO receives: qty_received column × budget unit price
        $directSpent = (float) ($this->attributes['qty_received'] ?? 0) * (float) ($this->unit_price ?? 0);

        if (!$this->product_variant_id) {
            return $directSpent;
        }

        // 1. Weekly Orders Spent (qty_received * unit_price)
        $weeklySpent = SupplierWeeklyOrderItem::whereHas('supplierWeeklyOrder', function ($query) {
            $query->where('lpo_id', $this->lpo_id);
        })->where('product_variant_id', $this->product_variant_id)->get()->sum(function ($item) {
            return (float) ($item->qty_received * $item->unit_price);
        });

        // 2. Shopping List Spent (purchased_cost)
        $dailySpent = ShoppingListItem::whereHas('shoppingList', function ($query) {
            $query->where('lpo_id', $this->lpo_id);
        })->where('product_variant_id', $this->product_variant_id)->sum('purchased_cost');

        return (float) ($directSpent + $weeklySpent + $dailySpent);
    }

    public function getActualBalanceAttribute()
    {
        return (float) ($this->total_price - $this->actual_spent_amount);
    }

    public function getSpentAmountAttribute()
    {
        return $this->actual_spent_amount;
    }

    public function getRemainingAmountAttribute()
    {
        return $this->actual_balance;
    }
}
