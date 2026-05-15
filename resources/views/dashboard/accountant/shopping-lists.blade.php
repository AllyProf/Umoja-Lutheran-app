@extends('dashboard.layouts.app')

@section('content')
    <div class="app-title">
        <div>
            <h1><i class="fa fa-list-alt"></i> Shopping Lists Management</h1>
            <p>Approve or monitor shopping lists from the storekeeper</p>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="{{ route('accountant.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item">Shopping Lists</li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <!-- Tab Navigation -->
                <ul class="nav nav-tabs mb-4">
                    <li class="nav-item">
                        <a class="nav-link {{ $tab === 'pending' ? 'active' : '' }}"
                            href="{{ route('accountant.shopping-lists', ['tab' => 'pending']) }}">
                            Awaiting Review <span
                                class="badge badge-danger ml-1">{{ $tab === 'pending' ? $lists->total() : '' }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $tab === 'approved' ? 'active' : '' }}"
                            href="{{ route('accountant.shopping-lists', ['tab' => 'approved']) }}">
                            Approved (To Pay) <span
                                class="badge badge-info ml-1">{{ $tab === 'approved' ? $lists->total() : '' }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $tab === 'purchased' ? 'active' : '' }}"
                            href="{{ route('accountant.shopping-lists', ['tab' => 'purchased']) }}">
                            Purchased/Completed
                        </a>
                    </li>
                </ul>

                <div class="tile-title-w-btn">
                    <h3 class="title">
                        @if($tab === 'pending') Lists Awaiting Financial Check
                        @elseif($tab === 'approved') Approved (Pay Disbursement)
                        @else History of Purchases @endif
                    </h3>
                </div>

                @if($lists->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Purchaser Name</th>
                                    <th>Estimated Cost</th>
                                    @if($tab !== 'pending')
                                        <th>Approved Budget</th>
                                    @endif
                                    @if($tab === 'purchased')
                                        <th>Actual Cost</th>
                                    @endif
                                    <th>Status</th>
                                    <th>Created Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lists as $i => $list)
                                    <tr>
                                        <td>{{ $lists->firstItem() + $i }}</td>
                                        <td><strong>{{ $list->name }}</strong><br><small
                                                class="text-muted">{{ $list->items->count() }} items</small></td>
                                        <td>TZS {{ number_format($list->total_estimated_cost) }}</td>
                                        @if($tab !== 'pending')
                                            <td>TZS {{ number_format($list->budget_amount) }}</td>
                                        @endif
                                        @if($tab === 'purchased')
                                            <td class="text-primary font-weight-bold">TZS {{ number_format($list->total_actual_cost) }}
                                            </td>
                                        @endif
                                        <td>
                                            @php
                                                $badgeClass = 'secondary';
                                                if ($list->status === 'pending')
                                                    $badgeClass = 'warning';
                                                elseif ($list->status === 'accountant_checked')
                                                    $badgeClass = 'primary';
                                                elseif ($list->status === 'approved')
                                                    $badgeClass = 'info';
                                                elseif ($list->status === 'ready_for_purchase')
                                                    $badgeClass = 'warning';
                                                elseif ($list->status === 'completed')
                                                    $badgeClass = 'success';
                                            @endphp
                                            <span class="badge badge-{{ $badgeClass }}">
                                                {{ strtoupper(str_replace('_', ' ', $list->status)) }}
                                            </span>
                                        </td>
                                        <td>{{ $list->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <a href="{{ route('accountant.shopping-list.show', $list->id) }}"
                                                class="btn btn-sm btn-outline-info">
                                                <i class="fa fa-eye"></i> Details
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center mt-3">
                        {{ $lists->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fa fa-check-circle fa-4x text-success mb-3"></i>
                        <h3>No Lists Found</h3>
                        <p class="text-muted">Everything is up to date.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
