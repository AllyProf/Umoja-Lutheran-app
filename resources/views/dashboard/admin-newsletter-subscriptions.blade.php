@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-envelope"></i> Newsletter Subscriptions</h1>
    <p>Manage newsletter email subscriptions</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">Newsletter Subscriptions</a></li>
  </ul>
</div>

<!-- Statistics Cards -->
<div class="row mb-3">
  <div class="col-md-3">
    <div class="widget-small primary coloured-icon">
      <i class="icon fa fa-envelope fa-3x"></i>
      <div class="info">
        <h4>Total Subscriptions</h4>
        <p><b>{{ number_format($totalSubscriptions) }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="widget-small success coloured-icon">
      <i class="icon fa fa-check-circle fa-3x"></i>
      <div class="info">
        <h4>Active</h4>
        <p><b>{{ number_format($activeSubscriptions) }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="widget-small warning coloured-icon">
      <i class="icon fa fa-pause-circle fa-3x"></i>
      <div class="info">
        <h4>Inactive</h4>
        <p><b>{{ number_format($inactiveSubscriptions) }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="widget-small info coloured-icon">
      <i class="icon fa fa-calendar fa-3x"></i>
      <div class="info">
        <h4>This Month</h4>
        <p><b>{{ number_format($thisMonthSubscriptions) }}</b></p>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-title-w-btn mb-3">
        <h3 class="title">All Subscriptions</h3>
        <p>
          <a class="btn btn-success icon-btn" href="{{ route('admin.newsletter.export') }}" target="_blank">
            <i class="fa fa-download"></i> Export CSV
          </a>
        </p>
      </div>
      
      <!-- Filters -->
      <div class="row mb-3">
        <div class="col-md-12">
          <form method="GET" action="{{ route('admin.newsletter.subscriptions') }}" class="form-inline">
            <div class="form-group mr-2">
              <input type="text" name="search" class="form-control" placeholder="Search by email..." value="{{ request('search') }}" style="width: 250px;">
            </div>
            <div class="form-group mr-2">
              <select name="status" class="form-control" onchange="this.form.submit()">
                <option value="">All Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
              </select>
            </div>
            <div class="form-group mr-2">
              <select name="sort_by" class="form-control" onchange="this.form.submit()">
                <option value="subscribed_at" {{ request('sort_by') === 'subscribed_at' ? 'selected' : '' }}>Subscribed Date</option>
                <option value="email" {{ request('sort_by') === 'email' ? 'selected' : '' }}>Email</option>
                <option value="created_at" {{ request('sort_by') === 'created_at' ? 'selected' : '' }}>Created Date</option>
              </select>
            </div>
            <div class="form-group mr-2">
              <select name="sort_order" class="form-control" onchange="this.form.submit()">
                <option value="desc" {{ request('sort_order') === 'desc' ? 'selected' : '' }}>Descending</option>
                <option value="asc" {{ request('sort_order') === 'asc' ? 'selected' : '' }}>Ascending</option>
              </select>
            </div>
            @if(request('search') || request('status'))
            <a href="{{ route('admin.newsletter.subscriptions') }}" class="btn btn-secondary">Reset</a>
            @endif
          </form>
        </div>
      </div>
      
      <div class="tile-body">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          {{ session('success') }}
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        @endif
        
        @if($subscriptions->count() > 0)
        <div class="table-responsive">
          <table class="table table-hover table-bordered">
            <thead>
              <tr>
                <th>#</th>
                <th>Email</th>
                <th>Status</th>
                <th>Subscribed At</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($subscriptions as $index => $subscription)
              <tr>
                <td>{{ $subscriptions->firstItem() + $index }}</td>
                <td>
                  <a href="mailto:{{ $subscription->email }}">{{ $subscription->email }}</a>
                </td>
                <td>
                  @if($subscription->is_active)
                    <span class="badge badge-success">Active</span>
                  @else
                    <span class="badge badge-warning">Inactive</span>
                  @endif
                </td>
                <td>{{ $subscription->subscribed_at->format('M d, Y H:i') }}</td>
                <td>
                  <form method="POST" action="{{ route('admin.newsletter.toggle', $subscription->id) }}" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to change the subscription status?');">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-{{ $subscription->is_active ? 'warning' : 'success' }}" title="{{ $subscription->is_active ? 'Deactivate' : 'Activate' }}">
                      <i class="fa fa-{{ $subscription->is_active ? 'pause' : 'play' }}"></i>
                    </button>
                  </form>
                  <form method="POST" action="{{ route('admin.newsletter.destroy', $subscription->id) }}" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this subscription? This action cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                      <i class="fa fa-trash"></i>
                    </button>
                  </form>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        
        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-3">
          {{ $subscriptions->appends(request()->query())->links() }}
        </div>
        @else
        <div class="alert alert-info">
          <i class="fa fa-info-circle"></i> No newsletter subscriptions found.
        </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection








