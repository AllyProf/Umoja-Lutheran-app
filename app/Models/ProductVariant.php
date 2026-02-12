<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'variant_name',
        'image',
        'measurement',
        'packaging',
        'items_per_package',
        'minimum_stock_level',
        'minimum_stock_level_unit',
        'display_order',
        'is_active',
        // PIC-based inventory tracking
        'servings_per_pic',
        'selling_unit',
        'can_sell_as_pic',
        'can_sell_as_serving',
        'selling_price_per_pic',
        'selling_price_per_serving',
        'price_history',
    ];

    protected $casts = [
        'items_per_package' => 'integer',
        'minimum_stock_level' => 'integer',
        'display_order' => 'integer',
        'is_active' => 'boolean',
        // PIC-based casts
        'servings_per_pic' => 'integer',
        'can_sell_as_pic' => 'boolean',
        'can_sell_as_serving' => 'boolean',
        'selling_price_per_pic' => 'decimal:2',
        'selling_price_per_serving' => 'decimal:2',
        'price_history' => 'array',
    ];

    // Relationships
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function stockReceipts()
    {
        return $this->hasMany(StockReceipt::class);
    }

    // Accessors
    public function getPackagingNameAttribute()
    {
        return match($this->packaging) {
            'crates' => 'Crates',
            'carton' => 'Carton',
            'boxes' => 'Boxes',
            'bags' => 'Bags',
            'packets' => 'Packets',
            default => ucfirst($this->packaging ?? ''),
        };
    }

    public function getSellingUnitNameAttribute()
    {
        return match($this->selling_unit) {
            'pic' => 'PIC (Bottle)',
            'glass' => 'Glass',
            'tot' => 'Tot/Shot',
            'shot' => 'Shot',
            'cocktail' => 'Cocktail',
            default => ucfirst($this->selling_unit ?? 'PIC'),
        };
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Helper Methods for Revenue Calculations
    public function calculateExpectedRevenue($quantity, $method = 'serving')
    {
        if ($method === 'pic') {
            return $quantity * ($this->selling_price_per_pic ?? 0);
        }
        return ($quantity * ($this->servings_per_pic ?? 1)) * ($this->selling_price_per_serving ?? 0);
    }

    public function calculateProfit($quantity, $unitCost, $method = 'serving')
    {
        $totalCost = $quantity * $unitCost;
        $revenue = $this->calculateExpectedRevenue($quantity, $method);
        return $revenue - $totalCost;
    }

    public function getProfitMargin($unitCost, $method = 'serving')
    {
        if ($method === 'pic' && $this->selling_price_per_pic > 0) {
            return (($this->selling_price_per_pic - $unitCost) / $this->selling_price_per_pic) * 100;
        } elseif ($method === 'serving' && $this->selling_price_per_serving > 0) {
            $costPerServing = $unitCost / ($this->servings_per_pic ?? 1);
            return (($this->selling_price_per_serving - $costPerServing) / $this->selling_price_per_serving) * 100;
        }
        return 0;
    }

    public function getTotalServings($picsQuantity)
    {
        return $picsQuantity * ($this->servings_per_pic ?? 1);
    }
}
