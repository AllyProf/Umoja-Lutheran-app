<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KitchenOrderController extends Controller
{
    /**
     * Display pending food orders for the kitchen
     */
    public function index()
    {
        $pendingOrders = ServiceRequest::with(['booking.room', 'service'])
            ->where(function($query) {
                // Include Generic Food Order (4), Restaurant Food Order (48), and categories
                $query->whereIn('service_id', [4, 48])
                      ->orWhereHas('service', function($q) {
                          $q->whereIn('category', ['food', 'restaurant']);
                      });
            })
            ->where(function($q) {
                // 1. All active orders (pending, approved, or preparing)
                $q->whereIn('status', ['pending', 'approved', 'preparing'])
                // 2. OR Served orders that are WAITING FOR PAYMENT
                ->orWhere(function($sub) {
                    $sub->where('status', 'completed')
                        ->whereIn('payment_status', ['pending', 'unpaid']);
                });
            })
            ->orderBy('requested_at', 'desc')
            ->get();

        // Statistics
        $stats = [
            'pending_count' => $pendingOrders->count(),
            'completed_today' => ServiceRequest::where(function($query) {
                                    $query->whereIn('service_id', [4, 48])
                                          ->orWhereHas('service', function($q) {
                                              $q->whereIn('category', ['food', 'restaurant']);
                                          });
                                })
                                ->where('status', 'completed')
                                ->whereDate('completed_at', now())
                                ->count(),
        ];

        return view('admin.restaurants.kitchen.orders', compact('pendingOrders', 'stats'));
    }

    public function startPreparation(Request $request, ServiceRequest $serviceRequest)
    {
        $user = Auth::guard('staff')->user();
        \Log::info('Kitchen starting preparation', ['order_id' => $serviceRequest->id, 'user_id' => $user->id ?? 'unknown']);
        
        try {
            $serviceRequest->update([
                'status' => 'preparing',
                'preparation_started_at' => now(),
                'approved_by' => $user->id ?? $serviceRequest->approved_by,
                'approved_at' => $serviceRequest->approved_at ?? now(),
            ]);

            return response()->json(['success' => true, 'message' => 'Preparation started!']);
        } catch (\Exception $e) {
            \Log::error('Kitchen start preparation failed', ['order_id' => $serviceRequest->id, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Mark an order as completed and deduct ingredients
     */
    public function complete(Request $request, ServiceRequest $serviceRequest)
    {
        $user = Auth::guard('staff')->user();
        
        DB::beginTransaction();
        try {
            \Log::info('Kitchen completing order', ['order_id' => $serviceRequest->id, 'user_id' => $user->id ?? 'unknown']);
            // 1. Mark as completed
            $updateData = [
                'status' => 'completed',
                'completed_at' => now(),
                'approved_by' => $user->id ?? $serviceRequest->approved_by,
                'approved_at' => $serviceRequest->approved_at ?? now(),
                'preparation_started_at' => $serviceRequest->preparation_started_at ?? now(),
                'reception_notes' => ($serviceRequest->reception_notes ? $serviceRequest->reception_notes . ' | ' : '') . "Completed by Kitchen (" . ($user->name ?? 'Staff') . ")"
            ];

            // Handle Payment if provided (e.g. for Walk-ins paying at Kitchen)
            if ($request->filled('payment_method')) {
                $isRoomCharge = $request->payment_method === 'room_charge';
                $updateData['payment_status'] = $isRoomCharge ? 'room_charge' : 'paid';
                $updateData['payment_method'] = $request->payment_method;
                $updateData['payment_reference'] = $request->payment_reference;
                
                $methodName = strtoupper(str_replace('_', ' ', $request->payment_method));
                $updateData['reception_notes'] .= " | Paid via $methodName";
            }

            $serviceRequest->update($updateData);

            // Note: Ingredient deduction is handled manually through the shopping list/inventory system 
            // in this simplified version. we just mark the order as completed.

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Order completed!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Show order history for the kitchen
     */
    public function history()
    {
        $completedOrders = ServiceRequest::with(['booking.room', 'service', 'approvedBy'])
            ->where(function($query) {
                // Service ID 48 is "Restaurant Food Order", 4 is "Generic Food Order"
                $query->whereIn('service_id', [4, 48])
                      ->orWhereHas('service', function($q) {
                          $q->whereIn('category', ['food', 'restaurant']);
                      });
            })
            ->where('status', 'completed')
            ->orderBy('completed_at', 'desc')
            ->paginate(20);

        return view('admin.restaurants.kitchen.order_history', compact('completedOrders'));
    }

    /**
     * Print kitchen order docket
     */
    public function printDocket(ServiceRequest $serviceRequest)
    {
        $order = $serviceRequest->load(['booking.room', 'service', 'dayService']);
        
        // Determine Destination
        $destination = 'Internal';
        if ($order->is_walk_in) {
            $walkInName = $order->walk_in_name ?? 'Guest';
            $destination = str_contains(strtolower($walkInName), 'walk-in') ? $walkInName : 'WALK-IN (' . $walkInName . ')';
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
        // Get group key from request
        $isWalkIn = $request->input('is_walk_in', false);
        $identifier = $request->input('identifier'); // walk_in_name or booking_id
        
        // Fetch all orders for this group
        $orders = ServiceRequest::with(['service', 'booking.room', 'dayService']);
        
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
}
