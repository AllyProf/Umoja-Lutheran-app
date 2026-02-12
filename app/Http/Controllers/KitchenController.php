<?php

namespace App\Http\Controllers;

use App\Models\ShoppingList;
use App\Models\ShoppingListItem;
use App\Models\Product;
use App\Models\StockReceipt;
use App\Models\PurchaseRequest;
use App\Models\Staff;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\KitchenInventoryItem;
use App\Models\KitchenStockMovement;
use App\Models\ServiceRequest;
use App\Models\Service;
use App\Mail\StaffTransferNotificationMail;
use Carbon\Carbon;

class KitchenController extends Controller
{
    // === SHOPPING LIST MANAGEMENT ===

    public function index(Request $request)
    {
        $query = ShoppingList::withCount('items')
            ->with('items'); // Eager load items for accessor calculation
        
        // Filter by status if provided
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        $shoppingLists = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->query());
        
        // Add hasPurchasedItems flag to each list
        foreach ($shoppingLists as $list) {
            $list->hasPurchasedItems = $list->items->where('is_purchased', true)->where('is_found', true)->isNotEmpty();
        }
        
        return view('admin.restaurants.shopping_list.index', compact('shoppingLists'));
    }

    public function create()
    {
        // Get existing food products to suggest
        $products = Product::where('category', 'food')
                           ->orWhere('type', 'kitchen')
                           ->orderBy('name')
                           ->get();
        
        // Check if we have pre-filled data from purchase requests
        $prefillItems = session('shopping_list_prefill_items', []);
        $prefillName = session('shopping_list_prefill_name', '');
        $prefillDate = session('shopping_list_prefill_date', date('Y-m-d'));
        $purchaseRequestIds = session('purchase_requests_for_shopping_list', []);
        
        // Clear session data after retrieving
        session()->forget(['shopping_list_prefill_items', 'shopping_list_prefill_name', 'shopping_list_prefill_date', 'purchase_requests_for_shopping_list']);
        
        return view('admin.restaurants.shopping_list.create', compact('products', 'prefillItems', 'prefillName', 'prefillDate', 'purchaseRequestIds'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'shopping_date' => 'nullable|date',
            'market_name' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_name' => 'nullable|string',
            'items.*.quantity' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Calculate total estimated cost
            $totalEstimatedCost = 0;
            foreach ($request->items as $itemData) {
                $totalEstimatedCost += $itemData['estimated_price'] ?? 0;
            }
            
            $list = ShoppingList::create([
                'name' => $request->name,
                'shopping_date' => $request->shopping_date,
                'market_name' => $request->market_name,
                'notes' => $request->notes,
                'status' => 'pending',
                'total_estimated_cost' => $totalEstimatedCost
            ]);

            $purchaseRequestIds = [];
            foreach ($request->items as $itemData) {
                // Check if product exists
                $product = Product::find($itemData['product_id'] ?? null);
                
                $shoppingListItem = ShoppingListItem::create([
                    'shopping_list_id' => $list->id,
                    'product_id' => $product ? $product->id : null,
                    'product_name' => $product ? $product->name : $itemData['product_name'],
                    'category' => $itemData['category'] ?? ($product ? $product->category_name : 'General'),
                    'quantity' => $itemData['quantity'],
                    'unit' => $itemData['unit'] ?? 'pcs',
                    'estimated_price' => $itemData['estimated_price'] ?? 0,
                    'purchase_request_id' => $itemData['purchase_request_id'] ?? null,
                ]);
                
                // Store purchase request ID if provided and update purchase request
                if (isset($itemData['purchase_request_id'])) {
                    $purchaseRequestIds[] = $itemData['purchase_request_id'];
                    
                    // Update purchase request quantity and status
                    $purchaseRequest = PurchaseRequest::find($itemData['purchase_request_id']);
                    if ($purchaseRequest) {
                        $oldQuantity = $purchaseRequest->quantity;
                        $purchaseRequest->update([
                            'shopping_list_id' => $list->id,
                            'status' => 'on_list',
                            'quantity' => $itemData['quantity'], // Update quantity from shopping list
                        ]);
                        
                        // Track changes if quantity was modified
                        if ($oldQuantity != $itemData['quantity']) {
                            $changes = $purchaseRequest->last_changes ?? [];
                            $changes[] = [
                                'field' => 'Quantity',
                                'old' => $oldQuantity . ' ' . ($purchaseRequest->unit ?? 'pcs'),
                                'new' => $itemData['quantity'] . ' ' . ($itemData['unit'] ?? 'pcs'),
                                'changed_by' => Auth::guard('staff')->id(),
                                'changed_at' => now()->toDateTimeString(),
                            ];
                            $purchaseRequest->update([
                                'last_changes' => $changes,
                                'last_edited_at' => now(),
                                'edited_by' => Auth::guard('staff')->id(),
                            ]);
                        }
                    }
                }
            }

            DB::commit();
            return redirect()->route('admin.restaurants.shopping-list.index')->with('success', 'Shopping List created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error creating list: ' . $e->getMessage())->withInput();
        }
    }

    public function show(ShoppingList $shoppingList)
    {
        $shoppingList->load('items');
        return view('admin.restaurants.shopping_list.show', compact('shoppingList'));
    }

    public function edit(ShoppingList $shoppingList)
    {
        $shoppingList->load('items');
        
        // Get existing food products to suggest (reusing logic from create)
        $products = Product::where('category', 'food')
                           ->orWhere('type', 'kitchen')
                           ->orderBy('name')
                           ->get();
                           
        return view('admin.restaurants.shopping_list.edit', compact('shoppingList', 'products'));
    }

    public function update(Request $request, ShoppingList $shoppingList)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'shopping_date' => 'nullable|date',
            'market_name' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_name' => 'nullable|string',
            'items.*.quantity' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Calculate total estimated cost
            $totalEstimatedCost = 0;
            foreach ($request->items as $itemData) {
                $totalEstimatedCost += $itemData['estimated_price'] ?? 0;
            }
            
            $shoppingList->update([
                'name' => $request->name,
                'shopping_date' => $request->shopping_date,
                'market_name' => $request->market_name,
                'notes' => $request->notes,
                'total_estimated_cost' => $totalEstimatedCost
            ]);

            // Sync Items Logic:
            // 1. Get all submitted item IDs (if they exist)
            // 2. Delete items not in the submission
            // 3. Update existing items
            // 4. Create new items

            $submittedIds = [];

            foreach ($request->items as $itemData) {
                // Determine if this is a new item (no ID) or existing
                $itemId = $itemData['id'] ?? null;
                
                // Check if product exists for linking
                $product = Product::find($itemData['product_id'] ?? null);
                
                $data = [
                    'product_id' => $product ? $product->id : null,
                    'product_name' => $product ? $product->name : ($itemData['product_name'] ?? 'Unknown Item'),
                    'category' => $itemData['category'] ?? ($product ? $product->category_name : 'General'),
                    'quantity' => $itemData['quantity'] ?? 0,
                    'unit' => $itemData['unit'] ?? 'pcs',
                    'estimated_price' => $itemData['estimated_price'] ?? 0,
                ];

                if ($itemId) {
                    // Update existing
                    $item = ShoppingListItem::find($itemId);
                    if ($item && $item->shopping_list_id == $shoppingList->id) {
                        $oldQuantity = $item->quantity;
                        $item->update($data);
                        $submittedIds[] = $itemId;
                        
                        // Update purchase request quantity if linked and quantity changed
                        if ($item->purchase_request_id && $oldQuantity != $data['quantity']) {
                            $purchaseRequest = PurchaseRequest::find($item->purchase_request_id);
                            if ($purchaseRequest) {
                                $oldRequestQuantity = $purchaseRequest->quantity;
                                $purchaseRequest->update([
                                    'quantity' => $data['quantity'],
                                ]);
                                
                                // Track changes
                                $changes = $purchaseRequest->last_changes ?? [];
                                $changes[] = [
                                    'field' => 'Quantity',
                                    'old' => $oldRequestQuantity . ' ' . ($purchaseRequest->unit ?? 'pcs'),
                                    'new' => $data['quantity'] . ' ' . ($data['unit'] ?? 'pcs'),
                                    'changed_by' => Auth::guard('staff')->id(),
                                    'changed_at' => now()->toDateTimeString(),
                                ];
                                $purchaseRequest->update([
                                    'last_changes' => $changes,
                                    'last_edited_at' => now(),
                                    'edited_by' => Auth::guard('staff')->id(),
                                ]);
                            }
                        }
                    }
                } else {
                    // Create new
                    $newItem = $shoppingList->items()->create($data);
                    $submittedIds[] = $newItem->id;
                    
                    // Update purchase request if linked
                    if (isset($itemData['purchase_request_id'])) {
                        $purchaseRequest = PurchaseRequest::find($itemData['purchase_request_id']);
                        if ($purchaseRequest) {
                            $oldQuantity = $purchaseRequest->quantity;
                            $purchaseRequest->update([
                                'shopping_list_id' => $shoppingList->id,
                                'status' => 'on_list',
                                'quantity' => $data['quantity'],
                            ]);
                            
                            // Track changes if quantity was modified
                            if ($oldQuantity != $data['quantity']) {
                                $changes = $purchaseRequest->last_changes ?? [];
                                $changes[] = [
                                    'field' => 'Quantity',
                                    'old' => $oldQuantity . ' ' . ($purchaseRequest->unit ?? 'pcs'),
                                    'new' => $data['quantity'] . ' ' . ($data['unit'] ?? 'pcs'),
                                    'changed_by' => Auth::guard('staff')->id(),
                                    'changed_at' => now()->toDateTimeString(),
                                ];
                                $purchaseRequest->update([
                                    'last_changes' => $changes,
                                    'last_edited_at' => now(),
                                    'edited_by' => Auth::guard('staff')->id(),
                                ]);
                            }
                        }
                    }
                }
            }

            // Remove items not in the submission
            $shoppingList->items()->whereNotIn('id', $submittedIds)->delete();

            DB::commit();
            return redirect()->route('admin.restaurants.shopping-list.index')->with('success', 'Shopping List updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating list: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(ShoppingList $shoppingList)
    {
        try {
            $shoppingList->delete();
            return redirect()->route('admin.restaurants.shopping-list.index')->with('success', 'Shopping list deleted successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting list: ' . $e->getMessage());
        }
    }

    // === PURCHASE RECORDING ===

    public function recordPurchaseView(ShoppingList $shoppingList)
    {
        if ($shoppingList->status === 'completed') {
            return redirect()->back()->with('info', 'This list is already completed.');
        }
        $shoppingList->load('items');
        return view('admin.restaurants.shopping_list.record_purchase', compact('shoppingList'));
    }

    public function updatePurchase(Request $request, ShoppingList $shoppingList)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.purchased_quantity' => 'nullable|numeric|min:0',
            'items.*.purchased_cost' => 'nullable|numeric|min:0',
            'items.*.expiry_date' => 'nullable|date',
            'items.*.unit_price' => 'nullable|numeric|min:0',
            'items.*.storage_location' => 'nullable|string|max:255',
            'items.*.is_found' => 'nullable|boolean',
            'budget_amount' => 'nullable|numeric|min:0',
            // PIC-based pricing fields
            'items.*.servings_per_pic' => 'nullable|integer|min:1',
            'items.*.selling_unit' => 'nullable|in:pic,glass,tot,shot,cocktail',
            'items.*.selling_price_per_pic' => 'nullable|numeric|min:0',
            'items.*.selling_price_per_serving' => 'nullable|numeric|min:0',
            'items.*.price_adjustment_reason' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $totalCost = 0;
            
            foreach ($request->items as $itemId => $data) {
                $item = ShoppingListItem::findOrFail($itemId);
                $boughtQty = round((float)($data['purchased_quantity'] ?? 0));
                $cost = round((float)($data['purchased_cost'] ?? 0));
                $expiryDate = $data['expiry_date'] ?? null;
                $unitPrice = isset($data['unit_price']) ? (float)$data['unit_price'] : null;
                
                // CRITICAL FIX: Checkbox is only present if checked
                $isFound = isset($data['is_found']) && $data['is_found'] == '1';

                // If unit price is provided but cost is not, calculate cost
                if ($unitPrice && !$cost && $boughtQty > 0) {
                    $cost = $unitPrice * $boughtQty;
                }

                $updateData = [
                    'purchased_quantity' => $boughtQty,
                    'purchased_cost' => $cost,
                    'expiry_date' => $expiryDate,
                    'is_purchased' => $isFound && $boughtQty > 0,
                    'is_found' => $isFound
                ];
                
                // Calculate unit price if not explicitly provided
                if ($unitPrice) {
                    $updateData['unit_price'] = $unitPrice;
                } elseif ($boughtQty > 0 && $cost > 0) {
                    $updateData['unit_price'] = $cost / $boughtQty;
                }
                
                $item->update($updateData);

                // Update purchase request status if it exists
                if ($item->purchaseRequest && $updateData['is_purchased']) {
                    $item->purchaseRequest->update(['status' => 'purchased']);
                }

                // Add to inventory only if found AND quantity > 0
                if ($isFound && $boughtQty > 0) {
                    $totalCost += $cost;

                    if ($request->has('finalize')) {
                        // We no longer add to inventory automatically on finalize
                        // since we now use the transfer/receive system.
                        // However, we still record that it's finalized.
                    }
                }
            }

            // Update budget tracking
            // Use budget from request, or existing budget, or default to estimated cost
            $budgetAmount = $request->budget_amount ?? $shoppingList->budget_amount ?? $shoppingList->total_estimated_cost ?? $shoppingList->items->sum('estimated_price');
            $amountUsed = $totalCost;
            $amountRemaining = $budgetAmount - $amountUsed;

            $shoppingList->total_actual_cost = $totalCost;
            $shoppingList->budget_amount = $budgetAmount;
            $shoppingList->amount_used = $amountUsed;
            $shoppingList->amount_remaining = $amountRemaining;
            
            if ($request->has('finalize')) {
                $shoppingList->status = 'completed';
                // Here we should probably officially add to stock if not done above
            }
            
            $shoppingList->save();

            DB::commit();

            if ($request->has('finalize')) {
                 return redirect()->route('admin.restaurants.shopping-list.receiving-report', ['shoppingList' => $shoppingList->id])->with('success', 'Purchases recorded. Print the receiving report, then transfer items to departments.');
            }

            return back()->with('success', 'Progress saved.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating purchase: ' . $e->getMessage());
        }
    }
    
    public function download(ShoppingList $shoppingList)
    {
        // Eager load items with their purchase requests and related staff
        $shoppingList->load([
            'items.purchaseRequest.requestedBy',
            'items.purchaseRequest.approvedBy'
        ]);
        
        // Get the manager who finalized the purchase (current user or from shopping list)
        $boughtBy = Auth::guard('staff')->user();
        
        return view('admin.restaurants.shopping_list.download', compact('shoppingList', 'boughtBy'));
    }
    
    /**
     * Download/Print receiving report
     */
    public function receivingReport(ShoppingList $shoppingList)
    {
        // Refresh the model to get latest data
        $shoppingList->refresh();
        $shoppingList->load('items');
        
        return view('admin.restaurants.shopping_list.receiving_report', compact('shoppingList'));
    }
    
    /**
     * Show all purchased items from the market (grouped by shopping list)
     */
    public function purchasedItems(Request $request)
    {
        $query = ShoppingList::whereHas('items', function($q) {
                $q->where('is_purchased', true)
                  ->where('is_found', true)
                  ->where('purchased_quantity', '>', 0);
            })
            ->with(['items' => function($q) {
                $q->where('is_purchased', true)
                  ->where('is_found', true)
                  ->where('purchased_quantity', '>', 0);
            }]);
        
        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->where('shopping_date', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->where('shopping_date', '<=', $request->date_to);
        }
        
        // Filter by shopping list
        if ($request->has('shopping_list_id') && $request->shopping_list_id) {
            $query->where('id', $request->shopping_list_id);
        }
        
        $shoppingLists = $query->orderBy('shopping_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        // Statistics
        $totalLists = ShoppingList::whereHas('items', function($q) {
                $q->where('is_purchased', true)
                  ->where('is_found', true)
                  ->where('purchased_quantity', '>', 0);
            })
            ->count();
        
        $totalItems = ShoppingListItem::where('is_purchased', true)
            ->where('is_found', true)
            ->where('purchased_quantity', '>', 0)
            ->whereNotNull('shopping_list_id')
            ->count();
        
        $totalCost = ShoppingListItem::where('is_purchased', true)
            ->where('is_found', true)
            ->whereNotNull('shopping_list_id')
            ->sum('purchased_cost');
        
        // Get all shopping lists for filter dropdown
        $allShoppingListsForFilter = ShoppingList::whereHas('items', function($q) {
                $q->where('is_purchased', true)
                  ->where('is_found', true)
                  ->where('purchased_quantity', '>', 0);
            })
            ->orderBy('created_at', 'desc')
            ->get();
        
        $activePage = 'purchased-items';
        
        return view('admin.restaurants.shopping_list.purchased_items', compact('shoppingLists', 'allShoppingListsForFilter', 'totalLists', 'totalItems', 'totalCost', 'activePage'));
    }
    
    /**
     * Show all items ready for transfer grouped by department
     */
    public function transfers(Request $request)
    {
        // Get all items ready for transfer (purchased, found, NOT YET TRANSFERRED)
        $items = ShoppingListItem::where('is_purchased', true)
            ->where('is_found', true)
            ->where('purchased_quantity', '>', 0)
            ->whereNull('transferred_to_department') // Only show items not yet sent to a department
            ->where('is_received_by_department', false)
            ->with(['purchaseRequest.requestedBy', 'shoppingList'])
            ->get();
        
        // Group items by department
        $itemsByDepartment = [];
        foreach ($items as $item) {
            $department = $item->transferred_to_department ?? 'Other';
            
            if (!$item->transferred_to_department) {
                if ($item->purchaseRequest) {
                    $department = $item->purchaseRequest->getDepartmentName();
                } else {
                    // Fallback: determine department from category
                    $category = strtolower($item->category ?? '');
                    if (in_array($category, ['cleaning_supplies', 'water', 'linens'])) {
                        $department = 'Housekeeping';
                    } elseif (in_array($category, ['alcoholic_beverage', 'non_alcoholic_beverage', 'spirits'])) {
                        $department = 'Bar';
                    } elseif (in_array($category, ['food', 'meat_poultry', 'seafood', 'pantry', 'dairy', 'baking', 'vegetables', 'spices', 'sauces', 'bakery'])) {
                        $department = 'Food';
                    } else {
                        $department = 'Reception';
                    }
                }
            }
            
            if (!isset($itemsByDepartment[$department])) {
                $itemsByDepartment[$department] = [];
            }

            // Pre-resolve variant to ensure prices show up in the form
            // This also saves the product_variant_id link if a match is found
            $variant = $item->productVariant;
            if ($variant && !$item->product_variant_id) {
                $item->update(['product_variant_id' => $variant->id]);
            }
            
            $itemsByDepartment[$department][] = $item;
        }
        
        // Sort departments: Housekeeping, Reception, Bar, Food, Other
        $departmentOrder = ['Housekeeping', 'Reception', 'Bar', 'Food', 'Other'];
        $itemsByDepartment = collect($itemsByDepartment)->sortBy(function($items, $dept) use ($departmentOrder) {
            $pos = array_search($dept, $departmentOrder);
            return $pos === false ? 999 : $pos;
        })->toArray();
        
        $activePage = 'transfers';
        
        return view('admin.restaurants.shopping_list.transfers', compact('itemsByDepartment', 'activePage'));
    }

    // === KITCHEN STOCK VIEW ===
    
    public function stock()
    {
        // 1. Existing Products
        $products = Product::where('category', 'food')
                           ->orWhere('type', 'kitchen')
                           ->orderBy('name')
                           ->get();

        $stockData = [];
        foreach ($products as $product) {
            // 1. Total Received from Stock Receipts (Packages x Items)
            $receiptsReceived = DB::table('stock_receipts')
                          ->join('product_variants', 'stock_receipts.product_variant_id', '=', 'product_variants.id')
                          ->where('stock_receipts.product_id', $product->id)
                          ->select(DB::raw('SUM(stock_receipts.quantity_received_packages * product_variants.items_per_package) as total_received'))
                          ->first()->total_received ?? 0;

            // 2. Total Received from Shopping List Purchases
            $shoppingListReceived = ShoppingListItem::where('product_id', $product->id)
                                        ->where('is_purchased', true)
                                        ->sum('purchased_quantity');

            $totalReceived = (float)$receiptsReceived + (float)$shoppingListReceived;

            // ONLY show if it has been received at least once (as requested)
            if ($totalReceived > 0) {
                // Total Consumed (from Recipes)
                $consumed = DB::table('recipe_consumptions')
                              ->where('product_id', $product->id)
                              ->sum('quantity_consumed');

                // Try to get a unit from the first variant
                $variant = $product->variants()->first();
                $unit = '';
                if ($variant) {
                    // Extract unit part from measurement (e.g. "25kg" -> "kg") or use packaging
                    $unit = $variant->measurement; 
                    // If measurement is just a number, we might need more logic, 
                    // but usually it contains the unit in this system.
                }

                $stockData[] = (object)[
                    'id' => $product->id,
                    'name' => $product->name,
                    'category' => $product->category_name,
                    'received' => (float)$totalReceived,
                    'consumed' => (float)$consumed,
                    'balance' => (float)($totalReceived - $consumed),
                    'unit' => $unit,
                    'image' => $product->image
                ];
            }
        }

        // 2. Unlinked Purchased Items
        $unlinkedItems = ShoppingListItem::whereNull('product_id')
                            ->where('is_purchased', true)
                            ->get()
                            ->groupBy('product_name')
                            ->map(function ($items) {
                                return [
                                    'name' => $items->first()->product_name,
                                    'category' => $items->first()->category,
                                    'total_qty' => $items->sum('purchased_quantity'),
                                    'unit' => $items->first()->unit,
                                    'storage' => $items->last()->storage_location,
                                ];
                            });
                           
        return view('admin.restaurants.kitchen.stock', compact('stockData', 'unlinkedItems'));
    }

    public function dashboard()
    {
        $user = Auth::guard('staff')->user();
        $isChef = \App\Services\RolePermissionService::hasRole($user, 'head_chef');

        $today = now()->startOfDay();
        $stats = [
            'shopping_lists' => ShoppingList::count(),
            'pending_lists' => ShoppingList::where('status', 'pending')->count(),
            'stock_items' => Product::where('category', 'food')->orWhere('type', 'kitchen')->count(),
            'today_orders' => \App\Models\ServiceRequest::where('status', 'completed')
                ->whereDate('completed_at', $today)
                ->whereIn('service_id', \App\Models\Service::whereIn('category', ['food', 'restaurant'])->pluck('id'))
                ->count(),
            'total_revenue' => \App\Models\ServiceRequest::where('status', 'completed')
                ->whereDate('completed_at', $today)
                ->whereIn('service_id', \App\Models\Service::whereIn('category', ['food', 'restaurant'])->pluck('id'))
                ->sum('total_price_tsh'),
        ];

        // Get Pending Food Orders (Service Requests)
        $foodCategories = ['food', 'restaurant'];
        
        $pendingOrders = \App\Models\ServiceRequest::with(['booking.room', 'service'])
            ->where(function($q) use ($foodCategories) {
                // Filter by category OR explicit ID (Generic Food Order)
                $q->whereHas('service', function($query) use ($foodCategories) {
                    $query->whereIn('category', $foodCategories);
                })->orWhere('service_id', 4); // Generic Food Order Check
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
        
        // Get available recipes for walk-in orders
        $recipes = \App\Models\Recipe::orderBy('name')
            ->get()
            ->map(function($recipe) {
                return (object)[
                    'id' => $recipe->id,
                    'name' => $recipe->name,
                    'price_tsh' => $recipe->selling_price ?? 0,
                    'image' => $recipe->image ? asset('storage/'.$recipe->image) : 'https://img.icons8.com/color/144/restaurant.png',
                ];
            });

        // Only fetch list records if not a chef (to avoid clutter or as requested)
        $recentLists = $isChef ? collect() : ShoppingList::orderBy('created_at', 'desc')->limit(5)->get();
        $role = $isChef ? 'head_chef' : 'manager';

        return view('admin.restaurants.kitchen.dashboard', compact(
            'stats', 
            'recentLists', 
            'isChef', 
            'role',
            'pendingOrders',
            'totalPendingOrders',
            'activeCeremonies',
            'recipes'
        ));
    }
    
    /**
     * Show transfer items to departments page
     */
    public function transferItems(ShoppingList $shoppingList)
    {
        // Allow transfer if status is completed OR if items have been purchased (for flexibility)
        $hasPurchasedItems = $shoppingList->items()->where('is_purchased', true)->exists();
        
        if ($shoppingList->status !== 'completed' && !$hasPurchasedItems) {
            return redirect()->route('admin.restaurants.shopping-list.index')
                ->with('error', 'Please record purchases before transferring items.');
        }
        
        $shoppingList->load(['items.purchaseRequest.requestedBy']);
        
        // Group items by department - only show purchased items that haven't been transferred yet
        $itemsByDepartment = [];
        foreach ($shoppingList->items as $item) {
            // Only include items that were found/purchased and NOT YET TRANSFERRED
            if ($item->is_found && $item->purchased_quantity > 0 && is_null($item->transferred_to_department)) {
                // Ensure variant is linked for correct pre-filling of selling config
                $variant = $item->productVariant;
                if ($variant && !$item->product_variant_id) {
                    $item->update(['product_variant_id' => $variant->id]);
                }

                if ($item->purchaseRequest) {
                    $deptName = $item->purchaseRequest->getDepartmentName();
                    if (!isset($itemsByDepartment[$deptName])) {
                        $itemsByDepartment[$deptName] = [];
                    }
                    $itemsByDepartment[$deptName][] = $item;
                } else {
                    // Fallback to Bar/Food based on category if no purchase request (manual additions to list)
                    $category = strtolower($item->category ?? '');
                    $deptName = 'Other';
                    if (in_array($category, ['beverages', 'alcoholic_beverage', 'spirits'])) $deptName = 'Bar';
                    elseif (in_array($category, ['food', 'meat_poultry', 'dairy'])) $deptName = 'Food';
                    
                    if (!isset($itemsByDepartment[$deptName])) $itemsByDepartment[$deptName] = [];
                    $itemsByDepartment[$deptName][] = $item;
                }
            }
        }
        
        // Sort departments: Housekeeping, Reception, Bar, Food, Other
        $departmentOrder = ['Housekeeping', 'Reception', 'Bar', 'Food', 'Other'];
        $itemsByDepartment = collect($itemsByDepartment)->sortBy(function($items, $dept) use ($departmentOrder) {
            $pos = array_search($dept, $departmentOrder);
            return $pos === false ? 999 : $pos;
        })->toArray();
        
        $activePage = 'transfer';
        
        return view('admin.restaurants.shopping_list.transfer', compact('shoppingList', 'itemsByDepartment', 'activePage'));
    }
    
    /**
     * Process bulk transfer of all items to departments (from transfers index page)
     */
    public function bulkTransfer(Request $request)
    {
        $request->validate([
            'transfers' => 'required|array',
            'transfers.*.item_id' => 'required|exists:shopping_list_items,id',
            'transfers.*.department' => 'required|string',
            'transfers.*.quantity' => 'required|numeric|min:0.01',
            'transfers.*.servings_per_pic' => 'nullable|integer|min:1',
            'transfers.*.selling_unit' => 'nullable|string',
            'transfers.*.selling_price_per_pic' => 'nullable|numeric|min:0',
            'transfers.*.selling_price_per_serving' => 'nullable|numeric|min:0',
        ]);
        
        DB::beginTransaction();
        try {
            $transferredCount = 0;
            $itemsByDepartment = [];
            
            // Group items by department
            foreach ($request->transfers as $transferData) {
                $item = ShoppingListItem::findOrFail($transferData['item_id']);
                
                $department = $transferData['department'];
            
                // Mark as transferred (but NOT received yet - department staff will confirm receipt)
                $item->update([
                    'transferred_to_department' => $department,
                    'is_received_by_department' => false,
                    'received_by_department_at' => null,
                ]);
                $transferredCount++;

                // Update product variant with PIC configuration if provided (usually for Bar)
                $variant = $item->fresh()->productVariant;
                if ($variant && strcasecmp($department, 'bar') === 0 && isset($transferData['selling_method'])) {
                    // Link the variant to the item if it's not already linked
                    if (!$item->product_variant_id) {
                        $item->update(['product_variant_id' => $variant->id]);
                    }
                    
                    $method = $transferData['selling_method'];
                    
                    $updateData = [
                        'servings_per_pic' => $transferData['servings_per_pic'] ?? 1,
                        'selling_unit' => $transferData['selling_unit'] ?? 'pic',
                        'can_sell_as_pic' => in_array($method, ['pic', 'mixed']),
                        'can_sell_as_serving' => in_array($method, ['serving', 'mixed']),
                    ];

                    // ONLY update prices if they are provided (prevent null overwrite)
                    if (isset($transferData['selling_price_per_pic']) && $transferData['selling_price_per_pic'] !== '') {
                        $updateData['selling_price_per_pic'] = (float)$transferData['selling_price_per_pic'];
                    }
                    
                    if (isset($transferData['selling_price_per_serving']) && $transferData['selling_price_per_serving'] !== '') {
                        $updateData['selling_price_per_serving'] = (float)$transferData['selling_price_per_serving'];
                    }

                    $variant->update($updateData);

                    \Log::info('Bar transfer configuration updated safely', [
                        'item' => $item->product_name,
                        'variant_id' => $variant->id,
                        'method' => $method,
                        'updated_fields' => array_keys($updateData)
                    ]);
                }
                
                // Group items by department for email/notification
                if (!isset($itemsByDepartment[$department])) {
                    $itemsByDepartment[$department] = [];
                }
                $itemsByDepartment[$department][] = $item->fresh();
            }
            
            DB::commit();
            
            // Send emails and create notifications for each department
            $transferredBy = Auth::guard('staff')->user();
            
            foreach ($itemsByDepartment as $department => $items) {
                // Get staff members in this department
                $departmentStaff = Staff::where('is_active', true)
                    ->get()
                    ->filter(function($staff) use ($department) {
                        return strcasecmp($staff->getDepartmentName(), $department) === 0;
                    });
                
                \Log::info('Transfer notifications - Department staff found', [
                    'department' => $department,
                    'staff_count' => $departmentStaff->count(),
                    'staff_ids' => $departmentStaff->pluck('id')->toArray(),
                    'staff_roles' => $departmentStaff->pluck('role')->toArray()
                ]);
                
                // Send email to each staff member
                foreach ($departmentStaff as $staff) {
                    if ($staff->isNotificationEnabled('purchase_request')) {
                        try {
                            Mail::to($staff->email)->send(
                                new StaffTransferNotificationMail($items, $department, $transferredBy)
                            );
                        } catch (\Exception $e) {
                            \Log::error('Failed to send transfer notification email to staff: ' . $staff->email . ' - ' . $e->getMessage());
                        }
                    }
                }
                
                // Create notifications for each staff member
                $itemCount = count($items);
                $totalCost = collect($items)->sum('purchased_cost');
                $firstItem = $items[0]; // Use first item for notifiable reference
                
                foreach ($departmentStaff as $staff) {
                    // Determine the correct route based on staff role
                    $routeName = 'housekeeper.purchase-requests.my'; // Default
                    if ($staff->role === 'reception') {
                        $routeName = 'reception.purchase-requests.my';
                    } elseif (in_array(strtolower($staff->role), ['bar_keeper', 'bar keeper', 'bartender'])) {
                        // Bar keeper might use housekeeper route or have their own
                        $routeName = 'housekeeper.purchase-requests.my';
                    } elseif (in_array(strtolower($staff->role), ['head_chef', 'head chef', 'chef'])) {
                        // Chef might use housekeeper route or have their own
                        $routeName = 'housekeeper.purchase-requests.my';
                    }
                    
                    try {
                        $notification = Notification::create([
                            'type' => 'purchase_request',
                            'title' => 'Items Transferred to ' . $department . ' Department',
                            'message' => "{$itemCount} item(s) have been transferred to your department. Total cost: " . number_format($totalCost, 2) . " TZS. Please receive them in 'My Requests'.",
                            'icon' => 'fa-exchange',
                            'color' => 'info',
                            'user_id' => $staff->id,
                            'role' => $staff->role,
                            'notifiable_id' => $firstItem->id,
                            'notifiable_type' => ShoppingListItem::class,
                            'link' => route($routeName),
                            'is_read' => false,
                        ]);
                        \Log::info('Notification created successfully', [
                            'notification_id' => $notification->id,
                            'staff_id' => $staff->id,
                            'staff_role' => $staff->role,
                            'department' => $department
                        ]);
                    } catch (\Exception $e) {
                        \Log::error('Failed to create transfer notification for staff', [
                            'staff_id' => $staff->id,
                            'staff_email' => $staff->email,
                            'staff_role' => $staff->role,
                            'department' => $department,
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ]);
                    }
                }
            }
            
            // Log summary of notifications created
            $totalNotificationsCreated = 0;
            foreach ($itemsByDepartment as $department => $items) {
                $departmentStaff = Staff::where('is_active', true)
                    ->get()
                    ->filter(function($staff) use ($department) {
                        return strcasecmp($staff->getDepartmentName(), $department) === 0;
                    });
                $totalNotificationsCreated += $departmentStaff->count();
            }
            
            \Log::info('Transfer completed - Summary', [
                'items_transferred' => $transferredCount,
                'departments_notified' => count($itemsByDepartment),
                'total_notifications_created' => $totalNotificationsCreated
            ]);
            
            return redirect()->route('admin.restaurants.shopping-list.index')
                ->with('success', "Successfully transferred {$transferredCount} item(s) to departments. Notifications have been sent to department staff.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error transferring items: ' . $e->getMessage());
        }
    }
    
    /**
     * Process transfer of items to departments (from specific shopping list)
     */
    public function processTransfer(Request $request, ShoppingList $shoppingList)
    {
        $request->validate([
            'transfers' => 'required|array',
            'transfers.*.item_id' => 'required|exists:shopping_list_items,id',
            'transfers.*.department' => 'required|string',
            'transfers.*.quantity' => 'required|numeric|min:0.01',
            'transfers.*.servings_per_pic' => 'nullable|integer|min:1',
            'transfers.*.selling_unit' => 'nullable|string',
            'transfers.*.selling_price_per_pic' => 'nullable|numeric|min:0',
            'transfers.*.selling_price_per_serving' => 'nullable|numeric|min:0',
        ]);
        
        DB::beginTransaction();
        try {
            foreach ($request->transfers as $transferData) {
                $item = ShoppingListItem::findOrFail($transferData['item_id']);
                
                // Update item with transfer info
                $item->update([
                    'transferred_to_department' => $transferData['department'],
                    'is_received_by_department' => false, // Ensure it's false until staff receives it
                    'received_by_department_at' => null,
                ]);
                
                // Update product variant with PIC configuration if provided (usually for Bar)
                $variant = $item->fresh()->productVariant;
                if ($variant && isset($transferData['selling_method'])) {
                    // Link the variant to the item if it's not already linked
                    if (!$item->product_variant_id) {
                        $item->update(['product_variant_id' => $variant->id]);
                    }
                    
                    $method = $transferData['selling_method'];
                    
                    $updateData = [
                        'servings_per_pic' => $transferData['servings_per_pic'] ?? 1,
                        'selling_unit' => $transferData['selling_unit'] ?? 'pic',
                        'can_sell_as_pic' => in_array($method, ['pic', 'mixed']),
                        'can_sell_as_serving' => in_array($method, ['serving', 'mixed']),
                    ];

                    // ONLY update prices if they are provided (prevent null overwrite)
                    if (isset($transferData['selling_price_per_pic']) && $transferData['selling_price_per_pic'] !== '') {
                        $updateData['selling_price_per_pic'] = (float)$transferData['selling_price_per_pic'];
                    }
                    
                    if (isset($transferData['selling_price_per_serving']) && $transferData['selling_price_per_serving'] !== '') {
                        $updateData['selling_price_per_serving'] = (float)$transferData['selling_price_per_serving'];
                    }

                    $variant->update($updateData);
                }
            }
            
            DB::commit();
            return redirect()->route('admin.restaurants.shopping-list.index')
                ->with('success', 'Items transferred to departments. Departments can now receive them.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error transferring items: ' . $e->getMessage());
        }
    }

    /**
     * View Kitchen Inventory (Mirror Housekeeper workflow)
     */
    public function inventory(Request $request)
    {
        $barCategories = ['drinks', 'alcoholic_beverage', 'non_alcoholic_beverage', 'water', 'juices', 'energy_drinks', 'spirits', 'wines', 'cocktails', 'beer'];
        
        $query = KitchenInventoryItem::whereNotIn('category', $barCategories);
        
        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }
        
        if ($request->has('search') && $request->search) {
            $query->where('name', 'LIKE', '%' . $request->search . '%');
        }
        
        $inventoryItems = $query->orderBy('name')->paginate(50);
        $categories = KitchenInventoryItem::whereNotIn('category', $barCategories)
            ->select('category')
            ->distinct()
            ->pluck('category');
        
        $stats = [
            'total_items' => KitchenInventoryItem::whereNotIn('category', $barCategories)->count(),
            'low_stock' => KitchenInventoryItem::whereNotIn('category', $barCategories)
                ->whereRaw('current_stock <= minimum_stock')
                ->count(),
            'out_of_stock' => KitchenInventoryItem::whereNotIn('category', $barCategories)
                ->where('current_stock', '<=', 0)
                ->count(),
        ];
        
        return view('admin.restaurants.kitchen.inventory', compact('inventoryItems', 'categories', 'stats'));
    }

    /**
     * Update Inventory Stock Level (Supply/Adjustment)
     */
    public function updateInventoryStock(Request $request, KitchenInventoryItem $item)
    {
        $request->validate([
            'quantity' => 'required|numeric|min:0.01',
            'type' => 'required|in:supply,adjustment,guest_use,staff_use,destroyed',
            'notes' => 'nullable|string|max:255',
        ]);
        
        $staff = Auth::guard('staff')->user();
        $qtyChange = (float)$request->quantity;
        
        DB::beginTransaction();
        try {
            if (in_array($request->type, ['guest_use', 'staff_use', 'destroyed'])) {
                // Consumption or Destruction: subtract from stock
                if ($item->current_stock < $qtyChange) {
                    return response()->json(['success' => false, 'message' => 'Insufficient stock. Current balance: ' . $item->current_stock], 400);
                }
                $item->current_stock -= $qtyChange;
            } elseif ($request->type === 'supply') {
                $item->current_stock += $qtyChange;
            } else {
                // Adjustment: can be positive or negative (manual fixes)
                $item->current_stock += $qtyChange;
            }
            
            $item->save();
            
            KitchenStockMovement::create([
                'inventory_item_id' => $item->id,
                'movement_type' => $request->type,
                'quantity' => $qtyChange, // Store actual change (not absolute)
                'performed_by' => $staff->id,
                'movement_date' => now(),
                'notes' => $request->notes ?? ucfirst(str_replace('_', ' ', $request->type)) . ' update',
            ]);
            
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Stock updated successfully. New balance: ' . $item->current_stock,
                'new_stock' => $item->current_stock,
                'status' => $item->getStockStatus()
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Update Warehouse Minimum Stock Level
     */
    public function updateMinimumStock(Request $request, KitchenInventoryItem $item)
    {
        $request->validate(['minimum_stock' => 'required|numeric|min:0']);
        $item->update(['minimum_stock' => $request->minimum_stock]);
        return response()->json(['success' => true, 'message' => 'Minimum stock updated.']);
    }

    /**
     * Get Usage Tracking for an item
     */
    public function getItemUsageTrack(KitchenInventoryItem $item)
    {
        $movements = KitchenStockMovement::where('inventory_item_id', $item->id)
            ->with('performedBy')
            ->orderBy('created_at', 'desc')
            ->limit(30)
            ->get();

        $runningBalance = (float)$item->current_stock;
        $formattedMovements = [];

        foreach ($movements as $m) {
            $isAddition = in_array($m->movement_type, ['supply', 'manual_add']);
            $isSubtraction = in_array($m->movement_type, ['sale', 'guest_use', 'staff_use', 'internal_use', 'destroyed']);
            
            // For adjustments, we might need a direction check if quantity can be negative
            // But usually the types above cover 99% of cases
            if ($m->movement_type === 'adjustment') {
                // If notes or something indicated direction we could check, 
                // but let's assume it follows the sign if we ever store negative
                $isAddition = $m->quantity > 0;
            }

            $currentBalance = $runningBalance;
            
            // Calculate what balance was BEFORE this movement for the NEXT iteration
            if ($isAddition) {
                $runningBalance -= (float)$m->quantity;
            } else {
                $runningBalance += (float)$m->quantity;
            }

            $formattedMovements[] = [
                'date' => $m->created_at->format('M d, Y H:i'),
                'type' => ucfirst(str_replace('_', ' ', $m->movement_type)),
                'quantity' => ($isAddition ? '+' : '-') . number_format($m->quantity, 2),
                'balance' => number_format($currentBalance, 2),
                'unit' => $item->unit,
                'user' => $m->performedBy->name ?? 'System',
                'notes' => $m->notes,
                'is_addition' => $isAddition
            ];
        }

        return response()->json([
            'success' => true,
            'item_name' => $item->name,
            'movements' => $formattedMovements
        ]);
    }

    /**
     * Release/Use items manually (In Use column)
     */
    public function releaseStock(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:kitchen_inventory_items,id',
            'quantity' => 'required|numeric|min:0.01',
            'type' => 'required|in:sale,internal_use', // "Sold" or "In Use"
            'unit_price' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $item = KitchenInventoryItem::findOrFail($request->item_id);
        $staff = Auth::guard('staff')->user();

        if ($item->current_stock < $request->quantity) {
             return response()->json(['success' => false, 'message' => 'Insufficient stock. Only ' . $item->current_stock . ' ' . $item->unit . ' available.'], 400);
        }

        DB::beginTransaction();
        try {
            $item->current_stock -= $request->quantity;
            $item->save();

            KitchenStockMovement::create([
                'inventory_item_id' => $item->id,
                'movement_type' => $request->type,
                'quantity' => $request->quantity,
                'unit_price' => $request->unit_price ?? 0,
                'total_amount' => ($request->unit_price ?? 0) * $request->quantity,
                'performed_by' => $staff->id,
                'movement_date' => now(),
                'notes' => $request->notes ?? 'Manual ' . str_replace('_', ' ', $request->type),
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Stock released successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Kitchen Daily Stock Sheet Report
     */
    public function reports(Request $request)
    {
        $reportType = $request->get('date_type', 'daily');
        $date = $request->date ? Carbon::parse($request->date) : now();
        
        if ($reportType === 'weekly') {
            $startDate = $date->copy()->startOfWeek();
            $endDate = $date->copy()->endOfWeek();
        } else {
            $startDate = $date->copy()->startOfDay();
            $endDate = $date->copy()->endOfDay();
        }

        // 1. Get ONLY Kitchen-related inventory items (Exclude ALL Bar/Drink categories)
        $barCategories = [
            'spirits', 'alcoholic_beverage', 'liquor', 'wine', 'beer', 
            'beers', 'soft_drinks', 'beverages', 'water', 'cocktails', 
            'energy_drinks', 'non_alcoholic_beverage', 'juices', 'drinks'
        ];
        $items = KitchenInventoryItem::whereNotIn('category', $barCategories)
            ->orderBy('category')
            ->orderBy('name')
            ->get();
        
        $reportData = [];
        foreach ($items as $item) {
            // Opening Stock Calculation (Movements BEFORE the period)
            $movementsBefore = KitchenStockMovement::where('inventory_item_id', $item->id)
                ->where('movement_date', '<', $startDate->toDateString())
                ->get();
            
            $openingStock = 0;
            foreach ($movementsBefore as $m) {
                if (in_array($m->movement_type, ['supply', 'adjustment', 'manual_add'])) {
                    $openingStock += (float)$m->quantity;
                } else {
                    $openingStock -= (float)$m->quantity;
                }
            }

            // Movements IN the period
            $movementsInPeriod = KitchenStockMovement::where('inventory_item_id', $item->id)
                ->whereBetween('movement_date', [$startDate->toDateString(), $endDate->toDateString()])
                ->get();
            
            $received = $movementsInPeriod->whereIn('movement_type', ['supply', 'manual_add'])->sum('quantity');
            
            // Adjustments in period: add all of them (assuming they are signed now)
            $adjustmentInPeriod = $movementsInPeriod->where('movement_type', 'adjustment')->sum('quantity');
            
            $sold = $movementsInPeriod->whereIn('movement_type', ['sale', 'guest_use'])->sum('quantity');
            $inUse = $movementsInPeriod->whereIn('movement_type', ['internal_use', 'staff_use'])->sum('quantity');
            $lost = $movementsInPeriod->where('movement_type', 'destroyed')->sum('quantity');
            $soldAmount = $movementsInPeriod->whereIn('movement_type', ['sale', 'guest_use'])->sum('total_amount');
            
            $productionStartingStock = $openingStock + $received + $adjustmentInPeriod;
            $closingStock = $productionStartingStock - $sold - $inUse - $lost;

            $reportData[] = (object)[
                'name' => $item->name,
                'unit' => $item->unit,
                'category' => $item->category,
                'opening_stock' => $openingStock, // Pure opening stock before receipts
                'received' => $received,
                'lost' => $lost,
                'closing_stock' => $closingStock,
                'expiry_date' => $item->expiry_date,
                'usage' => $sold + $inUse, // Total usage (guest + staff)
            ];
        }

        // 3. Get Foods Cooked (Produced) - Detailed Log (Direct Sales Only)
        $rawProduction = ServiceRequest::where(function($query) {
                $query->whereIn('service_id', [4, 48]) // Generic Food (4) and Restaurant Food (48)
                      ->orWhereHas('service', function($q) {
                          $q->whereIn('category', ['food', 'restaurant']);
                      });
            })
            ->whereBetween('completed_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->whereNull('day_service_id')
            ->with(['service', 'booking.room', 'dayService', 'approvedBy'])
            ->orderBy('completed_at', 'asc')
            ->get();

        $productionData = $rawProduction->map(function($order) {
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
                'item_name' => $order->service_specific_data['item_name'] ?? $order->service->name ?? 'Unknown Dish',
                'destinations' => $dest,
                'guest_label' => $guestLabel,
                'category' => ucfirst($order->service->category ?? 'Food'),
                'total_qty' => $order->quantity,
                'unit_price' => $order->unit_price_tsh,
                'total_revenue' => $order->total_price_tsh,
                'time' => $order->completed_at ? $order->completed_at->format('H:i') : '-',
                'served_by' => $order->approvedBy->name ?? 'Kitchen Staff',
                'payment_status' => $order->payment_status,
                'payment_method' => $order->payment_method,
                'payment_reference' => $order->payment_reference,
            ];
        });

        // 4. Ceremony Usage Breakdown
        $ceremonyUsage = ServiceRequest::with(['service', 'dayService'])
            ->whereNotNull('day_service_id')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where(function($query) {
                 $query->whereIn('service_id', [4, 48])
                       ->orWhereHas('service', function($q) {
                           $q->whereIn('category', ['food', 'restaurant']);
                       });
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $totalRev = $productionData->sum('total_revenue');
        $totalQty = $productionData->sum('total_qty');

        $activePage = 'kitchen/reports';
        return view('admin.restaurants.kitchen.reports', compact(
            'reportData', 
            'productionData',
            'ceremonyUsage',
            'date', 
            'startDate', 
            'endDate', 
            'reportType',
            'activePage',
            'totalRev',
            'totalQty'
        ));
    }
}
