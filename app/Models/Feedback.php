<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Feedback extends Model
{
    use HasFactory;

    protected $table = 'feedbacks';

    protected $fillable = [
        'booking_id',
        'guest_name',
        'guest_email',
        'rating',
        'comment',
        'categories',
    ];

    protected $casts = [
        'categories' => 'array',
        'rating' => 'integer',
    ];

    /**
     * Get the booking that this feedback belongs to.
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}

