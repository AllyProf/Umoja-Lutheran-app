<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

class Role extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'display_name',
        'description',
        'is_system',
    ];

    protected $casts = [
        'is_system' => 'boolean',
    ];

    /** Roles that belong to guests, not Staff accounts. */
    public const GUEST_ROLE_NAMES = ['guest', 'customer'];

    /**
     * Roles that can be assigned when creating/editing staff or guests.
     */
    public static function assignableRoles()
    {
        // Hide legacy/duplicate role keys so Create User does not list the same job twice
        // waiter + bar_keeper are replaced by counter for this hostel
        $hiddenDuplicates = ['assistant_manager', 'waiter', 'bar_keeper', 'bartender'];

        return static::query()
            ->whereNotIn('name', $hiddenDuplicates)
            ->orderByRaw("CASE WHEN name = 'super_admin' THEN 0 WHEN name = 'manager' THEN 1 WHEN name = 'restaurant_manager' THEN 2 WHEN name = 'counter' THEN 3 ELSE 4 END")
            ->orderBy('display_name')
            ->orderBy('name')
            ->get()
            ->map(function ($role) {
                if ($role->name === 'counter') {
                    $role->display_name = 'Counter';
                }
                return $role;
            });
    }

    /**
     * Whether this role name should create a Staff (not Guest) account.
     */
    public static function isStaffRoleName(?string $roleName): bool
    {
        if (!$roleName) {
            return false;
        }

        $normalized = strtolower(str_replace([' ', '-'], '_', trim($roleName)));

        return !in_array($normalized, self::GUEST_ROLE_NAMES, true);
    }

    /**
     * Get staff members with this role
     */
    public function staff()
    {
        return $this->hasMany(Staff::class, 'role', 'name');
    }

    /**
     * Get users (guests) with this role (for backward compatibility)
     */
    public function users()
    {
        return $this->hasMany(User::class, 'role', 'name');
    }

    /**
     * Get all users (staff + guests) with this role
     */
    public function allUsers()
    {
        // This is a helper method that combines staff and users
        // Note: This returns a collection, not a relationship
        $staff = $this->staff;
        $users = $this->users;
        return $staff->merge($users);
    }

    /**
     * Get permissions for this role
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permission', 'role_id', 'permission_id');
    }

    /**
     * Check if role has a permission
     */
    public function hasPermission($permissionName): bool
    {
        if (!$permissionName) {
            return false;
        }
        
        // Direct database query to ensure fresh data (bypasses relationship cache)
        $exists = DB::table('role_permission')
            ->join('permissions', 'role_permission.permission_id', '=', 'permissions.id')
            ->where('role_permission.role_id', $this->id)
            ->where('permissions.name', $permissionName)
            ->exists();
        
        // Log for debugging (can be removed in production)
        if (config('app.debug')) {
            \Log::debug("Permission check: Role '{$this->name}' (ID: {$this->id}) has permission '{$permissionName}': " . ($exists ? 'YES' : 'NO'));
        }
        
        return $exists;
    }

    /**
     * Get all permission names for this role
     */
    public function getPermissionNames(): array
    {
        return $this->permissions()->pluck('name')->toArray();
    }

    /**
     * Assign permission to role
     */
    public function assignPermission($permission)
    {
        if (is_string($permission)) {
            $permission = Permission::where('name', $permission)->first();
        }
        
        if ($permission && !$this->hasPermission($permission->name)) {
            $this->permissions()->attach($permission->id);
        }
    }

    /**
     * Remove permission from role
     */
    public function removePermission($permission)
    {
        if (is_string($permission)) {
            $permission = Permission::where('name', $permission)->first();
        }
        
        if ($permission) {
            $this->permissions()->detach($permission->id);
        }
    }
}
