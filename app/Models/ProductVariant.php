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
        'purchasing_unit', // e.g. Sado, Carton
        'receiving_unit',  // e.g. Kg, Litre
        'items_per_package',
        'minimum_stock_level',
        'minimum_stock_level_unit',
        'display_order',
        'is_active',
        'is_visible_to_bar',
        // PIC-based inventory tracking
        'servings_per_pic',
        'selling_unit',
        'can_sell_as_pic',
        'can_sell_as_serving',
        'selling_price_per_pic',
        'selling_price_per_serving',
        'buying_price',
        'price_history',
    ];

    protected $casts = [
        'items_per_package' => 'integer',
        'minimum_stock_level' => 'integer',
        'display_order' => 'integer',
        'is_active' => 'boolean',
        'is_visible_to_bar' => 'boolean',
        // PIC-based casts
        'servings_per_pic' => 'integer',
        'can_sell_as_pic' => 'boolean',
        'can_sell_as_serving' => 'boolean',
        'selling_price_per_pic' => 'decimal:2',
        'selling_price_per_serving' => 'decimal:2',
        'buying_price' => 'decimal:2',
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
        return match ($this->packaging) {
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
        return match ($this->selling_unit) {
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

    public function scopeActiveForBar($query)
    {
        return $query->where('is_visible_to_bar', true);
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

    /**
     * Get the current stock level in base units (bottles for drinks, kg/pcs for food/cleaning)
     */
    public function getLatestUnitCost()
    {
        // 1. Check for Shopping List items with explicit KG measurement (Most accurate for food)
        $measuredShoppingItem = \App\Models\ShoppingListItem::where('product_variant_id', $this->id)
            ->where('is_purchased', true)
            ->where('received_quantity_kg', '>', 0)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($measuredShoppingItem && $measuredShoppingItem->purchased_cost > 0) {
            return (float) ($measuredShoppingItem->purchased_cost / $measuredShoppingItem->received_quantity_kg);
        }

        // 2. Try to get the latest cost from StockReceipts
        $receipt = \App\Models\StockReceipt::where('product_variant_id', $this->id)
            ->orderBy('received_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->first();

        if ($receipt && $receipt->buying_price_per_bottle > 0) {
            return (float) $receipt->buying_price_per_bottle;
        }

        // 3. Fallback to any latest Shopping List items
        $shoppingItem = \App\Models\ShoppingListItem::where('product_variant_id', $this->id)
            ->where('is_purchased', true)
            ->where('unit_price', '>', 0)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($shoppingItem) {
            $price = (float) $shoppingItem->unit_price;

            // If the unit used in shopping list was a known package unit, normalize to base unit
            $packageUnits = ['crate', 'crates', 'package', 'packages', 'carton', 'cartons', 'case', 'cases', 'box', 'boxes', 'bundle', 'bundles', 'sado'];
            if (in_array(strtolower($shoppingItem->unit ?? ''), $packageUnits) && ($this->items_per_package ?? 0) > 1) {
                return $price / $this->items_per_package;
            }

            return $price;
        }

        return 0;
    }

    /**
     * Get a list of units that should trigger the items_per_package multiplier
     */
    public static function getPackageUnits()
    {
        return ['crate', 'crates', 'soda crate', 'soda crates', 'carton', 'cartons', 'package', 'packages', 'box', 'boxes', 'unit', 'units', 'sado', 'debe', 'kiroba', 'case', 'cases', 'bundle', 'bundles', 'pic', 'pics', 'pcs', 'tray', 'dozen', 'packet', 'bunch', 'bottle', 'bottles'];
    }

    public function getCurrentStock()
    {
        $packageUnits = self::getPackageUnits();

        // 1. Total In (Receipts + Shopping List)
        $receiptsIn = \DB::table('stock_receipts')
            ->join('product_variants', 'stock_receipts.product_variant_id', '=', 'product_variants.id')
            ->join('products', 'stock_receipts.product_id', '=', 'products.id')
            ->where('stock_receipts.product_variant_id', $this->id)
            ->sum(\DB::raw('CASE WHEN products.category = "food" AND LOWER(product_variants.receiving_unit) NOT IN ("pic", "pcs", "piece", "pieces") THEN quantity_received_packages ELSE (quantity_received_packages * product_variants.items_per_package) END'));

        $shoppingIn = \DB::table('shopping_list_items')
            ->join('product_variants', 'shopping_list_items.product_variant_id', '=', 'product_variants.id')
            ->join('products', 'shopping_list_items.product_id', '=', 'products.id')
            ->where('shopping_list_items.product_variant_id', $this->id)
            ->where('shopping_list_items.is_purchased', true)
            ->get();

        $totalShoppingIn = 0;
        foreach ($shoppingIn as $item) {
            $unit = strtolower($item->unit ?? '');
            if ($item->received_quantity_kg > 0) {
                $totalShoppingIn += $item->received_quantity_kg;
            } elseif (in_array($unit, $packageUnits) && ($this->product->category != 'food' || $this->product->category === null)) {
                $totalShoppingIn += $item->purchased_quantity * ($this->items_per_package ?: 1);
            } else {
                $totalShoppingIn += $item->purchased_quantity;
            }
        }

        // 1.5 Total Returned
        $returnsIn = \DB::table('stock_returns')
            ->where('product_variant_id', $this->id)
            ->where('status', 'received')
            ->sum('quantity');

        $totalIn = (float) $receiptsIn + (float) $totalShoppingIn + (float) $returnsIn;

        // 2. Total Out (Transfers) - Correctly handling different unit types
        $transfersOutGroups = \DB::table('stock_transfers')
            ->where('product_variant_id', $this->id)
            ->whereIn('status', ['completed', 'pending'])
            ->select('quantity_unit', \DB::raw('SUM(quantity_transferred) as total'))
            ->groupBy('quantity_unit')
            ->get();

        $totalTransferredOut = 0;
        foreach ($transfersOutGroups as $group) {
            $unit = strtolower($group->quantity_unit);
            if (in_array($unit, $packageUnits)) {
                $totalTransferredOut += $group->total * ($this->items_per_package ?: 1);
            } elseif (in_array($unit, ['glass', 'serving', 'servings'])) {
                $totalTransferredOut += $group->total / ($this->servings_per_pic ?: 1);
            } else {
                $totalTransferredOut += $group->total;
            }
        }

        return $totalIn - (float) $totalTransferredOut;
    }

    /**
     * Determine if stock level is low based on user thresholds or explicit minimum
     */
    public function isLowStock($currentStock)
    {
        // If an explicit minimum stock level is set, use it as primary source of truth
        if ($this->minimum_stock_level > 0) {
            return (float) $currentStock <= (float) $this->minimum_stock_level;
        }

        // Default logic based on unit type if no minimum is set
        $unit = strtolower($this->receiving_unit ?? 'pcs');

        if ($unit === 'kg') {
            return (float) $currentStock < 2.0;
        }

        if (in_array($unit, ['pcs', 'pieces', 'piece', 'bottle', 'bottles'])) {
            return (float) $currentStock <= 2.0;
        }

        // Fallback for other units (Grams, Litres, etc.)
        return (float) $currentStock < 2.0;
    }
}
