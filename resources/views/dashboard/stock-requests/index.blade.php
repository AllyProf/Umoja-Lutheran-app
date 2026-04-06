@extends('dashboard.layouts.app')

@php
    $type = request('type');
    $title = 'All Stock Requests';
    if ($type === 'drink')
        $title = 'Beverage Requests';
    elseif ($type === 'food')
        $title = 'Kitchen Requests';
    elseif ($type === 'housekeeping')
        $title = 'Housekeeping Requests';
@endphp

@section('title', $title)

@section('content')
    <div class="container-fluid">
        <div class="row page-titles">
            <div class="col-md-5 align-self-center">
                <h4 class="text-themecolor">{{ $title }}</h4>
            </div>
            <div class="col-md-7 align-self-center text-end">
                <div class="d-flex justify-content-end align-items-center">
                    <ol class="breadcrumb justify-content-end">
                        <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
                        <li class="breadcrumb-item active">{{ $title }}</li>
                    </ol>
                    @php $userRole = Auth::guard('staff')->user()->role; @endphp
                    @if(in_array($userRole, ['bar_keeper', 'bar keeper', 'head_chef', 'housekeeper']))
                        <a href="{{ route('stock-requests.create') }}" class="btn btn-info d-none d-lg-block m-l-15 text-white">
                            <i class="fa fa-plus-circle"></i> New Request
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">All Stock Requests</h4>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Date</th>
                                        <th>Requester</th>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Financials</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $currentBatch = null; @endphp
                                    @forelse($stockRequests as $request)
                                        @if($request->batch_id && $request->batch_id !== $currentBatch)
                                            @php $currentBatch = $request->batch_id; @endphp
                                            <tr class="table-light">
                                                <td colspan="7" class="py-2">
                                                    <i class="fa fa-folder-open text-warning"></i>
                                                    <strong>Batch: {{ $request->batch_reference ?? 'N/A' }}</strong>
                                                    <small
                                                        class="text-muted ml-2">({{ $request->created_at->format('M d, Y H:i') }})</small>
                                                </td>
                                                <td class="text-end py-1">
                                                    @if(Auth::guard('staff')->user()->role === 'storekeeper' && $request->status === 'approved')
                                                        <a href="{{ route('stock-requests.batch-distribute', $request->batch_id) }}"
                                                            class="btn btn-xs btn-primary shadow-sm">
                                                            <i class="fa fa-truck"></i> Distribute Batch
                                                        </a>
                                                    @endif
                                                    @if($request->status === 'completed')
                                                        <a href="{{ route('stock-requests.batch-print', $request->batch_id) }}"
                                                            class="btn btn-xs btn-secondary shadow-sm" target="_blank">
                                                            <i class="fa fa-print"></i> Print Batch Receipt
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <td>{{ $request->id }}</td>
                                            <td>{{ $request->created_at ? $request->created_at->format('M d, Y H:i') : 'N/A' }}
                                            </td>
                                            <td>{{ $request->requester->name ?? 'Unknown' }}</td>
                                            <td>
                                                <strong>{{ $request->productVariant->product->name ?? $request->productVariant->product->product_name ?? 'N/A' }}</strong><br>
                                                <small class="text-muted">
                                                    @if(($request->productVariant->product->category ?? '') === 'cleaning_supplies')
                                                        {{ trim(str_ireplace(['(ml)', 'ml', '(l)', 'l'], '', $request->productVariant->variant_name ?? '')) }}
                                                    @else
                                                        {{ $request->productVariant->variant_name ?? '' }}
                                                    @endif
                                                </small>
                                            </td>
                                            <td>
                                                <span class="badge badge-pill badge-light text-dark">
                                                    {{ number_format($request->quantity, 1) }}
                                                    @php
                                                        $unit = $request->unit;
                                                        if ($unit === 'packages') {
                                                            $unit = $request->productVariant->packaging_name ?? 'Crates';
                                                        } elseif ($unit === 'bottles') {
                                                            $unit = 'Individual Units';
                                                        }
                                                    @endphp
                                                    {{ $unit }}
                                                </span>
                                            </td>
                                            <td>
                                                @php
                                                    $reqCategory = $request->productVariant->product->category ?? '';
                                                    $beverageCats = ['spirits', 'wines', 'non_alcoholic_beverage', 'alcoholic_beverage', 'energy_drinks', 'juices', 'water', 'hot_beverages', 'cocktails'];
                                                    $reqRole = strtolower(str_replace(' ', '_', $request->requester->role ?? ''));
                                                    $isKitchenOrHouse = in_array($reqRole, ['head_chef', 'housekeeper']);
                                                    // Only show beverage financials if role is bar_keeper AND product is a beverage
                                                    $barRoles = ['bar_keeper', 'bar keeper', 'bartender'];
                                                    $reqIsBeverage = in_array($reqRole, $barRoles) && in_array($reqCategory, $beverageCats);
                                                @endphp
                                                @if($reqIsBeverage)
                                                    @php
                                                        $revenue = $request->expected_revenue;
                                                        $cost = $request->total_cost > 0 ? $request->total_cost : ($request->buying_price * $request->total_bottles);
                                                        $profit = $revenue - $cost;
                                                    @endphp
                                                    <small class="text-muted">Rev: {{ number_format($revenue, 2) }}</small><br>
                                                    <small class="text-muted">Cost: {{ number_format($cost, 2) }}</small><br>
                                                    <strong class="{{ $profit >= 0 ? 'text-success' : 'text-danger' }}"
                                                        style="font-size: 0.85em;">
                                                        Profit: {{ number_format($profit, 2) }}
                                                    </strong>
                                                @elseif($isKitchenOrHouse)
                                                    @if($request->status === 'completed')
                                                        <span class="text-muted">—</span>
                                                    @elseif($request->unit_cost > 0)
                                                        <small class="text-muted">Unit:
                                                            {{ number_format($request->unit_cost, 2) }}</small><br>
                                                        <strong class="text-primary" style="font-size: 0.9em;">Total:
                                                            {{ number_format($request->total_cost, 2) }}</strong>
                                                    @else
                                                        <span class="badge badge-light text-muted">N/A (Legacy)</span>
                                                    @endif
                                                @else
                                                    @if($request->unit_cost > 0)
                                                        <small class="text-muted">Unit:
                                                            {{ number_format($request->unit_cost, 2) }}</small><br>
                                                        <strong class="text-primary" style="font-size: 0.9em;">Total:
                                                            {{ number_format($request->total_cost, 2) }}</strong>
                                                    @else
                                                        <span class="badge badge-light text-muted">N/A</span>
                                                    @endif
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $request->status_color }}">
                                                    {{ $request->status_label }}
                                                </span>
                                            </td>
                                            <td>
                                                {{-- Accountant Actions --}}
                                                @if(Auth::guard('staff')->user()->role === 'accountant' && $request->status === 'pending_accountant')
                                                    <form action="{{ route('stock-requests.pass-to-manager', $request) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-info text-white confirm-submit"
                                                            data-confirm="Forward this request to Manager?">
                                                            <i class="fa fa-share"></i> Pass to Manager
                                                        </button>
                                                    </form>
                                                @endif

                                                {{-- Manager Actions --}}
                                                @if((Auth::guard('staff')->user()->isManager() || Auth::guard('staff')->user()->isSuperAdmin()) && in_array($request->status, ['pending_manager', 'pending_accountant']))
                                                    <form action="{{ route('stock-requests.approve', $request) }}" method="POST"
                                                        class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success confirm-submit"
                                                            data-confirm="Approve this request?">
                                                            <i class="fa fa-check"></i> Approve
                                                        </button>
                                                    </form>
                                                @endif

                                                {{-- Global Reject Action for Accountant/Manager --}}
                                                @if(
                                                        (Auth::guard('staff')->user()->role === 'accountant' && $request->status === 'pending_accountant') ||
                                                        ((Auth::guard('staff')->user()->isManager() || Auth::guard('staff')->user()->isSuperAdmin()) && in_array($request->status, ['pending_manager', 'pending_accountant']))
                                                    )
                                                    <button type="button" class="btn btn-sm btn-danger" data-toggle="modal"
                                                        data-target="#rejectModal{{ $request->id }}">
                                                        <i class="fa fa-close"></i> Reject Request
                                                    </button>

                                                    <!-- Reject Modal -->
                                                    <div class="modal fade" id="rejectModal{{ $request->id }}" tabindex="-1"
                                                        role="dialog" aria-hidden="true">
                                                        <div class="modal-dialog" role="document">
                                                            <div class="modal-content">
                                                                <form action="{{ route('stock-requests.reject', $request) }}"
                                                                    method="POST">
                                                                    @csrf
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">Reject Stock Request
                                                                            #{{ $request->id }}</h5>
                                                                        <button type="button" class="close" data-dismiss="modal"
                                                                            aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="form-group">
                                                                            <label>Reason for Rejection</label>
                                                                            <textarea name="rejection_reason" class="form-control"
                                                                                rows="3" required></textarea>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary"
                                                                            data-dismiss="modal">Cancel</button>
                                                                        <button type="submit" class="btn btn-danger">Reject
                                                                            Request</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                {{-- Storekeeper Actions --}}
                                                @if(Auth::guard('staff')->user()->role === 'storekeeper' && $request->status === 'approved')
                                                    <button type="button" class="btn btn-sm btn-primary" data-toggle="modal"
                                                        data-target="#distributeModal{{ $request->id }}">
                                                        <i class="fa fa-truck"></i> Issue Items
                                                    </button>

                                                    <!-- Distribution Modal -->
                                                    <div class="modal fade" id="distributeModal{{ $request->id }}" tabindex="-1"
                                                        role="dialog" aria-hidden="true">
                                                        <div class="modal-dialog" role="document">
                                                            <div class="modal-content">
                                                                <form action="{{ route('stock-requests.distribute', $request) }}"
                                                                    method="POST">
                                                                    @csrf
                                                                    <div class="modal-header bg-primary text-white">
                                                                        <h5 class="modal-title"><i class="fa fa-file-text"></i>
                                                                            Issue Items — Requisition #{{ $request->id }}</h5>
                                                                        <button type="button" class="close text-white"
                                                                            data-dismiss="modal"><span>&times;</span></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="alert alert-light border mb-3">
                                                                            <strong>Item:</strong>
                                                                            {{ $request->productVariant->product->name ?? 'N/A' }}
                                                                            {{ $request->productVariant->variant_name ? '(' . $request->productVariant->variant_name . ')' : '' }}<br>
                                                                            <strong>Requested By:</strong>
                                                                            {{ $request->requester->name ?? 'N/A' }}<br>
                                                                            <strong>Qty Requested:</strong>
                                                                            {{ number_format($request->quantity, 1) }}
                                                                            {{ $request->unit }}
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label><strong>Quantity to Issue <span
                                                                                        class="text-danger">*</span></strong></label>
                                                                            <input type="number" step="0.01" min="0.01"
                                                                                name="quantity_issued"
                                                                                class="form-control form-control-lg"
                                                                                value="{{ $request->quantity }}" required
                                                                                placeholder="Qty Issued">
                                                                            <small class="text-muted">You can issue less than
                                                                                requested if stock is limited.</small>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label><strong>Unit Price (TSH) <span
                                                                                        class="text-danger">*</span></strong></label>
                                                                            @php
                                                                                $suggestedCost = $request->unit_cost > 0 ? $request->unit_cost : null;
                                                                            @endphp
                                                                            <input type="number" step="0.01" min="0"
                                                                                name="unit_cost"
                                                                                class="form-control form-control-lg unit-cost-input"
                                                                                value="{{ $suggestedCost }}" required
                                                                                placeholder="Unit Price">
                                                                            @if($suggestedCost)
                                                                                <small class="text-success"><i
                                                                                        class="fa fa-info-circle"></i> Suggested price
                                                                                    from last purchase.</small>
                                                                            @endif
                                                                        </div>
                                                                        <div class="alert alert-info"
                                                                            id="totalDisplay{{ $request->id }}">
                                                                            <strong>Total Amount:</strong> <span
                                                                                class="total-amount">—</span> TSH
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary"
                                                                            data-dismiss="modal">Cancel</button>
                                                                        <button type="submit" class="btn btn-success">
                                                                            <i class="fa fa-check"></i> Issue & Print Note
                                                                        </button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                @if($request->status === 'completed' && $request->stock_transfer_id)
                                                    <a href="{{ route('stock-requests.print', $request) }}"
                                                        class="btn btn-sm btn-outline-secondary" target="_blank">
                                                        <i class="fa fa-print"></i> Requisition Note
                                                    </a>
                                                @endif

                                                @if($request->status === 'rejected')
                                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                                        data-toggle="tooltip" title="{{ $request->rejection_reason }}">
                                                        <i class="fa fa-info-circle"></i> View Reason
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">No stock requests found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if($stockRequests->hasPages())
                            <div class="m-t-20">
                                {{ $stockRequests->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
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
                } else {
                    if (confirm(message)) {
                        $form.submit();
                    }
                }
            });
        });
    </script>
@endsection