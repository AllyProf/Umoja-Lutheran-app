@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-cog"></i> System Settings</h1>
    <p>Configure system-wide settings and preferences</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('super_admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">System Settings</a></li>
  </ul>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title">System Configuration</h3>
      <div class="tile-body">
        <form action="{{ route('super_admin.system-settings.update') }}" method="POST">
          @csrf
          
          <div class="row">
            <div class="col-md-6">
              <h4 class="mb-3">Application Settings</h4>
              
              <div class="form-group">
                <label for="app_name">Application Name</label>
                <input type="text" name="app_name" id="app_name" class="form-control" 
                       value="{{ $settings['app_name'] ?? config('app.name') }}">
              </div>
              
              <div class="form-group">
                <label for="app_url">Application URL</label>
                <input type="url" name="app_url" id="app_url" class="form-control" 
                       value="{{ $settings['app_url'] ?? config('app.url') }}">
              </div>
              
              <div class="form-group">
                <label for="app_timezone">Timezone</label>
                <select name="app_timezone" id="app_timezone" class="form-control">
                  <option value="Africa/Dar_es_Salaam" {{ ($settings['app_timezone'] ?? config('app.timezone')) == 'Africa/Dar_es_Salaam' ? 'selected' : '' }}>Africa/Dar es Salaam</option>
                  <option value="UTC" {{ ($settings['app_timezone'] ?? config('app.timezone')) == 'UTC' ? 'selected' : '' }}>UTC</option>
                  <option value="America/New_York" {{ ($settings['app_timezone'] ?? config('app.timezone')) == 'America/New_York' ? 'selected' : '' }}>America/New York</option>
                  <option value="Europe/London" {{ ($settings['app_timezone'] ?? config('app.timezone')) == 'Europe/London' ? 'selected' : '' }}>Europe/London</option>
                </select>
              </div>
              
              <div class="form-group">
                <label for="app_locale">Locale</label>
                <select name="app_locale" id="app_locale" class="form-control">
                  <option value="en" {{ ($settings['app_locale'] ?? config('app.locale')) == 'en' ? 'selected' : '' }}>English</option>
                  <option value="sw" {{ ($settings['app_locale'] ?? config('app.locale')) == 'sw' ? 'selected' : '' }}>Swahili</option>
                </select>
              </div>
            </div>
            
            <div class="col-md-6">
              <h4 class="mb-3">Security Settings</h4>
              
              <div class="form-group">
                <label for="session_lifetime">Session Lifetime (minutes)</label>
                <input type="number" name="session_lifetime" id="session_lifetime" class="form-control" 
                       value="{{ $settings['session_lifetime'] ?? config('session.lifetime', 120) }}" min="1" max="1440">
                <small class="form-text text-muted">Default: 120 minutes (2 hours)</small>
              </div>
              
              <div class="form-group">
                <label for="max_login_attempts">Max Login Attempts</label>
                <input type="number" name="max_login_attempts" id="max_login_attempts" class="form-control" 
                       value="{{ $settings['max_login_attempts'] ?? 5 }}" min="1" max="20">
                <small class="form-text text-muted">Number of failed attempts before lockout</small>
              </div>
              
              <div class="form-group">
                <label for="lockout_duration">Lockout Duration (minutes)</label>
                <input type="number" name="lockout_duration" id="lockout_duration" class="form-control" 
                       value="{{ $settings['lockout_duration'] ?? 15 }}" min="1" max="1440">
                <small class="form-text text-muted">How long to lock account after max attempts</small>
              </div>
            </div>
          </div>
          
          <div class="tile-footer">
            <button type="submit" class="btn btn-primary">
              <i class="fa fa-save"></i> Save Settings
            </button>
            <a href="{{ route('super_admin.dashboard') }}" class="btn btn-secondary">
              <i class="fa fa-times"></i> Cancel
            </a>
          </div>
        </form>
      </div>
    </div>
    <div class="tile">
      <h3 class="tile-title">Backup & Storage</h3>
      <div class="tile-body">
        <div class="row">
          <div class="col-md-6">
            <h4>Google Drive Backup</h4>
            <p>Automatically backup your database to Google Drive. The system uses a refresh token to maintain access.</p>
            
            <div class="alert alert-info">
              <i class="fa fa-info-circle"></i> 
              <strong>Status:</strong> 
              @if(config('filesystems.disks.google.refreshToken'))
                <span class="text-success">Configured</span>
              @else
                <span class="text-danger">Not Configured</span>
              @endif
            </div>

            <a href="{{ route('admin.google.auth') }}" class="btn btn-warning">
              <i class="fa fa-google"></i> Connect / Re-authenticate Google Drive
            </a>
            
            <p class="mt-3 small text-muted">
              Note: This will redirect you to Google to authorize the application. 
              Ensure you have configured the Client ID and Secret in your environment settings.
            </p>
          </div>
          
          <div class="col-md-6">
            <h4>Backup Schedule</h4>
            <ul class="list-group">
              <li class="list-group-item d-flex justify-content-between align-items-center">
                Database Backup
                <span class="badge badge-primary badge-pill">Daily at 03:00 AM</span>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                Backup Cleanup
                <span class="badge badge-secondary badge-pill">Daily at 04:00 AM</span>
              </li>
            </ul>
            <p class="mt-2 small text-muted">Backups are stored in the configured Google Drive folder.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

