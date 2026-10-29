<?php

namespace App\Http\Controllers;

use App\Models\LocalPurchaseOrder;
use App\Models\LocalPurchaseOrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockReceipt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LocalPurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        $role = strtolower(Auth::guard('staff')->user()->role ?? '');
        $query = LocalPurchaseOrder::with(['storekeeper', 'accountant', 'manager']);
        $tab = $request->get('tab', in_array($role, ['accountant', 'manager', 'super_admin'], true) ? 'review' : 'all');

        if ($role === 'accountant') {
            if ($tab === 'review') {
                $query->where('status', 'sent_to_accountant');
            } elseif ($tab === 'forwarded') {
                $query->whereIn('status', ['sent_to_manager', 'verified_by_manager', 'funds_disbursed', 'closed']);
            } else {
                $query->whereIn('status', ['sent_to_accountant', 'sent_to_manager', 'verified_by_manager', 'funds_disbursed', 'closed']);
            }
            $pendingReviewCount = LocalPurchaseOrder::where('status', 'sent_to_accountant')->count();
        } elseif (in_array($role, ['manager', 'super_admin'], true)) {
            if ($tab === 'review') {
                $query->where('status', 'sent_to_manager');
            } elseif ($tab === 'verified') {
                $query->whereIn('status', ['verified_by_manager', 'funds_disbursed', 'closed']);
            } else {
                $query->whereIn('status', ['sent_to_manager', 'verified_by_manager', 'funds_disbursed', 'closed']);
            }
            $pendingReviewCount = LocalPurchaseOrder::where('status', 'sent_to_manager')->count();
        } else {
            $pendingReviewCount = 0;
        }

        $orders = $query->latest()->paginate(20)->withQueryString();

        return view('dashboard.lpo.index', compact('orders', 'role', 'tab', 'pendingReviewCount'));
    }

    /**
     * JSON count of LPOs waiting for the current role's action (sidebar badge).
     */
    public function pendingCount()
    {
        $role = strtolower(Auth::guard('staff')->user()->role ?? '');

        $count = match (true) {
            in_array($role, ['manager', 'super_admin'], true)
                => LocalPurchaseOrder::where('status', 'sent_to_manager')->count(),
            $role === 'accountant'
                => LocalPurchaseOrder::where('status', 'sent_to_accountant')->count(),
            default => 0,
        };

        return response()->json(['count' => $count]);
    }

    public function create()
    {
        return view('dashboard.lpo.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'notes' => 'nullable|string',
        ]);

        $startDate = \Carbon\Carbon::parse($request->start_date);
        $endDate = \Carbon\Carbon::parse($request->end_date);
        $diffInDays = $startDate->diffInDays($endDate);

        if ($diffInDays > 14) {
            return back()->withInput()->with('error', 'LPO Budget cannot exceed 14 days (2 weeks).');
        }

        $lpo = LocalPurchaseOrder::create([
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'notes' => $request->notes,
            'storekeeper_id' => Auth::guard('staff')->id(),
            'status' => 'pending',
        ]);

        return redirect()->route('lpo.edit', $lpo->id)->with('success', 'LPO Budget created. Now add items.');
    }

    public function show(LocalPurchaseOrder $lpo)
    {
        $lpo->load(['storekeeper', 'accountant', 'manager', 'items.productVariant.product', 'supplierOrders.supplier', 'shoppingLists']);
        return view('dashboard.lpo.show', compact('lpo'));
    }

    public function edit(LocalPurchaseOrder $lpo)
    {
        $role = strtolower(Auth::guard('staff')->user()->role ?? '');
        if (!$this->canEditLpo($lpo, $role)) {
            return redirect()->route('lpo.show', $lpo->id)
                ->with('error', 'You cannot edit this LPO in its current status.');
        }

        $lpo->load('items');
        $products = Product::active()->with('variants')->orderBy('name')->get();

        // Pre-calculate latest cost and unit for frontend use
        $products->each(function ($p) {
            $p->variants->each(function ($v) {
                $v->latest_cost = $v->getLatestUnitCost();
                $v->primary_unit = $v->receiving_unit ?: ($v->purchasing_unit ?: '');
            });
        });

        return view('dashboard.lpo.edit', compact('lpo', 'products', 'role'));
    }

    public function update(Request $request, LocalPurchaseOrder $lpo)
    {
        $role = strtolower(Auth::guard('staff')->user()->role ?? '');
        if (!$this->canEditLpo($lpo, $role)) {
            return back()->with('error', 'Cannot update LPO in current status.');
        }

        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $lpo->items()->delete();

            $totalAmount = 0;
            foreach ($request->items as $itemData) {
                if ((float) $itemData['quantity'] <= 0 && (float) $itemData['unit_price'] <= 0)
                    continue;

                $totalPrice = $itemData['quantity'] * $itemData['unit_price'];
                $itemName = preg_replace('/\s*[\(\-·]\s*standard\s*\)?\s*$/i', '', (string) ($itemData['item_name'] ?? ''));
                $itemName = trim(preg_replace('/\s+/', ' ', $itemName));

                $lpo->items()->create([
                    'item_name' => $itemName,
                    'product_variant_id' => $itemData['product_variant_id'] ?? null,
                    'quantity' => $itemData['quantity'],
                    'unit' => $itemData['unit'] ?? null,
                    'unit_price' => $itemData['unit_price'],
                    'total_price' => $totalPrice,
                ]);
                $totalAmount += $totalPrice;
            }

            $updates = [
                'total_amount' => $totalAmount,
                'notes' => $request->notes ?? $lpo->notes,
            ];

            // Accountant adjustment keeps status; storekeeper editing a rejected LPO clears rejection text
            if (in_array($lpo->status, ['rejected', 'pending'], true) && in_array($role, ['storekeeper', 'super_admin'], true)) {
                $updates['rejection_reason'] = null;
            }

            $lpo->update($updates);

            DB::commit();
            $msg = $role === 'accountant'
                ? 'Budget adjusted. You can now Accept or Reject.'
                : 'LPO Budget items updated.';
            return redirect()->route('lpo.show', $lpo->id)->with('success', $msg);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function sendToAccountant(LocalPurchaseOrder $lpo)
    {
        if (!in_array($lpo->status, ['pending', 'rejected'], true)) {
            return back()->with('error', 'Only pending or rejected LPOs can be sent to the accountant.');
        }

        if ($lpo->items()->count() === 0) {
            return back()->with('error', 'Add budget items before sending to the accountant.');
        }

        $lpo->update([
            'status' => 'sent_to_accountant',
            'rejection_reason' => null,
        ]);
        return redirect()->route('lpo.index')->with('success', 'LPO sent to accountant.');
    }

    /**
     * Accountant accepts the budget and forwards it to the manager.
     */
    public function sendToManager(LocalPurchaseOrder $lpo)
    {
        if ($lpo->status !== 'sent_to_accountant') {
            return back()->with('error', 'Only LPOs awaiting accountant review can be accepted.');
        }

        $lpo->update([
            'status' => 'sent_to_manager',
            'accountant_id' => Auth::guard('staff')->id(),
            'rejection_reason' => null,
        ]);
        return redirect()->route('lpo.index')->with('success', 'Budget accepted and sent to manager.');
    }

    /**
     * Accountant rejects the budget — returns it to the storekeeper for revision.
     */
    public function rejectByAccountant(Request $request, LocalPurchaseOrder $lpo)
    {
        $role = strtolower(Auth::guard('staff')->user()->role ?? '');
        if (!in_array($role, ['accountant', 'super_admin', 'manager'], true)) {
            abort(403, 'Only accountants can reject LPO budgets.');
        }

        if ($lpo->status !== 'sent_to_accountant') {
            return back()->with('error', 'Only LPOs awaiting accountant review can be rejected.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:2000',
        ]);

        $lpo->update([
            'status' => 'rejected',
            'accountant_id' => Auth::guard('staff')->id(),
            'rejection_reason' => $request->rejection_reason,
            'notes' => trim(($lpo->notes ? $lpo->notes . "\n" : '') . 'Rejected by accountant: ' . $request->rejection_reason),
        ]);

        return redirect()->route('lpo.index')
            ->with('success', 'Budget rejected and returned to the storekeeper.');
    }

    protected function canEditLpo(LocalPurchaseOrder $lpo, string $role): bool
    {
        if (in_array($role, ['super_admin', 'manager'], true) && in_array($lpo->status, ['pending', 'rejected', 'sent_to_accountant'], true)) {
            return true;
        }

        if ($role === 'storekeeper' && in_array($lpo->status, ['pending', 'rejected'], true)) {
            return true;
        }

        if ($role === 'accountant' && $lpo->status === 'sent_to_accountant') {
            return true;
        }

        return false;
    }

    public function managerVerify(Request $request, LocalPurchaseOrder $lpo)
    {
        $lpo->update([
            'status' => 'verified_by_manager',
            'manager_id' => Auth::guard('staff')->id(),
            'notes' => $lpo->notes . ' | VERIFIED: ' . $request->manager_notes,
        ]);
        return redirect()->route('lpo.index')->with('success', 'LPO verified. Accountant can now pay the storekeeper.');
    }

    /**
     * Accountant pays / disburses the verified LPO budget to the storekeeper.
     */
    public function disburseFunds(LocalPurchaseOrder $lpo)
    {
        $role = strtolower(Auth::guard('staff')->user()->role ?? '');
        if (!in_array($role, ['accountant', 'super_admin', 'manager'], true)) {
            abort(403, 'Only accountants can disburse LPO funds.');
        }

        if ($lpo->status !== 'verified_by_manager') {
            return back()->with('error', 'Only manager-verified LPOs can be paid to the storekeeper.');
        }

        $lpo->update([
            'status' => 'funds_disbursed',
            'accountant_id' => $lpo->accountant_id ?: Auth::guard('staff')->id(),
            'notes' => trim(($lpo->notes ? $lpo->notes . "\n" : '') . 'FUNDS DISBURSED to storekeeper: ' . now()->format('d/m/Y H:i')),
        ]);

        return redirect()->route('accountant.pay-storekeeper')
            ->with('success', 'Funds disbursed for LPO #' . $lpo->id . '. Storekeeper can now purchase / receive.');
    }

    public function close(LocalPurchaseOrder $lpo)
    {
        if (!in_array($lpo->status, ['funds_disbursed', 'verified_by_manager'], true)) {
            return back()->with('error', 'Only paid or verified LPOs can be closed.');
        }

        $lpo->update(['status' => 'closed']);
        return redirect()->route('lpo.index')->with('success', 'LPO closed.');
    }

    public function receiveForm(LocalPurchaseOrder $lpo)
    {
        if (!in_array($lpo->status, ['funds_disbursed', 'closed'])) {
            return back()->with('error', 'Accountant must pay the storekeeper before items can be received.');
        }

        // Use the same Receive Stock page as the storekeeper (pre-select this LPO)
        $role = strtolower(Auth::guard('staff')->user()->role ?? '');
        $route = $role === 'storekeeper'
            ? 'storekeeper.stock-receipts.create'
            : 'admin.stock-receipts.create';

        return redirect()->route($route, [
            'source' => 'lpo',
            'lpo_id' => $lpo->id,
        ]);
    }

    public function processReceive(Request $request, LocalPurchaseOrder $lpo)
    {
        DB::beginTransaction();
        try {
            $receivedById = Auth::guard('staff')->id();
            $addedSpend = 0;
            foreach ($lpo->items as $item) {
                $receivedQty = (float) $request->input("items.{$item->id}.received_qty", 0);
                if ($receivedQty <= 0)
                    continue;

                $variantId = $item->product_variant_id;
                if ($variantId) {
                    StockReceipt::create([
                        'product_id' => $item->productVariant->product_id,
                        'product_variant_id' => $variantId,
                        'quantity_received_packages' => $receivedQty,
                        'buying_price_per_bottle' => $item->unit_price,
                        'selling_price_per_bottle' => $item->unit_price,
                        'received_date' => now()->toDateString(),
                        'received_by' => $receivedById,
                        'notes' => 'Auto-received from LPO #' . $lpo->id,
                    ]);
                }
                $item->increment('qty_received', $receivedQty);
                $addedSpend += $receivedQty * (float) $item->unit_price;
            }

            if ($addedSpend > 0) {
                $lpo->increment('spent_amount', $addedSpend);
            }

            DB::commit();
            return redirect()->route('lpo.show', $lpo->id)->with('success', 'Items received.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
