<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles  Roles allowed to access this route
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = auth()->guard('staff')->user() ?? auth()->guard('guest')->user();
        
        if (!$user) {
            return redirect()->route('login')->withErrors(['email' => 'Please login to access this page.']);
        }
        
        // Get user's actual role
        $userRole = null;
        
        if ($user instanceof \App\Models\Staff) {
            $rawRole = $user->role ?? '';
            $normalizedRole = strtolower(str_replace([' ', '_'], '', trim($rawRole)));
            $rawRoleLower = strtolower(trim($rawRole));
            
            // Map to standard role names
            if ($normalizedRole === 'superadmin' || $rawRoleLower === 'super_admin' || $rawRoleLower === 'super admin') {
                $userRole = 'super_admin';
            } elseif ($normalizedRole === 'manager' || $rawRoleLower === 'manager') {
                $userRole = 'manager';
            } elseif ($normalizedRole === 'reception' || $rawRoleLower === 'reception') {
                $userRole = 'reception';
            } elseif ($normalizedRole === 'barkeeper' || $normalizedRole === 'barkeepr' || $rawRoleLower === 'bar_keeper' || $rawRoleLower === 'bar keeper' || $rawRoleLower === 'bartender') {
                $userRole = 'bar_keeper';
            } elseif ($normalizedRole === 'headchef' || $rawRoleLower === 'head_chef') {
                $userRole = 'head_chef';
            } elseif ($normalizedRole === 'housekeeper' || $rawRoleLower === 'housekeeper') {
                $userRole = 'housekeeper';
            } elseif ($normalizedRole === 'waiter' || $rawRoleLower === 'waiter') {
                $userRole = 'waiter';
            }
        } elseif ($user instanceof \App\Models\Guest) {
            $userRole = 'customer'; // Guests are mapped to 'customer' role
        }
        
        // If user role could not be determined, redirect to login (user session may be corrupted)
        if (!$userRole) {
            \Log::warning('CheckRole middleware: User role could not be determined, redirecting to login', [
                'user_id' => $user->id ?? null,
                'user_type' => get_class($user),
                'raw_role' => $user instanceof \App\Models\Staff ? $user->role : null,
            ]);
            return redirect()->route('login')->withErrors(['email' => 'Your session is invalid. Please login again.']);
        }
        
        // Normalize role names in the required roles array (handle 'guest' -> 'customer')
        $normalizedRoles = array_map(function($role) {
            return $role === 'guest' ? 'customer' : $role;
        }, $roles);
        
        // Check if user has one of the required roles
        // Super Admin always has access to everything
        if ($userRole === 'super_admin') {
            return $next($request);
        }

        if (!in_array($userRole, $normalizedRoles)) {
            // Log for debugging
            \Log::warning('CheckRole middleware blocked access - insufficient permissions', [
                'user_id' => $user->id ?? null,
                'user_type' => get_class($user),
                'raw_role' => $user instanceof \App\Models\Staff ? $user->role : null,
                'normalized_user_role' => $userRole,
                'required_roles' => $normalizedRoles,
                'route' => $request->route()?->getName(),
                'url' => $request->url(),
            ]);
            abort(403, 'Access denied. You do not have permission to access this page.');
        }
        
        return $next($request);
    }
}

