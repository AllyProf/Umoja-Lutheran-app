<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use App\Models\ProductVariant;

$products = Product::where('name', 'like', '%take away%')
    ->orWhere('name', 'like', '%portable%')
    ->get();

foreach ($products as $p) {
    echo "Product ID: {$p->id} | Name: {$p->name} | Category: {$p->category}\n";
    foreach ($p->variants as $v) {
        echo "  - Variant ID: {$v->id} | Name: {$v->variant_name} | Price: {$v->selling_price_per_pic} | Packaging: {$v->packaging} | Ratio: {$v->items_per_package}\n";
    }
}

$variants = ProductVariant::where('variant_name', 'like', '%take away%')
    ->orWhere('variant_name', 'like', '%portable%')
    ->get();

foreach ($variants as $v) {
    echo "Variant ID: {$v->id} | Name: {$v->variant_name} | Price: {$v->selling_price_per_pic} | Packaging: {$v->packaging} | Ratio: {$v->items_per_package}\n";
}
