@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-clock-o"></i> Pending Payments</h1>
    <p>Process payments for restaurant and bar services</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ $role === 'reception' ? route('reception.dashboard') : route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ $role === 'reception' ? route('reception.day-services.index') : route('admin.day-services.index') }}">Day Services</a></li>
    <li class="breadcrumb-item"><a href="#">Pending</a></li>
  </ul>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-title-w-btn mb-3">
        <h3 class="title">Pending Payment Services</h3>
        <div class="btn-group">
          <a class="btn btn-primary" href="{{ $role === 'reception' ? route('reception.day-services.create') : route('admin.day-services.create') }}">
            <i class="fa fa-plus"></i> Register Service
          </a>
          <a class="btn btn-info" href="{{ $role === 'reception' ? route('reception.day-services.index') : route('admin.day-services.index') }}">
            <i class="fa fa-list"></i> All Services
          </a>
        </div>
      </div>
      
      @if($pendingServices->count() > 0)
      <!-- Desktop Table View -->
      <div class="table-responsive">
        <table class="table table-hover table-bordered">
          <thead>
            <tr>
              <th>Reference</th>
              <th>Service Type</th>
              <th>Guest Name</th>
              <th>Phone</th>
              <th>Date & Time</th>
              <th>People</th>
              <th>Items Ordered</th>
              <th>Amount</th>
              <th>Registered By</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($pendingServices as $service)
            <tr>
              <td><strong>{{ $service->service_reference }}</strong></td>
              <td>
                <span class="badge badge-info">{{ $service->service_type_name }}</span>
              </td>
              <td>{{ $service->guest_name }}</td>
              <td>{{ $service->guest_phone ?? 'N/A' }}</td>
              <td>
                {{ $service->service_date->format('M d, Y') }}<br>
                <small class="text-muted">{{ $service->service_time }}</small>
              </td>
              <td>{{ $service->number_of_people }}</td>
              <td>{{ Str::limit($service->items_ordered ?? 'N/A', 50) }}</td>
              <td>
                @if($service->guest_type === 'tanzanian')
                  {{ number_format($service->amount, 2) }} TZS
                @else
                  ${{ number_format($service->amount, 2) }}
                  @if($service->exchange_rate)
                    <br><small class="text-muted">≈ {{ number_format($service->amount * $service->exchange_rate, 2) }} TZS</small>
                  @endif
                @endif
              </td>
              <td>{{ $service->registeredBy->name ?? 'N/A' }}</td>
              <td>
                <div class="btn-group">
                  <button class="btn btn-sm btn-info" onclick="viewService({{ $service->id }})" title="View Details">
                    <i class="fa fa-eye"></i>
                  </button>
                  <button class="btn btn-sm btn-warning" onclick="processPayment({{ $service->id }})" title="Process Payment">
                    <i class="fa fa-money"></i> Pay
                  </button>
                </div>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      
      <!-- Pagination -->
      <div class="d-flex justify-content-center mt-3">
        {{ $pendingServices->links() }}
      </div>
      @else
      <div class="text-center" style="padding: 50px;">
        <i class="fa fa-check-circle fa-5x text-success mb-3"></i>
        <h3>No Pending Payments</h3>
        <p class="text-muted">All services have been paid.</p>
        <a href="{{ $role === 'reception' ? route('reception.day-services.create') : route('admin.day-services.create') }}" class="btn btn-primary mt-3">
          <i class="fa fa-plus"></i> Register New Service
        </a>
      </div>
      @endif
    </div>
  </div>
</div>

<!-- Service Details Modal -->
<div class="modal fade" id="serviceDetailsModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background: #940000; color: white;">
        <h5 class="modal-title">Service Details</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="serviceDetailsContent">
        <!-- Content will be loaded here -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Process Payment Modal -->
<div class="modal fade" id="processPaymentModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background: #ffc107; color: white;">
        <h5 class="modal-title"><i class="fa fa-money"></i> Process Payment</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="paymentForm">
          <input type="hidden" id="payment_service_id" name="service_id">
          <div class="form-group">
            <label for="payment_items_ordered">Items Ordered</label>
            <textarea class="form-control" id="payment_items_ordered" name="items_ordered" rows="3" placeholder="Enter items ordered..."></textarea>
          </div>
          <div class="form-group">
            <label for="payment_amount">Amount <span class="text-danger">*</span></label>
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text" id="payment_currency_symbol">TZS</span>
              </div>
              <input class="form-control" type="number" id="payment_amount" name="amount" step="0.01" min="0" required>
            </div>
          </div>
          <div class="form-group">
            <label for="payment_method">Payment Method <span class="text-danger">*</span></label>
            <select class="form-control" id="payment_method" name="payment_method" required>
              <option value="">Select payment method...</option>
              <option value="cash">Cash</option>
              <option value="card">Card</option>
              <option value="mobile">Mobile Money</option>
              <option value="bank">Bank Transfer</option>
              <option value="online">Online Payment</option>
              <option value="other">Other</option>
            </select>
          </div>
          <div class="row" id="payment_provider_section" style="display: none;">
            <div class="col-md-6">
              <div class="form-group">
                <label for="payment_provider">Payment Provider</label>
                <select class="form-control" id="payment_provider" name="payment_provider">
                  <option value="">Select provider...</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="payment_reference">Reference Number</label>
                <input class="form-control" type="text" id="payment_reference" name="payment_reference" placeholder="Enter transaction reference">
              </div>
            </div>
          </div>
          <div class="form-group">
            <label for="payment_amount_paid">Amount Paid <span class="text-danger">*</span></label>
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text" id="payment_paid_currency_symbol">TZS</span>
              </div>
              <input class="form-control" type="number" id="payment_amount_paid" name="amount_paid" step="0.01" min="0" required>
            </div>
          </div>
          <div id="paymentAlert"></div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-warning" onclick="submitPayment()">
          <i class="fa fa-check"></i> Process Payment
        </button>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script src="{{ asset('dashboard_assets/js/plugins/sweetalert.min.js') }}"></script>
<script>
function viewService(serviceId) {
  $('#serviceDetailsModal').modal('show');
  document.getElementById('serviceDetailsContent').innerHTML = `
    <div class="text-center">
      <i class="fa fa-spinner fa-spin fa-2x"></i>
      <p>Loading service details...</p>
    </div>
  `;
  
  @php
    $showRoute = ($role === 'reception') ? 'reception.day-services.show' : 'admin.day-services.show';
  @endphp
  
  fetch('{{ route($showRoute, ":id") }}'.replace(':id', serviceId), {
    method: 'GET',
    headers: {
      'Accept': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    }
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      const service = data.day_service;
      const serviceDate = new Date(service.service_date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
      
      document.getElementById('serviceDetailsContent').innerHTML = `
        <div class="row">
          <div class="col-md-6">
            <h5><i class="fa fa-info-circle"></i> Service Information</h5>
            <table class="table table-sm table-bordered">
              <tr><td><strong>Reference:</strong></td><td>${service.service_reference}</td></tr>
              <tr><td><strong>Service Type:</strong></td><td><span class="badge badge-info">${service.service_type_name}</span></td></tr>
              <tr><td><strong>Date:</strong></td><td>${serviceDate}</td></tr>
              <tr><td><strong>Time:</strong></td><td>${service.service_time}</td></tr>
              <tr><td><strong>Number of People:</strong></td><td>${service.number_of_people}</td></tr>
              ${service.items_ordered ? `<tr><td><strong>Items Ordered:</strong></td><td>${service.items_ordered}</td></tr>` : ''}
            </table>
          </div>
          <div class="col-md-6">
            <h5><i class="fa fa-user"></i> Guest Information</h5>
            <table class="table table-sm table-bordered">
              <tr><td><strong>Name:</strong></td><td>${service.guest_name}</td></tr>
              <tr><td><strong>Phone:</strong></td><td>${service.guest_phone || 'N/A'}</td></tr>
              <tr><td><strong>Email:</strong></td><td>${service.guest_email || 'N/A'}</td></tr>
              <tr><td><strong>Guest Type:</strong></td><td>${service.guest_type === 'tanzanian' ? 'Tanzanian' : 'International'}</td></tr>
            </table>
          </div>
        </div>
        <div class="row mt-3">
          <div class="col-md-12">
            <h5><i class="fa fa-dollar"></i> Payment Information</h5>
            <table class="table table-sm table-bordered">
              <tr><td><strong>Amount:</strong></td><td>${service.guest_type === 'tanzanian' ? service.amount + ' TZS' : '$' + parseFloat(service.amount).toFixed(2) + (service.exchange_rate ? ' (≈ ' + (service.amount * service.exchange_rate).toFixed(2) + ' TZS)' : '')}</td></tr>
              <tr><td><strong>Payment Status:</strong></td><td><span class="badge badge-warning">Pending</span></td></tr>
            </table>
          </div>
        </div>
        ${service.notes ? `<div class="row mt-3"><div class="col-md-12"><p><strong>Notes:</strong> ${service.notes}</p></div></div>` : ''}
      `;
    }
  })
  .catch(error => {
    console.error('Error:', error);
    document.getElementById('serviceDetailsContent').innerHTML = `
      <div class="alert alert-danger">An error occurred while loading service details.</div>
    `;
  });
}

function processPayment(serviceId) {
  $('#processPaymentModal').modal('show');
  document.getElementById('payment_service_id').value = serviceId;
  document.getElementById('paymentForm').reset();
  document.getElementById('paymentAlert').innerHTML = '';
  
  // Load service details
  @php
    $showRoute = ($role === 'reception') ? 'reception.day-services.show' : 'admin.day-services.show';
  @endphp
  
  fetch('{{ route($showRoute, ":id") }}'.replace(':id', serviceId), {
    method: 'GET',
    headers: {
      'Accept': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    }
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      const service = data.day_service;
      document.getElementById('payment_items_ordered').value = service.items_ordered || '';
      document.getElementById('payment_amount').value = service.amount || '';
      
      const currencySymbol = service.guest_type === 'tanzanian' ? 'TZS' : 'USD';
      document.getElementById('payment_currency_symbol').textContent = currencySymbol;
      document.getElementById('payment_paid_currency_symbol').textContent = currencySymbol;
    }
  });
}

// Payment method change handler
document.getElementById('payment_method').addEventListener('change', function() {
  const paymentMethod = this.value;
  const providerSection = document.getElementById('payment_provider_section');
  
  if (paymentMethod === 'mobile') {
    providerSection.style.display = 'block';
    document.getElementById('payment_provider').innerHTML = `
      <option value="">Select provider...</option>
      <option value="M-PESA">M-PESA</option>
      <option value="HALOPESA">HALOPESA</option>
      <option value="MIXX BY YAS">MIXX BY YAS</option>
      <option value="AIRTEL MONEY">AIRTEL MONEY</option>
    `;
  } else if (paymentMethod === 'bank') {
    providerSection.style.display = 'block';
    document.getElementById('payment_provider').innerHTML = `
      <option value="">Select provider...</option>
      <option value="NMB">NMB</option>
      <option value="CRDB">CRDB</option>
      <option value="KCB">KCB BANK</option>
      <option value="NBC">NBC</option>
      <option value="EXIM">EXIM</option>
    `;
  } else {
    providerSection.style.display = 'none';
    document.getElementById('payment_provider').value = '';
    document.getElementById('payment_reference').value = '';
  }
});

function submitPayment() {
  const form = document.getElementById('paymentForm');
  const alertDiv = document.getElementById('paymentAlert');
  const submitBtn = event.target;
  const originalText = submitBtn.innerHTML;
  
  if (!form.checkValidity()) {
    form.reportValidity();
    return;
  }
  
  submitBtn.disabled = true;
  submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Processing...';
  
  const serviceId = document.getElementById('payment_service_id').value;
  const formData = {
    items_ordered: document.getElementById('payment_items_ordered').value,
    amount: document.getElementById('payment_amount').value,
    payment_method: document.getElementById('payment_method').value,
    payment_provider: document.getElementById('payment_provider').value,
    payment_reference: document.getElementById('payment_reference').value,
    amount_paid: document.getElementById('payment_amount_paid').value,
  };
  
  @php
    $paymentRoute = ($role === 'reception') ? 'reception.day-services.payment' : 'admin.day-services.payment';
  @endphp
  
  fetch('{{ route($paymentRoute, ":id") }}'.replace(':id', serviceId), {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}',
      'Accept': 'application/json'
    },
    body: JSON.stringify(formData)
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      swal({
        title: "Success!",
        text: data.message,
        type: "success",
        confirmButtonColor: "#28a745"
      }, function() {
        $('#processPaymentModal').modal('hide');
        if (data.receipt_url) {
          window.open(data.receipt_url, '_blank');
        }
        location.reload();
      });
    } else {
      let errorMsg = data.message || 'An error occurred. Please try again.';
      if (data.errors) {
        const errorList = Object.values(data.errors).flat().join('<br>');
        errorMsg = errorList;
      }
      alertDiv.innerHTML = '<div class="alert alert-danger">' + errorMsg + '</div>';
      submitBtn.disabled = false;
      submitBtn.innerHTML = originalText;
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alertDiv.innerHTML = '<div class="alert alert-danger">An error occurred. Please try again.</div>';
    submitBtn.disabled = false;
    submitBtn.innerHTML = originalText;
  });
}
</script>
@endsection


