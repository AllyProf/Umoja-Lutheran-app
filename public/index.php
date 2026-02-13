<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Define Base Path (IMPORTANT FIX)
|--------------------------------------------------------------------------
| This makes Laravel root = one level above public/
|--------------------------------------------------------------------------
*/

$basePath = dirname(__DIR__);

/*
|--------------------------------------------------------------------------
| Maintenance Mode Check (Custom – Safe)
|--------------------------------------------------------------------------
*/

$maintenance = $basePath . '/storage/framework/maintenance.php';
$down = $basePath . '/storage/framework/down';

if (file_exists($maintenance) || file_exists($down)) {

    $requestUri  = $_SERVER['REQUEST_URI'] ?? '/';
    $requestPath = parse_url($requestUri, PHP_URL_PATH) ?? '/';
    $path = strtolower(trim($requestPath, '/'));

    $allowed =
        str_contains($path, 'login') ||
        str_contains($path, 'auth') ||
        str_contains($path, 'dashboard') ||
        str_contains($path, 'super-admin') ||
        str_contains($path, 'super_admin');

    $hasSecret = isset($_GET['secret']) || isset($_GET['bypass']);

    if (!$allowed && !$hasSecret) {

        if (file_exists($maintenance)) {
            require $maintenance;
            exit;
        }

        if (file_exists($down)) {
            $data = json_decode(file_get_contents($down), true);

            http_response_code(503);
            header('Retry-After: ' . ($data['retry'] ?? 60));
            header('Content-Type: text/html; charset=utf-8');

            echo '<h1>Service Unavailable</h1>';
            echo '<p>' . htmlspecialchars($data['message'] ?? 'The application is under maintenance.') . '</p>';
            exit;
        }
    }
}

/*
|--------------------------------------------------------------------------
| Bootstrap Laravel (ONLY ONCE)
|--------------------------------------------------------------------------
*/

require $basePath . '/vendor/autoload.php';

$app = require_once $basePath . '/bootstrap/app.php';

$app->handleRequest(Request::capture());
