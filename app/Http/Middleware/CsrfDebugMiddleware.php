<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CsrfDebugMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only log for POST/PUT/PATCH/DELETE requests (CSRF protected)
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $this->logCsrfDetails($request);
        }

        try {
            $response = $next($request);
            
            // Log if CSRF validation failed (419 status)
            if ($response->getStatusCode() === 419) {
                $this->logCsrfFailure($request, $response);
            }
            
            return $response;
        } catch (\Illuminate\Session\TokenMismatchException $e) {
            $this->logCsrfException($request, $e);
            throw $e;
        }
    }

    /**
     * Log CSRF token details before validation
     */
    private function logCsrfDetails(Request $request): void
    {
        $session = $request->session();
        
        Log::channel('daily')->info('CSRF Debug - Request Details', [
            'timestamp' => now()->toDateTimeString(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'route' => $request->route()?->getName(),
            'scheme' => $request->getScheme(),
            'host' => $request->getHost(),
            'ip' => $request->ip(),
            'user_agent' => substr($request->userAgent(), 0, 100),
            
            // CSRF Token Details
            'has_token_in_request' => $request->has('_token'),
            'token_from_request' => $request->has('_token') ? substr($request->input('_token'), 0, 20) . '...' : 'NOT FOUND',
            'token_from_header' => $request->header('X-CSRF-TOKEN') ? substr($request->header('X-CSRF-TOKEN'), 0, 20) . '...' : 'NOT FOUND',
            'token_from_xsrf_header' => $request->header('X-XSRF-TOKEN') ? substr($request->header('X-XSRF-TOKEN'), 0, 20) . '...' : 'NOT FOUND',
            
            // Session Details
            'session_id' => $session->getId(),
            'session_exists' => $session->isStarted(),
            'session_token' => $session->token() ? substr($session->token(), 0, 20) . '...' : 'NOT FOUND',
            
            // Configuration
            'app_url' => config('app.url'),
            'app_env' => config('app.env'),
            'session_driver' => config('session.driver'),
            'session_domain' => config('session.domain'),
            'session_secure' => config('session.secure'),
            'session_same_site' => config('session.same_site'),
            'session_path' => config('session.path'),
            
            // Cookie Details
            'cookies' => $this->getCookieDetails($request),
        ]);
    }

    /**
     * Log CSRF validation failure (419 error)
     */
    private function logCsrfFailure(Request $request, Response $response): void
    {
        $session = $request->session();
        $requestToken = $request->input('_token') ?: $request->header('X-CSRF-TOKEN') ?: $request->header('X-XSRF-TOKEN');
        $sessionToken = $session->token();
        
        Log::channel('daily')->error('CSRF Validation FAILED - 419 Page Expired', [
            'timestamp' => now()->toDateTimeString(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'route' => $request->route()?->getName(),
            
            // Token Comparison
            'request_token' => $requestToken ? substr($requestToken, 0, 40) . '...' : 'MISSING',
            'session_token' => $sessionToken ? substr($sessionToken, 0, 40) . '...' : 'MISSING',
            'tokens_match' => $requestToken && $sessionToken && hash_equals($sessionToken, $requestToken),
            
            // Session Status
            'session_id' => $session->getId(),
            'session_started' => $session->isStarted(),
            'session_expired' => !$session->isStarted(),
            
            // Protocol/URL Issues
            'request_scheme' => $request->getScheme(),
            'app_url_scheme' => config('app.url') ? parse_url(config('app.url'), PHP_URL_SCHEME) : 'NOT SET',
            'scheme_mismatch' => $request->getScheme() !== parse_url(config('app.url'), PHP_URL_SCHEME),
            
            // Domain Issues
            'request_host' => $request->getHost(),
            'session_domain' => config('session.domain'),
            'domain_mismatch' => $this->checkDomainMismatch($request),
            
            // Cookie Issues
            'session_cookie_present' => $request->hasCookie(config('session.cookie')),
            'session_cookie_name' => config('session.cookie'),
            'cookies_received' => array_keys($request->cookies->all()),
            
            // Possible Causes
            'possible_causes' => $this->diagnoseCsrfFailure($request, $requestToken, $sessionToken),
        ]);
    }

    /**
     * Log CSRF exception
     */
    private function logCsrfException(Request $request, \Exception $e): void
    {
        Log::channel('daily')->error('CSRF Exception Thrown', [
            'timestamp' => now()->toDateTimeString(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'exception' => get_class($e),
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);
    }

    /**
     * Get cookie details from request
     */
    private function getCookieDetails(Request $request): array
    {
        $cookies = [];
        $sessionCookieName = config('session.cookie');
        
        foreach ($request->cookies->all() as $name => $value) {
            $cookies[$name] = [
                'present' => true,
                'is_session_cookie' => $name === $sessionCookieName,
                'value_length' => strlen($value),
                'value_preview' => substr($value, 0, 20) . '...',
            ];
        }
        
        return $cookies;
    }

    /**
     * Check for domain mismatch
     */
    private function checkDomainMismatch(Request $request): bool
    {
        $sessionDomain = config('session.domain');
        $requestHost = $request->getHost();
        
        if (!$sessionDomain) {
            return false; // No domain set, so no mismatch
        }
        
        // Remove leading dot if present
        $sessionDomain = ltrim($sessionDomain, '.');
        
        // Check if request host matches session domain
        return $requestHost !== $sessionDomain && !str_ends_with($requestHost, '.' . $sessionDomain);
    }

    /**
     * Diagnose possible causes of CSRF failure
     */
    private function diagnoseCsrfFailure(Request $request, ?string $requestToken, ?string $sessionToken): array
    {
        $causes = [];
        
        // No token in request
        if (!$requestToken) {
            $causes[] = 'CSRF token missing from request (check form has @csrf or meta tag)';
        }
        
        // No session token
        if (!$sessionToken) {
            $causes[] = 'Session token not found - session may have expired or not started';
        }
        
        // Tokens don't match
        if ($requestToken && $sessionToken && !hash_equals($sessionToken, $requestToken)) {
            $causes[] = 'CSRF tokens do not match - possible session hijacking or multiple tabs';
        }
        
        // Protocol mismatch
        $appUrlScheme = config('app.url') ? parse_url(config('app.url'), PHP_URL_SCHEME) : null;
        if ($appUrlScheme && $request->getScheme() !== $appUrlScheme) {
            $causes[] = "Protocol mismatch: APP_URL uses {$appUrlScheme} but request is {$request->getScheme()}";
        }
        
        // Session cookie not present
        if (!$request->hasCookie(config('session.cookie'))) {
            $causes[] = 'Session cookie not found - cookies may be blocked or domain/path incorrect';
        }
        
        // Session secure cookie mismatch
        $sessionSecure = config('session.secure');
        $isSecure = $request->isSecure();
        if ($sessionSecure !== null && $sessionSecure !== $isSecure) {
            $causes[] = "SESSION_SECURE_COOKIE is " . ($sessionSecure ? 'true' : 'false') . " but request is " . ($isSecure ? 'HTTPS' : 'HTTP');
        }
        
        // Domain mismatch
        if ($this->checkDomainMismatch($request)) {
            $causes[] = 'Session domain does not match request host';
        }
        
        // Session expired
        if (!$request->session()->isStarted()) {
            $causes[] = 'Session not started - may have expired or been cleared';
        }
        
        return $causes;
    }
}


