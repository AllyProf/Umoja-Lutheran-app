<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LocalPurchaseOrder extends Model
{
    protected $fillable = [
        'start_date',
        'end_date',
        'total_amount',
        'spent_amount',
        'status',
        'storekeeper_id',
        'accountant_id',
        'manager_id',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function items()
    {
        return $this->hasMany(LocalPurchaseOrderItem::class, 'lpo_id');
    }

    public function storekeeper()
    {
        return $this->belongsTo(Staff::class, 'storekeeper_id');
    }

    public function accountant()
    {
        return $this->belongsTo(Staff::class, 'accountant_id');
    }

    public function manager()
    {
        return $this->belongsTo(Staff::class, 'manager_id');
    }

    public function supplierOrders()
    {
        return $this->hasMany(SupplierWeeklyOrder::class, 'lpo_id');
    }

    public function shoppingLists()
    {
        return $this->hasMany(ShoppingList::class, 'lpo_id');
    }

    public function getTotalSpentAmountAttribute()
    {
        return $this->items->sum('spent_amount');
    }

    public function getRemainingBalanceAttribute()
    {
        return $this->total_amount - $this->total_spent_amount;
    }
}
