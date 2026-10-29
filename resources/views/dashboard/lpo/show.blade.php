@extends('dashboard.layouts.app')

@section('content')
    <div class="app-title">
        <div>
            <h1><i class="fa fa-info-circle"></i> LPO Budget Details: #{{ $lpo->id }}</h1>
            <p>Bi-weekly budget tracking</p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="{{ route('lpo.index') }}">LPO Budgets</a></li>
            <li class="breadcrumb-item">Details</li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="tile shadow-sm">
                <h3 class="tile-title">Budget Info</h3>
                <div class="tile-body">
                    <table class="table table-sm">
                        <tr>
                            <th>Period:</th>
                            <td>{{ $lpo->start_date->format('M d, Y') }} to {{ $lpo->end_date->format('M d, Y') }}</td>
                        </tr>
                        <tr class="bg-primary text-white">
                            <th>Total Budget:</th>
                            <td class="h5 text-white">{{ number_format($lpo->total_amount, 2) }} TZS</td>
                        </tr>
                        <tr class="bg-warning">
                            <th>Total Spent:</th>
                            <td class="h5">{{ number_format($lpo->total_spent_amount, 2) }} TZS</td>
                        </tr>
                        <tr class="bg-success text-white">
                            <th>Remaining:</th>
                            <td class="h5 text-white">{{ number_format($lpo->remaining_balance, 2) }} TZS</td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                @php
                                    $badgeClass = match ($lpo->status) {
                                        'pending' => 'badge-warning',
                                        'rejected' => 'badge-danger',
                                        'sent_to_accountant' => 'badge-info',
                                        'sent_to_manager' => 'badge-primary',
                                        'verified_by_manager' => 'badge-warning',
                                        'funds_disbursed' => 'badge-success',
                                        'closed' => 'badge-dark',
                                        'cancelled' => 'badge-danger',
                                        default => 'badge-secondary'
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }} h6">{{ ucfirst(str_replace('_', ' ', $lpo->status)) }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th>Created By:</th>
                            <td>{{ $lpo->storekeeper->name ?? 'N/A' }}</td>
                        </tr>
                        @if($lpo->accountant)
                            <tr>
                                <th>Accountant:</th>
                                <td>{{ $lpo->accountant->name }}</td>
                            </tr>
                        @endif
                    </table>
                    @if($lpo->status === 'rejected' && $lpo->rejection_reason)
                        <div class="alert alert-danger mt-3 mb-0">
                            <strong><i class="fa fa-undo"></i> Returned by accountant</strong><br>
                            <small>{{ $lpo->rejection_reason }}</small>
                        </div>
                    @endif
                    @if($lpo->notes)
                        <div class="bg-light p-2 mt-3 border rounded">
                            <strong>Budget Notes:</strong><br>
                            <small>{{ $lpo->notes }}</small>
                        </div>
                    @endif
                </div>
                <div class="tile-footer border-top pt-3 mt-4">
                    @php $role = strtolower(auth()->guard('staff')->user()->role ?? ''); @endphp

                    {{-- Storekeeper: edit & (re)send --}}
                    @if(in_array($lpo->status, ['pending', 'rejected']) && in_array($role, ['storekeeper', 'super_admin']))
                        <a href="{{ route('lpo.edit', $lpo->id) }}" class="btn btn-primary btn-block mb-2">
                            <i class="fa fa-edit"></i>
                            {{ $lpo->status === 'rejected' ? 'Revise Items' : 'Edit Items' }}
                        </a>
                        <form action="{{ route('lpo.send', $lpo->id) }}" method="POST"
                            onsubmit="return confirm('Send this budget to the accountant for review?');">
                            @csrf
                            <button type="submit" class="btn btn-success btn-block">
                                <i class="fa fa-paper-plane"></i>
                                {{ $lpo->status === 'rejected' ? 'Resend to Accountant' : 'Send to Accountant' }}
                            </button>
                        </form>
                    @endif

                    {{-- Accountant review: adjust / accept / reject --}}
                    @if($lpo->status === 'sent_to_accountant' && in_array($role, ['accountant', 'super_admin', 'manager']))
                        <div class="alert alert-info small py-2">
                            Review this budget. Adjust amounts if needed, then Accept or Reject.
                        </div>
                        <a href="{{ route('lpo.edit', $lpo->id) }}" class="btn btn-warning btn-block mb-2">
                            <i class="fa fa-sliders"></i> Adjust Budget
                        </a>
                        <form action="{{ route('lpo.send-to-manager', $lpo->id) }}" method="POST" class="mb-2"
                            onsubmit="return confirm('Accept this budget and send it to the manager?');">
                            @csrf
                            <button type="submit" class="btn btn-success btn-block">
                                <i class="fa fa-check"></i> Accept &amp; Send to Manager
                            </button>
                        </form>
                        <button type="button" class="btn btn-outline-danger btn-block" data-toggle="modal" data-target="#rejectLpoModal">
                            <i class="fa fa-times"></i> Reject &amp; Return to Storekeeper
                        </button>
                    @endif

                    @if($lpo->status === 'sent_to_manager' && in_array(auth()->guard('staff')->user()->role, ['manager', 'super_admin']))
                        <button type="button" class="btn btn-success btn-block" onclick="$('#verifyLpoModal').modal('show')"><i class="fa fa-check-circle"></i> Verify Budget</button>
                    @endif

                    @if($lpo->status === 'verified_by_manager' && in_array($role, ['accountant', 'super_admin', 'manager']))
                        <div class="alert alert-warning small py-2">
                            Manager verified this budget. Disburse funds so the storekeeper can purchase.
                        </div>
                        <form action="{{ route('lpo.disburse', $lpo->id) }}" method="POST" class="mb-2"
                            onsubmit="return confirm('Disburse TZS {{ number_format($lpo->total_amount) }} to the storekeeper?');">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fa fa-money"></i> Pay Storekeeper
                            </button>
                        </form>
                    @endif

                    @if($lpo->status === 'funds_disbursed' && in_array($role, ['accountant', 'super_admin']))
                        <div class="alert alert-success small py-2">
                            Funds paid. Storekeeper can now purchase / receive items.
                        </div>
                        <form action="{{ route('lpo.close', $lpo->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-dark btn-block"><i class="fa fa-lock"></i> Close Budget</button>
                        </form>
                    @endif

                    @if(in_array($lpo->status, ['funds_disbursed', 'closed']) && in_array($role, ['storekeeper', 'super_admin', 'manager']))
                        @php
                            $receiveRoute = $role === 'storekeeper'
                                ? route('storekeeper.stock-receipts.create', ['source' => 'lpo', 'lpo_id' => $lpo->id])
                                : route('admin.stock-receipts.create', ['source' => 'lpo', 'lpo_id' => $lpo->id]);
                        @endphp
                        <a href="{{ $receiveRoute }}" class="btn btn-warning btn-block mt-2">
                            <i class="fa fa-inbox"></i> Receive Items into Inventory
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="tile shadow-sm">
                <h3 class="tile-title border-bottom pb-2">Budget Utilization Tracking</h3>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Item Name</th>
                                <th class="text-center">Budgeted</th>
                                <th class="text-center">Ordered (PO)</th>
                                <th class="text-center">Balance Qty</th>
                                <th class="text-right">Amount Spent</th>
                                <th class="text-right text-success">Balance (TZS)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lpo->items as $index => $item)
                                <tr>
                                    <td><strong>{{ $item->item_name }}</strong></td>
                                    <td class="text-center">{{ number_format($item->quantity, 2) }} {{ $item->unit }}</td>
                                    <td class="text-center font-weight-bold text-primary">
                                        {{ number_format($item->qty_ordered, 2) }} {{ $item->unit }}
                                    </td>
                                    <td class="text-center font-weight-bold {{ $item->qty_remaining > 0 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($item->qty_remaining, 2) }} {{ $item->unit }}
                                    </td>
                                    <td class="text-right font-weight-bold text-primary">
                                        {{ number_format($item->actual_spent_amount, 2) }} TZS
                                    </td>
                                    <td class="text-right font-weight-bold {{ $item->actual_balance >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($item->actual_balance, 2) }} TZS
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="font-weight-bold bg-light h5">
                                <td colspan="3" class="text-right">REMAINING FINANCIAL BALANCE:</td>
                                <td colspan="3" class="text-success text-right text-dark">{{ number_format($lpo->remaining_balance, 2) }} TZS</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            @if($lpo->supplierOrders->count() > 0)
            <div class="tile shadow-sm mt-3">
                <h3 class="tile-title border-bottom pb-2">Linked Purchase Orders (Supplier Batches)</h3>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead class="bg-light">
                            <tr>
                                <th>Order #</th>
                                <th>Supplier</th>
                                <th>Date Range</th>
                                <th>Total Amount</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lpo->supplierOrders as $po)
                                <tr>
                                    <td>#{{ $po->id }}</td>
                                    <td>{{ $po->supplier->name }}</td>
                                    <td>{{ $po->start_date->format('M d') }} - {{ $po->end_date->format('M d') }}</td>
                                    <td class="text-right font-weight-bold">{{ number_format($po->total_amount, 2) }}</td>
                                    <td>
                                        <span class="badge badge-secondary">{{ ucfirst(str_replace('_', ' ', $po->status)) }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('supplier-orders.show', $po->id) }}" class="btn btn-sm btn-info py-0">View</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            @if($lpo->shoppingLists->count() > 0)
            <div class="tile shadow-sm mt-3">
                <h3 class="tile-title border-bottom pb-2"><i class="fa fa-shopping-basket mr-2 text-success"></i>Linked Daily Purchasing Lists</h3>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead class="bg-light">
                            <tr>
                                <th>List Name</th>
                                <th>Shopping Date</th>
                                <th>Market</th>
                                <th class="text-right">Est. Cost</th>
                                <th class="text-right">Actual Cost</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lpo->shoppingLists as $sl)
                                <tr>
                                    <td>{{ $sl->name }}</td>
                                    <td>{{ $sl->shopping_date ? $sl->shopping_date->format('d M Y') : '—' }}</td>
                                    <td>{{ $sl->market_name ?? '—' }}</td>
                                    <td class="text-right">{{ number_format($sl->total_estimated_cost, 2) }}</td>
                                    <td class="text-right font-weight-bold text-warning">{{ number_format($sl->total_actual_cost, 2) }}</td>
                                    <td>
                                        <span class="badge badge-{{ $sl->status == 'completed' ? 'success' : 'secondary' }}">
                                            {{ ucfirst(str_replace('_', ' ', $sl->status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.restaurants.shopping-list.show', $sl->id) }}" class="btn btn-sm btn-info py-0">View</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-light">
                            <tr>
                                <td colspan="4" class="text-right font-weight-bold">Total Actual Spend (Daily Lists):</td>
                                <td class="text-right font-weight-bold text-warning">{{ number_format($lpo->shoppingLists->sum('total_actual_cost'), 2) }} TZS</td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Accountant Reject Modal -->
    <div class="modal fade" id="rejectLpoModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form action="{{ route('lpo.reject', $lpo->id) }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Reject LPO Budget</h5>
                        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <p>This budget will be returned to the storekeeper for revision.</p>
                        <div class="form-group mb-0">
                            <label class="font-weight-bold">Reason for rejection <span class="text-danger">*</span></label>
                            <textarea name="rejection_reason" class="form-control" rows="4" required
                                placeholder="e.g. Reduce cooking oil qty, prices look high, missing items..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Reject &amp; Return</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Manager Verify Modal -->
    <div class="modal fade" id="verifyLpoModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <form action="{{ route('lpo.manager-verify', $lpo->id) }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Verify LPO Budget</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to verify this bi-weekly budget?</p>
                        <div class="form-group">
                            <label>Verification Notes (Optional)</label>
                            <textarea name="manager_notes" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Verify & Approve</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
