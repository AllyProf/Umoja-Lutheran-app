<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Checking Bar Orders ===\n\n";

// Get the Generic Bar Order service
$barService = \App\Models\Service::where('name', 'Generic Bar Order')->first();
echo "Generic Bar Order Service ID: " . ($barService ? $barService->id : 'NOT FOUND') . "\n";
echo "Category: " . ($barService ? $barService->category : 'N/A') . "\n\n";

// Get the latest service request
$latestOrder = \App\Models\ServiceRequest::orderBy('created_at', 'desc')->first();
if ($latestOrder) {
    echo "Latest Order Details:\n";
    echo "  ID: " . $latestOrder->id . "\n";
    echo "  Service ID: " . $latestOrder->service_id . "\n";
    echo "  Status: " . $latestOrder->status . "\n";
    echo "  Is Walk-in: " . ($latestOrder->is_walk_in ? 'Yes' : 'No') . "\n";
    echo "  Payment Status: " . $latestOrder->payment_status . "\n";
    echo "  Product Variant ID: " . ($latestOrder->product_variant_id ?? 'NULL') . "\n";
    echo "  Requested At: " . $latestOrder->requested_at . "\n";
    
    if ($latestOrder->service) {
        echo "  Service Name: " . $latestOrder->service->name . "\n";
        echo "  Service Category: " . $latestOrder->service->category . "\n";
    }
} else {
    echo "No orders found!\n";
}

echo "\n=== Testing Bar Keeper Query ===\n\n";

$barCategories = ['drinks', 'alcoholic_beverage', 'non_alcoholic_beverage', 'water', 'juices', 'energy_drinks', 'bar'];

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

echo "Pending Orders Found: " . $pendingOrders->count() . "\n\n";

foreach ($pendingOrders as $order) {
    echo "Order #" . $order->id . ":\n";
    echo "  Service: " . ($order->service ? $order->service->name : 'N/A') . "\n";
    echo "  Status: " . $order->status . "\n";
    echo "  Guest: " . ($order->is_walk_in ? ($order->walk_in_name ?? 'Walk-in') : ($order->booking->guest_name ?? 'N/A')) . "\n";
    echo "\n";
}
