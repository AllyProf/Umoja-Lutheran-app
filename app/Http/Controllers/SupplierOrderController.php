<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockReceipt;
use App\Models\Supplier;
use App\Models\SupplierWeeklyOrder;
use App\Models\SupplierWeeklyOrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SupplierOrderController extends Controller
{
    public function index(Request $request)
    {
        $role = Auth::guard('staff')->user()->role;
        $query = SupplierWeeklyOrder::with(['supplier', 'storekeeper', 'accountant', 'manager']);

        if ($role === 'storekeeper') {
            // Storekeeper sees all
        } elseif ($role === 'accountant') {
            $query->whereIn('status', ['sent_to_accountant', 'sent_to_manager', 'verified_by_manager', 'closed']);
        } elseif ($role === 'manager' || $role === 'super_admin') {
            $query->whereIn('status', ['sent_to_manager', 'verified_by_manager', 'closed']);
        }

        $orders = $query->latest()->paginate(20);
        return view('dashboard.supplier-orders.index', compact('orders', 'role'));
    }

    public function create()
    {
        $suppliers = Supplier::where('is_active', true)->get();
        // Get active LPOs that are verified and not yet fully received/closed
        $lpos = \App\Models\LocalPurchaseOrder::whereIn('status', ['verified_by_manager', 'closed'])->latest()->get();
        return view('dashboard.supplier-orders.create', compact('suppliers', 'lpos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'lpo_id' => 'nullable|exists:local_purchase_orders,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'notes' => 'nullable|string',
        ]);

        $startDate = \Carbon\Carbon::parse($request->start_date);
        $endDate = \Carbon\Carbon::parse($request->end_date);
        $diffInDays = $startDate->diffInDays($endDate);

        if ($diffInDays > 7) {
            return back()->withInput()->with('error', 'Weekly batches cannot exceed 7 days (1 week).');
        }

        $order = SupplierWeeklyOrder::create([
            'supplier_id' => $request->supplier_id,
            'lpo_id' => $request->lpo_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'notes' => $request->notes,
            'storekeeper_id' => Auth::guard('staff')->id(),
            'status' => 'pending',
        ]);

        return redirect()->route('supplier-orders.edit', $order->id)->with('success', 'Weekly order batch created. Now add items.');
    }

    public function show(SupplierWeeklyOrder $supplierOrder)
    {
        $supplierOrder->load(['supplier', 'storekeeper', 'accountant', 'manager', 'items']);
        return view('dashboard.supplier-orders.show', compact('supplierOrder'));
    }

    public function edit(SupplierWeeklyOrder $supplierOrder)
    {
        if ($supplierOrder->status !== 'pending') {
            return redirect()->route('supplier-orders.show', $supplierOrder->id)->with('error', 'Only pending orders can be edited.');
        }
        $supplierOrder->load('items');
        $products = Product::active()->orderBy('name')->get();
        return view('dashboard.supplier-orders.edit', compact('supplierOrder', 'products'));
    }

    public function update(Request $request, SupplierWeeklyOrder $supplierOrder)
    {
        if ($supplierOrder->status !== 'pending') {
            return back()->with('error', 'Cannot update order in current status.');
        }

        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit' => 'nullable|string',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Remove old items and add new ones (simpler for this type of form)
            $supplierOrder->items()->delete();

            $totalAmount = 0;
            foreach ($request->items as $itemData) {
                $totalPrice = $itemData['quantity'] * $itemData['unit_price'];
                $variantId = $itemData['product_variant_id'] ?? null;

                $supplierOrder->items()->create([
                    'item_name' => $itemData['item_name'],
                    'product_variant_id' => $variantId,
                    'quantity' => $itemData['quantity'],
                    'unit' => $itemData['unit'] ?? null,
                    'unit_price' => $itemData['unit_price'],
                    'total_price' => $totalPrice,
                ]);
                $totalAmount += $totalPrice;
            }

            $supplierOrder->update([
                'total_amount' => $totalAmount,
                'notes' => $request->notes ?? $supplierOrder->notes,
            ]);

            DB::commit();
            return redirect()->route('supplier-orders.show', $supplierOrder->id)->with('success', 'Order items updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating order: ' . $e->getMessage());
        }
    }

    public function sendToAccountant(SupplierWeeklyOrder $supplierOrder)
    {
        if ($supplierOrder->status !== 'pending') {
            return back()->with('error', 'Only pending orders can be sent to accountant.');
        }

        if ($supplierOrder->items()->count() === 0) {
            return back()->with('error', 'Cannot send an empty order batch.');
        }

        $supplierOrder->update(['status' => 'sent_to_accountant']);
        return redirect()->route('supplier-orders.index')->with('success', 'Order sent to accountant for review.');
    }

    public function sendToManager(SupplierWeeklyOrder $supplierOrder)
    {
        if ($supplierOrder->status !== 'sent_to_accountant') {
            return back()->with('error', 'Only orders reviewed by accountant can be sent to manager.');
        }

        $supplierOrder->update([
            'status' => 'sent_to_manager',
            'accountant_id' => Auth::guard('staff')->id(),
        ]);
        return redirect()->route('supplier-orders.index')->with('success', 'Order sent to manager for verification.');
    }

    public function managerVerify(Request $request, SupplierWeeklyOrder $supplierOrder)
    {
        if ($supplierOrder->status !== 'sent_to_manager') {
            return back()->with('error', 'Order is not in manager review status.');
        }

        $supplierOrder->update([
            'status' => 'verified_by_manager',
            'manager_id' => Auth::guard('staff')->id(),
            'manager_notes' => $request->manager_notes,
        ]);
        return redirect()->route('supplier-orders.index')->with('success', 'Order verified and sent back to accountant.');
    }

    public function close(Request $request, SupplierWeeklyOrder $supplierOrder)
    {
        $role = Auth::guard('staff')->user()->role;
        if ($role !== 'accountant') {
            abort(403);
        }

        if ($supplierOrder->status !== 'verified_by_manager') {
            return back()->with('error', 'Order must be verified by manager before closing.');
        }

        $supplierOrder->update([
            'status' => 'closed',
            'notes' => $supplierOrder->notes . ' | CLOSED BY ACCOUNTANT: ' . ($request->closing_notes ?? ''),
        ]);

        return redirect()->route('supplier-orders.index')->with('success', 'Order marked as closed/settled.');
    }

    /**
     * Show the form for the storekeeper to receive items into inventory.
     */
    public function receiveForm(SupplierWeeklyOrder $supplierOrder)
    {
        $role = Auth::guard('staff')->user()->role;
        if (!in_array($role, ['storekeeper', 'super_admin'])) {
            abort(403);
        }

        if (!in_array($supplierOrder->status, ['verified_by_manager', 'closed'])) {
            return back()->with('error', 'Order must be verified by the manager before items can be received.');
        }

        if ($supplierOrder->items->every(fn($item) => $item->qty_received >= $item->quantity)) {
            return redirect()->route('supplier-orders.show', $supplierOrder->id)->with('info', 'All items in this order have already been fully received.');
        }

        $supplierOrder->load(['supplier', 'items.productVariant.product']);
        $products = Product::active()->orderBy('name')->get();
        return view('dashboard.supplier-orders.receive', compact('supplierOrder', 'products'));
    }

    /**
     * Process receiving items into stock.
     */
    public function processReceive(Request $request, SupplierWeeklyOrder $supplierOrder)
    {
        $role = Auth::guard('staff')->user()->role;
        if (!in_array($role, ['storekeeper', 'super_admin'])) {
            abort(403);
        }

        if ($supplierOrder->items->every(fn($item) => (float) $item->qty_received >= (float) $item->quantity)) {
            return redirect()->route('supplier-orders.show', $supplierOrder->id)->with('error', 'Order already fully received.');
        }

        DB::beginTransaction();
        try {
            $receivedById = Auth::guard('staff')->id();
            $anyReceived = false;

            foreach ($supplierOrder->items as $item) {
                $receivedQty = (float) $request->input("items.{$item->id}.received_qty", 0);
                $variantId = $item->product_variant_id;

                if ($receivedQty <= 0)
                    continue;

                // Ensure we don't receive more than ordered (optional, but good for data integrity)
                $remaining = $item->quantity - $item->qty_received;
                // if ($receivedQty > $remaining) $receivedQty = $remaining; // user might actually receive more? let's stick to remaining for now or allow over-receipt


                // Handle manual linking from the receive form
                if (!$variantId && $request->has("items.{$item->id}.product_variant_id")) {
                    $manualVariantId = $request->input("items.{$item->id}.product_variant_id");
                    if ($manualVariantId) {
                        $item->update(['product_variant_id' => $manualVariantId]);
                        $variantId = $manualVariantId;
                    }
                }

                if (!$receivedQty || $receivedQty <= 0)
                    continue;

                if ($variantId) {
                    $variant = ProductVariant::with('product')->find($variantId);
                    if ($variant) {
                        // Create a StockReceipt record for audit trail.
                        // ProductVariant::getCurrentStock() will automatically include this in its sum.
                        StockReceipt::create([
                            'product_id' => $variant->product_id,
                            'product_variant_id' => $variantId,
                            'supplier_id' => $supplierOrder->supplier_id,
                            'quantity_received_packages' => $receivedQty,
                            'buying_price_per_bottle' => $item->unit_price,
                            'selling_price_per_bottle' => $item->unit_price, // storekeeper can adjust later
                            'received_date' => now()->toDateString(),
                            'received_by' => $receivedById,
                            'minimum_stock_level' => $variant->minimum_stock_level ?? 0,
                            'minimum_stock_level_unit' => $variant->minimum_stock_level_unit ?? 'bottles',
                            'notes' => 'Auto-received from Weekly Order #' . $supplierOrder->id,
                        ]);

                        $anyReceived = true;
                    }
                }

                // Mark item as partially or fully received
                $item->increment('qty_received', $receivedQty);
                $item->update(['received_at' => now()]);
            }

            // Check if entire order is now fully received
            $isFullyReceived = $supplierOrder->items()->whereColumn('qty_received', '<', 'quantity')->count() === 0;
            if ($isFullyReceived) {
                $supplierOrder->update(['received_at' => now()]);
            }

            DB::commit();

            if ($anyReceived) {
                return redirect()->route('supplier-orders.show', $supplierOrder->id)
                    ->with('success', 'Items received and added to inventory successfully!');
            } else {
                return back()->with('error', 'No linked product variants found. Ensure items are linked to registered products in the system.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error receiving items: ' . $e->getMessage());
        }
    }

    /**
     * Record a payment for the supplier order.
     */
    public function recordPayment(Request $request, SupplierWeeklyOrder $supplierOrder)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $newAmountPaid = $supplierOrder->amount_paid + $request->amount;

            // Limit to total amount (or allow overpayment? usually not)
            if ($newAmountPaid > $supplierOrder->total_amount) {
                $newAmountPaid = $supplierOrder->total_amount;
            }

            $paymentStatus = 'partial';
            if ($newAmountPaid >= $supplierOrder->total_amount) {
                $paymentStatus = 'settled';
            }

            $supplierOrder->update([
                'amount_paid' => $newAmountPaid,
                'payment_status' => $paymentStatus,
                'notes' => $supplierOrder->notes . ' | PAYMENT RECORDED: ' . number_format($request->amount, 2) . ' TZS. ' . $request->notes,
            ]);

            DB::commit();
            return redirect()->route('supplier-orders.show', $supplierOrder->id)->with('success', 'Payment recorded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error recording payment: ' . $e->getMessage());
        }
    }
}
