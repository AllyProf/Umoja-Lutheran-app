<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$items = ['karanga', 'ufuta', 'fruit salad'];
foreach ($items as $name) {
    $count = \App\Models\Recipe::whereRaw("LOWER(name) = ?", [$name])->update(['category' => 'bites']);
    echo "Updated '{$name}': {$count} rows -> bites\n";
}
echo "Done!\n";
