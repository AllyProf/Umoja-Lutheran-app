<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

\App\Models\Recipe::whereRaw("LOWER(name) = 'karanga'")->update(['category' => 'traditional']);
echo "Karanga moved to Traditional tab.\n";
