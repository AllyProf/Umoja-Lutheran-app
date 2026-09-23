@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-wrench"></i> Maintenance Mode</h1>
    <p>Turn the system off and write the message visitors will see</p>
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
      <h3 class="tile-title">Maintenance Mode</h3>
      <div class="tile-body">
        <div class="alert alert-{{ $isDown ? 'warning' : 'success' }}" role="alert">
          <h4>
            <i class="fa fa-{{ $isDown ? 'exclamation-triangle' : 'check-circle' }}"></i>
            System status: <strong>{{ $isDown ? 'OFF — under maintenance' : 'LIVE' }}</strong>
          </h4>
          <p class="mb-0">
            @if($isDown)
              Visitors still open the login page. After they click Login, they see the message below. A super admin can still sign in.
            @else
              The system is open. Turn it off when you need to do maintenance.
            @endif
          </p>
        </div>

        <form action="{{ route('super_admin.toggle-maintenance') }}" method="POST" id="maintenanceForm">
          @csrf

          <div class="form-group">
            <label for="message">Message visitors will see</label>
            <textarea name="message" id="message" class="form-control" rows="5" maxlength="2000"
                      placeholder="System is under maintenance. Please check back later.">{{ $maintenanceMessage }}</textarea>
            <small class="form-text text-muted">This text is shown exactly as you type it, including line breaks.</small>
            @error('message')
              <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
          </div>

          <div class="form-group">
            <label>Preview</label>
            <div id="messagePreview" style="background:#fff;color:#1c1c1c;border:1px solid #ddd;border-left:6px solid #940000;border-radius:8px;padding:16px 18px;font-size:18px;line-height:1.65;white-space:pre-wrap;word-wrap:break-word;"></div>
          </div>

          <div class="tile-footer">
            @if($isDown)
            <button type="submit" class="btn btn-primary btn-lg" name="action" value="update">
              <i class="fa fa-save"></i> Save message
            </button>
            <button type="submit" class="btn btn-success btn-lg" name="action" value="disable">
              <i class="fa fa-power-off"></i> Turn the system back on
            </button>
            @else
            <button type="button" class="btn btn-warning btn-lg" id="enableMaintenance">
              <i class="fa fa-wrench"></i> Turn the system off
            </button>
            @endif
            <a href="{{ route('super_admin.dashboard') }}" class="btn btn-secondary">Cancel</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
  (function () {
    var area = document.getElementById('message');
    var preview = document.getElementById('messagePreview');
    if (area && preview) {
      var sync = function () {
        preview.textContent = area.value;
      };
      area.addEventListener('input', sync);
      sync();
    }

    var enableBtn = document.getElementById('enableMaintenance');
    if (enableBtn) {
      enableBtn.addEventListener('click', function () {
        var form = document.getElementById('maintenanceForm');
        var submit = function () {
          var input = document.createElement('input');
          input.type = 'hidden';
          input.name = 'action';
          input.value = 'enable';
          form.appendChild(input);
          form.submit();
        };
        if (typeof confirmAction === 'function') {
          confirmAction(
            'Visitors will still see the login page. When they click Login, they will see the message you wrote. A super admin can still sign in.',
            'Turn the system off?',
            'Yes, turn it off',
            'Cancel'
          ).then(function (result) {
            if (result.isConfirmed) {
              submit();
            }
          });
        } else {
          submit();
        }
      });
    }
  })();
</script>
@endsection
