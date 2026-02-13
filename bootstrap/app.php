<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'single.session' => \App\Http\Middleware\CheckSingleSession::class,
            'super_admin' => \App\Http\Middleware\SuperAdminMiddleware::class,
            'check.auth' => \App\Http\Middleware\CheckAuth::class,
            'permission' => \App\Http\Middleware\CheckPermission::class,
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);
        
        // Trust proxies for proper HTTPS detection behind cPanel/proxy
        // This ensures Laravel correctly detects HTTPS when behind cPanel's proxy/load balancer
        $middleware->web(prepend: [
            \App\Http\Middleware\TrustProxies::class,
        ]);
        
        // Add CSRF debugging middleware (only when debug is enabled via env)
        // Use env() directly since config() isn't available yet at this bootstrap stage
        if (env('APP_DEBUG', false) || env('ENABLE_CSRF_DEBUG', false)) {
            $middleware->web(append: [
                \App\Http\Middleware\CsrfDebugMiddleware::class,
            ]);
        }
        
        // Add single session check to web middleware group (runs on all web requests)
        $middleware->web(append: [
            \App\Http\Middleware\CheckSingleSession::class,
        ]);
        
        // Trust all proxies for correct HTTPS/Session handling behind cPanel/proxy
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Log CSRF token mismatch exceptions with full details
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, $request) {
            \Log::channel('daily')->error('CSRF Token Mismatch Exception', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'route' => $request->route()?->getName(),
                'request_token' => $request->input('_token') ? substr($request->input('_token'), 0, 20) . '...' : 'MISSING',
                'session_token' => $request->session()->token() ? substr($request->session()->token(), 0, 20) . '...' : 'MISSING',
                'session_id' => $request->session()->getId(),
                'app_url' => config('app.url'),
                'request_scheme' => $request->getScheme(),
                'session_domain' => config('session.domain'),
                'session_secure' => config('session.secure'),
                'user_agent' => $request->userAgent(),
                'ip' => $request->ip(),
            ]);
        });
    })->create();
