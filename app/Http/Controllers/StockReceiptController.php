<?php

namespace App\Http\Controllers;

use App\Models\StockReceipt;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockReceiptController extends Controller
{
    /**
     * Display a listing of stock receipts
     */
    public function index(Request $request)
    {
        $user = Auth::guard('staff')->user();
        $role = strtolower($user->role ?? 'manager');
        
        $query = StockReceipt::with(['product', 'productVariant', 'supplier', 'receivedBy']);
        
        // Filter by product type
        if ($request->has('type') && in_array($request->type, ['drink', 'food'])) {
            $query->whereHas('product', function($q) use ($request) {
                $q->where('type', $request->type);
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
            $query->where(function($q) use ($search) {
                $q->whereHas('product', function($q2) use ($search) {
                    $q2->where('name', 'LIKE', "%{$search}%");
                })
                ->orWhereHas('supplier', function($q2) use ($search) {
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
        $type = $request->type;
        
        $query = Product::where('is_active', true);
        
        if ($type && in_array($type, ['drink', 'food'])) {
            $query->where('type', $type);
        }
        
        $products = $query->with('variants')
            ->orderBy('name')
            ->get();
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        
        return view('dashboard.stock-receipt-form', compact('products', 'suppliers', 'role', 'type'));
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
            'quantity_received_packages' => 'required|integer|min:1',
            'buying_price_per_bottle' => 'required|numeric|min:0',
            'selling_price_per_bottle' => 'required|numeric|min:0',
            'discount_type' => 'nullable|in:percentage,fixed,none',
            'discount_amount' => 'nullable|numeric|min:0',
            'received_date' => 'required|date',
            'expiry_date' => 'nullable|date|after_or_equal:received_date',
            'minimum_stock_level' => 'required|integer|min:0',
            'minimum_stock_level_unit' => 'required|in:bottles,packages',
            'notes' => 'nullable|string',
        ]);

        // Verify product variant belongs to product
        $variant = ProductVariant::findOrFail($validated['product_variant_id']);
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

        return redirect()->route('admin.stock-receipts.index')
            ->with('success', 'Stock receipt created successfully!');
    }

    /**
     * Get product variants for a specific product (AJAX)
     */
    public function getProductVariants(Product $product)
    {
        $variants = $product->variants()->where('is_active', true)->orderBy('display_order')->get();
        
        // Add packaging_name to each variant
        $variants->transform(function($variant) {
            $variant->packaging_name = match($variant->packaging) {
                'crates' => 'Crates',
                'carton' => 'Cartons',
                'boxes' => 'Boxes',
                'bags' => 'Bags',
                'packets' => 'Packets',
                default => ucfirst($variant->packaging),
            };
            return $variant;
        });
        
        return response()->json([
            'success' => true,
            'variants' => $variants,
        ]);
    }

    /**
     * Calculate stock receipt totals (AJAX)
     */
    public function calculateTotals(Request $request)
    {
        $request->validate([
            'product_variant_id' => 'required|exists:product_variants,id',
            'quantity_received_packages' => 'required|integer|min:1',
            'buying_price_per_bottle' => 'required|numeric|min:0',
            'selling_price_per_bottle' => 'required|numeric|min:0',
        ]);

        $variant = ProductVariant::findOrFail($request->product_variant_id);
        $quantityPackages = $request->quantity_received_packages;
        $buyingPrice = $request->buying_price_per_bottle;
        $sellingPrice = $request->selling_price_per_bottle;

        $totalBottles = $quantityPackages * $variant->items_per_package;
        $profitPerBottle = $sellingPrice - $buyingPrice;
        $totalBuyingCost = $totalBottles * $buyingPrice;
        $totalProfit = $totalBottles * $profitPerBottle;

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
