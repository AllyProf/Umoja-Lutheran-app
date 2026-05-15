<?php

namespace App\Http\Controllers;

use App\Models\ShoppingList;
use App\Models\ShoppingListItem;
use App\Models\PurchaseRequest;
use App\Models\StockReceipt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AccountantController extends Controller
{
    /**
     * Accountant Dashboard
     */
    public function dashboard()
    {
        $stats = [
            'pending_approval' => ShoppingList::where('status', 'pending')->count(),
            'checked_lists' => ShoppingList::where('status', 'accountant_checked')->count(),
            'approved_lists' => ShoppingList::where('status', 'approved')->count(),
            'ready_lists' => ShoppingList::where('status', 'ready_for_purchase')->count(),
            'purchased_lists' => ShoppingList::where('status', 'purchased')->count(),
            'total_spent' => ShoppingList::where('status', 'completed')->sum('total_actual_cost'),
            'total_day_services_revenue' => \App\Models\DayService::where('payment_status', 'paid')
                ->whereDate('service_date', now()->toDateString())
                ->sum('amount_paid'),
            'pending_cashier_shifts' => \App\Models\ShiftClosure::where('status', 'pending_accountant')->count(),
            'pending_cashier_revenue' => \App\Models\DayService::where('payment_status', 'paid')
                ->whereNotNull('cashier_collected_at')
                ->whereNull('accountant_verified_at')
                ->count(),
        ];

        // Shopping lists awaiting accountant approval
        $pendingLists = ShoppingList::with('items')
            ->where('status', 'pending')
            ->latest()
            ->limit(5)
            ->get();

        // Recently approved & completed
        $recentLists = ShoppingList::with('items')
            ->whereIn('status', ['approved', 'ready_for_purchase', 'completed'])
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard.accountant.dashboard', compact('stats', 'pendingLists', 'recentLists'));
    }

    /**
     * Shopping list approvals — the core accountant duty
     */
    public function shoppingLists(Request $request)
    {
        $tab = $request->get('tab', 'pending');

        $query = ShoppingList::with('items');

        if ($tab === 'pending') {
            $query->where('status', 'pending');
        } elseif ($tab === 'approved') {
            // Include accountant_checked (sent to manager) and approved (manager approved, awaiting disbursement)
            $query->whereIn('status', ['accountant_checked', 'approved']);
        } elseif ($tab === 'purchased') {
            // Include lists that are ready for purchase or already completed/purchased
            $query->whereIn('status', ['ready_for_purchase', 'purchased', 'completed']);
        }

        $lists = $query->latest()->paginate(20);

        return view('dashboard.accountant.shopping-lists', compact('lists', 'tab'));
    }

    /**
     * Show a single shopping list for review
     */
    public function showShoppingList(ShoppingList $shoppingList)
    {
        $shoppingList->load('items');
        return view('dashboard.accountant.shopping-list-show', compact('shoppingList'));
    }

    /**
     * Approve a shopping list
     */
    public function approveShoppingList(Request $request, ShoppingList $shoppingList)
    {
        $request->validate([
            'budget_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $shoppingList->update([
            'status' => 'accountant_checked',
            'budget_amount' => $request->budget_amount,
            'notes' => $request->notes ?? $shoppingList->notes,
        ]);

        return back()->with('success', 'Shopping list approved with budget of TZS ' . number_format($request->budget_amount));
    }

    /**
     * Disburse funds for an approved shopping list
     */
    public function disburseFunds(Request $request, ShoppingList $shoppingList)
    {
        if ($shoppingList->status !== 'approved') {
            return back()->with('error', 'Only approved lists can have funds disbursed.');
        }
        if ($request->has('direct_purchase')) {
            $shoppingList->update([
                'status' => 'ready_for_purchase',
                'notes' => $shoppingList->notes . ' | READY FOR DIRECT PURCHASE by Accountant on ' . now()->format('d M Y H:i'),
            ]);
            return back()->with('success', 'List is now ready for direct purchase. Please proceed to record purchases.');
        }

        $shoppingList->update([
            'status' => 'ready_for_purchase',
            'notes' => $shoppingList->notes . ' | FUNDS DISBURSED by Accountant on ' . now()->format('d M Y H:i'),
        ]);

        return back()->with('success', 'Funds disbursed. The Storekeeper can now proceed with the purchase.');
    }

    /**
     * Reject a shopping list
     */
    public function rejectShoppingList(Request $request, ShoppingList $shoppingList)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $shoppingList->update([
            'status' => 'rejected',
            'notes' => 'REJECTED: ' . $request->rejection_reason,
        ]);

        return back()->with('success', 'Shopping list rejected.');
    }

    /**
     * Purchase Payment Verification
     * Lists all shopping lists that have been purchased and need payment verified
     */
    public function paymentVerification(Request $request)
    {
        $tab = $request->get('tab', 'unverified');

        $query = ShoppingList::with('items')->where('status', 'purchased');

        if ($tab === 'unverified') {
            // Not yet verified — no payment_verified_at (we track this with a notes check or a dedicated field)
            // Since there's no payment_verified field, we use a status marker: 'purchased' = unverified, 'completed' = verified
            $query->where('status', 'purchased');
        } elseif ($tab === 'verified') {
            $query = ShoppingList::with('items')->where('status', 'completed');
        }

        $lists = $query->latest()->paginate(20);

        return view('dashboard.accountant.payment-verification', compact('lists', 'tab'));
    }

    /**
     * Mark payment as verified for a shopping list
     */
    public function verifyPayment(Request $request, ShoppingList $shoppingList)
    {
        // Strip commas from numeric input
        $actualCost = $request->actual_cost;
        if (is_string($actualCost)) {
            $actualCost = (float) str_replace(',', '', $actualCost);
        }

        $shoppingList->update([
            'status' => 'completed',
            'total_actual_cost' => $actualCost,
            'amount_used' => $actualCost,
            'amount_remaining' => max(0, ($shoppingList->budget_amount ?? 0) - $actualCost),
            'notes' => $shoppingList->notes . ' | PAYMENT VERIFIED by Accountant: ' . ($request->payment_notes ?? ''),
        ]);

        // NOTE: Stock is already tracked via shopping_list_items (purchased_quantity / received_quantity_kg).
        // The StorekeeperController sums those fields directly for inventory levels.
        // Creating StockReceipt records here would cause DOUBLE-COUNTING in the Main Store.

        return back()->with('success', 'Payment of TZS ' . number_format($request->actual_cost) . ' verified successfully.');
    }

    /**
     * Financial summary report
     */
    public function reports(Request $request)
    {
        $fromDate = $request->get('from', now()->startOfMonth()->toDateString());
        $toDate = $request->get('to', now()->toDateString());

        $summary = [
            'total_approved_budget' => ShoppingList::whereIn('status', ['approved', 'on_list', 'purchased', 'completed'])
                ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
                ->sum('budget_amount'),
            'total_spent' => ShoppingList::whereIn('status', ['purchased', 'completed'])
                ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
                ->sum('total_actual_cost'),
            'total_verified' => ShoppingList::where('status', 'completed')
                ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
                ->sum('total_actual_cost'),
            'lists_count' => ShoppingList::whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])->count(),
        ];

        $lists = ShoppingList::with('items')
            ->whereBetween('created_at', [$fromDate . ' 00:00:00', $toDate . ' 23:59:59'])
            ->latest()
            ->paginate(20);

        return view('dashboard.accountant.reports', compact('summary', 'lists', 'fromDate', 'toDate'));
    }

    /**
     * Day Services Revenue Dashboard for Accountant
     * Shows daily grouped revenue from reception that needs verification
     */
    public function dayServicesRevenue(Request $request)
    {
        $tab = $request->get('tab', 'unverified');

        // Build the base query: Group by date
        $query = \App\Models\DayService::select(
            'service_date',
            DB::raw('COUNT(id) as total_services'),
            DB::raw('SUM(CASE WHEN payment_status = "paid" THEN amount_paid ELSE 0 END) as total_revenue'),
            DB::raw('SUM(CASE WHEN payment_status != "paid" THEN amount ELSE 0 END) as pending_revenue'),
            DB::raw('MAX(accountant_verified_at) as verified_at'), // Will be null if any are unverified
            DB::raw('SUM(CASE WHEN payment_status = "paid" AND accountant_verified_at IS NULL THEN 1 ELSE 0 END) as unverified_count')
        )
            ->groupBy('service_date')
            ->orderByDesc('service_date');

        if ($tab === 'unverified') {
            // Show days that have at least one paid but unverified service
            $query->havingRaw('unverified_count > 0');
        } elseif ($tab === 'verified') {
            // Show days where all paid services have been verified (unverified_count = 0) AND there is at least some revenue
            $query->havingRaw('unverified_count = 0')->havingRaw('total_revenue > 0');
        }

        $dailyRevenues = $query->paginate(15);

        // Overall stats
        $stats = [
            'unverified_days' => \App\Models\DayService::select('service_date')
                ->where('payment_status', 'paid')
                ->whereNull('accountant_verified_at')
                ->groupBy('service_date')
                ->get()
                ->count(),
            'total_unverified_cash' => \App\Models\DayService::where('payment_status', 'paid')
                ->whereNull('accountant_verified_at')
                ->sum('amount_paid'),
        ];

        return view('dashboard.accountant.day-services-revenue', compact('dailyRevenues', 'tab', 'stats'));
    }

    /**
     * Verify all Day Services revenue for a specific date
     */
    public function verifyDayServicesRevenue(Request $request)
    {
        $request->validate([
            'service_date' => 'required|date',
        ]);

        $date = $request->service_date;

        // Find all paid, unverified services for this date
        $affectedRows = \App\Models\DayService::where('service_date', $date)
            ->where('payment_status', 'paid')
            ->whereNull('accountant_verified_at')
            ->update([
                'accountant_verified_at' => now(),
                'accountant_id' => Auth::guard('staff')->id(),
            ]);

        if ($affectedRows > 0) {
            return back()->with('success', "Revenue for " . \Carbon\Carbon::parse($date)->format('M d, Y') . " verified successfully. ($affectedRows services marked as received)");
        }

        return back()->with('info', "No unverified paid services found for " . \Carbon\Carbon::parse($date)->format('M d, Y') . ".");
    }
    /**
     * Claim a Shopping List for Direct Purchase
     */
    public function claimPurchase(Request $request, ShoppingList $shoppingList)
    {
        // Set the purchaser as the current accountant
        $staffId = Auth::guard('staff')->id();

        $shoppingList->update([
            'purchaser_id' => $staffId,
        ]);

        return back()->with('success', 'You have claimed this shopping list for direct purchase. Please proceed with the financial review if it is pending.');
    }

    public function recordPurchaseView(ShoppingList $shoppingList)
    {
        if ($shoppingList->status !== 'ready_for_purchase') {
            return redirect()->back()->with('info', 'This list is not yet ready for purchase (awaiting payment disbursement) or is already completed.');
        }

        // Ensure this list was claimed by this accountant
        if ($shoppingList->purchaser_id !== Auth::guard('staff')->id()) {
            return redirect()->back()->with('error', 'You can only record purchases for lists you have claimed.');
        }

        $shoppingList->load(['items.product', 'items.productVariant']);
        return view('admin.restaurants.shopping_list.record_purchase', compact('shoppingList'));
    }

    public function updatePurchase(Request $request, ShoppingList $shoppingList)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.purchased_quantity' => 'nullable|numeric|min:0',
            'items.*.purchased_cost' => 'nullable|numeric|min:0',
            'items.*.expiry_date' => 'nullable|date',
            'items.*.unit_price' => 'nullable|numeric|min:0',
            'items.*.storage_location' => 'nullable|string|max:255',
            'items.*.is_found' => 'nullable|boolean',
            'budget_amount' => 'nullable|numeric|min:0',
            'items.*.servings_per_pic' => 'nullable|integer|min:1',
            'items.*.selling_unit' => 'nullable|in:pic,glass,tot,shot,cocktail',
            'items.*.selling_price_per_pic' => 'nullable|numeric|min:0',
            'items.*.selling_price_per_serving' => 'nullable|numeric|min:0',
            'items.*.price_adjustment_reason' => 'nullable|string',
            'items.*.received_quantity_kg' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $cleanNumeric = function ($value) {
                if (is_null($value))
                    return "0";
                return str_replace(',', '', (string) $value);
            };

            $totalCost = 0;
            foreach ($request->items as $itemId => $data) {
                $item = ShoppingListItem::findOrFail($itemId);
                $boughtQty = round($cleanNumeric($data['purchased_quantity'] ?? 0));
                $cost = round($cleanNumeric($data['purchased_cost'] ?? 0));
                $expiryDate = $data['expiry_date'] ?? null;
                $unitPrice = isset($data['unit_price']) ? $cleanNumeric($data['unit_price']) : null;

                $isFound = isset($data['is_found']) && $data['is_found'] == '1';

                if ($unitPrice && !$cost && $boughtQty > 0) {
                    $cost = $unitPrice * $boughtQty;
                }

                $receivedKg = isset($data['received_quantity_kg']) ? (float) $cleanNumeric($data['received_quantity_kg']) : 0;
                $foodCategories = ['food', 'meat_poultry', 'seafood', 'vegetables', 'dairy', 'pantry_baking', 'spices_herbs', 'oils_fats', 'kitchen', 'snacks'];
                $isFood = in_array($item->category, $foodCategories);

                $updateData = [
                    'purchased_quantity' => ($isFood && $receivedKg > 0 && $boughtQty <= 0) ? 1 : $boughtQty,
                    'purchased_cost' => $cost,
                    'expiry_date' => $expiryDate,
                    'is_purchased' => $isFound && ($boughtQty > 0 || ($isFood && $receivedKg > 0)),
                    'is_found' => $isFound,
                    'received_quantity_kg' => $receivedKg > 0 ? $receivedKg : null
                ];

                $finalUnitPrice = $unitPrice;
                if (in_array($item->category, $foodCategories) && $receivedKg > 0 && $cost > 0) {
                    $finalUnitPrice = $cost / $receivedKg;
                } elseif ($unitPrice) {
                    $finalUnitPrice = $unitPrice;
                } elseif ($boughtQty > 0 && $cost > 0) {
                    $finalUnitPrice = $cost / $boughtQty;
                }

                if ($finalUnitPrice !== null) {
                    $updateData['unit_price'] = $finalUnitPrice;
                }

                $item->update($updateData);

                if ($item->purchaseRequest && $updateData['is_purchased']) {
                    $item->purchaseRequest->update(['status' => 'purchased']);
                }

                if ($isFound && $boughtQty > 0) {
                    $totalCost += $cost;
                }
            }

            $budgetAmount = $request->budget_amount ? $cleanNumeric($request->budget_amount) : ($shoppingList->budget_amount ?? $shoppingList->total_estimated_cost ?? $shoppingList->items->sum('estimated_price'));
            $amountUsed = $totalCost;
            $amountRemaining = $budgetAmount - $amountUsed;

            $shoppingList->total_actual_cost = (float) $totalCost;
            $shoppingList->budget_amount = (float) $budgetAmount;
            $shoppingList->amount_used = (float) $amountUsed;
            $shoppingList->amount_remaining = (float) $amountRemaining;

            if ($request->has('market_name')) {
                $shoppingList->market_name = $request->market_name;
            }

            if ($request->has('finalize')) {
                $shoppingList->status = 'purchased';
            }

            $shoppingList->save();
            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $request->has('finalize') ? 'Purchases recorded and submitted for verification.' : 'Progress saved successfully.',
                    'redirect_url' => route('accountant.shopping-lists'),
                    'report_url' => null, // Accountant usually doesn't need to print the storekeeper's report instantly, or they can view it from list
                    'finalize' => $request->has('finalize')
                ]);
            }

            if ($request->has('finalize')) {
                return redirect()->route('accountant.shopping-lists')->with('success', 'Purchases recorded and finalized. The storekeeper can now transfer the items.');
            }

            return back()->with('success', 'Progress saved.');

        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating purchase: ' . $e->getMessage()
                ], 422);
            }
            return back()->with('error', 'Error updating purchase: ' . $e->getMessage());
        }
    }

    /**
     * View all collections from Cashier awaiting Accountant verification
     */
    public function cashierCollections(Request $request)
    {
        $tab = $request->get('tab', 'pending');

        $shifts = \App\Models\ShiftClosure::with(['staff', 'receiver'])
            ->where('status', $tab === 'pending' ? 'pending_accountant' : 'finalized')
            ->orderByDesc('updated_at')
            ->paginate(15, ['*'], 'shifts_page');

        // 1. Day Service Revenue by Date
        $dsQuery = \App\Models\DayService::select(
            'service_date as date',
            DB::raw('COUNT(id) as ds_count'),
            DB::raw('SUM(amount_paid) as ds_revenue')
        )
        ->where('payment_status', 'paid')
        ->whereNotNull('cashier_collected_at');

        if ($tab === 'pending') {
            $dsQuery->whereNull('accountant_verified_at');
        } else {
            $dsQuery->whereNotNull('accountant_verified_at');
        }

        $dsDaily = $dsQuery->groupBy('service_date')->get()->keyBy('date');

        // 2. Booking Revenue by Date
        $bkQuery = \App\Models\Booking::select(
            DB::raw('DATE(paid_at) as date'),
            DB::raw('COUNT(id) as bk_count'),
            DB::raw('SUM(amount_paid) as bk_revenue')
        )
        ->where('payment_status', 'paid')
        ->whereNotNull('cashier_collected_at');

        if ($tab === 'pending') {
            $bkQuery->whereNull('accountant_verified_at');
        } else {
            $bkQuery->whereNotNull('accountant_verified_at');
        }

        $bkDaily = $bkQuery->groupBy(DB::raw('DATE(paid_at)'))->get()->keyBy('date');

        // 3. Merge All Dates
        $allDates = $dsDaily->keys()->merge($bkDaily->keys())->unique()->sortDesc();

        $mergedDaily = $allDates->map(function($date) use ($dsDaily, $bkDaily) {
            $ds = $dsDaily->get($date);
            $bk = $bkDaily->get($date);

            return (object) [
                'date' => $date,
                'ds_count' => $ds->ds_count ?? 0,
                'ds_revenue' => $ds->ds_revenue ?? 0,
                'bk_count' => $bk->bk_count ?? 0,
                'bk_revenue' => $bk->bk_revenue ?? 0,
                'total_revenue' => ($ds->ds_revenue ?? 0) + ($bk->bk_revenue ?? 0),
            ];
        });

        // 4. Pagination
        $perPage = 15;
        $page = $request->get('days_page', 1);
        $dayServices = new \Illuminate\Pagination\LengthAwarePaginator(
            $mergedDaily->forPage($page, $perPage),
            $mergedDaily->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query(), 'pageName' => 'days_page']
        );

        return view('dashboard.accountant.cashier-collections', compact('shifts', 'dayServices', 'tab'));
    }

    /**
     * Acknowledge and finalize cash received from Cashier
     */
    public function acknowledgeCashierShift(\App\Models\ShiftClosure $shiftClosure)
    {
        $shiftClosure->update([
            'status' => 'finalized',
            'receiver_id' => Auth::guard('staff')->id(),
            'notes' => $shiftClosure->notes . "\n[Finalized and Received by Accountant at " . now() . "]"
        ]);

        return back()->with('success', 'Cashier shift funds successfully received and finalized.');
    }

    /**
     * Final verify and collect day revenue from Cashier
     */
    public function verifyCashierDayRevenue(Request $request)
    {
        $request->validate([
            'service_date' => 'required|date',
        ]);

        $date = $request->service_date;
        $staffId = Auth::guard('staff')->id();

        // 1. Verify Day Services
        $dsAffected = \App\Models\DayService::where('service_date', $date)
            ->where('payment_status', 'paid')
            ->whereNotNull('cashier_collected_at')
            ->whereNull('accountant_verified_at')
            ->update([
                'accountant_verified_at' => now(),
                'accountant_id' => $staffId,
            ]);

        // 2. Verify Bookings
        $bkAffected = \App\Models\Booking::whereDate('paid_at', $date)
            ->where('payment_status', 'paid')
            ->whereNotNull('cashier_collected_at')
            ->whereNull('accountant_verified_at')
            ->update([
                'accountant_verified_at' => now(),
                'accountant_id' => $staffId,
            ]);

        if ($dsAffected > 0 || $bkAffected > 0) {
            $msg = "Revenue for " . \Carbon\Carbon::parse($date)->format('M d, Y') . " officially verified.";
            if ($dsAffected > 0) $msg .= " ($dsAffected Day Services)";
            if ($bkAffected > 0) $msg .= " ($bkAffected Bookings)";
            return back()->with('success', $msg . " Received from Cashier.");
        }

        return back()->with('info', "No unverified cashier collections found for " . \Carbon\Carbon::parse($date)->format('M d, Y') . ".");
    }
}
