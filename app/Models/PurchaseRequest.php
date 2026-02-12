<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'requested_by',
        'item_name',
        'category',
        'quantity',
        'unit',
        'water_size',
        'reason',
        'priority',
        'status',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'shopping_list_id',
        'edited_by',
        'last_edited_at',
        'last_changes',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'approved_at' => 'datetime',
        'last_edited_at' => 'datetime',
        'last_changes' => 'array',
    ];
    
    /**
     * Get the staff member who last edited this request
     */
    public function editor()
    {
        return $this->belongsTo(Staff::class, 'edited_by');
    }

    /**
     * Get the staff member who requested this
     */
    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'requested_by');
    }

    /**
     * Get the staff member who approved this
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'approved_by');
    }

    /**
     * Get the shopping list this request is part of
     */
    public function shoppingList(): BelongsTo
    {
        return $this->belongsTo(ShoppingList::class);
    }
    
    /**
     * Get department name for this purchase request
     * Cleaning supplies and Water → Housekeeping Department
     * Others → Based on requester's department
     */
    public function getDepartmentName(): string
    {
        $category = strtolower($this->category ?? '');
        
        // Cleaning supplies always go to Housekeeping
        if (in_array($category, ['cleaning_supplies'])) {
            return 'Housekeeping';
        }
        
        // For other categories, use the requester's department
        if ($this->requestedBy) {
            return $this->requestedBy->getDepartmentName();
        }
        
        // Fallback based on category
        $categoryToDepartment = [
            'linens' => 'Housekeeping',
            'beverages' => 'Bar',
            'food' => 'Kitchen',
            'other' => 'Reception',
        ];
        
        return $categoryToDepartment[$category] ?? 'Reception';
    }
}
