<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Latest 3 Orders ===\n\n";

$orders = \App\Models\ServiceRequest::orderBy('created_at', 'desc')->limit(3)->get();

foreach ($orders as $order) {
    echo "Order #" . $order->id . ":\n";
    echo "  Service ID: " . $order->service_id . "\n";
    echo "  Service Name: " . ($order->service ? $order->service->name : 'N/A') . "\n";
    echo "  Service Category: " . ($order->service ? $order->service->category : 'N/A') . "\n";
    echo "  Status: " . $order->status . "\n";
    echo "  Product Variant ID: " . ($order->product_variant_id ?? 'NULL') . "\n";
    echo "  Requested At: " . $order->requested_at . "\n";
    echo "  Service Specific Data: " . json_encode($order->service_specific_data) . "\n";
    echo "\n";
}

echo "=== Testing Query ===\n\n";

$barCategories = ['drinks', 'alcoholic_beverage', 'non_alcoholic_beverage', 'water', 'juices', 'energy_drinks', 'bar'];

// Test if service_id = 3 works
$test1 = \App\Models\ServiceRequest::where('service_id', 3)->count();
echo "Orders with service_id = 3: " . $test1 . "\n";

// Test the full query
$pendingOrders = \App\Models\ServiceRequest::with(['booking.room', 'service'])
    ->where(function($q) use ($barCategories) {
        $q->whereHas('service', function($query) use ($barCategories) {
            $query->whereIn('category', $barCategories);
        })->orWhere('service_id', 3);
    })
    ->where(function($query) {
        $query->where('status', 'pending')
            ->orWhere('status', 'approved')
            ->orWhere(function($q) {
                $q->where('status', 'completed')
                  ->where('payment_status', 'pending');
            });
    })
    ->orderBy('requested_at', 'desc')
    ->get();

echo "Pending orders found by query: " . $pendingOrders->count() . "\n";
