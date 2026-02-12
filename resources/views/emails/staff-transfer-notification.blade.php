@component('mail::message')
# Items Transferred to {{ $department }} Department

Hello,

Items have been transferred to the **{{ $department }} Department** and are ready for you to receive.

**Transferred by:** {{ $transferredBy->name ?? 'Manager' }}

## Transferred Items:

@component('mail::table')
| Item Name | Category | Quantity | Unit | Total Cost |
|:---------|:---------|:---------|:-----|:-----------|
@foreach($items as $item)
| {{ $item->product_name }} | {{ $item->category_name ?? $item->category }} | {{ number_format($item->purchased_quantity, 2) }} | {{ $item->unit }} | {{ number_format($item->purchased_cost ?? 0, 2) }} TZS |
@endforeach
@endcomponent

**Total Items:** {{ count($items) }}  
**Total Cost:** {{ number_format(collect($items)->sum('purchased_cost'), 2) }} TZS

Please log in to your account to receive these items and update your inventory.

@component('mail::button', ['url' => route('housekeeper.purchase-requests.my')])
View My Requests
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
