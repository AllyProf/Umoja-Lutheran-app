<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShoppingList extends Model
{
    protected $fillable = [
        'name',
        'status',
        'total_estimated_cost',
        'total_actual_cost',
        'budget_amount',
        'amount_used',
        'amount_remaining',
        'market_name',
        'shopping_date',
        'notes',
        'purchaser_id',
    ];

    protected $casts = [
        'shopping_date' => 'date',
        'total_estimated_cost' => 'decimal:2',
        'total_actual_cost' => 'decimal:2',
        'budget_amount' => 'decimal:2',
        'amount_used' => 'decimal:2',
        'amount_remaining' => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(ShoppingListItem::class);
    }

    public function purchaser()
    {
        return $this->belongsTo(Staff::class, 'purchaser_id');
    }

    /**
     * Get total estimated cost (always calculate from items to ensure accuracy)
     */
    public function getTotalEstimatedCostAttribute($value)
    {
        return $this->items->sum('estimated_price') ?: ($value ?? 0);
    }

    /**
     * Get total actual cost (always calculate from items to ensure accuracy)
     */
    public function getTotalActualCostAttribute($value)
    {
        return $this->items->sum('purchased_cost') ?: ($value ?? 0);
    }
}
