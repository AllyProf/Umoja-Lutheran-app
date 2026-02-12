@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-list"></i> Service Catalog</h1>
    <p>Manage day services offered by the hotel</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">Service Catalog</a></li>
  </ul>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-title-w-btn mb-3">
        <h3 class="title">Available Services</h3>
        <div class="btn-group">
          <a class="btn btn-primary" href="{{ route('admin.service-catalog.create') }}">
            <i class="fa fa-plus"></i> Add New Service
          </a>
        </div>
      </div>
      
      @if($services->count() > 0)
      <!-- Desktop Table View -->
      <div class="table-responsive">
        <table class="table table-hover table-bordered">
          <thead>
            <tr>
              <th>Order</th>
              <th>Service Name</th>
              <th>Service Key</th>
              <th>Pricing Type</th>
              <th>Price (Tanzanian)</th>
              <th>Price (International)</th>
              <th>Payment Required Upfront</th>
              <th>Requires Items</th>
              <th>Status</th>
              <th>Last Edited</th>
              <th>Changes</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($services as $service)
            <tr>
              <td>{{ $service->display_order }}</td>
              <td><strong>{{ $service->service_name }}</strong></td>
              <td><code>{{ $service->service_key }}</code></td>
              <td>{{ $service->pricing_type_name }}</td>
              <td>
                @php
                  $ageGroup = $service->age_group ?? 'both';
                  $hasChildPrice = $service->child_price_tanzanian && $service->child_price_tanzanian > 0;
                @endphp
                
                @if($ageGroup === 'both' && $hasChildPrice)
                  {{-- Show both adult and child prices --}}
                  <strong>Adult:</strong> {{ number_format($service->price_tanzanian, 2) }} TZS<br>
                  <strong>Child:</strong> {{ number_format($service->child_price_tanzanian, 2) }} TZS
                @elseif($ageGroup === 'adult')
                  {{-- Adult only --}}
                  {{ number_format($service->price_tanzanian, 2) }} TZS <small class="text-muted">(Adult Only)</small>
                @elseif($ageGroup === 'child')
                  {{-- Child only --}}
                  @if($service->child_price_tanzanian)
                    {{ number_format($service->child_price_tanzanian, 2) }} TZS <small class="text-muted">(Child Only)</small>
                  @else
                    {{ number_format($service->price_tanzanian, 2) }} TZS <small class="text-muted">(Child Only)</small>
                  @endif
                @else
                  {{-- Default: single price --}}
                  {{ number_format($service->price_tanzanian, 2) }} TZS
                @endif
              </td>
              <td>
                @if($service->price_international)
                  ${{ number_format($service->price_international, 2) }}
                @else
                  <span class="text-muted">Same as Tanzanian</span>
                @endif
              </td>
              <td>
                @if($service->payment_required_upfront)
                  <span class="badge badge-info">Yes</span>
                @else
                  <span class="badge badge-warning">No</span>
                @endif
              </td>
              <td>
                @if($service->requires_items)
                  <span class="badge badge-success">Yes</span>
                @else
                  <span class="badge badge-secondary">No</span>
                @endif
              </td>
              <td>
                @if($service->is_active)
                  <span class="badge badge-success">Active</span>
                @else
                  <span class="badge badge-danger">Inactive</span>
                @endif
              </td>
              <td>
                @if($service->last_edited_at)
                  <small class="text-muted">
                    <i class="fa fa-edit"></i> Edited by <strong>{{ $service->editor->name ?? 'Unknown' }}</strong><br>
                    <i class="fa fa-clock-o"></i> {{ $service->last_edited_at->format('M d, Y H:i') }}
                  </small>
                @else
                  <small class="text-muted">Never edited</small>
                @endif
              </td>
              <td>
                @if($service->last_changes && count($service->last_changes) > 0)
                  <div class="changes-list" style="max-width: 300px;">
                    @foreach($service->last_changes as $change)
                      <div class="mb-1" style="font-size: 11px;">
                        <strong>{{ $change['field'] }}:</strong><br>
                        <span class="text-danger" style="text-decoration: line-through;">{{ $change['old'] }}</span>
                        <i class="fa fa-arrow-right text-muted mx-1"></i>
                        <span class="text-success">{{ $change['new'] }}</span>
                      </div>
                    @endforeach
                  </div>
                @else
                  <small class="text-muted">No changes tracked</small>
                @endif
              </td>
              <td>
                <div class="btn-group">
                  <a href="{{ route('admin.service-catalog.edit', $service) }}" class="btn btn-sm btn-info" title="Edit">
                    <i class="fa fa-edit"></i>
                  </a>
                  <button class="btn btn-sm btn-danger" onclick="deleteService({{ $service->id }})" title="Delete">
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
      <div class="text-center" style="padding: 50px;">
        <i class="fa fa-list fa-5x text-muted mb-3"></i>
        <h3>No Services Configured</h3>
        <p class="text-muted">Start by adding your first service to the catalog.</p>
        <a href="{{ route('admin.service-catalog.create') }}" class="btn btn-primary mt-3">
          <i class="fa fa-plus"></i> Add New Service
        </a>
      </div>
      @endif
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script src="{{ asset('dashboard_assets/js/plugins/sweetalert.min.js') }}"></script>
<script>
function deleteService(serviceId) {
  swal({
    title: "Delete Service?",
    text: "Are you sure you want to delete this service? This action cannot be undone.",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#6c757d",
    confirmButtonText: "Yes, delete it!",
    cancelButtonText: "Cancel"
  }, function(isConfirm) {
    if (isConfirm) {
      fetch('{{ route("admin.service-catalog.destroy", ":id") }}'.replace(':id', serviceId), {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json'
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          swal({
            title: "Deleted!",
            text: data.message || "Service deleted successfully!",
            type: "success",
            confirmButtonColor: "#28a745"
          }, function() {
            location.reload();
          });
        } else {
          swal({
            title: "Error!",
            text: data.message || "Failed to delete service.",
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

