<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PurchaseDeadline extends Model
{
    use HasFactory;

    protected $fillable = [
        'day_of_week',
        'deadline_time',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'deadline_time' => 'string', // Store as time string (H:i format)
        'is_active' => 'boolean',
    ];

    /**
     * Get the next deadline date
     */
    public function getNextDeadlineDate(): Carbon
    {
        $today = Carbon::now();
        $dayOfWeek = strtolower($this->day_of_week);
        
        // Map day names to Carbon day constants
        $dayMap = [
            'monday' => Carbon::MONDAY,
            'tuesday' => Carbon::TUESDAY,
            'wednesday' => Carbon::WEDNESDAY,
            'thursday' => Carbon::THURSDAY,
            'friday' => Carbon::FRIDAY,
            'saturday' => Carbon::SATURDAY,
            'sunday' => Carbon::SUNDAY,
        ];

        $targetDay = $dayMap[$dayOfWeek] ?? Carbon::FRIDAY;
        
        // Get next occurrence of that day
        $nextDeadline = $today->copy()->next($targetDay);
        
        // If today is the deadline day and time hasn't passed, use today
        if ($today->dayOfWeek === $targetDay) {
            $deadlineDateTime = $today->copy()->setTimeFromTimeString($this->deadline_time);
            if ($today->lt($deadlineDateTime)) {
                return $deadlineDateTime;
            }
        }
        
        return $nextDeadline->setTimeFromTimeString($this->deadline_time);
    }

    /**
     * Check if deadline has passed
     */
    public function isDeadlinePassed(): bool
    {
        return Carbon::now()->gt($this->getNextDeadlineDate());
    }
}
