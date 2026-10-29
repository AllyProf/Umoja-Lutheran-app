<?php

namespace App\Http\Controllers;

use App\Models\StockReceipt;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Supplier;
use App\Models\ShoppingList;
use App\Models\LocalPurchaseOrder;
use App\Models\LocalPurchaseOrderItem;
use App\Models\ShoppingListItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockReceiptController extends Controller
{
    /**
     * Display a listing of stock receipts
     */
    public function index(Request $request)
    {
        $user = Auth::guard('staff')->user();
        $role = strtolower($user->role ?? 'manager');
        if ($role === 'manager' || $role === 'super_admin') {
            $role = 'admin';
        }

        $query = StockReceipt::with(['product', 'productVariant', 'supplier', 'receivedBy']);

        // Filter by product type
        if ($request->has('type')) {
            $type = $request->type;
            $query->whereHas('product', function ($q) use ($type) {
                if ($type === 'drink') {
                    $q->whereIn('category', ['alcoholic_beverage', 'non_alcoholic_beverage', 'water', 'juices', 'energy_drinks', 'drinks', 'beverage']);
                } elseif ($type === 'food') {
                    $q->whereIn('category', ['food', 'meat_poultry', 'seafood', 'vegetables', 'dairy', 'pantry_baking', 'spices_herbs', 'oils_fats', 'kitchen', 'snacks']);
                } elseif ($type === 'housekeeping') {
                    $q->whereIn('category', ['cleaning_supplies', 'linens', 'housekeeping']);
                }
            });
        }

        // Filter by date range
        if ($request->has('date_from')) {
            $query->where('received_date', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->where('received_date', '<=', $request->date_to);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('product', function ($q2) use ($search) {
                    $q2->where('name', 'LIKE', "%{$search}%");
                })
                    ->orWhereHas('supplier', function ($q2) use ($search) {
                        $q2->where('name', 'LIKE', "%{$search}%");
                    });
            });
        }

        $stockReceipts = $query->orderBy('received_date', 'desc')->paginate(20);

        $type = $request->type;
        return view('dashboard.stock-receipts-list', compact('stockReceipts', 'role', 'type'));
    }

    /**
     * Show the form for creating a new stock receipt
     */
    public function create(Request $request)
    {
        $user = Auth::guard('staff')->user();
        $role = strtolower($user->role ?? 'manager');
        $isStorekeeper = $role === 'storekeeper';
        if ($role === 'manager' || $role === 'super_admin') {
            $role = 'admin';
        }
        $type = $request->type;

        $query = Product::where('is_active', true);

        if ($type) {
            if ($type === 'drink') {
                $query->where(function ($q) {
                    $q->where('type', 'drink')
                        ->orWhereIn('category', ['vinywaji', 'alcoholic_beverage', 'non_alcoholic_beverage', 'water', 'juices', 'energy_drinks', 'drinks', 'beverage', 'spirits', 'wines']);
                });
            } elseif ($type === 'food') {
                $query->where(function ($q) {
                    $q->where('type', 'food')
                        ->orWhereIn('category', ['chakula', 'food', 'meat_poultry', 'seafood', 'vegetables', 'dairy', 'pantry_baking', 'spices_herbs', 'oils_fats', 'kitchen', 'snacks']);
                });
            } elseif ($type === 'housekeeping') {
                $query->where(function ($q) {
                    $q->where('type', 'housekeeping')
                        ->orWhereIn('category', ['cleaning_supplies', 'linens', 'housekeeping']);
                });
            }
        }

        $products = $query->with(['variants', 'supplier'])
            ->orderBy('name')
            ->get();
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();

        // Only documents already cleared by accountant (funds ready / LPO past accountant)
        $shoppingLists = ShoppingList::withCount('items')
            ->whereIn('status', $this->receivablePurchaseListStatuses())
            ->orderByDesc('shopping_date')
            ->orderByDesc('id')
            ->limit(80)
            ->get();

        $lpos = LocalPurchaseOrder::withCount('items')
            ->whereIn('status', $this->receivableLpoStatuses())
            ->orderByDesc('id')
            ->limit(80)
            ->get();

        // Always use the simplified storekeeper receive UI (supports LPO / purchase list)
        $preselectedSource = $request->get('source'); // purchase_list | lpo
        $preselectedId = $request->get('lpo_id') ?: $request->get('purchase_list_id');

        return view('dashboard.storekeeper.stock-receipt-create', compact(
            'products',
            'suppliers',
            'role',
            'type',
            'shoppingLists',
            'lpos',
            'isStorekeeper',
            'preselectedSource',
            'preselectedId'
        ));
    }

    /**
     * Purchase lists ready to receive: accountant approved + funds disbursed (or already purchased).
     */
    protected function receivablePurchaseListStatuses(): array
    {
        return ['ready_for_purchase', 'purchased'];
    }

    /**
     * LPOs ready to receive: funds already paid to the storekeeper.
     */
    protected function receivableLpoStatuses(): array
    {
        return ['funds_disbursed', 'closed'];
    }

    /**
     * Load items for a purchase list or LPO (AJAX)
     */
    public function sourceItems(Request $request)
    {
        $request->validate([
            'source' => 'required|in:purchase_list,lpo',
            'id' => 'required|integer',
        ]);

        if ($request->source === 'purchase_list') {
            $list = ShoppingList::with(['items.product.supplier', 'items.productVariant', 'lpo'])
                ->whereIn('status', $this->receivablePurchaseListStatuses())
                ->find($request->id);

            if (!$list) {
                return response()->json([
                    'success' => false,
                    'message' => 'This purchase list is not ready to receive. Accountant must approve and disburse funds first.',
                ], 422);
            }

            $items = $list->items->map(function (ShoppingListItem $item) {
                $variant = $item->product_variant;
                $product = $variant?->product ?? $item->product;
                $unit = $item->unit
                    ?: ($variant->receiving_unit ?? $variant->purchasing_unit ?? 'pcs');
                $ordered = (float) ($item->quantity ?? 0);
                $purchased = (float) ($item->purchased_quantity ?? 0);
                $buying = (float) ($item->unit_price ?: $item->estimated_price ?: ($variant->buying_price ?? 0));
                $selling = (float) ($variant->selling_price_per_pic ?? 0);

                return [
                    'id' => $item->id,
                    'name' => $item->product_name
                        ?: ($product->name ?? 'Item')
                        . ($variant && $variant->variant_name ? ' · ' . $variant->variant_name : ''),
                    'product_id' => $product->id ?? $item->product_id,
                    'product_variant_id' => $variant->id ?? $item->product_variant_id,
                    'supplier_id' => $product->supplier_id ?? null,
                    'unit' => $unit,
                    'ordered_qty' => $ordered,
                    'already_qty' => $purchased,
                    'suggested_qty' => $purchased > 0 ? $purchased : $ordered,
                    'buying_price' => $buying,
                    'selling_price' => $selling > 0 ? $selling : $buying,
                    'can_receive' => (bool) $variant,
                ];
            })->values();

            return response()->json([
                'success' => true,
                'source' => 'purchase_list',
                'label' => $list->name ?: ('Purchase list #' . $list->id),
                'meta' => ucfirst(str_replace('_', ' ', $list->status))
                    . ($list->shopping_date ? ' · ' . $list->shopping_date->format('d M Y') : '')
                    . ($list->lpo_id ? ' · LPO #' . $list->lpo_id : ''),
                'items' => $items,
            ]);
        }

        $lpo = LocalPurchaseOrder::with(['items.productVariant.product.supplier'])
            ->whereIn('status', $this->receivableLpoStatuses())
            ->find($request->id);

        if (!$lpo) {
            return response()->json([
                'success' => false,
                'message' => 'This LPO is not ready to receive. Accountant must approve it first.',
            ], 422);
        }

        $items = $lpo->items->map(function (LocalPurchaseOrderItem $item) {
            $variant = $item->productVariant;
            $product = $variant?->product;
            $rawReceived = (float) ($item->getAttributes()['qty_received'] ?? 0);
            $ordered = (float) ($item->quantity ?? 0);
            $remaining = max(0, $ordered - $rawReceived);
            $buying = (float) ($item->unit_price ?? ($variant->buying_price ?? 0));
            $selling = (float) ($variant->selling_price_per_pic ?? 0);

            return [
                'id' => $item->id,
                'name' => $item->item_name
                    ?: (($product->name ?? 'Item')
                        . ($variant && $variant->variant_name ? ' · ' . $variant->variant_name : '')),
                'product_id' => $product->id ?? null,
                'product_variant_id' => $variant->id ?? $item->product_variant_id,
                'supplier_id' => $product->supplier_id ?? null,
                'unit' => $item->unit ?: ($variant->purchasing_unit ?? $variant->receiving_unit ?? 'pcs'),
                'ordered_qty' => $ordered,
                'already_qty' => $rawReceived,
                'suggested_qty' => $remaining > 0 ? $remaining : $ordered,
                'buying_price' => $buying,
                'selling_price' => $selling > 0 ? $selling : $buying,
                'can_receive' => (bool) $variant,
            ];
        })->values();

        return response()->json([
            'success' => true,
            'source' => 'lpo',
            'label' => 'LPO #' . $lpo->id,
            'meta' => ucfirst(str_replace('_', ' ', $lpo->status))
                . ($lpo->start_date ? ' · ' . $lpo->start_date->format('d M') . ' – ' . $lpo->end_date->format('d M Y') : ''),
            'items' => $items,
        ]);
    }

    /**
     * Receive multiple items from a purchase list or LPO into main store stock
     */
    public function receiveFromSource(Request $request)
    {
        $validated = $request->validate([
            'source' => 'required|in:purchase_list,lpo',
            'source_id' => 'required|integer',
            'supplier_id' => 'required|exists:suppliers,id',
            'received_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|integer',
            'items.*.qty' => 'nullable|numeric|min:0',
            'items.*.buying_price' => 'nullable|numeric|min:0',
            'items.*.selling_price' => 'nullable|numeric|min:0',
        ]);

        if ($validated['source'] === 'purchase_list') {
            $allowed = ShoppingList::whereKey($validated['source_id'])
                ->whereIn('status', $this->receivablePurchaseListStatuses())
                ->exists();
            if (!$allowed) {
                return response()->json([
                    'success' => false,
                    'message' => 'This purchase list is not ready to receive. Accountant must approve and disburse funds first.',
                ], 422);
            }
        } else {
            $allowed = LocalPurchaseOrder::whereKey($validated['source_id'])
                ->whereIn('status', $this->receivableLpoStatuses())
                ->exists();
            if (!$allowed) {
                return response()->json([
                    'success' => false,
                    'message' => 'This LPO is not ready to receive. Accountant must approve it first.',
                ], 422);
            }
        }

        $receivedCount = 0;

        DB::beginTransaction();
        try {
            $receivedBy = Auth::guard('staff')->id();
            $notePrefix = $validated['source'] === 'purchase_list'
                ? 'Received from purchase list #' . $validated['source_id']
                : 'Received from LPO #' . $validated['source_id'];

            foreach ($validated['items'] as $row) {
                $qty = (float) ($row['qty'] ?? 0);
                if ($qty <= 0) {
                    continue;
                }

                if ($validated['source'] === 'purchase_list') {
                    $listItem = ShoppingListItem::with(['product', 'productVariant.product'])
                        ->where('shopping_list_id', $validated['source_id'])
                        ->findOrFail($row['id']);

                    $variant = $listItem->product_variant;
                    if (!$variant) {
                        continue;
                    }
                    $product = $variant->product;
                    $buying = (float) ($row['buying_price'] ?? $listItem->unit_price ?? $variant->buying_price ?? 0);
                    $selling = (float) ($row['selling_price'] ?? $variant->selling_price_per_pic ?? $buying);

                    StockReceipt::create([
                        'product_id' => $product->id,
                        'product_variant_id' => $variant->id,
                        'supplier_id' => $validated['supplier_id'],
                        'quantity_received_packages' => $qty,
                        'buying_price_per_bottle' => $buying,
                        'selling_price_per_bottle' => $selling,
                        'discount_type' => 'none',
                        'discount_amount' => 0,
                        'received_date' => $validated['received_date'],
                        'notes' => $notePrefix . ' · item #' . $listItem->id,
                        'received_by' => $receivedBy,
                    ]);

                    // Keep purchase list qty in sync when storekeeper receives goods
                    if (!(float) $listItem->purchased_quantity) {
                        $listItem->purchased_quantity = $qty;
                        $listItem->is_purchased = true;
                        $listItem->is_found = true;
                        if (!$listItem->unit_price && $buying > 0) {
                            $listItem->unit_price = $buying;
                            $listItem->purchased_cost = $buying * $qty;
                        }
                        $listItem->save();
                    }

                    $receivedCount++;
                    continue;
                }

                $lpoItem = LocalPurchaseOrderItem::with(['productVariant.product'])
                    ->where('lpo_id', $validated['source_id'])
                    ->findOrFail($row['id']);

                $variant = $lpoItem->productVariant;
                if (!$variant) {
                    continue;
                }
                $product = $variant->product;
                $buying = (float) ($row['buying_price'] ?? $lpoItem->unit_price ?? $variant->buying_price ?? 0);
                $selling = (float) ($row['selling_price'] ?? $variant->selling_price_per_pic ?? $buying);

                StockReceipt::create([
                    'product_id' => $product->id,
                    'product_variant_id' => $variant->id,
                    'supplier_id' => $validated['supplier_id'],
                    'quantity_received_packages' => $qty,
                    'buying_price_per_bottle' => $buying,
                    'selling_price_per_bottle' => $selling,
                    'discount_type' => 'none',
                    'discount_amount' => 0,
                    'received_date' => $validated['received_date'],
                    'notes' => $notePrefix . ' · item #' . $lpoItem->id,
                    'received_by' => $receivedBy,
                ]);

                $lpoItem->increment('qty_received', $qty);
                $lineCost = $qty * $buying;
                if ($lineCost > 0) {
                    LocalPurchaseOrder::where('id', $validated['source_id'])->increment('spent_amount', $lineCost);
                }
                $receivedCount++;
            }

            if ($receivedCount === 0) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Enter a quantity greater than 0 for at least one linked item.',
                ], 422);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $receivedCount . ' item' . ($receivedCount === 1 ? '' : 's') . ' received into store stock.',
                'received_count' => $receivedCount,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Could not save receipts: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created stock receipt
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'product_variant_id' => 'required|exists:product_variants,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'quantity_received_packages' => 'required|numeric|min:0.01',
            'buying_price_per_bottle' => 'required|numeric|min:0',
            'selling_price_per_bottle' => 'required|numeric|min:0',
            'discount_type' => 'nullable|in:percentage,fixed,none',
            'discount_amount' => 'nullable|numeric|min:0',
            'received_date' => 'required|date',
            'expiry_date' => 'nullable|date|after_or_equal:received_date',
            'minimum_stock_level' => 'required|numeric|min:0',
            'minimum_stock_level_unit' => 'required|in:bottles,packages',
            'notes' => 'nullable|string',
        ]);

        $variant = ProductVariant::findOrFail($validated['product_variant_id']);

        // Normalize buying price if submitted as package price
        if ($request->price_type === 'package' && ($variant->items_per_package ?? 0) > 0) {
            $validated['buying_price_per_bottle'] = $validated['buying_price_per_bottle'] / $variant->items_per_package;
        }

        // Verify product variant belongs to product
        if ($variant->product_id != $validated['product_id']) {
            return response()->json([
                'success' => false,
                'message' => 'Product variant does not belong to selected product.',
            ], 422);
        }

        // Verify supplier matches product supplier (optional check)
        $product = Product::findOrFail($validated['product_id']);
        if ($product->supplier_id != $validated['supplier_id']) {
            // Allow but warn - supplier might have changed
        }

        $validated['received_by'] = Auth::guard('staff')->id();

        $stockReceipt = StockReceipt::create($validated);

        // Convert minimum stock level to bottles if unit is packages
        $minimumStockLevelBottles = $validated['minimum_stock_level'];
        if ($validated['minimum_stock_level_unit'] === 'packages') {
            $minimumStockLevelBottles = $validated['minimum_stock_level'] * $variant->items_per_package;
        }

        // Update minimum stock level for the product variant (always store in bottles)
        $variant->minimum_stock_level = $minimumStockLevelBottles;
        $variant->minimum_stock_level_unit = $validated['minimum_stock_level_unit'];
        $variant->save();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Stock receipt created successfully!',
                'stock_receipt' => $stockReceipt->load(['product', 'productVariant', 'supplier', 'receivedBy']),
            ]);
        }

        $role = strtolower(Auth::guard('staff')->user()->role ?? '');
        $indexRoute = $role === 'storekeeper'
            ? 'storekeeper.stock-receipts.index'
            : 'admin.stock-receipts.index';

        return redirect()->route($indexRoute)
            ->with('success', 'Stock receipt created successfully!');
    }

    /**
     * Get product variants for a specific product (AJAX)
     */
    public function getProductVariants(Product $product)
    {
        $variants = $product->variants()->where('is_active', true)->orderBy('display_order')->get();

        // Add packaging_name from purchasing_unit (preferred) or legacy packaging field
        $variants->transform(function ($variant) {
            $purchaseUnit = $variant->purchasing_unit ?: $variant->packaging;
            $variant->packaging_name = match (strtolower((string) $purchaseUnit)) {
                'crates', 'crate' => 'Crate',
                'carton', 'cartons' => 'Carton',
                'boxes', 'box' => 'Box',
                'bags', 'bag' => 'Bag',
                'packets', 'packet', 'pack' => 'Pack',
                default => $purchaseUnit ? ucfirst($purchaseUnit) : 'Package',
            };
            $variant->name = $variant->variant_name;
            return $variant;
        });

        return response()->json([
            'success' => true,
            'variants' => $variants,
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'category' => $product->category,
                'type' => $product->type,
            ]
        ]);
    }

    /**
     * Calculate stock receipt totals (AJAX)
     */
    public function calculateTotals(Request $request)
    {
        $request->validate([
            'product_variant_id' => 'required|exists:product_variants,id',
            'quantity_received_packages' => 'required|numeric|min:0.01',
            'buying_price_per_bottle' => 'required|numeric|min:0',
            'selling_price_per_bottle' => 'required|numeric|min:0',
        ]);

        $variant = ProductVariant::with('product')->findOrFail($request->product_variant_id);
        $quantityPackages = $request->quantity_received_packages;
        $buyingPrice = $request->buying_price_per_bottle;
        $sellingPrice = $request->selling_price_per_bottle;

        $foodCategories = ['food', 'meat_poultry', 'seafood', 'vegetables', 'dairy', 'pantry_baking', 'spices_herbs', 'oils_fats', 'kitchen', 'snacks'];
        $isFood = in_array(strtolower($variant->product->category), $foodCategories);

        $totalBottles = ($isFood && !in_array(strtolower($variant->receiving_unit), ['pic', 'pcs', 'piece', 'pieces']))
            ? $quantityPackages
            : ($quantityPackages * ($variant->items_per_package ?: 1));
        $profitPerBottle = $sellingPrice - $buyingPrice;
        $totalBuyingCost = $totalBottles * $buyingPrice;
        $totalProfit = ($totalBottles * $sellingPrice) - $totalBuyingCost;

        return response()->json([
            'success' => true,
            'total_packages' => $quantityPackages,
            'total_bottles' => $totalBottles,
            'profit_per_bottle' => $profitPerBottle,
            'total_buying_cost' => $totalBuyingCost,
            'total_profit' => $totalProfit,
            'items_per_package' => $variant->items_per_package,
        ]);
    }

    /**
     * Download stock receipt (PDF view)
     */
    public function download(StockReceipt $stockReceipt)
    {
        $receipt = $stockReceipt->load(['product', 'productVariant', 'supplier', 'receivedBy']);
        return view('dashboard.stock-receipt-pdf', compact('receipt'));
    }
}
