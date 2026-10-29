<?php

namespace App\Http\Controllers;

use App\Models\StockTransfer;
use App\Models\StockRequest;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Staff;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StockTransferController extends Controller
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * Display a listing of stock transfers
     */
    public function index(Request $request)
    {
        $user = Auth::guard('staff')->user();
        $role = strtolower($user->role ?? 'manager');
        $isStorekeeper = $role === 'storekeeper';
        if ($role === 'manager' || $role === 'super_admin') {
            $role = 'admin';
        }

        $query = StockTransfer::with(['product', 'productVariant', 'transferredBy', 'receivedBy']);

        if ($request->filled('status') && in_array($request->status, ['pending', 'completed', 'cancelled'], true)) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('transfer_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('transfer_date', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('transfer_reference', 'LIKE', "%{$search}%")
                    ->orWhereHas('product', function ($q2) use ($search) {
                        $q2->where('name', 'LIKE', "%{$search}%")
                            ->orWhere('brand_or_type', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('productVariant', function ($q2) use ($search) {
                        $q2->where('variant_name', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('receivedBy', function ($q2) use ($search) {
                        $q2->where('name', 'LIKE', "%{$search}%")
                            ->orWhere('role', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('transferredBy', function ($q2) use ($search) {
                        $q2->where('name', 'LIKE', "%{$search}%");
                    });
            });
        }

        $type = $request->query('type');
        if ($type) {
            $query->whereHas('product', function ($q) use ($type) {
                $q->where('type', $type);
            });
        }

        $transfers = $query->orderBy('transfer_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        if ($isStorekeeper) {
            return view('dashboard.storekeeper.transfers-list', compact('transfers', 'role', 'type'));
        }

        return view('dashboard.stock-transfers-list', compact('transfers', 'role', 'type'));
    }

    /**
     * Show distribute form (storekeeper) or legacy transfer form (admin)
     */
    public function create(Request $request)
    {
        $user = Auth::guard('staff')->user();
        $role = strtolower($user->role ?? 'manager');
        $isStorekeeper = $role === 'storekeeper';
        if ($role === 'manager' || $role === 'super_admin') {
            $role = 'admin';
        }

        if ($isStorekeeper) {
            return $this->createDistributePage($role);
        }

        $type = $request->query('type', 'drink');

        $products = Product::with(['variants', 'supplier'])
            ->where('is_active', true)
            ->where('type', $type)
            ->orderBy('name')
            ->get();

        $barKeeper = $this->findStaffForDepartment('bar');

        $availableStock = [];
        foreach ($products as $product) {
            foreach ($product->variants as $variant) {
                $availableBaseUnits = $variant->getCurrentStock();
                $itemsPerPkg = $variant->items_per_package > 0 ? $variant->items_per_package : 1;

                $availableStock[$variant->id] = [
                    'packages' => (float) floor($availableBaseUnits / $itemsPerPkg),
                    'bottles' => (float) $availableBaseUnits,
                    'servings' => (float) ($availableBaseUnits * ($variant->servings_per_pic ?: 1)),
                ];
            }
        }

        return view('dashboard.stock-transfer-form', compact('products', 'barKeeper', 'availableStock', 'role', 'type'));
    }

    /**
     * Simplified storekeeper distribute page data
     */
    protected function createDistributePage(string $role)
    {
        $receivers = [
            'restaurant' => $this->findStaffForDepartment('restaurant'),
            'housekeeping' => $this->findStaffForDepartment('housekeeping'),
        ];

        $allStaff = Staff::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'role']);

        $staffOptions = $allStaff->map(function ($staff) {
            return [
                'id' => $staff->id,
                'name' => $staff->name,
                'role' => $staff->role,
                'label' => $staff->name . ' (' . $staff->role . ')',
            ];
        })->values();

        $approvedRequests = StockRequest::with([
            'requester',
            'productVariant.product',
        ])
            ->where('status', 'approved')
            ->orderByDesc('batch_id')
            ->orderByDesc('id')
            ->get();

        $batches = $approvedRequests->groupBy(function ($req) {
            return $req->batch_id ?: ('single-' . $req->id);
        })->map(function ($items, $batchKey) {
            $first = $items->first();
            return [
                'batch_id' => $first->batch_id,
                'batch_key' => $batchKey,
                'batch_reference' => $first->batch_reference ?: ('REQ-' . $first->id),
                'requester' => $first->requester->name ?? 'Unknown',
                'department' => $this->departmentFromRole($first->requester->role ?? ''),
                'created_at' => optional($first->created_at)->format('d M Y H:i'),
                'items' => $items->map(function ($req) {
                    $variant = $req->productVariant;
                    $product = $variant?->product;
                    $available = $variant ? (float) $variant->getCurrentStock() : 0;
                    $unitCost = $variant ? (float) $variant->getLatestUnitCost() : 0;

                    return [
                        'id' => $req->id,
                        'product_id' => $product?->id,
                        'name' => ($product->name ?? 'Item')
                            . ($variant && $variant->variant_name && strtolower($variant->variant_name) !== 'standard'
                                ? ' · ' . $variant->variant_name
                                : ''),
                        'unit' => $req->unit ?: ($variant->receiving_unit ?? 'pcs'),
                        'requested_qty' => (float) $req->quantity,
                        'available' => $available,
                        'suggested_qty' => min((float) $req->quantity, max(0, $available)),
                        'unit_cost' => $unitCost,
                        'product_variant_id' => $req->product_variant_id,
                        'is_returnable' => (bool) ($product->is_returnable ?? false),
                    ];
                })->values(),
            ];
        })->values();

        $catalog = [
            'restaurant' => $this->buildProductCatalog('restaurant'),
            'housekeeping' => $this->buildProductCatalog('housekeeping'),
        ];

        return view('dashboard.storekeeper.distribute', compact(
            'role',
            'receivers',
            'batches',
            'catalog',
            'allStaff',
            'staffOptions'
        ));
    }

    /**
     * Store transfer — supports legacy single transfer OR storekeeper distribute modes
     */
    public function store(Request $request)
    {
        $user = Auth::guard('staff')->user();
        $role = strtolower($user->role ?? '');

        if ($role === 'storekeeper' && $request->filled('mode')) {
            if ($request->mode === 'from_request') {
                return $this->storeFromRequest($request);
            }
            if ($request->mode === 'direct') {
                return $this->storeDirect($request);
            }
        }

        return $this->storeLegacyBarTransfer($request);
    }

    /**
     * Issue approved department request items
     */
    protected function storeFromRequest(Request $request)
    {
        $validated = $request->validate([
            'batch_key' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:stock_requests,id',
            'items.*.quantity_issued' => 'nullable|numeric|min:0',
            'items.*.unit_cost' => 'nullable|numeric|min:0',
            'items.*.is_returnable' => 'nullable|boolean',
        ]);

        $ids = collect($validated['items'])->pluck('id');
        $stockRequests = StockRequest::with(['requester', 'productVariant.product'])
            ->whereIn('id', $ids)
            ->where('status', 'approved')
            ->get()
            ->keyBy('id');

        if ($stockRequests->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No approved request items found to issue.',
            ], 422);
        }

        $issued = 0;
        $batchId = null;

        DB::beginTransaction();
        try {
            foreach ($validated['items'] as $row) {
                $qty = (float) ($row['quantity_issued'] ?? 0);
                if ($qty <= 0) {
                    continue;
                }

                $stockRequest = $stockRequests->get($row['id']);
                if (!$stockRequest) {
                    continue;
                }

                $variant = $stockRequest->productVariant;
                if (!$variant) {
                    continue;
                }

                $available = (float) $variant->getCurrentStock();
                $unit = strtolower($stockRequest->unit ?: 'bottles');
                $neededBase = $this->toBaseUnits($qty, $unit, $variant);
                if ($neededBase > $available + 0.0001) {
                    throw new \RuntimeException(
                        ($variant->product->name ?? 'Item') . ' has only ' . number_format($available, 2) . ' in store stock.'
                    );
                }

                $unitCost = (float) ($row['unit_cost'] ?? $variant->getLatestUnitCost());
                if (array_key_exists('is_returnable', $row)) {
                    $this->applyReturnableFlag($variant->product_id, $row['is_returnable']);
                }
                $this->issueStockRequest($stockRequest, $qty, $unitCost);
                $batchId = $stockRequest->batch_id;
                $issued++;
            }

            if ($issued === 0) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Enter a quantity greater than 0 for at least one item.',
                ], 422);
            }

            DB::commit();

            $redirect = $batchId
                ? route('stock-requests.batch-print', $batchId)
                : route('storekeeper.transfers.index');

            return response()->json([
                'success' => true,
                'message' => $issued . ' item' . ($issued === 1 ? '' : 's') . ' issued to the department.',
                'redirect' => $redirect,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Could not issue stock: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Storekeeper pushes stock directly to a department
     */
    protected function storeDirect(Request $request)
    {
        $validated = $request->validate([
            'department' => 'required|in:restaurant,housekeeping',
            'received_by' => 'nullable|exists:staffs,id',
            'transfer_date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.product_variant_id' => 'required|exists:product_variants,id',
            'items.*.quantity' => 'nullable|numeric|min:0',
            'items.*.quantity_unit' => 'nullable|string|max:50',
            'items.*.unit_cost' => 'nullable|numeric|min:0',
            'items.*.is_returnable' => 'nullable|boolean',
        ]);

        $receiver = null;
        if (!empty($validated['received_by'])) {
            $receiver = Staff::find($validated['received_by']);
        }
        if (!$receiver) {
            $receiver = $this->findStaffForDepartment($validated['department']);
        }
        if (!$receiver) {
            return response()->json([
                'success' => false,
                'message' => 'Select a receiver for this department (no default staff assigned yet).',
            ], 422);
        }

        // Both Restaurant and Housekeeping are treated as internal issue (no bar-confirm step)
        $isInternal = true;
        $created = 0;

        DB::beginTransaction();
        try {
            foreach ($validated['items'] as $row) {
                $qty = (float) ($row['quantity'] ?? 0);
                if ($qty <= 0) {
                    continue;
                }

                $variant = ProductVariant::with('product')->findOrFail($row['product_variant_id']);
                if ((int) $variant->product_id !== (int) $row['product_id']) {
                    throw new \RuntimeException('Product variant mismatch.');
                }

                $unit = $this->normalizeIssueUnit(
                    $row['quantity_unit'] ?? null,
                    $variant,
                    $validated['department'] ?? null
                );
                $neededBase = $this->toBaseUnits($qty, $unit, $variant);
                $available = (float) $variant->getCurrentStock();
                if ($neededBase > $available + 0.0001) {
                    throw new \RuntimeException(
                        ($variant->product->name ?? 'Item') . ' has only ' . number_format($available, 2) . ' available.'
                    );
                }

                $unitCost = (float) ($row['unit_cost'] ?? 0);
                if ($unitCost <= 0) {
                    $perBase = $variant->getLatestUnitCost();
                    $unitCost = ($unit === 'packages' && ($variant->items_per_package ?? 0) > 0)
                        ? $perBase * $variant->items_per_package
                        : $perBase;
                }

                $transfer = StockTransfer::create([
                    'transfer_reference' => StockTransfer::generateReference(),
                    'product_id' => $variant->product_id,
                    'product_variant_id' => $variant->id,
                    'quantity_transferred' => $qty,
                    'quantity_unit' => $unit,
                    'unit_cost' => $unitCost,
                    'transferred_by' => Auth::guard('staff')->id(),
                    'received_by' => $receiver->id,
                    'status' => $isInternal ? 'completed' : 'pending',
                    'transfer_date' => $validated['transfer_date'],
                    'received_at' => $isInternal ? Carbon::now() : null,
                    'notes' => trim(($validated['notes'] ?? '') . ' | Direct issue to ' . $validated['department']),
                ]);

                if (method_exists($transfer, 'calculateRevenueProjections')) {
                    $transfer->calculateRevenueProjections();
                    $transfer->save();
                }

                if ($isInternal) {
                    $this->pushToDepartmentInventory(
                        $validated['department'],
                        $variant,
                        $qty,
                        $unit,
                        $receiver->id,
                        'Direct issue from store · ' . $transfer->transfer_reference
                    );
                }

                if (array_key_exists('is_returnable', $row)) {
                    $this->applyReturnableFlag((int) $variant->product_id, $row['is_returnable']);
                }

                $created++;
            }

            if ($created === 0) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Enter a quantity greater than 0 for at least one item.',
                ], 422);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $created . ' item' . ($created === 1 ? '' : 's') . ' distributed to '
                    . ucfirst($validated['department']) . '.',
                'redirect' => route('storekeeper.transfers.index'),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Could not distribute: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Legacy admin/bar-only single transfer create
     */
    protected function storeLegacyBarTransfer(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'product_variant_id' => 'required|exists:product_variants,id',
            'quantity_transferred' => 'required|numeric|min:0.01',
            'quantity_unit' => 'required|in:packages,bottles,pic,glass,serving',
            'received_by' => 'required|exists:staffs,id',
            'transfer_date' => 'required|date',
            'expiry_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'unit_cost' => 'nullable|numeric|min:0',
        ]);

        if ($validated['quantity_unit'] === 'pic') {
            $validated['quantity_unit'] = 'bottles';
        }

        $variant = ProductVariant::findOrFail($validated['product_variant_id']);
        if ($variant->product_id != $validated['product_id']) {
            return response()->json([
                'success' => false,
                'message' => 'Product variant does not belong to selected product.',
            ], 422);
        }

        $barKeeper = Staff::findOrFail($validated['received_by']);
        $barKeeperRoles = ['bar_keeper', 'bar keeper', 'bartender'];
        if (!in_array(strtolower($barKeeper->role ?? ''), $barKeeperRoles)) {
            return response()->json([
                'success' => false,
                'message' => 'Selected staff is not a bar keeper.',
            ], 422);
        }

        $validated['transferred_by'] = Auth::guard('staff')->id();
        $validated['transfer_reference'] = StockTransfer::generateReference();
        $validated['status'] = 'pending';

        if (empty($validated['unit_cost'])) {
            $perBaseUnit = $variant->getLatestUnitCost();
            if ($validated['quantity_unit'] === 'packages' && ($variant->items_per_package ?? 0) > 0) {
                $validated['unit_cost'] = $perBaseUnit * $variant->items_per_package;
            } else {
                $validated['unit_cost'] = $perBaseUnit;
            }
        }

        $transfer = StockTransfer::create($validated);
        $transfer->calculateRevenueProjections();
        $transfer->save();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Stock transfer created successfully!',
                'transfer' => $transfer->load(['product', 'productVariant', 'transferredBy', 'receivedBy']),
            ]);
        }

        $role = strtolower(Auth::guard('staff')->user()->role ?? '');
        $indexRoute = $role === 'storekeeper'
            ? 'storekeeper.transfers.index'
            : 'admin.stock-transfers.index';

        return redirect()->route($indexRoute)
            ->with('success', 'Stock transfer created successfully!');
    }

    protected function issueStockRequest(StockRequest $stockRequest, float $quantityIssued, float $unitCost): void
    {
        $stockRequest->loadMissing(['requester', 'productVariant.product']);
        $receiverRole = strtolower(str_replace(' ', '_', $stockRequest->requester->role ?? ''));
        $isInternal = in_array($receiverRole, ['head_chef', 'chef', 'housekeeper', 'linen_keeper']);

        $issueUnit = $this->normalizeIssueUnit(
            $stockRequest->unit,
            $stockRequest->productVariant,
            $isInternal ? (in_array($receiverRole, ['head_chef', 'chef'], true) ? 'restaurant' : 'housekeeping') : null
        );

        $transfer = StockTransfer::create([
            'transfer_reference' => StockTransfer::generateReference(),
            'product_id' => $stockRequest->productVariant->product_id,
            'product_variant_id' => $stockRequest->product_variant_id,
            'quantity_transferred' => $quantityIssued,
            'quantity_unit' => $issueUnit,
            'unit_cost' => $unitCost,
            'transferred_by' => Auth::guard('staff')->id(),
            'received_by' => $stockRequest->requested_by,
            'status' => $isInternal ? 'completed' : 'pending',
            'transfer_date' => Carbon::now()->toDateString(),
            'received_at' => $isInternal ? Carbon::now() : null,
            'notes' => 'Requisition Note #' . $stockRequest->id
                . ($stockRequest->batch_reference ? ' · ' . $stockRequest->batch_reference : ''),
        ]);

        if (method_exists($transfer, 'calculateRevenueProjections')) {
            $transfer->calculateRevenueProjections();
            $transfer->save();
        }

        $stockRequest->forceFill([
            'status' => 'completed',
            'quantity_issued' => $quantityIssued,
            'unit_cost' => $unitCost,
            'total_cost' => $quantityIssued * $unitCost,
            'storekeeper_id' => Auth::guard('staff')->id(),
            'distributed_at' => Carbon::now(),
            'stock_transfer_id' => $transfer->id,
        ])->save();

        if ($isInternal) {
            $dept = in_array($receiverRole, ['head_chef', 'chef']) ? 'kitchen' : 'housekeeping';
            $this->pushToDepartmentInventory(
                $dept,
                $stockRequest->productVariant,
                $quantityIssued,
                $stockRequest->unit ?: 'bottles',
                $stockRequest->requested_by,
                "Issued from Store: Requisition #{$stockRequest->id}"
            );
        }
    }

    protected function pushToDepartmentInventory(
        string $department,
        ProductVariant $variant,
        float $quantity,
        string $unit,
        int $staffId,
        string $notes
    ): void {
        $product = $variant->product;
        $itemName = $product->name ?? 'Item';
        if ($variant->variant_name && strtolower($variant->variant_name) !== 'standard') {
            $itemName .= ' - ' . $variant->variant_name;
        }

        if ($department === 'restaurant' || $department === 'kitchen') {
            // Food/drinks issued to Restaurant go into kitchen/restaurant stock records
            $this->inventoryService->updateKitchenInventory(
                $itemName,
                $quantity,
                $unit,
                $product->category ?? 'food',
                $staffId,
                $notes,
                null,
                $variant
            );
        } elseif ($department === 'housekeeping') {
            $this->inventoryService->updateHousekeepingInventory(
                $itemName,
                $quantity,
                $unit,
                $product->category ?? 'housekeeping',
                $staffId,
                $notes,
                $variant
            );
        }
    }

    protected function toBaseUnits(float $qty, string $unit, ProductVariant $variant): float
    {
        $unit = strtolower($unit);
        if (in_array($unit, ['packages', 'package', 'carton', 'cartons', 'crate', 'crates'])) {
            return $qty * max(1, (float) ($variant->items_per_package ?: 1));
        }
        return $qty;
    }

    /**
     * Prefer the product's real receiving unit (Pcs) over the legacy "bottles" default.
     */
    protected function normalizeIssueUnit(?string $unit, ?ProductVariant $variant, ?string $department = null): string
    {
        $raw = trim((string) $unit);
        $lower = strtolower($raw);
        $receiving = trim((string) ($variant->receiving_unit ?? ''));
        $product = $variant?->product;
        $category = strtolower((string) ($product->category ?? ''));
        $type = strtolower((string) ($product->type ?? ''));
        $isDrink = in_array($type, ['drink', 'beverage'], true) || in_array($category, [
            'vinywaji', 'alcoholic_beverage', 'non_alcoholic_beverage', 'water',
            'juices', 'energy_drinks', 'drinks', 'beverage', 'spirits', 'wines',
            'hot_beverages', 'soft_drinks', 'beers',
        ], true);

        if (in_array($lower, ['packages', 'package'], true)) {
            return 'packages';
        }

        // Explicit bottles only for drinks
        if (in_array($lower, ['bottles', 'bottle', 'pic'], true)) {
            return $isDrink ? 'bottles' : ($receiving !== '' ? $receiving : 'Pcs');
        }

        if ($raw !== '' && !in_array($lower, ['bottles', 'bottle'], true)) {
            return $raw;
        }

        if ($receiving !== '') {
            return $receiving;
        }

        if ($department === 'housekeeping' || in_array($category, ['housekeeping', 'cleaning_supplies', 'linens', 'amenities'], true)) {
            return 'Pcs';
        }

        return $isDrink ? 'bottles' : 'Pcs';
    }

    protected function findStaffForDepartment(string $department): ?Staff
    {
        $roles = match ($department) {
            'restaurant' => ['head_chef', 'head chef', 'chef', 'restaurant_manager', 'restaurant manager'],
            'housekeeping' => ['housekeeper', 'house keeper', 'linen_keeper', 'linen keeper'],
            'bar' => ['bar_keeper', 'bar keeper', 'bartender'],
            'kitchen' => ['head_chef', 'head chef', 'chef'],
            default => [],
        };

        $staff = Staff::where('is_active', true)
            ->where(function ($q) use ($roles) {
                foreach ($roles as $role) {
                    $q->orWhereRaw('LOWER(TRIM(role)) = ?', [strtolower($role)]);
                }
            })
            ->orderBy('name')
            ->first();

        if ($staff) {
            return $staff;
        }

        // Fallback: staff.department column (some users are tagged by department, not role)
        $deptNames = match ($department) {
            'restaurant' => ['restaurant', 'food', 'kitchen', 'bar'],
            'housekeeping' => ['housekeeping', 'house keeping', 'hk'],
            default => [$department],
        };

        return Staff::where('is_active', true)
            ->where(function ($q) use ($deptNames) {
                foreach ($deptNames as $name) {
                    $q->orWhereRaw('LOWER(TRIM(department)) = ?', [strtolower($name)]);
                }
            })
            ->orderBy('name')
            ->first();
    }

    protected function departmentFromRole(?string $role): string
    {
        $normalized = strtolower(str_replace(' ', '_', $role ?? ''));
        return match (true) {
            in_array($normalized, ['head_chef', 'chef', 'restaurant_manager', 'bar_keeper', 'bartender']) => 'Restaurant',
            in_array($normalized, ['housekeeper', 'linen_keeper']) => 'Housekeeping',
            default => 'Department',
        };
    }

    protected function buildProductCatalog(string $department): array
    {
        $query = Product::with(['variants' => function ($q) {
            $q->where('is_active', true)->orderBy('display_order');
        }])->where('is_active', true);

        if ($department === 'restaurant') {
            $query->where(function ($q) {
                $q->whereIn('type', ['drink', 'food', 'kitchen', 'restaurant', 'beverage'])
                    ->orWhereIn('category', [
                        'vinywaji', 'alcoholic_beverage', 'non_alcoholic_beverage', 'water',
                        'juices', 'energy_drinks', 'drinks', 'beverage', 'spirits', 'wines',
                        'hot_beverages', 'soft_drinks', 'beers',
                        'chakula', 'food', 'meat_poultry', 'seafood', 'vegetables', 'dairy',
                        'pantry_baking', 'spices_herbs', 'oils_fats', 'kitchen', 'snacks', 'restaurant',
                    ])
                    ->orWhereHas('departments', fn ($d) => $d->whereIn('code', ['restaurant', 'shared', 'bar', 'kitchen']));
            });
        } else {
            $query->where(function ($q) {
                $q->whereIn('type', ['housekeeping', 'general'])
                    ->orWhereIn('category', [
                        'cleaning_supplies', 'linens', 'housekeeping', 'general', 'amenities',
                    ])
                    ->orWhereHas('departments', fn ($d) => $d->whereIn('code', ['housekeeping', 'shared']));
            });
        }

        return $query->orderBy('name')->get()->flatMap(function (Product $product) {
            return $product->variants->map(function (ProductVariant $variant) use ($product) {
                $available = (float) $variant->getCurrentStock();
                $per = max(1, (float) ($variant->items_per_package ?: 1));
                $purchase = $variant->purchasing_unit ?: 'Package';
                $receive = $variant->receiving_unit ?: 'pcs';
                $isPackageMode = $per > 1 && in_array(strtolower($product->type ?? ''), ['drink', 'beverage']);

                return [
                    'product_id' => $product->id,
                    'product_variant_id' => $variant->id,
                    'name' => $product->name
                        . ($variant->variant_name && strtolower($variant->variant_name) !== 'standard'
                            ? ' · ' . $variant->variant_name
                            : ''),
                    'available' => $available,
                    'available_packages' => floor($available / $per),
                    'items_per_package' => $per,
                    'purchasing_unit' => $purchase,
                    'receiving_unit' => $receive,
                    'unit_cost' => (float) $variant->getLatestUnitCost(),
                    'package_mode' => $isPackageMode,
                    'is_returnable' => (bool) ($product->is_returnable ?? false),
                ];
            });
        })->filter(fn ($row) => $row['available'] > 0)->values()->all();
    }

    /**
     * Persist returnable flag from distribute screen (broom/mop style tools).
     */
    protected function applyReturnableFlag(?int $productId, mixed $isReturnable): void
    {
        if (!$productId) {
            return;
        }

        $product = Product::find($productId);
        if (!$product) {
            return;
        }

        $flag = filter_var($isReturnable, FILTER_VALIDATE_BOOLEAN);
        if ((bool) $product->is_returnable === $flag) {
            return;
        }

        $product->is_returnable = $flag;
        $product->save();
    }

    public function show(StockTransfer $stockTransfer)
    {
        $stockTransfer->load(['product', 'productVariant', 'transferredBy', 'receivedBy']);

        return response()->json([
            'success' => true,
            'transfer' => $stockTransfer,
        ]);
    }

    public function updateStatus(Request $request, StockTransfer $stockTransfer)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,completed,cancelled',
        ]);

        if ($validated['status'] === 'completed' && !$stockTransfer->received_at) {
            $validated['received_at'] = now();
            if (!$stockTransfer->received_by) {
                $validated['received_by'] = Auth::guard('staff')->id();
            }
        }

        $stockTransfer->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Transfer status updated successfully!',
                'transfer' => $stockTransfer->load(['product', 'productVariant', 'transferredBy', 'receivedBy']),
            ]);
        }

        return redirect()->back()
            ->with('success', 'Transfer status updated successfully!');
    }

    public function download(StockTransfer $stockTransfer)
    {
        $transfer = $stockTransfer->load(['product', 'productVariant', 'transferredBy', 'receivedBy']);
        return view('dashboard.stock-transfer-pdf', compact('transfer'));
    }
}
