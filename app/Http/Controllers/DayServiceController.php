<?php

namespace App\Http\Controllers;

use App\Models\DayService;
use App\Models\Staff;
use App\Services\CurrencyExchangeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DayServiceController extends Controller
{
    protected $currencyService;

    public function __construct(CurrencyExchangeService $currencyService)
    {
        $this->currencyService = $currencyService;
    }
    /**
     * Show registration form
     */
    public function create(Request $request)
    {
        $user = Auth::guard('staff')->user();
        $role = strtolower($user->role ?? '');
        
        // Get pre-selected service type from query parameter
        $selectedServiceType = $request->query('service_type');
        
        // Get active services from catalog
        $services = \App\Models\ServiceCatalog::active()
            ->orderBy('display_order')
            ->orderBy('service_name')
            ->get();
        
        // If swimming is selected and service exists, check if we should redirect to dedicated page
        if ($selectedServiceType === 'swimming') {
            $swimmingService = $services->where('service_key', 'swimming')->first();
            if ($swimmingService) {
                // Check if Service model has adult/child pricing for swimming
                $serviceModel = \App\Models\Service::where('name', 'LIKE', '%swimming%')
                    ->where('is_active', true)
                    ->where(function($q) {
                        $q->where('age_group', 'both')
                          ->orWhere('age_group', 'adult');
                    })
                    ->first();
                
                // Only redirect if Service model exists with adult/child pricing
                if ($serviceModel && $serviceModel->age_group === 'both' && $serviceModel->child_price_tsh && $serviceModel->child_price_tsh > 0) {
                    return redirect()->route($role === 'reception' ? 'reception.day-services.swimming.create' : 'admin.day-services.swimming.create');
                }
            }
        }
        
        // Get Service model pricing for swimming services (if available)
        $serviceModel = null;
        if ($selectedServiceType === 'swimming' || $selectedServiceType === 'swimming_with_bucket' || $selectedServiceType === 'swimming-with-bucket') {
            $serviceModel = \App\Models\Service::where('name', 'LIKE', '%swimming%')
                ->where('is_active', true)
                ->where(function($q) {
                    $q->where('age_group', 'both')
                      ->orWhere('age_group', 'adult');
                })
                ->first();
        }
        
        return view('dashboard.day-service-register', compact('role', 'services', 'selectedServiceType', 'serviceModel'));
    }

    /**
     * Show swimming service registration page (handles both Swimming and Swimming with Bucket)
     */
    public function swimmingService()
    {
        $user = Auth::guard('staff')->user();
        $role = strtolower($user->role ?? 'manager');
        
        // Get swimming services from catalog (both swimming and swimming_with_bucket)
        $swimmingServices = \App\Models\ServiceCatalog::whereIn('service_key', ['swimming', 'swimming_with_bucket', 'swimming-with-bucket', 'swimming_with_floating_trey'])
            ->where('is_active', true)
            ->orderBy('display_order')
            ->get();
        
        if ($swimmingServices->isEmpty()) {
            return redirect()->route($role === 'reception' ? 'reception.service-catalog.index' : 'admin.service-catalog.index')
                ->with('error', 'Swimming services are not configured in the catalog. Please register them first.');
        }
        
        // Get Service models for adult/child pricing
        $swimmingServiceModel = \App\Models\Service::where('name', 'LIKE', '%swimming%')
            ->where('name', 'NOT LIKE', '%bucket%')
            ->where('is_active', true)
            ->where(function($q) {
                $q->where('age_group', 'both')
                  ->orWhere('age_group', 'adult');
            })
            ->first();
        
        $swimmingWithBucketServiceModel = \App\Models\Service::where('name', 'LIKE', '%swimming%')
            ->where('name', 'LIKE', '%bucket%')
            ->where('is_active', true)
            ->where(function($q) {
                $q->where('age_group', 'both')
                  ->orWhere('age_group', 'adult');
            })
            ->first();
        
        // Get ServiceCatalog pricing as fallback
        $swimmingCatalog = $swimmingServices->where('service_key', 'swimming')->first();
        $swimmingWithBucketCatalog = $swimmingServices->whereIn('service_key', ['swimming_with_bucket', 'swimming-with-bucket'])->first();
        
        // Get exchange rate
        try {
            $exchangeRate = $this->currencyService->getUsdToTshRate();
        } catch (\Exception $e) {
            $exchangeRate = 2487.81; // Default fallback rate
        }
        
        return view('dashboard.day-service-swimming-service', compact(
            'role', 
            'swimmingServices', 
            'swimmingServiceModel', 
            'swimmingWithBucketServiceModel', 
            'swimmingCatalog',
            'swimmingWithBucketCatalog',
            'exchangeRate'
        ));
    }

    /**
     * Show ceremony service registration page
     */
    public function ceremonyService()
    {
        $user = Auth::guard('staff')->user();
        $role = strtolower($user->role ?? 'manager');
        
        // Get ceremony package from catalog (check for ceremony, ceremony_package, birthday, ceremory typo, etc.)
        $ceremonyPackage = \App\Models\ServiceCatalog::where(function($query) {
                $query->where('service_key', 'LIKE', '%ceremony%')
                      ->orWhere('service_key', 'LIKE', '%ceremory%') // Common typo: ceremory
                      ->orWhere('service_key', 'LIKE', '%birthday%')
                      ->orWhere('service_key', 'LIKE', '%package%');
            })
            ->where('is_active', true)
            ->first();
        
        if (!$ceremonyPackage) {
            return redirect()->route($role === 'reception' ? 'reception.service-catalog.index' : 'admin.service-catalog.index')
                ->with('error', 'Ceremony package is not configured in the catalog. Please register it first.');
        }
        
        // Get package items from catalog (if configured)
        $packageItemsFromCatalog = $ceremonyPackage->package_items ?? [];
        
        // Default package items if not configured in catalog
        $defaultPackageItems = [
            'food' => 'Food',
            'swimming' => 'Swimming',
            'drinks' => 'Drinks',
            'photos' => 'Photos',
            'decoration' => 'Decoration'
        ];
        
        // Use catalog items if available, otherwise use defaults
        $packageItems = [];
        if (!empty($packageItemsFromCatalog) && is_array($packageItemsFromCatalog)) {
            // Convert array items to key-value pairs with labels
            $itemLabels = [
                'food' => 'Food',
                'swimming' => 'Swimming',
                'drinks' => 'Drinks',
                'photos' => 'Photos',
                'decoration' => 'Decoration'
            ];
            
            foreach ($packageItemsFromCatalog as $item) {
                $packageItems[$item] = $itemLabels[$item] ?? ucfirst($item);
            }
        } else {
            // Use default items
            $packageItems = $defaultPackageItems;
        }
        
        // Get exchange rate
        try {
            $exchangeRate = $this->currencyService->getUsdToTshRate();
        } catch (\Exception $e) {
            $exchangeRate = 2487.81; // Default fallback rate
        }
        
        return view('dashboard.day-service-ceremony', compact('role', 'ceremonyPackage', 'packageItems', 'exchangeRate'));
    }

    /**
     * Store new day service
     */
    public function store(Request $request)
    {
        // Get all service keys from catalog for validation
        $serviceKeys = \App\Models\ServiceCatalog::pluck('service_key')->toArray();
        // Fallback to default service types if catalog is empty
        $defaultServiceTypes = ['swimming', 'restaurant', 'bar', 'other'];
        $allowedServiceTypes = !empty($serviceKeys) ? implode(',', $serviceKeys) : implode(',', $defaultServiceTypes);
        
        $validated = $request->validate([
            'service_type' => 'required|string|max:100',
            'guest_name' => 'required|string|max:255',
            'guest_phone' => 'nullable|string|max:20',
            'guest_email' => 'nullable|email|max:255',
            'number_of_people' => 'required|integer|min:1',
            'adult_quantity' => 'nullable|integer|min:0',
            'child_quantity' => 'nullable|integer|min:0',
            'service_date' => 'required|date',
            'service_time' => 'required',
            'items_ordered' => 'nullable|string',
            'package_items' => 'nullable|string', // Can be JSON string or array
            'amount' => 'required|numeric|min:0',
            'payment_status' => 'required|in:pending,paid',
            'payment_method' => 'nullable|in:cash,card,mobile,bank,online,other',
            'payment_provider' => 'nullable|string|max:100',
            'payment_reference' => 'nullable|string|max:100',
            'amount_paid' => 'nullable|numeric|min:0',
            'guest_type' => 'required|in:tanzanian,international',
            'notes' => 'nullable|string',
            'discount_type' => 'nullable|in:none,percentage,fixed',
            'discount_value' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'discount_reason' => 'nullable|string|max:255',
        ]);
        
        // Calculate number_of_people from adult/child quantities if provided
        if ($request->has('adult_quantity') || $request->has('child_quantity')) {
            $adultQty = $validated['adult_quantity'] ?? 0;
            $childQty = $validated['child_quantity'] ?? 0;
            $validated['number_of_people'] = $adultQty + $childQty;
            
            // Ensure at least 1 person
            if ($validated['number_of_people'] < 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please enter at least 1 adult or child.',
                ], 422);
            }
        }

        // For swimming, payment is required
        if ($validated['service_type'] === 'swimming' && $validated['payment_status'] !== 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Payment is required for swimming/pool access.',
            ], 400);
        }

        // For swimming, payment details must be provided
        if ($validated['service_type'] === 'swimming') {
            if (empty($validated['payment_method']) || empty($validated['amount_paid'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment method and amount are required for swimming/pool access.',
                ], 400);
            }
        }

        // Get exchange rate if international guest
        $exchangeRate = null;
        if ($validated['guest_type'] === 'international') {
            $currencyService = new CurrencyExchangeService();
            $exchangeRate = $currencyService->getUsdToTshRate();
        }

        // Generate service reference
        $serviceReference = DayService::generateReference();

        // Get current staff member
        $staff = Auth::guard('staff')->user();

        // Handle package_items for ceremony services
        $packageItems = null;
        if ($request->has('package_items')) {
            $packageItemsData = $request->input('package_items');
            
            // If package_items is a JSON string, decode it
            if (is_string($packageItemsData)) {
                $packageItemsData = json_decode($packageItemsData, true);
            }
            
            // Ensure it's an array
            if (is_array($packageItemsData)) {
                $packageItems = $packageItemsData;
            }
        }
        
        // Prepare data
        $serviceData = [
            'service_reference' => $serviceReference,
            'service_type' => $validated['service_type'],
            'guest_name' => $validated['guest_name'],
            'guest_phone' => !empty($validated['guest_phone']) ? (str_starts_with($validated['guest_phone'], '+255') ? $validated['guest_phone'] : '+255' . ltrim($validated['guest_phone'], '0')) : null,
            'guest_email' => $validated['guest_email'] ?? null,
            'number_of_people' => $validated['number_of_people'],
            'adult_quantity' => $validated['adult_quantity'] ?? null,
            'child_quantity' => $validated['child_quantity'] ?? null,
            'service_date' => $validated['service_date'],
            'service_time' => $validated['service_time'],
            'items_ordered' => $validated['items_ordered'] ?? null,
            'package_items' => $packageItems ?? $validated['package_items'] ?? null,
            'amount' => $validated['amount'],
            'payment_status' => $validated['payment_status'],
            'payment_method' => $validated['payment_method'] ?? null,
            'payment_provider' => $validated['payment_provider'] ?? null,
            'payment_reference' => $validated['payment_reference'] ?? null,
            'amount_paid' => $validated['amount_paid'] ?? $validated['amount'],
            'exchange_rate' => $exchangeRate,
            'guest_type' => $validated['guest_type'],
            'notes' => $validated['notes'] ?? null,
            'discount_type' => $validated['discount_type'] ?? 'none',
            'discount_value' => $validated['discount_value'] ?? 0,
            'discount_amount' => $validated['discount_amount'] ?? 0,
            'discount_reason' => $validated['discount_reason'] ?? null,
            'registered_by' => $staff->id,
            'paid_at' => $validated['payment_status'] === 'paid' ? now() : null,
        ];

        $dayService = DayService::create($serviceData);

        // Generate receipt URL if paid
        $receiptUrl = null;
        if ($dayService->payment_status === 'paid') {
            $receiptUrl = route('admin.day-services.receipt', $dayService);
        }

        // Send email notification if email is provided
        $serviceKey = strtolower($validated['service_type'] ?? '');
        $isCeremonyService = str_contains($serviceKey, 'ceremony') || 
                            str_contains($serviceKey, 'ceremory') || 
                            str_contains($serviceKey, 'birthday') || 
                            str_contains($serviceKey, 'package');
        $isSwimmingService = str_contains($serviceKey, 'swimming');
        
        if (!empty($validated['guest_email'])) {
            try {
                if ($isCeremonyService) {
                    \Illuminate\Support\Facades\Mail::to($validated['guest_email'])
                        ->send(new \App\Mail\CeremonyServiceConfirmationMail($dayService));
                } elseif ($isSwimmingService) {
                    \Illuminate\Support\Facades\Mail::to($validated['guest_email'])
                        ->send(new \App\Mail\SwimmingServiceConfirmationMail($dayService));
                }
            } catch (\Exception $e) {
                // Log error but don't fail the registration
                \Log::error('Failed to send day service confirmation email: ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'message' => $dayService->payment_status === 'paid' 
                ? 'Day service registered and receipt generated successfully!' 
                : 'Day service registered. Payment pending.',
            'day_service' => $dayService,
            'receipt_url' => $receiptUrl,
        ]);
    }

    /**
     * List all day services
     */
    public function index(Request $request)
    {
        $query = DayService::with(['registeredBy', 'serviceRequests.service'])->orderBy('service_date', 'desc')->orderBy('service_time', 'desc');

        // Tab-based filtering
        if ($request->filled('tab')) {
            $tab = $request->tab;
            if ($tab === 'swimming') {
                $query->where('service_type', 'swimming');
            } elseif ($tab === 'swimming_with_bucket' || $tab === 'swimming-with-bucket') {
                $query->where(function($q) {
                    $q->where('service_type', 'swimming_with_bucket')
                      ->orWhere('service_type', 'swimming-with-bucket')
                      ->orWhere('service_type', 'swimming_with_floating_trey')
                      ->orWhere('service_type', 'swimming-with-floating-trey');
                });
            } elseif ($tab === 'ceremony' || $tab === 'ceremory') {
                $query->where(function($q) {
                    $q->where('service_type', 'ceremony')
                      ->orWhere('service_type', 'ceremory')
                      ->orWhere('service_type', 'LIKE', '%ceremony%')
                      ->orWhere('service_type', 'LIKE', '%ceremory%')
                      ->orWhere('service_type', 'LIKE', '%birthday%')
                      ->orWhere('service_type', 'LIKE', '%package%');
                });
            }
            // 'all' tab shows everything, no additional filter needed
        }

        // Filters
        if ($request->filled('service_type')) {
            $query->where('service_type', $request->service_type);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('date_from')) {
            $query->where('service_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('service_date', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('service_reference', 'like', "%{$search}%")
                  ->orWhere('guest_name', 'like', "%{$search}%")
                  ->orWhere('guest_phone', 'like', "%{$search}%")
                  ->orWhere('guest_email', 'like', "%{$search}%");
            });
        }

        $dayServices = $query->paginate(20);

        $user = Auth::guard('staff')->user();
        $role = strtolower($user->role ?? '');

        // Calculate statistics for the current tab
        $statsQuery = DayService::with(['serviceRequests']);
        
        // Apply same tab filter for statistics
        if ($request->filled('tab')) {
            $tab = $request->tab;
            if ($tab === 'swimming') {
                $statsQuery->where('service_type', 'swimming');
            } elseif ($tab === 'swimming_with_bucket' || $tab === 'swimming-with-bucket') {
                $statsQuery->where(function($q) {
                    $q->where('service_type', 'swimming_with_bucket')
                      ->orWhere('service_type', 'swimming-with-bucket');
                });
            } elseif ($tab === 'ceremony' || $tab === 'ceremory') {
                $statsQuery->where(function($q) {
                    $q->where('service_type', 'ceremony')
                      ->orWhere('service_type', 'ceremory')
                      ->orWhere('service_type', 'LIKE', '%ceremony%')
                      ->orWhere('service_type', 'LIKE', '%ceremory%')
                      ->orWhere('service_type', 'LIKE', '%birthday%')
                      ->orWhere('service_type', 'LIKE', '%package%');
                });
            }
        }

        // Apply same filters for statistics
        if ($request->filled('payment_status')) {
            $statsQuery->where('payment_status', $request->payment_status);
        }
        if ($request->filled('date_from')) {
            $statsQuery->where('service_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $statsQuery->where('service_date', '<=', $request->date_to);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $statsQuery->where(function($q) use ($search) {
                $q->where('service_reference', 'like', "%{$search}%")
                  ->orWhere('guest_name', 'like', "%{$search}%")
                  ->orWhere('guest_phone', 'like', "%{$search}%")
                  ->orWhere('guest_email', 'like', "%{$search}%");
            });
        }

        // Calculate statistics
        $totalServices = $statsQuery->count();
        $paidServices = (clone $statsQuery)->where('payment_status', 'paid')->count();
        $pendingServices = (clone $statsQuery)->where('payment_status', 'pending')->count();
        
        $totalRevenue = 0;
        $allServicesQuery = (clone $statsQuery)->get();
        foreach ($allServicesQuery as $service) {
            // 1. Add ceremony registration amount paid
            $amountPaid = $service->amount_paid ?? 0;
            if ($service->guest_type === 'international') {
                $exchangeRate = $service->exchange_rate ?? 2487.81;
                $amountPaid = $amountPaid * $exchangeRate;
            }
            $totalRevenue += $amountPaid;

            // 2. Add paid bar/restaurant usage
            $barPaid = $service->serviceRequests->where('payment_status', 'paid')->sum('total_price_tsh');
            $totalRevenue += $barPaid;
        }
        
        // Pending amount (amount not yet paid)
        $pendingAmount = 0;
        foreach ($allServicesQuery as $service) {
            // 1. Add unpaid ceremony registration amount
            $totalAmount = $service->amount ?? 0;
            $amountPaid = $service->amount_paid ?? 0;
            
            if ($service->guest_type === 'international') {
                $exchangeRate = $service->exchange_rate ?? 2487.81;
                $totalAmount = $totalAmount * $exchangeRate;
                $amountPaid = $amountPaid * $exchangeRate;
            }
            
            $pendingRegistration = max(0, $totalAmount - $amountPaid);
            $pendingAmount += $pendingRegistration;

            // 2. Add unpaid bar/restaurant usage
            $barUnpaid = $service->serviceRequests->where('payment_status', 'pending')->sum('total_price_tsh');
            $pendingAmount += $barUnpaid;
        }

        $statistics = [
            'total_services' => $totalServices,
            'paid_services' => $paidServices,
            'pending_services' => $pendingServices,
            'total_revenue' => $totalRevenue,
            'pending_amount' => $pendingAmount,
        ];

        return view('dashboard.day-services-list', compact('dayServices', 'role', 'statistics'));
    }

    /**
     * Show pending services (for restaurant/bar)
     */
    public function pending()
    {
        $pendingServices = DayService::with('registeredBy')
            ->where('payment_status', 'pending')
            ->whereIn('service_type', ['restaurant', 'bar'])
            ->orderBy('service_date', 'desc')
            ->orderBy('service_time', 'desc')
            ->paginate(20);

        $user = Auth::guard('staff')->user();
        $role = strtolower($user->role ?? '');

        return view('dashboard.day-services-pending', compact('pendingServices', 'role'));
    }

    /**
     * Show single day service
     */
    public function show(DayService $dayService)
    {
        $dayService->load(['registeredBy', 'serviceRequests.service']);
        return response()->json([
            'success' => true,
            'day_service' => $dayService,
        ]);
    }

    /**
     * Process payment for pending service
     */
    public function processPayment(Request $request, DayService $dayService)
    {
        if ($dayService->payment_status === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'This service is already paid.',
            ], 400);
        }

        $validated = $request->validate([
            'items_ordered' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,card,mobile,bank,online,other',
            'payment_provider' => 'nullable|string|max:100',
            'payment_reference' => 'nullable|string|max:100',
            'amount_paid' => 'required|numeric|min:0',
        ]);

        // Get exchange rate if international guest
        $exchangeRate = $dayService->exchange_rate;
        if (!$exchangeRate && $dayService->guest_type === 'international') {
            $currencyService = new CurrencyExchangeService();
            $exchangeRate = $currencyService->getUsdToTshRate();
        }

        $dayService->update([
            'items_ordered' => $validated['items_ordered'] ?? $dayService->items_ordered,
            'amount' => $validated['amount'],
            'payment_status' => 'paid',
            'payment_method' => $validated['payment_method'],
            'payment_provider' => $validated['payment_provider'] ?? null,
            'payment_reference' => $validated['payment_reference'] ?? null,
            'amount_paid' => $validated['amount_paid'],
            'exchange_rate' => $exchangeRate,
            'paid_at' => now(),
        ]);

        $receiptUrl = route('admin.day-services.receipt', $dayService);

        return response()->json([
            'success' => true,
            'message' => 'Payment processed and receipt generated successfully!',
            'day_service' => $dayService->fresh(),
            'receipt_url' => $receiptUrl,
        ]);
    }

    /**
     * Add additional items to existing ceremony service
     */
    public function addItems(Request $request, DayService $dayService)
    {
        // Only allow for ceremony services
        $serviceKey = strtolower($dayService->service_type ?? '');
        $isCeremonyService = str_contains($serviceKey, 'ceremony') || 
                            str_contains($serviceKey, 'ceremory') || 
                            str_contains($serviceKey, 'birthday') || 
                            str_contains($serviceKey, 'package');
        
        if (!$isCeremonyService) {
            return response()->json([
                'success' => false,
                'message' => 'Additional items can only be added to ceremony/package services.',
            ], 400);
        }

        $validated = $request->validate([
            'additional_items' => 'required|string', // JSON string
            'additional_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,card,mobile,bank,online,other',
            'payment_provider' => 'nullable|string|max:100',
            'payment_reference' => 'nullable|string|max:100',
            'additional_payment' => 'required|numeric|min:0',
        ]);

        // Decode additional items
        $additionalItemsData = json_decode($validated['additional_items'], true);
        if (!is_array($additionalItemsData)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid additional items format.',
            ], 400);
        }

        // Get existing package items
        $existingPackageItems = $dayService->package_items ?? [];
        if (!is_array($existingPackageItems)) {
            $existingPackageItems = [];
        }

        // Merge additional items with existing items
        $updatedPackageItems = array_merge($existingPackageItems, $additionalItemsData);

        // Calculate new total amount
        $newTotalAmount = $dayService->amount + $validated['additional_amount'];
        $newAmountPaid = $dayService->amount_paid + $validated['additional_payment'];

        // Update the day service
        $dayService->update([
            'package_items' => $updatedPackageItems,
            'amount' => $newTotalAmount,
            'amount_paid' => $newAmountPaid,
            'payment_status' => $newAmountPaid >= $newTotalAmount ? 'paid' : 'partial',
        ]);

        // Generate receipt URL
        $receiptUrl = route('admin.day-services.receipt', $dayService);

        return response()->json([
            'success' => true,
            'message' => 'Additional items added successfully!',
            'day_service' => $dayService->fresh(),
            'receipt_url' => $receiptUrl,
        ]);
    }

    /**
     * Update ceremony service items and prices
     */
    public function updateItems(Request $request, DayService $dayService)
    {
        // Only allow for ceremony services
        $serviceKey = strtolower($dayService->service_type ?? '');
        $isCeremonyService = str_contains($serviceKey, 'ceremony') || 
                            str_contains($serviceKey, 'ceremory') || 
                            str_contains($serviceKey, 'birthday') || 
                            str_contains($serviceKey, 'package');
        
        if (!$isCeremonyService) {
            return response()->json([
                'success' => false,
                'message' => 'Items can only be updated for ceremony/package services.',
            ], 400);
        }

        $validated = $request->validate([
            'package_items' => 'required|array',
            'additional_items' => 'nullable|array',
            'total_amount' => 'required|numeric|min:0',
            'payment_method' => 'nullable|in:cash,card,mobile,bank,online,other',
            'payment_provider' => 'nullable|string|max:100',
            'payment_reference' => 'nullable|string|max:100',
            'amount_paid' => 'nullable|numeric|min:0',
        ]);

        // Merge package and additional items
        $allItems = array_merge(
            $validated['package_items'] ?? [],
            $validated['additional_items'] ?? []
        );

        // Calculate payment status based on amount_paid and total_amount
        $amountPaid = $dayService->amount_paid ?? 0;
        
        if (isset($validated['amount_paid']) && $validated['amount_paid'] !== null) {
            $amountPaid = $validated['amount_paid'];
        }

        // Recalculate payment status based on amount_paid vs total_amount
        if ($amountPaid >= $validated['total_amount']) {
            $paymentStatus = 'paid';
        } elseif ($amountPaid > 0) {
            $paymentStatus = 'partial';
        } else {
            $paymentStatus = 'pending';
        }

        // Update the day service
        $updateData = [
            'package_items' => $allItems,
            'amount' => $validated['total_amount'],
            'payment_status' => $paymentStatus,
        ];

        if (isset($validated['amount_paid']) && $validated['amount_paid'] !== null) {
            $updateData['amount_paid'] = $amountPaid;
        }

        if ($validated['payment_method']) {
            $updateData['payment_method'] = $validated['payment_method'];
        }

        if ($validated['payment_provider']) {
            $updateData['payment_provider'] = $validated['payment_provider'];
        }

        if ($validated['payment_reference']) {
            $updateData['payment_reference'] = $validated['payment_reference'];
        }

        // If marking as paid, set paid_at
        if ($paymentStatus === 'paid' && !$dayService->paid_at) {
            $updateData['paid_at'] = now();
        }

        $dayService->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Service items and prices updated successfully!',
            'day_service' => $dayService->fresh(),
        ]);
    }

    /**
     * Download receipt
     */
    public function downloadReceipt(DayService $dayService)
    {
        if ($dayService->payment_status !== 'paid') {
            abort(404, 'Receipt not available. Service not paid.');
        }

        // Get exchange rate if needed
        $exchangeRate = $dayService->exchange_rate;
        if (!$exchangeRate && $dayService->guest_type === 'international') {
            $currencyService = new CurrencyExchangeService();
            $exchangeRate = $currencyService->getUsdToTshRate();
        }

        return response()->view('dashboard.day-service-receipt', [
            'dayService' => $dayService,
            'exchangeRate' => $exchangeRate,
        ])->header('Content-Type', 'text/html');
    }

    /**
     * Show reports page
     */
    public function reports(Request $request)
    {
        $user = Auth::guard('staff')->user();
        $role = strtolower($user->role ?? 'manager');

        // Only fetch data if filters are submitted
        if ($request->has('report_type')) {
            // Get filter parameters
            $reportType = $request->get('report_type', 'daily');
            $reportDate = $request->get('date', \Carbon\Carbon::today()->format('Y-m-d'));
            $startDate = $request->get('start_date');
            $endDate = $request->get('end_date');
            $serviceType = $request->get('service_type', 'all');

            // Calculate date range based on report type
            $dateRange = $this->calculateDateRange($reportType, $reportDate, $startDate, $endDate);
            $start = $dateRange['start'];
            $end = $dateRange['end'];

            // Build query for day services
            $query = DayService::with('registeredBy')
                ->whereBetween('service_date', [$start, $end])
                ->orderBy('service_date', 'desc')
                ->orderBy('service_time', 'desc');

            // Filter by service type if specified
            if ($serviceType && $serviceType !== 'all') {
                if ($serviceType === 'swimming') {
                    $query->where('service_type', 'swimming');
                } elseif ($serviceType === 'swimming_with_bucket') {
                    $query->where(function($q) {
                        $q->where('service_type', 'swimming_with_bucket')
                          ->orWhere('service_type', 'swimming-with-bucket');
                    });
                } elseif ($serviceType === 'ceremony') {
                    $query->where(function($q) {
                        $q->where('service_type', 'ceremony')
                          ->orWhere('service_type', 'ceremory')
                          ->orWhere('service_type', 'LIKE', '%ceremony%')
                          ->orWhere('service_type', 'LIKE', '%ceremory%')
                          ->orWhere('service_type', 'LIKE', '%birthday%')
                          ->orWhere('service_type', 'LIKE', '%package%');
                    });
                }
            }

            $dayServices = $query->get();

            // Calculate statistics
            $statistics = [
                'total_services' => $dayServices->count(),
                'paid_services' => $dayServices->where('payment_status', 'paid')->count(),
                'pending_services' => $dayServices->where('payment_status', 'pending')->count(),
            ];

            // Calculate revenue (convert to TZS)
            $totalRevenue = 0;
            $totalRevenueUsd = 0;
            $paidServices = $dayServices->where('payment_status', 'paid');
            $systemExchangeRate = $this->currencyService->getUsdToTshRate();
            foreach ($paidServices as $service) {
                $rate = $service->exchange_rate ?: $systemExchangeRate;
                if ($service->guest_type === 'tanzanian') {
                    $amount = $service->amount_paid ?? $service->amount ?? 0;
                    $totalRevenue += $amount;
                    $totalRevenueUsd += $amount / $rate;
                } else {
                    $amountUsd = $service->amount_paid ?? $service->amount ?? 0;
                    $amountInTzs = $amountUsd * $rate;
                    $totalRevenue += $amountInTzs;
                    $totalRevenueUsd += $amountUsd;
                }
            }

            $statistics['total_revenue'] = $totalRevenue;
            $statistics['total_revenue_usd'] = $totalRevenueUsd;

            // Get exchange rate for display
            $exchangeRate = $this->currencyService->getUsdToTshRate();

            return view('dashboard.day-services-reports', compact(
                'role',
                'reportType',
                'reportDate',
                'startDate',
                'endDate',
                'serviceType',
                'dateRange',
                'dayServices',
                'statistics',
                'exchangeRate'
            ));
        }

        // Initial page load - just show the form
        return view('dashboard.day-services-reports', compact('role'));
    }

    /**
     * Download report as HTML
     */
    public function downloadReport(Request $request)
    {
        $reportType = $request->get('report_type', 'daily');
        $reportDate = $request->get('date', \Carbon\Carbon::today()->format('Y-m-d'));
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $serviceType = $request->get('service_type'); // swimming, swimming_with_bucket, ceremony, all
        
        // Calculate date range based on report type
        $dateRange = $this->calculateDateRange($reportType, $reportDate, $startDate, $endDate);
        $start = $dateRange['start'];
        $end = $dateRange['end'];
        
        // Build query for day services
        $query = DayService::with('registeredBy')
            ->whereBetween('service_date', [$start, $end])
            ->orderBy('service_date', 'desc')
            ->orderBy('service_time', 'desc');

        // Filter by service type if specified
        if ($serviceType && $serviceType !== 'all') {
            if ($serviceType === 'swimming') {
                $query->where('service_type', 'swimming');
            } elseif ($serviceType === 'swimming_with_bucket') {
                $query->where(function($q) {
                    $q->where('service_type', 'swimming_with_bucket')
                      ->orWhere('service_type', 'swimming-with-bucket');
                });
            } elseif ($serviceType === 'ceremony') {
                $query->where(function($q) {
                    $q->where('service_type', 'ceremony')
                      ->orWhere('service_type', 'ceremory')
                      ->orWhere('service_type', 'LIKE', '%ceremony%')
                      ->orWhere('service_type', 'LIKE', '%ceremory%')
                      ->orWhere('service_type', 'LIKE', '%birthday%')
                      ->orWhere('service_type', 'LIKE', '%package%');
                });
            }
        }

        $dayServices = $query->get();

        // Calculate statistics
        $statistics = [
            'total_services' => $dayServices->count(),
            'paid_services' => $dayServices->where('payment_status', 'paid')->count(),
            'pending_services' => $dayServices->where('payment_status', 'pending')->count(),
        ];

        // Calculate revenue (convert to TZS)
        $totalRevenue = 0;
        $paidServices = $dayServices->where('payment_status', 'paid');
        foreach ($paidServices as $service) {
            if ($service->guest_type === 'tanzanian') {
                $totalRevenue += $service->amount_paid ?? $service->amount ?? 0;
            } else {
                $exchangeRate = $service->exchange_rate ?? 2487.81;
                $amountInTzs = ($service->amount_paid ?? $service->amount ?? 0) * $exchangeRate;
                $totalRevenue += $amountInTzs;
            }
        }

        $statistics['total_revenue'] = $totalRevenue;

        // Generate clean filename
        $cleanLabel = preg_replace('/[^a-z0-9]+/', '_', strtolower($dateRange['label']));
        $cleanLabel = trim($cleanLabel, '_'); // Remove leading/trailing underscores
        $filename = 'day_services_report_' . $cleanLabel . '_' . date('Y-m-d_His') . '.html';
        
        $html = view('dashboard.day-services-report-download', [
            'dateRange' => $dateRange,
            'dayServices' => $dayServices,
            'statistics' => $statistics,
            'serviceType' => $serviceType ?? 'all',
        ])->render();

        return response($html, 200)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Generate bill/docket for day service
     */
    public function docket(DayService $dayService)
    {
        $user = Auth::guard('staff')->user();
        $role = strtolower($user->role ?? '');
        
        $filterCategories = null;
        $showPackage = true;
        
        if ($role === 'bar_keeper') {
            $filterCategories = ['alcoholic_beverage', 'non_alcoholic_beverage', 'water', 'juices', 'energy_drinks', 'soft_drinks', 'beers', 'wines', 'spirits', 'cocktails', 'drinks', 'liquor'];
            $showPackage = false;
        } elseif ($role === 'head_chef') {
            $filterCategories = ['food', 'restaurant', 'kitchen'];
            $showPackage = false;
        }
        
        return view('dashboard.day-service-docket', compact('dayService', 'filterCategories', 'showPackage'));
    }
    
    /**
     * Calculate date range based on report type
     */
    private function calculateDateRange($reportType, $reportDate = null, $startDate = null, $endDate = null)
    {
        $today = \Carbon\Carbon::today();
        
        switch ($reportType) {
            case 'daily':
                $date = $reportDate ? \Carbon\Carbon::parse($reportDate) : $today;
                return [
                    'start' => $date->copy()->startOfDay(),
                    'end' => $date->copy()->endOfDay(),
                    'label' => 'Daily Report - ' . $date->format('M d, Y'),
                ];
            
            case 'weekly':
                $date = $reportDate ? \Carbon\Carbon::parse($reportDate) : $today;
                $startOfWeek = $date->copy()->startOfWeek();
                $endOfWeek = $date->copy()->endOfWeek();
                return [
                    'start' => $startOfWeek->startOfDay(),
                    'end' => $endOfWeek->endOfDay(),
                    'label' => 'Weekly Report - ' . $startOfWeek->format('M d') . ' to ' . $endOfWeek->format('M d, Y'),
                ];
            
            case 'monthly':
                $date = $reportDate ? \Carbon\Carbon::parse($reportDate) : $today;
                return [
                    'start' => $date->copy()->startOfMonth()->startOfDay(),
                    'end' => $date->copy()->endOfMonth()->endOfDay(),
                    'label' => 'Monthly Report - ' . $date->format('F Y'),
                ];
            
            case 'yearly':
                $date = $reportDate ? \Carbon\Carbon::parse($reportDate) : $today;
                return [
                    'start' => $date->copy()->startOfYear()->startOfDay(),
                    'end' => $date->copy()->endOfYear()->endOfDay(),
                    'label' => 'Yearly Report - ' . $date->format('Y'),
                ];
            
            case 'custom':
                $start = $startDate ? \Carbon\Carbon::parse($startDate)->startOfDay() : $today->copy()->startOfMonth()->startOfDay();
                $end = $endDate ? \Carbon\Carbon::parse($endDate)->endOfDay() : $today->copy()->endOfDay();
                return [
                    'start' => $start,
                    'end' => $end,
                    'label' => 'Custom Report - ' . $start->format('M d, Y') . ' to ' . $end->format('M d, Y'),
                ];
            
            default:
                return [
                    'start' => $today->copy()->startOfDay(),
                    'end' => $today->copy()->endOfDay(),
                    'label' => 'Daily Report - ' . $today->format('M d, Y'),
                ];
        }
    }
}
