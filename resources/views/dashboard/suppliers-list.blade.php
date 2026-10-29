@extends('dashboard.layouts.app')

@section('content')
@php
    $role = strtolower($role ?? auth()->guard('staff')->user()->role ?? 'manager');
    $dashboardRoute = match (true) {
        $role === 'storekeeper' => 'storekeeper.dashboard',
        in_array($role, ['super_admin', 'admin']) => 'admin.dashboard',
        default => 'admin.dashboard',
    };
@endphp

<div class="app-title">
    <div>
        <h1><i class="fa fa-address-book"></i> Suppliers</h1>
        <p>People and companies you buy stock from</p>
    </div>
    <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
        <li class="breadcrumb-item"><a href="{{ route($dashboardRoute) }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Suppliers</li>
    </ul>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fa fa-check-circle mr-1"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fa fa-exclamation-circle mr-1"></i> {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
@endif

<div class="tile shadow-sm">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3" style="gap:12px;">
        <div class="supplier-search flex-grow-1" style="max-width:360px;">
            <i class="fa fa-search"></i>
            <input type="text" id="supplierSearch" class="form-control" placeholder="Search by name, phone, location…">
        </div>
        <a class="btn btn-primary" href="{{ route('admin.suppliers.create') }}">
            <i class="fa fa-plus"></i> Add Supplier
        </a>
    </div>

    @if($suppliers->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="suppliersTable">
                <thead class="thead-light">
                    <tr>
                        <th>Supplier</th>
                        <th>Contact</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($suppliers as $supplier)
                        <tr class="supplier-row"
                            data-search="{{ strtolower($supplier->name . ' ' . ($supplier->phone ?? '') . ' ' . ($supplier->email ?? '') . ' ' . ($supplier->location ?? '')) }}">
                            <td>
                                <strong class="text-dark">{{ $supplier->name }}</strong>
                                @if($supplier->notes)
                                    <small class="d-block text-muted text-truncate" style="max-width:280px;">{{ $supplier->notes }}</small>
                                @endif
                            </td>
                            <td>
                                @if($supplier->phone)
                                    <div><i class="fa fa-phone text-muted mr-1"></i>{{ $supplier->phone }}</div>
                                @endif
                                @if($supplier->email)
                                    <div><i class="fa fa-envelope text-muted mr-1"></i>{{ $supplier->email }}</div>
                                @endif
                                @if(!$supplier->phone && !$supplier->email)
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>{{ $supplier->location ?: '—' }}</td>
                            <td>
                                @if($supplier->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-secondary">Inactive</span>
                                @endif
                            </td>
                            <td class="text-right text-nowrap">
                                <a href="{{ route('admin.suppliers.edit', $supplier) }}"
                                    class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="fa fa-edit"></i> Edit
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger"
                                    onclick="deleteSupplier({{ $supplier->id }})" title="Delete">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-3">
            @include('dashboard.partials.bootstrap-pagination', ['paginator' => $suppliers])
        </div>
    @else
        <div class="text-center py-5">
            <i class="fa fa-address-book fa-4x text-muted mb-3" style="opacity:.35;"></i>
            <h4>No suppliers yet</h4>
            <p class="text-muted">Add suppliers you buy from so purchase lists are easier to manage.</p>
            <a href="{{ route('admin.suppliers.create') }}" class="btn btn-primary mt-2">
                <i class="fa fa-plus"></i> Add Supplier
            </a>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script src="{{ asset('dashboard_assets/js/plugins/sweetalert.min.js') }}"></script>
<style>
    .supplier-search { position: relative; }
    .supplier-search i {
        position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #858796;
    }
    .supplier-search input { padding-left: 36px; }
    #suppliersTable td { vertical-align: middle; }
</style>
<script>
document.getElementById('supplierSearch')?.addEventListener('input', function () {
    const term = this.value.toLowerCase().trim();
    document.querySelectorAll('.supplier-row').forEach(row => {
        row.style.display = (!term || row.dataset.search.includes(term)) ? '' : 'none';
    });
});

function deleteSupplier(supplierId) {
    swal({
        title: "Delete Supplier?",
        text: "This cannot be undone.",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Yes, delete",
        cancelButtonText: "Cancel"
    }, function (isConfirm) {
        if (!isConfirm) return;

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
                    text: data.message || "Supplier deleted.",
                    type: "success",
                    confirmButtonColor: "#28a745"
                }, function () { location.reload(); });
            } else {
                swal({
                    title: "Cannot delete",
                    text: data.message || "Failed to delete supplier.",
                    type: "error",
                    confirmButtonColor: "#d33"
                });
            }
        })
        .catch(() => {
            swal({
                title: "Error!",
                text: "Something went wrong. Please try again.",
                type: "error",
                confirmButtonColor: "#d33"
            });
        });
    });
}
</script>
@endsection
