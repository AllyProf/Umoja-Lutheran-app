@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-refresh"></i> {{ __('Cache Management') }}</h1>
    <p>{{ __('Clear application caches to improve performance') }}</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('super_admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="#">{{ __('Cache Management') }}</a></li>
  </ul>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title">{{ __('Cache Status') }}</h3>
      <div class="tile-body">
        <div class="row mb-4">
          <div class="col-md-4">
            <div class="card">
              <div class="card-body">
                <h5>{{ __('Config Cache') }}</h5>
                <p class="{{ $cacheStats['config_cache'] ? 'text-success' : 'text-muted' }}">
                  <i class="fa fa-{{ $cacheStats['config_cache'] ? 'check-circle' : 'times-circle' }}"></i>
                  {{ $cacheStats['config_cache'] ? __('Cached') : __('Not Cached') }}
                </p>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card">
              <div class="card-body">
                <h5>{{ __('Route Cache') }}</h5>
                <p class="{{ $cacheStats['route_cache'] ? 'text-success' : 'text-muted' }}">
                  <i class="fa fa-{{ $cacheStats['route_cache'] ? 'check-circle' : 'times-circle' }}"></i>
                  {{ $cacheStats['route_cache'] ? __('Cached') : __('Not Cached') }}
                </p>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card">
              <div class="card-body">
                <h5>{{ __('View Cache') }}</h5>
                <p class="text-info">
                  <i class="fa fa-info-circle"></i>
                  {{ $cacheStats['view_cache'] }} {{ __('files') }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <h4 class="mb-3">{{ __('Clear Cache') }}</h4>
        <div class="row">
          <div class="col-md-6">
            <form action="{{ route('super_admin.clear-cache') }}" method="POST" class="mb-3">
              @csrf
              <input type="hidden" name="type" value="config">
              <button type="submit" class="btn btn-block btn-warning">
                <i class="fa fa-cog"></i> {{ __('Clear Config Cache') }}
              </button>
            </form>
          </div>
          <div class="col-md-6">
            <form action="{{ route('super_admin.clear-cache') }}" method="POST" class="mb-3">
              @csrf
              <input type="hidden" name="type" value="route">
              <button type="submit" class="btn btn-block btn-warning">
                <i class="fa fa-road"></i> {{ __('Clear Route Cache') }}
              </button>
            </form>
          </div>
          <div class="col-md-6">
            <form action="{{ route('super_admin.clear-cache') }}" method="POST" class="mb-3">
              @csrf
              <input type="hidden" name="type" value="view">
              <button type="submit" class="btn btn-block btn-warning">
                <i class="fa fa-eye"></i> {{ __('Clear View Cache') }}
              </button>
            </form>
          </div>
          <div class="col-md-6">
            <form action="{{ route('super_admin.clear-cache') }}" method="POST" class="mb-3">
              @csrf
              <input type="hidden" name="type" value="cache">
              <button type="submit" class="btn btn-block btn-warning">
                <i class="fa fa-database"></i> {{ __('Clear Application Cache') }}
              </button>
            </form>
          </div>
          <div class="col-md-12">
            <form action="{{ route('super_admin.clear-cache') }}" method="POST"
                  onsubmit="event.preventDefault(); confirmAction(@json(__('This will clear ALL caches. Continue?')), @json(__('Clear All Caches')), @json(__('Yes, clear all!')), @json(__('Cancel'))).then((result) => { if (result.isConfirmed) { this.submit(); } });">
              @csrf
              <input type="hidden" name="type" value="all">
              <button type="submit" class="btn btn-block btn-danger btn-lg">
                <i class="fa fa-trash"></i> {{ __('Clear All Caches') }}
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
