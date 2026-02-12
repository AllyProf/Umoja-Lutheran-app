<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Checking for Recipe #24 ===\n\n";

$recipe = \App\Models\Recipe::find(24);

if ($recipe) {
    echo "Recipe #24 exists!\n";
    echo "Name: " . $recipe->name . "\n";
    echo "Price: " . $recipe->selling_price . "\n";
} else {
    echo "No recipe with ID 24\n";
}

echo "\n=== Checking Product #24 ===\n\n";

$product = \App\Models\Product::find(24);

if ($product) {
    echo "Product #24 exists!\n";
    echo "Name: " . $product->name . "\n";
    echo "Category: " . $product->category . "\n";
}
