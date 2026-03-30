<?php

namespace App\Services;

use App\Models\KitchenInventoryItem;
use App\Models\KitchenStockMovement;
use App\Models\HousekeepingInventoryItem;
use App\Models\InventoryStockMovement;
use App\Models\StockTransfer;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    /**
     * Update kitchen inventory from a received item (StockTransfer or ShoppingListItem)
     */
    public function updateKitchenInventory($name, $quantity, $unit, $category, $staffId, $notes, $expiryDate = null, $variant = null)
    {
        if ($quantity <= 0) {
            return null;
        }

        // Handle packaging if variant is provided and unit is a package unit
        if ($variant && in_array(strtolower($unit), ['crates', 'crate', 'carton', 'packages', 'package'])) {
            $itemsPerPackage = $variant->items_per_package ?? 1;
            $quantity *= $itemsPerPackage;
            $unit = $variant->measurement ?: 'pcs';
        }

        $inventoryItem = KitchenInventoryItem::firstOrCreate(
            ['name' => $name],
            [
                'category' => $category ?? 'other',
                'unit' => $unit,
                'current_stock' => 0,
                'minimum_stock' => 0,
            ]
        );

        $inventoryItem->current_stock += $quantity;
        if ($expiryDate) {
            $inventoryItem->expiry_date = $expiryDate;
        }
        $inventoryItem->save();

        return KitchenStockMovement::create([
            'inventory_item_id' => $inventoryItem->id,
            'movement_type' => 'supply',
            'quantity' => $quantity,
            'performed_by' => $staffId,
            'movement_date' => now(),
            'expiry_date' => $expiryDate,
            'notes' => $notes,
        ]);
    }

    /**
     * Update housekeeping inventory from a received item
     */
    public function updateHousekeepingInventory($name, $quantity, $unit, $category, $staffId, $notes, $variant = null)
    {
        if ($quantity <= 0) {
            return null;
        }

        // Handle packaging if variant is provided
        if ($variant && in_array(strtolower($unit), ['crates', 'crate', 'carton', 'packages', 'package'])) {
            $itemsPerPackage = $variant->items_per_package ?? 1;
            $quantity *= $itemsPerPackage;
            $unit = $variant->measurement ?: 'pcs';
        }

        $inventoryItem = HousekeepingInventoryItem::where('name', $name)->first();

        if (!$inventoryItem) {
            $inventoryItem = HousekeepingInventoryItem::create([
                'name' => $name,
                'category' => $category ?? 'other',
                'unit' => $unit,
                'current_stock' => 0,
                'minimum_stock' => 0,
            ]);
        }

        $inventoryItem->current_stock += $quantity;
        $inventoryItem->save();

        return InventoryStockMovement::create([
            'inventory_item_id' => $inventoryItem->id,
            'movement_type' => 'supply',
            'quantity' => $quantity,
            'performed_by' => $staffId,
            'notes' => $notes,
        ]);
    }

    /**
     * Create a bar transfer from a received item
     */
    public function createBarTransfer($name, $quantity, $unit, $price, $cost, $staffId, $expiryDate, $variant)
    {
        if (!$variant || $quantity <= 0) {
            return null;
        }

        // Determine unit
        $isPackage = in_array(strtolower($unit), ['crates', 'crate', 'carton', 'packages', 'package']);
        $transferUnit = $isPackage ? 'packages' : 'bottles';

        // Create a completed stock transfer to represent adding to bar stock
        $transfer = StockTransfer::create([
            'transfer_reference' => StockTransfer::generateReference(),
            'product_id' => $variant->product_id,
            'product_variant_id' => $variant->id,
            'quantity_transferred' => $quantity,
            'quantity_unit' => $transferUnit,
            'transferred_by' => 1, // System / Admin
            'received_by' => $staffId,
            'status' => 'completed',
            'transfer_date' => now(),
            'received_at' => now(),
            'notes' => 'Directly received from purchase: ' . $name,
            'unit_cost' => $price,
            'total_cost' => $cost,
            'selling_price_per_pic' => $variant->selling_price_per_pic,
            'selling_price_per_serving' => $variant->selling_price_per_serving,
            'servings_per_pic' => $variant->servings_per_pic,
            'expiry_date' => $expiryDate,
        ]);

        if (method_exists($transfer, 'calculateRevenueProjections')) {
            $transfer->calculateRevenueProjections();
            $transfer->save();
        }

        return $transfer;
    }
}
