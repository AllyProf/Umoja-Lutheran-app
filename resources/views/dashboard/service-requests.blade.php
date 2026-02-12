@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-list"></i> Service Requests</h1>
    <p>Manage guest service requests</p>
    @if(isset($exchangeRate))
    <p style="font-size: 12px; color: #666; margin-top: 5px;">
      <i class="fa fa-exchange"></i> Exchange Rate: 1 USD = {{ number_format($exchangeRate, 2) }} TZS (Live)
    </p>
    @endif
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ $role === 'manager' ? route('admin.dashboard') : route('reception.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">Service Requests</a></li>
  </ul>
</div>

<!-- Statistics Cards -->
<div class="row mb-3">
  <div class="col-md-3 col-lg-3">
    <div class="widget-small warning coloured-icon">
      <i class="icon fa fa-clock-o fa-2x"></i>
      <div class="info">
        <h4>Pending</h4>
        <p><b>{{ $stats['pending'] ?? 0 }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-lg-3">
    <div class="widget-small info coloured-icon">
      <i class="icon fa fa-check fa-2x"></i>
      <div class="info">
        <h4>Approved</h4>
        <p><b>{{ $stats['approved'] ?? 0 }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-lg-3">
    <div class="widget-small success coloured-icon">
      <i class="icon fa fa-check-circle fa-2x"></i>
      <div class="info">
        <h4>Completed</h4>
        <p><b>{{ $stats['completed'] ?? 0 }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-lg-3">
    <div class="widget-small primary coloured-icon">
      <i class="icon fa fa-calendar fa-2x"></i>
      <div class="info">
        <h4>Today</h4>
        <p><b>{{ $stats['total_today'] ?? 0 }}</b></p>
      </div>
    </div>
  </div>
</div>

<!-- Filters -->
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-body">
        <form method="GET" action="{{ $role === 'manager' ? route('admin.service-requests') : route('reception.service-requests') }}" class="form-inline">
          <div class="form-group mr-2">
            <label for="status" class="mr-2">Status:</label>
            <select name="status" id="status" class="form-control">
              <option value="">All Status</option>
              <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
              <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
              <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
              <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
          </div>
          <button type="submit" class="btn btn-primary">
            <i class="fa fa-filter"></i> Filter
          </button>
          <a href="{{ $role === 'manager' ? route('admin.service-requests') : route('reception.service-requests') }}" class="btn btn-secondary ml-2">
            <i class="fa fa-refresh"></i> Clear
          </a>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Service Requests Table -->
<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title">Service Requests</h3>
      <div class="tile-body">
        @if($serviceRequests->count() > 0)
        <div class="table-responsive">
          <table class="table table-hover table-bordered">
            <thead>
              <tr>
                <th>Request ID</th>
                <th>Booking Reference</th>
                <th>Guest</th>
                <th>Service</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Total</th>
                <th>Status</th>
                <th>Requested At</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($serviceRequests as $request)
              <tr>
                <td><strong>#{{ $request->id }}</strong></td>
                <td>
                  @if($request->booking_id && $request->booking)
                    <a href="{{ route($role == 'reception' ? 'reception.bookings.show' : 'admin.bookings.show', $request->booking_id) }}" target="_blank">
                      {{ $request->booking->booking_reference }}
                    </a>
                  @else
                    <span class="badge badge-secondary">WALK-IN</span>
                  @endif
                </td>
                <td>
                  @if($request->booking_id && $request->booking)
                    <strong>{{ $request->booking->guest_name }}</strong><br>
                    <small>{{ $request->booking->guest_email }}</small>
                  @else
                    <strong>{{ $request->walk_in_name ?? 'Walk-in Guest' }}</strong><br>
                    <small>Direct Sale</small>
                  @endif
                </td>
                <td>
                  <strong>{{ $request->service_specific_data['item_name'] ?? $request->service->name }}</strong><br>
                  <small class="badge badge-secondary">{{ $request->service->category }}</small>
                  @if($request->service_specific_data)
                    <div style="margin-top: 5px; font-size: 11px; color: #666;">
                      @php
                        $summaryData = [];
                        $keyMappings = [
                          'arrival_date' => 'Arrival',
                          'departure_date' => 'Departure',
                          'arrival_time' => 'Time',
                          'departure_time' => 'Time',
                          'flight_number' => 'Flight',
                          'preferred_date' => 'Date',
                          'preferred_time' => 'Time',
                          'service_location' => 'Location',
                          'room_number' => 'Room',
                        ];
                        
                        // Show only most important fields in summary
                        foreach($request->service_specific_data as $key => $value) {
                          if($value && in_array($key, ['arrival_date', 'departure_date', 'arrival_time', 'departure_time', 'flight_number', 'preferred_date', 'preferred_time', 'service_location', 'room_number'])) {
                            $label = $keyMappings[$key] ?? ucfirst(str_replace('_', ' ', $key));
                            $summaryData[] = ['label' => $label, 'value' => $value];
                          }
                        }
                      @endphp
                      @if(count($summaryData) > 0)
                        @foreach(array_slice($summaryData, 0, 3) as $item)
                          <div><strong>{{ $item['label'] }}:</strong> {{ $item['value'] }}</div>
                        @endforeach
                        @if(count($request->service_specific_data) > 3)
                          <button class="btn btn-xs btn-link p-0 mt-1" onclick="viewServiceDetails({{ $request->id }})" style="font-size: 10px; padding: 0;">
                            <i class="fa fa-eye"></i> View More
                          </button>
                        @endif
                      @else
                        <button class="btn btn-xs btn-link p-0 mt-1" onclick="viewServiceDetails({{ $request->id }})" style="font-size: 10px; padding: 0;">
                          <i class="fa fa-eye"></i> View Details
                        </button>
                      @endif
                    </div>
                  @endif
                </td>
                <td>{{ $request->quantity }} {{ $request->service->unit }}</td>
                <td>
                  <div>{{ number_format($request->unit_price_tsh, 2) }} TZS</div>
                  <div style="color: #28a745; font-size: 11px;">
                    ≈ ${{ number_format($request->unit_price_tsh / ($exchangeRate ?? 2500), 2) }}
                  </div>
                </td>
                <td>
                  <div><strong>{{ number_format($request->total_price_tsh, 2) }} TZS</strong></div>
                  <div style="color: #28a745; font-size: 11px;">
                    <strong>≈ ${{ number_format($request->total_price_tsh / ($exchangeRate ?? 2500), 2) }}</strong>
                  </div>
                </td>
                <td>
                  @if($request->status === 'pending')
                    <span class="badge badge-warning">Pending</span>
                  @elseif($request->status === 'approved')
                    <span class="badge badge-info">Approved</span>
                  @elseif($request->status === 'completed')
                    <span class="badge badge-success">Completed</span>
                  @else
                    <span class="badge badge-danger">Cancelled</span>
                  @endif
                </td>
                <td>{{ $request->requested_at->format('M d, Y H:i') }}</td>
                <td>
                  <button class="btn btn-sm btn-info" onclick="viewServiceRequest({{ $request->id }})" title="View Details">
                    <i class="fa fa-eye"></i>
                  </button>
                  @if($request->status === 'pending')
                    <button class="btn btn-sm btn-success" onclick="updateRequestStatus({{ $request->id }}, 'approved')" title="Approve">
                      <i class="fa fa-check"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="updateRequestStatus({{ $request->id }}, 'cancelled')" title="Cancel">
                      <i class="fa fa-times"></i>
                    </button>
                  @elseif($request->status === 'approved')
                    <button class="btn btn-sm btn-primary" onclick="updateRequestStatus({{ $request->id }}, 'completed')" title="Mark as Completed">
                      <i class="fa fa-check-circle"></i>
                    </button>
                  @endif
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        
        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-3">
          {{ $serviceRequests->appends(request()->query())->links('pagination::bootstrap-4') }}
        </div>
        @else
        <div class="text-center" style="padding: 50px;">
          <i class="fa fa-inbox fa-5x text-muted mb-3"></i>
          <h4>No Service Requests</h4>
          <p class="text-muted">No service requests found.</p>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- View Service Request Modal -->
<div class="modal fade" id="viewRequestModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Service Request Details</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="requestDetailsContent">
        <!-- Content loaded via AJAX -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- View Service Details Modal -->
<div class="modal fade" id="serviceDetailsModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-info-circle"></i> Service Request Details</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="serviceDetailsContent">
        <!-- Content loaded dynamically -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Update Status Modal -->
<div class="modal fade" id="updateStatusModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Update Service Request Status</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="updateStatusForm">
          <input type="hidden" id="update_request_id" name="request_id">
          <input type="hidden" id="update_status" name="status">
          <div class="form-group">
            <label for="reception_notes">Reception Notes (Optional)</label>
            <textarea class="form-control" id="reception_notes" name="reception_notes" rows="4" placeholder="Add any notes about this service request..."></textarea>
          </div>
          <div id="updateStatusAlert"></div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="confirmUpdateStatus()">Confirm</button>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script src="{{ asset('dashboard_assets/js/plugins/sweetalert.min.js') }}"></script>
<script>
// Service requests data for modal
const serviceRequestsData = @json($serviceRequestsData ?? []);

function viewServiceDetails(requestId) {
    const request = serviceRequestsData.find(r => r.id == requestId);
    if (!request) {
        swal({
            title: "Error!",
            text: "Service request not found.",
            type: "error",
            confirmButtonColor: "#d33"
        });
        return;
    }
    
    let html = `
        <div class="row">
            <div class="col-md-6">
                <h6 style="color: #e07632; border-bottom: 2px solid #e07632; padding-bottom: 5px; margin-bottom: 15px;">Request Information</h6>
                <table class="table table-borderless">
                    <tr>
                        <td width="40%"><strong>Request ID:</strong></td>
                        <td>#${request.id}</td>
                    </tr>
                    <tr>
                        <td><strong>Booking Reference:</strong></td>
                        <td><strong>${request.booking_reference}</strong></td>
                    </tr>
                    <tr>
                        <td><strong>Service:</strong></td>
                        <td>${request.item_name || request.service_name} <span class="badge badge-secondary">${request.service_category}</span></td>
                    </tr>
                    <tr>
                        <td><strong>Quantity:</strong></td>
                        <td>${request.quantity} ${request.unit}</td>
                    </tr>
                    <tr>
                        <td><strong>Status:</strong></td>
                        <td>
                            ${request.status === 'pending' ? '<span class="badge badge-warning">Pending</span>' : ''}
                            ${request.status === 'approved' ? '<span class="badge badge-info">Approved</span>' : ''}
                            ${request.status === 'completed' ? '<span class="badge badge-success">Completed</span>' : ''}
                            ${request.status === 'cancelled' ? '<span class="badge badge-danger">Cancelled</span>' : ''}
                        </td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6 style="color: #e07632; border-bottom: 2px solid #e07632; padding-bottom: 5px; margin-bottom: 15px;">Guest Information</h6>
                <table class="table table-borderless">
                    <tr>
                        <td width="40%"><strong>Name:</strong></td>
                        <td>${request.guest_name}</td>
                    </tr>
                    <tr>
                        <td><strong>Email:</strong></td>
                        <td>${request.guest_email}</td>
                    </tr>
                    <tr>
                        <td><strong>Phone:</strong></td>
                        <td>${request.guest_phone}</td>
                    </tr>
                    <tr>
                        <td><strong>Room:</strong></td>
                        <td>${request.room_number} (${request.room_type})</td>
                    </tr>
                </table>
            </div>
        </div>
    `;
    
    // Service-specific details
    if (request.service_specific_data && Object.keys(request.service_specific_data).length > 0) {
        html += `
            <hr>
            <h6 style="color: #e07632; border-bottom: 2px solid #e07632; padding-bottom: 5px; margin-bottom: 15px;">Service Details</h6>
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered">
                        <thead style="background-color: #f8f9fa;">
                            <tr>
                                <th>Field</th>
                                <th>Value</th>
                            </tr>
                        </thead>
                        <tbody>
        `;
        
        Object.keys(request.service_specific_data).forEach(key => {
            if (request.service_specific_data[key]) {
                const label = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                html += `
                    <tr>
                        <td><strong>${label}:</strong></td>
                        <td>${request.service_specific_data[key]}</td>
                    </tr>
                `;
            }
        });
        
        html += `
                        </tbody>
                    </table>
                </div>
            </div>
        `;
    }
    
    // Additional notes
    if (request.guest_request) {
        html += `
            <hr>
            <h6 style="color: #e07632; border-bottom: 2px solid #e07632; padding-bottom: 5px; margin-bottom: 15px;">Guest Notes</h6>
            <p style="background-color: #f8f9fa; padding: 10px; border-radius: 4px;">${request.guest_request}</p>
        `;
    }
    
    // Reception notes
    if (request.reception_notes) {
        html += `
            <hr>
            <h6 style="color: #e07632; border-bottom: 2px solid #e07632; padding-bottom: 5px; margin-bottom: 15px;">Reception Notes</h6>
            <p style="background-color: #fff3cd; padding: 10px; border-radius: 4px;">${request.reception_notes}</p>
        `;
    }
    
    // Pricing
    const exchangeRate = {{ $exchangeRate ?? 2500 }};
    html += `
        <hr>
        <h6 style="color: #e07632; border-bottom: 2px solid #e07632; padding-bottom: 5px; margin-bottom: 15px;">Pricing</h6>
        <table class="table table-bordered">
            <tr>
                <td width="40%"><strong>Unit Price:</strong></td>
                <td>
                    ${parseFloat(request.unit_price_tsh).toLocaleString()} TZS<br>
                    <small style="color: #28a745;">≈ $${(parseFloat(request.unit_price_tsh) / exchangeRate).toFixed(2)}</small>
                </td>
            </tr>
            <tr>
                <td><strong>Quantity:</strong></td>
                <td>${request.quantity}</td>
            </tr>
            <tr style="background-color: #f8f9fa;">
                <td><strong>Total Price:</strong></td>
                <td>
                    <strong>${parseFloat(request.total_price_tsh).toLocaleString()} TZS</strong><br>
                    <strong style="color: #28a745;">≈ $${(parseFloat(request.total_price_tsh) / exchangeRate).toFixed(2)}</strong>
                </td>
            </tr>
        </table>
        <p class="text-muted" style="font-size: 11px; margin-top: 10px;">
            <i class="fa fa-info-circle"></i> Exchange Rate: 1 USD = ${exchangeRate.toLocaleString()} TZS (via Frankfurter.app)
        </p>
    `;
    
    // Timestamps
    html += `
        <hr>
        <h6 style="color: #e07632; border-bottom: 2px solid #e07632; padding-bottom: 5px; margin-bottom: 15px;">Timeline</h6>
        <table class="table table-borderless">
            <tr>
                <td width="40%"><strong>Requested At:</strong></td>
                <td>${request.requested_at}</td>
            </tr>
            ${request.approved_at ? `<tr><td><strong>Approved At:</strong></td><td>${request.approved_at}</td></tr>` : ''}
            ${request.completed_at ? `<tr><td><strong>Completed At:</strong></td><td>${request.completed_at}</td></tr>` : ''}
        </table>
    `;
    
    document.getElementById('serviceDetailsContent').innerHTML = html;
    $('#serviceDetailsModal').modal('show');
}

function viewServiceRequest(requestId) {
    viewServiceDetails(requestId);
}

let currentRequestId = null;
let currentStatus = null;

function updateRequestStatus(requestId, status) {
    currentRequestId = requestId;
    currentStatus = status;
    document.getElementById('update_request_id').value = requestId;
    document.getElementById('update_status').value = status;
    document.getElementById('reception_notes').value = '';
    document.getElementById('updateStatusAlert').innerHTML = '';
    
    const statusLabels = {
        'approved': 'Approve',
        'completed': 'Mark as Completed',
        'cancelled': 'Cancel'
    };
    
    $('#updateStatusModal').modal('show');
}

function confirmUpdateStatus() {
    const requestId = document.getElementById('update_request_id').value;
    const status = document.getElementById('update_status').value;
    const notes = document.getElementById('reception_notes').value;
    const alertContainer = document.getElementById('updateStatusAlert');
    
    alertContainer.innerHTML = '<div class="alert alert-info">Updating...</div>';
    
    const updateUrl = '{{ $role === 'manager' ? route("admin.service-requests.update", ":id") : route("reception.service-requests.update", ":id") }}'.replace(':id', requestId);
    fetch(updateUrl, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            status: status,
            reception_notes: notes
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            swal({
                title: "Success!",
                text: data.message || "Status updated successfully!",
                type: "success",
                confirmButtonColor: "#28a745"
            }, function() {
                $('#updateStatusModal').modal('hide');
                location.reload();
            });
        } else {
            alertContainer.innerHTML = '<div class="alert alert-danger">' + (data.message || 'Failed to update status.') + '</div>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alertContainer.innerHTML = '<div class="alert alert-danger">An error occurred. Please try again.</div>';
    });
}
</script>
@endsection

