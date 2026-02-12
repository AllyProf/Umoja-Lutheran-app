@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-shield"></i> Failed Login Attempts</h1>
    <p>Monitor and manage failed login attempts</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('super_admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">Failed Login Attempts</a></li>
  </ul>
</div>

<!-- Statistics -->
<div class="row mb-3">
  <div class="col-md-3">
    <div class="widget-small danger coloured-icon">
      <i class="icon fa fa-exclamation-triangle fa-2x"></i>
      <div class="info">
        <h4>Total Attempts</h4>
        <p><b>{{ $stats['total'] ?? 0 }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="widget-small warning coloured-icon">
      <i class="icon fa fa-calendar fa-2x"></i>
      <div class="info">
        <h4>Today</h4>
        <p><b>{{ $stats['today'] ?? 0 }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="widget-small info coloured-icon">
      <i class="icon fa fa-ban fa-2x"></i>
      <div class="info">
        <h4>Blocked IPs</h4>
        <p><b>{{ $stats['blocked'] ?? 0 }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="widget-small primary coloured-icon">
      <i class="icon fa fa-globe fa-2x"></i>
      <div class="info">
        <h4>Unique IPs</h4>
        <p><b>{{ $stats['unique_ips'] ?? 0 }}</b></p>
      </div>
    </div>
  </div>
</div>

<!-- Filters -->
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-body">
        <form method="GET" action="{{ route('super_admin.failed-login-attempts') }}" class="row">
          <div class="col-md-4">
            <div class="form-group">
              <label for="email">Email</label>
              <input type="text" name="email" id="email" class="form-control" 
                     placeholder="Search by email..." value="{{ $filters['email'] ?? '' }}">
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label for="ip_address">IP Address</label>
              <input type="text" name="ip_address" id="ip_address" class="form-control" 
                     placeholder="Search by IP..." value="{{ $filters['ip_address'] ?? '' }}">
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group">
              <label for="blocked">Status</label>
              <select name="blocked" id="blocked" class="form-control">
                <option value="">All</option>
                <option value="1" {{ ($filters['blocked'] ?? '') == '1' ? 'selected' : '' }}>Blocked</option>
                <option value="0" {{ ($filters['blocked'] ?? '') == '0' ? 'selected' : '' }}>Not Blocked</option>
              </select>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group">
              <label>&nbsp;</label>
              <button type="submit" class="btn btn-primary btn-block">
                <i class="fa fa-search"></i> Filter
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Failed Login Attempts Table -->
<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-title-w-btn">
        <h3 class="title"><i class="fa fa-shield"></i> Failed Login Attempts</h3>
      </div>
      <div class="tile-body">
        <div class="table-responsive">
          <table class="table table-hover table-bordered">
            <thead>
              <tr>
                <th>Email</th>
                <th>IP Address</th>
                <th>User Agent</th>
                <th>Reason</th>
                <th>Blocked</th>
                <th>Time</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($attempts as $attempt)
              <tr>
                <td>{{ $attempt->email ?? 'N/A' }}</td>
                <td><code>{{ $attempt->ip_address }}</code></td>
                <td><small>{{ Str::limit($attempt->user_agent, 50) }}</small></td>
                <td>
                  <span class="badge badge-{{ $attempt->reason == 'wrong_password' ? 'danger' : 'warning' }}">
                    {{ ucfirst(str_replace('_', ' ', $attempt->reason)) }}
                  </span>
                </td>
                <td>
                  @if($attempt->blocked)
                    <span class="badge badge-danger">Blocked</span>
                    @if($attempt->blocked_until)
                      <br><small>Until: {{ $attempt->blocked_until->format('M d, Y H:i') }}</small>
                    @endif
                  @else
                    <span class="badge badge-success">Not Blocked</span>
                  @endif
                </td>
                <td>
                  {{ $attempt->created_at->format('M d, Y H:i:s') }}<br>
                  <small class="text-muted">{{ $attempt->created_at->diffForHumans() }}</small>
                </td>
                <td>
                  @if(!$attempt->blocked)
                  <form action="{{ route('super_admin.block-ip', $attempt->ip_address) }}" method="POST" style="display: inline-block;">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-danger" title="Block IP">
                      <i class="fa fa-ban"></i> Block IP
                    </button>
                  </form>
                  @else
                  <form action="{{ route('super_admin.unblock-ip', $attempt->ip_address) }}" method="POST" style="display: inline-block;">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-success" title="Unblock IP">
                      <i class="fa fa-check"></i> Unblock
                    </button>
                  </form>
                  @endif
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="7" class="text-center">No failed login attempts found</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        
        @if($attempts->hasPages())
        <div class="mt-3">
          <nav aria-label="Page navigation">
            <ul class="pagination pagination-sm justify-content-center">
              {{-- Previous Page Link --}}
              @if($attempts->onFirstPage())
                <li class="page-item disabled">
                  <span class="page-link">
                    <i class="fa fa-angle-left"></i> Prev
                  </span>
                </li>
              @else
                <li class="page-item">
                  <a class="page-link" href="{{ $attempts->previousPageUrl() }}" rel="prev">
                    <i class="fa fa-angle-left"></i> Prev
                  </a>
                </li>
              @endif

              {{-- Pagination Elements --}}
              @php
                $currentPage = $attempts->currentPage();
                $lastPage = $attempts->lastPage();
                $startPage = max(1, $currentPage - 2);
                $endPage = min($lastPage, $currentPage + 2);
              @endphp

              {{-- First page --}}
              @if($startPage > 1)
                <li class="page-item">
                  <a class="page-link" href="{{ $attempts->url(1) }}">1</a>
                </li>
                @if($startPage > 2)
                  <li class="page-item disabled">
                    <span class="page-link">...</span>
                  </li>
                @endif
              @endif

              {{-- Page range around current page --}}
              @for($page = $startPage; $page <= $endPage; $page++)
                @if($page == $currentPage)
                  <li class="page-item active">
                    <span class="page-link">{{ $page }}</span>
                  </li>
                @else
                  <li class="page-item">
                    <a class="page-link" href="{{ $attempts->url($page) }}">{{ $page }}</a>
                  </li>
                @endif
              @endfor

              {{-- Last page --}}
              @if($endPage < $lastPage)
                @if($endPage < $lastPage - 1)
                  <li class="page-item disabled">
                    <span class="page-link">...</span>
                  </li>
                @endif
                <li class="page-item">
                  <a class="page-link" href="{{ $attempts->url($lastPage) }}">{{ $lastPage }}</a>
                </li>
              @endif

              {{-- Next Page Link --}}
              @if($attempts->hasMorePages())
                <li class="page-item">
                  <a class="page-link" href="{{ $attempts->nextPageUrl() }}" rel="next">
                    Next <i class="fa fa-angle-right"></i>
                  </a>
                </li>
              @else
                <li class="page-item disabled">
                  <span class="page-link">
                    Next <i class="fa fa-angle-right"></i>
                  </span>
                </li>
              @endif
            </ul>
          </nav>
          
          <div class="text-center mt-2">
            <small class="text-muted">
              Showing {{ $attempts->firstItem() }} to {{ $attempts->lastItem() }} of {{ $attempts->total() }} results
            </small>
          </div>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>

<style>
  /* Bootstrap pagination styling - compact size */
  .pagination-sm .page-link {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    line-height: 1.5;
  }
  
  .pagination-sm .page-item:first-child .page-link {
    border-top-left-radius: 0.2rem;
    border-bottom-left-radius: 0.2rem;
  }
  
  .pagination-sm .page-item:last-child .page-link {
    border-top-right-radius: 0.2rem;
    border-bottom-right-radius: 0.2rem;
  }
  
  .pagination-sm .page-item.active .page-link {
    z-index: 2;
    color: #fff;
    background-color: #007bff;
    border-color: #007bff;
  }
  
  .pagination-sm .page-item.disabled .page-link {
    color: #868e96;
    pointer-events: none;
    background-color: #fff;
    border-color: #ddd;
  }
  
  .pagination-sm .page-link i {
    font-size: 0.75rem;
  }
</style>
@endsection

