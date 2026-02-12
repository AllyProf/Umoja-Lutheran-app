<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class BypassMaintenanceMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If maintenance mode is active and user is authenticated as super admin, allow access
        if (app()->isDownForMaintenance()) {
            $user = Auth::guard('staff')->user() ?? Auth::guard('guest')->user();
            if ($user && $user instanceof \App\Models\Staff && $user->isSuperAdmin()) {
                // Super admin can bypass maintenance mode
                return $next($request);
            }
        }

        return $next($request);
    }
}

