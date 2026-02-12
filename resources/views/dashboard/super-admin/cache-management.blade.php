@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-refresh"></i> Cache Management</h1>
    <p>Clear application caches to improve performance</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('super_admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">Cache Management</a></li>
  </ul>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title">Cache Status</h3>
      <div class="tile-body">
        <div class="row mb-4">
          <div class="col-md-4">
            <div class="card">
              <div class="card-body">
                <h5>Config Cache</h5>
                <p class="{{ $cacheStats['config_cache'] ? 'text-success' : 'text-muted' }}">
                  <i class="fa fa-{{ $cacheStats['config_cache'] ? 'check-circle' : 'times-circle' }}"></i>
                  {{ $cacheStats['config_cache'] ? 'Cached' : 'Not Cached' }}
                </p>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card">
              <div class="card-body">
                <h5>Route Cache</h5>
                <p class="{{ $cacheStats['route_cache'] ? 'text-success' : 'text-muted' }}">
                  <i class="fa fa-{{ $cacheStats['route_cache'] ? 'check-circle' : 'times-circle' }}"></i>
                  {{ $cacheStats['route_cache'] ? 'Cached' : 'Not Cached' }}
                </p>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card">
              <div class="card-body">
                <h5>View Cache</h5>
                <p class="text-info">
                  <i class="fa fa-info-circle"></i>
                  {{ $cacheStats['view_cache'] }} files
                </p>
              </div>
            </div>
          </div>
        </div>

        <h4 class="mb-3">Clear Cache</h4>
        <div class="row">
          <div class="col-md-6">
            <form action="{{ route('super_admin.clear-cache') }}" method="POST" class="mb-3">
              @csrf
              <input type="hidden" name="type" value="config">
              <button type="submit" class="btn btn-block btn-warning">
                <i class="fa fa-cog"></i> Clear Config Cache
              </button>
            </form>
          </div>
          <div class="col-md-6">
            <form action="{{ route('super_admin.clear-cache') }}" method="POST" class="mb-3">
              @csrf
              <input type="hidden" name="type" value="route">
              <button type="submit" class="btn btn-block btn-warning">
                <i class="fa fa-road"></i> Clear Route Cache
              </button>
            </form>
          </div>
          <div class="col-md-6">
            <form action="{{ route('super_admin.clear-cache') }}" method="POST" class="mb-3">
              @csrf
              <input type="hidden" name="type" value="view">
              <button type="submit" class="btn btn-block btn-warning">
                <i class="fa fa-eye"></i> Clear View Cache
              </button>
            </form>
          </div>
          <div class="col-md-6">
            <form action="{{ route('super_admin.clear-cache') }}" method="POST" class="mb-3">
              @csrf
              <input type="hidden" name="type" value="cache">
              <button type="submit" class="btn btn-block btn-warning">
                <i class="fa fa-database"></i> Clear Application Cache
              </button>
            </form>
          </div>
          <div class="col-md-12">
            <form action="{{ route('super_admin.clear-cache') }}" method="POST" 
                  onsubmit="event.preventDefault(); confirmAction('This will clear ALL caches. Continue?', 'Clear All Caches', 'Yes, clear all!', 'Cancel').then((result) => { if (result.isConfirmed) { this.submit(); } });">
              @csrf
              <input type="hidden" name="type" value="all">
              <button type="submit" class="btn btn-block btn-danger btn-lg">
                <i class="fa fa-trash"></i> Clear All Caches
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

