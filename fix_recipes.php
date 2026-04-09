<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// 1. Remove duplicate hot lemon - keep only the first one (lowest id)
$hotLemons = \App\Models\Recipe::whereRaw("LOWER(name) LIKE '%hot lemon%'")->orderBy('id')->get();
echo "Found " . $hotLemons->count() . " hot lemon entries:\n";
foreach ($hotLemons as $r)
    echo "  ID {$r->id}: {$r->name}\n";

if ($hotLemons->count() > 1) {
    // Keep first, delete the rest
    $hotLemons->skip(1)->each(function ($r) {
        echo "Deleting duplicate ID {$r->id}: {$r->name}\n";
        $r->delete();
    });
}

// 2. Rename "black coffee with milk" -> "Coffee with Milk"
$blackCoffee = \App\Models\Recipe::whereRaw("LOWER(name) LIKE '%black coffee with milk%'")->get();
echo "\nFound " . $blackCoffee->count() . " 'black coffee with milk' entries:\n";
foreach ($blackCoffee as $r) {
    echo "  Renaming ID {$r->id}: '{$r->name}' -> 'Coffee with Milk'\n";
    $r->update(['name' => 'Coffee with Milk']);
}

echo "\nDone!\n";
