<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAuth
{
    /**
     * Handle an incoming request.
     * Checks authentication for both staff and guest guards
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check both staff and guest guards
        $user = auth()->guard('staff')->user() ?? auth()->guard('guest')->user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}
