@extends('dashboard.layouts.app')

@section('title', 'Batch Stock Distribution')

@section('content')
<div class="container-fluid">
    <div class="row page-titles">
        <div class="col-md-5 align-self-center">
            <h4 class="text-themecolor">Batch Distribution — {{ $batchReference }}</h4>
        </div>
        <div class="col-md-7 align-self-center text-end">
            <div class="d-flex justify-content-end align-items-center">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('stock-requests.index') }}">Stock Requests</a></li>
                    <li class="breadcrumb-item active">Batch Distribution</li>
                </ol>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Approved Items in Batch: {{ $batchReference }}</h4>
                    <p class="text-muted">Review and confirm the quantities and unit prices for each item before issuing.</p>
                    
                    <form action="{{ route('stock-requests.batch-distribute.submit', $batchId) }}" method="POST">
                        @csrf
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped custom-table">
                                <thead class="bg-primary text-white">
                                    <tr>
                                        <th style="width: 50px;">#</th>
                                        <th>Product / Variant</th>
                                        <th style="width: 150px;">Requested Qty</th>
                                        <th style="width: 150px;">Issue Qty <span class="text-danger">*</span></th>
                                        <th style="width: 200px;">Unit Price (TSH) <span class="text-danger">*</span></th>
                                        <th style="width: 180px;">Sub-Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stockRequests as $index => $request)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <strong>{{ $request->productVariant->product->name }}</strong><br>
                                                <small class="text-muted">{{ $request->productVariant->variant_name }}</small>
                                                <input type="hidden" name="items[{{ $index }}][id]" value="{{ $request->id }}">
                                            </td>
                                            <td>
                                                <span class="badge badge-light">
                                                    {{ number_format($request->quantity, 1) }} {{ $request->unit }}
                                                </span>
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" min="0.01" name="items[{{ $index }}][quantity_issued]" 
                                                    value="{{ $request->quantity }}" class="form-control qty-input" 
                                                    data-index="{{ $index }}" required>
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" min="0" name="items[{{ $index }}][unit_cost]" 
                                                    value="{{ $request->unit_cost }}" class="form-control cost-input" 
                                                    data-index="{{ $index }}" required>
                                                @if($request->unit_cost > 0)
                                                    <small class="text-success"><i class="fa fa-info"></i> Latest Cost: {{ number_format($request->unit_cost) }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="subtotal-display" id="subtotal-{{ $index }}">
                                                    {{ number_format($request->quantity * $request->unit_cost, 2) }}
                                                </span> TSH
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="table-info font-weight-bold">
                                        <td colspan="5" class="text-end">GRAND TOTAL:</td>
                                        <td><span id="grand-total">0.00</span> TSH</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('stock-requests.index') }}" class="btn btn-secondary">
                                <i class="fa fa-arrow-left"></i> Back to List
                            </a>
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fa fa-check-circle"></i> Complete Distribution & Print Note
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        function calculateTotals() {
            let grandTotal = 0;
            $('.qty-input').each(function() {
                let index = $(this).data('index');
                let qty = parseFloat($(this).val()) || 0;
                let cost = parseFloat($(`.cost-input[data-index="${index}"]`).val()) || 0;
                let subtotal = qty * cost;
                
                $(`#subtotal-${index}`).text(subtotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                grandTotal += subtotal;
            });
            $('#grand-total').text(grandTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        }

        $('.qty-input, .cost-input').on('input', calculateTotals);
        calculateTotals();
    });
</script>
@endsection
