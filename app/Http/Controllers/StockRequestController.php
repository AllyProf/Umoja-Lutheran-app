<?php

namespace App\Http\Controllers;

use App\Models\StockRequest;
use App\Models\ProductVariant;
use App\Models\StockTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StockRequestController extends Controller
{
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

        $stockRequests = $query->latest()->paginate(20);

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

        if ($isChef) {
            // Chef requests food and kitchen items
            $query->whereHas('product', function ($q) {
                $q->whereIn('category', ['food', 'kitchen'])
                    ->orWhere('type', 'kitchen');
            });
        } elseif ($isHousekeeper) {
            // Housekeeper requests cleaning supplies and linens
            $query->whereHas('product', function ($q) {
                $q->whereIn('category', ['cleaning_supplies', 'linens', 'housekeeping'])
                    ->orWhere('type', 'housekeeping');
            });
        } else {
            // Others (Counter/Bar) request beverages
            $query->whereHas('product', function ($q) {
                $q->whereIn('category', ['non_alcoholic_beverage', 'alcoholic_beverage', 'drinks', 'beverage']);
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

        $status = ($isChef || $isHousekeeper) ? 'pending_manager' : 'pending_accountant';
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

            StockRequest::create([
                'requested_by' => $user->id,
                'product_variant_id' => $item['product_variant_id'],
                'quantity' => $item['quantity'],
                'unit' => $item['unit'],
                'status' => $status,
                'notes' => $notes,
                'unit_cost' => $storedUnitCost,
                'total_cost' => $totalCost,
            ]);
        }

        $count = count($request->items);
        $message = ($isChef || $isHousekeeper)
            ? "$count item(s) submitted to Manager."
            : "$count beverage request(s) submitted to Accountant.";

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

        return redirect()->back()->with('error', 'Request has been rejected.');
    }

    /**
     * Storekeeper distributes the products — creates a StockTransfer automatically.
     */
    public function distribute(Request $request, StockRequest $stockRequest)
    {
        if (Auth::guard('staff')->user()->role !== 'storekeeper') {
            abort(403, 'Only storekeepers can distribute products.');
        }

        if ($stockRequest->status !== 'approved') {
            return redirect()->back()->with('error', 'Request must be approved by Manager first.');
        }

        DB::beginTransaction();
        try {
            // Get the latest buying price for accurate revenue projections
            $latestReceipt = \App\Models\StockReceipt::where('product_variant_id', $stockRequest->product_variant_id)
                ->orderBy('received_date', 'desc')
                ->orderBy('created_at', 'desc')
                ->first();

            $unitCost = null;
            if ($latestReceipt) {
                $raw_price = $latestReceipt->buying_price_per_bottle;
                $variant = $stockRequest->productVariant;
                $sp = $variant->selling_price_per_pic ?? 0;

                // If raw price > selling price for a bottle, it's definitely a package price
                $isPackagePrice = false;
                if (($variant->items_per_package ?? 0) > 0 && $sp > 0 && $raw_price > $sp) {
                    $isPackagePrice = true;
                }

                if ($stockRequest->unit === 'packages') {
                    $unitCost = $isPackagePrice ? $raw_price : $raw_price * ($variant->items_per_package ?? 1);
                } else {
                    $unitCost = $isPackagePrice ? $raw_price / ($variant->items_per_package ?? 1) : $raw_price;
                }
            }

            $stockRequest->loadMissing('requester');
            $receiverRole = strtolower(str_replace(' ', '_', $stockRequest->requester->role ?? ''));
            $isInternal = in_array($receiverRole, ['head_chef', 'housekeeper', 'linen_keeper']);

            $transferStatus = $isInternal ? 'completed' : 'pending';
            $receivedAt = $isInternal ? Carbon::now() : null;

            $transfer = StockTransfer::create([
                'transfer_reference' => StockTransfer::generateReference(),
                'product_id' => $stockRequest->productVariant->product_id,
                'product_variant_id' => $stockRequest->product_variant_id,
                'quantity_transferred' => $stockRequest->quantity,
                'quantity_unit' => $stockRequest->unit,
                'unit_cost' => $unitCost,
                'transferred_by' => Auth::guard('staff')->id(),
                'received_by' => $stockRequest->requested_by,
                'status' => $transferStatus,
                'transfer_date' => Carbon::now(),
                'received_at' => $receivedAt,
                'notes' => 'Auto-generated from Stock Request #' . $stockRequest->id,
            ]);

            // Calculate revenue projections if available
            if (method_exists($transfer, 'calculateRevenueProjections')) {
                $transfer->calculateRevenueProjections();
                $transfer->save();
            }

            $stockRequest->update([
                'status' => 'completed',
                'storekeeper_id' => Auth::guard('staff')->id(),
                'distributed_at' => Carbon::now(),
                'stock_transfer_id' => $transfer->id,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Products distributed successfully. Stock transfer created.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to distribute: ' . $e->getMessage());
        }
    }
}
