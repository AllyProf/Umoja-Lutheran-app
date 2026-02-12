<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Simulating Frontend Request ===\n\n";

// Mimic the item structure from your console log
$items = [
    [
        "cartId" => "d_67_pic",
        "id" => 24,
        "name" => "Coca (500 ml) (Bottle)",
        "price" => 20000,
        "qty" => 1,
        "isFood" => "false", // Simulate string "false" sometimes sent by JS frameworks
        "variantId" => 67,
        "productId" => 24,
        "method" => "pic",
        "note" => ""
    ]
];

echo "Input Item isFood: " . $items[0]['isFood'] . "\n";

// Replicate Controller Logic
$isFoodRaw = $items[0]['isFood'];
$isFood = isset($items[0]['isFood']) && filter_var($items[0]['isFood'], FILTER_VALIDATE_BOOLEAN);

echo "filter_var result: " . ($isFood ? 'TRUE' : 'FALSE') . "\n";

if ($isFood) {
    echo "Logic: DETECTED AS FOOD (ID 4)\n";
} elseif (isset($items[0]['variantId']) && $items[0]['variantId']) {
    echo "Logic: DETECTED AS DRINK (ID 3)\n";
} else {
    echo "Logic: DETECTED AS UNKNOWN\n";
}

echo "\n=== Doing it again with boolean false ===\n";
$items[0]['isFood'] = false;
$isFood = isset($items[0]['isFood']) && filter_var($items[0]['isFood'], FILTER_VALIDATE_BOOLEAN);
echo "filter_var result: " . ($isFood ? 'TRUE' : 'FALSE') . "\n";

if ($isFood) {
    echo "Logic: DETECTED AS FOOD (ID 4)\n";
} elseif (isset($items[0]['variantId']) && $items[0]['variantId']) {
    echo "Logic: DETECTED AS DRINK (ID 3)\n";
} else {
    echo "Logic: DETECTED AS UNKNOWN\n";
}
