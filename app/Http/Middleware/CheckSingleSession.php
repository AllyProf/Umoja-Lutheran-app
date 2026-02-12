<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSingleSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip session check for login, logout, and public routes
        $skipRoutes = ['login', 'login.post', 'logout', 'booking.index', 'booking.check-availability'];
        $routeName = $request->route()?->getName();
        
        // Always allow login and logout routes
        if (in_array($routeName, $skipRoutes)) {
            return $next($request);
        }
        
        // Check both staff and guest guards
        $user = Auth::guard('staff')->user() ?? Auth::guard('guest')->user();
        
        // Only check authenticated users
        if (!$user) {
            return $next($request);
        }
        
        $currentSessionId = $request->session()->getId();
        
        // Get fresh user data from database to check latest session info
        // Use fresh() to bypass any model caching
        $freshUser = null;
        if ($user instanceof \App\Models\Staff) {
            $freshUser = \App\Models\Staff::find($user->id);
        } elseif ($user instanceof \App\Models\Guest) {
            $freshUser = \App\Models\Guest::find($user->id);
        }
        
        if (!$freshUser) {
            Auth::guard('staff')->logout();
            Auth::guard('guest')->logout();
            $request->session()->invalidate();
            return redirect()->route('login');
        }
        
        // Check if user has a session token (means they're logged in)
        if ($freshUser->session_token && $freshUser->last_session_id) {
            // Check if current session matches the stored session ID
            if ($freshUser->last_session_id !== $currentSessionId) {
                // Session doesn't match - this means user logged in from another device
                // Clear the session data for this user
                $freshUser->session_token = null;
                $freshUser->last_session_id = null;
                $freshUser->save();
                
                Auth::guard('staff')->logout();
                Auth::guard('guest')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                return redirect()->route('login')->withErrors([
                    'email' => 'Your account has been logged in from another device. Please log in again.'
                ]);
            }
        }
        // If session_token doesn't exist yet, it means this is a new login
        // Don't log them out - let the login process set the token
        
        return $next($request);
    }
}
