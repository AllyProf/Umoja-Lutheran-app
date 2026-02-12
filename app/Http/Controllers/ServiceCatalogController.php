<?php

namespace App\Http\Controllers;

use App\Models\ServiceCatalog;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceCatalogController extends Controller
{
    /**
     * Display a listing of service catalog items
     */
    public function index()
    {
        $services = ServiceCatalog::with('editor')->orderBy('display_order')->orderBy('service_name')->get();
        $user = Auth::guard('staff')->user();
        $role = strtolower($user->role ?? '');
        
        return view('dashboard.service-catalog-list', compact('services', 'role'));
    }

    /**
     * Show the form for creating a new service catalog item
     */
    public function create()
    {
        $user = Auth::guard('staff')->user();
        $role = strtolower($user->role ?? '');
        
        return view('dashboard.service-catalog-form', compact('role'));
    }

    /**
     * Store a newly created service catalog item
     */
    public function store(Request $request)
    {
        // Check if this is a ceremony/package service
        $isPackageService = false;
        if ($request->has('service_key')) {
            $serviceKey = strtolower($request->service_key);
            $isPackageService = str_contains($serviceKey, 'ceremony') || 
                               str_contains($serviceKey, 'ceremory') ||
                               str_contains($serviceKey, 'birthday') || 
                               str_contains($serviceKey, 'package');
        }
        
        $validated = $request->validate([
            'service_name' => 'required|string|max:255',
            'service_key' => 'required|string|max:50|unique:service_catalog,service_key',
            'description' => 'nullable|string',
            'pricing_type' => 'required|in:per_person,per_hour,fixed,custom',
            'price_tanzanian' => 'required|numeric|min:0', // Allow 0 for ceremony packages
            'price_international' => 'nullable|numeric|min:0',
            'age_group' => 'nullable|in:adult,child,both',
            'child_price_tanzanian' => 'nullable|numeric|min:0',
            'payment_required_upfront' => 'required|boolean',
            'requires_items' => 'required|boolean',
            'is_active' => 'required|boolean',
            'display_order' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
            'package_items' => 'nullable|string', // JSON string
        ]);
        
        // Handle package_items JSON
        if ($request->has('package_items') && is_string($request->package_items)) {
            $packageItems = json_decode($request->package_items, true);
            $validated['package_items'] = is_array($packageItems) ? $packageItems : null;
        } else {
            $validated['package_items'] = null;
        }

        ServiceCatalog::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Service catalog item created successfully!',
        ]);
    }

    /**
     * Show the form for editing the specified service catalog item
     */
    public function edit(ServiceCatalog $serviceCatalog)
    {
        $user = Auth::guard('staff')->user();
        $role = strtolower($user->role ?? '');
        
        return view('dashboard.service-catalog-form', compact('serviceCatalog', 'role'));
    }

    /**
     * Update the specified service catalog item
     */
    public function update(Request $request, ServiceCatalog $serviceCatalog)
    {
        // Check if this is a ceremony/package service
        $isPackageService = false;
        if ($request->has('service_key')) {
            $serviceKey = strtolower($request->service_key);
            $isPackageService = str_contains($serviceKey, 'ceremony') || 
                               str_contains($serviceKey, 'ceremory') ||
                               str_contains($serviceKey, 'birthday') || 
                               str_contains($serviceKey, 'package');
        }
        
        $validated = $request->validate([
            'service_name' => 'required|string|max:255',
            'service_key' => 'required|string|max:50|unique:service_catalog,service_key,' . $serviceCatalog->id,
            'description' => 'nullable|string',
            'pricing_type' => 'required|in:per_person,per_hour,fixed,custom',
            'price_tanzanian' => 'required|numeric|min:0', // Allow 0 for ceremony packages
            'price_international' => 'nullable|numeric|min:0',
            'age_group' => 'nullable|in:adult,child,both',
            'child_price_tanzanian' => 'nullable|numeric|min:0',
            'payment_required_upfront' => 'required|boolean',
            'requires_items' => 'required|boolean',
            'is_active' => 'required|boolean',
            'display_order' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
            'package_items' => 'nullable|string', // JSON string
        ]);
        
        // Handle package_items JSON
        if ($request->has('package_items') && is_string($request->package_items)) {
            $packageItems = json_decode($request->package_items, true);
            $validated['package_items'] = is_array($packageItems) ? $packageItems : null;
        } else {
            $validated['package_items'] = null;
        }

        // Track who edited and when
        $staff = Auth::guard('staff')->user();
        $validated['edited_by'] = $staff->id;
        $validated['last_edited_at'] = now();

        // Store old values for comparison and track changes
        $oldValues = $serviceCatalog->only([
            'service_name', 
            'price_tanzanian', 
            'price_international', 
            'is_active',
            'pricing_type',
            'description',
            'age_group',
            'child_price_tanzanian',
            'payment_required_upfront',
            'requires_items',
            'display_order'
        ]);
        
        // Build changes array
        $changes = [];
        foreach ($oldValues as $field => $oldValue) {
            $newValue = $validated[$field] ?? $serviceCatalog->$field ?? null;
            
            // Compare values (handle different types)
            if ($field === 'is_active' || $field === 'payment_required_upfront' || $field === 'requires_items') {
                // Boolean comparison
                if ((bool)$oldValue !== (bool)$newValue) {
                    $changes[] = [
                        'field' => ucfirst(str_replace('_', ' ', $field)),
                        'old' => $oldValue ? 'Yes' : 'No',
                        'new' => $newValue ? 'Yes' : 'No'
                    ];
                }
            } elseif ($field === 'price_tanzanian' || $field === 'price_international' || $field === 'child_price_tanzanian') {
                // Price comparison (with 2 decimal precision)
                if (round((float)$oldValue, 2) !== round((float)$newValue, 2)) {
                    $changes[] = [
                        'field' => ucfirst(str_replace('_', ' ', $field)),
                        'old' => number_format((float)$oldValue, 2),
                        'new' => number_format((float)$newValue, 2)
                    ];
                }
            } elseif ($field === 'display_order') {
                // Integer comparison
                if ((int)$oldValue !== (int)$newValue) {
                    $changes[] = [
                        'field' => ucfirst(str_replace('_', ' ', $field)),
                        'old' => (string)$oldValue,
                        'new' => (string)$newValue
                    ];
                }
            } else {
                // String comparison
                if (trim((string)$oldValue) !== trim((string)$newValue)) {
                    $changes[] = [
                        'field' => ucfirst(str_replace('_', ' ', $field)),
                        'old' => $oldValue ?: '(empty)',
                        'new' => $newValue ?: '(empty)'
                    ];
                }
            }
        }
        
        // Store changes in validated data
        $validated['last_changes'] = !empty($changes) ? $changes : null;
        
        $serviceCatalog->update($validated);

        // Send notifications to relevant staff if significant changes were made
        try {
            $notificationService = new NotificationService();
            $notificationService->createServiceCatalogUpdateNotification($serviceCatalog, $staff, $oldValues);
        } catch (\Exception $e) {
            \Log::error('Failed to create service catalog update notification: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Service catalog item updated successfully!',
        ]);
    }

    /**
     * Remove the specified service catalog item
     */
    public function destroy(ServiceCatalog $serviceCatalog)
    {
        // Check if service is being used in any day services
        $usageCount = \App\Models\DayService::where('service_type', $serviceCatalog->service_key)->count();
        
        if ($usageCount > 0) {
            return response()->json([
                'success' => false,
                'message' => "Cannot delete service. It's being used in {$usageCount} day service record(s). You can deactivate it instead.",
            ], 400);
        }

        $serviceCatalog->delete();

        return response()->json([
            'success' => true,
            'message' => 'Service catalog item deleted successfully!',
        ]);
    }

    /**
     * Get active services for dropdown (API endpoint)
     */
    public function getActiveServices()
    {
        $services = ServiceCatalog::active()
            ->orderBy('display_order')
            ->orderBy('service_name')
            ->get()
            ->map(function($service) {
                return [
                    'id' => $service->id,
                    'key' => $service->service_key,
                    'name' => $service->service_name,
                    'pricing_type' => $service->pricing_type,
                    'price_tanzanian' => $service->price_tanzanian,
                    'price_international' => $service->price_international,
                    'payment_required_upfront' => $service->payment_required_upfront,
                    'requires_items' => $service->requires_items,
                ];
            });

        return response()->json([
            'success' => true,
            'services' => $services,
        ]);
    }
}
