<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceCatalog extends Model
{
    protected $table = 'service_catalog';

    protected $fillable = [
        'service_name',
        'service_key',
        'description',
        'pricing_type',
        'price_tanzanian',
        'price_international',
        'age_group',
        'child_price_tanzanian',
        'payment_required_upfront',
        'requires_items',
        'is_active',
        'display_order',
        'notes',
        'package_items',
        'edited_by',
        'last_edited_at',
        'last_changes',
    ];

    protected $casts = [
        'price_tanzanian' => 'decimal:2',
        'price_international' => 'decimal:2',
        'child_price_tanzanian' => 'decimal:2',
        'payment_required_upfront' => 'boolean',
        'requires_items' => 'boolean',
        'is_active' => 'boolean',
        'display_order' => 'integer',
        'package_items' => 'array',
        'last_edited_at' => 'datetime',
        'last_changes' => 'array',
    ];
    
    /**
     * Get the staff member who last edited this service
     */
    public function editor()
    {
        return $this->belongsTo(Staff::class, 'edited_by');
    }

    // Scope for active services
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Get price based on guest type
    public function getPriceForGuestType($guestType)
    {
        if ($guestType === 'tanzanian') {
            return $this->price_tanzanian;
        } else {
            return $this->price_international ?? $this->price_tanzanian;
        }
    }

    // Get pricing type name
    public function getPricingTypeNameAttribute()
    {
        return match($this->pricing_type) {
            'per_person' => 'Per Person',
            'per_hour' => 'Per Hour',
            'fixed' => 'Fixed Price',
            'custom' => 'Custom',
            default => 'Unknown',
        };
    }
}
