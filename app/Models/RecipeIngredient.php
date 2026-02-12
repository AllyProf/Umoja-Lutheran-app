<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecipeIngredient extends Model
{
    use HasFactory;

    protected $fillable = [
        'recipe_id',
        'product_id',
        'custom_ingredient_name',
        'quantity',
        'unit',
        'notes'
    ];

    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getNameAttribute()
    {
        return $this->product_id ? $this->product->name : $this->custom_ingredient_name;
    }
}
