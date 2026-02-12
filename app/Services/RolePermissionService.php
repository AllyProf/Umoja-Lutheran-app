<?php

namespace App\Services;

use App\Models\Staff;
use App\Models\Guest;

class RolePermissionService
{
    /**
     * Get user's normalized role
     */
    public static function getUserRole($user): ?string
    {
        if (!$user) {
            return null;
        }
        
        if ($user instanceof Staff) {
            $rawRole = $user->role ?? '';
            $normalizedRole = strtolower(str_replace([' ', '_'], '', trim($rawRole)));
            
            if ($normalizedRole === 'superadmin' || $rawRole === 'super_admin' || strtolower($rawRole) === 'super admin') {
                return 'super_admin';
            } elseif ($normalizedRole === 'manager' || $rawRole === 'manager') {
                return 'manager';
            } elseif ($normalizedRole === 'reception' || $rawRole === 'reception') {
                return 'reception';
            } elseif ($normalizedRole === 'headchef' || $rawRole === 'head_chef') {
                return 'head_chef';
            }
        } elseif ($user instanceof Guest) {
            return 'customer';
        }
        
        return null;
    }
    
    /**
     * Check if user has a specific role
     */
    public static function hasRole($user, string $role): bool
    {
        $userRole = self::getUserRole($user);
        return $userRole === $role;
    }
    
    /**
     * Check if user has any of the specified roles
     */
    public static function hasAnyRole($user, array $roles): bool
    {
        $userRole = self::getUserRole($user);
        return $userRole && in_array($userRole, $roles);
    }
    
    /**
     * Check if user has permission (with role check)
     */
    public static function hasPermission($user, string $permissionName): bool
    {
        if (!$user) {
            if (config('app.debug')) {
                \Log::debug("RolePermissionService::hasPermission: User is null for permission '{$permissionName}'");
            }
            return false;
        }
        
        if (!$permissionName) {
            if (config('app.debug')) {
                \Log::debug("RolePermissionService::hasPermission: Permission name is empty for user ID " . ($user->id ?? 'unknown'));
            }
            return false;
        }
        
        // Super admin always has all permissions
        if ($user instanceof Staff && $user->isSuperAdmin()) {
            return true;
        }
        
        // Guest model doesn't have hasPermission method
        if ($user instanceof Guest) {
            if (config('app.debug')) {
                \Log::debug("RolePermissionService::hasPermission: Guest user cannot have permissions");
            }
            return false;
        }
        
        // Check permission through role
        if (method_exists($user, 'hasPermission')) {
            $result = $user->hasPermission($permissionName);
            if (config('app.debug')) {
                \Log::debug("RolePermissionService::hasPermission: User ID " . ($user->id ?? 'unknown') . ", Permission: '{$permissionName}', Result: " . ($result ? 'YES' : 'NO'));
            }
            return $result;
        }
        
        if (config('app.debug')) {
            \Log::warning("RolePermissionService::hasPermission: User object doesn't have hasPermission method. User type: " . get_class($user));
        }
        
        return false;
    }
    
    /**
     * Check if user can access a route based on role
     */
    public static function canAccessRoute($user, string $routeName): bool
    {
        $userRole = self::getUserRole($user);
        
        // Map routes to required roles
        $routeRoleMap = [
            'super_admin' => ['super_admin.dashboard', 'super_admin.users', 'super_admin.roles', 'super_admin.permissions'],
            'manager' => ['admin.dashboard', 'admin.users', 'admin.rooms', 'admin.bookings', 'admin.payments', 'admin.reports'],
            'reception' => ['reception.dashboard', 'reception.bookings', 'reception.reservations', 'reception.guests', 'reception.rooms'],
            'customer' => ['customer.dashboard', 'customer.bookings', 'customer.profile'],
        ];
        
        foreach ($routeRoleMap as $role => $routes) {
            if ($userRole === $role && in_array($routeName, $routes)) {
                return true;
            }
        }
        
        // Check if route starts with role prefix
        if ($userRole === 'super_admin' && str_starts_with($routeName, 'super_admin.')) {
            return true;
        }
        if ($userRole === 'manager' && str_starts_with($routeName, 'admin.')) {
            return true;
        }
        if ($userRole === 'reception' && str_starts_with($routeName, 'reception.')) {
            return true;
        }
        if ($userRole === 'customer' && str_starts_with($routeName, 'customer.')) {
            return true;
        }
        
        return false;
    }
}








