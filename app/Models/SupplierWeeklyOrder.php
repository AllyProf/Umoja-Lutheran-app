<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierWeeklyOrder extends Model
{
    protected $fillable = [
        'supplier_id',
        'lpo_id',
        'start_date',
        'end_date',
        'total_amount',
        'amount_paid',
        'payment_status',
        'status',
        'received_at',
        'storekeeper_id',
        'accountant_id',
        'manager_id',
        'notes',
        'manager_notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'total_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'received_at' => 'datetime',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function lpo()
    {
        return $this->belongsTo(LocalPurchaseOrder::class, 'lpo_id');
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

    public function items()
    {
        return $this->hasMany(SupplierWeeklyOrderItem::class);
    }
}
