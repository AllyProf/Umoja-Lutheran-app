<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DayService extends Model
{
    protected $fillable = [
        'service_reference',
        'service_type',
        'guest_name',
        'guest_phone',
        'guest_email',
        'number_of_people',
        'adult_quantity',
        'child_quantity',
        'service_date',
        'service_time',
        'items_ordered',
        'package_items',
        'amount',
        'payment_status',
        'payment_method',
        'payment_provider',
        'payment_reference',
        'amount_paid',
        'exchange_rate',
        'guest_type',
        'notes',
        'registered_by',
        'paid_at',
        'discount_type',
        'discount_value',
        'discount_amount',
        'discount_reason',
    ];

    protected $casts = [
        'service_date' => 'date',
        'service_time' => 'datetime',
        'amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'exchange_rate' => 'decimal:4',
        'paid_at' => 'datetime',
        'number_of_people' => 'integer',
        'adult_quantity' => 'integer',
        'child_quantity' => 'integer',
        'package_items' => 'array',
        'discount_value' => 'decimal:2',
        'discount_amount' => 'decimal:2',
    ];

    // Relationships
    public function registeredBy()
    {
        return $this->belongsTo(\App\Models\Staff::class, 'registered_by');
    }

    /**
     * Get the service requests associated with this day service session
     */
    public function serviceRequests()
    {
        return $this->hasMany(ServiceRequest::class);
    }

    // Accessors
    public function getServiceTypeNameAttribute()
    {
        $serviceKey = strtolower($this->service_type ?? '');
        
        // Check for specific overrides first
        $normalizedKey = strtolower($serviceKey);
        
        // Handle ceremony variations
        if (str_contains($normalizedKey, 'ceremory') || str_contains($normalizedKey, 'ceremony') || str_contains($normalizedKey, 'birthday') || str_contains($normalizedKey, 'package')) {
            return 'Ceremony/Birthday Package';
        }

        // Specific named services
        $overrides = [
            'swimming' => 'Swimming/Pool Access',
            'swimming_with_bucket' => 'Swimming with Floating Trey',
            'swimming-with-bucket' => 'Swimming with Floating Trey',
            'swimming_with_floating_trey' => 'Swimming with Floating Trey',
            'swimming-with-floating-trey' => 'Swimming with Floating Trey',
            'restaurant' => 'Restaurant',
            'bar' => 'Bar',
            'other' => 'Other',
        ];

        if (array_key_exists($normalizedKey, $overrides)) {
            return $overrides[$normalizedKey];
        }

        // If no override, try catalog
        $catalogItem = \App\Models\ServiceCatalog::where('service_key', $this->service_type)
            ->orWhere('service_key', 'LIKE', '%' . $serviceKey . '%')
            ->first();
            
        if ($catalogItem) {
            return $catalogItem->service_name;
        }
        
        // Fallback
        return ucfirst(str_replace('_', ' ', $this->service_type ?? 'Unknown'));
    }

    public function getPaymentMethodNameAttribute()
    {
        return match($this->payment_method) {
            'cash' => 'Cash',
            'card' => 'Card',
            'mobile' => 'Mobile Money',
            'bank' => 'Bank Transfer',
            'online' => 'Online Payment',
            'other' => 'Other',
            default => 'N/A',
        };
    }

    // Generate unique service reference
    public static function generateReference()
    {
        do {
            $reference = 'DSV' . strtoupper(substr(uniqid(), -8));
        } while (self::where('service_reference', $reference)->exists());

        return $reference;
    }
}
