<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$items = App\Models\ShoppingListItem::whereNull('product_variant_id')->whereNotNull('product_id')->get();
$count = 0;
foreach ($items as $item) {
    if ($item->product && $item->product->variants->first()) {
        $item->product_variant_id = $item->product->variants->first()->id;
        $item->save();
        $count++;
    }
}
echo "Fixed $count items.\n";
