@extends('dashboard.layouts.app')

@section('content')
<div class="app-title d-print-none">
    <div>
        <h1><i class="fa fa-file-text-o"></i> Shopping List Details</h1>
        <p>{{ $shoppingList->name }}</p>
    </div>
    <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.restaurants.shopping-list.index') }}">Shopping Lists</a></li>
        <li class="breadcrumb-item active"><a href="#">View</a></li>
    </ul>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="tile">
            <div class="tile-title-w-btn d-print-none">
                <h3 class="title">Print Preview</h3>
                <p>
                    <button class="btn btn-primary" onclick="window.print();"><i class="fa fa-print"></i> Print</button>
                    <a href="{{ route('admin.restaurants.shopping-list.download', $shoppingList->id) }}" class="btn btn-secondary" target="_blank"><i class="fa fa-download"></i> Download</a>
                    <a href="{{ route('admin.restaurants.shopping-list.record', $shoppingList->id) }}" class="btn btn-success"><i class="fa fa-check-square-o"></i> Receive</a>
                </p>
            </div>
            
            <div class="invoice p-3 mb-3">
                <div class="row mb-1">
                    <div class="col-12 text-center border-bottom pb-1">
                        <h4 style="margin: 0; color: #000; font-weight: bold;">PRIMELAND HOTEL - SHOPPING LIST</h4>
                        <p style="font-size: 10px; margin: 0;">Ref: SL-{{ $shoppingList->id }} | Date: {{ $shoppingList->created_at->format('d/m/Y') }}</p>
                    </div>
                </div>
                
                @php
                    $estTotal = $shoppingList->items->sum('estimated_price');
                    $actTotal = $shoppingList->items->sum('purchased_cost');
                    $remaining = ($shoppingList->budget_amount ?? $estTotal) - $actTotal;
                    $marketBudget = $shoppingList->budget_amount ?? $estTotal;
                @endphp

                <div class="row mt-1 mb-1" style="font-size: 9px; line-height: 1.2;">
                    <div class="col-12 text-center">
                        <strong>Market:</strong> {{ $shoppingList->market_name ?? 'Any' }} | 
                        <strong>Items:</strong> {{ $shoppingList->items->count() }} | 
                        <strong>Status:</strong> {{ strtoupper($shoppingList->status) }}
                    </div>
                </div>

                <div class="row mt-1 mb-2" style="font-size: 10px; border-top: 1px dotted #ccc; border-bottom: 1px dotted #ccc; padding: 3px 0;">
                    <div class="col-4 text-center">
                        <strong>Budget to Market</strong><br>
                        {{ number_format($marketBudget, 0) }} TZS
                    </div>
                    <div class="col-4 text-center">
                        <strong>Amount Used</strong><br>
                        {{ number_format($actTotal, 0) }} TZS
                    </div>
                    <div class="col-4 text-center">
                        <strong>Remaining</strong><br>
                        <span style="color: {{ $remaining >= 0 ? 'green' : 'red' }};">{{ number_format($remaining, 0) }} TZS</span>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 table-responsive">
                        <table class="table table-sm table-bordered mb-0" style="font-size: 9px;">
                            <thead>
                                <tr style="background-color: #f4f4f4;">
                                    <th width="3%">✓</th>
                                    <th width="50%">Item Description (Dept)</th>
                                    <th width="12%">Qty</th>
                                    <th width="15%">Unit</th>
                                    <th width="20%" class="text-right">Est. Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    // Consolidate identical items across all departments to save paper
                                    $consolidated = [];
                                    foreach ($shoppingList->items as $item) {
                                        $key = strtolower($item->product_name) . '_' . strtolower($item->unit);
                                        
                                        if (!isset($consolidated[$key])) {
                                            $consolidated[$key] = [
                                                'product_name' => $item->product_name,
                                                'category' => $item->category,
                                                'category_name' => $item->category_name,
                                                'quantity' => 0,
                                                'unit' => $item->unit,
                                                'estimated_price' => 0,
                                                'departments' => []
                                            ];
                                        }
                                        
                                        $consolidated[$key]['quantity'] += (float)$item->quantity;
                                        $consolidated[$key]['estimated_price'] += (float)$item->estimated_price;

                                        // Collect departments for reference
                                        $dept = 'Other';
                                        if ($item->purchaseRequest) {
                                            $dept = substr($item->purchaseRequest->getDepartmentName(), 0, 1); // Short code: H, B, F, R
                                        }
                                        if (!in_array($dept, $consolidated[$key]['departments'])) {
                                            $consolidated[$key]['departments'][] = $dept;
                                        }
                                    }

                                    $groupedItems = collect($consolidated)->groupBy('category');
                                    $grandTotal = 0;
                                @endphp

                                @foreach($groupedItems as $category => $items)
                                    <tr style="background-color: #eee; line-height: 1;">
                                        <td colspan="5"><strong>{{ strtoupper($category) }}</strong></td>
                                    </tr>
                                    @foreach($items as $item)
                                    <tr>
                                        <td class="text-center">☐</td>
                                        <td>
                                            {{ $item['product_name'] }}
                                            <span style="font-size: 8px; color: #777;">({{ implode(',', $item['departments']) }})</span>
                                        </td>
                                        <td>{{ number_format($item['quantity'], 0) }}</td>
                                        <td>{{ $item['unit'] == 'bottles' ? 'PIC' : $item['unit'] }}</td>
                                        <td class="text-right">{{ number_format($item['estimated_price'], 0) }}</td>
                                    </tr>
                                    @php $grandTotal += $item['estimated_price']; @endphp
                                    @endforeach
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="5" class="text-right">Total Estimated:</th>
                                    <th>{{ number_format($grandTotal, 0) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                
                <div class="row mt-2 border-top pt-1">
                    <div class="col-12 text-center small text-muted" style="font-size: 8px;">
                        Generated on {{ date('d M Y H:i A') }} | Powered By EmCa Technologies
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style media="print">
    @page { size: portrait; margin: 3mm; }
    .app-header, .app-sidebar, .app-title, .tile-title-w-btn, .d-print-none {
        display: none !important;
    }
    .tile {
        border: none !important;
        box-shadow: none !important;
    }
    .content-wrapper {
        margin-left: 0 !important;
        padding: 10px !important;
        background: white !important;
    }
    body {
        font-family: 'Times New Roman', serif;
        background: white !important;
    }
    .badge { border: 1px solid #000; color: #000; }
</style>
@endsection
