@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-edit"></i> Bulk Edit Rooms</h1>
    <p>Edit multiple rooms at once</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ url('/admin/dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.rooms.index') }}">Rooms</a></li>
    <li class="breadcrumb-item"><a href="#">Bulk Edit</a></li>
  </ul>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title">Bulk Edit {{ $rooms->count() }} Room(s)</h3>
      
      <div class="alert alert-info">
        <i class="fa fa-info-circle"></i> <strong>Note:</strong> Only fill in the fields you want to update. Leave fields empty to keep their current values.
      </div>

      <!-- Selected Rooms List -->
      <div class="card mb-4">
        <div class="card-header">
          <h5><i class="fa fa-list"></i> Selected Rooms ({{ $rooms->count() }})</h5>
        </div>
        <div class="card-body">
          <!-- Filter by Room Type -->
          @if($rooms->pluck('room_type')->unique()->count() > 1)
          <div class="mb-3">
            <label><strong>Filter by Room Type:</strong></label>
            <div class="btn-group" role="group">
              <button type="button" class="btn btn-sm btn-outline-primary active" onclick="filterRoomsByType('all', this)">
                All Types ({{ $rooms->count() }})
              </button>
              <button type="button" class="btn btn-sm btn-outline-primary" onclick="filterRoomsByType('Single', this)">
                Single ({{ $rooms->where('room_type', 'Single')->count() }})
              </button>
              <button type="button" class="btn btn-sm btn-outline-primary" onclick="filterRoomsByType('Double', this)">
                Double ({{ $rooms->where('room_type', 'Double')->count() }})
              </button>
              <button type="button" class="btn btn-sm btn-outline-primary" onclick="filterRoomsByType('Twins', this)">
                Standard Twin ({{ $rooms->where('room_type', 'Twins')->count() }})
              </button>
            </div>
            @if(isset($filteredType))
            <div class="alert alert-info mt-2">
              <i class="fa fa-info-circle"></i> Showing only <strong>{{ $filteredType === 'Twins' ? 'Standard Twin' : $filteredType }}</strong> rooms. 
              <a href="{{ route('admin.rooms.bulk-edit', ['room_ids' => $roomIds]) }}" class="alert-link">Show all selected rooms</a>
            </div>
            @endif
          </div>
          @else
          <div class="alert alert-info">
            <i class="fa fa-info-circle"></i> All selected rooms are of type: 
            <strong>{{ $rooms->first()->room_type === 'Twins' ? 'Standard Twin Room' : $rooms->first()->room_type }}</strong>
          </div>
          @endif
          
          <div class="row" id="roomsList">
            @foreach($rooms as $room)
            <div class="col-md-3 mb-2 room-item" data-room-type="{{ $room->room_type }}">
              <div class="badge badge-primary p-2">
                <strong>{{ $room->room_number }}</strong> - 
                @if($room->room_type === 'Twins')
                  Standard Twin Room
                @else
                  {{ $room->room_type }}
                @endif
              </div>
            </div>
            @endforeach
          </div>
          
          <div id="noRoomsMessage" class="alert alert-info" style="display: none;">
            <i class="fa fa-info-circle"></i> No rooms match the selected filter.
          </div>
        </div>
      </div>

      <form id="bulkEditForm" method="POST" action="{{ route('admin.rooms.bulk-update') }}">
        @csrf
        
        @foreach($roomIds as $roomId)
          <input type="hidden" name="room_ids[]" value="{{ $roomId }}">
        @endforeach

        <!-- Pricing Section -->
        <div class="card mb-4">
          <div class="card-header">
            <h5><i class="fa fa-money"></i> Pricing (TZS)</h5>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label for="price_per_night">Price per Night (TZS)</label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">TZS</span>
                    </div>
                    <input class="form-control" type="number" id="price_per_night" name="update_fields[price_per_night]" min="0" placeholder="0">
                  </div>
                  <small class="form-text text-muted">Update price for all selected rooms (Leave empty to keep current)</small>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Basic Information -->
        <div class="card mb-4">
          <div class="card-header">
            <h5><i class="fa fa-info-circle"></i> Basic Information</h5>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="capacity">Capacity (Max Guests)</label>
                  <input class="form-control" type="number" id="capacity" name="update_fields[capacity]" min="1" max="10" placeholder="Leave empty to keep current">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="bed_type">Bed Type</label>
                  <select class="form-control" id="bed_type" name="update_fields[bed_type]">
                    <option value="">-- Keep Current --</option>
                    <option value="King">King</option>
                    <option value="Queen">Queen</option>
                    <option value="Twin">Twin</option>
                    <option value="Bunk">Bunk</option>
                    <option value="Single">Single</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="status">Status</label>
                  <select class="form-control" id="status" name="update_fields[status]">
                    <option value="">-- Keep Current --</option>
                    <option value="available">Available</option>
                    <option value="occupied">Occupied</option>
                    <option value="to_be_cleaned">To Be Cleaned</option>
                    <option value="maintenance">Maintenance</option>
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="floor_location">Floor Location</label>
                  <input class="form-control" type="text" id="floor_location" name="update_fields[floor_location]" placeholder="Leave empty to keep current">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label for="description">Description</label>
                  <textarea class="form-control" id="description" name="update_fields[description]" rows="3" placeholder="Leave empty to keep current"></textarea>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Additional Features -->
        <div class="card mb-4">
          <div class="card-header">
            <h5><i class="fa fa-star"></i> Additional Features</h5>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="bathroom_type">Bathroom Type</label>
                  <input class="form-control" type="text" id="bathroom_type" name="update_fields[bathroom_type]" placeholder="Leave empty to keep current">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="pet_friendly">Pet Friendly</label>
                  <select class="form-control" id="pet_friendly" name="update_fields[pet_friendly]">
                    <option value="">-- Keep Current --</option>
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="smoking_allowed">Smoking Allowed</label>
                  <select class="form-control" id="smoking_allowed" name="update_fields[smoking_allowed]">
                    <option value="">-- Keep Current --</option>
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                  </select>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions mt-4 pt-3 border-top">
          <button type="button" class="btn btn-secondary" onclick="window.location.href='{{ route('admin.rooms.index') }}'">
            <i class="fa fa-times"></i> Cancel
          </button>
          <button type="submit" class="btn btn-primary float-right">
            <i class="fa fa-save"></i> Update All Selected Rooms
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('dashboard_assets/js/plugins/sweetalert.min.js') }}"></script>
<script>
// Filter rooms by type in the display
function filterRoomsByType(type, buttonElement) {
  const roomItems = document.querySelectorAll('.room-item');
  let visibleCount = 0;
  
  roomItems.forEach(item => {
    const roomType = item.getAttribute('data-room-type');
    
    if (type === 'all' || roomType === type) {
      item.style.display = '';
      visibleCount++;
    } else {
      item.style.display = 'none';
    }
  });
  
  // Show/hide no rooms message
  const noRoomsMessage = document.getElementById('noRoomsMessage');
  if (visibleCount === 0) {
    noRoomsMessage.style.display = 'block';
  } else {
    noRoomsMessage.style.display = 'none';
  }
  
  // Update button states
  if (buttonElement) {
    document.querySelectorAll('.btn-group button').forEach(btn => {
      btn.classList.remove('active');
    });
    buttonElement.classList.add('active');
  }
}

// Initialize - show filtered type if provided, otherwise all
document.addEventListener('DOMContentLoaded', function() {
  @if(isset($filteredType))
    // Auto-filter to the selected type
    const typeButtons = document.querySelectorAll('.btn-group button');
    typeButtons.forEach(btn => {
      if (btn.textContent.includes('{{ $filteredType === "Twins" ? "Standard Twin" : $filteredType }}')) {
        filterRoomsByType('{{ $filteredType }}', btn);
      }
    });
  @else
    // Set first button as active
    const firstBtn = document.querySelector('.btn-group button');
    if (firstBtn) {
      firstBtn.classList.add('active');
    }
  @endif
});

document.getElementById('bulkEditForm').addEventListener('submit', function(e) {
  e.preventDefault();
  
  // Check if at least one field is filled
  const formData = new FormData(this);
  let hasFields = false;
  
  for (let [key, value] of formData.entries()) {
    if (key.startsWith('update_fields[') && value !== '') {
      hasFields = true;
      break;
    }
  }
  
  if (!hasFields) {
    swal({
      title: "No Changes",
      text: "Please fill in at least one field to update.",
      type: "warning",
      confirmButtonColor: "#940000"
    });
    return;
  }
  
  // Confirm before submitting
  swal({
    title: "Update Rooms?",
    text: "Are you sure you want to update {{ $rooms->count() }} room(s) with the provided information?",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor: "#940000",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "Yes, update!",
    cancelButtonText: "Cancel"
  }, function(isConfirm) {
    if (isConfirm) {
      // Show loading
      swal({
        title: "Updating...",
        text: "Please wait while we update the rooms.",
        type: "info",
        showConfirmButton: false,
        allowOutsideClick: false
      });
      
      // Submit form
      const form = document.getElementById('bulkEditForm');
      const formData = new FormData(form);
      
      fetch('{{ route("admin.rooms.bulk-update") }}', {
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
            text: data.message,
            type: "success",
            confirmButtonColor: "#940000"
          }, function() {
            window.location.href = '{{ route("admin.rooms.index") }}';
          });
        } else {
          swal({
            title: "Error!",
            text: data.message || "An error occurred.",
            type: "error",
            confirmButtonColor: "#940000"
          });
        }
      })
      .catch(error => {
        console.error('Error:', error);
        swal({
          title: "Error!",
          text: "An error occurred while updating rooms.",
          type: "error",
          confirmButtonColor: "#940000"
        });
      });
    }
  });
});
</script>
@endsection

