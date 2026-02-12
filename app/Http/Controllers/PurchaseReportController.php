<?php

namespace App\Http\Controllers;

use App\Models\ShoppingList;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PurchaseReportController extends Controller
{
    /**
     * Show purchase reports
     */
    public function index(Request $request)
    {
        $query = ShoppingList::with('items')
            ->orderBy('created_at', 'desc');
        
        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        $shoppingLists = $query->paginate(20)->appends($request->query());
        
        // Calculate overall statistics
        $totalLists = ShoppingList::count();
        $completedLists = ShoppingList::where('status', 'completed')->count();
        $totalBudget = ShoppingList::whereNotNull('budget_amount')->sum('budget_amount');
        $totalSpent = ShoppingList::whereNotNull('amount_used')->sum('amount_used');
        $totalRemaining = $totalBudget - $totalSpent;
        
        $stats = [
            'total_lists' => $totalLists,
            'completed_lists' => $completedLists,
            'total_budget' => $totalBudget,
            'total_spent' => $totalSpent,
            'total_remaining' => $totalRemaining,
        ];
        
        return view('dashboard.purchase-reports', compact('shoppingLists', 'stats'));
    }

    /**
     * Show detailed purchase report for a specific shopping list
     */
    public function show(ShoppingList $shoppingList)
    {
        $shoppingList->load('items');
        
        // Calculate item statistics
        $foundItems = $shoppingList->items->where('is_found', true)->count();
        $missingItems = $shoppingList->items->where('is_found', false)->count();
        $totalItems = $shoppingList->items->count();
        
        $itemStats = [
            'found' => $foundItems,
            'missing' => $missingItems,
            'total' => $totalItems,
        ];
        
        return view('dashboard.purchase-report-detail', compact('shoppingList', 'itemStats'));
    }
}
