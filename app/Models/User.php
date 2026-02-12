<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'profile_photo',
        'room_preferences',
        'dietary_restrictions',
        'special_occasions',
        'is_active',
        'session_token',
        'last_session_id',
    ];
    
    protected $casts = [
        'room_preferences' => 'array',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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
        ];
    }

    /**
     * Get all bookings for this user (by email)
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'guest_email', 'email');
    }

    /**
     * Get activity logs for this user
     */
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * Get the role model
     */
    public function roleModel()
    {
        if (!$this->role) {
            return null;
        }
        return Role::where('name', $this->role)->first();
    }

    /**
     * Check if user is super admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /**
     * Check if user is manager
     */
    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    /**
     * Check if user is reception
     */
    public function isReception(): bool
    {
        return $this->role === 'reception';
    }

    /**
     * Check if user is customer
     */
    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    /**
     * Check if user has a specific permission
     */
    public function hasPermission($permissionName): bool
    {
        if ($this->isSuperAdmin()) {
            return true; // Super admin has all permissions
        }

        $role = $this->roleModel();
        if (!$role) {
            return false;
        }
        
        return $role->hasPermission($permissionName);
    }

    /**
     * Check if user can manage other users
     */
    public function canManageUsers(): bool
    {
        return $this->isSuperAdmin();
    }

    /**
     * Check if user can manage roles
     */
    public function canManageRoles(): bool
    {
        return $this->isSuperAdmin();
    }

    /**
     * Get the department name based on user role
     * Used for Purchase Requests and Reports
     */
    public function getDepartmentName(): string
    {
        $role = $this->role ?? 'customer';
        
        $map = [
            'bar_keeper' => 'Bar',
            'housekeeper' => 'Housekeeping',
            'reception' => 'Reception',
            'front_office' => 'Reception',
            'manager' => 'Management', // Managers often oversee all, but keeping separate is fine
            'admin' => 'Management',
            'super_admin' => 'Management',
            'head_chef' => 'Chef', 
            'chef' => 'Chef',
            'kitchen_master' => 'Chef',
            'maintenance' => 'Maintenance',
        ];

        return $map[$role] ?? ucfirst(str_replace('_', ' ', $role));
    }
}
