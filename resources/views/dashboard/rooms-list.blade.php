@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-bed"></i> Rooms Management</h1>
    <p>View and manage all hotel rooms</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ isset($role) && $role === 'super_admin' ? route('super_admin.dashboard') : route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">Rooms</a></li>
  </ul>
</div>

<!-- Statistics Cards -->
<div class="row mb-3">
  <div class="col-md-3 col-sm-6 mb-3">
    <div class="widget-small info coloured-icon">
      <i class="icon fa fa-bed fa-2x"></i>
      <div class="info">
        <h4>Total Rooms</h4>
        <p><b>{{ $stats['total'] ?? 0 }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-sm-6 mb-3">
    <div class="widget-small success coloured-icon">
      <i class="icon fa fa-check-circle fa-2x"></i>
      <div class="info">
        <h4>Available</h4>
        <p><b>{{ $stats['available'] ?? 0 }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-sm-6 mb-3">
    <div class="widget-small danger coloured-icon">
      <i class="icon fa fa-user fa-2x"></i>
      <div class="info">
        <h4>Occupied</h4>
        <p><b>{{ $stats['occupied'] ?? 0 }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-sm-6 mb-3">
    <div class="widget-small warning coloured-icon">
      <i class="icon fa fa-broom fa-2x"></i>
      <div class="info">
        <h4>To Be Cleaned</h4>
        <p><b>{{ $stats['to_be_cleaned'] ?? 0 }}</b></p>
      </div>
    </div>
  </div>
</div>

<!-- Statistics by Type -->
@if(isset($statsByType) && !empty($statsByType))
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-body">
        <h5 class="mb-3">
          <i class="fa fa-bar-chart"></i> Statistics by Room Type
          <button class="btn btn-sm btn-link" type="button" data-toggle="collapse" data-target="#statsByTypeCollapse" aria-expanded="false" aria-controls="statsByTypeCollapse">
            <i class="fa fa-chevron-down" id="statsToggleIcon"></i>
          </button>
        </h5>
        <div class="collapse" id="statsByTypeCollapse">
          <div class="row">
            @php
              $typeDisplayNames = [
                'Single' => 'Single',
                'Double' => 'Double',
                'Twins' => 'Standard Twin Room'
              ];
            @endphp
            @foreach($statsByType as $type => $typeStats)
              @if($typeStats['total'] > 0)
              <div class="col-md-4 mb-2">
                <div class="card border-primary">
                  <div class="card-body">
                    <h6 class="card-title">
                      <strong>{{ $typeDisplayNames[$type] ?? $type }}</strong>
                      <span class="badge badge-primary float-right">{{ $typeStats['total'] }} total</span>
                    </h6>
                    <div class="stats-detail">
                      <small>
                        <span class="badge badge-success">{{ $typeStats['available'] }} available</span>
                        <span class="badge badge-danger">{{ $typeStats['occupied'] }} occupied</span>
                        @if($typeStats['to_be_cleaned'] > 0)
                          <span class="badge badge-warning">{{ $typeStats['to_be_cleaned'] }} to clean</span>
                        @endif
                        @if($typeStats['maintenance'] > 0)
                          <span class="badge badge-secondary">{{ $typeStats['maintenance'] }} maintenance</span>
                        @endif
                      </small>
                    </div>
                    <div class="mt-2">
                      <button class="btn btn-sm btn-outline-primary" onclick="filterByType('{{ $type }}')">
                        <i class="fa fa-filter"></i> Filter by {{ $typeDisplayNames[$type] ?? $type }}
                      </button>
                      <button class="btn btn-sm btn-outline-success" onclick="selectAllByType('{{ $type }}')">
                        <i class="fa fa-check-square"></i> Select All {{ $typeDisplayNames[$type] ?? $type }}
                      </button>
                    </div>
                  </div>
                </div>
              </div>
              @endif
            @endforeach
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endif

<!-- Filter and Search -->
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-body">
        <div class="row">
          <div class="col-md-3">
            <div class="form-group">
              <label for="statusFilter"><strong>Filter by Status:</strong></label>
              <select id="statusFilter" class="form-control" onchange="filterRooms()">
                <option value="all">All Status</option>
                <option value="available">Available</option>
                <option value="occupied">Occupied</option>
                <option value="to_be_cleaned">To Be Cleaned</option>
                <option value="maintenance">Maintenance</option>
              </select>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label for="typeFilter"><strong>Filter by Type:</strong></label>
              <select id="typeFilter" class="form-control" onchange="filterRooms()">
                <option value="all">All Types</option>
                <option value="Single">Single</option>
                <option value="Double">Double</option>
                <option value="Twins">Standard Twin Room</option>
              </select>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label for="searchInput"><strong>Search:</strong></label>
              <input type="text" id="searchInput" class="form-control" 
                     placeholder="Search by room number, type..." 
                     onkeyup="filterRooms()">
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group">
              <label>&nbsp;</label>
              <button type="button" class="btn btn-secondary btn-block" onclick="resetFilters()">
                <i class="fa fa-refresh"></i> Reset
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-title-w-btn mb-3">
        <h3 class="title">All Rooms</h3>
        <p>
          <div class="btn-group">
            <a class="btn btn-primary icon-btn" href="{{ route('admin.rooms.create') }}">
              <i class="fa fa-plus"></i> Add Room
            </a>
            <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <span class="sr-only">Toggle Dropdown</span>
            </button>
            <div class="dropdown-menu">
              <a class="dropdown-item" href="{{ route('admin.rooms.create') }}?type=Single&bulk=1">
                <i class="fa fa-layer-group"></i> Create Multiple Single Rooms
              </a>
              <a class="dropdown-item" href="{{ route('admin.rooms.create') }}?type=Double&bulk=1">
                <i class="fa fa-layer-group"></i> Create Multiple Double Rooms
              </a>
              <a class="dropdown-item" href="{{ route('admin.rooms.create') }}?type=Twins&bulk=1">
                <i class="fa fa-layer-group"></i> Create Multiple Standard Twin Rooms
              </a>
            </div>
          </div>
        </p>
      </div>

      <!-- Bulk Actions Bar -->
      <div id="bulkActionsBar" class="alert alert-info mb-3" style="display: none;">
        <div class="row align-items-center">
          <div class="col-md-4">
            <strong><span id="selectedCount">0</span> room(s) selected</strong>
          </div>
          <div class="col-md-8">
            <div class="btn-group">
              <button type="button" class="btn btn-sm btn-secondary" onclick="clearSelection()">
                <i class="fa fa-times"></i> Clear Selection
              </button>
              <button type="button" class="btn btn-sm btn-info" onclick="selectAllVisible()">
                <i class="fa fa-check-square"></i> Select All Visible
              </button>
              <button type="button" class="btn btn-sm btn-warning" onclick="openBulkEdit()">
                <i class="fa fa-edit"></i> Bulk Edit
              </button>
              <div class="btn-group">
                <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown">
                  <i class="fa fa-cog"></i> Quick Actions
                </button>
                <div class="dropdown-menu">
                  <h6 class="dropdown-header">Change Status</h6>
                  <a class="dropdown-item" href="#" onclick="executeBulkAction('status', 'available'); return false;">
                    <i class="fa fa-check-circle text-success"></i> Set to Available
                  </a>
                  <a class="dropdown-item" href="#" onclick="executeBulkAction('status', 'occupied'); return false;">
                    <i class="fa fa-user text-danger"></i> Set to Occupied
                  </a>
                  <a class="dropdown-item" href="#" onclick="executeBulkAction('status', 'to_be_cleaned'); return false;">
                    <i class="fa fa-broom text-warning"></i> Set to To Be Cleaned
                  </a>
                  <a class="dropdown-item" href="#" onclick="executeBulkAction('status', 'maintenance'); return false;">
                    <i class="fa fa-wrench text-secondary"></i> Set to Maintenance
                  </a>
                  <div class="dropdown-divider"></div>
                  <h6 class="dropdown-header">Other Actions</h6>
                  <a class="dropdown-item text-danger" href="#" onclick="executeBulkAction('delete', null); return false;">
                    <i class="fa fa-trash"></i> Delete Selected
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      @if($rooms->count() > 0)
      <div class="table-responsive">
        <table class="table table-hover table-bordered" id="roomsTable">
          <thead>
            <tr>
              <th width="50">
                <input type="checkbox" id="selectAll" onchange="toggleSelectAll(this)">
              </th>
              <th>Room #</th>
              <th>Type</th>
              <th>Status</th>
              <th>Capacity</th>
              <th>Bed Type</th>
              <th>Price/Night</th>
              <th>Images</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($rooms as $room)
            <tr class="room-row"
                data-status="{{ $room->status }}"
                data-room-type="{{ strtolower($room->room_type) }}"
                data-room-number="{{ strtolower($room->room_number) }}">
              <td>
                <input type="checkbox" class="room-checkbox" value="{{ $room->id }}" onchange="updateBulkActionsBar()">
              </td>
              <td>
                <strong>{{ $room->room_number }}</strong>
                @if(isset($room->bulk_creation_group_id) && $room->bulk_creation_group_id)
                  <br><small class="badge badge-info" title="Created in bulk">
                    <i class="fa fa-layer-group"></i> Bulk Created
                  </small>
                @endif
              </td>
              <td>
                <span class="badge badge-primary">
                  @if($room->room_type === 'Twins')
                    Standard Twin Room
                  @else
                    {{ $room->room_type }}
                  @endif
                </span>
              </td>
              <td>
                @if($room->status === 'available')
                  <span class="badge badge-success">Available</span>
                @elseif($room->status === 'occupied')
                  <span class="badge badge-danger">Occupied</span>
                @elseif($room->status === 'to_be_cleaned')
                  <span class="badge badge-warning">To Be Cleaned</span>
                @elseif($room->status === 'maintenance')
                  <span class="badge badge-secondary">Maintenance</span>
                @else
                  <span class="badge badge-secondary">{{ ucfirst($room->status) }}</span>
                @endif
              </td>
              <td>{{ $room->capacity }} Guest(s)</td>
              <td>{{ $room->bed_type }}</td>
              <td>
                <strong>TZS {{ number_format($room->price_per_night, 0) }}</strong>
              </td>
              <td>
                @if($room->images && is_array($room->images) && count($room->images) > 0)
                  @php
                    $imagePath = trim($room->images[0]);
                    // Images are stored as 'rooms/filename.jpg' in database
                    // Ensure path doesn't start with a slash
                    $imagePath = ltrim($imagePath, '/');
                    // Generate URL using asset() helper
                    $imageUrl = asset('storage/' . $imagePath);
                  @endphp
                  <div class="room-images-preview">
                    <img src="{{ $imageUrl }}" alt="Room Image" class="room-thumbnail" data-toggle="modal" data-target="#imageModal{{ $room->id }}" onerror="this.onerror=null; this.style.display='none'; this.parentElement.innerHTML='<span class=\'text-muted\'>Image not found</span>';">
                    @if(count($room->images) > 1)
                      <span class="image-count">+{{ count($room->images) - 1 }}</span>
                    @endif
                  </div>
                @else
                  <span class="text-muted">No images</span>
                @endif
              </td>
              <td>
                <div class="btn-group" role="group">
                  <button class="btn btn-sm btn-info" onclick="viewRoom({{ $room->id }})" title="View Details">
                    <i class="fa fa-eye"></i>
                  </button>
                  <a href="{{ route('admin.rooms.edit', $room) }}" class="btn btn-sm btn-warning" title="Edit">
                    <i class="fa fa-edit"></i>
                  </a>
                  <button class="btn btn-sm btn-primary" onclick="openChangeTypeModal({{ $room->id }})" title="Change Room Type">
                    <i class="fa fa-exchange"></i>
                  </button>
                  <button class="btn btn-sm btn-danger" onclick="deleteRoom({{ $room->id }})" title="Delete">
                    <i class="fa fa-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @else
      <div class="alert alert-info text-center">
        <i class="fa fa-info-circle fa-2x mb-3"></i>
        <h4>No rooms found</h4>
        <p>You haven't created any rooms yet. Click the button above to add your first room.</p>
        <a href="{{ route('admin.rooms.create') }}" class="btn btn-primary">
          <i class="fa fa-plus"></i> Add Your First Room
        </a>
      </div>
      @endif
    </div>
  </div>
</div>

<!-- Room Details Modal -->
<div class="modal fade" id="roomDetailsModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background: #940000; color: white;">
        <h5 class="modal-title"><i class="fa fa-bed"></i> Room Details Information</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="roomDetailsContent">
        <!-- Content will be loaded here -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Change Room Type Modal -->
<div class="modal fade" id="changeTypeModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title"><i class="fa fa-exchange"></i> Change Room Type</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="changeTypeForm" enctype="multipart/form-data">
        @csrf
        <input type="hidden" id="change_type_room_id" name="room_id">
        <div class="modal-body">
          <div class="alert alert-info">
            <i class="fa fa-info-circle"></i> <strong>Current Room:</strong> <span id="current_room_info"></span>
          </div>
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="new_room_type">New Room Type <span class="text-danger">*</span></label>
                <select class="form-control" id="new_room_type" name="room_type" required>
                  <option value="">Select New Room Type</option>
                  <option value="Single">Single</option>
                  <option value="Double">Double</option>
                  <option value="Twins">Standard Twin Room</option>
                </select>
                <small class="form-text text-muted">Select the new room type</small>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="new_capacity">Capacity (Max Guests) <span class="text-danger">*</span></label>
                <input class="form-control" type="number" id="new_capacity" name="capacity" min="1" max="10" required>
                <small class="form-text text-muted">Maximum number of guests</small>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="new_bed_type">Bed Type <span class="text-danger">*</span></label>
                <select class="form-control" id="new_bed_type" name="bed_type" required>
                  <option value="">Select Bed Type</option>
                  <option value="King">King</option>
                  <option value="Queen">Queen</option>
                  <option value="Twin">Twin</option>
                  <option value="Bunk">Bunk</option>
                  <option value="Single">Single</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="new_price_per_night">Price per Night (TZS) <span class="text-danger">*</span></label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">TZS</span>
                  </div>
                  <input class="form-control" type="number" id="new_price_per_night" name="price_per_night" step="1" min="0" required>
                </div>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label for="new_description">Description</label>
            <textarea class="form-control" id="new_description" name="description" rows="3" placeholder="Room description, amenities, or view"></textarea>
          </div>

          <div class="form-group">
            <label for="new_room_images">Room Images</label>
            <input type="file" class="form-control-file" id="new_room_images" name="room_images[]" multiple accept="image/*">
            <small class="form-text text-muted">You can upload new images. Leave empty to keep current images.</small>
            <div id="current_images_preview" class="mt-2"></div>
          </div>

          <div class="alert alert-warning">
            <i class="fa fa-exclamation-triangle"></i> <strong>Note:</strong> Changing the room type will update the room's category. Make sure to update the price, capacity, and bed type accordingly.
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">
            <i class="fa fa-save"></i> Update Room Type
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('scripts')
{{-- SweetAlert2 is already loaded in app.blade.php layout, no need to load old version --}}
<script>
// Bulk Selection Functions
function toggleSelectAll(checkbox) {
  const checkboxes = document.querySelectorAll('.room-checkbox');
  checkboxes.forEach(cb => {
    cb.checked = checkbox.checked;
  });
  updateBulkActionsBar();
}

function updateBulkActionsBar() {
  const checkboxes = document.querySelectorAll('.room-checkbox:checked');
  const count = checkboxes.length;
  const bulkBar = document.getElementById('bulkActionsBar');
  const selectedCount = document.getElementById('selectedCount');
  
  if (count > 0) {
    bulkBar.style.display = 'block';
    selectedCount.textContent = count;
  } else {
    bulkBar.style.display = 'none';
  }
  
  // Update select all checkbox
  const selectAll = document.getElementById('selectAll');
  const allCheckboxes = document.querySelectorAll('.room-checkbox');
  selectAll.checked = allCheckboxes.length > 0 && checkboxes.length === allCheckboxes.length;
}

function clearSelection() {
  const checkboxes = document.querySelectorAll('.room-checkbox');
  checkboxes.forEach(cb => cb.checked = false);
  document.getElementById('selectAll').checked = false;
  updateBulkActionsBar();
}

function getSelectedRoomIds() {
  const checkboxes = document.querySelectorAll('.room-checkbox:checked');
  return Array.from(checkboxes).map(cb => parseInt(cb.value));
}

function openBulkEdit() {
  const roomIds = getSelectedRoomIds();
  if (roomIds.length === 0) {
    Swal.fire({
      title: "No Selection",
      text: "Please select at least one room to edit.",
      icon: "warning",
      confirmButtonColor: "#940000"
    });
    return;
  }
  
  // Check if all selected rooms are of the same type
  const selectedCheckboxes = document.querySelectorAll('.room-checkbox:checked');
  const roomTypes = new Set();
  
  selectedCheckboxes.forEach(checkbox => {
    const row = checkbox.closest('.room-row');
    if (row) {
      const roomType = row.getAttribute('data-room-type');
      if (roomType) {
        // Convert back to proper case
        const properType = roomType.charAt(0).toUpperCase() + roomType.slice(1);
        if (properType === 'Twins') {
          roomTypes.add('Twins');
        } else {
          roomTypes.add(properType);
        }
      }
    }
  });
  
  // Build query string
  let queryString = roomIds.map(id => `room_ids[]=${id}`).join('&');
  
  // If all rooms are of the same type, add type filter
  if (roomTypes.size === 1) {
    const type = Array.from(roomTypes)[0];
    queryString += `&type=${type}`;
  }
  
  window.location.href = '{{ route("admin.rooms.bulk-edit") }}?' + queryString;
}

function executeBulkAction(action, value) {
  const roomIds = getSelectedRoomIds();
  if (roomIds.length === 0) {
    Swal.fire({
      title: "No Selection",
      text: "Please select at least one room.",
      icon: "warning",
      confirmButtonColor: "#940000"
    });
    return;
  }
  
  let confirmText = '';
  let confirmTitle = '';
  
  if (action === 'delete') {
    confirmTitle = 'Delete Rooms?';
    confirmText = `Are you sure you want to delete ${roomIds.length} room(s)? This action cannot be undone!`;
  } else if (action === 'status') {
    confirmTitle = 'Change Status?';
    confirmText = `Are you sure you want to change the status of ${roomIds.length} room(s) to "${value}"?`;
  } else {
    confirmTitle = 'Confirm Action';
    confirmText = `Are you sure you want to perform this action on ${roomIds.length} room(s)?`;
  }
  
  Swal.fire({
    title: confirmTitle,
    text: confirmText,
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: action === 'delete' ? "#d33" : "#940000",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "Yes, proceed!",
    cancelButtonText: "Cancel"
  }).then((result) => {
    if (result.isConfirmed) {
      // Show loading
      Swal.fire({
        title: "Processing...",
        text: "Please wait while we process your request.",
        icon: "info",
        showConfirmButton: false,
        allowOutsideClick: false,
        willOpen: () => {
          Swal.showLoading();
        }
      });
      
      // Prepare data
      const data = {
        action: action,
        room_ids: roomIds
      };
      
      if (value !== null) {
        data.value = value;
      }
      
      // Send request
      fetch('{{ route("admin.rooms.bulk-action") }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json'
        },
        body: JSON.stringify(data)
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          Swal.fire({
            title: "Success!",
            text: data.message,
            icon: "success",
            confirmButtonColor: "#940000"
          }).then(() => {
            location.reload();
          });
        } else {
          Swal.fire({
            title: "Error!",
            text: data.message || "An error occurred.",
            icon: "error",
            confirmButtonColor: "#940000"
          });
        }
      })
      .catch(error => {
        console.error('Error:', error);
        Swal.fire({
          title: "Error!",
          text: "An error occurred while processing your request.",
          icon: "error",
          confirmButtonColor: "#940000"
        });
      });
    }
  });
}

function filterByType(type) {
  document.getElementById('typeFilter').value = type;
  filterRooms();
}

function selectAllByType(type) {
  // First filter by type
  filterByType(type);
  
  // Wait a moment for filter to apply, then select all visible rooms
  setTimeout(function() {
    const rows = document.querySelectorAll('.room-row');
    const checkboxes = document.querySelectorAll('.room-checkbox');
    
    rows.forEach((row, index) => {
      const roomType = row.getAttribute('data-room-type');
      // Check if row is visible and matches the type
      if (row.style.display !== 'none' && roomType === type.toLowerCase()) {
        // Find corresponding checkbox
        const checkbox = row.querySelector('.room-checkbox');
        if (checkbox) {
          checkbox.checked = true;
        }
      }
    });
    
    updateBulkActionsBar();
    
    // Show success message
    Swal.fire({
      title: "Selected!",
      text: `All visible ${type === 'Twins' ? 'Standard Twin' : type} rooms have been selected.`,
      icon: "success",
      timer: 2000,
      showConfirmButton: false,
      confirmButtonColor: "#940000"
    });
  }, 100);
}

function selectAllVisible() {
  const rows = document.querySelectorAll('.room-row');
  let selectedCount = 0;
  
  rows.forEach(row => {
    // Only select visible rows
    if (row.style.display !== 'none') {
      const checkbox = row.querySelector('.room-checkbox');
      if (checkbox) {
        checkbox.checked = true;
        selectedCount++;
      }
    }
  });
  
  updateBulkActionsBar();
  
  if (selectedCount > 0) {
    swal({
      title: "Selected!",
      text: `${selectedCount} visible room(s) have been selected.`,
      type: "success",
      timer: 2000,
      showConfirmButton: false,
      confirmButtonColor: "#940000"
    });
  } else {
    Swal.fire({
      title: "No Rooms",
      text: "No rooms are currently visible. Try adjusting your filters.",
      icon: "info",
      confirmButtonColor: "#940000"
    });
  }
}

// Statistics collapse toggle icon
$(document).ready(function() {
  $('#statsByTypeCollapse').on('show.bs.collapse', function () {
    $('#statsToggleIcon').removeClass('fa-chevron-down').addClass('fa-chevron-up');
  });

  $('#statsByTypeCollapse').on('hide.bs.collapse', function () {
    $('#statsToggleIcon').removeClass('fa-chevron-up').addClass('fa-chevron-down');
  });
});

function filterRooms() {
  const statusFilter = document.getElementById('statusFilter').value;
  const typeFilter = document.getElementById('typeFilter').value;
  const searchInput = document.getElementById('searchInput').value.toLowerCase();
  
  const rows = document.querySelectorAll('.room-row');
  let visibleCount = 0;
  
  rows.forEach(row => {
    const status = row.getAttribute('data-status');
    const roomType = row.getAttribute('data-room-type');
    const roomNumber = row.getAttribute('data-room-number');
    
    let show = true;
    
    // Status filter
    if (statusFilter !== 'all' && status !== statusFilter) {
      show = false;
    }
    
    // Type filter
    if (show && typeFilter !== 'all' && roomType !== typeFilter.toLowerCase()) {
      show = false;
    }
    
    // Search filter
    if (show && searchInput) {
      if (!roomNumber.includes(searchInput) && 
          !roomType.includes(searchInput)) {
        show = false;
      }
    }
    
    row.style.display = show ? '' : 'none';
    if (show) visibleCount++;
  });
  
  // Show/hide "no results" message
  const tbody = document.querySelector('#roomsTable tbody');
  if (tbody) {
    let noResultsRow = tbody.querySelector('.no-results-row');
    
    if (visibleCount === 0) {
      if (!noResultsRow) {
        noResultsRow = document.createElement('tr');
        noResultsRow.className = 'no-results-row';
        noResultsRow.innerHTML = `
          <td colspan="9" class="text-center">
            <i class="fa fa-search fa-3x text-muted mb-2"></i>
            <p>No rooms found matching your filters</p>
          </td>
        `;
        tbody.appendChild(noResultsRow);
      }
    } else {
      if (noResultsRow) {
        noResultsRow.remove();
      }
    }
  }
}

function resetFilters() {
  document.getElementById('statusFilter').value = 'all';
  document.getElementById('typeFilter').value = 'all';
  document.getElementById('searchInput').value = '';
  filterRooms();
}
</script>
<style>
.room-images-preview {
  position: relative;
  display: inline-block;
}

.room-thumbnail {
  width: 60px;
  height: 60px;
  object-fit: cover;
  border-radius: 4px;
  cursor: pointer;
  border: 2px solid #e0e0e0;
  transition: transform 0.3s ease;
}

.room-thumbnail:hover {
  transform: scale(1.1);
  border-color: #940000;
}

.image-count {
  position: absolute;
  top: -5px;
  right: -5px;
  background-color: #940000;
  color: white;
  border-radius: 50%;
  width: 20px;
  height: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 10px;
  font-weight: bold;
}

.tile-title-w-btn {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.tile-title-w-btn .title {
  margin: 0;
}

.icon-btn {
  padding: 8px 15px;
}

.badge {
  padding: 5px 10px;
  font-size: 12px;
}

.badge-primary {
  background-color: #940000;
  color: white;
}

#roomsTable {
  font-size: 14px;
}

#roomsTable td {
  vertical-align: middle;
}

.btn-group {
  display: flex;
  gap: 5px;
}

.btn-group .btn {
  margin: 0;
}

.room-details-view {
  padding: 10px;
}

.preview-container {
  background-color: #f8f9fa;
  padding: 15px;
  border-radius: 8px;
}

.preview-section {
  background-color: white;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  margin-bottom: 20px;
}

.preview-section:last-child {
  margin-bottom: 0;
}

.preview-section h5 {
  color: #940000;
  margin-bottom: 15px;
  font-weight: 600;
  border-bottom: 2px solid #940000;
  padding-bottom: 8px;
}

.preview-section table {
  margin-bottom: 0;
}

.preview-section th {
  background-color: #fcfcfc;
  width: 35%;
  color: #666;
  font-weight: 600;
}

.room-details-view .table-sm td {
  padding: 8px;
}
</style>

<script>
// Ensure functions are available globally and add error handling
// Attach to window object so they're accessible from onclick handlers
window.viewRoom = function(roomId) {
  console.log('viewRoom called with ID:', roomId);
  try {
    // Fetch room details via AJAX
    fetch('{{ route("admin.rooms.show", ":id") }}'.replace(':id', roomId), {
      method: 'GET',
      headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      const room = data.room;
      let amenitiesHtml = '';
      if (room.amenities && Array.isArray(room.amenities)) {
        amenitiesHtml = room.amenities.map(a => `<span class="badge badge-secondary mr-1">${a}</span>`).join('');
      }
      
      let imagesHtml = '';
      if (room.images && Array.isArray(room.images) && room.images.length > 0) {
        imagesHtml = room.images.map(img => {
          // Handle different image path formats
          let imgPath = img;
          if (imgPath.startsWith('storage/')) {
            imgPath = imgPath.substring(8); // Remove 'storage/' prefix
          } else if (!imgPath.startsWith('rooms/') && !imgPath.startsWith('http') && !imgPath.startsWith('/')) {
            imgPath = 'rooms/' + imgPath;
          }
          const imageUrl = imgPath.startsWith('http') ? imgPath : '{{ asset("storage") }}/' + imgPath;
          return `<img src="${imageUrl}" class="img-thumbnail m-1" style="max-width: 150px;" alt="Room Image" onerror="this.onerror=null; this.style.display='none';">`;
        }).join('');
      } else {
        imagesHtml = '<p class="text-muted">No images available</p>';
      }
      
      // Helper function to check if a value is valid (not null, not empty, not 'N/A')
      const isValid = (value) => {
        return value !== null && value !== undefined && value !== '' && value !== 'N/A' && value !== 'n/a';
      };
      
      // Build Basic Information table rows
      let basicInfoRows = `
        <tr><th>Room Number:</th><td>${room.room_number}</td></tr>
        <tr><th>Room Type:</th><td><span class="badge badge-primary">${room.room_type}</span></td></tr>
        <tr><th>Capacity:</th><td>${room.capacity} Guest(s)</td></tr>
      `;
      if (isValid(room.bed_type)) {
        basicInfoRows += `<tr><th>Bed Type:</th><td>${room.bed_type}</td></tr>`;
      }
      if (isValid(room.floor_location)) {
        basicInfoRows += `<tr><th>Floor Location:</th><td>${room.floor_location}</td></tr>`;
      }
      if (isValid(room.bathroom_type)) {
        basicInfoRows += `<tr><th>Bathroom Type:</th><td>${room.bathroom_type}</td></tr>`;
      }
      
      // Helper function to format price with TZS
      const formatPrice = (price) => {
        return `<strong>TZS ${parseFloat(price).toLocaleString()}</strong>`;
      };
      
      // Build Pricing Information table rows
      let pricingRows = `
        <tr><th>Price/Night:</th><td>${formatPrice(room.price_per_night)}</td></tr>
      `;
      if (isValid(room.extra_guest_fee) && parseFloat(room.extra_guest_fee) > 0) {
        pricingRows += `<tr><th>Extra Guest Fee:</th><td>${formatPrice(room.extra_guest_fee)}</td></tr>`;
      }
      if (isValid(room.peak_season_price) && parseFloat(room.peak_season_price) > 0) {
        pricingRows += `<tr><th>Peak Season:</th><td>${formatPrice(room.peak_season_price)}</td></tr>`;
      }
      if (isValid(room.off_season_price) && parseFloat(room.off_season_price) > 0) {
        pricingRows += `<tr><th>Off Season:</th><td>${formatPrice(room.off_season_price)}</td></tr>`;
      }
      if (isValid(room.discount_percentage) && parseFloat(room.discount_percentage) > 0) {
        pricingRows += `<tr><th>Discount:</th><td>${room.discount_percentage}%</td></tr>`;
      }
      if (isValid(room.promo_code)) {
        pricingRows += `<tr><th>Promo Code:</th><td>${room.promo_code}</td></tr>`;
      }
      
      // Build Check-in/out table rows
      let checkinRows = '';
      if (isValid(room.checkin_time)) {
        checkinRows += `<tr><th>Check-in Time:</th><td>${room.checkin_time}</td></tr>`;
      }
      if (isValid(room.checkout_time)) {
        checkinRows += `<tr><th>Check-out Time:</th><td>${room.checkout_time}</td></tr>`;
      }
      
      // Build Additional Info table rows
      let additionalInfoRows = `
        <tr><th>Pet Friendly:</th><td>${room.pet_friendly ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-secondary">No</span>'}</td></tr>
        <tr><th>Smoking Allowed:</th><td>${room.smoking_allowed ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-secondary">No</span>'}</td></tr>
      `;
      if (isValid(room.sku_code)) {
        additionalInfoRows += `<tr><th>SKU Code:</th><td>${room.sku_code}</td></tr>`;
      }
      
      const detailsHtml = `
        <div class="room-details-view">
          <div class="preview-container">
            <div class="row">
              <div class="col-md-6">
                <div class="preview-section h-100">
                  <h5><i class="fa fa-bed"></i> Basic Information</h5>
                  <table class="table table-sm table-bordered">
                    ${basicInfoRows}
                  </table>
                </div>
              </div>
              <div class="col-md-6">
                <div class="preview-section h-100">
                  <h5><i class="fa fa-money"></i> Pricing Information (TZS)</h5>
                  <table class="table table-sm table-bordered">
                    ${pricingRows}
                  </table>
                </div>
              </div>
            </div>
            
            ${room.description ? `
            <div class="preview-section mt-4">
              <h5><i class="fa fa-file-text"></i> Description</h5>
              <p style="white-space: pre-wrap; word-wrap: break-word;">${room.description}</p>
            </div>` : ''}
            
            ${amenitiesHtml ? `
            <div class="preview-section mt-4">
              <h5><i class="fa fa-star"></i> Amenities & Features</h5>
              <div class="amenities-list">${amenitiesHtml}</div>
            </div>` : ''}
            
            <div class="preview-section mt-4">
              <h5><i class="fa fa-image"></i> Room Images</h5>
              <div class="d-flex flex-wrap">${imagesHtml}</div>
            </div>
            
            <div class="row mt-4">
              ${checkinRows ? `
              <div class="col-md-6">
                <div class="preview-section h-100">
                  <h5><i class="fa fa-clock-o"></i> Check-in/out</h5>
                  <table class="table table-sm table-bordered">${checkinRows}</table>
                </div>
              </div>` : ''}
              <div class="col-md-6">
                <div class="preview-section h-100">
                  <h5><i class="fa fa-info-circle"></i> Additional Info</h5>
                  <table class="table table-sm table-bordered">
                    ${additionalInfoRows}
                  </table>
                </div>
              </div>
            </div>
            
            ${room.special_notes ? `
            <div class="preview-section mt-4">
              <h5><i class="fa fa-sticky-note"></i> Special Notes</h5>
              <p class="alert alert-info mb-0">${room.special_notes}</p>
            </div>` : ''}
          </div>
        </div>
      `;
      
      document.getElementById('roomDetailsContent').innerHTML = detailsHtml;
      $('#roomDetailsModal').modal('show');
    } else {
      Swal.fire({
        title: "Error",
        text: "Failed to load room details",
        icon: "error",
        confirmButtonColor: "#940000"
      });
    }
  })
  .catch(error => {
    console.error('Error:', error);
    Swal.fire({
      title: "Error",
      text: "An error occurred while loading room details",
      icon: "error",
      confirmButtonColor: "#940000"
    });
  });
  } catch (error) {
    console.error('Error in viewRoom:', error);
    Swal.fire({
      title: "Error!",
      text: "An error occurred: " + error.message,
      icon: "error",
      confirmButtonColor: "#940000"
    });
  }
}

window.deleteRoom = function(roomId) {
  console.log('deleteRoom called with ID:', roomId);
  try {
    Swal.fire({
    title: "Are you sure?",
    text: "You will not be able to recover this room!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#940000",
    cancelButtonColor: "#d33",
    confirmButtonText: "Yes, delete it!",
    cancelButtonText: "Cancel"
  }).then((result) => {
    if (result.isConfirmed) {
      // Show loading
      Swal.fire({
        title: "Deleting...",
        text: "Please wait while we delete the room.",
        icon: "info",
        showConfirmButton: false,
        allowOutsideClick: false,
        willOpen: () => {
          Swal.showLoading();
        }
      });
      
      // Delete room via AJAX
      fetch('{{ route("admin.rooms.destroy", ":id") }}'.replace(':id', roomId), {
        method: 'DELETE',
        headers: {
          'Accept': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          Swal.fire({
            title: "Deleted!",
            text: data.message || "Room has been deleted.",
            icon: "success",
            confirmButtonColor: "#940000"
          }).then(() => {
            location.reload();
          });
        } else {
          Swal.fire({
            title: "Error!",
            text: data.message || "Failed to delete room.",
            icon: "error",
            confirmButtonColor: "#940000"
          });
        }
      })
      .catch(error => {
        console.error('Error:', error);
        Swal.fire({
          title: "Error!",
          text: "An error occurred while deleting the room.",
          icon: "error",
          confirmButtonColor: "#940000"
        });
      });
    }
  });
  } catch (error) {
    console.error('Error in deleteRoom:', error);
    Swal.fire({
      title: "Error!",
      text: "An error occurred: " + error.message,
      icon: "error",
      confirmButtonColor: "#940000"
    });
  }
}

window.openChangeTypeModal = function(roomId) {
  console.log('openChangeTypeModal called with ID:', roomId);
  try {
    // Fetch room details
    fetch('{{ route("admin.rooms.show", ":id") }}'.replace(':id', roomId), {
    method: 'GET',
    headers: {
      'Accept': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}',
      'X-Requested-With': 'XMLHttpRequest'
    }
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      const room = data.room;
      
      // Set room ID
      document.getElementById('change_type_room_id').value = roomId;
      
      // Display current room info
      const currentType = room.room_type === 'Twins' ? 'Standard Twin Room' : room.room_type;
      document.getElementById('current_room_info').textContent = 
        `Room ${room.room_number} - Currently: ${currentType}`;
      
      // Pre-fill form with current values
      document.getElementById('new_room_type').value = room.room_type;
      document.getElementById('new_capacity').value = room.capacity;
      document.getElementById('new_bed_type').value = room.bed_type || '';
      document.getElementById('new_price_per_night').value = room.price_per_night;
      document.getElementById('new_description').value = room.description || '';
      
      // Update price display
      updatePriceDisplay(room.price_per_night);
      
      // Display current images
      displayCurrentImages(room.images);
      
      // Show modal
      $('#changeTypeModal').modal('show');
    } else {
      Swal.fire({
        title: "Error",
        text: "Failed to load room details",
        icon: "error",
        confirmButtonColor: "#940000"
      });
    }
  })
  .catch(error => {
    console.error('Error:', error);
    Swal.fire({
      title: "Error",
      text: "An error occurred while loading room details",
      icon: "error",
      confirmButtonColor: "#940000"
    });
  });
  } catch (error) {
    console.error('Error in openChangeTypeModal:', error);
    Swal.fire({
      title: "Error!",
      text: "An error occurred: " + error.message,
      icon: "error",
      confirmButtonColor: "#940000"
    });
  }
}

function updatePriceDisplay(price) {
  // Logic removed: only TZS is used directly
}

function displayCurrentImages(images) {
  const previewDiv = document.getElementById('current_images_preview');
  previewDiv.innerHTML = '';
  
  if (images && Array.isArray(images) && images.length > 0) {
    const currentLabel = document.createElement('strong');
    currentLabel.textContent = 'Current Images: ';
    previewDiv.appendChild(currentLabel);
    
    images.forEach((image, index) => {
      // Handle different image path formats
      let imgPath = image;
      if (imgPath.startsWith('storage/')) {
        imgPath = imgPath.substring(8); // Remove 'storage/' prefix
      } else if (!imgPath.startsWith('rooms/') && !imgPath.startsWith('http') && !imgPath.startsWith('/')) {
        imgPath = 'rooms/' + imgPath;
      }
      const imageUrl = imgPath.startsWith('http') ? imgPath : '{{ asset("storage") }}/' + imgPath;
      
      const img = document.createElement('img');
      img.src = imageUrl;
      img.className = 'img-thumbnail m-1';
      img.style.maxWidth = '100px';
      img.style.maxHeight = '100px';
      img.style.objectFit = 'cover';
      img.onerror = function() {
        this.onerror = null;
        this.src = '{{ asset("dashboard_assets/img/placeholder-room.jpg") }}';
        this.style.opacity = '0.5';
      };
      previewDiv.appendChild(img);
    });
  } else {
    previewDiv.innerHTML = '<small class="text-muted">No current images</small>';
  }
}

// Handle price input change
document.addEventListener('DOMContentLoaded', function() {
  const priceInput = document.getElementById('new_price_per_night');
  if (priceInput) {
    priceInput.addEventListener('input', function() {
      if (this.value) {
        updatePriceDisplay(this.value);
      }
    });
  }
  
  // Handle form submission
  const changeTypeForm = document.getElementById('changeTypeForm');
  if (changeTypeForm) {
    changeTypeForm.addEventListener('submit', function(e) {
      e.preventDefault();
      
      const formData = new FormData(this);
      
      // Show loading
      Swal.fire({
        title: "Updating...",
        text: "Please wait while we update the room type.",
        icon: "info",
        showConfirmButton: false,
        allowOutsideClick: false,
        willOpen: () => {
          Swal.showLoading();
        }
      });
      
      fetch('{{ route("admin.rooms.change-type") }}', {
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
          Swal.fire({
            title: "Success!",
            text: data.message || "Room type has been updated successfully.",
            icon: "success",
            confirmButtonColor: "#940000"
          }).then(() => {
            $('#changeTypeModal').modal('hide');
            location.reload();
          });
        } else {
          Swal.fire({
            title: "Error!",
            text: data.message || "Failed to update room type.",
            icon: "error",
            confirmButtonColor: "#940000"
          });
        }
      })
      .catch(error => {
        console.error('Error:', error);
        Swal.fire({
          title: "Error!",
          text: "An error occurred while updating the room type.",
          icon: "error",
          confirmButtonColor: "#940000"
        });
      });
    });
  }
});
</script>
@endsection

