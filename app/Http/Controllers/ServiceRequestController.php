<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceRequest;
use App\Models\Booking;
use App\Models\User;
use App\Services\CurrencyExchangeService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ServiceRequestController extends Controller
{
    /**
     * Determine the role based on current route
     */
    private function getRole()
    {
        $routeName = request()->route()->getName() ?? '';
        return str_starts_with($routeName, 'admin.') ? 'manager' : 'reception';
    }

    /**
     * Get available services for customer
     */
    public function getAvailableServices()
    {
        // Exclude food and drinks categories as they have an independent ordering section
        $excludeCategories = [
            'food', 
            'restaurant', 
            'alcoholic_beverage', 
            'non_alcoholic_beverage', 
            'water', 
            'juices', 
            'energy_drinks',
            'drinks',
            'beverage'
        ];

        $services = Service::where('is_active', true)
            ->whereNotIn('category', $excludeCategories)
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'services' => $services
        ]);
    }

    /**
     * Customer requests a service
     */
    public function requestService(Request $request)
    {
        try {
            try {
                $request->validate([
                    'booking_id' => 'required_without:is_walk_in|exists:bookings,id',
                    'is_walk_in' => 'nullable|boolean',
                    'walk_in_name' => 'nullable|string|max:255',
                    'service_id' => 'required',
                    'product_id' => 'nullable|integer',
                    'product_variant_id' => 'nullable|integer',
                    'selling_method' => 'nullable|string|in:pic,serving',
                    'quantity' => 'nullable|integer|min:1',
                    'adult_quantity' => 'nullable|integer|min:0',
                    'child_quantity' => 'nullable|integer|min:0',
                    'guest_request' => 'nullable|string|max:500',
                    'service_specific_data' => 'nullable|array',
                    'item_name' => 'nullable|string', // For custom named items
                    'day_service_id' => 'nullable|exists:day_services,id',
                    'payment_timing' => 'nullable|string|in:immediate,later',
                ]);
            } catch (\Illuminate\Validation\ValidationException $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }

            // Get authenticated user (guest or staff)
            $user = Auth::guard('guest')->user() ?? Auth::guard('staff')->user() ?? Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Please log in.',
                ], 401);
            }

            // Verify booking belongs to logged-in customer (unless it's a walk-in by staff)
            $booking = null;
            if (!$request->is_walk_in) {
                $booking = Booking::where('id', $request->booking_id)
                    ->where('guest_email', $user->email)
                    ->first();
                    
                if (!$booking) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Booking not found or does not belong to you.',
                    ], 404);
                }
            }

            // Get service
            $service = null;
            
            // Check if this is a generic bar or food order by looking up the service
            if (is_numeric($request->service_id)) {
                $service = Service::find($request->service_id);
            }
            
            // If service not found by ID, try to find by name for generic orders
            if (!$service) {
                // Try to find Generic Bar Order
                $barService = Service::where('name', 'Generic Bar Order')->first();
                if ($barService && $request->product_id) {
                    $service = $barService;
                }
                
                // Try to find Generic Food Order
                if (!$service) {
                    $foodService = Service::where('name', 'Generic Food Order')->first();
                    if ($foodService && isset($request->service_specific_data['food_id'])) {
                        $service = $foodService;
                    }
                }
            }
            
            // Determine if this is a generic bar or food order
            $isGenericBar = $service && $service->name === 'Generic Bar Order';
            $isGenericFood = $service && $service->name === 'Generic Food Order';

            // Get quantities
            $adultQuantity = $request->adult_quantity ?? 0;
            $childQuantity = $request->child_quantity ?? 0;
            $quantity = $request->quantity ?? 1;

            // Calculate total price and determine name
            $unitPrice = 0;
            $totalPrice = 0;
            $itemName = $request->item_name;
            $additionalData = $request->input('service_specific_data', []);

            if ($isGenericBar && $request->product_id) {
                // Logic for Bar Products
                $product = \App\Models\Product::find($request->product_id);
                $variant = \App\Models\ProductVariant::find($request->product_variant_id);
                $sellingMethod = $request->selling_method ?? 'pic';
                
                if ($product && $variant) {
                    $unitName = $sellingMethod === 'serving' ? ($variant->selling_unit_name ?? 'Glass') : 'Bottle';
                    $itemName = $product->name . " ($unitName)";
                    if ($variant->measurement) {
                        $itemName .= " (" . $variant->measurement . ")";
                    }

                    $additionalData['product_id'] = $product->id;
                    $additionalData['product_variant_id'] = $variant->id;
                    $additionalData['selling_method'] = $sellingMethod;
                    
                    // Get price from variant primarily
                    if ($sellingMethod === 'serving') {
                        $unitPrice = (float)$variant->selling_price_per_serving;
                    } else {
                        $unitPrice = (float)$variant->selling_price_per_pic;
                    }
                    
                    // Fallback to StockReceipt if variant prices are not set
                    if ($unitPrice <= 0) {
                        $latestReceipt = \App\Models\StockReceipt::where('product_variant_id', $variant->id)
                            ->orderBy('received_date', 'desc')
                            ->first();
                        $unitPrice = $latestReceipt ? (float)$latestReceipt->selling_price_per_bottle : 0;
                    }
                    
                    $totalPrice = $unitPrice * $quantity;

                    // --- Real-time Stock Validation ---
                    $allTransfers = \App\Models\StockTransfer::where('status', 'completed')
                        ->where('product_variant_id', $variant->id)
                        ->get();
                    
                    // Get all sales for this specific variant
                    $allSales = \App\Models\ServiceRequest::where('status', 'completed')
                        ->get()
                        ->filter(function($s) use ($variant) {
                            return isset($s->service_specific_data['product_variant_id']) && 
                                   (int)$s->service_specific_data['product_variant_id'] === $variant->id;
                        });

                    $currentStockPics = 0;
                    foreach ($allTransfers as $t) {
                        $itemsPerPkg = $t->productVariant->items_per_package ?? 1;
                        $pics = ($t->quantity_unit === 'packages') ? ($t->quantity_transferred * $itemsPerPkg) : $t->quantity_transferred;
                        $currentStockPics += $pics;
                    }

                    foreach ($allSales as $s) {
                        $meta = $s->service_specific_data;
                        $meth = $meta['selling_method'] ?? 'pic';
                        if ($meth === 'pic') {
                            $currentStockPics -= $s->quantity;
                        } else {
                            $servingsPerPic = $variant->servings_per_pic > 0 ? $variant->servings_per_pic : 1;
                            $currentStockPics -= ($s->quantity / $servingsPerPic);
                        }
                    }

                    // Calculate consumption of current request
                    $requestedCons = ($sellingMethod === 'pic') ? $quantity : ($quantity / ($variant->servings_per_pic > 0 ? $variant->servings_per_pic : 1));
                    
                    if ($requestedCons > ($currentStockPics + 0.001)) {
                        $availableWhole = floor($currentStockPics);
                        return response()->json([
                            'success' => false, 
                            'message' => "Insufficient stock. Only " . ($availableWhole > 0 ? $availableWhole : '0') . " bottles/units available."
                        ], 400);
                    }
                    // --- End Stock Validation ---
                } else {
                    return response()->json(['success' => false, 'message' => 'Product variant not found.'], 404);
                }
            } elseif ($isGenericFood) {
                // Cleaned up Food ordering logic via Recipes
                $extraData = $request->input('service_specific_data', []);
                $foodId = $extraData['food_id'] ?? null;
                
                if ($foodId) {
                    $recipe = \App\Models\Recipe::find($foodId);
                    if ($recipe) {
                        $itemName = $recipe->name;
                        $unitPrice = (float)$recipe->selling_price ?? 0;
                        $totalPrice = $unitPrice * $quantity;
                        $additionalData['food_id'] = $recipe->id;
                    }
                }
            } else if ($service) {
                // Standard Service Logic
                $itemName = $service->name;
                if ($service->is_free_for_internal) {
                    $totalPrice = 0;
                } else {
                    $serviceAgeGroup = $service->age_group ?? 'both';
                    
                    if ($serviceAgeGroup === 'both' && $service->child_price_tsh && $service->child_price_tsh > 0) {
                        // Service supports both adult and child pricing
                        $adultTotal = ($service->price_tsh ?? 0) * $adultQuantity;
                        $childTotal = ($service->child_price_tsh ?? 0) * $childQuantity;
                        $totalPrice = $adultTotal + $childTotal;
                        
                        // If no adult/child quantities provided, use single quantity
                        if ($adultQuantity === 0 && $childQuantity === 0) {
                            $totalPrice = ($service->price_tsh ?? 0) * $quantity;
                        }
                    } else if ($serviceAgeGroup === 'child') {
                        $unitPriceRaw = $service->child_price_tsh ?? $service->price_tsh ?? 0;
                        $totalPrice = $unitPriceRaw * $quantity;
                    } else {
                        $unitPriceRaw = $service->price_tsh ?? 0;
                        $totalPrice = $unitPriceRaw * $quantity;
                    }
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Service or item not found.',
                ], 404);
            }
            
            $booking = $booking ?? null; // Ensure $booking is defined for notifications even if null
            
            // Calculate unit price for storage (average or single)
            $unitPrice = $quantity > 0 ? ($totalPrice / $quantity) : 0;

            // Validate service-specific fields if required (only if $service exists)
            $serviceSpecificData = [];
            if ($request->has('service_specific_data') && is_array($request->service_specific_data)) {
                $serviceSpecificData = $request->service_specific_data;
                
                // Validate required fields based on service configuration
                if ($service && $service->required_fields && is_array($service->required_fields)) {
                    foreach ($service->required_fields as $field) {
                        if (isset($field['required']) && $field['required']) {
                            $fieldName = $field['name'];
                            if (!isset($serviceSpecificData[$fieldName]) || empty($serviceSpecificData[$fieldName])) {
                                return response()->json([
                                    'success' => false,
                                    'message' => "Field '{$field['label']}' is required for this service.",
                                ], 422);
                            }
                        }
                    }
                }
            }

            // Create service request - walk-ins can be immediate or later
            $isWalkIn = $request->input('is_walk_in', false);
            $paymentTiming = $request->input('payment_timing', 'immediate'); // immediate or later
            $dayServiceId = $request->input('day_service_id');

            // Determine status and payment status
            $status = 'pending';
            $paymentStatus = 'pending';

            if ($isWalkIn) {
                if ($dayServiceId) {
                    // If it's a walk-in linked to a day service (ceremony), it's considered completed immediately
                    $status = 'completed';
                    $paymentStatus = ($paymentTiming === 'immediate') ? 'paid' : 'pending';
                } else {
                    // Standard walk-in
                    $status = ($paymentTiming === 'later' ? 'approved' : 'completed');
                    $paymentStatus = ($paymentTiming === 'later' ? 'pending' : 'paid');
                }
            }
            
            $serviceRequest = ServiceRequest::create([
                'booking_id' => $isWalkIn ? null : $booking->id,
                'service_id' => $service ? $service->id : null,
                'guest_request' => $request->guest_request,
                'service_specific_data' => array_merge($serviceSpecificData, $additionalData, [
                    'adult_quantity' => $adultQuantity,
                    'child_quantity' => $childQuantity,
                    'item_name' => $itemName // Store the actual name for orders
                ]),
                'quantity' => $quantity,
                'unit_price_tsh' => $unitPrice,
                'total_price_tsh' => $totalPrice,
                'status' => $status,
                'payment_status' => $paymentStatus,
                'is_walk_in' => $isWalkIn,
                'walk_in_name' => $request->walk_in_name,
                'day_service_id' => $dayServiceId,
                'requested_at' => now(),
                'approved_at' => $isWalkIn ? now() : null,
                'approved_by' => $isWalkIn ? Auth::guard('staff')->id() : null,
                'completed_at' => ($isWalkIn && ($paymentTiming === 'immediate' || $dayServiceId)) ? now() : null,
            ]);

            // Create notification for service request
            if (!$isWalkIn) {
                try {
                    $notificationService = new NotificationService();
                    $notificationService->createServiceRequestNotification($serviceRequest->load(['booking.room', 'service']));
                    
                    // Also notify customer that their request was submitted
                    $notificationService->createServiceRequestConfirmationNotification($serviceRequest->load(['booking.room', 'service']), $user);
                } catch (\Exception $e) {
                    \Log::error('Failed to create service request notification: ' . $e->getMessage());
                }
            }

            // Send email notification (send immediately)
            if (!$isWalkIn && isset($booking->guest_email)) {
                try {
                    // Check if guest has notifications enabled
                    $guest = \App\Models\Guest::where('email', $booking->guest_email)->first();
                    if (!$guest || $guest->isNotificationEnabled('service_request')) {
                        \Illuminate\Support\Facades\Mail::to($booking->guest_email)
                            ->send(new \App\Mail\ServiceRequestSubmittedMail($serviceRequest->fresh()->load(['booking.room', 'service'])));
                    }
                } catch (\Exception $e) {
                    \Log::error('Failed to send service request submitted email: ' . $e->getMessage());
                }
            }

            // Send email notification to managers and super admins
            try {
                $managersAndAdmins = \App\Models\Staff::whereIn('role', ['manager', 'super_admin'])
                    ->where('is_active', true)
                    ->get();
                
                foreach ($managersAndAdmins as $staff) {
                    // Check if user has notifications enabled
                    if ($staff->isNotificationEnabled('service_request')) {
                        try {
                            \Illuminate\Support\Facades\Mail::to($staff->email)
                                ->send(new \App\Mail\StaffNewServiceRequestMail($serviceRequest->fresh()->load(['booking.room', 'service'])));
                        } catch (\Exception $e) {
                            \Log::error('Failed to send service request email to staff: ' . $staff->email . ' - ' . $e->getMessage());
                        }
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Failed to send service request emails to managers/admins: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Service request submitted! Call to confirm: 0677155156 - Reception, 0677155157 - Manager.',
                'service_request' => $serviceRequest->load('service')
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Service request error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your request. Please try again or contact reception.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Reception dashboard - show overview with service requests
     */
    public function receptionDashboard()
    {
        try {
            $user = Auth::guard('staff')->user();
            
            // Get today's date range
            $today = Carbon::today();
            $thisMonth = Carbon::now()->startOfMonth();
            
            // Get exchange rate for currency conversion
            $exchangeRate = 2500; // Default fallback rate
            try {
                $currencyService = new CurrencyExchangeService();
                $exchangeRate = $currencyService->getUsdToTshRate();
            } catch (\Exception $e) {
                \Log::warning('Failed to get exchange rate, using default', ['error' => $e->getMessage()]);
            }
            
            // Calculate total revenue (bookings + service requests + day services)
            $totalBookingRevenueTZS = Booking::whereIn('payment_status', ['paid', 'partial'])
                ->whereNotNull('amount_paid')
                ->where('amount_paid', '>', 0)
                ->get()
                ->sum(function($booking) use ($exchangeRate) {
                    return ($booking->amount_paid ?? 0) * ($booking->locked_exchange_rate ?? $exchangeRate);
                });
            $totalServiceRevenueTZS = ServiceRequest::where('status', 'completed')->sum('total_price_tsh');
            $totalDayServiceRevenueTZS = \App\Models\DayService::where('payment_status', 'paid')->get()->sum(function($s) use ($exchangeRate) {
                $amount = $s->amount_paid ?? $s->amount ?? 0;
                return $s->guest_type === 'tanzanian' ? $amount : ($amount * ($s->exchange_rate ?? $exchangeRate));
            });
            $totalRevenueTZS = $totalBookingRevenueTZS + $totalServiceRevenueTZS + $totalDayServiceRevenueTZS;
            
            // Calculate today's revenue (using paid_at if available)
            $todayBookingRevenueTZS = Booking::whereIn('payment_status', ['paid', 'partial'])
                ->whereNotNull('amount_paid')
                ->where('amount_paid', '>', 0)
                ->where(function($q) use ($today) {
                    $q->whereDate('paid_at', $today)
                      ->orWhere(function($subQ) use ($today) {
                          $subQ->whereNull('paid_at')
                               ->whereDate('created_at', $today);
                      });
                })
                ->get()
                ->sum(function($booking) use ($exchangeRate) {
                    return ($booking->amount_paid ?? 0) * ($booking->locked_exchange_rate ?? $exchangeRate);
                });
            $todayServiceRevenueTZS = ServiceRequest::where('status', 'completed')
                ->whereDate('completed_at', $today)
                ->sum('total_price_tsh');
            $todayDayServiceRevenueTZS = \App\Models\DayService::where('payment_status', 'paid')
                ->whereDate('paid_at', $today)
                ->get()->sum(function($s) use ($exchangeRate) {
                    $amount = $s->amount_paid ?? $s->amount ?? 0;
                    return $s->guest_type === 'tanzanian' ? $amount : ($amount * ($s->exchange_rate ?? $exchangeRate));
                });
            $todayRevenueTZS = $todayBookingRevenueTZS + $todayServiceRevenueTZS + $todayDayServiceRevenueTZS;
            
            // Statistics
            $stats = [
                'total_rooms' => \App\Models\Room::count(),
                'total_bookings' => Booking::count(),
                'total_revenue' => $totalRevenueTZS,
                'today_revenue' => $todayRevenueTZS,
                'total_active_guests' => Booking::where('check_in_status', 'checked_in')
                    ->where('check_out', '>=', $today)
                    ->sum('number_of_guests') ?: Booking::where('check_in_status', 'checked_in')->count(),
                'pending_requests' => ServiceRequest::where('status', 'pending')->count(),
                'approved_requests' => ServiceRequest::where('status', 'approved')->count(),
                'today_requests' => ServiceRequest::whereDate('requested_at', $today)->count(),
                'pending_extensions' => Booking::where('extension_status', 'pending')->count(),
                'room_issues' => \App\Models\IssueReport::where('status', '!=', 'resolved')->count(),
            ];
            
            // Recent bookings
            $recentBookings = Booking::with(['room', 'company'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
            
            // Pending Requests
            $pendingRequests = ServiceRequest::with(['booking.room', 'service'])
                ->where('status', 'pending')
                ->orderBy('requested_at', 'asc')
                ->limit(5)
                ->get();
            
            // Today's Requests
            $todayRequests = ServiceRequest::with(['booking.room', 'service'])
                ->whereDate('requested_at', $today)
                ->orderBy('requested_at', 'desc')
                ->limit(5)
                ->get();
            
            // Pending Extensions
            $pendingExtensions = Booking::where('extension_status', 'pending')
                ->with(['room'])
                ->orderBy('extension_requested_at', 'asc')
                ->get();

            // Revenue chart data (last 6 months)
            $revenueData = [];
            for ($i = 5; $i >= 0; $i--) {
                $month = Carbon::now()->subMonths($i);
                $monthStart = $month->copy()->startOfMonth();
                $monthEnd = $month->copy()->endOfMonth();
                
                $mBookingRevTZS = Booking::whereBetween('created_at', [$monthStart, $monthEnd])
                    ->where('payment_status', 'paid')
                    ->get()
                    ->sum(function($b) use ($exchangeRate) {
                        return ($b->amount_paid ?? $b->total_price ?? 0) * ($b->locked_exchange_rate ?? $exchangeRate);
                    });
                $mServiceRevTZS = ServiceRequest::where('status', 'completed')
                    ->whereBetween('completed_at', [$monthStart, $monthEnd])
                    ->sum('total_price_tsh');
                $mDayServiceRevTZS = \App\Models\DayService::where('payment_status', 'paid')
                    ->whereBetween('paid_at', [$monthStart, $monthEnd])
                    ->get()->sum(function($s) use ($exchangeRate) {
                        $amount = $s->amount_paid ?? $s->amount ?? 0;
                        return $s->guest_type === 'tanzanian' ? $amount : ($amount * ($s->exchange_rate ?? $exchangeRate));
                    });
                
                $revenueData[] = [
                    'month' => $month->format('M Y'),
                    'revenue' => $mBookingRevTZS + $mServiceRevTZS + $mDayServiceRevTZS
                ];
            }

            // Booking status chart data
            $bookingStatusData = [
                'Pending' => Booking::where('status', 'pending')->count(),
                'Confirmed' => Booking::where('status', 'confirmed')->count(),
                'Completed' => Booking::where('status', 'completed')->count(),
                'Cancelled' => Booking::where('status', 'cancelled')->count(),
            ];
            
            $role = $this->getRole();
            return view('dashboard.reception-dashboard', [
                'role' => $role,
                'userName' => $user->name ?? 'Reception Staff',
                'userRole' => $role === 'manager' ? 'Manager' : 'Reception',
                'stats' => $stats,
                'recentBookings' => $recentBookings,
                'pendingRequests' => $pendingRequests,
                'todayRequests' => $todayRequests,
                'pendingExtensions' => $pendingExtensions,
                'revenueData' => $revenueData,
                'bookingStatusData' => $bookingStatusData,
                'exchangeRate' => $exchangeRate,
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Reception dashboard error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load dashboard.');
        }
    }

    /**
     * Reception: View all service requests
     */
    public function receptionIndex(Request $request)
    {
        $query = ServiceRequest::with(['booking.room', 'service', 'approvedBy'])
            ->orderBy('requested_at', 'desc');

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by booking
        if ($request->has('booking_id') && $request->booking_id) {
            $query->where('booking_id', $request->booking_id);
        }

        $serviceRequests = $query->paginate(20);

        $stats = [
            'pending' => ServiceRequest::where('status', 'pending')->count(),
            'approved' => ServiceRequest::where('status', 'approved')->count(),
            'completed' => ServiceRequest::where('status', 'completed')->count(),
            'total_today' => ServiceRequest::whereDate('requested_at', today())->count(),
        ];

        // Get exchange rate
        $currencyService = new CurrencyExchangeService();
        $exchangeRate = $currencyService->getUsdToTshRate();

        // Prepare service requests data for JavaScript
        $serviceRequestsData = $serviceRequests->map(function($req) use ($currencyService) {
            return [
                'id' => $req->id,
                'booking_reference' => $req->booking->booking_reference ?? 'WALK-IN',
                'guest_name' => $req->booking->guest_name ?? $req->walk_in_name ?? 'Walk-in Guest',
                'guest_email' => $req->booking->guest_email ?? 'N/A',
                'guest_phone' => $req->booking->guest_phone ?? 'N/A',
                'service_name' => $req->service->name,
                'service_category' => $req->service->category,
                'quantity' => $req->quantity,
                'unit' => $req->service->unit,
                'unit_price_tsh' => $req->unit_price_tsh,
                'unit_price_usd' => $currencyService->convertTshToUsd($req->unit_price_tsh),
                'total_price_tsh' => $req->total_price_tsh,
                'total_price_usd' => $currencyService->convertTshToUsd($req->total_price_tsh),
                'status' => $req->status,
                'guest_request' => $req->guest_request,
                'item_name' => $req->service_specific_data['item_name'] ?? $req->service->name,
                'service_specific_data' => $req->service_specific_data,
                'reception_notes' => $req->reception_notes,
                'requested_at' => $req->requested_at->format('F d, Y \a\t g:i A'),
                'approved_at' => $req->approved_at ? $req->approved_at->format('F d, Y \a\t g:i A') : null,
                'completed_at' => $req->completed_at ? $req->completed_at->format('F d, Y \a\t g:i A') : null,
                'room_number' => $req->booking?->room?->room_number ?? 'N/A',
                'room_type' => $req->booking?->room?->room_type ?? 'N/A',
            ];
        })->values();

        $role = $this->getRole();
        return view('dashboard.service-requests', [
            'role' => $role,
            'userName' => Auth::user()->name ?? ($role === 'admin' ? 'Admin' : 'Reception Staff'),
            'userRole' => $role === 'admin' ? 'Admin' : 'Reception',
            'serviceRequests' => $serviceRequests,
            'stats' => $stats,
            'serviceRequestsData' => $serviceRequestsData,
            'exchangeRate' => $exchangeRate,
        ]);
    }

    /**
     * Reception: Update service request status
     */
    public function updateStatus(Request $request, ServiceRequest $serviceRequest)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,preparing,completed,cancelled',
            'reception_notes' => 'nullable|string|max:1000',
        ]);

        $updateData = [
            'status' => $request->status,
            'reception_notes' => $request->reception_notes,
        ];

        if ($request->status === 'approved' && $serviceRequest->status !== 'approved') {
            $updateData['approved_at'] = now();
            $updateData['approved_by'] = Auth::id();
        }

        if ($request->status === 'completed' && !$serviceRequest->completed_at) {
            $updateData['completed_at'] = now();
        }

        // Update the service request first
        $serviceRequest->update($updateData);
        $serviceRequest = $serviceRequest->fresh();
        
        // Mark the notification as read when action is taken (approve/cancel/complete)
        if (in_array($request->status, ['approved', 'completed', 'cancelled'])) {
            try {
                $notificationService = new NotificationService();
                $notificationService->markNotificationAsReadByNotifiable(
                    ServiceRequest::class,
                    $serviceRequest->id,
                    'service_request',
                    'reception'
                );
            } catch (\Exception $e) {
                \Log::error('Failed to mark service request notification as read: ' . $e->getMessage());
            }
        }
        
        // Now recalculate booking total service charges after status update
        // Only include approved and completed service requests
        $booking = $serviceRequest->booking->fresh();
        $totalServiceCharges = $booking->serviceRequests()
            ->whereIn('status', ['approved', 'completed'])
            ->sum('total_price_tsh');
        
        $booking->update([
            'total_service_charges_tsh' => $totalServiceCharges
        ]);
        
        // Log for debugging
        \Log::info('Service request status updated', [
            'service_request_id' => $serviceRequest->id,
            'old_status' => $serviceRequest->getOriginal('status'),
            'new_status' => $serviceRequest->status,
            'booking_id' => $booking->id,
            'total_service_charges_tsh' => $totalServiceCharges,
            'approved_completed_count' => $booking->serviceRequests()->whereIn('status', ['approved', 'completed'])->count(),
        ]);

        // Notify customer when status changes
        try {
            $booking = $serviceRequest->booking->fresh();
            $user = \App\Models\Guest::where('email', $booking->guest_email)->first();
            
            if ($user && in_array($request->status, ['approved', 'completed', 'cancelled'])) {
                $notificationService = new NotificationService();
                
                if ($request->status === 'approved') {
                    $notificationService->createServiceRequestStatusUpdateNotification($serviceRequest, $user, 'approved');
                } elseif ($request->status === 'completed') {
                    $notificationService->createServiceRequestStatusUpdateNotification($serviceRequest, $user, 'completed');
                } elseif ($request->status === 'cancelled') {
                    $notificationService->createServiceRequestStatusUpdateNotification($serviceRequest, $user, 'cancelled');
                }

                // Send email notification (send immediately)
                try {
                    // Check if guest has notifications enabled
                    $guest = \App\Models\Guest::where('email', $booking->guest_email)->first();
                    if (!$guest || $guest->isNotificationEnabled('service_request')) {
                        \Illuminate\Support\Facades\Mail::to($booking->guest_email)
                            ->send(new \App\Mail\ServiceRequestStatusMail($serviceRequest->fresh()->load(['booking.room', 'service']), $request->status));
                    }
                } catch (\Exception $e) {
                    \Log::error('Failed to send service request status email: ' . $e->getMessage());
                }

                // Send email notification to managers and super admins
                try {
                    $managersAndAdmins = \App\Models\Staff::whereIn('role', ['manager', 'super_admin'])
                        ->where('is_active', true)
                        ->get();
                    
                    foreach ($managersAndAdmins as $staff) {
                        // Check if user has notifications enabled
                        if ($staff->isNotificationEnabled('service_request')) {
                            try {
                                \Illuminate\Support\Facades\Mail::to($staff->email)
                                    ->send(new \App\Mail\StaffServiceRequestStatusMail($serviceRequest->fresh()->load(['booking.room', 'service']), $request->status));
                            } catch (\Exception $e) {
                                \Log::error('Failed to send service request status email to staff: ' . $staff->email . ' - ' . $e->getMessage());
                            }
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error('Failed to send service request status emails to managers/admins: ' . $e->getMessage());
                }

            }
        } catch (\Exception $e) {
            \Log::error('Failed to create service request status update notification: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Service request status updated successfully!',
            'service_request' => $serviceRequest->load('service', 'booking')
        ]);
    }

    /**
     * Settle payment for a POS order (Used by Bar Keeper/Chef)
     */
    public function settlePayment(Request $request, ServiceRequest $serviceRequest)
    {
        $request->validate([
            'payment_method' => 'required|string',
            'payment_reference' => 'nullable|string',
        ]);

        $isRoomCharge = $request->payment_method === 'room_charge';
        
        // Prevent room charge for walk-ins
        if ($isRoomCharge && $serviceRequest->is_walk_in) {
            return response()->json([
                'success' => false,
                'message' => 'Walk-in orders cannot be charged to a room. Please select a valid payment method.'
            ], 422);
        }
        
        // Items to settle (start with this one)
        $itemsToSettle = [$serviceRequest];
        $totalCollected = $serviceRequest->total_price_tsh;

        // If walk-in, find all other pending items for this name
        if ($serviceRequest->is_walk_in && $serviceRequest->walk_in_name) {
            $others = ServiceRequest::where('is_walk_in', true)
                ->where('walk_in_name', $serviceRequest->walk_in_name)
                ->where('payment_status', 'pending')
                ->where('id', '!=', $serviceRequest->id)
                ->get();
            
            foreach ($others as $other) {
                $itemsToSettle[] = $other;
                $totalCollected += $other->total_price_tsh;
            }
        } elseif ($serviceRequest->booking_id) {
            // Also settle all other pending items for this room booking
            $others = ServiceRequest::where('booking_id', $serviceRequest->booking_id)
                ->where('payment_status', 'pending')
                ->where('id', '!=', $serviceRequest->id)
                ->get();
            
            foreach ($others as $other) {
                $itemsToSettle[] = $other;
                $totalCollected += $other->total_price_tsh;
            }
        }

        // Apply settlement to all identified items
        foreach ($itemsToSettle as $item) {
            $item->update([
                'payment_status' => $isRoomCharge ? 'room_charge' : 'paid',
                'payment_method' => $request->payment_method,
                'payment_reference' => $request->payment_reference,
                'status' => 'completed',
                'completed_at' => now(),
            ]);
        }

        // Log activity for the group
        try {
            $user = Auth::guard('staff')->user();
            if ($user) {
                $itemNames = collect($itemsToSettle)->map(fn($i) => $i->service->name ?? ($i->service_specific_data['item_name'] ?? 'Item'))->implode(', ');
                \App\Models\ActivityLog::create([
                    'user_id' => $user->id,
                    'user_type' => get_class($user),
                    'action' => 'payment_received',
                    'description' => "Received payment of " . number_format($totalCollected) . " TSH for: " . $itemNames . " (Guest: " . ($serviceRequest->walk_in_name ?? 'Walk-in') . ")",
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            }
        } catch (\Exception $e) {}

        return response()->json([
            'success' => true,
            'message' => 'Payment recorded and all pending items for this guest settled!',
            'count' => count($itemsToSettle),
            'total' => $totalCollected
        ]);
    }

    /**
     * Get service requests for a booking
     */
    public function getBookingServices(Booking $booking)
    {
        // Get authenticated user (guest or staff)
        $user = Auth::guard('guest')->user() ?? Auth::user();
        
        // Verify booking belongs to logged-in customer
        if ($user && ($user->role === 'customer' || $user instanceof \App\Models\Guest) && $booking->guest_email !== $user->email) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.'
            ], 403);
        }

        $serviceRequests = $booking->serviceRequests()
            ->with('service')
            ->orderBy('requested_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'service_requests' => $serviceRequests,
            'total_service_charges_tsh' => $booking->total_service_charges_tsh ?? 0
        ]);
    }

    /**
     * Generate checkout bill
     */
    public function generateCheckoutBill(Booking $booking)
    {
        // Get authenticated user from staff or guest guard
        $user = auth()->guard('staff')->user() ?? auth()->guard('guest')->user();
        
        if (!$user) {
            abort(403, 'Unauthorized access. Please login.');
        }
        
        // Verify booking belongs to logged-in customer or user is reception/admin/manager
        if ($user instanceof \App\Models\Guest && $booking->guest_email !== $user->email) {
            abort(403, 'Unauthorized access.');
        }

        // Check if this is a guest viewing their own corporate booking
        $isStaff = $user instanceof \App\Models\Staff;
        $isCorporateBooking = $booking->is_corporate_booking ?? false;
        $isGuestViewingCorporate = !$isStaff && $isCorporateBooking;
        $paymentResponsibility = $booking->payment_responsibility ?? 'company';
        $isGuestWithSelfPaidServices = $isGuestViewingCorporate && $paymentResponsibility === 'self';
        $isGuestWithCompanyPaidServices = $isGuestViewingCorporate && $paymentResponsibility === 'company';
        
        // For staff viewing corporate bookings, also handle payment responsibility
        $isStaffViewingCorporate = $isStaff && $isCorporateBooking;
        $isStaffViewingSelfPaid = $isStaffViewingCorporate && $paymentResponsibility === 'self';
        $isStaffViewingCompanyPaid = $isStaffViewingCorporate && $paymentResponsibility === 'company';

        $serviceRequests = $booking->serviceRequests()
            ->whereIn('status', ['approved', 'completed'])
            ->with('service')
            ->get();

        // Calculate total bill in TZS (room price converted + service charges + extension charges)
        // Use locked exchange rate from booking, or fallback to current rate if not set (for old bookings)
        $exchangeRate = $booking->locked_exchange_rate;
        if (!$exchangeRate) {
            // Fallback for old bookings that don't have locked rate
            $currencyService = new \App\Services\CurrencyExchangeService();
            $exchangeRate = $currencyService->getUsdToTshRate();
        }
        
        // Initialize display variables for corporate bookings
        $displayRoomPriceTsh = null;
        $displayBaseRoomPriceUsd = null;
        $displayExtensionCostUsd = null;
        $displayExtensionCostTsh = null;
        $displayExtensionNights = null;
        $displayOriginalNights = null;
        
        // For guests with company-paid services, skip room charge calculations for guest's bill
        // Also for staff viewing company-paid corporate bookings
        // But we still need to calculate display values for transparency
        if ($isGuestWithCompanyPaidServices || $isStaffViewingCompanyPaid) {
            // Calculate display values for bill breakdown (even though company pays)
            $originalCheckOutDate = $booking->original_check_out 
                ? \Carbon\Carbon::parse($booking->original_check_out) 
                : \Carbon\Carbon::parse($booking->check_out);
            $displayOriginalNights = $booking->check_in->diffInDays($originalCheckOutDate);
            $displayBaseRoomPriceUsd = $booking->room ? ($booking->room->price_per_night * $displayOriginalNights) : 0;
            $displayRoomPriceTsh = $displayBaseRoomPriceUsd * $exchangeRate;
            
            $displayExtensionCostUsd = 0;
            $displayExtensionCostTsh = 0;
            $displayExtensionNights = 0;
            if ($booking->extension_status === 'approved' && $booking->original_check_out && $booking->extension_requested_to) {
                $originalCheckOut = \Carbon\Carbon::parse($booking->original_check_out);
                $requestedCheckOut = \Carbon\Carbon::parse($booking->extension_requested_to);
                $displayExtensionNights = $originalCheckOut->diffInDays($requestedCheckOut);
                if ($displayExtensionNights > 0 && $booking->room) {
                    $displayExtensionCostUsd = $booking->room->price_per_night * $displayExtensionNights;
                    $displayExtensionCostTsh = $displayExtensionCostUsd * $exchangeRate;
                }
            }
            
            // Guest's bill (zero since company pays)
            $roomPriceTsh = 0;
            $baseRoomPriceUsd = 0;
            $extensionCostUsd = 0;
            $extensionCostTsh = 0;
            $extensionNights = 0;
            $originalNights = 0;
            $totalServiceChargesTsh = 0; // Company pays for services too
            $totalBillTsh = 0;
            $amountPaidUsd = 0;
            $amountPaidTsh = 0;
            $outstandingBalanceTsh = 0;
        } elseif ($isGuestWithSelfPaidServices || $isStaffViewingSelfPaid) {
            // For guests with self-paid services, only calculate service charges (exclude room charges)
            $roomPriceTsh = 0;
            $baseRoomPriceUsd = 0;
            $extensionCostUsd = 0;
            $extensionCostTsh = 0;
            $extensionNights = 0;
            $originalNights = $booking->check_in->diffInDays($booking->check_out);
            
            // For self-paid responsibility, the guest is responsible for ALL services
            // even if they were charged to the room (payment_method = room_charge)
            $selfPaidServiceRequests = $serviceRequests;
            $totalServiceChargesTsh = $selfPaidServiceRequests->sum('total_price_tsh');
            
            // Calculate total bill (only services, no room charges)
            $totalBillTsh = $totalServiceChargesTsh;
            
            // Calculate amount paid (only from service payments, not room payments)
            // For self-paid services, amount_paid in booking might include room payments
            // So we need to calculate from service requests instead
            $amountPaidUsd = 0;
            $amountPaidTsh = 0;
            foreach ($selfPaidServiceRequests as $sr) {
                if ($sr->payment_status === 'paid') {
                    $amountPaidTsh += $sr->total_price_tsh;
                }
            }
            $amountPaidUsd = $amountPaidTsh > 0 ? $amountPaidTsh / $exchangeRate : 0;
            
            // Calculate outstanding balance
            $outstandingBalanceTsh = max(0, $totalBillTsh - $amountPaidTsh);
        } elseif ($isStaffViewingCorporate) {
            // Staff viewing corporate booking - show company pays for rooms
            // Only show what guest owes (services if self-paid, nothing if company-paid)
            if ($paymentResponsibility === 'self') {
                // Guest owes unpaid services only
                $roomPriceTsh = 0;
                $baseRoomPriceUsd = 0;
                $extensionCostUsd = 0;
                $extensionCostTsh = 0;
                $extensionNights = 0;
                $originalNights = $booking->check_in->diffInDays($booking->check_out);
                
                // Only count unpaid self-paid service requests
                $unpaidServiceRequests = $serviceRequests->filter(function($sr) {
                    $paymentMethod = $sr->payment_method ?? null;
                    $paymentStatus = $sr->payment_status ?? 'pending';
                    return $paymentMethod !== 'room_charge' && $paymentStatus !== 'paid';
                });
                $totalServiceChargesTsh = $unpaidServiceRequests->sum('total_price_tsh');
                $totalBillTsh = $totalServiceChargesTsh;
                $amountPaidUsd = 0;
                $amountPaidTsh = 0;
                $outstandingBalanceTsh = max(0, $totalBillTsh - $amountPaidTsh);
            } else {
                // Company pays everything - guest owes nothing
                $roomPriceTsh = 0;
                $baseRoomPriceUsd = 0;
                $extensionCostUsd = 0;
                $extensionCostTsh = 0;
                $extensionNights = 0;
                $originalNights = $booking->check_in->diffInDays($booking->check_out);
                $totalServiceChargesTsh = 0;
                $totalBillTsh = 0;
                $amountPaidUsd = 0;
                $amountPaidTsh = 0;
                $outstandingBalanceTsh = 0;
            }
        } else {
            // For individual bookings (non-corporate), calculate full bill including room charges
            // Calculate extension cost if extension was approved
            $extensionCostUsd = 0;
            $extensionNights = 0;
            
            if ($booking->extension_status === 'approved') {
                if ($booking->extension_requested_to && $booking->original_check_out) {
                    $originalCheckOut = \Carbon\Carbon::parse($booking->original_check_out);
                    $requestedCheckOut = \Carbon\Carbon::parse($booking->extension_requested_to);
                    $extensionNights = $originalCheckOut->diffInDays($requestedCheckOut);
                    
                    if ($extensionNights > 0 && $booking->room) {
                        $extensionCostUsd = $booking->room->price_per_night * $extensionNights;
                    }
                }
                
                // Ensure extension cost doesn't exceed total_price, and base price is the remainder
                $extensionCostUsd = min($extensionCostUsd, $booking->total_price);
                $baseRoomPriceUsd = $booking->total_price - $extensionCostUsd;
            } else {
                // No extension, whole total_price is the base room price
                $baseRoomPriceUsd = $booking->total_price;
            }
            
            $extensionCostTsh = $extensionCostUsd * $exchangeRate;
            $roomPriceTsh = $baseRoomPriceUsd * $exchangeRate;

            // Calculate original nights (excluding extension) for display
            $originalCheckOutDate = $booking->original_check_out 
                ? \Carbon\Carbon::parse($booking->original_check_out) 
                : \Carbon\Carbon::parse($booking->check_out);
            $originalNights = $booking->check_in->diffInDays($originalCheckOutDate);
            
            $totalServiceChargesTsh = $serviceRequests->sum('total_price_tsh');
            
            // Calculate total bill (room + extension + services)
            $totalBillTsh = $roomPriceTsh + $extensionCostTsh + $totalServiceChargesTsh;
            
            // Calculate amount paid (Booking deposit/payment + any settled service payments)
            $amountPaidUsd = $booking->amount_paid ?? 0;
            $amountPaidTsh = $amountPaidUsd * $exchangeRate;
            
            // Add payments for completed/paid services
            foreach ($serviceRequests as $sr) {
                if ($sr->payment_status === 'paid') {
                    $amountPaidTsh += $sr->total_price_tsh;
                }
            }
            
            // Update USD for display consistency
            $amountPaidUsd = $amountPaidTsh / $exchangeRate;
            
            // Calculate outstanding balance
            $outstandingBalanceTsh = max(0, $totalBillTsh - $amountPaidTsh);
        }

        // Update booking with total bill
        $booking->update([
            'total_service_charges_tsh' => $totalServiceChargesTsh,
            'total_bill_tsh' => $totalBillTsh
        ]);

        // Get user role for display
        $userRole = 'Customer';
        if ($user instanceof \App\Models\Staff) {
            $rawRole = $user->role ?? '';
            $normalizedRole = strtolower(str_replace([' ', '_'], '', trim($rawRole)));
            
            if ($normalizedRole === 'superadmin' || $rawRole === 'super_admin' || strtolower($rawRole) === 'super admin') {
                $userRole = 'Super Administrator';
            } elseif ($normalizedRole === 'manager' || $rawRole === 'manager') {
                $userRole = 'Manager';
            } elseif ($normalizedRole === 'reception' || $rawRole === 'reception') {
                $userRole = 'Reception';
            }
        } elseif ($user instanceof \App\Models\Guest) {
            $userRole = 'Customer';
        }

        // Get role for view
        $roleForView = 'customer';
        if ($user instanceof \App\Models\Staff) {
            $rawRole = $user->role ?? '';
            $normalizedRole = strtolower(str_replace([' ', '_'], '', trim($rawRole)));
            
            if ($normalizedRole === 'superadmin' || $rawRole === 'super_admin' || strtolower($rawRole) === 'super admin') {
                $roleForView = 'super_admin';
            } elseif ($normalizedRole === 'manager' || $rawRole === 'manager') {
                $roleForView = 'manager';
            } elseif ($normalizedRole === 'reception' || $rawRole === 'reception') {
                $roleForView = 'reception';
            }
        } elseif ($user instanceof \App\Models\Guest) {
            $roleForView = 'customer';
        }
        
        // Display values are already calculated above if needed
        
        return view('dashboard.checkout-bill', [
            'role' => $roleForView,
            'userName' => $user->name ?? 'Guest User',
            'userRole' => $userRole,
            'booking' => $booking->load(['room', 'company']),
            'serviceRequests' => $serviceRequests,
            'roomPriceTsh' => $roomPriceTsh,
            'baseRoomPriceUsd' => $baseRoomPriceUsd,
            'extensionCostUsd' => $extensionCostUsd,
            'extensionCostTsh' => $extensionCostTsh,
            'extensionNights' => $extensionNights,
            'originalNights' => $originalNights ?? 0,
            'totalServiceChargesTsh' => $totalServiceChargesTsh,
            'totalBillTsh' => $totalBillTsh,
            'amountPaidTsh' => $amountPaidTsh,
            'amountPaidUsd' => $amountPaidUsd,
            'outstandingBalanceTsh' => $outstandingBalanceTsh,
            'exchangeRate' => $exchangeRate,
            'isGuestViewingCorporate' => $isGuestViewingCorporate,
            'isGuestWithSelfPaidServices' => $isGuestWithSelfPaidServices,
            'isGuestWithCompanyPaidServices' => $isGuestWithCompanyPaidServices,
            'isStaffViewingCorporate' => $isStaffViewingCorporate,
            'isStaffViewingSelfPaid' => $isStaffViewingSelfPaid,
            'isStaffViewingCompanyPaid' => $isStaffViewingCompanyPaid,
            'paymentResponsibility' => $paymentResponsibility,
            'displayRoomPriceTsh' => $displayRoomPriceTsh,
            'displayBaseRoomPriceUsd' => $displayBaseRoomPriceUsd,
            'displayExtensionCostUsd' => $displayExtensionCostUsd,
            'displayExtensionCostTsh' => $displayExtensionCostTsh,
            'displayExtensionNights' => $displayExtensionNights,
            'displayOriginalNights' => $displayOriginalNights,
        ]);
    }

    /**
     * Show service history for customer
     */
    public function serviceHistory(Request $request)
    {
        $user = Auth::user();
        
        // Get all service requests for customer's bookings
        $query = ServiceRequest::whereHas('booking', function($q) use ($user) {
            $q->where('guest_email', $user->email);
        })
        ->with(['service', 'booking.room'])
        ->orderBy('created_at', 'desc');
        
        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        // Filter by service
        if ($request->has('service_id') && $request->service_id) {
            $query->where('service_id', $request->service_id);
        }
        
        $serviceRequests = $query->paginate(20);
        
        // Get all services for filter
        $services = Service::where('is_active', true)->orderBy('name')->get();
        
        // Get statistics
        $stats = [
            'total' => ServiceRequest::whereHas('booking', function($q) use ($user) {
                $q->where('guest_email', $user->email);
            })->count(),
            'pending' => ServiceRequest::whereHas('booking', function($q) use ($user) {
                $q->where('guest_email', $user->email);
            })->where('status', 'pending')->count(),
            'approved' => ServiceRequest::whereHas('booking', function($q) use ($user) {
                $q->where('guest_email', $user->email);
            })->where('status', 'approved')->count(),
            'completed' => ServiceRequest::whereHas('booking', function($q) use ($user) {
                $q->where('guest_email', $user->email);
            })->where('status', 'completed')->count(),
        ];
        
        // Get exchange rate
        $currencyService = new CurrencyExchangeService();
        $exchangeRate = $currencyService->getUsdToTshRate();
        
        return view('dashboard.customer-service-history', [
            'role' => 'customer',
            'userName' => $user->name ?? 'Guest User',
            'userRole' => 'Customer',
            'user' => $user,
            'serviceRequests' => $serviceRequests,
            'services' => $services,
            'stats' => $stats,
            'exchangeRate' => $exchangeRate,
        ]);
    }

    /**
     * Settle pending bar/restaurant usage for a ceremony.
     * Filters by category based on the user's role (Chef vs Bar Keeper).
     */
    public function settleCeremonyUsage(Request $request)
    {
        $request->validate([
            'day_service_id' => 'required|exists:day_services,id',
            'payment_method' => 'required|string',
            'payment_reference' => 'nullable|string',
        ]);

        $dayServiceId = $request->day_service_id;
        $paymentMethod = $request->payment_method;
        $paymentReference = $request->payment_reference;

        $query = ServiceRequest::where('day_service_id', $dayServiceId)
            ->where('payment_status', 'pending');

        // Filter based on user role to ensure separation of concerns
        $user = auth()->guard('staff')->user();
        if ($user) {
            if ($user->role === 'head_chef') {
                $query->whereHas('service', function($q) {
                    $q->whereIn('category', ['food', 'restaurant', 'kitchen']);
                });
            } elseif ($user->role === 'bar_keeper') {
                $query->whereHas('service', function($q) {
                    $q->whereIn('category', ['alcoholic_beverage', 'non_alcoholic_beverage', 'water', 'juices', 'energy_drinks', 'soft_drinks', 'beers', 'wines', 'spirits', 'cocktails', 'drinks', 'liquor']);
                });
            }
        }

        $pendingRequests = $query->get();

        if ($pendingRequests->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No pending usage found for your department.',
            ], 404);
        }

        $isRoomCharge = $paymentMethod === 'room_charge';
        foreach ($pendingRequests as $req) {
            $req->update([
                'payment_status' => $isRoomCharge ? 'room_charge' : 'paid',
                'status' => 'completed',
                'payment_method' => $paymentMethod,
                'payment_reference' => $paymentReference,
                'completed_at' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Ceremony consumption settled successfully!',
            'count' => $pendingRequests->count(),
        ]);
    }
}
