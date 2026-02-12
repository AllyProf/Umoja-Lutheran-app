<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'category',
        'price_tsh',
        'is_free_for_internal',
        'age_group',
        'child_price_tsh',
        'unit',
        'is_active',
        'requires_approval',
        'required_fields',
    ];

    protected $casts = [
        'price_tsh' => 'decimal:2',
        'is_free_for_internal' => 'boolean',
        'child_price_tsh' => 'decimal:2',
        'is_active' => 'boolean',
        'requires_approval' => 'boolean',
        'required_fields' => 'array',
    ];

    /**
     * Get all service requests for this service
     */
    public function serviceRequests(): HasMany
    {
        return $this->hasMany(ServiceRequest::class);
    }
}
