<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->guard('staff')->user() ?? auth()->guard('guest')->user();
        
        if (!$user) {
            return redirect()->route('super_admin.login');
        }

        // Check if user is staff and super admin
        if (!($user instanceof \App\Models\Staff && $user->isSuperAdmin())) {
            abort(403, 'Access denied. Super Administrator privileges required.');
        }

        return $next($request);
    }
}
