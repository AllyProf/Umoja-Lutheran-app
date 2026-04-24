@extends('dashboard.layouts.app')

@section('content')
    <div class="app-title">
        <div>
            <h1><i class="fa fa-edit"></i> Edit Items for Batch #{{ $supplierOrder->id }}</h1>
            <p>{{ $supplierOrder->supplier->name }} ({{ \Carbon\Carbon::parse($supplierOrder->start_date)->format('M d') }}
                – {{ \Carbon\Carbon::parse($supplierOrder->end_date)->format('M d, Y') }})</p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="{{ route('supplier-orders.index') }}">Weekly Orders</a></li>
            <li class="breadcrumb-item"><a
                    href="{{ route('supplier-orders.show', $supplierOrder->id) }}">#{{ $supplierOrder->id }}</a></li>
            <li class="breadcrumb-item">Edit Items</li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <div class="tile-title-w-btn">
                    <h3 class="title">Batch Items</h3>
                    @if($supplierOrder->status !== 'pending')
                        <div class="alert alert-warning mb-0 ml-3 py-1">
                            <i class="fa fa-warning"></i> <strong>Amendment Mode:</strong> Adding items or increasing quantities
                            will reset the status for re-approval.
                        </div>
                    @endif
                    <p><button class="btn btn-primary icon-btn" type="button" id="addRow"><i class="fa fa-plus"></i>Add Item
                            Row</button></p>
                </div>
                <div class="tile-body">
                    <form action="{{ route('supplier-orders.update', $supplierOrder->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="table-responsive">
                            <table class="table table-bordered" id="itemsTable">
                                <thead>
                                    <tr>
                                        <th style="width: 40%;">Item / Registered Product <span class="text-danger">*</span>
                                        </th>
                                        <th style="width: 15%;">Quantity <span class="text-danger">*</span></th>
                                        <th style="width: 15%;">Unit</th>
                                        <th style="width: 20%;">Unit Price (TSZ) <span class="text-danger">*</span></th>
                                        <th style="width: 10%;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($supplierOrder->items as $index => $item)
                                        <tr>
                                            <td>
                                                <select name="items[{{ $index }}][product_variant_id]"
                                                    class="form-control product-select" required>
                                                    <option value="">-- Select Registered Product --</option>
                                                    @foreach($products as $product)
                                                        @foreach($product->variants as $variant)
                                                            <option value="{{ $variant->id }}" {{ $item->product_variant_id == $variant->id ? 'selected' : '' }}>
                                                                {{ $product->name }} ({{ $variant->variant_name }})
                                                            </option>
                                                        @endforeach
                                                    @endforeach
                                                </select>
                                                {{-- Hidden fields for sync --}}
                                                <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                                                <input type="hidden" name="items[{{ $index }}][item_name]"
                                                    class="item-name-hidden" value="{{ $item->item_name }}">
                                            </td>
                                            <td><input type="number" name="items[{{ $index }}][quantity]"
                                                    class="form-control qty" step="any" value="{{ (float) $item->quantity }}"
                                                    required>
                                            </td>
                                            <td>
                                                <select name="items[{{ $index }}][unit]" class="form-control">
                                                    @foreach(['Piece (Pcs)', 'Kg', 'Litres (L)', 'Grams (g)', 'Tray', 'Packet', 'Box', 'Carton', 'Crate', 'Sado', 'Debe', 'Kiroba', 'Bunch', 'Bucket', 'Bundle', 'Bottle', 'Dozen'] as $u)
                                                        <option value="{{ $u }}" {{ $item->unit == $u ? 'selected' : '' }}>{{ $u }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input type="number" name="items[{{ $index }}][unit_price]"
                                                    class="form-control price" step="any" value="{{ (int) $item->unit_price }}"
                                                    required></td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-danger btn-sm removeRow"><i
                                                        class="fa fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    @endforeach

                                    @if($supplierOrder->items->isEmpty())
                                        <tr>
                                            <td>
                                                <select name="items[0][product_variant_id]" class="form-control product-select"
                                                    required>
                                                    <option value="">-- Select Registered Product --</option>
                                                    @foreach($products as $product)
                                                        @foreach($product->variants as $variant)
                                                            <option value="{{ $variant->id }}">
                                                                {{ $product->name }} ({{ $variant->variant_name }})
                                                            </option>
                                                        @endforeach
                                                    @endforeach
                                                </select>
                                                <input type="hidden" name="items[0][item_name]" class="item-name-hidden">
                                            </td>
                                            <td><input type="number" name="items[0][quantity]" class="form-control qty"
                                                    step="any" required></td>
                                            <td>
                                                <select name="items[0][unit]" class="form-control">
                                                    @foreach(['Piece (Pcs)', 'Kg', 'Litres (L)', 'Grams (g)', 'Tray', 'Packet', 'Box', 'Carton', 'Crate', 'Sado', 'Debe', 'Kiroba', 'Bunch', 'Bucket', 'Bundle', 'Bottle', 'Dozen'] as $u)
                                                        <option value="{{ $u }}">{{ $u }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input type="number" name="items[0][unit_price]" class="form-control price"
                                                    step="any" required></td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-danger btn-sm removeRow"><i
                                                        class="fa fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>

                            <div class="row mb-3">
                                <div class="col-md-12 text-right">
                                    <h4 id="grandTotalDisplay">Grand Total:
                                        {{ number_format($supplierOrder->total_amount, 0) }} TZS
                                    </h4>
                                </div>
                            </div>
                            <button class="btn btn-success" type="submit"><i class="fa fa-fw fa-lg fa-save"></i>Save Items &
                                Update Total</button>
                            <a class="btn btn-secondary" href="{{ route('supplier-orders.show', $supplierOrder->id) }}"><i
                                    class="fa fa-fw fa-lg fa-times-circle"></i>Cancel</a>
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
            let rowCount = {{ $supplierOrder->items->count() ?: 1 }};

            // Prepare options HTML once to reuse in JS
            const productOptions = `
                                <option value="">-- Select Registered Product --</option>
                                @foreach($products as $product)
                                    @foreach($product->variants as $variant)
                                        <option value="{{ $variant->id }}">{{ $product->name }} ({{ $variant->variant_name }})</option>
                                    @endforeach
                                @endforeach
                            `;

            $('#addRow').click(function () {
                let newRow = `
                                    <tr>
                                        <td>
                                            <select name="items[${rowCount}][product_variant_id]" class="form-control product-select" required>
                                                ${productOptions}
                                            </select>
                                            <input type="hidden" name="items[${rowCount}][item_name]" class="item-name-hidden">
                                        </td>
                                        <td><input type="number" name="items[${rowCount}][quantity]" class="form-control qty" step="any" required></td>
                                        <td>
                                            <select name="items[${rowCount}][unit]" class="form-control">
                                                @foreach(['Piece (Pcs)', 'Kg', 'Litres (L)', 'Grams (g)', 'Tray', 'Packet', 'Box', 'Carton', 'Crate', 'Sado', 'Debe', 'Kiroba', 'Bunch', 'Bucket', 'Bundle', 'Bottle', 'Dozen'] as $u)
                                                    <option value="{{ $u }}">{{ $u }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input type="number" name="items[${rowCount}][unit_price]" class="form-control price" step="any" required></td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-danger btn-sm removeRow"><i class="fa fa-trash"></i></button>
                                        </td>
                                    </tr>
                                `;
                $('#itemsTable tbody').append(newRow);
                rowCount++;
            });

            $(document).on('click', '.removeRow', function () {
                if ($('#itemsTable tbody tr').length > 1) {
                    $(this).closest('tr').remove();
                    updateGrandTotal();
                }
            });

            // Auto-populate item_name hidden field when selection changes
            $(document).on('change', '.product-select', function () {
                let text = $(this).find('option:selected').text().trim();
                if ($(this).val()) {
                    $(this).closest('td').find('.item-name-hidden').val(text);
                } else {
                    $(this).closest('td').find('.item-name-hidden').val('');
                }
            });

            function updateGrandTotal() {
                let grandTotal = 0;
                $('#itemsTable tbody tr').each(function () {
                    let qty = parseFloat($(this).find('.qty').val()) || 0;
                    let price = parseFloat($(this).find('.price').val()) || 0;
                    grandTotal += (qty * price);
                });
                $('#grandTotalDisplay').text('Grand Total: ' + grandTotal.toLocaleString() + ' TZS');
            }

            $(document).on('input', '.qty, .price', function () {
                updateGrandTotal();
            });
        });
    </script>
@endsection