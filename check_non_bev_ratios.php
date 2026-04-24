<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ProductVariant;

$beverageCategories = ['spirits', 'wines', 'non_alcoholic_beverage', 'alcoholic_beverage', 'energy_drinks', 'juices', 'water', 'hot_beverages', 'cocktails', 'drinks', 'beverage'];

$variants = ProductVariant::whereHas('product', function ($q) use ($beverageCategories) {
    $q->whereNotIn('category', $beverageCategories);
})->where('items_per_package', '>', 1)->get();

foreach ($variants as $v) {
    echo "ID: {$v->id} | Product: {$v->product->name} | Variant: {$v->variant_name} | Ratio: {$v->items_per_package} | Category: " . ($v->product->category ?? 'N/A') . "\n";
}
