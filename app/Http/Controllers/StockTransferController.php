<?php

namespace App\Http\Controllers;

use App\Models\StockTransfer;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockTransferController extends Controller
{
    /**
     * Display a listing of stock transfers
     */
    public function index(Request $request)
    {
        $user = Auth::guard('staff')->user();
        $role = strtolower($user->role ?? 'manager');
        
        $query = StockTransfer::with(['product', 'productVariant', 'transferredBy', 'receivedBy']);
        
        // Filter by status
        if ($request->has('status') && in_array($request->status, ['pending', 'completed', 'cancelled'])) {
            $query->where('status', $request->status);
        }
        
        // Filter by date range
        if ($request->has('date_from')) {
            $query->where('transfer_date', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->where('transfer_date', '<=', $request->date_to);
        }
        
        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('transfer_reference', 'LIKE', "%{$search}%")
                  ->orWhereHas('product', function($q2) use ($search) {
                      $q2->where('name', 'LIKE', "%{$search}%");
                  })
                  ->orWhereHas('transferredBy', function($q2) use ($search) {
                      $q2->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }
        
        // Filter by type
        $type = $request->query('type', 'drink');
        $query->whereHas('product', function($q) use ($type) {
            $q->where('type', $type);
        });
        
        $transfers = $query->orderBy('transfer_date', 'desc')
                          ->orderBy('created_at', 'desc')
                          ->paginate(20);
        
        return view('dashboard.stock-transfers-list', compact('transfers', 'role', 'type'));
    }

    /**
     * Show the form for creating a new stock transfer
     */
    public function create(Request $request)
    {
        $user = Auth::guard('staff')->user();
        $role = strtolower($user->role ?? 'manager');
        
        $type = $request->query('type', 'drink');
        
        $products = Product::with(['variants'])
            ->where('is_active', true)
            ->where('type', $type)
            ->orderBy('name')
            ->get();
        
        // Auto-select bar keeper (get the first/only one)
        $barKeeper = Staff::where(function($q) {
                            $q->where('role', 'bar_keeper')
                              ->orWhere('role', 'bar keeper')
                              ->orWhere('role', 'bartender');
                        })
                        ->where('is_active', true)
                        ->orderBy('name')
                        ->first();
        
        // Calculate available stock for each variant
        $availableStock = [];
        foreach ($products as $product) {
            foreach ($product->variants as $variant) {
                // Calculate total packages received
                $totalPackagesReceived = \App\Models\StockReceipt::where('product_variant_id', $variant->id)
                    ->sum('quantity_received_packages');
                
                // Calculate total packages transferred
                $totalPackagesTransferred = StockTransfer::where('product_variant_id', $variant->id)
                    ->where('quantity_unit', 'packages')
                    ->where('status', '!=', 'cancelled')
                    ->sum('quantity_transferred');
                
                // Calculate total bottles transferred and convert to packages
                $totalBottlesTransferred = StockTransfer::where('product_variant_id', $variant->id)
                    ->where('quantity_unit', 'bottles')
                    ->where('status', '!=', 'cancelled')
                    ->sum('quantity_transferred');
                
                $bottlesToPackages = $variant->items_per_package > 0 
                    ? floor($totalBottlesTransferred / $variant->items_per_package) 
                    : 0;
                
                // Available packages and bottles
                $availablePackages = max(0, $totalPackagesReceived - ($totalPackagesTransferred + $bottlesToPackages));
                $availableBottles = $availablePackages * ($variant->items_per_package ?? 0);
                
                $availableStock[$variant->id] = [
                    'packages' => $availablePackages,
                    'bottles' => $availableBottles,
                ];
            }
        }
        
        return view('dashboard.stock-transfer-form', compact('products', 'barKeeper', 'availableStock', 'role', 'type'));
    }

    /**
     * Store a newly created stock transfer
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'product_variant_id' => 'required|exists:product_variants,id',
            'quantity_transferred' => 'required|numeric|min:0.01',
            'quantity_unit' => 'required|in:packages,bottles,pic',
            'received_by' => 'required|exists:staffs,id',
            'transfer_date' => 'required|date',
            'expiry_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'unit_cost' => 'nullable|numeric|min:0',
        ]);

        if ($validated['quantity_unit'] === 'pic') {
            $validated['quantity_unit'] = 'bottles'; // Map 'pic' to 'bottles' for DB compatibility if needed, or keep as is if enum supports it. Assuming 'bottles' encompasses 'pic' for now based on previous context.
        }

        // Verify product variant belongs to product
        $variant = ProductVariant::findOrFail($validated['product_variant_id']);
        if ($variant->product_id != $validated['product_id']) {
            return response()->json([
                'success' => false,
                'message' => 'Product variant does not belong to selected product.',
            ], 422);
        }

        // Verify received_by is a bar keeper
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

        $transfer = StockTransfer::create($validated);
        
        // Calculate revenue projections
        $transfer->calculateRevenueProjections();
        $transfer->save();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Stock transfer created successfully!',
                'transfer' => $transfer->load(['product', 'productVariant', 'transferredBy', 'receivedBy']),
            ]);
        }

        return redirect()->route('admin.stock-transfers.index')
            ->with('success', 'Stock transfer created successfully!');
    }

    /**
     * Display the specified stock transfer
     */
    public function show(StockTransfer $stockTransfer)
    {
        $stockTransfer->load(['product', 'productVariant', 'transferredBy', 'receivedBy']);
        
        return response()->json([
            'success' => true,
            'transfer' => $stockTransfer,
        ]);
    }

    /**
     * Update the transfer status (mark as completed or cancelled)
     */
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

    /**
     * Download stock transfer note
     */
    public function download(StockTransfer $stockTransfer)
    {
        $transfer = $stockTransfer->load(['product', 'productVariant', 'transferredBy', 'receivedBy']);
        return view('dashboard.stock-transfer-pdf', compact('transfer'));
    }
}
