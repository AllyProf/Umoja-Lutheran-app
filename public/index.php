<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
// Check for both old maintenance.php and new down.json formats
// For cPanel: public_html is at /home/primeland/public_html/
// Laravel app is at /home/primeland/PrimeLand_Hotel/
$maintenance = __DIR__.'/../storage/framework/maintenance.php';
$down = __DIR__.'/../storage/framework/down';

// If maintenance mode is active, check if user is trying to access super admin routes
// Super admins should be able to bypass maintenance mode
if (file_exists($maintenance) || file_exists($down)) {
    // Get the request URI and parse it properly
    $requestUri = $_SERVER['REQUEST_URI'] ?? $_SERVER['SCRIPT_NAME'] ?? '/';
    // Remove query string for path checking
    $requestPath = parse_url($requestUri, PHP_URL_PATH);
    if (!$requestPath) {
        $requestPath = $requestUri;
    }
    
    // Normalize the path - remove leading/trailing slashes
    $requestPath = trim($requestPath, '/');
    $requestUriLower = strtolower($requestUri);
    $requestPathLower = strtolower($requestPath);
    
    // Check if it's a super admin route, login route, or auth route
    // Allow any route that contains these keywords (case-insensitive)
    $isSuperAdminRoute = 
        strpos($requestPathLower, 'super-admin') !== false || 
        strpos($requestPathLower, 'super_admin') !== false ||
        strpos($requestUriLower, '/super-admin') !== false ||
        strpos($requestUriLower, '/super_admin') !== false ||
        strpos($requestPathLower, 'login') !== false ||
        strpos($requestPathLower, 'auth') !== false ||
        strpos($requestUriLower, '/login') !== false ||
        strpos($requestUriLower, '/auth') !== false ||
        // Also allow dashboard routes that might be accessed
        strpos($requestPathLower, 'dashboard') !== false ||
        strpos($requestUriLower, '/dashboard') !== false;
    
    // Also check for secret bypass parameter
    $hasSecret = isset($_GET['secret']) || isset($_GET['bypass']);
    
    // If it's a super admin route or has secret, allow through (will be checked later in middleware)
    if (!$isSuperAdminRoute && !$hasSecret) {
        // Check if it's the old maintenance.php format
        if (file_exists($maintenance)) {
            require $maintenance;
            exit;
        } else if (file_exists($down)) {
            // Handle new JSON format maintenance mode
            $downData = json_decode(file_get_contents($down), true);
            $message = $downData['message'] ?? 'The application is down for maintenance.';
            $retry = $downData['retry'] ?? 60;
            
            http_response_code(503);
            header('Retry-After: ' . $retry);
            header('Content-Type: text/html; charset=utf-8');
            
            echo '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Service Unavailable</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; background: #f5f5f5; }
        .container { text-align: center; padding: 2rem; background: white; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); max-width: 500px; }
        h1 { color: #333; margin-bottom: 1rem; }
        p { color: #666; line-height: 1.6; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Service Unavailable</h1>
        <p>' . htmlspecialchars($message) . '</p>
    </div>
</body>
</html>';
            exit;
        }
    }
}

// Register the Composer autoloader
// For cPanel: public_html is at /home/primeland/public_html/
// Laravel app is at /home/primeland/PrimeLand_Hotel/
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
