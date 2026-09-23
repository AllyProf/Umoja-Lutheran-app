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
| Bootstrap Laravel (ONLY ONCE)
|--------------------------------------------------------------------------
| Hostel maintenance is applied after the session starts, so a super admin
| stays signed in and everyone else sees the saved message.
*/

require $basePath . '/vendor/autoload.php';

$app = require_once $basePath . '/bootstrap/app.php';

$app->handleRequest(Request::capture());
