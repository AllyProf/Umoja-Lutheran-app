@extends('dashboard.layouts.app')

@section('content')
    <div class="app-title">
        <div>
            <h1><i class="fa fa-file-text"></i> Local Purchase Orders (LPO)</h1>
            <p>Manage and track bi-weekly stock budgets</p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="#">LPO Budgets</a></li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="tile shadow-sm">
                <div class="tile-title-w-btn">
                    <h3 class="title">LPO History</h3>
                    <p>
                        @if(auth()->guard('staff')->user()->role === 'storekeeper' || auth()->guard('staff')->user()->role === 'super_admin')
                            <a href="{{ route('lpo.create') }}" class="btn btn-primary icon-btn"><i class="fa fa-plus"></i> New LPO Budget</a>
                        @endif
                    </p>
                </div>
                <div class="tile-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="lpoTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Period</th>
                                    <th class="text-right">Budget Amount</th>
                                    <th class="text-right">Spent Amount</th>
                                    <th class="text-right">Remaining</th>
                                    <th class="text-center">Status</th>
                                    <th>Created By</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                    <tr>
                                        <td>#{{ $order->id }}</td>
                                        <td>
                                            <span class="small">{{ $order->start_date->format('M d, Y') }} to {{ $order->end_date->format('M d, Y') }}</span>
                                        </td>
                                        <td class="text-right font-weight-bold">{{ number_format($order->total_amount, 2) }}</td>
                                        <td class="text-right text-warning">{{ number_format($order->total_spent_amount, 2) }}</td>
                                        <td class="text-right text-success">{{ number_format($order->remaining_balance, 2) }}</td>
                                        <td class="text-center">
                                            @php
                                                $badgeClass = match ($order->status) {
                                                    'pending' => 'badge-warning',
                                                    'sent_to_accountant' => 'badge-info',
                                                    'sent_to_manager' => 'badge-primary',
                                                    'verified_by_manager' => 'badge-success',
                                                    'closed' => 'badge-dark',
                                                    'cancelled' => 'badge-danger',
                                                    default => 'badge-secondary'
                                                };
                                            @endphp
                                            <span class="badge {{ $badgeClass }}">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</span>
                                        </td>
                                        <td>{{ $order->storekeeper->name ?? 'System' }}</td>
                                        <td>
                                            <a href="{{ route('lpo.show', $order->id) }}" class="btn btn-sm btn-info"><i class="fa fa-eye"></i> Details</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $orders->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
