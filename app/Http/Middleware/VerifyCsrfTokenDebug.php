<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Support\Facades\Log;

class VerifyCsrfTokenDebug extends Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        // Log CSRF token validation details for debugging
        if ($request->isMethod('POST') && $request->routeIs('login.post')) {
            Log::info('CSRF Token Validation Debug', [
                'url' => $request->fullUrl(),
                'scheme' => $request->getScheme(),
                'app_url' => config('app.url'),
                'has_token' => $request->has('_token'),
                'session_id' => $request->session()->getId(),
                'session_domain' => config('session.domain'),
                'session_secure' => config('session.secure'),
            ]);
        }

        return parent::handle($request, $next);
    }
}


