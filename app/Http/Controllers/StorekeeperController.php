<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockReceipt;
use App\Models\StockTransfer;
use App\Models\PurchaseRequest;
use App\Models\ShoppingList;
use App\Models\ShoppingListItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StorekeeperController extends Controller
{
    /**
     * Storekeeper Dashboard
     */
    public function dashboard()
    {
        $user = Auth::guard('staff')->user();

        $approvedRequests = \App\Models\StockRequest::where('status', 'approved')->count();
        $readyToPurchase = ShoppingList::whereIn('status', ['ready_for_purchase', 'purchased'])->count();
        $pendingLists = ShoppingList::whereIn('status', ['pending', 'accountant_checked', 'approved'])->count();

        // Approximate low-stock: variants with a minimum set and current stock at/below it
        $lowStockAlerts = 0;
        $lowStockItems = collect();
        $variantsWithMin = ProductVariant::with('product')
            ->where('is_active', true)
            ->where('minimum_stock_level', '>', 0)
            ->orderBy('product_id')
            ->limit(80)
            ->get();

        foreach ($variantsWithMin as $variant) {
            $stock = (float) $variant->getCurrentStock();
            $min = (float) $variant->minimum_stock_level;
            if ($stock <= $min) {
                $lowStockAlerts++;
                if ($lowStockItems->count() < 6) {
                    $lowStockItems->push([
                        'name' => ($variant->product->name ?? 'Item')
                            . ($variant->variant_name && strtolower($variant->variant_name) !== 'standard'
                                ? ' · ' . $variant->variant_name
                                : ''),
                        'stock' => $stock,
                        'minimum' => $min,
                        'unit' => $variant->receiving_unit ?: 'pcs',
                    ]);
                }
            }
        }

        $stats = [
            'total_items' => Product::where('is_active', true)->count(),
            'low_stock_alerts' => $lowStockAlerts,
            'approved_requests' => $approvedRequests,
            'ready_to_receive' => $readyToPurchase,
            'pending_lists' => $pendingLists,
            'today_receipts' => StockReceipt::whereDate('received_date', today())->count(),
            'today_transfers' => StockTransfer::whereDate('transfer_date', today())->count(),
        ];

        $waitingRequests = \App\Models\StockRequest::with(['requester', 'productVariant.product'])
            ->where('status', 'approved')
            ->orderByDesc('updated_at')
            ->limit(8)
            ->get();

        $recentShoppingLists = ShoppingList::latest()
            ->limit(5)
            ->get();

        $recentTransfers = StockTransfer::with(['product', 'productVariant', 'receivedBy'])
            ->latest()
            ->limit(5)
            ->get();

        $recentReceipts = StockReceipt::with(['product', 'productVariant', 'supplier'])
            ->latest('received_date')
            ->latest('id')
            ->limit(5)
            ->get();

        return view('dashboard.storekeeper.dashboard', compact(
            'user',
            'stats',
            'lowStockItems',
            'waitingRequests',
            'recentShoppingLists',
            'recentTransfers',
            'recentReceipts'
        ));
    }

    /**
     * Main Store Inventory
     */
    public function inventory(Request $request)
    {
        $query = Product::with(['variants']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'LIKE', "%{$search}%")
                ->orWhere('category', 'LIKE', "%{$search}%");
        }

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        $products = $query->paginate(20);

        // Calculate stock for each variant of these products
        foreach ($products as $product) {
            foreach ($product->variants as $variant) {
                $packageUnits = ProductVariant::getPackageUnits();
                $unitsList = "'" . implode("','", $packageUnits) . "'";

                // 1. Total In (Receipts + Shopping List)
                $receiptsIn = DB::table('stock_receipts')
                    ->join('product_variants', 'stock_receipts.product_variant_id', '=', 'product_variants.id')
                    ->join('products', 'stock_receipts.product_id', '=', 'products.id')
                    ->where('stock_receipts.product_variant_id', $variant->id)
                    ->sum(DB::raw('CASE WHEN products.category = "food" THEN quantity_received_packages ELSE (quantity_received_packages * product_variants.items_per_package) END'));

                $shoppingIn = DB::table('shopping_list_items')
                    ->join('product_variants', 'shopping_list_items.product_variant_id', '=', 'product_variants.id')
                    ->join('products', 'shopping_list_items.product_id', '=', 'products.id')
                    ->where('shopping_list_items.product_variant_id', $variant->id)
                    ->where('shopping_list_items.is_purchased', true)
                    ->sum(DB::raw("CASE 
                        WHEN (received_quantity_kg > 0) THEN received_quantity_kg 
                        WHEN LOWER(unit) IN ($unitsList) AND (products.category != 'food' OR products.category IS NULL) THEN purchased_quantity * product_variants.items_per_package 
                        ELSE purchased_quantity 
                    END"));

                $totalIn = (float) $receiptsIn + (float) $shoppingIn;

                // 1.5 Total Returned
                $returnsIn = DB::table('stock_returns')
                    ->where('product_variant_id', $variant->id)
                    ->where('status', 'received')
                    ->sum('quantity');

                $totalIn += (float) $returnsIn;

                // 2. Total Out (Transfers)
                $transfersOut = DB::table('stock_transfers')
                    ->join('product_variants', 'stock_transfers.product_variant_id', '=', 'product_variants.id')
                    ->where('stock_transfers.product_variant_id', $variant->id)
                    ->whereIn('stock_transfers.status', ['completed', 'pending'])
                    ->sum(DB::raw("CASE WHEN LOWER(quantity_unit) IN ($unitsList) THEN quantity_transferred * product_variants.items_per_package ELSE quantity_transferred END"));

                $variant->current_stock = $totalIn - (float) $transfersOut;
            }
        }

        $categories = Product::distinct()->whereNotNull('category')->pluck('category');
        $totalItems = Product::count();

        // Calculate low stock count for ALL variants
        $lowStockCount = 0;
        foreach (Product::with('variants')->get() as $product) {
            foreach ($product->variants as $variant) {
                // Calculate stock (reusing logic or simplified)
                $receiptsIn = DB::table('stock_receipts')
                    ->where('product_variant_id', $variant->id)
                    ->join('product_variants', 'stock_receipts.product_variant_id', '=', 'product_variants.id')
                    ->join('products', 'stock_receipts.product_id', '=', 'products.id')
                    ->sum(DB::raw('CASE WHEN products.category = "food" THEN quantity_received_packages ELSE (quantity_received_packages * product_variants.items_per_package) END'));

                $shoppingIn = DB::table('shopping_list_items')
                    ->where('product_variant_id', $variant->id)
                    ->where('is_purchased', true)
                    ->join('product_variants', 'shopping_list_items.product_variant_id', '=', 'product_variants.id')
                    ->join('products', 'shopping_list_items.product_id', '=', 'products.id')
                    ->sum(DB::raw("CASE 
                        WHEN (received_quantity_kg > 0) THEN received_quantity_kg 
                        WHEN LOWER(unit) IN ($unitsList) AND (products.category != 'food' OR products.category IS NULL) THEN purchased_quantity * product_variants.items_per_package 
                        ELSE purchased_quantity 
                    END"));

                $transfersOut = DB::table('stock_transfers')
                    ->where('product_variant_id', $variant->id)
                    ->whereIn('status', ['completed', 'pending'])
                    ->join('product_variants', 'stock_transfers.product_variant_id', '=', 'product_variants.id')
                    ->sum(DB::raw("CASE WHEN LOWER(quantity_unit) IN ($unitsList) THEN quantity_transferred * product_variants.items_per_package ELSE quantity_transferred END"));

                $returnsIn = DB::table('stock_returns')
                    ->where('product_variant_id', $variant->id)
                    ->where('status', 'received')
                    ->sum('quantity');

                $currentStock = ((float) $receiptsIn + (float) $shoppingIn + (float) $returnsIn) - (float) $transfersOut;

                if ($variant->isLowStock($currentStock)) {
                    $lowStockCount++;
                }
            }
        }
        return view('dashboard.storekeeper.inventory', compact('products', 'categories', 'totalItems', 'lowStockCount'));
    }

    /**
     * Purchase Requests from Departments
     */
    public function purchaseRequests(Request $request)
    {
        $tab = $request->get('tab', 'pending');
        $query = PurchaseRequest::with(['requestedBy', 'shoppingList']);

        if ($tab === 'pending') {
            $query->where('status', 'pending');
        } elseif ($tab === 'approved') {
            $query->whereIn('status', ['approved', 'on_list', 'purchased']);
        }

        $requests = $query->latest()->paginate(20);

        return view('dashboard.storekeeper.purchase-requests', compact('requests', 'tab'));
    }
}
