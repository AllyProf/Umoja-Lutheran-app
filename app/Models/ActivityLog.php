<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Models\Staff;
use App\Models\Guest;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'user_type',
        'action',
        'model_type',
        'model_id',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    /**
     * Get the user that performed the action
     * Since we split users into Staff and Guest, we need to check both
     * This is an accessor, not a relationship, to avoid conflicts
     */
    public function getUserAttribute()
    {
        if (!$this->user_id) {
            return null;
        }
        
        // Try Staff first, then Guest
        $staff = Staff::find($this->user_id);
        if ($staff) {
            return $staff;
        }
        return Guest::find($this->user_id);
    }

    /**
     * Get the model that was acted upon
     */
    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Log an activity
     */
    public static function log($action, $model = null, $description = null, $oldValues = null, $newValues = null)
    {
        // Try to get user from any guard (staff, guest, or web)
        $user = auth()->guard('staff')->user() 
            ?? auth()->guard('guest')->user() 
            ?? auth()->user();
        
        return self::create([
            'user_id' => $user?->id,
            'user_type' => $user?->role ?? 'guest',
            'action' => $action,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model?->id,
            'description' => $description ?? self::generateDescription($action, $model),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Generate description from action and model
     */
    private static function generateDescription($action, $model)
    {
        if (!$model) {
            return ucfirst($action);
        }

        $modelName = class_basename($model);
        return ucfirst($action) . ' ' . $modelName . ($model->id ? " #{$model->id}" : '');
    }
}
