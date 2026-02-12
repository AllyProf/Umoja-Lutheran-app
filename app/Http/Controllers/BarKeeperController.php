<?php

namespace App\Http\Controllers;

use App\Models\StockTransfer;
use App\Models\ServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BarKeeperController extends Controller
{
    /**
     * Bar Keeper Dashboard
     */
    public function dashboard()
    {
        $user = Auth::guard('staff')->user();
        
        // Get pending transfers for this bar keeper
        $pendingTransfers = StockTransfer::with(['product', 'productVariant', 'transferredBy'])
            ->where('received_by', $user->id)
            ->where('status', 'pending')
            ->orderBy('transfer_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        // Get completed transfers (recent)
        $completedTransfers = StockTransfer::with(['product', 'productVariant', 'transferredBy'])
            ->where('received_by', $user->id)
            ->where('status', 'completed')
            ->orderBy('received_at', 'desc')
            ->limit(10)
            ->get();
            
        $barCategories = ['drinks', 'alcoholic_beverage', 'non_alcoholic_beverage', 'water', 'juices', 'energy_drinks', 'bar'];
        
        $pendingOrders = \App\Models\ServiceRequest::with(['booking.room', 'service'])
            ->where(function($q) use ($barCategories) {
                $q->whereHas('service', function($query) use ($barCategories) {
                    $query->whereIn('category', $barCategories);
                })->orWhere('service_id', 3); // Generic Bar Order (ID 3)
            })
            ->where(function($query) {
                // All pending orders (from waiters, guests, etc.)
                $query->where('status', 'pending')
                    // OR approved orders waiting to be served
                    ->orWhere('status', 'approved')
                    // OR completed orders that haven't been paid yet (walk-ins)
                    ->orWhere(function($q) {
                        $q->where('status', 'completed')
                          ->where('payment_status', 'pending');
                    });
            })
            ->orderBy('requested_at', 'desc')
            ->get();
        
        // Statistics
        $totalPending = StockTransfer::where('received_by', $user->id)
            ->where('status', 'pending')
            ->count();
        
        $totalCompleted = StockTransfer::where('received_by', $user->id)
            ->where('status', 'completed')
            ->count();
        
        $totalProducts = StockTransfer::where('received_by', $user->id)
            ->where('status', 'completed')
            ->distinct('product_id')
            ->count('product_id');
            
        $totalPendingOrders = $pendingOrders->count();

        // Get active ceremonies (registered by reception today)
        $activeCeremonies = \App\Models\DayService::with(['serviceRequests.service'])
            ->where(function($query) {
                $query->where('service_type', 'LIKE', '%ceremony%')
                      ->orWhere('service_type', 'LIKE', '%ceremory%')
                      ->orWhere('service_type', 'LIKE', '%birthday%');
            })
            ->whereDate('service_date', now()->toDateString())
            ->get();
        
        // --- Walk-in Sale Menu Items (POS) ---
        // Match logic from customer restaurant page for consistency
        $barCategories = ['drinks', 'alcoholic_beverage', 'non_alcoholic_beverage', 'water', 'juices', 'energy_drinks', 'spirits', 'wines', 'cocktails', 'hot_beverages', 'beers', 'liquor', 'whiskey'];
        
        // 1. Calculate stock levels from transfers and sales
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
            $meta = $s->service_specific_data;
            $vid = $meta['product_variant_id'] ?? ($meta['variant_id'] ?? null);
            if ($vid && isset($stockLevels[$vid])) {
                $variant = \App\Models\ProductVariant::find($vid);
                if ($variant) {
                    $unitPrice = (float)$s->unit_price_tsh;
                    $isPicSale = abs($unitPrice - (float)$variant->selling_price_per_pic) < 100;
                    if ($isPicSale) {
                        $stockLevels[$vid] -= $s->quantity;
                    } else {
                        $servingsPerPic = $variant->servings_per_pic > 0 ? $variant->servings_per_pic : 1;
                        $stockLevels[$vid] -= ($s->quantity / $servingsPerPic);
                    }
                }
            }
        }

        // 2. Fetch Products and group options
        $products = \App\Models\Product::whereIn('category', $barCategories)
            ->with(['variants'])
            ->get();

        $drinks = [];
        foreach ($products as $product) {
            foreach ($product->variants as $variant) {
                $options = [];
                // Option A: Bottle (PIC)
                if ($variant->can_sell_as_pic && $variant->selling_price_per_pic > 0) {
                    $options[] = (object)[
                        'type' => $variant->packaging ?: 'Bottle',
                        'method' => 'pic',
                        'price' => (float)$variant->selling_price_per_pic
                    ];
                }
                // Option B: Glass (Serving)
                if ($variant->can_sell_as_serving && $variant->selling_price_per_serving > 0) {
                    $options[] = (object)[
                        'type' => $variant->selling_unit_name ?: 'Glass',
                        'method' => 'serving',
                        'price' => (float)$variant->selling_price_per_serving
                    ];
                }

                // Fallback for legacy items
                if (empty($options)) {
                    $latestReceipt = \App\Models\StockReceipt::where('product_variant_id', $variant->id)->orderBy('received_date', 'desc')->first();
                    $price = $latestReceipt ? $latestReceipt->selling_price_per_bottle : 0;
                    if ($price > 0) {
                        $options[] = (object)['type' => 'Bottle', 'method' => 'pic', 'price' => (float)$price];
                    }
                }

                if (!empty($options)) {
                    $currentStock = $stockLevels[$variant->id] ?? 0;
                    $drinks[] = (object)[
                        'product_id' => $product->id,
                        'variant_id' => $variant->id,
                        'name' => ($variant->variant_name ?: $product->name) . ($variant->measurement ? ' (' . $variant->measurement . ')' : ''),
                        'category' => $product->category,
                        'image' => $variant->image ?: $product->image,
                        'options' => $options,
                        'current_stock' => $currentStock,
                        'is_product' => true
                    ];
                }
            }
        }

        // 3. Include Services that overlap with drinks
        $serviceDrinks = \App\Models\Service::whereIn('category', $barCategories)->where('is_active', true)->get();
        foreach ($serviceDrinks as $service) {
            $alreadyAdded = false;
            foreach ($drinks as $drink) {
                if (str_contains(strtolower($service->name), strtolower($drink->name))) {
                    $alreadyAdded = true;
                    break;
                }
            }
            if (!$alreadyAdded) {
                $drinks[] = (object)[
                    'id' => $service->id,
                    'variant_id' => null,
                    'name' => $service->name,
                    'category' => $service->category,
                    'options' => [(object)['type' => 'Unit', 'method' => 'pic', 'price' => (float)$service->price_tsh]],
                    'image' => null,
                    'current_stock' => 999,
                    'is_product' => false,
                    'service_id' => $service->id
                ];
            }
        }

        $role = 'bar_keeper';
        
        return view('dashboard.bar-keeper-dashboard', compact(
            'pendingTransfers',
            'completedTransfers',
            'pendingOrders',
            'totalPending',
            'totalCompleted',
            'totalProducts',
            'totalProducts',
            'totalPendingOrders',
            'drinks',
            'activeCeremonies',
            'role'
        ));
    }
    
    /**
     * Update transfer status (mark as received)
     */
    public function receiveTransfer(Request $request, StockTransfer $stockTransfer)
    {
        // Verify this transfer is for the logged-in bar keeper
        if ($stockTransfer->received_by != Auth::guard('staff')->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. This transfer is not assigned to you.',
            ], 403);
        }
        
        $validated = $request->validate([
            'status' => 'required|in:completed,cancelled',
        ]);
        
        if ($validated['status'] === 'completed' && !$stockTransfer->received_at) {
            $validated['received_at'] = now();
        }
        
        $stockTransfer->update($validated);
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Transfer status updated successfully!',
                'transfer' => $stockTransfer->load(['product', 'productVariant', 'transferredBy']),
            ]);
        }
        
        return redirect()->back()
            ->with('success', 'Transfer status updated successfully!');
    }
    
    /**
     * Bar Keeper Stock & Sales Reports (Matching Kitchen Style)
     */
    public function reports(Request $request)
    {
        $user = Auth::guard('staff')->user();
        $dateType = $request->get('date_type', 'daily');
        $date = $request->date ? \Carbon\Carbon::parse($request->date) : now();
        
        if ($dateType === 'weekly') {
            $startDate = $date->copy()->startOfWeek();
            $endDate = $date->copy()->endOfWeek();
        } else {
            $startDate = $date->copy()->startOfDay();
            $endDate = $date->copy()->endOfDay();
        }

        // 1. Get ALL products ever transferred TO the target bar keepers
        $targetUserIds = [$user->id];
        // Check if user is manager or admin to show consolidated stock
        if (in_array($user->role, ['manager', 'admin'])) {
            $barKeeperIds = \App\Models\Staff::where('role', 'bar_keeper')->pluck('id')->toArray();
            if (!empty($barKeeperIds)) {
                $targetUserIds = $barKeeperIds;
            }
        }

        $variantIds = StockTransfer::whereIn('received_by', $targetUserIds)
            ->where('status', 'completed')
            ->distinct()
            ->pluck('product_variant_id')
            ->toArray();
            
        // Also include variants that appear in sales records
        $soldVariantIds = \App\Models\ServiceRequest::where(function($q) {
                $q->where('status', 'completed')
                  ->orWhereNotNull('day_service_id');
            })
            ->whereNotNull('service_specific_data->product_variant_id')
            ->get()
            ->pluck('service_specific_data.product_variant_id')
            ->toArray();

        $allVariantIds = array_unique(array_merge($variantIds, $soldVariantIds));
        $variants = \App\Models\ProductVariant::with('product')->whereIn('id', $allVariantIds)->get();
        // Filter to only show bar categories in stock movements
        $barCategories = ['drinks', 'beverage', 'alcoholic_beverage', 'non_alcoholic_beverage', 'water', 'juices', 'energy_drinks', 'spirits', 'whiskey', 'wine', 'wines', 'beers', 'liquor', 'cocktails', 'soda', 'beverages', 'alcoholic', 'hot_beverages', 'bar'];

        $reportData = [];
        foreach ($variants as $variant) {
            // Check if product belongs to bar categories
            if (!in_array($variant->product->category, $barCategories)) {
                continue;
            }
            
            // Opening Stock: (Received before) - (Sold before)
            // RECEIVED BEFORE
            $receivedBefore = StockTransfer::where('product_variant_id', $variant->id)
                ->whereIn('received_by', $targetUserIds)
                ->where('status', 'completed')
                ->where(function($q) use ($startDate) {
                    $q->where('received_at', '<', $startDate)
                      ->orWhere(function($sub) use ($startDate) {
                          $sub->whereNull('received_at')->where('created_at', '<', $startDate);
                      });
                })
                ->get()
                ->sum(function($t) {
                    return $t->quantity_unit === 'packages' 
                        ? (float)$t->quantity_transferred * (float)($t->productVariant->items_per_package ?? 1) 
                        : (float)$t->quantity_transferred;
                });

            // SOLD BEFORE (Need to robustly filter ServiceRequests)
            $soldBefore = \App\Models\ServiceRequest::where(function($q) use ($startDate) {
                    $q->where(function($sub) use ($startDate) {
                        $sub->where('status', 'completed')
                            ->whereNull('day_service_id')
                            ->where('completed_at', '<', $startDate);
                    })->orWhere(function($sub) use ($startDate) {
                        $sub->whereNotNull('day_service_id')
                            ->where('created_at', '<', $startDate);
                    });
                })
                ->where(function($q) use ($variant) {
                    $q->where('service_specific_data->product_variant_id', $variant->id)
                      ->orWhere('service_specific_data->product_variant_id', (string)$variant->id)
                      ->orWhere('service_specific_data->variant_id', $variant->id)
                      ->orWhere('service_specific_data->variant_id', (string)$variant->id);
                })
                ->get()
                ->sum(function($s) use ($variant) {
                    $servingsPerPic = ($variant->servings_per_pic > 0) ? (float)$variant->servings_per_pic : 1.0;
                    return (isset($s->service_specific_data['selling_method']) && $s->service_specific_data['selling_method'] === 'serving')
                        ? ($s->quantity / $servingsPerPic)
                        : $s->quantity;
                });

            $openingStock = max(0, $receivedBefore - $soldBefore);

            // Movements IN PERIOD
            $receivedInPeriod = StockTransfer::where('product_variant_id', $variant->id)
                ->whereIn('received_by', $targetUserIds)
                ->where('status', 'completed')
                ->whereBetween('received_at', [$startDate, $endDate])
                ->get()
                ->sum(function($t) {
                    return $t->quantity_unit === 'packages' 
                        ? (float)$t->quantity_transferred * (float)($t->productVariant->items_per_package ?? 1) 
                        : (float)$t->quantity_transferred;
                });

            $salesInPeriod = \App\Models\ServiceRequest::where(function($q) use ($startDate, $endDate) {
                    $q->where(function($sub) use ($startDate, $endDate) {
                        $sub->where('status', 'completed')
                            ->whereNull('day_service_id')
                            ->whereBetween('completed_at', [$startDate, $endDate]);
                    })->orWhere(function($sub) use ($startDate, $endDate) {
                        $sub->whereNotNull('day_service_id')
                            ->whereBetween('created_at', [$startDate, $endDate]);
                    });
                })
                ->where(function($q) use ($variant) {
                    $q->where('service_specific_data->product_variant_id', $variant->id)
                      ->orWhere('service_specific_data->product_variant_id', (string)$variant->id)
                      ->orWhere('service_specific_data->variant_id', $variant->id)
                      ->orWhere('service_specific_data->variant_id', (string)$variant->id);
                })
                ->get();

            $soldInPeriod = $salesInPeriod->sum(function($s) use ($variant) {
                $servingsPerPic = ($variant->servings_per_pic > 0) ? (float)$variant->servings_per_pic : 1.0;
                return (isset($s->service_specific_data['selling_method']) && $s->service_specific_data['selling_method'] === 'serving')
                    ? ($s->quantity / $servingsPerPic)
                    : $s->quantity;
            });

            // Financial Metrics
            $actualRevenue = $salesInPeriod->sum('total_price_tsh');
            
            // Expiry
            $latestTransfer = StockTransfer::where('product_variant_id', $variant->id)
                ->where('received_by', $user->id)
                ->where('status', 'completed')
                ->whereNotNull('expiry_date')
                ->orderBy('received_at', 'desc')
                ->first();
            
            $expireText = "-";
            if ($latestTransfer && $latestTransfer->expiry_date) {
                $daysLeft = now()->startOfDay()->diffInDays($latestTransfer->expiry_date, false);
                if ($daysLeft < 0) $expireText = "Expired";
                elseif ($daysLeft == 0) $expireText = "Today";
                else $expireText = $daysLeft . " Days";
            }

            // Closing Stock = Opening + Received - Sold
            $closingStock = $openingStock + $receivedInPeriod - $soldInPeriod;

            if ($openingStock > 0 || $receivedInPeriod > 0 || $soldInPeriod > 0) {
                // Determine actual revenue from sales records
                $actualRevenue = $salesInPeriod->sum('total_price_tsh');
                
                // Buying Price for Profit Potential calculations
                $latestReceipt = \App\Models\StockReceipt::where('product_variant_id', $variant->id)
                    ->orderBy('received_date', 'desc')
                    ->first();
                $buyingPricePerPic = $latestReceipt ? (float)$latestReceipt->buying_price_per_bottle : 0;
                
                // Potential Revenue (Value of stock if sold as PICs)
                // Use higher of PIC price or servings * glass price if configured
                $picPrice = (float)($variant->selling_price_per_pic ?? 0);
                $servingPriceTotal = (float)($variant->servings_per_pic ?? 1) * (float)($variant->selling_price_per_serving ?? 0);
                $bestUnitPrice = max($picPrice, $servingPriceTotal);
                
                $stockValue = $closingStock * $bestUnitPrice;
                $profitPotential = $stockValue - ($closingStock * $buyingPricePerPic);

                $reportData[] = (object)[
                    'name' => ($variant->variant_name ?: $variant->product->name) . ' (' . $variant->measurement . ')',
                    'category' => $variant->product->category,
                    'unit' => $variant->unit ?? 'pcs',
                    'expiry_date' => $expireText,
                    'servings_per_pic' => $variant->servings_per_pic,
                    'opening_stock' => $openingStock,
                    'received' => $receivedInPeriod > 0 ? $receivedInPeriod : 0,
                    'sold' => $soldInPeriod,
                    'expected_revenue' => $soldInPeriod * $picPrice, // Base bottle revenue
                    'actual_revenue' => $actualRevenue,
                    'max_potential_revenue' => ($openingStock + $receivedInPeriod) * $bestUnitPrice,
                    'stock_value' => $stockValue,
                    'profit_potential' => $profitPotential,
                    'image' => $variant->image ?: $variant->product->image,
                    'in_use' => 0, 
                    'closing_stock' => $closingStock,
                ];
            }
        }

        // 2. Production (Bar Sales) during this period
        $rawSales = \App\Models\ServiceRequest::with(['service', 'booking.room', 'approvedBy'])
            ->where(function($q) use ($barCategories) {
                $q->whereHas('service', function($query) use ($barCategories) {
                    $query->whereIn('category', $barCategories);
                })->orWhereIn('service_id', [3]); // Generic Bar (3)
            })
            ->where('status', 'completed')
            ->whereNull('day_service_id')
            ->whereBetween('completed_at', [$startDate, $endDate])
            ->orderBy('completed_at', 'desc')
            ->get();

        $salesData = $rawSales->map(function($order) {
            // Determine Destination
            $dest = 'N/A';
            $guestLabel = 'Room Guest';
            if ($order->is_walk_in) {
                $walkInName = $order->walk_in_name ?? 'Guest';
                $dest = str_contains(strtolower($walkInName), 'walk-in') ? $walkInName : 'Walk-in (' . $walkInName . ')';
                $guestLabel = 'Walk-in';
            } elseif ($order->booking) {
                $dest = ($order->booking->room->room_number ?? 'N/A') . ' - ' . ($order->booking->guest_name ?? 'N/A');
                $guestLabel = 'Room ' . ($order->booking->room->room_number ?? 'N/A');
            }

            return (object)[
                'item_name' => $order->service_specific_data['item_name'] ?? $order->service->name ?? 'Unknown Drink',
                'destinations' => $dest,
                'guest_label' => $guestLabel,
                'category' => ucfirst($order->service->category ?? 'Beverage'),
                'total_qty' => $order->quantity,
                'unit_price' => $order->unit_price_tsh,
                'total_revenue' => $order->total_price_tsh,
                'time' => $order->completed_at ? $order->completed_at->format('H:i') : '-',
                'served_by' => $order->approvedBy->name ?? 'Bar Staff',
                'payment_status' => $order->payment_status,
                'payment_method' => $order->payment_method,
                'payment_reference' => $order->payment_reference,
            ];
        });

        // 3. Ceremony Usage Breakdown
        $ceremonyUsage = \App\Models\ServiceRequest::with(['service', 'dayService'])
            ->whereNotNull('day_service_id')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where(function($q) use ($barCategories) {
                $q->whereHas('service', function($query) use ($barCategories) {
                    $query->whereIn('category', $barCategories);
                })->orWhereIn('service_id', [3]);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('dashboard.bar-keeper-reports', compact(
            'reportData', 
            'salesData',
            'ceremonyUsage',
            'date', 
            'startDate', 
            'endDate', 
            'dateType'
        ));
    }

    /**
     * Complete a Guest Order (Service Request)
     */
    public function completeOrder(Request $request, \App\Models\ServiceRequest $serviceRequest)
    {
        $request->validate([
            'payment_method' => 'required|string',
            'payment_reference' => 'nullable|string|max:255',
        ]);
        
        $user = Auth::guard('staff')->user();
        
        try {
            \DB::beginTransaction();
            
            // 1. Mark status as completed
            $isRoomCharge = $request->payment_method === 'room_charge';
            
            $serviceRequest->update([
                'status' => 'completed',
                'completed_at' => now(),
                'approved_by' => $user->id,
                'approved_at' => now(), // Auto-approve when completed by bar keeper
                'payment_status' => $isRoomCharge ? 'room_charge' : 'paid',
                'payment_method' => $request->payment_method,
                'payment_reference' => $request->payment_reference,
                'reception_notes' => $serviceRequest->reception_notes . " | Served by {$user->name} (" . ucfirst(str_replace('_', ' ', $request->payment_method)) . ")" . ($request->payment_reference ? " Ref: {$request->payment_reference}" : ""),
            ]);
            
            // 2. Handle Payment for Residents (Booking-based)
            if ($serviceRequest->booking_id && $request->payment_method !== 'room_charge') {
                $booking = $serviceRequest->booking;
                $amountTsh = $serviceRequest->total_price_tsh;
                
                // Convert to USD using locked rate or current rate
                $exchangeRate = $booking->locked_exchange_rate;
                if (!$exchangeRate) {
                    $currencyService = new \App\Services\CurrencyExchangeService();
                    $exchangeRate = $currencyService->getUsdToTshRate();
                }
                
                $amountUsd = $amountTsh / $exchangeRate;
                $newAmountPaidUsd = ($booking->amount_paid ?? 0) + $amountUsd;
                
                // Finalize Booking Payment Status if fully paid
                $serviceTotalTsh = $booking->serviceRequests()->whereIn('status', ['approved', 'completed'])->sum('total_price_tsh');
                $roomTotalTsh = ($booking->total_price * $exchangeRate);
                
                // Extension cost check
                $extensionCostTsh = 0;
                if ($booking->extension_status === 'approved' && $booking->original_check_out && $booking->extension_requested_to) {
                    $nights = \Carbon\Carbon::parse($booking->original_check_out)->diffInDays($booking->extension_requested_to);
                    if ($nights > 0 && $booking->room) $extensionCostTsh = $booking->room->price_per_night * $nights * $exchangeRate;
                }

                $totalBillTsh = $roomTotalTsh + $serviceTotalTsh + $extensionCostTsh;
                $isFullyPaid = (($newAmountPaidUsd * $exchangeRate) >= ($totalBillTsh - 50));

                $booking->update([
                    'amount_paid' => $newAmountPaidUsd,
                    'payment_status' => $isFullyPaid ? 'paid' : 'partial'
                ]);
            }
            
            // Note: Ingredient deduction for food items is now handled manually.
            // Bar item stock deduction is handled via transfers and sales logic in the stock view.

            \DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Order marked as completed successfully!',
            ]);
            
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error completing bar order: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error processing order: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mark a Guest Order as SERVED (Taken) but Payment is Pending
     */
    public function serveOrder(Request $request, \App\Models\ServiceRequest $serviceRequest)
    {
        $user = Auth::guard('staff')->user();
        
        try {
            $serviceRequest->update([
                'status' => 'completed', // Moved from Pending to Completed (Taken out of queue)
                'completed_at' => now(),
                'approved_by' => $user->id,
                'approved_at' => now(), 
                'payment_status' => 'pending', // Explicitly Pending Payment
                'reception_notes' => $serviceRequest->reception_notes . " | Served by {$user->name} (Pending Payment)",
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Order marked as SERVED (Awaiting Payment)!',
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error serving bar order: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * View Completed Order History & Statistics
     */
    public function completedOrders(Request $request)
    {
        $user = Auth::guard('staff')->user();
        
        // Base query for completed bar services
        $barCategories = ['drinks', 'alcoholic_beverage', 'non_alcoholic_beverage', 'water', 'juices', 'energy_drinks', 'bar', 'beverage', 'spirits', 'whiskey', 'wine', 'wines', 'beers', 'liquor', 'cocktails', 'soda', 'beverages', 'alcoholic', 'hot_beverages'];
        
        $query = \App\Models\ServiceRequest::with(['service', 'booking'])
            ->where(function($q) use ($barCategories) {
                $q->whereHas('service', function($query) use ($barCategories) {
                    $query->whereIn('category', $barCategories);
                })->orWhereIn('service_id', [3]); // Generic Bar (3) only
            })
            ->where('status', 'completed')
            ->latest('completed_at');

        // Statistics
        $today = now()->startOfDay();
        $thisMonth = now()->startOfMonth();

        $stats = [
            'today_orders' => (clone $query)->where('completed_at', '>=', $today)->count(),
            'today_revenue' => (clone $query)->where('completed_at', '>=', $today)->sum('total_price_tsh'),
            'month_revenue' => (clone $query)->where('completed_at', '>=', $thisMonth)->sum('total_price_tsh'),
        ];

        // Paginated results for the table
        $orders = $query->paginate(20);

        return view('dashboard.bar-keeper-orders', compact('orders', 'stats'));
    }

    /**
     * Dedicated Stock Transfers Page
     */
    public function transfers(Request $request)
    {
        $user = Auth::guard('staff')->user();
        
        $query = StockTransfer::with(['product', 'productVariant', 'transferredBy'])
            ->where('received_by', $user->id);
            
        // Status filter
        if ($request->has('status') && in_array($request->status, ['pending', 'completed', 'cancelled'])) {
            $query->where('status', $request->status);
        }
        
        // Sort by date (pending first effectively via status check, but mainly date)
        $transfers = $query->orderBy('transfer_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        $role = 'bar_keeper';
        
        return view('dashboard.bar-keeper-transfers', compact('transfers', 'role'));
    }

    /**
     * My Stock Overview Page
     */
    public function stock(Request $request)
    {
        $user = Auth::guard('staff')->user();
        $role = strtolower($user->role ?? 'bar_keeper');
        $isManager = $role === 'manager';
        
        // 1. Fetch Key Data
        // ----------------------------------------
        
        // A. Transfers (IN)
        $transfersQuery = StockTransfer::with(['product', 'productVariant'])
            ->where('status', 'completed');
            
        if (!$isManager) {
            $transfersQuery->where('received_by', $user->id);
        }
        $allTransfers = $transfersQuery->get();
        
        // B. Sales (OUT) - Bar Categories Only
        $barCategories = ['drinks', 'beverage', 'alcoholic_beverage', 'non_alcoholic_beverage', 'water', 'juices', 'energy_drinks', 'spirits', 'whiskey', 'wine', 'beers', 'liquor', 'food', 'restaurant', 'bar'];
        
        $allSales = \App\Models\ServiceRequest::with(['service'])
            ->where(function($q) use ($barCategories) {
                $q->whereHas('service', function($query) use ($barCategories) {
                    $query->whereIn('category', $barCategories);
                })->orWhereIn('service_id', [3, 4]);
            })
            ->where('status', 'completed')
            ->get();
            
        // 2. Build Stock Map
        // ----------------------------------------
        $stockMap = [];
        
        // Initialize from ALL relevant variants to ensures cards show up even with 0 stock
        $allVariants = \App\Models\ProductVariant::with(['product'])
            ->whereHas('product', function($q) use ($barCategories) {
                $q->whereIn('category', $barCategories);
            })
            ->get();

        foreach ($allVariants as $variant) {
            $key = $variant->product_id . '_' . $variant->id;
            
            $stockMap[$key] = [
                'product_id' => $variant->product_id,
                'variant_id' => $variant->id,
                'product_name' => $variant->variant_name ?: $variant->product->name,
                'brand_name' => $variant->product->name,
                'product_image' => $variant->image ?: $variant->product->image,
                'product_category' => $variant->product->category ?? 'other',
                'category_name' => $variant->product->category_name ?? 'Other',
                'variant_name' => $variant->measurement ?? '',
                'packaging' => $variant->packaging ?? 'unit',
                
                // PIC Configuration
                'servings_per_pic' => (float)($variant->servings_per_pic ?? 1),
                'selling_unit' => $variant->selling_unit ?? 'pic',
                'can_sell_as_pic' => $variant->can_sell_as_pic,
                'can_sell_as_serving' => $variant->can_sell_as_serving,
                
                // Prices
                'selling_price_per_pic' => (float)($variant->selling_price_per_pic ?? 0),
                'selling_price_per_serving' => (float)($variant->selling_price_per_serving ?? 0),
                
                // Metrics (In PICs)
                'opening_stock' => (float)($variant->opening_stock ?? 0),
                'total_received_pics' => (float)($variant->opening_stock ?? 0), // Start with opening stock
                'total_sold_pics' => 0,
                'current_stock_pics' => 0,
                'total_servings_available' => 0,
                'minimum_stock' => $variant->minimum_stock_level ?? 0,
                
                // Cost tracking
                'total_cost' => 0,
                'unit_cost' => 0,
                
                // Financials
                'revenue_pic' => 0,
                'revenue_serving' => 0,
                'expected_profit' => 0,
                'current_profit' => 0,
                'profit_difference' => 0,
                'nearest_expiry' => null,
                'revenue_generated' => 0,
            ];
        }

        // Add Received Quantity from Transfers
        foreach ($allTransfers as $transfer) {
            $key = $transfer->product_id . '_' . $transfer->product_variant_id;
            
            if (!isset($stockMap[$key])) continue;

            $itemsPerPackage = $transfer->productVariant->items_per_package ?? 1;
            
            $picsReceived = 0;
            if ($transfer->quantity_unit === 'packages') {
                $picsReceived = (float)$transfer->quantity_transferred * (float)$itemsPerPackage;
            } else {
                $picsReceived = (float)$transfer->quantity_transferred;
            }
            
            $stockMap[$key]['total_received_pics'] += $picsReceived;
            $stockMap[$key]['total_cost'] += (float)($transfer->total_cost ?? 0);
            
            // Track nearest expiry
            if ($transfer->expiry_date) {
                $expDate = \Carbon\Carbon::parse($transfer->expiry_date);
                if (!$stockMap[$key]['nearest_expiry'] || $expDate->lt($stockMap[$key]['nearest_expiry'])) {
                    $stockMap[$key]['nearest_expiry'] = $expDate;
                }
            }
        }

        // Calculate average unit cost for each item
        foreach ($stockMap as &$item) {
            if ($item['total_received_pics'] > 0) {
                $item['unit_cost'] = $item['total_cost'] / $item['total_received_pics'];
            }
        }
        
        // 3. Process Sales (Deduct from Stock)
        // ----------------------------------------
        foreach ($allSales as $sale) {
            $meta = $sale->service_specific_data;
            if (!isset($meta['product_id']) || !isset($meta['product_variant_id'])) {
                continue;
            }
            
            $key = $meta['product_id'] . '_' . $meta['product_variant_id'];
            
            if (isset($stockMap[$key])) {
                $item = &$stockMap[$key];
                $qtySold = (float)$sale->quantity;
                $unitPrice = (float)$sale->unit_price_tsh;
                
                // Precision check for unit matching
                $isPicSale = abs($unitPrice - $item['selling_price_per_pic']) < 100;
                
                if ($isPicSale) {
                    $item['total_sold_pics'] += $qtySold;
                } else {
                    $ratio = $item['servings_per_pic'] > 0 ? $item['servings_per_pic'] : 1;
                    $item['total_sold_pics'] += ($qtySold / $ratio);
                }

                $item['revenue_generated'] += (float)$sale->total_price_tsh;
            }
        }
        
        // 4. Calculate Finals
        // ----------------------------------------
        foreach ($stockMap as &$item) {
            $item['current_stock_pics'] = max(0, $item['total_received_pics'] - $item['total_sold_pics']);
            
            // Breakdown for display
            $item['full_bottles'] = floor($item['current_stock_pics'] + 0.0001); // Handle rounding
            $item['open_stock_fraction'] = max(0, $item['current_stock_pics'] - $item['full_bottles']);
            $item['open_servings'] = round($item['open_stock_fraction'] * $item['servings_per_pic']);

            $item['sold_full_bottles'] = floor($item['total_sold_pics'] + 0.0001);
            $item['sold_fraction'] = max(0, $item['total_sold_pics'] - $item['sold_full_bottles']);
            $item['sold_servings'] = round($item['sold_fraction'] * $item['servings_per_pic']);
            
            $item['total_servings_available'] = floor(($item['current_stock_pics'] * $item['servings_per_pic']) + 0.0001);
            
            // Financial Potential
            if ($item['selling_price_per_serving'] > 0) {
                 $item['revenue_potential'] = $item['total_servings_available'] * $item['selling_price_per_serving'];
            } else {
                 $item['revenue_potential'] = $item['current_stock_pics'] * $item['selling_price_per_pic']; 
            }
            
            $item['revenue_pic'] = $item['current_stock_pics'] * $item['selling_price_per_pic'];
            $item['revenue_serving'] = $item['revenue_potential'];

            // Profit Calculations
            $currentCost = $item['current_stock_pics'] * $item['unit_cost'];
            $item['current_profit'] = $item['revenue_serving'] - $currentCost;
            
            $soldCost = $item['total_sold_pics'] * $item['unit_cost'];
            $item['profit_generated'] = $item['revenue_generated'] - $soldCost;
            
            $item['profit_per_pic'] = max(0, $item['selling_price_per_pic'] - $item['unit_cost']);
            if ($item['servings_per_pic'] > 0) {
                $item['profit_per_serving'] = max(0, $item['selling_price_per_serving'] - ($item['unit_cost'] / $item['servings_per_pic']));
            } else {
                $item['profit_per_serving'] = 0;
            }

        }
        
        $myStock = collect($stockMap)->where('total_received_pics', '>', 0)->sortBy('product_name');
        
        // Group by category for tabs
        $categories = $myStock->groupBy('product_category')->sortKeys();
        
        return view('dashboard.bar-keeper-stock', compact('myStock', 'role', 'categories'));
    }

    /**
     * Update product variant prices (Both PIC and Serving)
     */
    public function updatePrices(Request $request, \App\Models\ProductVariant $variant)
    {
        $request->validate([
            'selling_price_per_pic' => 'required|numeric|min:0',
            'selling_price_per_serving' => 'nullable|numeric|min:0',
        ]);

        try {
            $oldPic = $variant->selling_price_per_pic;
            $oldServing = $variant->selling_price_per_serving;
            
            $variant->selling_price_per_pic = $request->selling_price_per_pic;
            $variant->selling_price_per_serving = $request->selling_price_per_serving ?? 0;
            
            // Record Price History
            $history = is_array($variant->price_history) ? $variant->price_history : (json_decode($variant->price_history, true) ?? []);
            $history[] = [
                'old_pic' => $oldPic,
                'old_serving' => $oldServing,
                'new_pic' => $request->selling_price_per_pic,
                'new_serving' => $request->selling_price_per_serving ?? 0,
                'user' => Auth::guard('staff')->user()->name,
                'date' => now()->toDateTimeString()
            ];
            $variant->price_history = $history;
            $variant->save();

            return response()->json([
                'success' => true,
                'message' => 'Prices updated successfully for ' . $variant->product->name
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating prices: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get usage tracking for a bar item
     */
    public function getBarItemUsageTrack(\App\Models\ProductVariant $variant)
    {
        $movements = collect();

        // 1. Transfers (IN)
        $transfers = \App\Models\StockTransfer::where('product_variant_id', $variant->id)
            ->where('status', 'completed')
            ->get();

        foreach ($transfers as $t) {
            $itemsPerPackage = $variant->items_per_package ?? 1;
            $qty = ($t->quantity_unit === 'packages') ? ($t->quantity_transferred * $itemsPerPackage) : $t->quantity_transferred;
            
            $movements->push([
                'date' => $t->received_at ?: $t->updated_at,
                'type' => 'Stock Received',
                'change' => (float)$qty,
                'is_addition' => true,
                'user' => $t->receivedBy->name ?? 'System',
                'notes' => $t->notes ?: 'Transfer from warehouse'
            ]);
        }

        // 2. Sales (OUT)
        $barCategories = ['drinks', 'alcoholic_beverage', 'non_alcoholic_beverage', 'water', 'juices', 'energy_drinks', 'food', 'restaurant', 'spirits', 'wines', 'cocktails', 'hot_beverages'];
        
        $sales = \App\Models\ServiceRequest::where('status', 'completed')
            ->whereHas('service', function($q) use ($barCategories) {
                $q->whereIn('category', $barCategories);
            })
            ->get();

        foreach ($sales as $s) {
            $meta = $s->service_specific_data;
            if (isset($meta['product_variant_id']) && $meta['product_variant_id'] == $variant->id) {
                // Check if it was a pic sale or serving sale
                $unitPrice = (float)$s->unit_price_tsh;
                $isPicSale = abs($unitPrice - (float)$variant->selling_price_per_pic) < 100;
                
                $qtyDeduction = 0;
                if ($isPicSale) {
                    $qtyDeduction = $s->quantity;
                } else {
                    $servingsPerPic = $variant->servings_per_pic > 0 ? $variant->servings_per_pic : 1;
                    $qtyDeduction = ($s->quantity / $servingsPerPic);
                }

                $movements->push([
                    'date' => $s->completed_at ?: $s->created_at,
                    'type' => $s->is_walk_in ? 'Walk-in Sale' : 'Room Service',
                    'change' => (float)$qtyDeduction,
                    'is_addition' => false,
                    'user' => 'System',
                    'notes' => $s->booking ? 'Room ' . $s->booking->room->room_number : 'Direct Sale'
                ]);
            }
        }

        // 2.5 Price Changes (LOGS)
        $history = is_array($variant->price_history) ? $variant->price_history : (json_decode($variant->price_history, true) ?? []);
        foreach ($history as $h) {
            $movements->push([
                'date' => $h['date'],
                'type' => 'Price Change',
                'change' => (float)$h['new_pic'],
                'is_addition' => null, // neutral
                'user' => $h['user'],
                'notes' => "PIC: " . number_format($h['old_pic']) . " -> " . number_format($h['new_pic']) . " | Glass: " . number_format($h['old_serving']) . " -> " . number_format($h['new_serving']),
                'is_price_change' => true
            ]);
        }

        // 3. Sort by date and calculate running balance
        $ascMovements = $movements->sortBy('date');
        $runningBalance = 0;
        $formatted = [];

        foreach ($ascMovements as $m) {
            if (isset($m['is_addition']) && $m['is_addition'] !== null) {
                if ($m['is_addition']) {
                    $runningBalance += $m['change'];
                } else {
                    $runningBalance -= $m['change'];
                }
            }

            $ratio = (float)($variant->servings_per_pic ?? 1);
            
            // Format Change
            $changeText = "";
            if ($m['type'] === 'Price Change') {
                $changeText = "New Price";
            } else {
                $prefix = $m['is_addition'] ? '+' : '-';
                $val = (float)$m['change'];
                if ($ratio > 1 && ($val - (int)$val) > 0.001) {
                    $cFull = floor($val);
                    $cGls = round(($val - $cFull) * $ratio);
                    $changeText = $prefix . ($cFull > 0 ? $cFull . ' Bot ' : '') . ($cGls > 0 ? ($cFull > 0 ? '+ ' : '') . $cGls . ' gls' : '');
                } else {
                    $changeText = $prefix . number_format($val, 1);
                }
            }

            // Format Balance
            $balanceText = "";
            if ($m['type'] === 'Price Change') {
                $balanceText = number_format((float)$m['change'], 0);
            } else {
                $bAbs = abs($runningBalance);
                if ($ratio > 1 && ($bAbs - (int)$bAbs) > 0.001) {
                    $bFull = floor($bAbs);
                    $bGls = round(($bAbs - $bFull) * $ratio);
                    $balanceText = ($bFull > 0 ? $bFull . ' Bot ' : '') . ($bGls > 0 ? ($bFull > 0 ? '+ ' : '') . $bGls . ' gls' : '');
                } else {
                    $balanceText = number_format($runningBalance, 1);
                }
            }

            $formatted[] = [
                'date' => \Carbon\Carbon::parse($m['date'])->format('M d, Y H:i'),
                'type' => $m['type'],
                'quantity' => $changeText,
                'balance' => $balanceText,
                'unit' => ($m['type'] === 'Price Change') ? 'TSH' : 'Pic',
                'user' => $m['user'],
                'notes' => $m['notes'],
                'is_addition' => $m['is_addition'],
                'is_price_change' => isset($m['is_price_change']) ? $m['is_price_change'] : false
            ];
        }

        return response()->json([
            'success' => true,
            'item_name' => $variant->product->name . ' (' . $variant->measurement . ')',
            'movements' => array_reverse($formatted)
        ]);
    }

    /**
     * Display all recorded items (walk-in sales and ceremony consumption)
     */
    public function recordedItems(Request $request)
    {
        $user = Auth::guard('staff')->user();
        $role = strtolower($user->role ?? 'bar_keeper');

        $query = ServiceRequest::with(['service', 'dayService'])
            ->where('is_walk_in', true)
            ->orderBy('created_at', 'desc');

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Filter by payment status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by type (ceremony vs walk-in)
        if ($request->filled('item_type')) {
            if ($request->item_type === 'ceremony') {
                $query->whereNotNull('day_service_id');
            } elseif ($request->item_type === 'walk_in') {
                $query->whereNull('day_service_id');
            }
        }

        $recordedItems = $query->paginate(20);

        // Calculate statistics
        $stats = [
            'total_items' => ServiceRequest::where('is_walk_in', true)->count(),
            'total_paid' => ServiceRequest::where('is_walk_in', true)->where('payment_status', 'paid')->count(),
            'total_unpaid' => ServiceRequest::where('is_walk_in', true)->where('payment_status', 'pending')->count(),
            'total_revenue' => ServiceRequest::where('is_walk_in', true)->where('payment_status', 'paid')->sum('total_price_tsh'),
            'ceremony_items' => ServiceRequest::where('is_walk_in', true)->whereNotNull('day_service_id')->count(),
            'walk_in_items' => ServiceRequest::where('is_walk_in', true)->whereNull('day_service_id')->count(),
        ];

        return view('dashboard.bar-keeper-recorded-items', compact('recordedItems', 'stats', 'role'));
    }

    /**
     * Update minimum stock level for a product variant
     */
    public function updateMinimumStock(Request $request, $variantId)
    {
        // Manager should not set Inventory Alert as per user request
        if (strtolower(Auth::guard('staff')->user()->role->name ?? '') === 'manager') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Managers cannot adjust stock thresholds.',
            ], 403);
        }

        $request->validate([
            'minimum_stock' => 'required|numeric|min:0',
        ]);

        $variant = \App\Models\ProductVariant::findOrFail($variantId);
        
        $variant->update([
            'minimum_stock_level' => $request->minimum_stock,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Minimum stock level updated successfully!',
        ]);
    }

    /**
     * Print walk-in docket/receipt
     */
    public function printDocket(ServiceRequest $serviceRequest)
    {
        $user = Auth::guard('staff')->user();
        
        // Get all items for this walk-in customer (group by walk_in_name if multiple items)
        $items = collect([$serviceRequest]);
        
        // If this is part of a multi-item order, get all items
        if ($serviceRequest->is_walk_in && $serviceRequest->walk_in_name) {
            $items = ServiceRequest::where('is_walk_in', true)
                ->where('walk_in_name', $serviceRequest->walk_in_name)
                ->where('status', '!=', 'cancelled')
                ->whereDate('created_at', $serviceRequest->created_at->toDateString())
                ->get();
        }
        
        $totalAmount = $items->sum('total_price_tsh');
        $walkInName = $serviceRequest->walk_in_name ?? 'General Walk-in';
        $guestName = str_contains(strtolower($walkInName), 'walk-in') ? $walkInName : $walkInName; // Keep as is for guest name display
        // Actually, just keep it clean
        $guestName = $walkInName;
        
        return view('dashboard.print-walk-in-docket', compact('items', 'totalAmount', 'guestName', 'serviceRequest'));
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
        
        // Determine Requested By (Waiter or Bar)
        $requestedBy = 'Bar Keeper';
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
