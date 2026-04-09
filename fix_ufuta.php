<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// 1. Remove duplicate Ufuta - keep lowest id
$ufutas = \App\Models\Recipe::whereRaw("LOWER(name) = 'ufuta'")->orderBy('id')->get();
echo "Found " . $ufutas->count() . " Ufuta entries:\n";
foreach ($ufutas as $r)
    echo "  ID {$r->id}: {$r->name}\n";
if ($ufutas->count() > 1) {
    $ufutas->skip(1)->each(function ($r) {
        echo "Deleting duplicate ID {$r->id}\n";
        $r->delete();
    });
}

// 2. Add Karanga (1500 as per menu)
$karanga = \App\Models\Recipe::firstOrCreate(
    ['name' => 'Karanga'],
    ['category' => 'traditional', 'selling_price' => 1500, 'is_available' => true, 'is_active' => true, 'food_type' => 'food', 'image' => null]
);
$karanga->update(['is_available' => true, 'selling_price' => 1500]);
echo "\nKaranga: " . ($karanga->wasRecentlyCreated ? 'Added' : 'Already exists, updated') . "\n";

// 3. Add Fruit Salad (3000)
$fruitSalad = \App\Models\Recipe::firstOrCreate(
    ['name' => 'Fruit Salad'],
    ['category' => 'traditional', 'selling_price' => 3000, 'is_available' => true, 'is_active' => true, 'food_type' => 'food', 'image' => null]
);
$fruitSalad->update(['is_available' => true, 'selling_price' => 3000]);
echo "Fruit Salad: " . ($fruitSalad->wasRecentlyCreated ? 'Added' : 'Already exists, updated') . "\n";

echo "\nDone!\n";
