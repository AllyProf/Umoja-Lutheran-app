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

        $stats = [
            'total_items' => Product::count(),
            'low_stock_alerts' => ProductVariant::whereRaw('minimum_stock_level > 0')
                ->whereExists(function ($query) {
                    // This logic would need to be refined based on actual stock calculation
                    // For now, let's just get variants with minimum stock level set
                })->count(), // Placeholder
            'pending_requests' => PurchaseRequest::where('status', 'pending')->count(),
            'active_shopping_lists' => ShoppingList::whereIn('status', ['pending', 'approved', 'on_list'])->count(),
        ];

        $recentShoppingLists = ShoppingList::latest()
            ->limit(5)
            ->get();

        $recentTransfers = StockTransfer::with(['product', 'productVariant', 'receivedBy'])
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard.storekeeper.dashboard', compact('stats', 'recentShoppingLists', 'recentTransfers'));
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

                if ($variant->minimum_stock_level > 0 && $currentStock <= $variant->minimum_stock_level) {
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
