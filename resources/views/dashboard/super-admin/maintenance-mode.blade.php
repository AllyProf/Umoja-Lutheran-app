@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-wrench"></i> Maintenance Mode</h1>
    <p>Enable or disable system maintenance mode</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('super_admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">Maintenance Mode</a></li>
  </ul>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title">Maintenance Mode Control</h3>
      <div class="tile-body">
        <div class="alert alert-{{ $isDown ? 'warning' : 'success' }}" role="alert">
          <h4>
            <i class="fa fa-{{ $isDown ? 'exclamation-triangle' : 'check-circle' }}"></i>
            System Status: <strong>{{ $isDown ? 'MAINTENANCE MODE ACTIVE' : 'SYSTEM IS LIVE' }}</strong>
          </h4>
          <p class="mb-0">
            @if($isDown)
              The system is currently in maintenance mode. Only super administrators can access the system.
            @else
              The system is currently live and accessible to all users.
            @endif
          </p>
        </div>

        <form action="{{ route('super_admin.toggle-maintenance') }}" method="POST" id="maintenanceForm">
          @csrf
          
          <div class="form-group">
            <label for="message">Maintenance Message</label>
            <textarea name="message" id="message" class="form-control" rows="4" 
                      placeholder="System is under maintenance. Please check back later.">{{ $maintenanceMessage }}</textarea>
            <small class="form-text text-muted">This message will be displayed to users when maintenance mode is enabled.</small>
          </div>

          <div class="tile-footer">
            @if($isDown)
            <button type="submit" class="btn btn-success btn-lg">
              <i class="fa fa-power-off"></i> Disable Maintenance Mode
            </button>
            @else
            <button type="button" class="btn btn-warning btn-lg" 
                    onclick="confirmAction('This will put the system in maintenance mode. Only super admins will be able to access. Continue?', 'Enable Maintenance Mode', 'Yes, enable!', 'Cancel').then((result) => { if (result.isConfirmed) { document.getElementById('maintenanceForm').submit(); } });">
              <i class="fa fa-wrench"></i> Enable Maintenance Mode
            </button>
            @endif
            <a href="{{ route('super_admin.dashboard') }}" class="btn btn-secondary">
              <i class="fa fa-times"></i> Cancel
            </a>
          </div>
        </form>

        <div class="mt-4">
          <h5>Important Notes:</h5>
          <ul>
            <li>When maintenance mode is enabled, only super administrators can access the system.</li>
            <li>All other users will see the maintenance message.</li>
            <li>You can customize the maintenance message above.</li>
            <li>Remember to disable maintenance mode when done.</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

