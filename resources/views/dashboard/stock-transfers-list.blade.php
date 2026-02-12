@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-exchange-alt"></i> Stock Transfers</h1>
    <p>Manage product transfers to bar keepers</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">Stock Transfers</a></li>
  </ul>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-title-w-btn mb-3">
        <h3 class="title">All Stock Transfers</h3>
        <div class="btn-group">
          <a class="btn btn-primary" href="{{ route('admin.stock-transfers.create', ['type' => $type ?? 'drink']) }}">
            <i class="fa fa-plus"></i> New {{ isset($type) ? ucfirst($type) : '' }} Transfer
          </a>
        </div>
      </div>
      
      <!-- Filters -->
      <div class="row mb-3">
        <div class="col-md-3">
          <select class="form-control" id="statusFilter" onchange="filterTransfers()">
            <option value="">All Status</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
          </select>
        </div>
        <div class="col-md-3">
          <input type="text" class="form-control" id="searchInput" placeholder="Search transfers..." 
                 value="{{ request('search') }}" 
                 oninput="filterTransfers()">
        </div>
        <div class="col-md-6 text-right">
          <button class="btn btn-secondary btn-sm" onclick="resetFilters()">
            <i class="fa fa-refresh"></i> Reset Filters
          </button>
        </div>
      </div>
      
      @if($transfers->count() > 0)
      <div class="table-responsive">
        <table class="table table-hover table-bordered">
          <thead>
            <tr>
              <th>Transfer Ref</th>
              <th>Date</th>
              <th>Product</th>
              <th>Variant</th>
              <th>Quantity</th>
              <th>Transferred By</th>
              <th>Received By</th>
              <th>Status</th>
              <th>Selling Price</th>
              <th>Expected Collection</th>
              <th>Expected Profit</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="transfersContainer">
            @foreach($transfers as $transfer)
            <tr class="transfer-row" 
                data-transfer-ref="{{ strtolower($transfer->transfer_reference) }}"
                data-product-name="{{ strtolower($transfer->product->name) }}"
                data-transferred-by="{{ strtolower($transfer->transferredBy->name ?? '') }}"
                data-status="{{ strtolower($transfer->status) }}">
              <td><strong>{{ $transfer->transfer_reference }}</strong></td>
              <td>{{ $transfer->transfer_date->format('M d, Y') }}</td>
              <td><strong>{{ $transfer->product->name }}</strong></td>
              <td>
                {{ $transfer->productVariant->measurement }} 
                <br><small class="text-muted">{{ $transfer->productVariant->packaging_name ?? $transfer->productVariant->packaging }} - {{ $transfer->productVariant->items_per_package }} Bottles/{{ \Illuminate\Support\Str::singular($transfer->productVariant->packaging_name ?? $transfer->productVariant->packaging) }}</small>
              </td>
              <td>
                {{ number_format($transfer->quantity_transferred) }} 
                <span class="text-muted">{{ $transfer->quantity_unit === 'packages' ? ($transfer->productVariant->packaging_name ?? 'packages') : 'bottles' }}</span>
                @if($transfer->quantity_unit === 'packages' && $transfer->total_bottles > 0)
                  <br><small class="text-info">({{ number_format($transfer->total_bottles) }} Bottles)</small>
                @endif
              </td>
              <td>{{ $transfer->transferredBy->name ?? 'N/A' }}</td>
              <td>{{ $transfer->receivedBy->name ?? 'Pending' }}</td>
              <td>
                @if($transfer->status === 'pending')
                  <span class="badge badge-warning">Pending</span>
                @elseif($transfer->status === 'completed')
                  <span class="badge badge-success">Completed</span>
                @else
                  <span class="badge badge-danger">Cancelled</span>
                @endif
              </td>
              <td><strong>{{ number_format($transfer->selling_price) }} TSh</strong></td>
              <td class="text-info"><strong>{{ number_format($transfer->expected_revenue) }} TSh</strong></td>
              <td class="text-success"><strong>{{ number_format($transfer->expected_profit, 2) }} TSh</strong></td>
              <td>
                <div class="btn-group">
                  <button class="btn btn-sm btn-info" onclick="viewTransfer({{ $transfer->id }})" title="View Details">
                    <i class="fa fa-eye"></i>
                  </button>
                  <a href="{{ route('admin.stock-transfers.download', $transfer->id) }}" target="_blank" class="btn btn-sm btn-secondary" title="Download Transfer Note">
                    <i class="fa fa-download"></i>
                  </a>
                  @if($transfer->status === 'pending')

                  <button class="btn btn-sm btn-danger" onclick="cancelTransfer({{ $transfer->id }})" title="Cancel">
                    <i class="fa fa-times"></i>
                  </button>
                  @endif
                </div>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      
      <div class="d-flex justify-content-center mt-4">
        {{ $transfers->links() }}
      </div>
      @else
      <div class="text-center" style="padding: 80px 20px;">
        <i class="fa fa-exchange-alt fa-5x text-muted mb-4" style="opacity: 0.5;"></i>
        <h3 class="mb-3">No Stock Transfers</h3>
        <p class="text-muted mb-4">Start by creating your first stock transfer to a bar keeper.</p>
        <a href="{{ route('admin.stock-transfers.create') }}" class="btn btn-primary btn-lg">
          <i class="fa fa-plus"></i> New Transfer
        </a>
      </div>
      @endif
      
      <!-- No Results Message (hidden by default) -->
      <div id="noResultsMessage" class="text-center" style="display: none; padding: 80px 20px;">
        <i class="fa fa-search fa-5x text-muted mb-4" style="opacity: 0.5;"></i>
        <h3 class="mb-3">No Transfers Found</h3>
        <p class="text-muted mb-4">Try adjusting your search or filter criteria.</p>
        <button class="btn btn-secondary" onclick="resetFilters()">
          <i class="fa fa-refresh"></i> Reset Filters
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Transfer Details Modal -->
<div class="modal fade" id="transferDetailsModal" tabindex="-1" role="dialog" aria-labelledby="transferDetailsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="transferDetailsModalLabel">
          <i class="fa fa-exchange-alt"></i> Transfer Details
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="transferDetailsContent">
        <div class="text-center">
          <i class="fa fa-spinner fa-spin fa-3x"></i>
          <p>Loading transfer details...</p>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script src="{{ asset('dashboard_assets/js/plugins/sweetalert.min.js') }}"></script>
<script>
// Real-time search and filter
let searchTimeout;
function filterTransfers() {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(function() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value.toLowerCase();
    const transferRows = document.querySelectorAll('.transfer-row');
    let visibleCount = 0;
    
    transferRows.forEach(row => {
      const transferRef = row.dataset.transferRef || '';
      const productName = row.dataset.productName || '';
      const transferredBy = row.dataset.transferredBy || '';
      const status = row.dataset.status || '';
      
      const matchesSearch = !searchTerm || 
        transferRef.includes(searchTerm) || 
        productName.includes(searchTerm) || 
        transferredBy.includes(searchTerm);
      
      const matchesStatus = !statusFilter || status === statusFilter;
      
      if (matchesSearch && matchesStatus) {
        row.style.display = '';
        visibleCount++;
      } else {
        row.style.display = 'none';
      }
    });
    
    // Show/hide no results message
    const noResultsMessage = document.getElementById('noResultsMessage');
    if (visibleCount === 0 && (searchTerm || statusFilter)) {
      noResultsMessage.style.display = 'block';
    } else {
      noResultsMessage.style.display = 'none';
    }
  }, 300);
}

function resetFilters() {
  document.getElementById('searchInput').value = '';
  document.getElementById('statusFilter').value = '';
  filterTransfers();
}

// View transfer details
function viewTransfer(transferId) {
  const modal = $('#transferDetailsModal');
  const content = $('#transferDetailsContent');
  
  modal.modal('show');
  content.html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-3x"></i><p>Loading transfer details...</p></div>');
  
  fetch(`{{ route('admin.stock-transfers.show', ':id') }}`.replace(':id', transferId), {
    headers: {
      'X-Requested-With': 'XMLHttpRequest',
      'Accept': 'application/json'
    }
  })
  .then(response => response.json())
  .then(data => {
    if (data.success || data.id) {
      const transfer = data.transfer || data;
      const quantityUnitName = transfer.quantity_unit === 'packages' 
        ? (transfer.product_variant?.packaging_name || transfer.product_variant?.packaging || 'packages')
        : 'bottles';
      
      const html = `
        <div class="row">
          <div class="col-md-6">
            <h5 class="mb-3">Transfer Information</h5>
            <table class="table table-sm table-borderless">
              <tr>
                <td width="150"><strong>Transfer Reference:</strong></td>
                <td><span class="badge badge-secondary">${transfer.transfer_reference}</span></td>
              </tr>
              <tr>
                <td><strong>Transfer Date:</strong></td>
                <td>${new Date(transfer.transfer_date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })}</td>
              </tr>
              <tr>
                <td><strong>Product:</strong></td>
                <td><strong>${transfer.product?.name || 'N/A'}</strong></td>
              </tr>
              <tr>
                <td><strong>Variant:</strong></td>
                <td>${transfer.product_variant?.measurement || 'N/A'} (${transfer.product_variant?.packaging || 'N/A'})</td>
              </tr>
              <tr>
                <td><strong>Quantity:</strong></td>
                <td>
                  <strong>${transfer.quantity_transferred}</strong> ${quantityUnitName}
                  ${transfer.quantity_unit === 'packages' ? `<br><small class="text-info">(${new Number(transfer.total_bottles).toLocaleString()} Bottles)</small>` : ''}
                </td>
              </tr>
              <tr>
                <td><strong>Status:</strong></td>
                <td>
                  ${transfer.status === 'pending' ? '<span class="badge badge-warning">Pending</span>' : ''}
                  ${transfer.status === 'completed' ? '<span class="badge badge-success">Completed</span>' : ''}
                  ${transfer.status === 'cancelled' ? '<span class="badge badge-danger">Cancelled</span>' : ''}
                </td>
              </tr>
              <tr>
                <td><strong>Selling Price:</strong></td>
                <td><strong>${new Number(transfer.selling_price).toLocaleString()} TSh</strong> <small class="text-muted">per bottle</small></td>
              </tr>
              <tr>
                <td><strong>Buying Price:</strong></td>
                <td><strong>${new Number(transfer.buying_price).toLocaleString()} TSh</strong> <small class="text-muted">per bottle</small></td>
              </tr>
              <tr class="table-info">
                <td><strong>Expected Collection:</strong></td>
                <td><strong class="text-info">${new Number(transfer.expected_revenue).toLocaleString()} TSh</strong><br><small>(Total Revenue from sales)</small></td>
              </tr>
              <tr class="table-success">
                <td><strong>Expected Profit:</strong></td>
                <td><strong class="text-success">${new Number(transfer.expected_profit).toLocaleString()} TSh</strong><br><small>(Net gain after subtracting buying cost)</small></td>
              </tr>
            </table>
          </div>
          <div class="col-md-6">
            <h5 class="mb-3">People Involved</h5>
            <table class="table table-sm table-borderless">
              <tr>
                <td width="150"><strong>Transferred By:</strong></td>
                <td>${transfer.transferred_by?.name || 'N/A'}</td>
              </tr>
              <tr>
                <td><strong>Received By:</strong></td>
                <td>${transfer.received_by?.name || 'Pending'}</td>
              </tr>
              ${transfer.received_at ? `
              <tr>
                <td><strong>Received At:</strong></td>
                <td>${new Date(transfer.received_at).toLocaleString('en-US')}</td>
              </tr>
              ` : ''}
            </table>
            ${transfer.notes ? `
            <h5 class="mb-3">Notes</h5>
            <p class="text-muted">${transfer.notes}</p>
            ` : ''}
          </div>
        </div>
      `;
      
      content.html(html);
    } else {
      content.html('<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Failed to load transfer details. Please try again.</div>');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    content.html('<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> An error occurred while loading transfer details. Please try again.</div>');
  });
}



// Cancel transfer
function cancelTransfer(transferId) {
  swal({
    title: "Cancel Transfer?",
    text: "Are you sure you want to cancel this transfer?",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#6c757d",
    confirmButtonText: "Yes, cancel it!",
    cancelButtonText: "Cancel"
  }, function(isConfirm) {
    if (isConfirm) {
      fetch(`{{ route('admin.stock-transfers.update-status', ':id') }}`.replace(':id', transferId), {
        method: 'PUT',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ status: 'cancelled' })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          swal({
            title: "Cancelled!",
            text: data.message || "Transfer cancelled successfully!",
            type: "success",
            confirmButtonColor: "#28a745"
          }, function() {
            location.reload();
          });
        } else {
          swal({
            title: "Error!",
            text: data.message || "Failed to cancel transfer.",
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


