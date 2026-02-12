<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{
    /**
     * Display a listing of suppliers
     */
    public function index()
    {
        $user = Auth::guard('staff')->user();
        $role = strtolower($user->role ?? 'manager');
        
        $suppliers = Supplier::orderBy('name')->paginate(20);
        
        return view('dashboard.suppliers-list', compact('suppliers', 'role'));
    }

    /**
     * Show the form for creating a new supplier
     */
    public function create()
    {
        $user = Auth::guard('staff')->user();
        $role = strtolower($user->role ?? 'manager');
        
        return view('dashboard.supplier-form', compact('role'));
    }

    /**
     * Store a newly created supplier
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $supplier = Supplier::create($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Supplier created successfully!',
                'supplier' => $supplier,
            ]);
        }

        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Supplier created successfully!');
    }

    /**
     * Show the form for editing the specified supplier
     */
    public function edit(Supplier $supplier)
    {
        $user = Auth::guard('staff')->user();
        $role = strtolower($user->role ?? 'manager');
        
        return view('dashboard.supplier-form', compact('supplier', 'role'));
    }

    /**
     * Update the specified supplier
     */
    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $supplier->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Supplier updated successfully!',
                'supplier' => $supplier,
            ]);
        }

        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Supplier updated successfully!');
    }

    /**
     * Remove the specified supplier
     */
    public function destroy(Supplier $supplier)
    {
        // Check if supplier has products
        if ($supplier->products()->count() > 0) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete supplier with existing products. Please remove products first.',
                ], 422);
            }
            return redirect()->route('admin.suppliers.index')
                ->with('error', 'Cannot delete supplier with existing products.');
        }

        $supplier->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Supplier deleted successfully!',
            ]);
        }

        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Supplier deleted successfully!');
    }
}
