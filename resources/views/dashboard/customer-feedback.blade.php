@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-star"></i> Feedback & Reviews</h1>
    <p>Share your experience with us</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="#">Feedback</a></li>
  </ul>
</div>

<!-- Feedback Form -->
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-comment"></i> Submit Feedback</h3>
      <div class="tile-body">
        <form id="feedbackForm">
          <div class="form-group">
            <label for="booking_id">Select Booking *</label>
            <select class="form-control" id="booking_id" name="booking_id" required>
              <option value="">Select a completed booking...</option>
              @forelse($bookings as $booking)
              <option value="{{ $booking->id }}">
                {{ $booking->booking_reference }} - {{ $booking->room->room_type ?? ($booking->room->room_number ?? 'N/A') }} 
                ({{ \Carbon\Carbon::parse($booking->check_in)->format('M d, Y') }} to {{ \Carbon\Carbon::parse($booking->check_out)->format('M d, Y') }})
                @if($booking->checked_out_at)
                  - Checked Out: {{ \Carbon\Carbon::parse($booking->checked_out_at)->format('M d, Y') }}
                @endif
              </option>
              @empty
              <option value="" disabled>No completed bookings available for feedback</option>
              @endforelse
            </select>
          </div>
          
          <div class="form-group">
            <label>Overall Rating *</label>
            <div class="rating-input">
              @for($i = 5; $i >= 1; $i--)
              <input type="radio" name="rating" id="rating{{ $i }}" value="{{ $i }}">
              <label for="rating{{ $i }}" class="star-label">
                <i class="fa fa-star"></i>
              </label>
              @endfor
            </div>
            <small class="form-text text-muted">Click on a star to rate</small>
          </div>
          
          <div class="form-group">
            <label>Rate by Category</label>
            <div class="row">
              <div class="col-md-6 mb-2">
                <label>Room Quality</label>
                <select class="form-control" name="categories[room_quality]">
                  <option value="">Select rating...</option>
                  <option value="5">Excellent</option>
                  <option value="4">Very Good</option>
                  <option value="3">Good</option>
                  <option value="2">Fair</option>
                  <option value="1">Poor</option>
                </select>
              </div>
              <div class="col-md-6 mb-2">
                <label>Service</label>
                <select class="form-control" name="categories[service]">
                  <option value="">Select rating...</option>
                  <option value="5">Excellent</option>
                  <option value="4">Very Good</option>
                  <option value="3">Good</option>
                  <option value="2">Fair</option>
                  <option value="1">Poor</option>
                </select>
              </div>
              <div class="col-md-6 mb-2">
                <label>Cleanliness</label>
                <select class="form-control" name="categories[cleanliness]">
                  <option value="">Select rating...</option>
                  <option value="5">Excellent</option>
                  <option value="4">Very Good</option>
                  <option value="3">Good</option>
                  <option value="2">Fair</option>
                  <option value="1">Poor</option>
                </select>
              </div>
              <div class="col-md-6 mb-2">
                <label>Value for Money</label>
                <select class="form-control" name="categories[value]">
                  <option value="">Select rating...</option>
                  <option value="5">Excellent</option>
                  <option value="4">Very Good</option>
                  <option value="3">Good</option>
                  <option value="2">Fair</option>
                  <option value="1">Poor</option>
                </select>
              </div>
            </div>
          </div>
          
          <div class="form-group">
            <label for="comment">Your Comments</label>
            <textarea class="form-control" id="comment" name="comment" rows="5" placeholder="Tell us about your experience..."></textarea>
          </div>
          
          <div id="feedbackAlert"></div>
          
          <button type="submit" class="btn btn-primary">
            <i class="fa fa-paper-plane"></i> Submit Feedback
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

@if(count($bookings) === 0 && count($submittedFeedback ?? []) === 0)
<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-body text-center" style="padding: 50px;">
        <i class="fa fa-star-o fa-5x text-muted mb-3"></i>
        <h3>No Completed Bookings</h3>
        <p class="text-muted">You need to complete a stay before you can submit feedback.</p>
      </div>
    </div>
  </div>
</div>
@endif

<!-- Submitted Feedback -->
@if(count($submittedFeedback ?? []) > 0)
<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-check-circle"></i> Your Submitted Feedback</h3>
      <div class="tile-body">
        @foreach($submittedFeedback as $feedback)
        <div class="card mb-3" style="border-left: 4px solid #28a745;">
          <div class="card-body">
            <div class="row">
              <div class="col-md-8">
                <h5 class="card-title">
                  <i class="fa fa-calendar"></i> 
                  Booking: <strong>{{ $feedback->booking->booking_reference ?? 'N/A' }}</strong>
                  @if($feedback->booking && $feedback->booking->room)
                    - {{ $feedback->booking->room->room_type ?? ($feedback->booking->room->room_number ?? 'N/A') }}
                  @endif
                </h5>
                <p class="text-muted mb-2">
                  <small>
                    <i class="fa fa-clock-o"></i> Submitted: {{ \Carbon\Carbon::parse($feedback->created_at)->format('M d, Y h:i A') }}
                  </small>
                </p>
                
                <!-- Overall Rating -->
                <div class="mb-2">
                  <strong>Overall Rating:</strong>
                  <div class="d-inline-block ml-2">
                    @for($i = 1; $i <= 5; $i++)
                      <i class="fa fa-star {{ $i <= $feedback->rating ? 'text-warning' : 'text-muted' }}"></i>
                    @endfor
                    <span class="ml-2">({{ $feedback->rating }}/5)</span>
                  </div>
                </div>
                
                <!-- Category Ratings -->
                @if($feedback->categories && is_array($feedback->categories) && count($feedback->categories) > 0)
                <div class="mb-2">
                  <strong>Category Ratings:</strong>
                  <div class="row mt-2">
                    @foreach($feedback->categories as $category => $rating)
                      @if($rating)
                      <div class="col-md-6 mb-1">
                        <small>
                          <strong>{{ ucfirst(str_replace('_', ' ', $category)) }}:</strong>
                          @for($i = 1; $i <= 5; $i++)
                            <i class="fa fa-star {{ $i <= $rating ? 'text-warning' : 'text-muted' }}" style="font-size: 12px;"></i>
                          @endfor
                          ({{ $rating }}/5)
                        </small>
                      </div>
                      @endif
                    @endforeach
                  </div>
                </div>
                @endif
                
                <!-- Comment -->
                @if($feedback->comment)
                <div class="mt-2">
                  <strong>Your Comments:</strong>
                  <p class="mt-1 mb-0" style="font-style: italic; color: #555;">{{ $feedback->comment }}</p>
                </div>
                @endif
              </div>
              <div class="col-md-4 text-right">
                <span class="badge badge-success" style="font-size: 14px; padding: 8px 12px;">
                  <i class="fa fa-check"></i> Submitted
                </span>
              </div>
            </div>
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </div>
</div>
@endif
@endsection

@section('scripts')
<script src="{{ asset('dashboard_assets/js/plugins/sweetalert.min.js') }}"></script>
<style>
.rating-input {
    display: flex;
    flex-direction: row-reverse;
    justify-content: flex-end;
}
.rating-input input[type="radio"] {
    display: none;
}
.rating-input label {
    cursor: pointer;
    font-size: 30px;
    color: #ddd;
    margin-right: 5px;
    transition: color 0.2s;
}
.rating-input label:hover,
.rating-input label:hover ~ label,
.rating-input input[type="radio"]:checked ~ label {
    color: #ffc107;
}
</style>
<script>
document.getElementById('feedbackForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const alertContainer = document.getElementById('feedbackAlert');
    
    // Check if rating is selected
    if (!formData.get('rating')) {
        alertContainer.innerHTML = '<div class="alert alert-danger">Please select a star rating.</div>';
        return;
    }
    
    alertContainer.innerHTML = '<div class="alert alert-info">Submitting feedback...</div>';
    
    fetch('{{ route("customer.feedback.submit") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            swal({
                title: "Thank You!",
                text: data.message,
                type: "success",
                confirmButtonColor: "#28a745"
            }, function() {
                // Reload the page to show the submitted feedback
                window.location.reload();
            }.bind(this));
        } else {
            alertContainer.innerHTML = '<div class="alert alert-danger">' + (data.message || 'Failed to submit feedback.') + '</div>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alertContainer.innerHTML = '<div class="alert alert-danger">An error occurred. Please try again.</div>';
    });
});
</script>
@endsection





