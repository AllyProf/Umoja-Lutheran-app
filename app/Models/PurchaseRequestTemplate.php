<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseRequestTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'items',
        'created_by',
        'is_active',
    ];

    protected $casts = [
        'items' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the staff member who created this template
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'created_by');
    }
}
