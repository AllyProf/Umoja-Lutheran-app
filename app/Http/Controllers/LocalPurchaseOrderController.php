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
        $role = Auth::guard('staff')->user()->role;
        $query = LocalPurchaseOrder::with(['storekeeper', 'accountant', 'manager']);

        if ($role === 'accountant') {
            $query->whereIn('status', ['sent_to_accountant', 'sent_to_manager', 'verified_by_manager', 'closed']);
        } elseif ($role === 'manager' || $role === 'super_admin') {
            $query->whereIn('status', ['sent_to_manager', 'verified_by_manager', 'closed']);
        }

        $orders = $query->latest()->paginate(20);
        return view('dashboard.lpo.index', compact('orders', 'role'));
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
        if ($lpo->status !== 'pending') {
            return redirect()->route('lpo.show', $lpo->id)->with('error', 'Only pending LPOs can be edited.');
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

        return view('dashboard.lpo.edit', compact('lpo', 'products'));
    }

    public function update(Request $request, LocalPurchaseOrder $lpo)
    {
        if ($lpo->status !== 'pending') {
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
                $lpo->items()->create([
                    'item_name' => $itemData['item_name'],
                    'product_variant_id' => $itemData['product_variant_id'] ?? null,
                    'quantity' => $itemData['quantity'],
                    'unit' => $itemData['unit'] ?? null,
                    'unit_price' => $itemData['unit_price'],
                    'total_price' => $totalPrice,
                ]);
                $totalAmount += $totalPrice;
            }

            $lpo->update([
                'total_amount' => $totalAmount,
                'notes' => $request->notes ?? $lpo->notes,
            ]);

            DB::commit();
            return redirect()->route('lpo.show', $lpo->id)->with('success', 'LPO Budget items updated.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function sendToAccountant(LocalPurchaseOrder $lpo)
    {
        $lpo->update(['status' => 'sent_to_accountant']);
        return redirect()->route('lpo.index')->with('success', 'LPO sent to accountant.');
    }

    public function sendToManager(LocalPurchaseOrder $lpo)
    {
        $lpo->update([
            'status' => 'sent_to_manager',
            'accountant_id' => Auth::guard('staff')->id(),
        ]);
        return redirect()->route('lpo.index')->with('success', 'LPO sent to manager.');
    }

    public function managerVerify(Request $request, LocalPurchaseOrder $lpo)
    {
        $lpo->update([
            'status' => 'verified_by_manager',
            'manager_id' => Auth::guard('staff')->id(),
            'notes' => $lpo->notes . ' | VERIFIED: ' . $request->manager_notes,
        ]);
        return redirect()->route('lpo.index')->with('success', 'LPO verified.');
    }

    public function close(LocalPurchaseOrder $lpo)
    {
        $lpo->update(['status' => 'closed']);
        return redirect()->route('lpo.index')->with('success', 'LPO closed.');
    }

    public function receiveForm(LocalPurchaseOrder $lpo)
    {
        if (!in_array($lpo->status, ['verified_by_manager', 'closed'])) {
            return back()->with('error', 'LPO must be verified first.');
        }
        $lpo->load(['items.productVariant.product']);
        $products = Product::active()->orderBy('name')->get();
        return view('dashboard.lpo.receive', compact('lpo', 'products'));
    }

    public function processReceive(Request $request, LocalPurchaseOrder $lpo)
    {
        DB::beginTransaction();
        try {
            $receivedById = Auth::guard('staff')->id();
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
            }
            DB::commit();
            return redirect()->route('lpo.show', $lpo->id)->with('success', 'Items received.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
