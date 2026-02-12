<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KitchenInventoryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'unit',
        'current_stock',
        'minimum_stock',
        'expiry_date',
        'description',
        'is_active',
    ];

    protected $casts = [
        'current_stock' => 'decimal:2',
        'minimum_stock' => 'decimal:2',
        'expiry_date' => 'date',
        'is_active' => 'boolean',
    ];

    protected $appends = ['category_name', 'total_received', 'total_consumed'];

    /**
     * Get human readable category name
     */
    public function getCategoryNameAttribute(): string
    {
        return match($this->category) {
            'meat_poultry' => 'Meat & Poultry',
            'seafood' => 'Seafood & Fish',
            'vegetables' => 'Vegetables & Fruits',
            'dairy' => 'Dairy & Eggs',
            'pantry_baking' => 'Pantry & Baking',
            'spices_herbs' => 'Spices & Herbs',
            'grains_pasta' => 'Grains & Pasta',
            'bakery' => 'Bakery & Bread',
            'oils_fats' => 'Oils & Fats',
            'frozen_foods' => 'Frozen Foods',
            'canned_goods' => 'Canned & Packaged Goods',
            'beverages' => 'Beverages',
            'water' => 'Water',
            'kitchen_disposables' => 'Kitchen Disposables',
            'cleaning_supplies' => 'Cleaning Supplies',
            'linens' => 'Linens',
            'other' => 'Other',
            default => ucfirst(str_replace('_', ' ', $this->category ?? '')),
        };
    }

    /**
     * Get total quantity received (sum of all supply movements)
     */
    public function getTotalReceivedAttribute(): float
    {
        return (float) $this->movements()
            ->whereIn('movement_type', ['supply', 'manual_add'])
            ->sum('quantity');
    }

    /**
     * Get total quantity consumed (sum of all usage movements)
     */
    public function getTotalConsumedAttribute(): float
    {
        return (float) $this->movements()
            ->whereIn('movement_type', ['guest_use', 'staff_use', 'sale', 'internal_use', 'destroyed'])
            ->sum('quantity');
    }

    /**
     * Get stock status level
     */
    public function getStockStatus(): string
    {
        if ($this->current_stock <= 0) {
            return 'critical';
        } elseif ($this->current_stock <= $this->minimum_stock) {
            return 'low';
        } else {
            return 'normal';
        }
    }

    /**
     * Get movements for this item
     */
    public function movements(): HasMany
    {
        return $this->hasMany(KitchenStockMovement::class, 'inventory_item_id');
    }
}
