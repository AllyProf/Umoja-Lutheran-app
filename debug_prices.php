<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ProductVariant;

$variants = ProductVariant::with('product')
    ->where('variant_name', 'like', '%take away%')
    ->orWhere('variant_name', 'like', '%portable%')
    ->get();

foreach ($variants as $v) {
    echo "ID: {$v->id} | Product: {$v->product->name} | Variant: {$v->variant_name} | Price: {$v->selling_price_per_pic} | Packaging: {$v->packaging} | Ratio: {$v->items_per_package}\n";
}
