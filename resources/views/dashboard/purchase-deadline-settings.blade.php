@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-clock-o"></i> Purchase Deadline Settings</h1>
    <p>Configure when purchase requests should be submitted</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.purchase-requests.index') }}">Purchase Requests</a></li>
    <li class="breadcrumb-item"><a href="#">Deadline Settings</a></li>
  </ul>
</div>

<div class="row mb-3">
  <div class="col-md-8 offset-md-2">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-calendar"></i> Configure Purchase Deadline</h3>
      <div class="tile-body">
        @if($deadline)
        <div class="alert alert-info mb-4">
          <h5><i class="fa fa-info-circle"></i> Current Settings</h5>
          <p class="mb-1"><strong>Day of Week:</strong> {{ ucfirst($deadline->day_of_week) }}</p>
          <p class="mb-1"><strong>Deadline Time:</strong> {{ \Carbon\Carbon::parse($deadline->deadline_time)->format('H:i') }}</p>
          @if($nextDeadline)
          <p class="mb-0"><strong>Next Deadline:</strong> {{ $nextDeadline->format('F d, Y H:i') }}</p>
          @endif
        </div>
        @endif
        
        <form id="deadlineForm">
          <div class="form-group">
            <label for="day_of_week"><strong>Day of Week <span class="text-danger">*</span></strong></label>
            <select class="form-control" id="day_of_week" name="day_of_week" required>
              <option value="monday" {{ $deadline && $deadline->day_of_week == 'monday' ? 'selected' : '' }}>Monday</option>
              <option value="tuesday" {{ $deadline && $deadline->day_of_week == 'tuesday' ? 'selected' : '' }}>Tuesday</option>
              <option value="wednesday" {{ $deadline && $deadline->day_of_week == 'wednesday' ? 'selected' : '' }}>Wednesday</option>
              <option value="thursday" {{ $deadline && $deadline->day_of_week == 'thursday' ? 'selected' : '' }}>Thursday</option>
              <option value="friday" {{ !$deadline || $deadline->day_of_week == 'friday' ? 'selected' : '' }}>Friday</option>
              <option value="saturday" {{ $deadline && $deadline->day_of_week == 'saturday' ? 'selected' : '' }}>Saturday</option>
              <option value="sunday" {{ $deadline && $deadline->day_of_week == 'sunday' ? 'selected' : '' }}>Sunday</option>
            </select>
            <small class="form-text text-muted">Select which day of the week purchase requests should be submitted by.</small>
          </div>
          
          <div class="form-group">
            <label for="deadline_time"><strong>Deadline Time <span class="text-danger">*</span></strong></label>
            <input type="time" class="form-control" id="deadline_time" name="deadline_time" 
                   value="{{ $deadline ? \Carbon\Carbon::parse($deadline->deadline_time)->format('H:i') : '17:00' }}" required>
            <small class="form-text text-muted">Select the time by which purchase requests must be submitted (24-hour format).</small>
          </div>
          
          <div class="form-group">
            <label for="notes"><strong>Notes</strong></label>
            <textarea class="form-control" id="notes" name="notes" rows="3" 
                      placeholder="Optional notes about this deadline (e.g., 'Changed from Friday to Monday due to market schedule')">{{ $deadline ? $deadline->notes : '' }}</textarea>
            <small class="form-text text-muted">Optional notes about this deadline configuration.</small>
          </div>
          
          <div class="form-group">
            <button type="submit" class="btn btn-primary">
              <i class="fa fa-save"></i> Save Deadline Settings
            </button>
            <a href="{{ route('admin.purchase-requests.index') }}" class="btn btn-secondary">
              <i class="fa fa-arrow-left"></i> Back to Purchase Requests
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('dashboard_assets/js/plugins/sweetalert.min.js') }}"></script>
<script>
$(document).ready(function() {
    $('#deadlineForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = {
            _token: '{{ csrf_token() }}',
            day_of_week: $('#day_of_week').val(),
            deadline_time: $('#deadline_time').val(),
            notes: $('#notes').val()
        };
        
        $.ajax({
            url: '{{ route('admin.purchase-requests.update-deadline') }}',
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    swal({
                        title: "Success!",
                        text: response.message + "\nNext deadline: " + response.next_deadline,
                        type: "success",
                        timer: 3000,
                        showConfirmButton: true
                    }).then(function() {
                        window.location.href = '{{ route('admin.purchase-requests.index') }}';
                    });
                }
            },
            error: function(xhr) {
                var errorMsg = xhr.responseJSON?.message || 'Failed to update deadline settings.';
                swal("Error!", errorMsg, "error");
            }
        });
    });
});
</script>
@endsection
