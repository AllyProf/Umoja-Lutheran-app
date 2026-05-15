@extends('dashboard.layouts.app')

@section('content')
    <div class="app-title">
        <div>
            <h1><i class="fa fa-undo"></i> Receive Returned Items</h1>
            <p>Process housekeeping item returns — stock is updated on receipt</p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="{{ route('storekeeper.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Item Returns</li>
        </ul>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fa fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div class="row">
        {{-- Pending Returns --}}
        <div class="col-md-12">
            <div class="tile">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="tile-title mb-0">
                        <i class="fa fa-clock text-warning"></i> Pending Returns
                        @if($pendingReturns->count() > 0)
                            <span class="badge badge-warning ml-2">{{ $pendingReturns->count() }}</span>
                        @endif
                    </h4>
                </div>
                @if($pendingReturns->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Item</th>
                                    <th>Returned By</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-center">Unit</th>
                                    <th>Notes</th>
                                    <th>Submitted</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingReturns as $ret)
                                    <tr id="row-{{ $ret->id }}">
                                        <td>
                                            <strong>{{ $ret->productVariant->product->name ?? '—' }}</strong><br>
                                            <small
                                                class="text-muted">{{ preg_replace('/\s*\(?\s*ml\s*\)?\s*/i', '', $ret->productVariant->variant_name ?? '') }}</small>
                                        </td>
                                        <td>
                                            <i class="fa fa-user-circle text-info"></i>
                                            {{ $ret->staff->name ?? '—' }}
                                        </td>
                                        <td class="text-center"><strong class="text-success">{{ $ret->quantity }}</strong></td>
                                        <td class="text-center"><span class="badge badge-light">{{ $ret->unit }}</span></td>
                                        <td>
                                            @if($ret->notes)
                                                <span class="text-muted small">{{ $ret->notes }}</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td><small>{{ $ret->created_at->format('d M, H:i') }}</small></td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-success mr-1"
                                                onclick="processReturn({{ $ret->id }}, 'receive')">
                                                <i class="fa fa-check"></i> Receive
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger" onclick="openRejectModal({{ $ret->id }})">
                                                <i class="fa fa-times"></i> Reject
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fa fa-check-circle fa-4x text-success mb-3" style="opacity:0.5;"></i>
                        <h5 class="text-muted">No pending returns at this time.</h5>
                    </div>
                @endif
            </div>
        </div>

        {{-- Recently Received --}}
        <div class="col-md-12 mt-2">
            <div class="tile">
                <h4 class="tile-title"><i class="fa fa-history text-secondary"></i> Recently Received</h4>
                @if($recentlyReceived->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead class="thead-light">
                                <tr>
                                    <th>Item</th>
                                    <th>Returned By</th>
                                    <th class="text-center">Qty</th>
                                    <th>Received By</th>
                                    <th>Received At</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentlyReceived as $ret)
                                    <tr>
                                        <td>{{ $ret->productVariant->product->name ?? '—' }}</td>
                                        <td>{{ $ret->staff->name ?? '—' }}</td>
                                        <td class="text-center">{{ $ret->quantity }} {{ $ret->unit }}</td>
                                        <td>{{ $ret->receiver->name ?? '—' }}</td>
                                        <td><small>{{ $ret->received_at ? $ret->received_at->format('d M H:i') : '—' }}</small></td>
                                        <td class="text-center">
                                            <span class="badge badge-{{ $ret->status_color }}">{{ $ret->status_label }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center mt-3">No recent return history.</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Reject Modal --}}
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fa fa-times-circle"></i> Reject Return</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <form id="rejectForm">
                    @csrf
                    <input type="hidden" id="rejectReturnId">
                    <div class="modal-body">
                        <div class="form-group">
                            <label><strong>Reason for Rejection</strong> <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="rejection_reason" rows="3" required
                                placeholder="e.g. Item is damaged, partial quantity only, wrong item..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger" id="submitRejectBtn">
                            <i class="fa fa-times"></i> Reject Return
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
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        function processReturn(id, action) {
            swal({
                title: 'Confirm Receipt',
                text: 'Mark this item as received? The store inventory will be updated.',
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Receive it!',
                confirmButtonColor: '#28a745',
            }, function (confirmed) {
                if (!confirmed) return;
                fetch(`/storekeeper/stock-returns/${id}/receive`, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken }
                }).then(r => r.json()).then(data => {
                    if (data.success) {
                        swal({ title: 'Received!', text: data.message, type: 'success' }, () => {
                            const row = document.getElementById('row-' + id);
                            if (row) row.remove();
                        });
                    } else {
                        swal({ title: 'Error', text: data.message, type: 'error' });
                    }
                });
            });
        }

        function openRejectModal(id) {
            document.getElementById('rejectReturnId').value = id;
            document.querySelector('#rejectForm textarea').value = '';
            $('#rejectModal').modal('show');
        }

        document.getElementById('rejectForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const id = document.getElementById('rejectReturnId').value;
            const btn = document.getElementById('submitRejectBtn');
            btn.disabled = true;
            const formData = new FormData(this);
            fetch(`/storekeeper/stock-returns/${id}/reject`, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken }
            }).then(r => r.json()).then(data => {
                if (data.success) {
                    $('#rejectModal').modal('hide');
                    swal({ title: 'Rejected', text: data.message, type: 'info' }, () => {
                        const row = document.getElementById('row-' + id);
                        if (row) row.remove();
                    });
                } else {
                    swal({ title: 'Error', text: data.message, type: 'error' });
                    btn.disabled = false;
                }
            });
        });
    </script>
@endsection
