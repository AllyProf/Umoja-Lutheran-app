<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Checking Order #61 ===\n\n";

$order = \App\Models\ServiceRequest::find(61);

if ($order) {
    echo "Service ID: " . $order->service_id . "\n";
    echo "Product ID: " . ($order->product_id ?? 'NULL') . "\n";
    echo "Product Variant ID: " . ($order->product_variant_id ?? 'NULL') . "\n";
    echo "Selling Method: " . ($order->selling_method ?? 'NULL') . "\n";
    echo "Service Specific Data: " . json_encode($order->service_specific_data) . "\n";
    echo "Reception Notes: " . $order->reception_notes . "\n";
} else {
    echo "Order not found!\n";
}
