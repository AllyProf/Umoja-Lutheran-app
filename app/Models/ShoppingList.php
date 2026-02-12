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
    
    /**
     * Get total estimated cost (calculate from items if not stored)
     */
    public function getTotalEstimatedCostAttribute($value)
    {
        // If value is explicitly set (even if 0), use it
        if ($this->attributes['total_estimated_cost'] !== null) {
            return $value;
        }
        // Calculate from items if not stored (null)
        return $this->items->sum('estimated_price') ?? 0;
    }
    
    /**
     * Get total actual cost (from stored value or calculate from purchased items)
     */
    public function getTotalActualCostAttribute($value)
    {
        // If value is explicitly set (even if 0), use it
        if ($this->attributes['total_actual_cost'] !== null) {
            return $value;
        }
        // Calculate from purchased items if not stored (null)
        return $this->items->sum('purchased_cost') ?? 0;
    }
}
