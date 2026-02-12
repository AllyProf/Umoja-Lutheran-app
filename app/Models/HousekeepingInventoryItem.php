<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HousekeepingInventoryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'unit',
        'current_stock',
        'minimum_stock',
        'reorder_quantity',
        'description',
    ];

    protected $casts = [
        'current_stock' => 'decimal:2',
        'minimum_stock' => 'decimal:2',
        'reorder_quantity' => 'decimal:2',
    ];

    /**
     * Check if stock is low
     */
    public function isLowStock(): bool
    {
        return $this->current_stock <= $this->minimum_stock;
    }

    /**
     * Get stock status level
     * Returns: 'critical', 'low', or 'normal'
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
     * Get stock status color class
     */
    public function getStockStatusColor(): string
    {
        return match($this->getStockStatus()) {
            'critical' => 'danger',
            'low' => 'warning',
            default => 'success',
        };
    }

    /**
     * Get stock movements for this item
     */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(InventoryStockMovement::class, 'inventory_item_id');
    }
}
