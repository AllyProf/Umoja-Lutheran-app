<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'supplier_id',
        'brand_or_type',
        'category',
        'image',
        'description',
        'type',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function stockReceipts()
    {
        return $this->hasMany(StockReceipt::class);
    }

    public function shoppingListItems()
    {
        return $this->hasMany(ShoppingListItem::class);
    }

    // Accessors
    public function getCategoryNameAttribute()
    {
        return match($this->category) {
            'spirits' => 'Spirits',
            'wines' => 'Wines',
            'non_alcoholic_beverage' => 'Soda / Soft Drinks',
            'energy_drinks' => 'Energy Drinks',
            'juices' => 'Juices',
            'hot_beverages' => 'Hot Beverages',
            'cocktails' => 'Cocktails',
            'alcoholic_beverage' => 'Beer / Cider',
            'water' => 'Water',
            'food' => 'General Food',
            default => ucfirst(str_replace('_', ' ', $this->category ?? '')),
        };
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }
}
