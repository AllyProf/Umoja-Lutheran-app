@extends('dashboard.layouts.app')

@section('content')
    <div class="app-title">
        <div>
            <h1><i class="fa fa-money"></i> Payment Verification</h1>
            <p>Verify and reconcile actual purchase costs against budgets</p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="{{ route('accountant.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item">Payment Verification</li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <ul class="nav nav-tabs mb-4">
                    <li class="nav-item">
                        <a class="nav-link {{ $tab === 'unverified' ? 'active' : '' }}"
                            href="{{ route('accountant.payments', ['tab' => 'unverified']) }}">
                            Awaiting Verification <span
                                class="badge badge-warning ml-1">{{ $tab === 'unverified' ? $lists->total() : '' }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $tab === 'verified' ? 'active' : '' }}"
                            href="{{ route('accountant.payments', ['tab' => 'verified']) }}">
                            Verified Payments
                        </a>
                    </li>
                </ul>

                <div class="tile-title-w-btn mb-3">
                    <h3 class="title">
                        @if($tab === 'unverified') Purchased Shopping Lists Awaiting Reconciliation
                        @else History of Verified Payments @endif
                    </h3>
                </div>

                @if($lists->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>List Name</th>
                                    <th>Approved Budget</th>
                                    <th>Actual Cost (Market)</th>
                                    <th>Variance</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lists as $i => $list)
                                    @php
                                        $variance = ($list->budget_amount ?? 0) - ($list->total_actual_cost ?? 0);
                                    @endphp
                                    <tr>
                                        <td>{{ $lists->firstItem() + $i }}</td>
                                        <td><strong>{{ $list->name }}</strong><br><small
                                                class="text-muted">{{ $list->items->count() }} items</small></td>
                                        <td>TZS {{ number_format($list->budget_amount) }}</td>
                                        <td>TZS {{ number_format($list->total_actual_cost) }}<br><small class="text-muted">Market:
                                                {{ $list->market_name }}</small></td>
                                        <td class="{{ $variance < 0 ? 'text-danger' : 'text-success' }}">
                                            {{ $variance < 0 ? '-' : '+' }} TZS {{ number_format(abs($variance)) }}
                                        </td>
                                        <td>{{ $list->updated_at->format('M d, Y') }}</td>
                                        <td>
                                            @if($tab === 'unverified')
                                                <button type="button" class="btn btn-sm btn-success" data-toggle="modal"
                                                    data-target="#verifyModal{{ $list->id }}">
                                                    <i class="fa fa-check"></i> Verify
                                                </button>
                                            @else
                                                <button class="btn btn-sm btn-outline-secondary" disabled><i class="fa fa-lock"></i>
                                                    Verified</button>
                                            @endif
                                        </td>
                                    </tr>

                                    <!-- Verify Modal -->
                                    <div class="modal fade" id="verifyModal{{ $list->id }}" tabindex="-1" role="dialog"
                                        aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <form action="{{ route('accountant.payment.verify', $list->id) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Verify Payment: {{ $list->name }}</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <label>Approved Budget</label>
                                                            <input type="text" class="form-control"
                                                                value="TZS {{ number_format($list->budget_amount) }}" disabled>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Final Actual Cost (TZS)</label>
                                                            <input type="number" name="actual_cost" class="form-control"
                                                                value="{{ $list->total_actual_cost }}" required step="0.01">
                                                            <small class="text-muted">Confirm the final amount spent from the
                                                                receipts.</small>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Reconciliation Notes</label>
                                                            <textarea name="payment_notes" class="form-control" rows="3"
                                                                placeholder="Reference invoice numbers, receipts, or explain budget variance..."></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-success">Verify & Finalize</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center mt-3">
                        {{ $lists->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fa fa-check-square-o fa-4x text-success mb-3"></i>
                        <h3>No Payments Awaiting Verification</h3>
                        <p class="text-muted">All purchased lists have been verified.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
