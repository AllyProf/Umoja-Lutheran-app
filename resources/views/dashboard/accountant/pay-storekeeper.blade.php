@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
    <div>
        <h1><i class="fa fa-money"></i> Pay Storekeeper</h1>
        <p>Disburse funds after the manager has verified / approved</p>
    </div>
    <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
        <li class="breadcrumb-item"><a href="{{ route('accountant.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item">Pay Storekeeper</li>
    </ul>
</div>

{{-- LPO budgets awaiting payment --}}
<div class="row mb-3">
    <div class="col-md-12">
        <div class="tile">
            <h3 class="tile-title"><i class="fa fa-file-text text-danger"></i> LPO Budgets Awaiting Payment</h3>
            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th>LPO</th>
                            <th>Period</th>
                            <th>Storekeeper</th>
                            <th>Verified By</th>
                            <th class="text-right">Budget</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lpos as $lpo)
                            <tr class="table-warning">
                                <td><strong>#{{ $lpo->id }}</strong></td>
                                <td>{{ $lpo->start_date->format('d M Y') }} – {{ $lpo->end_date->format('d M Y') }}</td>
                                <td>{{ $lpo->storekeeper->name ?? 'Storekeeper' }}</td>
                                <td>{{ $lpo->manager->name ?? 'Manager' }}</td>
                                <td class="text-right font-weight-bold">TZS {{ number_format($lpo->total_amount) }}</td>
                                <td class="text-center">
                                    <a href="{{ route('lpo.show', $lpo->id) }}" class="btn btn-sm btn-outline-secondary mr-1">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    <form action="{{ route('lpo.disburse', $lpo->id) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Disburse TZS {{ number_format($lpo->total_amount) }} to the storekeeper for LPO #{{ $lpo->id }}?');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-primary">
                                            <i class="fa fa-money"></i> Pay Storekeeper
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    No manager-verified LPO budgets waiting for payment.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @include('dashboard.partials.bootstrap-pagination', ['paginator' => $lpos])
        </div>
    </div>
</div>

{{-- Purchase lists awaiting payment --}}
<div class="row">
    <div class="col-md-12">
        <div class="tile">
            <h3 class="tile-title"><i class="fa fa-list-alt text-info"></i> Purchase Lists Awaiting Payment</h3>
            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>List Name</th>
                            <th>Items</th>
                            <th class="text-right">Budget</th>
                            <th>Approved</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($shoppingLists as $i => $list)
                            <tr>
                                <td>{{ $shoppingLists->firstItem() + $i }}</td>
                                <td><strong>{{ $list->name }}</strong></td>
                                <td>{{ $list->items->count() }}</td>
                                <td class="text-right font-weight-bold">TZS {{ number_format($list->budget_amount) }}</td>
                                <td>{{ $list->updated_at->format('d M Y') }}</td>
                                <td class="text-center">
                                    <a href="{{ route('accountant.shopping-list.show', $list->id) }}" class="btn btn-sm btn-outline-secondary mr-1">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    <form action="{{ route('accountant.shopping-list.disburse', $list->id) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Disburse TZS {{ number_format($list->budget_amount) }} to the storekeeper?');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-primary">
                                            <i class="fa fa-money"></i> Pay Storekeeper
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    No manager-approved purchase lists waiting for payment.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @include('dashboard.partials.bootstrap-pagination', ['paginator' => $shoppingLists])
        </div>
    </div>
</div>
@endsection
