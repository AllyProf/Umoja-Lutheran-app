@extends('dashboard.layouts.app')

@section('content')
    <div class="app-title">
        <div>
            <h1><i class="fa fa-undo"></i> Return Items to Store</h1>
            <p>Return non-consumable housekeeping items back to the storeroom</p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="{{ route('housekeeper.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Return Items</li>
        </ul>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fa fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div class="row">
        {{-- Held Items --}}
        <div class="col-md-8">
            <div class="tile">
                <h4 class="tile-title"><i class="fa fa-box-open text-info"></i> Items You Currently Hold</h4>
                @if($heldItems->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Item</th>
                                    <th class="text-center">Held</th>
                                    <th class="text-center">Pending Return</th>
                                    <th class="text-center">Unit</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($heldItems as $item)
                                    <tr>
                                        <td>
                                            <strong>{{ $item->productVariant->product->name ?? 'Item' }}</strong><br>
                                            <small
                                                class="text-muted">{{ preg_replace('/\s*\(?\s*ml\s*\)?\s*/i', '', $item->productVariant->variant_name ?? '') }}</small>
                                        </td>
                                        <td class="text-center">
                                            @if($item->quantity_held > 0)
                                                <span class="badge badge-success"
                                                    style="font-size:1rem;">{{ $item->quantity_held }}</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($item->quantity_pending_return > 0)
                                                <span class="badge badge-warning"
                                                    style="font-size:1rem;">{{ $item->quantity_pending_return }}</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td class="text-center"><span class="badge badge-light">{{ $item->unit }}</span></td>
                                        <td class="text-center">
                                            @if($item->quantity_held > 0)
                                                <button class="btn btn-sm btn-outline-info"
                                                    onclick="openReturnModal({{ $item->product_variant_id }}, '{{ addslashes($item->productVariant->product->name ?? '') }}', {{ $item->quantity_held }}, '{{ $item->unit }}')">
                                                    <i class="fa fa-undo"></i> Return
                                                </button>
                                            @else
                                                <span class="text-muted small">Awaiting receipt</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fa fa-check-circle fa-4x text-success mb-3" style="opacity:0.5;"></i>
                        <h5 class="text-muted">No returnable items currently held.</h5>
                        <p class="text-muted">All items have been returned or are consumables.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Return History --}}
        <div class="col-md-4">
            <div class="tile">
                <h4 class="tile-title"><i class="fa fa-history text-secondary"></i> My Return History</h4>
                @forelse($myReturns as $ret)
                    <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                        <div>
                            <strong>{{ $ret->productVariant->product->name ?? '—' }}</strong>
                            <br><small class="text-muted">{{ $ret->quantity }} {{ $ret->unit }} &bull;
                                {{ $ret->created_at->format('d M H:i') }}</small>
                        </div>
                        <span class="badge badge-{{ $ret->status_color }}">{{ $ret->status_label }}</span>
                    </div>
                @empty
                    <p class="text-muted text-center mt-3">No return history yet.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Return Modal --}}
    <div class="modal fade" id="returnModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title"><i class="fa fa-undo"></i> Return Item to Store</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <form id="returnForm">
                    @csrf
                    <input type="hidden" id="returnVariantId" name="product_variant_id">
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i> Returning: <strong id="returnItemName"></strong>
                        </div>
                        <div class="form-group">
                            <label><strong>Quantity to Return</strong> <span class="text-danger">*</span></label>
                            <input type="number" class="form-control form-control-lg" id="returnQty" name="quantity"
                                min="0.01" step="0.01" required placeholder="How many are you returning?">
                            <small class="text-muted">Max available: <span id="maxQty"></span></small>
                        </div>
                        <div class="form-group">
                            <label><strong>Unit</strong></label>
                            <input type="text" class="form-control" id="returnUnit" name="unit" readonly>
                        </div>
                        <div class="form-group">
                            <label>Notes <span class="text-muted">(optional)</span></label>
                            <textarea class="form-control" name="notes" rows="2"
                                placeholder="Any condition notes..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-info" id="submitReturnBtn">
                            <i class="fa fa-undo"></i> Submit Return
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('dashboard_assets/js/plugins/sweetalert.min.js') }}"></script>
    <script>
        function openReturnModal(variantId, itemName, maxQty, unit) {
            document.getElementById('returnVariantId').value = variantId;
            document.getElementById('returnItemName').textContent = itemName;
            document.getElementById('returnQty').max = maxQty;
            document.getElementById('returnQty').value = '';
            document.getElementById('maxQty').textContent = maxQty + ' ' + unit;
            document.getElementById('returnUnit').value = unit;
            $('#returnModal').modal('show');
        }

        document.getElementById('returnForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const btn = document.getElementById('submitReturnBtn');
            const qty = parseFloat(document.getElementById('returnQty').value);
            const max = parseFloat(document.getElementById('returnQty').max);
            if (qty > max) {
                swal({ title: 'Invalid Quantity', text: 'You cannot return more than you hold (' + max + ').', type: 'warning' });
                return;
            }
            btn.disabled = true;
            btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Submitting...';
            const formData = new FormData(this);
            fetch('{{ route("stock-returns.store") }}', {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            }).then(r => r.json()).then(data => {
                if (data.success) {
                    swal({ title: 'Success!', text: data.message, type: 'success' }, () => location.reload());
                } else {
                    swal({ title: 'Error', text: data.message, type: 'error' });
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fa fa-undo"></i> Submit Return';
                }
            }).catch(() => {
                swal({ title: 'Error', text: 'Could not submit. Please try again.', type: 'error' });
                btn.disabled = false;
                btn.innerHTML = '<i class="fa fa-undo"></i> Submit Return';
            });
        });
    </script>
@endsection
