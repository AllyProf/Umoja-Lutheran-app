<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryStockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_item_id',
        'movement_type',
        'quantity',
        'room_id',
        'performed_by',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
    ];

    /**
     * Get the inventory item for this movement
     */
    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(HousekeepingInventoryItem::class, 'inventory_item_id');
    }

    /**
     * Get the room for this movement (if applicable)
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Get the staff member who performed this movement
     */
    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'performed_by');
    }
}
