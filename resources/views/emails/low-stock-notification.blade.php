@component('mail::message')
# Low Stock Alert

Hello,

This is to notify you that the following inventory item has fallen below its minimum stock level:

**Item Name:** {{ $item->name }}  
**Category:** {{ ucfirst(str_replace('_', ' ', $item->category)) }}  
**Current Stock:** {{ number_format($item->current_stock, 2) }} {{ $item->unit }}  
**Minimum Stock:** {{ number_format($item->minimum_stock, 2) }} {{ $item->unit }}  
**Stock Status:** 
@if($item->current_stock <= 0)
<span style="color: #dc3545; font-weight: bold;">CRITICAL - Out of Stock</span>
@else
<span style="color: #ffc107; font-weight: bold;">LOW - Below Minimum</span>
@endif

**Action Required:** Please review and place a purchase request to replenish this item.

@component('mail::button', ['url' => route('housekeeper.inventory')])
View Inventory
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
