<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'contact_person',
        'guider_email',
        'guider_phone',
        'billing_address',
        'payment_terms',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get all bookings for this company
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
