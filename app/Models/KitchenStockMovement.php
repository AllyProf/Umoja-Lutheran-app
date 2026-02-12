<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KitchenStockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_item_id',
        'movement_type',
        'quantity',
        'unit_price',
        'total_amount',
        'movement_date',
        'expiry_date',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'movement_date' => 'date',
        'expiry_date' => 'date',
    ];

    /**
     * Get the inventory item for this movement
     */
    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(KitchenInventoryItem::class, 'inventory_item_id');
    }

    /**
     * Get the staff member who performed this movement
     */
    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'performed_by');
    }
}
