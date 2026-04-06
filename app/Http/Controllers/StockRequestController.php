<?php

namespace App\Http\Controllers;

use App\Models\StockRequest;
use App\Models\ProductVariant;
use App\Models\StockTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Services\NotificationService;
use App\Services\InventoryService;

class StockRequestController extends Controller
{
    protected $notificationService;
    protected $inventoryService;

    public function __construct(NotificationService $notificationService, InventoryService $inventoryService)
    {
        $this->notificationService = $notificationService;
        $this->inventoryService = $inventoryService;
    }
    /**
     * Display a listing of stock requests.
     */
    public function index(Request $request)
    {
        $user = Auth::guard('staff')->user();
        $query = StockRequest::with(['requester', 'productVariant.product', 'accountant', 'manager', 'storekeeper']);

        // Filter based on role
        if ($user->role === 'bar_keeper' || $user->role === 'bar keeper' || $user->role === 'head_chef' || $user->role === 'housekeeper') {
            $query->where('requested_by', $user->id);
        } elseif ($user->role === 'accountant') {
            // Accountants see all (primarily beverages)
        } elseif ($user->role === 'storekeeper') {
            $query->whereIn('status', ['approved', 'completed']);
        }
        // Managers and super_admin see everything
        if ($request->has('type')) {
            $type = $request->type;
            $query->whereHas('productVariant.product', function ($q) use ($type) {
                if ($type === 'drink') {
                    $q->whereIn('category', ['alcoholic_beverage', 'non_alcoholic_beverage', 'water', 'juices', 'energy_drinks', 'drinks', 'beverage']);
                } elseif ($type === 'food') {
                    $q->whereIn('category', ['food', 'meat_poultry', 'seafood', 'vegetables', 'dairy', 'pantry_baking', 'spices_herbs', 'oils_fats', 'kitchen', 'snacks']);
                } elseif ($type === 'housekeeping') {
                    $q->whereIn('category', ['cleaning_supplies', 'linens', 'housekeeping']);
                }
            });
        }

        $stockRequests = $query->orderBy('batch_id', 'desc')->orderBy('created_at', 'desc')->paginate(25);

        return view('dashboard.stock-requests.index', compact('stockRequests'));
    }

    /**
     * Show the form for creating a new stock request (Counter only).
     */
    public function create()
    {
        $user = Auth::guard('staff')->user();
        $isChef = ($user->role === 'head_chef');
        $isHousekeeper = ($user->role === 'housekeeper');

        $query = ProductVariant::with('product')
            ->where('is_active', true);

        $sharedCategories = ['cleaning_supplies', 'other', 'stationery'];

        if ($isChef) {
            // Chef requests food, kitchen items and shared supplies
            $query->whereHas('product', function ($q) use ($sharedCategories) {
                $q->whereIn('category', array_merge(['food', 'kitchen'], $sharedCategories))
                    ->orWhere('type', 'kitchen');
            });
        } elseif ($isHousekeeper) {
            // Housekeeper requests cleaning supplies, linens and housekeeping
            $query->whereHas('product', function ($q) {
                $q->whereIn('category', ['cleaning_supplies', 'linens', 'housekeeping', 'other'])
                    ->orWhere('type', 'housekeeping');
            });
        } else {
            // Others (Counter/Bar) request beverages + supplies + cleaning items
            $barCategories = ['non_alcoholic_beverage', 'alcoholic_beverage', 'drinks', 'beverage', 'water', 'juices', 'energy_drinks', 'soft_drinks', 'beers', 'wines', 'spirits', 'cocktails', 'liquor', 'supplies', 'equipment', 'sauces', 'hot_beverages'];
            $query->whereHas('product', function ($q) use ($barCategories, $sharedCategories) {
                $q->whereIn('category', array_merge($barCategories, $sharedCategories));
            });
        }

        $products = $query->get();

        // If no specifically filtered variants found, show all active variants (fallback)
        if ($products->isEmpty()) {
            $products = ProductVariant::with('product')->where('is_active', true)->get();
        }

        return view('dashboard.stock-requests.create', compact('products', 'isChef', 'isHousekeeper'));
    }

    /**
     * Counter creates a new beverage request.
     */
    public function store(Request $request)
    {
        $user = Auth::guard('staff')->user();
        $normalizedRole = strtolower(trim($user->role ?? ''));
        $isChef = in_array($normalizedRole, ['head_chef', 'head chef', 'chef']);
        $isHousekeeper = ($normalizedRole === 'housekeeper');

        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_variant_id' => 'required|exists:product_variants,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit' => 'required|string',
            'notes' => 'nullable|string|max:1000',
        ]);

        $isBarKeeper = in_array($normalizedRole, ['bar_keeper', 'bar keeper', 'bartender']);

        // Bar Keeper, Chef and Housekeeper all go directly to Manager
        $status = ($isChef || $isHousekeeper || $isBarKeeper) ? 'pending_manager' : 'pending_accountant';
        $notes = $request->notes;

        // Stock availability check
        $errors = [];
        foreach ($request->items as $item) {
            $variant = ProductVariant::with('product')->find($item['product_variant_id']);
            if (!$variant)
                continue;

            $currentStock = $variant->getCurrentStock();
            $requestedQty = (float) $item['quantity'];
            $unit = $item['unit'];

            // Convert requested quantity to base units for comparison
            $baseRequestedQty = $requestedQty;
            if ($unit === 'packages' || $unit === 'crates' || $unit === 'carton') {
                $baseRequestedQty = $requestedQty * ($variant->items_per_package ?? 1);
            } elseif ($unit === 'grams') {
                $baseRequestedQty = $requestedQty / 1000;
            }

            if ($baseRequestedQty > $currentStock) {
                $productName = $variant->product->name . ($variant->variant_name ? " ({$variant->variant_name})" : "");
                $availableDisplay = number_format($currentStock, 2);

                // Format available display based on category
                $baseUnit = ($variant->product->category === 'food' || $variant->product->category === 'kitchen') ? ($variant->receiving_unit ?? 'kg') : 'units';

                $errors[] = "Insufficient stock for **$productName**. Available in store: $availableDisplay $baseUnit. You requested: $requestedQty $unit.";
            }
        }

        if (!empty($errors)) {
            return redirect()->back()
                ->withInput()
                ->with('error_list', $errors);
        }

        $batchId = (string) \Illuminate\Support\Str::uuid();
        $today = date('ymd');
        $lastBatch = StockRequest::where('batch_reference', 'like', "REQ-$today-%")->orderBy('batch_reference', 'desc')->first();
        $nextNum = $lastBatch ? (int) substr($lastBatch->batch_reference, -3) + 1 : 1;
        $batchRef = "REQ-$today-" . str_pad($nextNum, 3, '0', STR_PAD_LEFT);

        foreach ($request->items as $item) {
            $variant = ProductVariant::find($item['product_variant_id']);
            $unitPrice = $variant ? $variant->getLatestUnitCost() : 0;

            $requestedQty = (float) $item['quantity'];
            $unit = $item['unit'];

            // Calculate total cost based on unit
            $calcQty = $requestedQty;
            if ($unit === 'packages' || $unit === 'crates' || $unit === 'carton') {
                $calcQty = $requestedQty * ($variant->items_per_package ?? 1);
            } elseif ($unit === 'grams') {
                $calcQty = $requestedQty / 1000;
            }

            $totalCost = $calcQty * $unitPrice;

            // Adjust unit_cost based on the requested unit for intuitive display in the table
            $storedUnitCost = $unitPrice;
            if ($unit === 'packages' || $unit === 'crates' || $unit === 'carton') {
                $storedUnitCost = $unitPrice * ($variant->items_per_package ?? 1);
            }

            $stockRequest = StockRequest::create([
                'requested_by' => $user->id,
                'batch_id' => $batchId,
                'batch_reference' => $batchRef,
                'product_variant_id' => $item['product_variant_id'],
                'quantity' => $item['quantity'],
                'unit' => $item['unit'],
                'status' => $status,
                'notes' => $notes,
                'unit_cost' => $storedUnitCost,
                'total_cost' => $totalCost,
            ]);

            // Notify Accountant or Manager
            $this->notificationService->createStockRequestCreatedNotification($stockRequest);
        }

        $count = count($request->items);
        $message = ($isChef || $isHousekeeper || $isBarKeeper)
            ? "$count item(s) submitted to Manager for approval."
            : "$count stock request(s) submitted to Accountant.";


        return redirect()->route('stock-requests.index')->with('success', $message);
    }

    /**
     * Return a single item row HTML for AJAX row-addition.
     */
    public function rowTemplate()
    {
        $user = Auth::guard('staff')->user();
        $normalizedRole = strtolower(trim($user->role ?? ''));
        $isChef = in_array($normalizedRole, ['head_chef', 'head chef', 'chef']);
        $isHousekeeper = ($normalizedRole === 'housekeeper');
        $index = (int) request('index', 1);

        $query = ProductVariant::with('product')->where('is_active', true);
        if ($isChef) {
            $query->whereHas('product', fn($q) => $q->whereIn('category', ['food', 'kitchen'])->orWhere('type', 'kitchen'));
        } elseif ($isHousekeeper) {
            $query->whereHas('product', fn($q) => $q->whereIn('category', ['cleaning_supplies', 'linens', 'housekeeping'])->orWhere('type', 'housekeeping'));
        } else {
            $query->whereHas('product', fn($q) => $q->whereIn('category', ['non_alcoholic_beverage', 'alcoholic_beverage', 'drinks', 'beverage']));
        }
        $products = $query->get();
        if ($products->isEmpty()) {
            $products = ProductVariant::with('product')->where('is_active', true)->get();
        }

        return view('dashboard.stock-requests._item_row', compact('index', 'products', 'isChef', 'isHousekeeper'));
    }

    /**
     * Accountant verifies and passes the request to the Manager.
     */
    public function passToManager(Request $request, StockRequest $stockRequest)
    {
        if (Auth::guard('staff')->user()->role !== 'accountant') {
            abort(403, 'Only accountants can forward requests.');
        }

        if ($stockRequest->status !== 'pending_accountant') {
            return redirect()->back()->with('error', 'This request is not pending accountant review.');
        }

        $stockRequest->update([
            'status' => 'pending_manager',
            'accountant_id' => Auth::guard('staff')->id(),
            'accountant_approved_at' => Carbon::now(),
        ]);

        $this->notificationService->createStockRequestPassedToManagerNotification($stockRequest);

        return redirect()->back()->with('success', 'Request verified and forwarded to Manager.');
    }

    /**
     * Manager approves the request (Storekeeper can then distribute).
     */
    public function approve(Request $request, StockRequest $stockRequest)
    {
        $user = Auth::guard('staff')->user();
        if (!$user->isManager() && !$user->isSuperAdmin()) {
            abort(403, 'Only managers can approve requests.');
        }

        if (!in_array($stockRequest->status, ['pending_manager', 'pending_accountant'])) {
            return redirect()->back()->with('error', 'This request is not pending manager or accountant approval.');
        }

        $stockRequest->update([
            'status' => 'approved',
            'manager_id' => $user->id,
            'manager_approved_at' => Carbon::now(),
        ]);

        $this->notificationService->createStockRequestStatusUpdateNotification($stockRequest, 'approved');

        return redirect()->back()->with('success', 'Request approved. Storekeeper can now distribute.');
    }

    /**
     * Reject a request (Accountant or Manager).
     */
    public function reject(Request $request, StockRequest $stockRequest)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $stockRequest->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        $this->notificationService->createStockRequestStatusUpdateNotification($stockRequest, 'rejected');

        return redirect()->back()->with('error', 'Request has been rejected.');
    }

    /**
     * Storekeeper distributes the products — accepts price/quantity input from form.
     */
    public function distribute(Request $request, StockRequest $stockRequest)
    {
        if (Auth::guard('staff')->user()->role !== 'storekeeper') {
            abort(403, 'Only storekeepers can distribute products.');
        }

        if ($stockRequest->status !== 'approved') {
            return redirect()->back()->with('error', 'Request must be approved by Manager first.');
        }

        $request->validate([
            'quantity_issued' => 'required|numeric|min:0.01',
            'unit_cost' => 'required|numeric|min:0',
        ]);

        $quantityIssued = (float) $request->quantity_issued;
        $unitCost = (float) $request->unit_cost;
        $totalCost = $quantityIssued * $unitCost;

        DB::beginTransaction();
        try {
            $stockRequest->loadMissing('requester');
            $receiverRole = strtolower(str_replace(' ', '_', $stockRequest->requester->role ?? ''));
            $isInternal = in_array($receiverRole, ['head_chef', 'housekeeper', 'linen_keeper']);

            $transferStatus = $isInternal ? 'completed' : 'pending';
            $receivedAt = $isInternal ? Carbon::now() : null;

            $transfer = StockTransfer::create([
                'transfer_reference' => StockTransfer::generateReference(),
                'product_id' => $stockRequest->productVariant->product_id,
                'product_variant_id' => $stockRequest->product_variant_id,
                'quantity_transferred' => $quantityIssued,
                'quantity_unit' => $stockRequest->unit,
                'unit_cost' => $unitCost,
                'transferred_by' => Auth::guard('staff')->id(),
                'received_by' => $stockRequest->requested_by,
                'status' => $transferStatus,
                'transfer_date' => Carbon::now(),
                'received_at' => $receivedAt,
                'notes' => 'Requisition Note #' . $stockRequest->id,
            ]);

            if (method_exists($transfer, 'calculateRevenueProjections')) {
                $transfer->calculateRevenueProjections();
                $transfer->save();
            }

            $stockRequest->update([
                'status' => 'completed',
                'quantity_issued' => $quantityIssued,
                'unit_cost' => $unitCost,
                'total_cost' => $totalCost,
                'storekeeper_id' => Auth::guard('staff')->id(),
                'distributed_at' => Carbon::now(),
                'stock_transfer_id' => $transfer->id,
            ]);

            $this->notificationService->createStockRequestStatusUpdateNotification($stockRequest, 'completed');

            if ($isInternal) {
                $itemName = $stockRequest->productVariant->product->name;
                if ($stockRequest->productVariant->variant_name && strtolower($stockRequest->productVariant->variant_name) !== 'standard') {
                    $itemName .= ' - ' . $stockRequest->productVariant->variant_name;
                }
                $quantity = $quantityIssued;
                $unit = $stockRequest->unit;
                $category = $stockRequest->productVariant->product->category;
                $staffId = $stockRequest->requested_by;
                $notes = "Issued from Store: Requisition #{$stockRequest->id}";
                $variant = $stockRequest->productVariant;

                if (in_array($receiverRole, ['head_chef', 'chef'])) {
                    $this->inventoryService->updateKitchenInventory($itemName, $quantity, $unit, $category, $staffId, $notes, null, $variant);
                } elseif (in_array($receiverRole, ['housekeeper', 'linen_keeper'])) {
                    $this->inventoryService->updateHousekeepingInventory($itemName, $quantity, $unit, $category, $staffId, $notes, $variant);
                }
            }

            DB::commit();

            // Redirect to print after distributing
            return redirect()->route('stock-requests.print', $stockRequest->id)
                ->with('success', 'Items issued successfully. Print the Requisition Note below.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to distribute: ' . $e->getMessage());
        }
    }

    /**
     * Print the Requisition/Issue Note for a stock request.
     */
    public function printNote(StockRequest $stockRequest)
    {
        $stockRequest->loadMissing([
            'requester',
            'productVariant.product',
            'manager',
            'storekeeper',
            'accountant',
        ]);

        // Suggest latest unit cost for pre-filling the form (if not yet distributed)
        $suggestedUnitCost = null;
        if ($stockRequest->status === 'approved') {
            $latestReceipt = \App\Models\StockReceipt::where('product_variant_id', $stockRequest->product_variant_id)
                ->orderBy('received_date', 'desc')
                ->orderBy('created_at', 'desc')
                ->first();

            if ($latestReceipt) {
                $raw_price = $latestReceipt->buying_price_per_bottle;
                $variant = $stockRequest->productVariant;
                $sp = $variant->selling_price_per_pic ?? 0;
                $isPackagePrice = ($variant->items_per_package ?? 0) > 0 && $sp > 0 && $raw_price > $sp;

                if ($stockRequest->unit === 'packages') {
                    $suggestedUnitCost = $isPackagePrice ? $raw_price : $raw_price * ($variant->items_per_package ?? 1);
                } else {
                    $suggestedUnitCost = $isPackagePrice ? $raw_price / ($variant->items_per_package ?? 1) : $raw_price;
                }
            }
        }

        return view('dashboard.stock-requests.print', compact('stockRequest', 'suggestedUnitCost'));
    }

    /**
     * Storekeeper distributes a batch of products.
     */
    public function batchDistribute(Request $request, $batchId)
    {
        if (Auth::guard('staff')->user()->role !== 'storekeeper') {
            abort(403, 'Only storekeepers can distribute products.');
        }

        $stockRequests = StockRequest::where('batch_id', $batchId)
            ->where('status', 'approved')
            ->get();

        if ($stockRequests->isEmpty()) {
            return redirect()->route('stock-requests.index')->with('error', 'No approved items found in this batch.');
        }

        if ($request->isMethod('get')) {
            $batchReference = $stockRequests->first()->batch_reference;
            return view('dashboard.stock-requests.batch_distribute', compact('stockRequests', 'batchId', 'batchReference'));
        }

        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:stock_requests,id',
            'items.*.quantity_issued' => 'required|numeric|min:0',
            'items.*.unit_cost' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->input('items') as $itemData) {
                $stockRequest = $stockRequests->find($itemData['id']);
                if (!$stockRequest)
                    continue;

                $quantityIssued = (float) $itemData['quantity_issued'];
                if ($quantityIssued <= 0)
                    continue;

                $unitCost = (float) $itemData['unit_cost'];
                $totalCost = $quantityIssued * $unitCost;

                $stockRequest->loadMissing('requester');
                $receiverRole = strtolower(str_replace(' ', '_', $stockRequest->requester->role ?? ''));
                $isInternal = in_array($receiverRole, ['head_chef', 'housekeeper', 'linen_keeper']);

                $transferStatus = $isInternal ? 'completed' : 'pending';
                $receivedAt = $isInternal ? Carbon::now() : null;

                $transfer = StockTransfer::create([
                    'transfer_reference' => StockTransfer::generateReference(),
                    'product_id' => $stockRequest->productVariant->product_id,
                    'product_variant_id' => $stockRequest->product_variant_id,
                    'quantity_transferred' => $quantityIssued,
                    'quantity_unit' => $stockRequest->unit,
                    'unit_cost' => $unitCost,
                    'transferred_by' => Auth::guard('staff')->id(),
                    'received_by' => $stockRequest->requested_by,
                    'status' => $transferStatus,
                    'transfer_date' => Carbon::now(),
                    'received_at' => $receivedAt,
                    'notes' => 'Batch Requisition ' . $stockRequest->batch_reference,
                ]);

                if (method_exists($transfer, 'calculateRevenueProjections')) {
                    $transfer->calculateRevenueProjections();
                    $transfer->save();
                }

                $stockRequest->update([
                    'status' => 'completed',
                    'quantity_issued' => $quantityIssued,
                    'unit_cost' => $unitCost,
                    'total_cost' => $totalCost,
                    'storekeeper_id' => Auth::guard('staff')->id(),
                    'distributed_at' => Carbon::now(),
                    'stock_transfer_id' => $transfer->id,
                ]);

                $this->notificationService->createStockRequestStatusUpdateNotification($stockRequest, 'completed');

                if ($isInternal) {
                    $itemName = $stockRequest->productVariant->product->name;
                    if ($stockRequest->productVariant->variant_name && strtolower($stockRequest->productVariant->variant_name) !== 'standard') {
                        $itemName .= ' - ' . $stockRequest->productVariant->variant_name;
                    }
                    if (in_array($receiverRole, ['head_chef', 'chef'])) {
                        $this->inventoryService->updateKitchenInventory($itemName, $quantityIssued, $stockRequest->unit, $stockRequest->productVariant->product->category, $stockRequest->requested_by, "Issued from Store: Batch {$stockRequest->batch_reference}", null, $stockRequest->productVariant);
                    } elseif (in_array($receiverRole, ['housekeeper', 'linen_keeper'])) {
                        $this->inventoryService->updateHousekeepingInventory($itemName, $quantityIssued, $stockRequest->unit, $stockRequest->productVariant->product->category, $stockRequest->requested_by, "Issued from Store: Batch {$stockRequest->batch_reference}", $stockRequest->productVariant);
                    }
                }
            }

            DB::commit();
            return redirect()->route('stock-requests.batch-print', $batchId)
                ->with('success', 'Batch items issued successfully. Print the Requisition Note below.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to distribute batch: ' . $e->getMessage());
        }
    }

    /**
     * Print consolidated requisition note for a batch.
     */
    public function batchPrint($batchId)
    {
        $stockRequests = StockRequest::where('batch_id', $batchId)
            ->with(['requester', 'productVariant.product', 'manager', 'storekeeper', 'accountant'])
            ->get();

        if ($stockRequests->isEmpty()) {
            abort(404, 'Batch not found.');
        }

        return view('dashboard.stock-requests.batch_print', compact('stockRequests', 'batchId'));
    }
}
