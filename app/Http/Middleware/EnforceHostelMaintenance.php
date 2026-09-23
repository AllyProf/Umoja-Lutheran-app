<?php

namespace App\Http\Middleware;

use App\Support\HostelMaintenance;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnforceHostelMaintenance
{
    public function handle(Request $request, Closure $next): Response
    {
        $data = HostelMaintenance::data();
        if (empty($data['enabled'])) {
            return $next($request);
        }

        $user = Auth::guard('staff')->user();
        if ($user && $user->isSuperAdmin()) {
            return $next($request);
        }

        $path = trim($request->path(), '/');
        $onLogin = $path === 'login';
        if ($onLogin || $path === 'logout' || str_ends_with($path, '/logout')) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => HostelMaintenance::message()], 503)
                ->header('Retry-After', '60');
        }

        if ($user || Auth::guard('guest')->user()) {
            return HostelMaintenance::pageResponse();
        }

        return redirect()->route('login');
    }
}
