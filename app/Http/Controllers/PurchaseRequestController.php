<?php

namespace App\Http\Controllers;

use App\Models\PurchaseRequest;
use App\Models\PurchaseDeadline;
use App\Models\PurchaseRequestTemplate;
use App\Models\ShoppingList;
use App\Models\ShoppingListItem;
use App\Models\HousekeepingInventoryItem;
use App\Models\InventoryStockMovement;
use App\Models\KitchenInventoryItem;
use App\Models\KitchenStockMovement;
use App\Models\StockTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PurchaseRequestController extends Controller
{
    /**
     * Show purchase request form
     */
    public function create(Request $request)
    {
        $staff = Auth::guard('staff')->user();
        $staffDept = $staff->getDepartmentName();
        $deadline = PurchaseDeadline::where('is_active', true)->first();
        $nextDeadline = $deadline ? $deadline->getNextDeadlineDate() : null;
        
        // Get active templates
        $allTemplates = PurchaseRequestTemplate::where('is_active', true)
            ->with('createdBy')
            ->get();
            
        // Filter by user's department for non-managers
        if (!$staff->isManager() && !$staff->isSuperAdmin()) {
            $templates = $allTemplates->filter(function($template) use ($staffDept) {
                return $template->createdBy && strcasecmp($template->createdBy->getDepartmentName(), $staffDept) === 0;
            })->sortBy('name');
        } else {
            $templates = $allTemplates->sortBy('name');
        }

        // Handle pre-filled items from inventory page
        $preFilledItems = [];
        if ($request->has('ids')) {
            $ids = is_array($request->ids) ? $request->ids : explode(',', $request->ids);
            $variants = \App\Models\ProductVariant::with('product')->whereIn('id', $ids)->get();
            
            foreach ($variants as $variant) {
                $preFilledItems[] = [
                    'item_name' => ($variant->product->name ?? '') . ($variant->measurement ? " ({$variant->measurement})" : ""),
                    'category' => $variant->product->category ?? '',
                    'unit' => $variant->selling_unit ?? 'bottles',
                    'quantity' => 1,
                    'priority' => 'medium'
                ];
            }
        }

        // Handle pre-filled items from kitchen inventory
        if ($request->has('kitchen_ids')) {
            $ids = is_array($request->kitchen_ids) ? $request->kitchen_ids : explode(',', $request->kitchen_ids);
            $items = \App\Models\KitchenInventoryItem::whereIn('id', $ids)->get();
            
            foreach ($items as $item) {
                $preFilledItems[] = [
                    'item_name' => $item->name,
                    'category' => $item->category,
                    'unit' => $item->unit,
                    'quantity' => 1,
                    'priority' => 'medium'
                ];
            }
        }

        // Handle pre-filled items from housekeeping inventory
        if ($request->has('housekeeping_ids')) {
            $ids = is_array($request->housekeeping_ids) ? $request->housekeeping_ids : explode(',', $request->housekeeping_ids);
            $items = \App\Models\HousekeepingInventoryItem::whereIn('id', $ids)->get();
            
            foreach ($items as $item) {
                $preFilledItems[] = [
                    'item_name' => $item->name,
                    'category' => $item->category,
                    'unit' => $item->unit,
                    'quantity' => 1,
                    'priority' => 'medium'
                ];
            }
        }
        
        return view('dashboard.purchase-request-create', compact('deadline', 'nextDeadline', 'templates', 'preFilledItems'));
    }

    /**
     * Store purchase request(s)
     */
    public function store(Request $request)
    {
        // Check if items array is provided (new format) or single item (old format for backward compatibility)
        if ($request->has('items') && is_array($request->items)) {
            // Multiple items format
            $request->validate([
                'items' => 'required|array|min:1',
                'items.*.item_name' => 'required|string|max:255',
                'items.*.category' => 'nullable|string|max:255',
                'items.*.quantity' => 'required|numeric|min:0.01',
                'items.*.unit' => 'required|string|max:50',
                'items.*.water_size' => 'nullable|in:small,large',
                'items.*.reason' => 'nullable|string|max:1000',
                'items.*.priority' => 'required|in:low,medium,high,urgent',
            ]);
            
            $staff = Auth::guard('staff')->user();
            $createdRequests = [];
            
            foreach ($request->items as $item) {
                // Build item name with water size if applicable
                $itemName = trim($item['item_name']);
                
                // Remove any existing size suffix to avoid duplication (check anywhere in the name)
                $itemName = preg_replace('/\s*\(Small\)\s*/i', '', $itemName);
                $itemName = preg_replace('/\s*\(Large\)\s*/i', '', $itemName);
                $itemName = trim($itemName); // Clean up any extra spaces
                
                // Only add size if it's water category and size is specified
                if (isset($item['water_size']) && $item['water_size'] && 
                    isset($item['category']) && $item['category'] === 'water' && 
                    isset($item['unit']) && $item['unit'] === 'pcs') {
                    // Check if the size is not already in the name
                    $sizeText = ucfirst($item['water_size']);
                    if (stripos($itemName, $sizeText) === false) {
                        $itemName = $itemName . ' (' . $sizeText . ')';
                    }
                }
                
                $purchaseRequest = PurchaseRequest::create([
                    'requested_by' => $staff->id,
                    'item_name' => $itemName,
                    'category' => $item['category'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit' => $item['unit'],
                    'water_size' => ($item['category'] ?? '') === 'water' && ($item['unit'] ?? '') === 'pcs' ? ($item['water_size'] ?? null) : null,
                    'reason' => $item['reason'] ?? null,
                    'priority' => $item['priority'],
                    'status' => 'pending',
                ]);
                $createdRequests[] = $purchaseRequest;
            }
            
            $itemCount = count($createdRequests);
            $message = $itemCount === 1 
                ? 'Purchase request submitted successfully.' 
                : "{$itemCount} purchase requests submitted successfully.";
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'requests' => $createdRequests,
                'count' => $itemCount,
            ]);
        } else {
            // Single item format (backward compatibility)
            $request->validate([
                'item_name' => 'required|string|max:255',
                'category' => 'nullable|string|max:255',
                'quantity' => 'required|numeric|min:0.01',
                'unit' => 'required|string|max:50',
                'reason' => 'nullable|string|max:1000',
                'priority' => 'required|in:low,medium,high,urgent',
            ]);
            
            $staff = Auth::guard('staff')->user();
            
            $purchaseRequest = PurchaseRequest::create([
                'requested_by' => $staff->id,
                'item_name' => $request->item_name,
                'category' => $request->category,
                'quantity' => $request->quantity,
                'unit' => $request->unit,
                'reason' => $request->reason,
                'priority' => $request->priority,
                'status' => 'pending',
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Purchase request submitted successfully.',
                'request' => $purchaseRequest,
            ]);
        }
    }

    /**
     * Show my purchase requests
     */
    public function myRequests()
    {
        $staff = Auth::guard('staff')->user();
        
        // Get staff's department name
        $staffDepartment = $staff->getDepartmentName();
        
        // Get items ready to receive (transferred to this department but not received)
        // Items should be visible to all staff in the department, not just the original requester
        // Use case-insensitive matching to handle variations
        $itemsToReceive = ShoppingListItem::whereRaw('LOWER(TRIM(transferred_to_department)) = ?', [strtolower(trim($staffDepartment))])
            ->where('is_received_by_department', false)
            ->where('is_purchased', true)
            ->where('is_found', true)
            ->where('purchased_quantity', '>', 0)
            ->with(['purchaseRequest.requestedBy', 'shoppingList'])
            ->orderBy('updated_at', 'desc')
            ->get();
        
        // Get purchase requests - newest first
        $requests = PurchaseRequest::with(['editor', 'shoppingList'])
            ->where('requested_by', $staff->id)
            ->orderBy('created_at', 'desc')
            ->paginate(5);
        
        // Determine route prefix based on staff role
        $routePrefix = 'housekeeper'; // default
        $normalizedRole = strtolower(str_replace([' ', '_'], '', trim($staff->role ?? '')));
        if ($normalizedRole === 'reception') {
            $routePrefix = 'reception';
        } elseif (in_array($normalizedRole, ['barkeeper', 'bartender', 'bar_keeper', 'bar keeper'])) {
            $routePrefix = 'bar-keeper';
        } elseif (in_array($normalizedRole, ['headchef', 'head_chef', 'head chef', 'chef'])) {
            $routePrefix = 'chef-master';
        }
        
        return view('dashboard.purchase-requests-my', compact('requests', 'itemsToReceive', 'routePrefix'));
    }

    /**
     * Show purchase requests and received items history
     */
    public function history()
    {
        $staff = Auth::guard('staff')->user();
        
        // Get staff's department name
        $staffDepartment = $staff->getDepartmentName();
        
        // Get received items history (items that have been received by this department)
        $receivedItems = ShoppingListItem::whereRaw('LOWER(TRIM(transferred_to_department)) = ?', [strtolower(trim($staffDepartment))])
            ->where('is_received_by_department', true)
            ->whereNotNull('received_by_department_at')
            ->with(['purchaseRequest.requestedBy', 'shoppingList'])
            ->orderBy('received_by_department_at', 'desc')
            ->paginate(5, ['*'], 'received_page');
        
        // Get purchase requests - newest first
        $requests = PurchaseRequest::with(['editor', 'shoppingList'])
            ->where('requested_by', $staff->id)
            ->orderBy('created_at', 'desc')
            ->paginate(5, ['*'], 'requests_page');
        
        // Determine route prefix based on staff role
        $routePrefix = 'housekeeper'; // default
        $normalizedRole = strtolower(str_replace([' ', '_'], '', trim($staff->role ?? '')));
        if ($normalizedRole === 'reception') {
            $routePrefix = 'reception';
        } elseif (in_array($normalizedRole, ['barkeeper', 'bartender', 'bar_keeper', 'bar keeper'])) {
            $routePrefix = 'bar-keeper';
        } elseif (in_array($normalizedRole, ['headchef', 'head_chef', 'head chef', 'chef'])) {
            $routePrefix = 'chef-master';
        }
        
        return view('dashboard.purchase-requests-history', compact('requests', 'receivedItems', 'routePrefix'));
    }
    
    /**
     * Receive transferred items (Department staff)
     */
    public function receiveItems(Request $request)
    {
        $staff = Auth::guard('staff')->user();
        $request->validate([
            'item_ids' => 'required|array',
            'item_ids.*' => 'exists:shopping_list_items,id',
        ]);
        
        DB::beginTransaction();
        try {
            $receivedCount = 0;
            $staffDeptName = $staff->getDepartmentName();
            
            foreach ($request->item_ids as $itemId) {
                $item = ShoppingListItem::findOrFail($itemId);
                
                // Verify this item has been transferred to the staff's department
                // Any staff member in the department can receive items transferred to that department
                $transferredDept = $item->transferred_to_department;
                
                if ($transferredDept &&
                    strtolower(trim($transferredDept)) === strtolower(trim($staffDeptName)) &&
                    !$item->is_received_by_department &&
                    $item->is_purchased &&
                    $item->is_found &&
                    ($item->purchased_quantity ?? 0) > 0) {
                    
                    $item->update([
                        'is_received_by_department' => true,
                        'received_by_department_at' => now(),
                    ]);
                    
                    // Update purchase request status if it exists
                    if ($item->purchaseRequest) {
                        $item->purchaseRequest->update([
                            'status' => 'completed',
                        ]);
                    }
                    
                    // Add to inventory (only for housekeeping and food/kitchen department)
                    if (strtolower(trim($staffDeptName)) === 'housekeeping') {
                        $this->addToInventory($item, $staff);
                    } elseif (strtolower(trim($staffDeptName)) === 'food' || strtolower(trim($staffDeptName)) === 'kitchen') {
                        $this->addToKitchenInventory($item, $staff);
                    } elseif (strtolower(trim($staffDeptName)) === 'bar') {
                        $this->addToBarInventory($item, $staff);
                    }
                    
                    $receivedCount++;
                }
            }
            
            DB::commit();
            
            $inventoryMessage = (in_array(strtolower(trim($staffDeptName)), ['housekeeping', 'food', 'kitchen', 'bar'])) 
                ? " and added to your inventory stock." 
                : ".";
            
            return response()->json([
                'success' => true,
                'message' => "{$receivedCount} item(s) received successfully{$inventoryMessage}",
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error receiving items: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Add received item to inventory
     */
    private function addToInventory($shoppingListItem, $staff)
    {
        $itemName = $shoppingListItem->product_name;
        $category = $shoppingListItem->category ?? 'other';
        $quantity = $shoppingListItem->purchased_quantity ?? $shoppingListItem->quantity ?? 0;
        $unit = $shoppingListItem->unit ?? 'pcs';
        
        if ($quantity <= 0) {
            return; // Skip if no quantity
        }
        
        // Find or create inventory item (Match by name to avoid duplicates if category varies slightly)
        $inventoryItem = HousekeepingInventoryItem::where('name', $itemName)->first();
        
        if (!$inventoryItem) {
            $inventoryItem = HousekeepingInventoryItem::create([
                'name' => $itemName,
                'category' => $category,
                'unit' => $unit,
                'current_stock' => 0,
                'minimum_stock' => 0,
            ]);
        }
        
        // Add received quantity to stock
        $inventoryItem->current_stock += $quantity;
        $inventoryItem->save();
        
        // Create stock movement log
        InventoryStockMovement::create([
            'inventory_item_id' => $inventoryItem->id,
            'movement_type' => 'supply',
            'quantity' => $quantity,
            'performed_by' => $staff->id,
            'notes' => 'Received from purchase request: ' . ($shoppingListItem->purchaseRequest->item_name ?? $itemName),
        ]);
    }
    
    /**
     * Add received item to kitchen inventory
     */
    private function addToKitchenInventory($shoppingListItem, $staff)
    {
        $itemName = $shoppingListItem->product_name;
        $category = $shoppingListItem->category ?? 'other';
        $quantity = $shoppingListItem->purchased_quantity ?? $shoppingListItem->quantity ?? 0;
        $unit = $shoppingListItem->unit ?? 'pcs';
        $expiryDate = $shoppingListItem->expiry_date;
        
        if ($quantity <= 0) {
            return; // Skip if no quantity
        }
        
        // Find or create kitchen inventory item
        $inventoryItem = KitchenInventoryItem::firstOrCreate(
            [
                'name' => $itemName,
            ],
            [
                'category' => $category,
                'unit' => $unit,
                'current_stock' => 0,
                'minimum_stock' => 0,
            ]
        );
        
        // Add received quantity to stock and update expiry date if provided
        $inventoryItem->current_stock += $quantity;
        if ($expiryDate) {
            $inventoryItem->expiry_date = $expiryDate;
        }
        $inventoryItem->save();
        
        // Create stock movement log
        KitchenStockMovement::create([
            'inventory_item_id' => $inventoryItem->id,
            'movement_type' => 'supply',
            'quantity' => $quantity,
            'performed_by' => $staff->id,
            'movement_date' => now(),
            'expiry_date' => $expiryDate,
            'notes' => 'Received from purchase request: ' . ($shoppingListItem->purchaseRequest->item_name ?? $itemName),
        ]);
    }
    
    /**
     * Add received item to bar inventory (as a completed stock transfer)
     */
    private function addToBarInventory($shoppingListItem, $staff)
    {
        $variant = $shoppingListItem->productVariant;
        if (!$variant) {
            return; // Cannot add to bar inventory without product link
        }

        $quantity = $shoppingListItem->purchased_quantity ?? $shoppingListItem->quantity ?? 0;
        if ($quantity <= 0) {
            return;
        }

        // Create a completed stock transfer to represent adding to bar stock
        $transfer = StockTransfer::create([
            'transfer_reference' => StockTransfer::generateReference(),
            'product_id' => $variant->product_id,
            'product_variant_id' => $variant->id,
            'quantity_transferred' => $quantity,
            'quantity_unit' => 'bottles',
            'transferred_by' => 1, // System / Admin
            'received_by' => $staff->id,
            'status' => 'completed',
            'transfer_date' => now(),
            'received_at' => now(),
            'notes' => 'Directly received from purchase: ' . $shoppingListItem->product_name,
            'unit_cost' => $shoppingListItem->unit_price,
            'total_cost' => $shoppingListItem->purchased_cost,
            'selling_price_per_pic' => $variant->selling_price_per_pic,
            'selling_price_per_serving' => $variant->selling_price_per_serving,
            'servings_per_pic' => $variant->servings_per_pic,
            'expiry_date' => $shoppingListItem->expiry_date,
        ]);
        
        $transfer->calculateRevenueProjections();
        $transfer->save();
    }
    
    /**
     * Show all received items (Manager view)
     */
    public function receivedItems(Request $request)
    {
        $query = ShoppingListItem::where('is_received_by_department', true)
            ->with(['purchaseRequest.requestedBy', 'shoppingList']);
        
        // Filter by department
        if ($request->has('department') && $request->department) {
            $query->whereHas('purchaseRequest', function($q) use ($request) {
                $deptName = $request->department;
                $q->whereHas('requestedBy', function($subQ) use ($deptName) {
                    $normalizedRole = strtolower(str_replace([' ', '_'], '', trim($deptName)));
                    $roleMap = [
                        'housekeeping' => ['housekeeper'],
                        'reception' => ['reception'],
                        'bar' => ['bar_keeper', 'bar keeper', 'bartender'],
                        'food' => ['head_chef', 'head chef', 'chef'],
                    ];
                    
                    if (isset($roleMap[$normalizedRole])) {
                        $roles = $roleMap[$normalizedRole];
                        $subQ->where(function($roleQuery) use ($roles) {
                            foreach ($roles as $role) {
                                $roleQuery->orWhereRaw('LOWER(TRIM(role)) = ?', [strtolower($role)]);
                            }
                        });
                    }
                });
            });
        }
        
        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->where('received_by_department_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->where('received_by_department_at', '<=', $request->date_to . ' 23:59:59');
        }
        
        $receivedItems = $query->orderBy('received_by_department_at', 'desc')
            ->paginate(50);
        
        // Get department stats
        $departmentStats = ShoppingListItem::where('is_received_by_department', true)
            ->whereHas('purchaseRequest.requestedBy')
            ->with('purchaseRequest.requestedBy')
            ->get()
            ->groupBy(function($item) {
                if ($item->purchaseRequest && $item->purchaseRequest->requestedBy) {
                    return $item->purchaseRequest->requestedBy->getDepartmentName();
                }
                return 'Unknown';
            })
            ->map(function($items) {
                return [
                    'count' => $items->count(),
                    'total_quantity' => $items->sum(function($item) {
                        return $item->purchased_quantity ?? $item->quantity ?? 0;
                    }),
                ];
            });
        
        $activePage = 'purchase-requests/received';
        return view('dashboard.purchase-requests-received', compact('receivedItems', 'departmentStats', 'activePage'));
    }

    /**
     * Show all purchase requests (for manager)
     */
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'new'); // default to new requests
        $query = PurchaseRequest::with(['requestedBy', 'approvedBy', 'shoppingList', 'editor']);
        
        // Filter by status (from general filter dropdown)
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        // Filter by priority
        if ($request->has('priority') && $request->priority) {
            $query->where('priority', $request->priority);
        }
        
        // Filter by department
        if ($request->has('department') && $request->department) {
            $department = strtolower($request->department);
            
            // Map department names to roles
            $departmentToRoles = [
                'housekeeping' => ['housekeeper'],
                'reception' => ['reception'],
                'bar' => ['bar_keeper', 'bar keeper', 'bartender'],
                'food' => ['head_chef', 'head chef', 'chef'],
            ];
            
            if (isset($departmentToRoles[$department])) {
                $roles = $departmentToRoles[$department];
                $query->whereHas('requestedBy', function($q) use ($roles) {
                    $q->where(function($subQuery) use ($roles) {
                        foreach ($roles as $role) {
                            $subQuery->orWhereRaw('LOWER(TRIM(role)) = ?', [strtolower($role)]);
                        }
                    });
                });
            }
        }
        
        // Filter by Tab
        if ($tab === 'completed') {
            $query->whereIn('status', ['purchased', 'completed', 'received']);
        } elseif ($tab === 'rejected') {
            $query->where('status', 'rejected');
        } else {
            // New/Active requests (pending, approved, or on list but not yet fully purchased)
            $query->whereIn('status', ['pending', 'approved', 'on_list']);
        }
        
        // Get all requests (not paginated for grouping)
        $allRequests = $query->orderBy('created_at', 'desc')->get();
        
        // Group requests by department
        $groupedRequests = $allRequests->groupBy(function($request) {
            if ($request->requestedBy) {
                return $request->requestedBy->getDepartmentName();
            }
            return 'Other';
        });
        
        // Also get paginated version for backward compatibility
        $requests = $query->orderBy('created_at', 'desc')->paginate(20)->appends($request->query());
        
        $deadline = PurchaseDeadline::where('is_active', true)->first();
        $nextDeadline = $deadline ? $deadline->getNextDeadlineDate() : null;
        
        // Statistics
        $stats = [
            'pending' => PurchaseRequest::where('status', 'pending')->count(),
            'approved' => PurchaseRequest::whereIn('status', ['approved', 'on_list'])->count(),
            'rejected' => PurchaseRequest::where('status', 'rejected')->count(),
            'purchased' => PurchaseRequest::whereIn('status', ['purchased', 'completed', 'received'])->count(),
            'total' => PurchaseRequest::count(),
        ];
        
        $filters = $request->only(['status', 'priority', 'department']);
        
        return view('dashboard.purchase-requests', compact('requests', 'groupedRequests', 'deadline', 'nextDeadline', 'stats', 'filters', 'tab'));
    }
    
    /**
     * Update purchase request (Manager only)
     */
    public function update(Request $request, PurchaseRequest $purchaseRequest)
    {
        $manager = Auth::guard('staff')->user();
        
        if ($manager->role !== 'manager' && !$manager->isSuperAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only managers can edit requests.',
            ], 403);
        }
        
        $request->validate([
            'item_name' => 'required|string|max:255',
            'quantity' => 'required|numeric|min:0.01',
            'unit' => 'required|string|max:50',
            'priority' => 'required|in:low,medium,high,urgent',
            'reason' => 'nullable|string|max:1000',
        ]);
        
        // Store old values for comparison
        $oldValues = $purchaseRequest->only([
            'item_name',
            'quantity',
            'unit',
            'priority',
            'reason'
        ]);
        
        // Build changes array
        $changes = [];
        $updateData = [
            'item_name' => $request->item_name,
            'quantity' => $request->quantity,
            'unit' => $request->unit,
            'priority' => $request->priority,
            'reason' => $request->reason,
            'edited_by' => $manager->id,
            'last_edited_at' => now(),
        ];
        
        // Compare and track changes
        foreach ($oldValues as $field => $oldValue) {
            $newValue = $updateData[$field] ?? null;
            
            if ($field === 'quantity') {
                // Quantity comparison (with 2 decimal precision)
                if (round((float)$oldValue, 2) !== round((float)$newValue, 2)) {
                    $changes[] = [
                        'field' => ucfirst(str_replace('_', ' ', $field)),
                        'old' => number_format((float)$oldValue, 0) . ' ' . $purchaseRequest->unit,
                        'new' => number_format((float)$newValue, 0) . ' ' . $updateData['unit']
                    ];
                }
            } elseif ($field === 'priority') {
                // Priority comparison
                if (strtolower(trim($oldValue)) !== strtolower(trim($newValue))) {
                    $changes[] = [
                        'field' => ucfirst(str_replace('_', ' ', $field)),
                        'old' => ucfirst($oldValue),
                        'new' => ucfirst($newValue)
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
        
        // Store changes
        $updateData['last_changes'] = !empty($changes) ? $changes : null;
        
        $purchaseRequest->update($updateData);
        
        return response()->json([
            'success' => true,
            'message' => 'Purchase request updated successfully.',
            'request' => $purchaseRequest->fresh(['requestedBy', 'editor']),
            'changes' => $changes,
        ]);
    }

    /**
     * Show purchase request details (Manager only)
     */
    public function show(PurchaseRequest $purchaseRequest)
    {
        $manager = Auth::guard('staff')->user();
        
        if ($manager->role !== 'manager' && !$manager->isSuperAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.',
            ], 403);
        }
        
        return response()->json([
            'success' => true,
            'item_name' => $purchaseRequest->item_name,
            'category' => $purchaseRequest->category,
            'quantity' => $purchaseRequest->quantity,
            'unit' => $purchaseRequest->unit,
            'priority' => $purchaseRequest->priority,
            'reason' => $purchaseRequest->reason,
            'requested_by' => $purchaseRequest->requestedBy->name ?? 'N/A',
        ]);
    }

    /**
     * Approve purchase request (Manager only)
     */
    public function approve(Request $request, PurchaseRequest $purchaseRequest)
    {
        $manager = Auth::guard('staff')->user();
        
        if ($manager->role !== 'manager' && !$manager->isSuperAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only managers can approve requests.',
            ], 403);
        }
        
        $purchaseRequest->update([
            'status' => 'approved',
            'approved_by' => $manager->id,
            'approved_at' => now(),
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Purchase request approved successfully.',
        ]);
    }

    /**
     * Bulk approve purchase requests (Manager only)
     */
    public function bulkApprove(Request $request)
    {
        $manager = Auth::guard('staff')->user();
        
        if ($manager->role !== 'manager' && !$manager->isSuperAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only managers can approve requests.',
            ], 403);
        }
        
        $request->validate([
            'request_ids' => 'required|array',
            'request_ids.*' => 'exists:purchase_requests,id',
        ]);
        
        $requestIds = $request->request_ids;
        $approvedCount = 0;
        $alreadyApprovedCount = 0;
        
        foreach ($requestIds as $requestId) {
            $purchaseRequest = PurchaseRequest::find($requestId);
            
            if ($purchaseRequest && $purchaseRequest->status === 'pending') {
                $purchaseRequest->update([
                    'status' => 'approved',
                    'approved_by' => $manager->id,
                    'approved_at' => now(),
                ]);
                $approvedCount++;
            } elseif ($purchaseRequest && $purchaseRequest->status === 'approved') {
                $alreadyApprovedCount++;
            }
        }
        
        $message = '';
        if ($approvedCount > 0) {
            $message = "{$approvedCount} purchase request(s) approved successfully.";
        }
        if ($alreadyApprovedCount > 0) {
            $message .= ($message ? ' ' : '') . "{$alreadyApprovedCount} request(s) were already approved.";
        }
        
        return response()->json([
            'success' => true,
            'message' => $message ?: 'No requests were approved.',
            'approved_count' => $approvedCount,
            'already_approved_count' => $alreadyApprovedCount,
        ]);
    }

    /**
     * Reject purchase request (Manager only)
     */
    public function reject(Request $request, PurchaseRequest $purchaseRequest)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);
        
        $manager = Auth::guard('staff')->user();
        
        if ($manager->role !== 'manager' && !$manager->isSuperAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only managers can reject requests.',
            ], 403);
        }
        
        $purchaseRequest->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Purchase request rejected.',
        ]);
    }

    /**
     * Show purchase deadline settings (Manager only)
     */
    public function showDeadline()
    {
        $manager = Auth::guard('staff')->user();
        
        if ($manager->role !== 'manager' && !$manager->isSuperAdmin()) {
            return redirect()->route('admin.purchase-requests.index')
                ->with('error', 'Unauthorized. Only managers can access deadline settings.');
        }
        
        $deadline = PurchaseDeadline::where('is_active', true)->first();
        
        // If no active deadline exists, create a default one
        if (!$deadline) {
            $deadline = PurchaseDeadline::create([
                'day_of_week' => 'friday',
                'deadline_time' => '17:00',
                'notes' => 'Default purchase day - every Friday',
                'is_active' => true,
            ]);
        }
        
        $nextDeadline = $deadline->getNextDeadlineDate();
        
        return view('dashboard.purchase-deadline-settings', compact('deadline', 'nextDeadline'));
    }
    
    /**
     * Update purchase deadline (Manager only)
     */
    public function updateDeadline(Request $request)
    {
        $manager = Auth::guard('staff')->user();
        
        if ($manager->role !== 'manager' && !$manager->isSuperAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only managers can update deadline settings.',
            ], 403);
        }
        
        $request->validate([
            'day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'deadline_time' => 'required|date_format:H:i',
            'notes' => 'nullable|string|max:500',
        ]);
        
        $deadline = PurchaseDeadline::where('is_active', true)->first();
        
        if (!$deadline) {
            $deadline = PurchaseDeadline::create([
                'day_of_week' => $request->day_of_week,
                'deadline_time' => $request->deadline_time,
                'notes' => $request->notes,
                'is_active' => true,
            ]);
        } else {
            // Deactivate current deadline and create a new one (or update existing)
            $deadline->update([
                'day_of_week' => $request->day_of_week,
                'deadline_time' => $request->deadline_time,
                'notes' => $request->notes,
            ]);
        }
        
        $nextDeadline = $deadline->getNextDeadlineDate();
        
        return response()->json([
            'success' => true,
            'message' => 'Purchase deadline updated successfully.',
            'deadline' => $deadline,
            'next_deadline' => $nextDeadline->format('F d, Y H:i'),
        ]);
    }

    /**
     * Add requests to shopping list (Manager only)
     * Redirects to shopping list create page with pre-filled data
     */
    public function addToShoppingList(Request $request)
    {
        $request->validate([
            'request_ids' => 'required|array',
            'request_ids.*' => 'exists:purchase_requests,id',
        ]);
        
        $manager = Auth::guard('staff')->user();
        
        if ($manager->role !== 'manager' && !$manager->isSuperAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only managers can add requests to shopping list.',
            ], 403);
        }
        
        // Get approved purchase requests
        $purchaseRequests = PurchaseRequest::whereIn('id', $request->request_ids)
            ->where('status', 'approved')
            ->with('requestedBy')
            ->get();
        
        if ($purchaseRequests->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No approved requests selected.',
            ], 400);
        }
        
        // Prepare items data for pre-filling
        $items = [];
        foreach ($purchaseRequests as $purchaseRequest) {
            $items[] = [
                'product_name' => $purchaseRequest->item_name,
                'category' => $purchaseRequest->category ?? 'food',
                'quantity' => $purchaseRequest->quantity,
                'unit' => $purchaseRequest->unit ?? 'pcs',
                'estimated_price' => 0, // Manager will fill this
                'purchase_request_id' => $purchaseRequest->id, // Store for later reference
            ];
        }
        
        // Store purchase request IDs and items in session for pre-filling
        session([
            'purchase_requests_for_shopping_list' => $request->request_ids,
            'shopping_list_prefill_items' => $items,
            'shopping_list_prefill_name' => 'Purchase List - ' . Carbon::now()->format('F d, Y'),
            'shopping_list_prefill_date' => Carbon::now()->next('friday')->format('Y-m-d'),
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Redirecting to shopping list creation...',
            'redirect_url' => route('admin.restaurants.shopping-list.create'),
        ]);
    }

    /**
     * Show templates list
     */
    public function templates()
    {
        $staff = Auth::guard('staff')->user();
        
        $templates = PurchaseRequestTemplate::with('createdBy')
            ->where('created_by', $staff->id)
            ->orderBy('name')
            ->get();
        
        $products = collect();
        
        // Determine route prefix based on staff role
        $routePrefix = 'housekeeper'; // default
        $normalizedRole = strtolower(str_replace([' ', '_'], '', trim($staff->role ?? '')));
        if ($normalizedRole === 'reception') {
            $routePrefix = 'reception';
        } elseif (in_array($normalizedRole, ['barkeeper', 'bartender', 'bar_keeper', 'bar keeper'])) {
            $routePrefix = 'bar-keeper';
            // Fetch registered products for bar keeper to use in templates
            $products = \App\Models\Product::with('variants')
                ->where('type', 'drink')
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        } elseif (in_array($normalizedRole, ['headchef', 'head_chef', 'head chef', 'chef'])) {
            $routePrefix = 'chef-master';
        }
        
        return view('dashboard.purchase-request-templates', compact('templates', 'routePrefix', 'products'));
    }

    /**
     * Store a new template
     */
    public function storeTemplate(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.category' => 'nullable|string|max:255',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit' => 'required|string|max:50',
            'items.*.water_size' => 'nullable|in:small,large',
            'items.*.reason' => 'nullable|string|max:1000',
            'items.*.priority' => 'required|in:low,medium,high,urgent',
        ]);
        
        $staff = Auth::guard('staff')->user();
        
        // Process items similar to purchase request creation
        $processedItems = [];
        foreach ($request->items as $item) {
            $itemName = trim($item['item_name']);
            
            // Remove any existing size suffix
            $itemName = preg_replace('/\s*\(Small\)\s*/i', '', $itemName);
            $itemName = preg_replace('/\s*\(Large\)\s*/i', '', $itemName);
            $itemName = trim($itemName);
            
            // Add size if water category
            if (isset($item['water_size']) && $item['water_size'] && 
                isset($item['category']) && $item['category'] === 'water' && 
                isset($item['unit']) && $item['unit'] === 'pcs') {
                $sizeText = ucfirst($item['water_size']);
                if (stripos($itemName, $sizeText) === false) {
                    $itemName = $itemName . ' (' . $sizeText . ')';
                }
            }
            
            $processedItems[] = [
                'item_name' => $itemName,
                'category' => $item['category'] ?? null,
                'quantity' => $item['quantity'],
                'unit' => $item['unit'],
                'water_size' => ($item['category'] ?? '') === 'water' && ($item['unit'] ?? '') === 'pcs' ? ($item['water_size'] ?? null) : null,
                'reason' => $item['reason'] ?? null,
                'priority' => $item['priority'],
            ];
        }
        
        $template = PurchaseRequestTemplate::create([
            'name' => $request->name,
            'description' => $request->description,
            'items' => $processedItems,
            'created_by' => $staff->id,
            'is_active' => true,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Template created successfully.',
            'template' => $template->load('createdBy'),
        ]);
    }

    /**
     * Get template by ID
     */
    public function getTemplate($id)
    {
        $staff = Auth::guard('staff')->user();
        
        $template = PurchaseRequestTemplate::where('id', $id)
            ->where('created_by', $staff->id)
            ->firstOrFail();
        
        return response()->json([
            'success' => true,
            'template' => $template,
        ]);
    }

    /**
     * Update template
     */
    public function updateTemplate(Request $request, $id)
    {
        $staff = Auth::guard('staff')->user();
        
        $template = PurchaseRequestTemplate::where('id', $id)
            ->where('created_by', $staff->id)
            ->firstOrFail();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.category' => 'nullable|string|max:255',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit' => 'required|string|max:50',
            'items.*.water_size' => 'nullable|in:small,large',
            'items.*.reason' => 'nullable|string|max:1000',
            'items.*.priority' => 'required|in:low,medium,high,urgent',
        ]);
        
        // Process items similar to storeTemplate
        $processedItems = [];
        foreach ($request->items as $item) {
            $itemName = trim($item['item_name']);
            
            $itemName = preg_replace('/\s*\(Small\)\s*/i', '', $itemName);
            $itemName = preg_replace('/\s*\(Large\)\s*/i', '', $itemName);
            $itemName = trim($itemName);
            
            if (isset($item['water_size']) && $item['water_size'] && 
                isset($item['category']) && $item['category'] === 'water' && 
                isset($item['unit']) && $item['unit'] === 'pcs') {
                $sizeText = ucfirst($item['water_size']);
                if (stripos($itemName, $sizeText) === false) {
                    $itemName = $itemName . ' (' . $sizeText . ')';
                }
            }
            
            $processedItems[] = [
                'item_name' => $itemName,
                'category' => $item['category'] ?? null,
                'quantity' => $item['quantity'],
                'unit' => $item['unit'],
                'water_size' => ($item['category'] ?? '') === 'water' && ($item['unit'] ?? '') === 'pcs' ? ($item['water_size'] ?? null) : null,
                'reason' => $item['reason'] ?? null,
                'priority' => $item['priority'],
            ];
        }
        
        $template->update([
            'name' => $request->name,
            'description' => $request->description,
            'items' => $processedItems,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Template updated successfully.',
            'template' => $template->load('createdBy'),
        ]);
    }

    /**
     * Delete template
     */
    public function deleteTemplate($id)
    {
        $staff = Auth::guard('staff')->user();
        
        $template = PurchaseRequestTemplate::where('id', $id)
            ->where('created_by', $staff->id)
            ->firstOrFail();
        
        $template->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Template deleted successfully.',
        ]);
    }
}
