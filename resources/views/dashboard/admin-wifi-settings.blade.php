@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-wifi"></i> WiFi Settings</h1>
    <p>Manage WiFi network and password for each room</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">WiFi Settings</a></li>
  </ul>
</div>

<!-- Hotel-wide WiFi Settings (Fallback) -->
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-building"></i> Hotel-Wide WiFi Settings (Fallback)</h3>
      <div class="tile-body">
        <p class="text-muted">These settings will be used if a room doesn't have its own WiFi configured.</p>
        <form id="hotelWifiSettingsForm">
          @csrf
          <div class="row">
            <div class="col-md-5">
              <div class="form-group">
                <label for="hotel_wifi_network_name">WiFi Network Name (SSID)</label>
                <input type="text" class="form-control" id="hotel_wifi_network_name" name="wifi_network_name" 
                       value="{{ $hotelWifiNetworkName }}" placeholder="Enter WiFi network name">
              </div>
            </div>
            <div class="col-md-5">
              <div class="form-group">
                <label for="hotel_wifi_password">WiFi Password</label>
                <div class="input-group">
                  <input type="password" class="form-control" id="hotel_wifi_password" name="wifi_password" 
                         value="{{ $hotelWifiPassword }}" placeholder="Enter WiFi password">
                  <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="button" onclick="toggleHotelPasswordVisibility()">
                      <i class="fa fa-eye" id="hotelPasswordToggleIcon"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-2">
              <div class="form-group">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-primary btn-block">
                  <i class="fa fa-save"></i> Save
                </button>
              </div>
            </div>
          </div>
          <div id="hotelAlertMessage"></div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Per-Room WiFi Settings -->
<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-bed"></i> Room-Specific WiFi Settings</h3>
      <div class="tile-body">
        @if($rooms->count() > 0)
        <div class="table-responsive">
          <table class="table table-hover table-bordered">
            <thead>
              <tr>
                <th>Room Number</th>
                <th>Room Type</th>
                <th>WiFi Network Name</th>
                <th>WiFi Password</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($rooms as $room)
              <tr id="room-row-{{ $room->id }}">
                <td><strong>{{ $room->room_number }}</strong></td>
                <td>{{ $room->room_type }}</td>
                <td>
                  <input type="text" class="form-control form-control-sm" 
                         id="wifi_network_{{ $room->id }}" 
                         value="{{ $room->wifi_network_name ?? '' }}" 
                         placeholder="Network name">
                </td>
                <td>
                  <div class="input-group input-group-sm">
                    <input type="password" class="form-control" 
                           id="wifi_password_{{ $room->id }}" 
                           value="{{ $room->wifi_password ?? '' }}" 
                           placeholder="Password">
                    <div class="input-group-append">
                      <button class="btn btn-outline-secondary" type="button" 
                              onclick="toggleRoomPasswordVisibility({{ $room->id }})">
                        <i class="fa fa-eye" id="passwordToggleIcon_{{ $room->id }}"></i>
                      </button>
                    </div>
                  </div>
                </td>
                <td>
                  @if($room->wifi_password || $room->wifi_network_name)
                    <span class="badge badge-success">Configured</span>
                  @else
                    <span class="badge badge-warning">Not Set</span>
                  @endif
                </td>
                <td>
                  <button class="btn btn-sm btn-primary" onclick="saveRoomWifi({{ $room->id }})">
                    <i class="fa fa-save"></i> Save
                  </button>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        @else
        <div class="alert alert-info text-center">
          <i class="fa fa-info-circle fa-2x mb-3"></i>
          <h4>No Rooms Found</h4>
          <p>You need to create rooms first before configuring WiFi settings.</p>
          <a href="{{ route('admin.rooms.create') }}" class="btn btn-primary">
            <i class="fa fa-plus"></i> Create Room
          </a>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script src="{{ asset('dashboard_assets/js/plugins/sweetalert.min.js') }}"></script>
<script>
function toggleHotelPasswordVisibility() {
    const passwordInput = document.getElementById('hotel_wifi_password');
    const toggleIcon = document.getElementById('hotelPasswordToggleIcon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}

function toggleRoomPasswordVisibility(roomId) {
    const passwordInput = document.getElementById('wifi_password_' + roomId);
    const toggleIcon = document.getElementById('passwordToggleIcon_' + roomId);
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}

// Save hotel-wide WiFi settings
document.getElementById('hotelWifiSettingsForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const formData = new FormData(form);
    const alertDiv = document.getElementById('hotelAlertMessage');
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Saving...';
    
    if (alertDiv) {
        alertDiv.innerHTML = '';
    }
    
    fetch('{{ route("admin.wifi-settings.update") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            swal({
                title: "Success!",
                text: data.message || "Hotel WiFi settings updated successfully.",
                type: "success",
                confirmButtonColor: "#28a745",
                timer: 2000,
                showConfirmButton: false
            });
        } else {
            let errorMsg = data.message || 'An error occurred. Please try again.';
            if (data.errors) {
                const errorList = Object.values(data.errors).flat().join('<br>');
                errorMsg = errorList;
            }
            if (alertDiv) {
                alertDiv.innerHTML = '<div class="alert alert-danger">' + errorMsg + '</div>';
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (alertDiv) {
            alertDiv.innerHTML = '<div class="alert alert-danger">An error occurred. Please try again.</div>';
        }
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
});

// Save room-specific WiFi settings
function saveRoomWifi(roomId) {
    const networkName = document.getElementById('wifi_network_' + roomId).value;
    const password = document.getElementById('wifi_password_' + roomId).value;
    const row = document.getElementById('room-row-' + roomId);
    const saveBtn = row.querySelector('button');
    const originalText = saveBtn.innerHTML;
    
    saveBtn.disabled = true;
    saveBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Saving...';
    
    const updateUrl = '{{ url("/manager/wifi-settings/room") }}/' + roomId;
    fetch(updateUrl, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            wifi_network_name: networkName,
            wifi_password: password
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update status badge
            const statusCell = row.querySelector('td:nth-child(5)');
            if (networkName || password) {
                statusCell.innerHTML = '<span class="badge badge-success">Configured</span>';
            } else {
                statusCell.innerHTML = '<span class="badge badge-warning">Not Set</span>';
            }
            
            swal({
                title: "Success!",
                text: data.message || "Room WiFi settings updated successfully.",
                type: "success",
                confirmButtonColor: "#28a745",
                timer: 2000,
                showConfirmButton: false
            });
        } else {
            swal({
                title: "Error!",
                text: data.message || "An error occurred. Please try again.",
                type: "error",
                confirmButtonColor: "#d33"
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        swal({
            title: "Error!",
            text: "An error occurred. Please try again.",
            type: "error",
            confirmButtonColor: "#d33"
        });
    })
    .finally(() => {
        saveBtn.disabled = false;
        saveBtn.innerHTML = originalText;
    });
}
</script>
@endsection
