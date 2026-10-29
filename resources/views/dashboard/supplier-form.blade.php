@extends('dashboard.layouts.app')

@section('content')
@php
    $isEdit = isset($supplier);
    $role = strtolower($role ?? auth()->guard('staff')->user()->role ?? 'manager');
    $dashboardRoute = match (true) {
        $role === 'storekeeper' => 'storekeeper.dashboard',
        default => 'admin.dashboard',
    };
@endphp

<div class="app-title">
    <div>
        <h1><i class="fa fa-{{ $isEdit ? 'edit' : 'plus' }}"></i> {{ $isEdit ? 'Edit Supplier' : 'Add Supplier' }}</h1>
        <p>{{ $isEdit ? 'Update supplier details' : 'Register a supplier you buy stock from' }}</p>
    </div>
    <ul class="app-breadcrumb breadcrumb">
        <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
        <li class="breadcrumb-item"><a href="{{ route($dashboardRoute) }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.suppliers.index') }}">Suppliers</a></li>
        <li class="breadcrumb-item active">{{ $isEdit ? 'Edit' : 'Add' }}</li>
    </ul>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="tile shadow-sm">
            <form id="supplierForm">
                @csrf
                @if($isEdit)
                    @method('PUT')
                @endif

                <div class="form-group">
                    <label class="font-weight-bold" for="name">Supplier name <span class="text-danger">*</span></label>
                    <input class="form-control form-control-lg" type="text" id="name" name="name"
                        value="{{ old('name', $supplier->name ?? '') }}"
                        placeholder="e.g. City Soft Drinks Ltd" required autofocus>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label class="font-weight-bold" for="phone">Phone</label>
                        <input class="form-control" type="text" id="phone" name="phone"
                            value="{{ old('phone', $supplier->phone ?? '') }}"
                            placeholder="+255 7XX XXX XXX">
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="font-weight-bold" for="email">Email</label>
                        <input class="form-control" type="email" id="email" name="email"
                            value="{{ old('email', $supplier->email ?? '') }}"
                            placeholder="supplier@example.com">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label class="font-weight-bold" for="location">Location</label>
                        <input class="form-control" type="text" id="location" name="location"
                            value="{{ old('location', $supplier->location ?? '') }}"
                            placeholder="e.g. Kariakoo, Dar es Salaam">
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="font-weight-bold" for="is_active">Status</label>
                        <select class="form-control" id="is_active" name="is_active" required>
                            <option value="1" {{ old('is_active', $supplier->is_active ?? true) ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ !old('is_active', $supplier->is_active ?? true) ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="font-weight-bold" for="notes">Notes <span class="text-muted">(optional)</span></label>
                    <textarea class="form-control" id="notes" name="notes" rows="3"
                        placeholder="Payment terms, delivery days, preferred contact…">{{ old('notes', $supplier->notes ?? '') }}</textarea>
                </div>

                <div id="formAlert"></div>

                <div class="d-flex flex-wrap justify-content-between align-items-center mt-4" style="gap:10px;">
                    <a href="{{ route('admin.suppliers.index') }}" class="btn btn-outline-secondary">
                        <i class="fa fa-arrow-left"></i> Back
                    </a>
                    <button type="submit" class="btn btn-success btn-lg px-4">
                        <i class="fa fa-check"></i> {{ $isEdit ? 'Save Changes' : 'Save Supplier' }}
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
document.getElementById('supplierForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const form = this;

    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Saving...';
    document.getElementById('formAlert').innerHTML = '';

    @if($isEdit)
        const url = @json(route('admin.suppliers.update', $supplier));
        formData.append('_method', 'PUT');
    @else
        const url = @json(route('admin.suppliers.store'));
    @endif

    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            swal({
                title: "Saved!",
                text: data.message,
                type: "success",
                confirmButtonColor: "#28a745"
            }, function () {
                window.location.href = @json(route('admin.suppliers.index'));
            });
        } else {
            let errorMsg = data.message || 'Could not save supplier.';
            if (data.errors) {
                errorMsg = Object.values(data.errors).flat().join('<br>');
            }
            document.getElementById('formAlert').innerHTML = '<div class="alert alert-danger">' + errorMsg + '</div>';
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    })
    .catch(() => {
        document.getElementById('formAlert').innerHTML = '<div class="alert alert-danger">Something went wrong. Please try again.</div>';
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
});
</script>
@endsection
