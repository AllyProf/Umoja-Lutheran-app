<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_number',
        'status',
        'room_type',
        'capacity',
        'bed_type',
        'floor_location',
        'sku_code',
        'description',
        'price_per_night',
        'extra_guest_fee',
        'peak_season_price',
        'off_season_price',
        'discount_percentage',
        'promo_code',
        'amenities',
        'bathroom_type',
        'checkin_time',
        'checkout_time',
        'pet_friendly',
        'smoking_allowed',
        'special_notes',
        'wifi_password',
        'wifi_network_name',
        'images',
    ];

    protected $casts = [
        'amenities' => 'array',
        'images' => 'array',
        'pet_friendly' => 'boolean',
        'smoking_allowed' => 'boolean',
        'price_per_night' => 'decimal:2',
        'extra_guest_fee' => 'decimal:2',
        'peak_season_price' => 'decimal:2',
        'off_season_price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
    ];

    /**
     * Get the bookings for this room.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get the cleaning logs for this room.
     */
    public function cleaningLogs(): HasMany
    {
        return $this->hasMany(RoomCleaningLog::class);
    }

    /**
     * Get the latest cleaning log for this room.
     */
    public function latestCleaningLog()
    {
        return $this->hasOne(RoomCleaningLog::class)->latestOfMany();
    }

    /**
     * Get room issues for this room.
     */
    public function issues(): HasMany
    {
        return $this->hasMany(RoomIssue::class);
    }
}

