<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$recipes = \App\Models\Recipe::get(['id', 'name', 'is_available'])->toArray();
foreach ($recipes as $r) {
    echo $r['name'] . "\n";
}
