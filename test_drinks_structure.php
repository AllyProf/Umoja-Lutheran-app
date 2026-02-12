<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Checking Drink Data Structure ===\n\n";

$barCategories = ['drinks', 'alcoholic_beverage', 'non_alcoholic_beverage', 'water', 'juices', 'energy_drinks', 'spirits', 'wines', 'cocktails', 'hot_beverages', 'beers', 'liquor', 'whiskey'];

$allTransfers = \App\Models\StockTransfer::where('status', 'completed')->get();
$allSales = \App\Models\ServiceRequest::where('status', 'completed')
    ->whereHas('service', function($q) use ($barCategories) {
        $q->whereIn('category', $barCategories);
    })->get();

$stockLevels = [];
foreach ($allTransfers as $t) {
    $vid = $t->product_variant_id;
    if (!isset($stockLevels[$vid])) $stockLevels[$vid] = 0;
    
    $itemsPerPkg = $t->productVariant->items_per_package ?? 1;
    $pics = ($t->quantity_unit === 'packages') ? ($t->quantity_transferred * $itemsPerPkg) : $t->quantity_transferred;
    $stockLevels[$vid] += $pics;
}

foreach ($allSales as $s) {
    if ($s->service_id == 3 && $s->product_variant_id) {
        $vid = $s->product_variant_id;
        if (isset($stockLevels[$vid])) {
            $variant = \App\Models\ProductVariant::find($vid);
            if ($variant && $s->selling_method === 'glass') {
                $stockLevels[$vid] -= ($s->quantity / ($variant->servings_per_pic ?: 1));
            } else {
                $stockLevels[$vid] -= $s->quantity;
            }
        }
    }
}

$products = \App\Models\Product::whereIn('category', $barCategories)
    ->with(['variants'])
    ->get();

$drinks = [];
foreach ($products as $product) {
    foreach ($product->variants as $variant) {
        $options = [];
        if ($variant->can_sell_as_serving && $variant->selling_price_per_serving > 0) {
            $options[] = ['type' => 'Glass', 'method' => 'glass', 'price' => (float)$variant->selling_price_per_serving];
        }
        if ($variant->can_sell_as_pic && $variant->selling_price_per_pic > 0) {
            $options[] = ['type' => 'Bottle', 'method' => 'pic', 'price' => (float)$variant->selling_price_per_pic];
        }

        if (!empty($options)) {
            $currentStock = $stockLevels[$variant->id] ?? 0;
            $drinks[] = (object)[
                'id' => $product->id,
                'variant_id' => $variant->id,
                'name' => ($variant->variant_name ?: $product->name) . ($variant->measurement ? ' (' . $variant->measurement . ')' : ''),
                'category' => $product->category,
                'image' => $variant->image ?: $product->image,
                'options' => $options,
                'in_stock' => $currentStock > 0.01,
                'current_stock' => round($currentStock, 2),
                'servings_per_pic' => $variant->servings_per_pic > 0 ? (float)$variant->servings_per_pic : 1
            ];
        }
    }
}

echo "Total drinks found: " . count($drinks) . "\n\n";

if (count($drinks) > 0) {
    echo "First drink structure:\n";
    print_r($drinks[0]);
    
    echo "\n\nJSON encoded:\n";
    echo json_encode($drinks[0]);
}
