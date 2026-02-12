<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Recipe extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'category',
        'prep_time',
        'image',
        'selling_price',
        'is_available',
        'created_by'
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'selling_price' => 'decimal:2',
        'prep_time' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($recipe) {
            $recipe->slug = Str::slug($recipe->name) . '-' . uniqid();
        });
    }

    public function creator()
    {
        return $this->belongsTo(Staff::class, 'created_by');
    }

    /**
     * Get category name for display
     */
    public function getCategoryNameAttribute()
    {
        return match($this->category) {
            'appetizers' => 'Appetizers',
            'main_course' => 'Main Course',
            'desserts' => 'Desserts',
            'beverages' => 'Beverages',
            'breakfast' => 'Breakfast',
            'lunch' => 'Lunch',
            'dinner' => 'Dinner',
            'snacks' => 'Snacks',
            'salads' => 'Salads',
            'soups' => 'Soups',
            default => ucfirst(str_replace('_', ' ', $this->category ?? 'Other')),
        };
    }

    /**
     * Scope for available menu items
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    /**
     * Scope for specific category
     */
    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }
}
