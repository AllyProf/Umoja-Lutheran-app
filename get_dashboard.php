<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\Staff::where('role', 'bar_keeper')->first();
Auth::guard('staff')->login($user);

$request = Illuminate\Http\Request::create('/bar-keeper/dashboard', 'GET');
$response = app()->handle($request);
file_put_contents(__DIR__ . '/rendered_dashboard.html', $response->getContent());
echo "Saved to rendered_dashboard.html\n";
