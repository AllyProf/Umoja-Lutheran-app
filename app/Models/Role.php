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
