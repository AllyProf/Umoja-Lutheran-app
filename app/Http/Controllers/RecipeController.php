<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RecipeController extends Controller
{
    /**
     * Display a listing of menu items
     */
    public function index()
    {
        $recipes = Recipe::with('creator')->latest()->paginate(12);
        $categories = Recipe::select('category')->distinct()->pluck('category');
        return view('admin.restaurants.recipes.index', compact('recipes', 'categories'));
    }

    /**
     * Show the form for creating a new menu item
     */
    public function create()
    {
        $categories = [
            'appetizers' => 'Appetizers',
            'main_course' => 'Main Course',
            'desserts' => 'Desserts',
            'beverages' => 'Beverages',
            'breakfast' => 'Breakfast',
            'lunch' => 'Lunch',
            'dinner' => 'Dinner',
            'snacks' => 'Snacks',
            'salads' => 'Salads',
            'soups' => 'Soups',
        ];

        return view('admin.restaurants.recipes.create', compact('categories'));
    }

    /**
     * Store a newly created menu item
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'category' => 'required|string',
            'prep_time' => 'nullable|integer|min:0',
            'selling_price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);
        
        try {
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('menu', 'public');
            }

            Recipe::create([
                'name' => $request->name,
                'description' => $request->description,
                'category' => $request->category,
                'prep_time' => $request->prep_time,
                'selling_price' => $request->selling_price,
                'is_available' => $request->has('is_available'),
                'image' => $imagePath,
                'created_by' => Auth::guard('staff')->id(),
            ]);

            return redirect()->route('admin.recipes.index')->with('success', 'Menu item created successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error creating menu item: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the menu item details
     */
    public function show(Recipe $recipe)
    {
        $recipe->load('creator');
        return view('admin.restaurants.recipes.show', compact('recipe'));
    }

    /**
     * Show the form for editing the menu item
     */
    public function edit(Recipe $recipe)
    {
        $categories = [
            'appetizers' => 'Appetizers',
            'main_course' => 'Main Course',
            'desserts' => 'Desserts',
            'beverages' => 'Beverages',
            'breakfast' => 'Breakfast',
            'lunch' => 'Lunch',
            'dinner' => 'Dinner',
            'snacks' => 'Snacks',
            'salads' => 'Salads',
            'soups' => 'Soups',
        ];

        return view('admin.restaurants.recipes.edit', compact('recipe', 'categories'));
    }

    /**
     * Update the menu item
     */
    public function update(Request $request, Recipe $recipe)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'category' => 'required|string',
            'prep_time' => 'nullable|integer|min:0',
            'selling_price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        try {
            $recipe->update([
                'name' => $request->name,
                'description' => $request->description,
                'category' => $request->category,
                'prep_time' => $request->prep_time,
                'selling_price' => $request->selling_price,
                'is_available' => $request->has('is_available'),
            ]);

            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($recipe->image && \Storage::disk('public')->exists($recipe->image)) {
                    \Storage::disk('public')->delete($recipe->image);
                }
                $recipe->image = $request->file('image')->store('menu', 'public');
                $recipe->save();
            }

            return redirect()->route('admin.recipes.index')->with('success', 'Menu item updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error updating menu item: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Quick update for the selling price
     */
    public function updatePrice(Request $request, Recipe $recipe)
    {
        $request->validate([
            'selling_price' => 'required|numeric|min:0',
        ]);

        try {
            $recipe->update([
                'selling_price' => $request->selling_price,
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Price updated successfully.',
                    'new_price' => number_format($recipe->selling_price)
                ]);
            }

            return back()->with('success', 'Price updated successfully.');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating price: ' . $e->getMessage()
                ], 500);
            }
            return back()->with('error', 'Error updating price.');
        }
    }

    /**
     * Remove the menu item
     */
    public function destroy(Recipe $recipe)
    {
        try {
            // Delete image if exists
            if ($recipe->image && \Storage::disk('public')->exists($recipe->image)) {
                \Storage::disk('public')->delete($recipe->image);
            }
            $recipe->delete();
            return redirect()->route('admin.recipes.index')->with('success', 'Menu item deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting menu item: ' . $e->getMessage());
        }
    }
}
