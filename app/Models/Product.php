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
        'usage',
        'is_active',
        'is_returnable',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_returnable' => 'boolean',
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

    public function departments()
    {
        return $this->belongsToMany(Department::class, 'department_product')
            ->withPivot('category', 'notes')
            ->withTimestamps();
    }

    // Accessors
    public function getCategoryNameAttribute()
    {
        return match ($this->category) {
            'spirits' => 'Spirits',
            'wines' => 'Wines',
            'non_alcoholic_beverage' => 'Soft Drinks',
            'cleaning_supplies' => 'Housekeeping',
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
