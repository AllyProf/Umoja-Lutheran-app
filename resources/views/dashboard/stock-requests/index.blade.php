@extends('dashboard.layouts.app')

@php
    $type = $type ?? request('type');
    if (in_array($type, ['drink', 'food'], true)) {
        $type = 'restaurant';
    }
    if (!in_array($type, ['restaurant', 'housekeeping'], true)) {
        $type = null;
    }
    $status = request('status');
    $userRole = strtolower(auth()->guard('staff')->user()->role ?? '');
    $title = match ($type) {
        'restaurant' => 'Restaurant Stock',
        'housekeeping' => 'Housekeeping Stock',
        default => 'All Stock Issues',
    };
    $statusCounts = $statusCounts ?? [];
    $manualTransfers = $manualTransfers ?? collect();
@endphp

@section('content')
<div class="app-title">
    <div>
        <h1><i class="fa fa-exchange"></i> {{ $title }}</h1>
        <p>Department requests and items you distributed manually</p>
    </div>
    <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
        <li class="breadcrumb-item"><a href="#">Stock</a></li>
        <li class="breadcrumb-item">{{ $title }}</li>
    </ul>
</div>

@if(in_array($userRole, ['bar_keeper', 'bar keeper', 'head_chef', 'housekeeper'], true))
    <div class="mb-3 text-right">
        <a href="{{ route('stock-requests.create') }}" class="btn btn-primary">
            <i class="fa fa-plus"></i> New Request
        </a>
    </div>
@endif

@if(in_array($userRole, ['storekeeper', 'manager', 'super_admin'], true) && Route::has('storekeeper.transfers.create'))
    <div class="mb-3 text-right">
        <a href="{{ route('storekeeper.transfers.create') }}" class="btn btn-outline-primary">
            <i class="fa fa-truck"></i> Distribute manually
        </a>
    </div>
@endif

{{-- Department tabs --}}
<ul class="nav nav-pills mb-3">
    <li class="nav-item">
        <a class="nav-link {{ !$type ? 'active' : '' }}"
            href="{{ route('stock-requests.index', array_filter(['status' => $status])) }}">
            All
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $type === 'restaurant' ? 'active' : '' }}"
            href="{{ route('stock-requests.index', array_filter(['type' => 'restaurant', 'status' => $status])) }}">
            <i class="fa fa-cutlery"></i> Restaurant
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $type === 'housekeeping' ? 'active' : '' }}"
            href="{{ route('stock-requests.index', array_filter(['type' => 'housekeeping', 'status' => $status])) }}">
            <i class="fa fa-bed"></i> Housekeeping
        </a>
    </li>
</ul>

{{-- ========== REQUESTS ========== --}}
<div class="row mb-3">
    <div class="col-md-12">
        <div class="tile">
            <div class="tile-title-w-btn">
                <h3 class="title"><i class="fa fa-paper-plane text-primary"></i> Department requests</h3>
            </div>

            <ul class="nav nav-tabs mb-3 flex-wrap">
                <li class="nav-item">
                    <a class="nav-link {{ !$status ? 'active' : '' }}"
                        href="{{ route('stock-requests.index', array_filter(['type' => $type])) }}">
                        All
                        <span class="badge badge-secondary ml-1">{{ $statusCounts['all'] ?? 0 }}</span>
                    </a>
                </li>
                @if(($statusCounts['pending_manager'] ?? 0) > 0 || $status === 'pending_manager')
                    <li class="nav-item">
                        <a class="nav-link {{ $status === 'pending_manager' ? 'active' : '' }}"
                            href="{{ route('stock-requests.index', array_filter(['type' => $type, 'status' => 'pending_manager'])) }}">
                            Awaiting manager
                            <span class="badge badge-info ml-1">{{ $statusCounts['pending_manager'] ?? 0 }}</span>
                        </a>
                    </li>
                @endif
                @if(($statusCounts['pending_accountant'] ?? 0) > 0 || $status === 'pending_accountant')
                    <li class="nav-item">
                        <a class="nav-link {{ $status === 'pending_accountant' ? 'active' : '' }}"
                            href="{{ route('stock-requests.index', array_filter(['type' => $type, 'status' => 'pending_accountant'])) }}">
                            Awaiting accountant
                            <span class="badge badge-warning ml-1">{{ $statusCounts['pending_accountant'] ?? 0 }}</span>
                        </a>
                    </li>
                @endif
                <li class="nav-item">
                    <a class="nav-link {{ $status === 'approved' ? 'active' : '' }}"
                        href="{{ route('stock-requests.index', array_filter(['type' => $type, 'status' => 'approved'])) }}">
                        Ready to issue
                        <span class="badge badge-primary ml-1">{{ $statusCounts['approved'] ?? 0 }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $status === 'completed' ? 'active' : '' }}"
                        href="{{ route('stock-requests.index', array_filter(['type' => $type, 'status' => 'completed'])) }}">
                        Completed
                        <span class="badge badge-success ml-1">{{ $statusCounts['completed'] ?? 0 }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $status === 'rejected' ? 'active' : '' }}"
                        href="{{ route('stock-requests.index', array_filter(['type' => $type, 'status' => 'rejected'])) }}">
                        Rejected
                        <span class="badge badge-danger ml-1">{{ $statusCounts['rejected'] ?? 0 }}</span>
                    </a>
                </li>
            </ul>

            <div class="table-responsive">
                <table class="table table-hover table-bordered table-striped">
                    <thead class="thead-light">
                        <tr>
                            <th style="width:70px">#</th>
                            <th style="width:130px">Date</th>
                            <th>From</th>
                            <th>Product</th>
                            <th class="text-center">Qty</th>
                            <th>Cost</th>
                            <th>Status</th>
                            <th style="min-width:160px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $currentBatch = null; @endphp
                        @forelse($stockRequests as $request)
                            @php
                                $product = $request->productVariant->product ?? null;
                                $variant = $request->productVariant;
                                $productName = $product->name ?? $product->product_name ?? null;
                                $requesterRole = strtolower(str_replace([' ', '-'], '_', $request->requester->role ?? ''));
                                $department = match (true) {
                                    in_array($requesterRole, ['bar_keeper', 'bartender'], true) => 'Restaurant (Bar)',
                                    in_array($requesterRole, ['head_chef', 'chef'], true) => 'Restaurant (Kitchen)',
                                    $requesterRole === 'housekeeper' => 'Housekeeping',
                                    default => ucfirst(str_replace('_', ' ', $requesterRole ?: 'Department')),
                                };
                                $unitLabel = $request->unit;
                                if ($unitLabel === 'packages') {
                                    $unitLabel = $variant->packaging_name ?? 'packages';
                                } elseif ($unitLabel === 'bottles') {
                                    $unitLabel = 'pcs';
                                }
                                $hasCost = (float) ($request->unit_cost ?? 0) > 0 || (float) ($request->total_cost ?? 0) > 0;
                            @endphp

                            @if($request->batch_id && $request->batch_id !== $currentBatch)
                                @php $currentBatch = $request->batch_id; @endphp
                                <tr class="table-secondary">
                                    <td colspan="7" class="py-2">
                                        <i class="fa fa-folder-open text-warning"></i>
                                        <strong>
                                            Batch
                                            {{ $request->batch_reference ?: ('#' . \Illuminate\Support\Str::limit($request->batch_id, 8, '')) }}
                                        </strong>
                                        <small class="text-muted ml-2">
                                            {{ optional($request->created_at)->format('d M Y H:i') }}
                                        </small>
                                    </td>
                                    <td class="text-right py-1">
                                        @if($userRole === 'storekeeper' && $request->status === 'approved')
                                            <a href="{{ route('stock-requests.batch-distribute', $request->batch_id) }}"
                                                class="btn btn-xs btn-primary">
                                                <i class="fa fa-truck"></i> Issue batch
                                            </a>
                                        @endif
                                        @if($request->status === 'completed')
                                            <a href="{{ route('stock-requests.batch-print', $request->batch_id) }}"
                                                class="btn btn-xs btn-secondary" target="_blank">
                                                <i class="fa fa-print"></i> Print
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endif

                            <tr>
                                <td><strong>#{{ $request->id }}</strong></td>
                                <td>
                                    {{ optional($request->created_at)->format('d M Y') }}
                                    <div class="small text-muted">{{ optional($request->created_at)->format('H:i') }}</div>
                                </td>
                                <td>
                                    <strong>{{ $request->requester->name ?? 'Unknown staff' }}</strong>
                                    <div class="small text-muted">{{ $department }}</div>
                                </td>
                                <td>
                                    @if($productName)
                                        <strong>{{ $productName }}</strong>
                                        @if($variant && $variant->variant_name)
                                            <div class="small text-muted">{{ $variant->variant_name }}</div>
                                        @endif
                                    @else
                                        <span class="text-danger">
                                            <i class="fa fa-exclamation-triangle"></i> Product unavailable
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-light border text-dark">
                                        {{ rtrim(rtrim(number_format((float) $request->quantity, 2), '0'), '.') }}
                                        {{ $unitLabel }}
                                    </span>
                                    @if($request->quantity_issued)
                                        <div class="small text-success mt-1">
                                            Issued: {{ rtrim(rtrim(number_format((float) $request->quantity_issued, 2), '0'), '.') }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @if($hasCost)
                                        @if((float) $request->unit_cost > 0)
                                            <div class="small text-muted">Unit: TZS {{ number_format($request->unit_cost) }}</div>
                                        @endif
                                        <strong class="text-primary">
                                            TZS {{ number_format($request->total_cost ?: ($request->unit_cost * $request->quantity)) }}
                                        </strong>
                                    @elseif(in_array($request->status, ['approved', 'pending_manager', 'pending_accountant'], true))
                                        <span class="badge badge-light text-muted border" title="Price is entered when items are issued">
                                            Cost not set yet
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-{{ $request->status_color }}">
                                        {{ $request->status_label }}
                                    </span>
                                </td>
                                <td>
                                    @if($userRole === 'accountant' && $request->status === 'pending_accountant')
                                        <form action="{{ route('stock-requests.pass-to-manager', $request) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-info text-white confirm-submit mb-1"
                                                data-confirm="Forward this request to the manager?">
                                                <i class="fa fa-share"></i> To manager
                                            </button>
                                        </form>
                                    @endif

                                    @if(in_array($userRole, ['manager', 'super_admin'], true) && in_array($request->status, ['pending_manager', 'pending_accountant'], true))
                                        <form action="{{ route('stock-requests.approve', $request) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success confirm-submit mb-1"
                                                data-confirm="Approve this request?">
                                                <i class="fa fa-check"></i> Approve
                                            </button>
                                        </form>
                                    @endif

                                    @if(
                                        ($userRole === 'accountant' && $request->status === 'pending_accountant') ||
                                        (in_array($userRole, ['manager', 'super_admin'], true) && in_array($request->status, ['pending_manager', 'pending_accountant'], true))
                                    )
                                        <button type="button" class="btn btn-sm btn-outline-danger mb-1" data-toggle="modal"
                                            data-target="#rejectModal{{ $request->id }}">
                                            <i class="fa fa-times"></i> Reject
                                        </button>
                                        <div class="modal fade" id="rejectModal{{ $request->id }}" tabindex="-1" role="dialog">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <form action="{{ route('stock-requests.reject', $request) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Reject request #{{ $request->id }}</h5>
                                                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <label>Reason</label>
                                                            <textarea name="rejection_reason" class="form-control" rows="3" required></textarea>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-danger">Reject</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    @if($userRole === 'storekeeper' && $request->status === 'approved')
                                        <button type="button" class="btn btn-sm btn-primary mb-1" data-toggle="modal"
                                            data-target="#distributeModal{{ $request->id }}">
                                            <i class="fa fa-truck"></i> Issue
                                        </button>
                                        <div class="modal fade" id="distributeModal{{ $request->id }}" tabindex="-1" role="dialog">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <form action="{{ route('stock-requests.distribute', $request) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-header bg-primary text-white">
                                                            <h5 class="modal-title">Issue items — #{{ $request->id }}</h5>
                                                            <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="alert alert-light border mb-3">
                                                                <strong>Item:</strong> {{ $productName ?: 'Product unavailable' }}
                                                                @if($variant && $variant->variant_name) ({{ $variant->variant_name }}) @endif
                                                                <br>
                                                                <strong>Requested by:</strong> {{ $request->requester->name ?? 'Unknown' }}<br>
                                                                <strong>Qty requested:</strong>
                                                                {{ rtrim(rtrim(number_format((float) $request->quantity, 2), '0'), '.') }} {{ $unitLabel }}
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Quantity to issue <span class="text-danger">*</span></label>
                                                                <input type="number" step="0.01" min="0.01" name="quantity_issued"
                                                                    class="form-control form-control-lg" value="{{ $request->quantity }}" required>
                                                            </div>
                                                            <div class="form-group mb-0">
                                                                <label>Unit price (TZS) <span class="text-danger">*</span></label>
                                                                @php $suggestedCost = $request->unit_cost > 0 ? $request->unit_cost : null; @endphp
                                                                <input type="number" step="0.01" min="0" name="unit_cost"
                                                                    class="form-control form-control-lg" value="{{ $suggestedCost }}" required>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-success">
                                                                <i class="fa fa-check"></i> Issue &amp; print note
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    @if($request->status === 'completed' && $request->stock_transfer_id)
                                        <a href="{{ route('stock-requests.print', $request) }}"
                                            class="btn btn-sm btn-outline-secondary mb-1" target="_blank">
                                            <i class="fa fa-print"></i> Note
                                        </a>
                                    @endif

                                    @if($request->status === 'rejected' && $request->rejection_reason)
                                        <button type="button" class="btn btn-sm btn-outline-danger mb-1"
                                            data-toggle="tooltip" title="{{ $request->rejection_reason }}">
                                            <i class="fa fa-info-circle"></i> Reason
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    No department requests for this filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @include('dashboard.partials.bootstrap-pagination', ['paginator' => $stockRequests])
        </div>
    </div>
</div>

{{-- ========== MANUAL DISTRIBUTES ========== --}}
<div class="row">
    <div class="col-md-12">
        <div class="tile">
            <div class="tile-title-w-btn">
                <h3 class="title"><i class="fa fa-truck text-success"></i> Manual distributes</h3>
                @if(in_array($userRole, ['storekeeper', 'manager', 'super_admin'], true) && Route::has('storekeeper.transfers.create'))
                    <a href="{{ route('storekeeper.transfers.create') }}" class="btn btn-sm btn-outline-success">
                        <i class="fa fa-plus"></i> New distribute
                    </a>
                @endif
            </div>
            <p class="text-muted small mb-3">
                Items sent directly from the store to Restaurant or Housekeeping (without a department request).
            </p>

            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th>Ref</th>
                            <th>Date</th>
                            <th>Department</th>
                            <th>Product</th>
                            <th class="text-center">Qty</th>
                            <th>Issued by</th>
                            <th>Received by</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($manualTransfers as $transfer)
                            @php
                                $dept = str_contains(strtolower($transfer->notes ?? ''), 'housekeeping')
                                    ? 'Housekeeping'
                                    : 'Restaurant';
                                $unit = $transfer->quantity_unit === 'packages'
                                    ? ($transfer->productVariant->packaging_name ?? 'packages')
                                    : 'pcs';
                            @endphp
                            <tr>
                                <td>
                                    <strong>{{ $transfer->transfer_reference }}</strong>
                                    <div><span class="badge badge-success badge-pill">Manual</span></div>
                                </td>
                                <td>{{ optional($transfer->transfer_date)->format('d M Y') }}</td>
                                <td>
                                    <span class="badge badge-{{ $dept === 'Housekeeping' ? 'secondary' : 'info' }}">
                                        {{ $dept }}
                                    </span>
                                </td>
                                <td>
                                    <strong>{{ $transfer->product->name ?? 'Product' }}</strong>
                                    @if($transfer->productVariant && $transfer->productVariant->variant_name)
                                        <div class="small text-muted">{{ $transfer->productVariant->variant_name }}</div>
                                    @endif
                                </td>
                                <td class="text-center">
                                    {{ rtrim(rtrim(number_format((float) $transfer->quantity_transferred, 2), '0'), '.') }}
                                    {{ $unit }}
                                </td>
                                <td>{{ $transfer->transferredBy->name ?? 'Storekeeper' }}</td>
                                <td>{{ $transfer->receivedBy->name ?? '—' }}</td>
                                <td>
                                    <span class="badge badge-{{ $transfer->status === 'completed' ? 'success' : 'warning' }}">
                                        {{ ucfirst($transfer->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    No manual distributes yet for this filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @include('dashboard.partials.bootstrap-pagination', ['paginator' => $manualTransfers])
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip();

        $('.confirm-submit').on('click', function (e) {
            e.preventDefault();
            var $button = $(this);
            var message = $button.data('confirm') || 'Are you sure you want to proceed?';
            var $form = $button.closest('form');

            if (typeof showConfirmDialog === 'function') {
                showConfirmDialog('Confirm Action', message, 'Yes, proceed', 'Cancel', function (result) {
                    if (result.isConfirmed) {
                        $form.submit();
                    }
                });
            } else if (confirm(message)) {
                $form.submit();
            }
        });
    });
</script>
@endsection
