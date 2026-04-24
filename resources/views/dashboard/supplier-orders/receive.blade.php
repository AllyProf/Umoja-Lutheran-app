@extends('dashboard.layouts.app')

@section('content')
    <div class="app-title">
        <div>
            <h1><i class="fa fa-inbox"></i> Receive Items into Inventory</h1>
            <p>Order #{{ $supplierOrder->id }} - <strong>{{ $supplierOrder->supplier->name }}</strong>
                ({{ \Carbon\Carbon::parse($supplierOrder->start_date)->format('M d') }} –
                {{ \Carbon\Carbon::parse($supplierOrder->end_date)->format('M d, Y') }})
            </p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="{{ route('supplier-orders.index') }}">Weekly Orders</a></li>
            <li class="breadcrumb-item"><a
                    href="{{ route('supplier-orders.show', $supplierOrder->id) }}">#{{ $supplierOrder->id }}</a></li>
            <li class="breadcrumb-item">Receive Items</li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <div class="tile-title-w-btn">
                    <h3 class="title">Confirm Received Quantities</h3>
                </div>
                <div class="tile-body">

                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i>
                        Weka idadi halisi ya <strong>Packages iliyopokelewa</strong> (e.g., Crates, Cartons).
                        Mfumo utaongeza moja kwa moja kwenye stoo ukizingatia ratio (chupa kwa kila crate).
                    </div>

                    <form action="{{ route('supplier-orders.process-receive', $supplierOrder->id) }}" method="POST">
                        @csrf
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Item Name</th>
                                        <th>Planned Qty</th>
                                        <th>Already Received</th>
                                        <th>System Product</th>
                                        <th>Packaging Info</th>
                                        <th>Receiving Now <span class="text-danger">*</span></th>
                                        <th>= Bottles/Units</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($supplierOrder->items as $item)
                                        @php
                                            $variant = $item->productVariant;
                                            $product = $variant ? $variant->product : null;
                                            $itemsPerPkg = ($variant && $variant->items_per_package > 0) ? $variant->items_per_package : 1;
                                            $purchasingUnit = $variant ? ($variant->purchasing_unit ?: 'Package') : 'Package';
                                            $receivingUnit = $variant ? ($variant->receiving_unit ?: 'pcs') : 'pcs';
                                        @endphp
                                        <tr>
                                            <td><strong>{{ $item->item_name }}</strong></td>
                                            <td>{{ (float) $item->quantity }} {{ $item->unit }}</td>
                                            <td>
                                                <span class="badge badge-secondary px-2">
                                                    {{ (float) $item->qty_received }} {{ $item->unit ?: 'pkg' }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($variant && $product)
                                                    <span class="badge badge-success" style="font-size: 13px;">
                                                        <i class="fa fa-check-circle"></i>
                                                        {{ $product->name }} ({{ $variant->variant_name }})
                                                    </span>
                                                    <input type="hidden" name="items[{{ $item->id }}][product_variant_id]"
                                                        value="{{ $variant->id }}">
                                                @else
                                                    <select name="items[{{ $item->id }}][product_variant_id]"
                                                        class="form-control form-control-sm variant-select" required>
                                                        <option value="">-- Chagua Bidhaa --</option>
                                                        @foreach($products as $p)
                                                            @foreach($p->variants as $v)
                                                                <option value="{{ $v->id }}" data-per-pkg="{{ $v->items_per_package ?: 1 }}"
                                                                    data-purchasing="{{ $v->purchasing_unit ?: 'Package' }}"
                                                                    data-receiving="{{ $v->receiving_unit ?: 'pcs' }}">
                                                                    {{ $p->name }} ({{ $v->variant_name }}) — 1
                                                                    {{ $v->purchasing_unit ?: 'pkg' }} = {{ $v->items_per_package ?: 1 }}
                                                                    {{ $v->receiving_unit ?: 'pcs' }}
                                                                </option>
                                                            @endforeach
                                                        @endforeach
                                                    </select>
                                                @endif
                                            </td>
                                            <td>
                                                @if($variant)
                                                    <span class="badge badge-info" style="font-size: 13px;">
                                                        1 {{ $purchasingUnit }} = {{ $itemsPerPkg }} {{ $receivingUnit }}
                                                    </span>
                                                @else
                                                    <span class="pkg-info text-muted small">Chagua bidhaa kwanza</span>
                                                @endif
                                            </td>
                                            <td style="width: 140px;">
                                                <div class="input-group input-group-sm">
                                                    <input type="number" name="items[{{ $item->id }}][received_qty]"
                                                        class="form-control qty-input" step="0.01" min="0"
                                                        value="{{ number_format(max(0, $item->quantity - $item->qty_received), 2, '.', '') }}"
                                                        {{ !$variant ? 'disabled style=background:#eee;' : '' }}
                                                        data-per-pkg="{{ $itemsPerPkg }}"
                                                        data-receiving-unit="{{ $receivingUnit }}" placeholder="0">
                                                    <div class="input-group-append">
                                                        <span
                                                            class="input-group-text pkg-unit-label">{{ $purchasingUnit }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($variant)
                                                    <span class="calculated-bottles text-success font-weight-bold">
                                                        {{ number_format(max(0, $item->quantity - $item->qty_received) * $itemsPerPkg) }}
                                                        {{ $receivingUnit }}
                                                    </span>
                                                @else
                                                    <span class="calculated-bottles text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($item->qty_received >= $item->quantity)
                                                    <span class="badge badge-success">
                                                        <i class="fa fa-check-circle"></i> Fully Received
                                                    </span>
                                                @elseif($item->qty_received > 0)
                                                    <span class="badge badge-info">
                                                        Partial
                                                        ({{ number_format(($item->qty_received / $item->quantity) * 100, 0) }}%)
                                                    </span>
                                                @else
                                                    <span class="badge badge-secondary">Pending</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="tile-footer">
                            <button type="submit" class="btn btn-success" id="receiveBtn">
                                <i class="fa fa-check-circle"></i> Confirm Receipt & Update Inventory
                            </button>
                            <a href="{{ route('supplier-orders.show', $supplierOrder->id) }}"
                                class="btn btn-secondary ml-2">
                                <i class="fa fa-times-circle"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            // On variant selection (for unlinked items), update packaging info and enable qty
            $(document).on('change', '.variant-select', function () {
                let $row = $(this).closest('tr');
                let $selected = $(this).find('option:selected');
                let $qtyInput = $row.find('.qty-input');
                let $pkgLabel = $row.find('.pkg-unit-label');
                let $pkgInfo = $row.find('.pkg-info');
                let $calcBottles = $row.find('.calculated-bottles');

                if ($(this).val()) {
                    let perPkg = parseInt($selected.data('per-pkg')) || 1;
                    let purchasingUnit = $selected.data('purchasing') || 'Package';
                    let receivingUnit = $selected.data('receiving') || 'pcs';

                    $pkgInfo.html(`<span class="badge badge-info" style="font-size:13px;">1 ${purchasingUnit} = ${perPkg} ${receivingUnit}</span>`);
                    $pkgLabel.text(purchasingUnit);
                    $qtyInput.prop('disabled', false).css('background', '#fff').data('per-pkg', perPkg).data('receiving-unit', receivingUnit);

                    let qty = parseFloat($qtyInput.val()) || 0;
                    $calcBottles.html(`<span class="text-success font-weight-bold">${(qty * perPkg).toLocaleString()} ${receivingUnit}</span>`);
                } else {
                    $qtyInput.prop('disabled', true).css('background', '#eee');
                    $pkgInfo.html('<span class="text-muted small">Chagua bidhaa kwanza</span>');
                    $pkgLabel.text('Package');
                    $calcBottles.html('<span class="text-muted">—</span>');
                }
            });

            // Live calculation of bottles as qty is typed
            $(document).on('input', '.qty-input', function () {
                let $row = $(this).closest('tr');
                let $calcBottles = $row.find('.calculated-bottles');
                let perPkg = $(this).data('per-pkg') || 1;
                let receivingUnit = $(this).data('receiving-unit') || 'pcs';
                let qty = parseFloat($(this).val()) || 0;
                $calcBottles.html(`<span class="text-success font-weight-bold">${(qty * perPkg).toLocaleString()} ${receivingUnit}</span>`);
            });

            $('#receiveBtn').click(function (e) {
                e.preventDefault();

                let allLinked = true;
                $('.variant-select').each(function () {
                    if (!$(this).val()) allLinked = false;
                });

                if (!allLinked) {
                    Swal.fire({ icon: 'error', title: 'Oops!', text: 'Chagua bidhaa iliyosajiliwa kwa kila item kwanza!' });
                    return;
                }

                Swal.fire({
                    title: 'Confirm Receipt?',
                    text: 'Hii itaongeza stock kwenye mfumo. Endelea?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ndio, Pokea!',
                    cancelButtonText: 'Kagua tena'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $(this).closest('form').submit();
                    }
                });
            });
        });
    </script>
@endsection