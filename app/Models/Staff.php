<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Staff extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'staffs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'profile_photo',
        'phone',
        'department',
        'position',
        'hire_date',
        'is_active',
        'notification_preferences',
        'session_token',
        'last_session_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'hire_date' => 'date',
            'notification_preferences' => 'array',
        ];
    }

    /**
     * Check if email notifications are enabled for this user
     */
    public function emailNotificationsEnabled(): bool
    {
        $prefs = $this->notification_preferences ?? [];
        return $prefs['email_notifications_enabled'] ?? true; // Default to true
    }

    /**
     * Check if a specific notification type is enabled
     */
    public function isNotificationEnabled(string $type): bool
    {
        if (!$this->emailNotificationsEnabled()) {
            return false;
        }
        
        $prefs = $this->notification_preferences ?? [];
        $key = $type . '_notifications';
        return $prefs[$key] ?? true; // Default to true
    }

    /**
     * Get the role model
     */
    public function roleModel()
    {
        if (!$this->role) {
            return null;
        }
        
        $staffRole = trim($this->role);
        
        // Try exact match first
        $role = Role::where('name', $staffRole)->first();
        if ($role) {
            return $role;
        }
        
        // Try case-insensitive match
        $role = Role::whereRaw('LOWER(TRIM(name)) = ?', [strtolower($staffRole)])->first();
        if ($role) {
            return $role;
        }
        
        // Try matching with spaces/underscores normalized
        $normalizedStaffRole = str_replace([' ', '_'], '', strtolower($staffRole));
        $role = Role::whereRaw('REPLACE(LOWER(TRIM(name)), " ", "") = ? OR REPLACE(LOWER(TRIM(name)), "_", "") = ?', [$normalizedStaffRole, $normalizedStaffRole])->first();
        if ($role) {
            return $role;
        }
        
        // Additional fallback: try matching display_name
        $role = Role::whereRaw('LOWER(TRIM(display_name)) = ?', [strtolower($staffRole)])->first();
        if ($role) {
            return $role;
        }
        
        // Last resort: try partial match on display_name
        $role = Role::whereRaw('LOWER(display_name) LIKE ?', ['%' . strtolower($staffRole) . '%'])->first();
        
        return $role;
    }

    /**
     * Check if staff is super admin
     */
    public function isSuperAdmin(): bool
    {
        if (!$this->role) {
            return false;
        }
        // Check for both 'super_admin' and 'Super Admin' (with space) for compatibility
        $normalizedRole = strtolower(trim($this->role));
        return in_array($normalizedRole, ['super_admin', 'super admin']);
    }

    /**
     * Check if staff is manager
     */
    public function isManager(): bool
    {
        if (!$this->role) {
            return false;
        }
        // Case-insensitive check for manager role
        $normalizedRole = strtolower(trim($this->role));
        return $normalizedRole === 'manager';
    }

    /**
     * Check if staff is reception
     */
    public function isReception(): bool
    {
        return $this->role === 'reception';
    }

    /**
     * Check if staff is waiter
     */
    public function isWaiter(): bool
    {
        $normalizedRole = strtolower(trim($this->role ?? ''));
        return $normalizedRole === 'waiter';
    }

    /**
     * Get department name based on role
     * Maps roles to departments: housekeeper -> Housekeeping, reception -> Reception, 
     * bar_keeper -> Bar, head_chef -> Food
     */
    public function getDepartmentName(): string
    {
        // If department is explicitly set, use it
        if ($this->department) {
            return ucfirst($this->department);
        }
        
        // Otherwise, map role to department
        $normalizedRole = strtolower(trim($this->role ?? ''));
        
        $roleToDepartment = [
            'housekeeper' => 'Housekeeping',
            'reception' => 'Reception',
            'bar_keeper' => 'Bar',
            'bar keeper' => 'Bar',
            'bartender' => 'Bar',
            'head_chef' => 'Food',
            'head chef' => 'Food',
            'chef' => 'Food',
            'manager' => 'Management',
            'super_admin' => 'Management',
            'super admin' => 'Management',
        ];
        
        // Check exact match first
        if (isset($roleToDepartment[$normalizedRole])) {
            return $roleToDepartment[$normalizedRole];
        }
        
        // Check partial matches
        foreach ($roleToDepartment as $role => $dept) {
            if (strpos($normalizedRole, $role) !== false || strpos($role, $normalizedRole) !== false) {
                return $dept;
            }
        }
        
        // Default fallback
        return ucfirst($this->role ?? 'Staff');
    }

    /**
     * Check if staff has a specific permission
     */
    public function hasPermission($permissionName): bool
    {
        if (!$permissionName) {
            return false;
        }
        
        // Super admin always has all permissions (bypass role permission check)
        if ($this->isSuperAdmin()) {
            return true;
        }

        // For other roles, check through role system
        $role = $this->roleModel();
        if (!$role) {
            // Log for debugging if role not found
            if (config('app.debug')) {
                \Log::warning("Staff permission check failed: Role model not found for staff ID {$this->id} with role '{$this->role}'");
            }
            return false;
        }
        
        $hasPermission = $role->hasPermission($permissionName);
        
        // Log for debugging
        if (config('app.debug')) {
            \Log::debug("Staff permission check: Staff ID {$this->id} ({$this->name}), Role: '{$this->role}' (Role ID: {$role->id}), Permission: '{$permissionName}', Result: " . ($hasPermission ? 'YES' : 'NO'));
        }
        
        return $hasPermission;
    }

    /**
     * Get activity logs for this staff
     */
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class, 'user_id')->where('user_type', 'staff');
    }
}
