<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Service;
use App\Models\ServiceRequest;
use App\Models\Staff;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WaiterController extends Controller
{
    /**
     * Show Waiter Dashboard / POS Interface
     */
    public function dashboard()
    {
        // 1. Get Active Bookings (Checked In) for Room Selection
    $activeBookings = Booking::with(['room', 'company'])
        ->where('check_in_status', 'checked_in')
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function($b) {
            return [
                'id' => $b->id,
                'room_number' => $b->room->room_number ?? '?',
                'room_type' => $b->room->room_type ?? 'Room',
                'guest_name' => $b->guest_name,
                'company' => $b->company->name ?? 'Private Guest',
                'pax' => $b->number_of_guests,
                'arrival' => $b->check_in->format('d M'),
                'stay' => $b->check_in->diffInDays($b->check_out) . ' Nights'
            ];
        });

        // 2. Get available drinks and calculate stock levels (Replicated from restaurantService logic)
        $barCategories = ['drinks', 'alcoholic_beverage', 'non_alcoholic_beverage', 'water', 'juices', 'energy_drinks', 'spirits', 'wines', 'cocktails', 'hot_beverages', 'bar'];
        
        // Fetch all completed transfers to build historical stock
        $allTransfers = \App\Models\StockTransfer::where('status', 'completed')->get();
        // Fetch all completed sales to deduct from stock
        $allSales = \App\Models\ServiceRequest::where('status', 'completed')
            ->whereHas('service', function($q) use ($barCategories) {
                $q->whereIn('category', $barCategories);
            })->get();

        // Build a stock mapping [variant_id => current_stock_in_pics]
        $stockLevels = [];
        foreach ($allTransfers as $t) {
            $vid = $t->product_variant_id;
            if (!isset($stockLevels[$vid])) $stockLevels[$vid] = 0;
            
            $itemsPerPkg = $t->productVariant->items_per_package ?? 1;
            $pics = ($t->quantity_unit === 'packages') ? ($t->quantity_transferred * $itemsPerPkg) : $t->quantity_transferred;
            $stockLevels[$vid] += $pics;
        }

        foreach ($allSales as $s) {
            if ($s->service_id == 3 && $s->product_variant_id) { // Generic Bar Order Ref
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
                // Build options based on pricing
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

        // 3. Get all available Food Recipes
        $recipes = \App\Models\Recipe::where('is_available', true)->get();
        $foodItems = [];
        foreach ($recipes as $recipe) {
            $foodItems[] = [
                'id' => $recipe->id,
                'name' => $recipe->name,
                'description' => $recipe->description ?? 'Chef Special',
                'price' => $recipe->selling_price,
                'category' => $recipe->category ?? 'Food',
                'image' => $recipe->image,
            ];
        }

        return view('dashboard.waiter-dashboard', compact('activeBookings', 'foodItems', 'drinks'));
    }

    /**
     * Fetch active bookings via AJAX (if needed for refreshing)
     */
    public function getActiveBookings()
    {
        $bookings = Booking::with('room')
            ->where('check_in_status', 'checked_in')
            ->get()
            ->map(function($b) {
                return [
                    'id' => $b->id,
                    'room_number' => $b->room->room_number ?? 'N/A',
                    'guest_name' => $b->guest_name,
                ];
            });
            
        return response()->json($bookings);
    }

    /**
     * Submit an order from Waiter POS
     */
    public function storeOrder(Request $request)
    {
        $request->validate([
            'order_type' => 'required|in:resident,walk_in',
            'booking_id' => 'required_if:order_type,resident',
            'walk_in_name' => 'nullable|string',
            'items' => 'required|array|min:1',
            'payment_intent' => 'nullable|string', // 'now' or 'later'
        ]);

        try {
            DB::beginTransaction();
            $user = Auth::guard('staff')->user();
            $createdRequests = [];

            foreach ($request->items as $item) {
                // Determine base note
                $notePrefix = 'POS Order by Waiter: ' . ($user->name ?? 'Staff');
                if ($request->payment_intent === 'now') {
                    $notePrefix .= ' [PAY AT COUNTER]';
                }
                
                $data = [
                    'quantity' => $item['qty'],
                    'unit_price_tsh' => $item['price'],
                    'total_price_tsh' => $item['price'] * $item['qty'],
                    'status' => 'pending',
                    'requested_at' => now(),
                    'reception_notes' => $notePrefix . (isset($item['note']) && $item['note'] ? (' - Msg: ' . $item['note']) : ''),
                    'payment_status' => 'pending', // Waiters always send as pending for Bar/Chef to settle
                ];

                // Handle Identity (Booking or Walk-in)
                if ($request->order_type === 'resident') {
                    $data['booking_id'] = $request->booking_id;
                    $data['is_walk_in'] = false;
                } else {
                    $data['is_walk_in'] = true;
                    $data['walk_in_name'] = $request->walk_in_name;
                }

                // Handle Item Type (Food Recipe vs Drink Product vs Generic Service)
                // DEBUG: Inspect item data
                \Log::info("Waiter Order Item: " . json_encode($item));

                $isFood = isset($item['isFood']) && filter_var($item['isFood'], FILTER_VALIDATE_BOOLEAN);

                if ($isFood) {
                    $data['service_id'] = 4; // Generic Food Order ID
                    $data['service_specific_data'] = [
                        'food_id' => $item['id'],
                        'item_name' => $item['name']
                    ];
                } elseif (isset($item['variantId']) && $item['variantId']) {
                    $data['service_id'] = 3; // Generic Bar Order ID
                    $data['product_id'] = $item['productId'];
                    $data['product_variant_id'] = $item['variantId'];
                    $data['selling_method'] = $item['method'];
                    $data['service_specific_data'] = [
                        'product_id' => $item['productId'],
                        'product_variant_id' => $item['variantId'],
                        'selling_method' => $item['method'],
                        'item_name' => $item['name']
                    ];
                } elseif (isset($item['is_service_only']) && $item['is_service_only']) {
                    $data['service_id'] = $item['id'];
                } else {
                    $data['service_id'] = $item['id'];
                }

                $requestModel = ServiceRequest::create($data);
                $createdRequests[] = $requestModel->id;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order submitted successfully! Sent to Kitchen/Bar.',
                'request_ids' => $createdRequests
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error submitting waiter order: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show Waiter Order History
     */
    public function orders()
    {
        $user = Auth::guard('staff')->user();
        
        $orders = ServiceRequest::with(['service', 'booking.room'])
            ->where('reception_notes', 'LIKE', '%Waiter: ' . $user->name . '%')
            ->orderBy('requested_at', 'desc')
            ->paginate(15);
            
        return view('dashboard.waiter-orders', compact('orders'));
    }

    /**
     * Print Docket for an Order
     */
    public function printDocket(ServiceRequest $serviceRequest)
    {
        $order = $serviceRequest->load(['booking.room', 'service', 'dayService']);
        
        // Determine Destination
        $destination = 'Internal';
        if ($order->is_walk_in) {
            $destination = 'WALK-IN (' . ($order->walk_in_name ?? 'Guest') . ')';
        } elseif ($order->booking) {
            $destination = 'ROOM ' . ($order->booking->room->room_number ?? 'N/A');
        } elseif ($order->dayService) {
            $destination = 'CEREMONY (' . ($order->dayService->name ?? 'Event') . ')';
        }

        // Determine Guest Name
        $guestName = $order->is_walk_in ? ($order->walk_in_name ?? 'General Guest') : ($order->booking->guest_name ?? 'Hotel Guest');

        // Determine Requested By
        $requestedBy = 'N/A';
        if ($order->reception_notes && str_contains($order->reception_notes, 'Waiter: ')) {
            $parts = explode('Waiter: ', $order->reception_notes);
            $byParts = explode(' - Msg:', $parts[1] ?? '');
            $requestedBy = $byParts[0] ?? 'Waiter';
        }

        // Determine Note
        $note = $order->guest_request;
        if (!$note && $order->reception_notes && str_contains($order->reception_notes, '- Msg: ')) {
            $parts = explode('- Msg: ', $order->reception_notes);
            $note = $parts[1] ?? null;
        }

        // Determine Item Name
        $itemName = $order->service_specific_data['item_name'] ?? ($order->service->name ?? 'Special Item');

        return view('dashboard.print-kitchen-order-docket', compact('order', 'destination', 'guestName', 'itemName', 'requestedBy', 'note'));
    }
    
    /**
     * Print Docket for All Items in a Guest Group
     */
    public function printGroupDocket(Request $request)
    {
        $user = Auth::guard('staff')->user();
        
        // Get group key from request
        $isWalkIn = $request->input('is_walk_in', false);
        $identifier = $request->input('identifier'); // walk_in_name or booking_id
        
        // Fetch all orders for this group
        $orders = ServiceRequest::with(['service', 'booking.room', 'dayService'])
            ->where('reception_notes', 'LIKE', '%Waiter: ' . $user->name . '%');
        
        if ($isWalkIn) {
            $orders = $orders->where('is_walk_in', true)
                ->where('walk_in_name', $identifier);
        } else {
            $orders = $orders->where('booking_id', $identifier);
        }
        
        $orders = $orders->orderBy('requested_at', 'desc')->get();
        
        if ($orders->isEmpty()) {
            abort(404, 'No orders found');
        }

        // If walk-in, further filter by date to avoid picking up same name from different days
        if ($isWalkIn) {
            $firstDate = $orders->first()->requested_at->toDateString();
            $orders = $orders->filter(function($o) use ($firstDate) {
                return $o->requested_at->toDateString() === $firstDate;
            });
        }
        
        $first = $orders->first();
        
        // Determine Destination
        $destination = 'Internal';
        if ($first->is_walk_in) {
            $walkInName = $first->walk_in_name ?? 'Guest';
            $destination = str_contains(strtolower($walkInName), 'walk-in') ? $walkInName : 'WALK-IN (' . $walkInName . ')';
        } elseif ($first->booking) {
            $destination = 'ROOM ' . ($first->booking->room->room_number ?? 'N/A');
        }
        
        // Determine Guest Name
        $guestName = $first->is_walk_in ? ($first->walk_in_name ?? 'General Guest') : ($first->booking->guest_name ?? 'Hotel Guest');
        
        // Determine Requested By
        $requestedBy = 'N/A';
        if ($first->reception_notes && str_contains($first->reception_notes, 'Waiter: ')) {
            $parts = explode('Waiter: ', $first->reception_notes);
            $byParts = explode(' - Msg:', $parts[1] ?? '');
            $requestedBy = $byParts[0] ?? 'Waiter';
        }
        
        // Calculate total
        $totalAmount = $orders->sum('total_price_tsh');
        
        return view('dashboard.print-waiter-group-docket', compact('orders', 'destination', 'guestName', 'requestedBy', 'totalAmount', 'first'));
    }

    /**
     * Cancel an Individual Order
     */
    public function cancelOrder(Request $request, ServiceRequest $serviceRequest)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        // Only allow cancelling if not already completed or cancelled
        if (in_array($serviceRequest->status, ['completed', 'cancelled'])) {
            return back()->with('error', 'Cannot cancel an order that is already ' . $serviceRequest->status . '.');
        }

        $user = Auth::guard('staff')->user();
        $reason = $request->reason;
        
        $serviceRequest->update([
            'status' => 'cancelled',
            'reception_notes' => ($serviceRequest->reception_notes ? $serviceRequest->reception_notes . " | " : "") . "CANCELLED by Waiter (" . ($user->name ?? 'Staff') . "): " . $reason
        ]);

        return back()->with('success', 'Order cancelled successfully.');
    }

    /**
     * Show Waiter Sales Summary
     */
    public function salesSummary(Request $request)
    {
        $user = Auth::guard('staff')->user();
        $date = $request->get('date', now()->toDateString());
        
        // Base query for orders placed by this waiter on this date
        $query = ServiceRequest::where('reception_notes', 'LIKE', '%Waiter: ' . $user->name . '%')
            ->whereDate('requested_at', $date);

        $totalSales = $query->sum('total_price_tsh');
        $totalOrders = $query->count();
        
        $paidSales = (clone $query)->where('payment_status', 'paid')->sum('total_price_tsh');
        $pendingSales = (clone $query)->where('payment_status', 'pending')->sum('total_price_tsh');
        $roomChargeSales = (clone $query)->where('payment_status', 'room_charge')->sum('total_price_tsh');

        $activeOrders = (clone $query)->whereIn('status', ['pending', 'approved', 'preparing'])->count();
        $completedOrders = (clone $query)->where('status', 'completed')->count();
        $cancelledOrders = (clone $query)->where('status', 'cancelled')->count();

        $itemsBreakdown = (clone $query)->get()
            ->groupBy(function($item) {
                return $item->service_specific_data['item_name'] ?? ($item->service->name ?? 'Unknown');
            })
            ->map(function($group) {
                return [
                    'qty' => $group->sum('quantity'),
                    'revenue' => $group->sum('total_price_tsh')
                ];
            })
            ->sortByDesc('qty')
            ->take(20);

        return view('dashboard.waiter-sales-summary', compact(
            'totalSales', 'totalOrders', 'paidSales', 'pendingSales', 
            'roomChargeSales', 'itemsBreakdown', 'date',
            'activeOrders', 'completedOrders', 'cancelledOrders'
        ));
    }
}
