<?php

namespace App\Http\Controllers;

use App\Models\ProductVariant;
use App\Models\StockRequest;
use App\Models\StockReturn;
use App\Models\StockTransfer;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockReturnController extends Controller
{
    public function __construct(protected InventoryService $inventoryService)
    {
    }

    /**
     * Housekeeper: View returnable items currently held and pending returns.
     */
    public function housekeeperIndex()
    {
        $staff = Auth::guard('staff')->user();
        $heldItems = $this->buildHeldItems($staff->id);

        $myReturns = StockReturn::with(['productVariant.product'])
            ->where('staff_id', $staff->id)
            ->latest()
            ->take(20)
            ->get()
            ->map(function ($ret) {
                $ret->display_unit = $this->resolveDisplayUnit($ret->unit, $ret->productVariant);
                $ret->display_quantity = $this->formatQty((float) $ret->quantity, $ret->display_unit);
                return $ret;
            });

        return view('dashboard.stock-returns.housekeeper-index', compact('heldItems', 'myReturns', 'staff'));
    }

    /**
     * Housekeeper: Submit a return request.
     */
    public function store(Request $request)
    {
        $staff = Auth::guard('staff')->user();

        $validated = $request->validate([
            'product_variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|numeric|min:0.01',
            'unit' => 'required|string',
            'notes' => 'nullable|string|max:500',
        ]);

        $variant = ProductVariant::with('product')->findOrFail($validated['product_variant_id']);
        if (!$variant->product || !$variant->product->is_returnable) {
            return $this->respond($request, false, 'This item is not marked as returnable.', 422);
        }

        $held = $this->buildHeldItems($staff->id)
            ->firstWhere('product_variant_id', (int) $validated['product_variant_id']);

        $available = (float) ($held->quantity_held ?? 0);
        $qty = (float) $validated['quantity'];

        if ($available <= 0) {
            return $this->respond($request, false, 'You do not currently hold this returnable item.', 422);
        }

        if ($qty > $available + 0.0001) {
            return $this->respond(
                $request,
                false,
                'You cannot return more than you hold (' . rtrim(rtrim(number_format($available, 2, '.', ''), '0'), '.') . ').',
                422
            );
        }

        StockReturn::create([
            'staff_id' => $staff->id,
            'product_variant_id' => $variant->id,
            'quantity' => $qty,
            'unit' => $this->resolveDisplayUnit(
                $validated['unit'] ?: ($held->unit ?? null),
                $variant
            ),
            'status' => 'pending',
            'notes' => $validated['notes'] ?? null,
        ]);

        return $this->respond(
            $request,
            true,
            'Return request submitted. Waiting for storekeeper to receive it.',
            200,
            'housekeeper.stock-returns.index'
        );
    }

    /**
     * Storekeeper: View pending return requests.
     */
    public function storekeeperIndex()
    {
        $pendingReturns = StockReturn::with(['staff', 'productVariant.product'])
            ->where('status', 'pending')
            ->latest()
            ->get()
            ->map(function ($ret) {
                $ret->display_unit = $this->resolveDisplayUnit($ret->unit, $ret->productVariant);
                $ret->display_quantity = $this->formatQty((float) $ret->quantity, $ret->display_unit);
                return $ret;
            });

        $recentlyReceived = StockReturn::with(['staff', 'productVariant.product', 'receiver'])
            ->whereIn('status', ['received', 'rejected'])
            ->latest()
            ->take(20)
            ->get()
            ->map(function ($ret) {
                $ret->display_unit = $this->resolveDisplayUnit($ret->unit, $ret->productVariant);
                $ret->display_quantity = $this->formatQty((float) $ret->quantity, $ret->display_unit);
                return $ret;
            });

        return view('dashboard.stock-returns.storekeeper-index', compact('pendingReturns', 'recentlyReceived'));
    }

    /**
     * Storekeeper: Confirm receipt of a returned item (restocks store, debits HK inventory).
     */
    public function receive(Request $request, StockReturn $stockReturn)
    {
        $staff = Auth::guard('staff')->user();

        if ($stockReturn->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'This return has already been processed.'], 422);
        }

        $stockReturn->loadMissing('productVariant.product');

        DB::transaction(function () use ($stockReturn, $staff) {
            $stockReturn->update([
                'status' => 'received',
                'received_by' => $staff->id,
                'received_at' => now(),
            ]);

            $variant = $stockReturn->productVariant;
            if ($variant) {
                $itemName = $this->inventoryItemName($variant);
                $this->inventoryService->returnFromHousekeepingInventory(
                    $itemName,
                    (float) $stockReturn->quantity,
                    $stockReturn->unit ?: 'pcs',
                    $staff->id,
                    "Returned to store · Return #{$stockReturn->id}",
                    $variant
                );
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Item received back into store. Housekeeping stock updated.',
        ]);
    }

    /**
     * Storekeeper: Reject a return request.
     */
    public function reject(Request $request, StockReturn $stockReturn)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        if ($stockReturn->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'This return has already been processed.'], 422);
        }

        $stockReturn->update([
            'status' => 'rejected',
            'received_by' => Auth::guard('staff')->id(),
            'received_at' => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Return rejected.',
        ]);
    }

    /**
     * Build held returnable quantities per product variant for a staff member.
     */
    protected function buildHeldItems(int $staffId)
    {
        $issuedByVariant = [];

        // Issued via completed stock requests (quantity_issued preferred)
        $requests = StockRequest::with(['productVariant.product'])
            ->where('requested_by', $staffId)
            ->where('status', 'completed')
            ->whereHas('productVariant.product', fn ($q) => $q->where('is_returnable', true))
            ->get();

        foreach ($requests as $req) {
            $vid = (int) $req->product_variant_id;
            if (!$vid) {
                continue;
            }
            $qty = (float) ($req->quantity_issued ?? $req->quantity ?? 0);
            if (!isset($issuedByVariant[$vid])) {
                $issuedByVariant[$vid] = [
                    'product_variant_id' => $vid,
                    'variant' => $req->productVariant,
                    'unit' => $this->resolveDisplayUnit($req->unit, $req->productVariant),
                    'issued' => 0.0,
                ];
            }
            $issuedByVariant[$vid]['issued'] += $qty;
        }

        // Direct distributes to this staff (not already counted via stock_request.stock_transfer_id)
        $linkedTransferIds = StockRequest::where('requested_by', $staffId)
            ->whereNotNull('stock_transfer_id')
            ->pluck('stock_transfer_id')
            ->all();

        $directTransfers = StockTransfer::with(['productVariant.product'])
            ->where('received_by', $staffId)
            ->where('status', 'completed')
            ->whereHas('productVariant.product', fn ($q) => $q->where('is_returnable', true))
            ->when(!empty($linkedTransferIds), fn ($q) => $q->whereNotIn('id', $linkedTransferIds))
            ->get();

        foreach ($directTransfers as $transfer) {
            $vid = (int) $transfer->product_variant_id;
            if (!$vid) {
                continue;
            }
            if (!isset($issuedByVariant[$vid])) {
                $issuedByVariant[$vid] = [
                    'product_variant_id' => $vid,
                    'variant' => $transfer->productVariant,
                    'unit' => $this->resolveDisplayUnit($transfer->quantity_unit, $transfer->productVariant),
                    'issued' => 0.0,
                ];
            }
            $issuedByVariant[$vid]['issued'] += (float) $transfer->quantity_transferred;
        }

        return collect($issuedByVariant)->map(function ($row) use ($staffId) {
            $vid = $row['product_variant_id'];

            $returned = (float) StockReturn::where('staff_id', $staffId)
                ->where('product_variant_id', $vid)
                ->where('status', 'received')
                ->sum('quantity');

            $pending = (float) StockReturn::where('staff_id', $staffId)
                ->where('product_variant_id', $vid)
                ->where('status', 'pending')
                ->sum('quantity');

            $held = max(0, (float) $row['issued'] - $returned - $pending);
            $unit = $this->resolveDisplayUnit($row['unit'], $row['variant']);

            return (object) [
                'product_variant_id' => $vid,
                'productVariant' => $row['variant'],
                'unit' => $unit,
                'quantity_issued_total' => (float) $row['issued'],
                'quantity_held' => $held,
                'quantity_held_display' => $this->formatQty($held, $unit),
                'quantity_returned' => $returned,
                'quantity_pending_return' => $pending,
                'quantity_pending_display' => $this->formatQty($pending, $unit),
            ];
        })
            ->filter(fn ($r) => $r->quantity_held > 0 || $r->quantity_pending_return > 0)
            ->values();
    }

    /**
     * Map legacy "bottles" to the product receiving unit (Pcs) for non-drink items.
     */
    protected function resolveDisplayUnit(?string $unit, $variant = null): string
    {
        $raw = trim((string) $unit);
        $lower = strtolower($raw);
        $receiving = trim((string) ($variant->receiving_unit ?? ''));
        $product = $variant?->product;
        $category = strtolower((string) ($product->category ?? ''));
        $type = strtolower((string) ($product->type ?? ''));

        $isDrink = in_array($type, ['drink', 'beverage'], true) || in_array($category, [
            'vinywaji', 'alcoholic_beverage', 'non_alcoholic_beverage', 'water',
            'juices', 'energy_drinks', 'drinks', 'beverage', 'spirits', 'wines',
            'hot_beverages', 'soft_drinks', 'beers',
        ], true);

        if (in_array($lower, ['bottles', 'bottle', 'pic'], true) && !$isDrink) {
            if ($receiving !== '' && !in_array(strtolower($receiving), ['bottle', 'bottles', 'pic'], true)) {
                return $receiving;
            }
            return 'Pcs';
        }

        if (in_array($lower, ['pcs', 'pc', 'piece', 'pieces', 'units', 'unit'], true)) {
            return $receiving !== '' ? $receiving : 'Pcs';
        }

        if ($raw !== '') {
            return $raw;
        }

        return $receiving !== '' ? $receiving : 'Pcs';
    }

    protected function formatQty(float $qty, string $unit): string
    {
        $lower = strtolower($unit);
        if (in_array($lower, ['pcs', 'pc', 'piece', 'pieces', 'unit', 'units', 'bottle', 'bottles', 'pic'], true)
            || !preg_match('/kg|litre|liter|gram|ml|l\b/i', $unit)) {
            return (string) (int) round($qty);
        }

        $formatted = number_format($qty, 2, '.', '');
        return rtrim(rtrim($formatted, '0'), '.');
    }

    protected function inventoryItemName(ProductVariant $variant): string
    {
        $product = $variant->product;
        $itemName = $product->name ?? 'Item';
        if ($variant->variant_name && !in_array(strtolower($variant->variant_name), ['standard', 'unit', 'default'], true)) {
            $itemName .= ' - ' . $variant->variant_name;
        }

        return $itemName;
    }

    protected function respond(Request $request, bool $success, string $message, int $status = 200, ?string $redirectRoute = null)
    {
        if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
            return response()->json([
                'success' => $success,
                'message' => $message,
            ], $status);
        }

        if ($success && $redirectRoute) {
            return redirect()->route($redirectRoute)->with('success', $message);
        }

        return redirect()->back()->with($success ? 'success' : 'error', $message);
    }
}
