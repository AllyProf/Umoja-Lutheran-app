<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$bases = ['Chipsi', 'Ndizi', 'Tambi'];
$sides = [
    'nyama' => 7000,
    'kuku' => 9000,
    'samaki' => 15000,
    'kuku kienyeji' => 12000,
    'vegetable' => 4000
];

$count = 0;
foreach ($bases as $base) {
    foreach ($sides as $side => $price) {
        $name = $base . ' ' . $side;
        $recipe = \App\Models\Recipe::firstOrCreate(
            ['name' => $name],
            [
                'category' => 'food',
                'selling_price' => $price,
                'is_available' => true,
                'is_active' => true,
                'food_type' => 'food',
                'image' => null
            ]
        );

        // Even if it already existed, make sure it's available
        $recipe->update([
            'is_available' => true,
            'is_active' => true,
            'selling_price' => $price
        ]);

        $count++;
    }
}
echo "Added/Verified {$count} food variations for Chipsi, Ndizi, and Tambi.\n";
