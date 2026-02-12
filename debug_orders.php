<?php
use App\Models\Service;
use App\Models\ServiceRequest;

// Check Service 4
$s = Service::find(4);
echo "Service 4: " . ($s ? $s->name . " (Category: " . $s->category . ")" : "Not Found") . PHP_EOL;

// Check pending orders
$pending = ServiceRequest::where('status', 'pending')->count();
echo "Total Pending Requests: " . $pending . PHP_EOL;

// Check last order details
$last = ServiceRequest::orderBy('id', 'desc')->first();
if ($last) {
    echo "Last Order ID: " . $last->id . PHP_EOL;
    echo "Last Order Service ID: " . $last->service_id . PHP_EOL;
    echo "Last Order Status: " . $last->status . PHP_EOL;
    echo "Last Order Payment Status: " . $last->payment_status . PHP_EOL;
    if ($last->service) {
        echo "Last Order Service Category: " . $last->service->category . PHP_EOL;
    } else {
        echo "Last Order Service: Not loaded or null" . PHP_EOL;
    }
} else {
    echo "No orders found." . PHP_EOL;
}
