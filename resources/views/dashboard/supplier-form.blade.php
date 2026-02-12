@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-{{ isset($supplier) ? 'edit' : 'plus' }}"></i> {{ isset($supplier) ? 'Edit' : 'Add' }} Supplier</h1>
    <p>{{ isset($supplier) ? 'Update supplier information' : 'Add new supplier' }}</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.suppliers.index') }}">Suppliers</a></li>
    <li class="breadcrumb-item"><a href="#">{{ isset($supplier) ? 'Edit' : 'Add' }}</a></li>
  </ul>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <form id="supplierForm">
        @csrf
        @if(isset($supplier))
          @method('PUT')
        @endif
        
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="name">Supplier Name <span class="text-danger">*</span></label>
              <input class="form-control" type="text" id="name" name="name" 
                     value="{{ $supplier->name ?? '' }}" 
                     placeholder="Enter supplier name" required>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="phone">Phone Number</label>
              <input class="form-control" type="text" id="phone" name="phone" 
                     value="{{ $supplier->phone ?? '' }}" 
                     placeholder="e.g., +255712345678">
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="email">Email</label>
              <input class="form-control" type="email" id="email" name="email" 
                     value="{{ $supplier->email ?? '' }}" 
                     placeholder="supplier@example.com">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="location">Location</label>
              <input class="form-control" type="text" id="location" name="location" 
                     value="{{ $supplier->location ?? '' }}" 
                     placeholder="Enter supplier location">
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="is_active">Status <span class="text-danger">*</span></label>
              <select class="form-control" id="is_active" name="is_active" required>
                <option value="1" {{ (!isset($supplier) || $supplier->is_active) ? 'selected' : '' }}>Active</option>
                <option value="0" {{ (isset($supplier) && !$supplier->is_active) ? 'selected' : '' }}>Inactive</option>
              </select>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <label for="notes">Notes (Optional)</label>
              <textarea class="form-control" id="notes" name="notes" rows="3" 
                        placeholder="Any additional notes about this supplier...">{{ $supplier->notes ?? '' }}</textarea>
            </div>
          </div>
        </div>

        <div id="formAlert"></div>

        <div class="form-group mt-4">
          <button type="submit" class="btn btn-primary">
            <i class="fa fa-save"></i> {{ isset($supplier) ? 'Update' : 'Create' }} Supplier
          </button>
          <a href="{{ route('admin.suppliers.index') }}" class="btn btn-secondary">
            <i class="fa fa-times"></i> Cancel
          </a>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script src="{{ asset('dashboard_assets/js/plugins/sweetalert.min.js') }}"></script>
<script>
$(document).ready(function() {
  const form = document.getElementById('supplierForm');
  
  form.addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (!form.checkValidity()) {
      form.reportValidity();
      return;
    }
    
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Saving...';

    const alertDiv = document.getElementById('formAlert');
    alertDiv.innerHTML = '';

    @if(isset($supplier))
      const url = '{{ route("admin.suppliers.update", $supplier) }}';
      const method = 'POST';
      formData.append('_method', 'PUT');
    @else
      const url = '{{ route("admin.suppliers.store") }}';
      const method = 'POST';
    @endif

    fetch(url, {
      method: method,
      body: formData,
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
      }
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
          window.location.href = '{{ route("admin.suppliers.index") }}';
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
  });
});
</script>
@endsection


