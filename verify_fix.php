<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ProductVariant;

$variant = ProductVariant::find(102); // Soda Take Away
$ratio = $variant->items_per_package;
$sellingPrice = $variant->selling_price_per_pic;
$qty = 1;
$unit = 'packages';

$totalItems = $qty * $ratio;
$revenue = $totalItems * $sellingPrice;

echo "Product: {$variant->product->name}\n";
echo "Quantity: $qty $unit\n";
echo "Ratio: $ratio\n";
echo "Selling Price: $sellingPrice\n";
echo "Calculated Revenue: $revenue\n";

if ($revenue == 36000) {
    echo "SUCCESS: Revenue calculation is correct (36,000 for 1 crate of 24 sodas).\n";
} else {
    echo "FAILURE: Revenue calculation is $revenue, expected 36,000.\n";
}
