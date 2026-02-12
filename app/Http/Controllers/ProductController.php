<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of products
     */
    public function index(Request $request)
    {
        $user = Auth::guard('staff')->user();
        $role = strtolower($user->role ?? 'manager');
        
        $query = Product::with(['supplier', 'variants']);
        
        // Bar Keeper only sees bar products (drinks)
        if ($role === 'bar_keeper') {
            $query->where('type', 'drink');
        } else {
            // Managers can filter by type
            if ($request->has('type') && in_array($request->type, ['drink', 'food'])) {
                $query->where('type', $request->type);
            }
        }
        
        // Filter by category
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }
        
        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('brand_or_type', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }
        
        // Order by category priority and then name
        $query->orderByRaw("CASE 
            WHEN category IN ('non_alcoholic_beverage', 'energy_drinks', 'juices') THEN 1
            WHEN category = 'water' THEN 2
            WHEN category = 'alcoholic_beverage' THEN 3
            WHEN category = 'wines' THEN 4
            WHEN category = 'spirits' THEN 5
            WHEN category = 'hot_beverages' THEN 6
            WHEN category = 'cocktails' THEN 7
            ELSE 8 END")
            ->orderBy('name');
            
        // Clone for stats before pagination
        $statsQuery = clone $query;
        $totalBrands = $statsQuery->count();
        $totalVariants = \App\Models\ProductVariant::whereHas('product', function($q) use ($role, $request) {
            if ($role === 'bar_keeper') $q->where('type', 'drink');
            if ($request->has('category')) $q->where('category', $request->category);
        })->count();
        $totalCategories = $statsQuery->distinct()->count('category');
        $activeItems = $statsQuery->where('is_active', true)->count();
        
        $products = $query->paginate(200);
        
        $summaryStats = [
            'brands' => $totalBrands,
            'variants' => $totalVariants,
            'categories' => $totalCategories,
            'active' => $activeItems
        ];
        
        return view('dashboard.products-list', compact('products', 'role', 'summaryStats'));
    }

    /**
     * Show the form for creating a new product
     */
    public function create()
    {
        $user = Auth::guard('staff')->user();
        $role = strtolower($user->role ?? 'manager');
        
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        
        return view('dashboard.product-form', compact('suppliers', 'role'));
    }

    /**
     * Store a newly created product (Brand + Variants)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255', // Brand Name
            'category' => 'required|string',
            'description' => 'nullable|string',
            'type' => 'nullable|string',
            
            // Variants Validation
            'variants' => 'required|array|min:1',
            'variants.*.name' => 'required|string|max:255',
            'variants.*.measurement' => 'required|string',
            'variants.*.unit' => 'required|string', // ml, l, etc.
            'variants.*.selling_method' => 'required|in:pic,glass,mixed',
            'variants.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            
            // Configuration checks
            'variants.*.servings' => 'nullable|integer|min:1',
        ]);

        $user = Auth::guard('staff')->user();
        $role = $user->role;
        $isBarKeeper = ($role === 'bar_keeper' || $role === 'barkeeper');
        
        DB::beginTransaction();
        try {
            // Determine valid type (drink or food)
            $type = 'drink'; // Default
            if (isset($validated['type'])) {
                 if ($validated['type'] === 'food' || $validated['type'] === 'kitchen') {
                     $type = 'food';
                 }
            }
            // Auto-detect from category if needed
            $foodCategories = ['food', 'meat_poultry', 'seafood', 'pantry', 'dairy', 'baking', 'vegetables', 'spices', 'sauces', 'bakery'];
            if (in_array($validated['category'], $foodCategories)) {
                $type = 'food';
            }

            // 1. Create Parent Product (Brand)
            $productData = [
                'name' => $validated['name'],
                'category' => $validated['category'],
                'description' => $validated['description'],
                'type' => $type,
                'is_active' => true,
                'supplier_id' => null, // Optional now
            ];
            
            $product = Product::create($productData);

            // 2. Create Variants
            foreach ($request->variants as $index => $variantData) {
                // Determine selling flags
                $method = $variantData['selling_method'];
                $canSellPic = in_array($method, ['pic', 'mixed']);
                $canSellServing = in_array($method, ['glass', 'mixed']);
                
                // Handle Variant Image
                $imagePath = null;
                if (isset($variantData['image']) && $variantData['image'] instanceof \Illuminate\Http\UploadedFile) {
                    $imagePath = $variantData['image']->store('products/variants', 'public');
                }

                ProductVariant::create([
                    'product_id' => $product->id,
                    'variant_name' => $variantData['name'],
                    'measurement' => $variantData['measurement'] . ' ' . $variantData['unit'],
                    'image' => $imagePath,
                    
                    // Selling Config
                    'can_sell_as_pic' => $canSellPic,
                    'can_sell_as_serving' => $canSellServing,
                    'selling_unit' => $canSellServing ? ($variantData['unit'] === 'ml' ? 'glass' : 'serving') : 'pic',
                    'servings_per_pic' => $variantData['servings'] ?? 1,
                    
                    // Prices (Set to 0 as they are managed via stock/prices)
                    'selling_price_per_pic' => 0,
                    'selling_price_per_serving' => 0,
                    
                    'display_order' => $index,
                    'is_active' => true,
                ]);
            }
            
            DB::commit();

            $routePrefix = $isBarKeeper ? 'bar-keeper' : 'admin';
            return redirect()->route($routePrefix . '.products.index')
                ->with('success', 'Brand and ' . count($request->variants) . ' items registered successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to register product: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified product
     */
    public function show(Product $product)
    {
        $product->load(['supplier', 'variants']);
        
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'product' => $product,
            ]);
        }
        
        $user = Auth::guard('staff')->user();
        $role = strtolower($user->role ?? 'manager');
        
        return view('dashboard.product-show', compact('product', 'role'));
    }

    /**
     * Show the form for editing the specified product
     */
    public function edit(Product $product)
    {
        $user = Auth::guard('staff')->user();
        $role = strtolower($user->role ?? 'manager');
        
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        $product->load('variants');
        
        return view('dashboard.product-form', compact('product', 'suppliers', 'role'));
    }

    /**
     * Update the specified product
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'category' => 'required|string',
            'description' => 'nullable|string',
            'variants' => 'required|array|min:1',
            'variants.*.id' => 'nullable|exists:product_variants,id',
            'variants.*.name' => 'required|string|max:255',
            'variants.*.measurement' => 'required|string',
            'variants.*.unit' => 'required|string', // ml, l
            'variants.*.selling_method' => 'required|in:pic,glass,mixed',
            'variants.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'variants.*.servings' => 'nullable|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            // Determine type
            $type = 'drink';
            $foodCategories = ['food', 'meat_poultry', 'seafood', 'pantry', 'dairy', 'baking', 'vegetables', 'spices', 'sauces', 'bakery'];
            if (in_array($validated['category'], $foodCategories)) {
                $type = 'food';
            }

            // Update Parent Product
            $product->update([
                'name' => $validated['name'],
                'category' => $validated['category'],
                'description' => $validated['description'],
                'type' => $type,
                'supplier_id' => $validated['supplier_id'] ?? null,
            ]);

            // Update or create variants
            $existingVariantIds = [];
            foreach ($request->variants as $index => $variantData) {
                // Determine selling flags
                $method = $variantData['selling_method'];
                $canSellPic = in_array($method, ['pic', 'mixed']);
                $canSellServing = in_array($method, ['glass', 'mixed']);

                $data = [
                    'variant_name' => $variantData['name'],
                    'measurement' => $variantData['measurement'] . ' ' . $variantData['unit'],
                    'can_sell_as_pic' => $canSellPic,
                    'can_sell_as_serving' => $canSellServing,
                    'selling_unit' => $canSellServing ? ($variantData['unit'] === 'ml' ? 'glass' : 'serving') : 'pic',
                    'servings_per_pic' => $variantData['servings'] ?? 1,
                    'display_order' => $index,
                ];

                // Handle Image
                if (isset($variantData['image']) && $variantData['image'] instanceof \Illuminate\Http\UploadedFile) {
                    $data['image'] = $variantData['image']->store('products/variants', 'public');
                }

                if (isset($variantData['id'])) {
                    // Update existing
                    $variant = ProductVariant::find($variantData['id']);
                    if ($variant && $variant->product_id === $product->id) {
                        $variant->update($data);
                        $existingVariantIds[] = $variant->id;
                    }
                } else {
                    // Create new
                    $variant = ProductVariant::create(array_merge($data, ['product_id' => $product->id, 'is_active' => true]));
                    $existingVariantIds[] = $variant->id;
                }
            }

            // Delete removed variants
            // Note: Be careful with deletions if stock exists. For now, we allow it but in production prevent if constraint.
            // Ideally we should check if they can be deleted.
            // $product->variants()->whereNotIn('id', $existingVariantIds)->delete(); 
            // Commented out deletion to prevent accidental data loss until better UI is in place

            DB::commit();
            
            return redirect()->route($this->getRoutePrefix() . '.products.index', ['type' => $product->type])
                ->with('success', 'Product updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Update failed: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified product
     */
    public function destroy(Product $product)
    {
        // Check if product has stock receipts
        if ($product->stockReceipts()->count() > 0) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete product with existing stock receipts.',
                ], 422);
            }
            return redirect()->route($this->getRoutePrefix() . '.products.index')
                ->with('error', 'Cannot delete product with existing stock receipts.');
        }

        // Delete image
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully!',
            ]);
        }

        return redirect()->route($this->getRoutePrefix() . '.products.index')
            ->with('success', 'Product deleted successfully!');
    }

    /**
     * Remove the specified product variant
     */
    public function destroyVariant($id)
    {
        $variant = ProductVariant::findOrFail($id);
        $product = $variant->product;
        
        // Check if variant has specific dependencies if needed (like order items)
        // For now, allow delete.
        
        $variant->delete();
        
        // If parent product has no more variants, delete it too? 
        // Or keep it empty? Let's keep it for now, or check count.
        if ($product->variants()->count() === 0) {
            $product->delete();
            return response()->json([
                'success' => true,
                'message' => 'Variant and empty brand family deleted successfully!',
                'reload' => true
            ]);
        }
        
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Product variant deleted successfully!',
                'reload' => true
            ]);
        }
        
        return back()->with('success', 'Variant deleted successfully!');
    }

    /**
     * Configure serving details for a product variant (PIC-based)
     */
    public function configureServing(ProductVariant $variant)
    {
        $user = Auth::guard('staff')->user();
        $role = strtolower($user->role ?? 'manager');
        
        $variant->load('product');
        
        return view('dashboard.product-configure-serving', compact('variant', 'role'));
    }

    /**
     * Update serving configuration for a product variant
     */
    public function updateServing(Request $request, ProductVariant $variant)
    {
        $validated = $request->validate([
            'servings_per_pic' => 'required|integer|min:1|max:100',
            'selling_unit' => 'required|in:pic,glass,tot,shot,cocktail',
            'can_sell_as_pic' => 'boolean',
            'can_sell_as_serving' => 'boolean',
            'selling_price_per_pic' => 'nullable|numeric|min:0',
            'selling_price_per_serving' => 'nullable|numeric|min:0',
        ]);
        
        $variant->update($validated);
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Serving configuration updated successfully!',
                'variant' => $variant->fresh(),
            ]);
        }
        
        return redirect()->route($this->getRoutePrefix() . '.products.show', $variant->product_id)
            ->with('success', 'Serving configuration updated successfully!');
    }

    /**
     * Get route prefix based on role
     */
    private function getRoutePrefix(): string
    {
        $role = strtolower(auth()->guard('staff')->user()->role ?? 'manager');
        return ($role === 'bar_keeper') ? 'bar-keeper' : 'admin';
    }
}
