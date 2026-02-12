@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-file-text"></i> Purchase Reports</h1>
    <p>View purchase reports with budget tracking</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">Purchase Reports</a></li>
  </ul>
</div>

<!-- Statistics Cards -->
<div class="row mb-3">
  <div class="col-md-3 col-lg-3">
    <div class="widget-small info coloured-icon">
      <i class="icon fa fa-list fa-2x"></i>
      <div class="info">
        <h4>Total Lists</h4>
        <p><b>{{ $stats['total_lists'] ?? 0 }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-lg-3">
    <div class="widget-small success coloured-icon">
      <i class="icon fa fa-check-circle fa-2x"></i>
      <div class="info">
        <h4>Completed</h4>
        <p><b>{{ $stats['completed_lists'] ?? 0 }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-lg-3">
    <div class="widget-small primary coloured-icon">
      <i class="icon fa fa-dollar fa-2x"></i>
      <div class="info">
        <h4>Total Budget</h4>
        <p><b>{{ number_format($stats['total_budget'] ?? 0, 2) }}</b> TZS</p>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-lg-3">
    <div class="widget-small {{ ($stats['total_remaining'] ?? 0) < 0 ? 'danger' : 'warning' }} coloured-icon">
      <i class="icon fa fa-balance-scale fa-2x"></i>
      <div class="info">
        <h4>Remaining</h4>
        <p><b>{{ number_format($stats['total_remaining'] ?? 0, 2) }}</b> TZS</p>
      </div>
    </div>
  </div>
</div>

<!-- Filters -->
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-body">
        <form method="GET" action="{{ route('admin.purchase-reports.index') }}" class="form-inline">
          <div class="form-group mr-3">
            <label for="date_from" class="mr-2">From:</label>
            <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
          </div>
          <div class="form-group mr-3">
            <label for="date_to" class="mr-2">To:</label>
            <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
          </div>
          <div class="form-group mr-3">
            <label for="status" class="mr-2">Status:</label>
            <select class="form-control" id="status" name="status">
              <option value="">All</option>
              <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
              <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
              <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
          </div>
          <button type="submit" class="btn btn-primary">
            <i class="fa fa-filter"></i> Filter
          </button>
          <a href="{{ route('admin.purchase-reports.index') }}" class="btn btn-secondary ml-2">
            <i class="fa fa-refresh"></i> Reset
          </a>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Purchase Lists Table -->
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-file-text"></i> Purchase Reports</h3>
      <div class="tile-body">
        @if($shoppingLists->count() > 0)
        <div class="table-responsive">
          <table class="table table-hover table-bordered">
            <thead>
              <tr>
                <th>List Name</th>
                <th>Date</th>
                <th>Status</th>
                <th>Budget</th>
                <th>Amount Used</th>
                <th>Amount Remaining</th>
                <th>Items</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($shoppingLists as $list)
              <tr>
                <td><strong>{{ $list->name }}</strong></td>
                <td>{{ $list->shopping_date ? \Carbon\Carbon::parse($list->shopping_date)->format('M d, Y') : $list->created_at->format('M d, Y') }}</td>
                <td>
                  @if($list->status === 'completed')
                    <span class="badge badge-success">Completed</span>
                  @elseif($list->status === 'pending')
                    <span class="badge badge-warning">Pending</span>
                  @elseif($list->status === 'cancelled')
                    <span class="badge badge-danger">Cancelled</span>
                  @endif
                </td>
                <td>
                  @if($list->budget_amount)
                    {{ number_format($list->budget_amount, 2) }} TZS
                  @else
                    <span class="text-muted">Not Set</span>
                  @endif
                </td>
                <td>
                  <strong>{{ number_format($list->amount_used ?? 0, 2) }}</strong> TZS
                </td>
                <td>
                  @if($list->budget_amount)
                    <span class="{{ ($list->amount_remaining ?? 0) < 0 ? 'text-danger' : 'text-success' }}">
                      <strong>{{ number_format($list->amount_remaining ?? 0, 2) }}</strong> TZS
                    </span>
                  @else
                    <span class="text-muted">N/A</span>
                  @endif
                </td>
                <td>
                  {{ $list->items->count() }} items
                  @if($list->items->where('is_found', false)->count() > 0)
                    <br><small class="text-danger">{{ $list->items->where('is_found', false)->count() }} missing</small>
                  @endif
                </td>
                <td>
                  <a href="{{ route('admin.purchase-reports.show', $list->id) }}" class="btn btn-sm btn-info">
                    <i class="fa fa-eye"></i> View Report
                  </a>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        
        <div class="mt-3">
          {{ $shoppingLists->links() }}
        </div>
        @else
        <div class="text-center" style="padding: 50px;">
          <i class="fa fa-file-text fa-4x text-muted mb-3"></i>
          <h3>No Purchase Reports</h3>
          <p class="text-muted">No shopping lists found matching your filters.</p>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection
