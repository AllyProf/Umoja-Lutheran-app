@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-cogs"></i> Services Management</h1>
    <p>Manage hotel services and their pricing</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">Services</a></li>
  </ul>
</div>

@if(session('success'))
<div class="row mb-3">
  <div class="col-md-12">
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="fa fa-check-circle"></i> {{ session('success') }}
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
  </div>
</div>
@endif

@if(session('error'))
<div class="row mb-3">
  <div class="col-md-12">
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <i class="fa fa-exclamation-circle"></i> {{ session('error') }}
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
  </div>
</div>
@endif

<!-- Filters and Actions -->
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-title-w-btn">
        <h3 class="title">All Services</h3>
        <p><a class="btn btn-primary icon-btn" href="{{ route('admin.services.create') }}">
          <i class="fa fa-plus"></i> Add New Service
        </a></p>
      </div>
      <div class="tile-body">
        <div class="row mb-3">
          <div class="col-md-4">
            <div class="form-group">
              <label for="filter_category">Filter by Category</label>
              <select class="form-control" id="filter_category" onchange="filterServices()">
                <option value="">All Categories</option>
                <option value="swimming">Swimming / Pool</option>
                <option value="recreation">Recreation</option>
                <option value="laundry">Laundry</option>
                <option value="spa">Spa / Wellness</option>
                <option value="room_service">Room Service</option>
                <option value="photography">Photography</option>
                <option value="general">General</option>
              </select>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label for="filter_status">Filter by Status</label>
              <select class="form-control" id="filter_status" onchange="filterServices()">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label for="search_service">Search</label>
              <input type="text" class="form-control" id="search_service" placeholder="Search by name, description..." onkeyup="filterServices()">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <button class="btn btn-secondary" onclick="resetFilters()">
              <i class="fa fa-refresh"></i> Reset Filters
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Services List -->
<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-body">
        @if($services->count() > 0)
        <div class="table-responsive">
          <table class="table table-hover table-bordered" id="servicesTable">
            <thead>
              <tr>
                <th>Name</th>
                <th>Category</th>
                <th>Description</th>
                <th>Price (TZS)</th>
                <th>Unit</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($services as $service)
              <tr data-category="{{ strtolower($service->category) }}"
                  data-status="{{ $service->is_active ? 'active' : 'inactive' }}"
                  data-service-name="{{ strtolower($service->name) }}"
                  data-service-description="{{ strtolower($service->description ?? '') }}">
                <td><strong>{{ $service->name }}</strong></td>
                <td>
                  @php
                    $categoryNames = [
                      'swimming' => 'Swimming / Pool',
                      'recreation' => 'Recreation',
                      'laundry' => 'Laundry',
                      'spa' => 'Spa / Wellness',
                      'room_service' => 'Room Service',
                      'photography' => 'Photography',
                      'general' => 'General',
                      'transport' => 'Transport', // Legacy
                      'food' => 'Food', // Legacy
                    ];
                    $categoryDisplay = $categoryNames[$service->category] ?? ucfirst(str_replace('_', ' ', $service->category));
                  @endphp
                  <span class="badge badge-info">{{ $categoryDisplay }}</span>
                </td>
                <td>{{ Str::limit($service->description ?? 'N/A', 50) }}</td>
                <td>
                  @if($service->is_free_for_internal)
                    <span class="badge badge-success">Free for Internal Guests</span>
                    <br><small class="text-muted">Price: 0.00 TZS</small>
                  @else
                    @php
                      $ageGroup = $service->age_group ?? 'both';
                      $showAdultPrice = ($ageGroup === 'adult' || $ageGroup === 'both');
                      // Check if child price exists and is greater than 0 (handle both null and 0 cases)
                      $childPriceValue = $service->child_price_tsh ?? null;
                      $hasChildPrice = $childPriceValue !== null && $childPriceValue != 0 && $childPriceValue > 0;
                      $showChildPrice = ($ageGroup === 'child' || $ageGroup === 'both') && $hasChildPrice;
                    @endphp
                    @if($showAdultPrice)
                      <strong>Adult: {{ number_format($service->price_tsh, 2) }} TZS</strong>
                    @endif
                    @if($showChildPrice)
                      @if($showAdultPrice)<br>@endif
                      <strong>Child: {{ number_format($service->child_price_tsh, 2) }} TZS</strong>
                    @elseif($ageGroup === 'child')
                      <strong>{{ number_format($service->price_tsh, 2) }} TZS</strong>
                    @elseif($ageGroup === 'both' && $showAdultPrice && !$hasChildPrice)
                      <br><small class="text-muted">(Same price for children)</small>
                    @endif
                  @endif
                </td>
                <td>
                  @php
                    $unitNames = [
                      'per_session' => 'Per Session',
                      'per_hour' => 'Per Hour',
                      'per_day' => 'Per Day',
                      'per_person' => 'Per Person',
                      'per_item' => 'Per Item',
                      'per_photo' => 'Per Photo',
                      'per_package' => 'Per Package',
                      'per_kg' => 'Per Kilogram',
                      'per_trip' => 'Per Trip', // Legacy
                    ];
                    $unitDisplay = $unitNames[$service->unit] ?? ucfirst(str_replace('_', ' ', $service->unit));
                    $ageGroupDisplay = [
                      'adult' => 'Adult Only',
                      'child' => 'Child Only',
                      'both' => 'Adult & Child'
                    ];
                    $ageGroupText = $ageGroupDisplay[$service->age_group ?? 'both'] ?? 'Adult & Child';
                  @endphp
                  {{ $unitDisplay }}
                  @if(($service->category ?? '') === 'swimming' || ($service->category ?? '') === 'recreation')
                    <br><small class="text-muted">({{ $ageGroupText }})</small>
                  @endif
                </td>
                <td>
                  @if($service->is_active)
                    <span class="badge badge-success">Active</span>
                  @else
                    <span class="badge badge-secondary">Inactive</span>
                  @endif
                </td>
                <td>
                  <a href="{{ route('admin.services.edit', $service) }}" class="btn btn-sm btn-primary" title="Edit">
                    <i class="fa fa-edit"></i> Edit
                  </a>
                  <button class="btn btn-sm btn-danger" onclick="deleteService({{ $service->id }}, '{{ $service->name }}')" title="Delete">
                    <i class="fa fa-trash"></i> Delete
                  </button>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <div id="noResults" class="alert alert-info text-center" style="display: none;">
          <i class="fa fa-info-circle"></i> No services found matching your filters.
        </div>
        @else
        <div class="text-center" style="padding: 50px;">
          <i class="fa fa-cogs fa-5x text-muted mb-3"></i>
          <h3>No Services Found</h3>
          <p class="text-muted">Get started by adding your first service.</p>
          <a href="{{ route('admin.services.create') }}" class="btn btn-primary">
            <i class="fa fa-plus"></i> Add New Service
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
function filterServices() {
  const categoryFilter = document.getElementById('filter_category').value.toLowerCase();
  const statusFilter = document.getElementById('filter_status').value.toLowerCase();
  const searchTerm = document.getElementById('search_service').value.toLowerCase();
  
  const rows = document.querySelectorAll('#servicesTable tbody tr');
  let visibleCount = 0;
  
  rows.forEach(row => {
    const rowCategory = row.getAttribute('data-category');
    const rowStatus = row.getAttribute('data-status');
    const serviceName = row.getAttribute('data-service-name');
    const serviceDescription = row.getAttribute('data-service-description');
    
    let show = true;
    
    // Category filter
    if (categoryFilter && rowCategory !== categoryFilter) {
      show = false;
    }
    
    // Status filter
    if (statusFilter && rowStatus !== statusFilter) {
      show = false;
    }
    
    // Search filter
    if (searchTerm) {
      if (!serviceName.includes(searchTerm) && !serviceDescription.includes(searchTerm)) {
        show = false;
      }
    }
    
    if (show) {
      row.style.display = '';
      visibleCount++;
    } else {
      row.style.display = 'none';
    }
  });
  
  // Show/hide no results message
  const noResults = document.getElementById('noResults');
  if (noResults) {
    if (visibleCount === 0) {
      noResults.style.display = 'block';
    } else {
      noResults.style.display = 'none';
    }
  }
}

function resetFilters() {
  document.getElementById('filter_category').value = '';
  document.getElementById('filter_status').value = '';
  document.getElementById('search_service').value = '';
  filterServices();
}

function deleteService(serviceId, serviceName) {
  swal({
    title: "Delete Service?",
    text: "Are you sure you want to delete \"" + serviceName + "\"? This action cannot be undone.",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#6c757d",
    confirmButtonText: "Yes, Delete!",
    cancelButtonText: "Cancel",
    closeOnConfirm: false,
    showLoaderOnConfirm: true
  }, function(isConfirm) {
    if (isConfirm) {
      fetch('{{ route("admin.services.delete", ":id") }}'.replace(':id', serviceId), {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          swal({
            title: "Deleted!",
            text: data.message || "Service deleted successfully.",
            type: "success",
            confirmButtonColor: "#28a745"
          }, function() {
            location.reload();
          });
        } else {
          swal({
            title: "Error!",
            text: data.message || "Failed to delete service. Please try again.",
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
      });
    }
  });
}
</script>
@endsection

