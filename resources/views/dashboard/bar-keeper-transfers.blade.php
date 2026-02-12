
@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-exchange-alt"></i> Stock Transfers</h1>
    <p>View and manage incoming stock transfers</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('bar-keeper.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">Stock Transfers</a></li>
  </ul>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-title-w-btn mb-3">
        <h3 class="title">My Transfers</h3>
      </div>
      
      <!-- Filters -->
      <div class="row mb-3">
        <div class="col-md-12">
          <div class="btn-group" role="group">
            <a href="{{ route('bar-keeper.transfers.index') }}" class="btn {{ !request('status') ? 'btn-primary' : 'btn-outline-primary' }}">All</a>
            <a href="{{ route('bar-keeper.transfers.index', ['status' => 'pending']) }}" class="btn {{ request('status') == 'pending' ? 'btn-primary' : 'btn-outline-primary' }}">Pending</a>
            <a href="{{ route('bar-keeper.transfers.index', ['status' => 'completed']) }}" class="btn {{ request('status') == 'completed' ? 'btn-primary' : 'btn-outline-primary' }}">Completed</a>
          </div>
        </div>
      </div>
      
      @if($transfers->count() > 0)
      <div class="table-responsive">
        <table class="table table-hover table-bordered">
          <thead>
            <tr>
              <th>Ref</th>
              <th>Date</th>
              <th>Product</th>
              <th>Variant</th>
              <th>Quantity</th>
              <th>Transferred By</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($transfers as $transfer)
            <tr>
              <td><strong>{{ $transfer->transfer_reference }}</strong></td>
              <td>{{ $transfer->transfer_date->format('M d, Y') }}</td>
              <td><strong>{{ $transfer->product->name }}</strong></td>
              <td>{{ $transfer->productVariant->measurement }} ({{ $transfer->productVariant->packaging }})</td>
              <td>
                {{ number_format($transfer->quantity_transferred) }} 
                <span class="text-muted">{{ $transfer->quantity_unit === 'packages' ? ($transfer->productVariant->packaging_name ?? 'packages') : 'PIC' }}</span>
              </td>
              <td>{{ $transfer->transferredBy->name ?? 'N/A' }}</td>
              <td>
                @if($transfer->status === 'pending')
                    <span class="badge badge-warning">Pending</span>
                @elseif($transfer->status === 'completed')
                    <span class="badge badge-success">Completed</span>
                @else
                    <span class="badge badge-danger">Cancelled</span>
                @endif
              </td>
              <td>
                @if($transfer->status === 'pending')
                <button class="btn btn-sm btn-success" onclick="receiveTransfer({{ $transfer->id }})" title="Mark as Received">
                  <i class="fa fa-check"></i> Receive
                </button>
                @else
                <button class="btn btn-sm btn-secondary" disabled>
                    <i class="fa fa-lock"></i>
                </button>
                @endif
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      
      <div class="d-flex justify-content-center mt-4">
        {{ $transfers->withQueryString()->links() }}
      </div>
      @else
      <div class="text-center" style="padding: 50px;">
        <i class="fa fa-exchange-alt fa-5x text-muted mb-4" style="opacity: 0.5;"></i>
        <h3>No Transfers Found</h3>
        <p class="text-muted">You don't have any stock transfers with this status.</p>
        <a href="{{ route('bar-keeper.transfers.index') }}" class="btn btn-outline-secondary btn-sm mt-3">View All Transfers</a>
      </div>
      @endif
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('dashboard_assets/js/plugins/sweetalert.min.js') }}"></script>
<script>
function receiveTransfer(transferId) {
  swal({
    title: "Receive Transfer?",
    text: "Mark this transfer as received?",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor: "#28a745",
    cancelButtonColor: "#6c757d",
    confirmButtonText: "Yes, received!",
    cancelButtonText: "Cancel"
  }, function(isConfirm) {
    if (isConfirm) {
      callApi(`{{ route("bar-keeper.transfers.receive", ":id") }}`.replace(':id', transferId), 'PUT', { status: 'completed' });
    }
  });
}

function callApi(url, method, data) {
    fetch(url, {
        method: method,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            swal({
                title: "Success!",
                text: data.message || "Action completed successfully!",
                type: "success",
                timer: 2000,
                showConfirmButton: false
            });
            setTimeout(() => location.reload(), 1500);
        } else {
            swal("Error!", data.message || "Action failed.", "error");
        }
    })
    .catch(error => {
        console.error('Error:', error);
        swal("Error!", "An error occurred. Please try again.", "error");
    });
}
</script>
@endsection
