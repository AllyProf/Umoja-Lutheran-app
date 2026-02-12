@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-truck"></i> Suppliers</h1>
    <p>Manage product suppliers</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">Suppliers</a></li>
  </ul>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-title-w-btn mb-3">
        <h3 class="title">All Suppliers</h3>
        <div class="btn-group">
          <a class="btn btn-primary" href="{{ route('admin.suppliers.create') }}">
            <i class="fa fa-plus"></i> Add New Supplier
          </a>
        </div>
      </div>
      
      @if($suppliers->count() > 0)
      <div class="table-responsive">
        <table class="table table-hover table-bordered">
          <thead>
            <tr>
              <th>Name</th>
              <th>Phone</th>
              <th>Email</th>
              <th>Location</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($suppliers as $supplier)
            <tr>
              <td><strong>{{ $supplier->name }}</strong></td>
              <td>{{ $supplier->phone ?? 'N/A' }}</td>
              <td>{{ $supplier->email ?? 'N/A' }}</td>
              <td>{{ $supplier->location ?? 'N/A' }}</td>
              <td>
                @if($supplier->is_active)
                  <span class="badge badge-success">Active</span>
                @else
                  <span class="badge badge-danger">Inactive</span>
                @endif
              </td>
              <td>
                <div class="btn-group">
                  <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="btn btn-sm btn-info" title="Edit">
                    <i class="fa fa-edit"></i>
                  </a>
                  <button class="btn btn-sm btn-danger" onclick="deleteSupplier({{ $supplier->id }})" title="Delete">
                    <i class="fa fa-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      
      <div class="d-flex justify-content-center">
        {{ $suppliers->links() }}
      </div>
      @else
      <div class="text-center" style="padding: 50px;">
        <i class="fa fa-truck fa-5x text-muted mb-3"></i>
        <h3>No Suppliers Registered</h3>
        <p class="text-muted">Start by adding your first supplier.</p>
        <a href="{{ route('admin.suppliers.create') }}" class="btn btn-primary mt-3">
          <i class="fa fa-plus"></i> Add New Supplier
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
function deleteSupplier(supplierId) {
  swal({
    title: "Delete Supplier?",
    text: "Are you sure you want to delete this supplier? This action cannot be undone.",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#6c757d",
    confirmButtonText: "Yes, delete it!",
    cancelButtonText: "Cancel"
  }, function(isConfirm) {
    if (isConfirm) {
      fetch('{{ route("admin.suppliers.destroy", ":id") }}'.replace(':id', supplierId), {
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
            text: data.message || "Supplier deleted successfully!",
            type: "success",
            confirmButtonColor: "#28a745"
          }, function() {
            location.reload();
          });
        } else {
          swal({
            title: "Error!",
            text: data.message || "Failed to delete supplier.",
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


