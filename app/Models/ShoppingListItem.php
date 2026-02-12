<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShoppingListItem extends Model
{
    protected $fillable = [
        'shopping_list_id',
        'product_id',
        'product_variant_id',
        'product_name',
        'category',
        'quantity',
        'unit',
        'estimated_price',
        'is_purchased',
        'purchased_quantity',
        'purchased_cost',
        'unit_price',
        'is_found',
        'storage_location',
        'purchase_request_id',
        'transferred_to_department',
        'is_received_by_department',
        'received_by_department_at',
        'expiry_date',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'estimated_price' => 'decimal:2',
        'purchased_quantity' => 'decimal:2',
        'purchased_cost' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'is_purchased' => 'boolean',
        'is_found' => 'boolean',
        'is_received_by_department' => 'boolean',
        'received_by_department_at' => 'datetime',
        'expiry_date' => 'date',
    ];
    
    /**
     * Get the purchase request this item is linked to
     */
    public function purchaseRequest()
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

    public function shoppingList()
    {
        return $this->belongsTo(ShoppingList::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getCategoryNameAttribute()
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
     * Get the associated product variant (or first variant of product)
     */
    public function getProductVariantAttribute()
    {
        // 1. Precise variant link (Priority)
        if ($this->getAttribute('product_variant_id')) {
            return ProductVariant::find($this->getAttribute('product_variant_id'));
        }

        // 2. Standard product link
        if ($this->product_id) {
            return ProductVariant::where('product_id', $this->product_id)->orderBy('id')->first();
        }

        // 3. Smart name & size search (fallback for manually typed items)
        $name = $this->product_name;
        $size = null;
        if (preg_match('/\((.*?)\)/', $name, $matches)) {
            $size = trim($matches[1]);
            $baseName = trim(str_replace($matches[0], '', $name));
        } else {
            $baseName = trim($name);
        }

        // --- MATCHING STRATEGY ---
        
        // A. Match by Variant Name directly (Top Priority for Spirits/Drinks)
        // Many records use "Konyagi" as Variant Name and "Vodka" as Product name
        $variant = ProductVariant::where('variant_name', $baseName)->first()
                 ?? ProductVariant::where('variant_name', 'LIKE', $baseName . '%')->first()
                 ?? ProductVariant::where('variant_name', 'LIKE', '%' . $baseName . '%')->first();

        // B. Match by Product Name
        if (!$variant) {
            $product = Product::where('name', $baseName)->first()
                     ?? Product::where('name', 'LIKE', $baseName . '%')->first()
                     ?? Product::where('name', 'LIKE', '%' . $baseName . '%')->first()
                     // Reverse search: e.g. baseName is "Kilimanjaro Water Small", product name is "Kilimanjaro Water"
                     ?? Product::whereRaw('? LIKE CONCAT("%", name, "%")', [$baseName])->first();

            if ($product) {
                // If we found a product, try to see if the baseName also contains a variant name
                // e.g. "Kilimanjaro Water Small" -> check if any variant of product 6 is named "Small"
                $remainingPart = trim(str_ireplace($product->name, '', $baseName));
                $variantQuery = ProductVariant::where('product_id', $product->id);
                
                if ($remainingPart) {
                    $specificVariant = (clone $variantQuery)->where('variant_name', 'LIKE', '%' . $remainingPart . '%')->first();
                    if ($specificVariant) $variant = $specificVariant;
                }
                
                if (!$variant) $variant = $variantQuery->orderBy('id')->first();
            }
        }

        // C. Refine by Size if multiple variants exist
        if ($variant && $size) {
            $sizedVariant = ProductVariant::where('product_id', $variant->product_id)
                ->where(function($q) use ($size) {
                    $q->where('measurement', 'LIKE', $size)
                      ->orWhere('measurement', 'LIKE', '%' . $size . '%')
                      ->orWhere('variant_name', 'LIKE', '%' . $size . '%');
                })->first();
            
            if ($sizedVariant) {
                return $sizedVariant;
            }
        }

        return $variant;
    }
}
