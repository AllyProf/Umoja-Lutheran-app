@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-star"></i> Guest Feedback Analysis</h1>
    <p>View and analyze guest feedback and reviews</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">Feedback Analysis</a></li>
  </ul>
</div>

<!-- Statistics Cards -->
<div class="row mb-3">
  <div class="col-md-3">
    <div class="widget-small primary coloured-icon">
      <i class="icon fa fa-comments fa-2x"></i>
      <div class="info">
        <h4>Total Feedbacks</h4>
        <p><b>{{ $totalFeedbacks }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="widget-small success coloured-icon">
      <i class="icon fa fa-star fa-2x"></i>
      <div class="info">
        <h4>Average Rating</h4>
        <p><b>{{ $averageRating }}/5.0</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="widget-small info coloured-icon">
      <i class="icon fa fa-calendar fa-2x"></i>
      <div class="info">
        <h4>Recent (30 Days)</h4>
        <p><b>{{ $recentFeedbacks->count() }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="widget-small warning coloured-icon">
      <i class="icon fa fa-thumbs-up fa-2x"></i>
      <div class="info">
        <h4>Positive (4-5 Stars)</h4>
        <p><b>{{ $ratingDistribution[5] + $ratingDistribution[4] }}</b></p>
      </div>
    </div>
  </div>
</div>

<!-- Rating Distribution -->
<div class="row mb-3">
  <div class="col-md-6">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-bar-chart"></i> Rating Distribution</h3>
      <div class="tile-body">
        @for($i = 5; $i >= 1; $i--)
        <div class="mb-3">
          <div class="d-flex justify-content-between mb-1">
            <span><strong>{{ $i }} Star{{ $i > 1 ? 's' : '' }}</strong></span>
            <span>{{ $ratingDistribution[$i] }} ({{ $totalFeedbacks > 0 ? round(($ratingDistribution[$i] / $totalFeedbacks) * 100, 1) : 0 }}%)</span>
          </div>
          <div class="progress" style="height: 25px;">
            <div class="progress-bar 
              @if($i >= 4) bg-success
              @elseif($i == 3) bg-warning
              @else bg-danger
              @endif" 
              role="progressbar" 
              style="width: {{ $totalFeedbacks > 0 ? ($ratingDistribution[$i] / $totalFeedbacks) * 100 : 0 }}%"
              aria-valuenow="{{ $ratingDistribution[$i] }}" 
              aria-valuemin="0" 
              aria-valuemax="{{ $totalFeedbacks }}">
              {{ $ratingDistribution[$i] }}
            </div>
          </div>
        </div>
        @endfor
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-pie-chart"></i> Category Averages</h3>
      <div class="tile-body">
        <div class="mb-3">
          <div class="d-flex justify-content-between mb-1">
            <span><strong>Room Quality</strong></span>
            <span><strong>{{ $categoryAverages['room_quality'] }}/5.0</strong></span>
          </div>
          <div class="progress" style="height: 20px;">
            <div class="progress-bar bg-primary" role="progressbar" 
                 style="width: {{ ($categoryAverages['room_quality'] / 5) * 100 }}%"></div>
          </div>
        </div>
        <div class="mb-3">
          <div class="d-flex justify-content-between mb-1">
            <span><strong>Service</strong></span>
            <span><strong>{{ $categoryAverages['service'] }}/5.0</strong></span>
          </div>
          <div class="progress" style="height: 20px;">
            <div class="progress-bar bg-info" role="progressbar" 
                 style="width: {{ ($categoryAverages['service'] / 5) * 100 }}%"></div>
          </div>
        </div>
        <div class="mb-3">
          <div class="d-flex justify-content-between mb-1">
            <span><strong>Cleanliness</strong></span>
            <span><strong>{{ $categoryAverages['cleanliness'] }}/5.0</strong></span>
          </div>
          <div class="progress" style="height: 20px;">
            <div class="progress-bar bg-success" role="progressbar" 
                 style="width: {{ ($categoryAverages['cleanliness'] / 5) * 100 }}%"></div>
          </div>
        </div>
        <div class="mb-3">
          <div class="d-flex justify-content-between mb-1">
            <span><strong>Value for Money</strong></span>
            <span><strong>{{ $categoryAverages['value'] }}/5.0</strong></span>
          </div>
          <div class="progress" style="height: 20px;">
            <div class="progress-bar bg-warning" role="progressbar" 
                 style="width: {{ ($categoryAverages['value'] / 5) * 100 }}%"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Monthly Trend -->
@if(count($monthlyTrend) > 0)
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-line-chart"></i> Monthly Trend (Last 6 Months)</h3>
      <div class="tile-body">
        <div class="table-responsive">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Month</th>
                <th>Number of Feedbacks</th>
                <th>Average Rating</th>
                <th>Trend</th>
              </tr>
            </thead>
            <tbody>
              @foreach($monthlyTrend as $month => $data)
              <tr>
                <td><strong>{{ $month }}</strong></td>
                <td>{{ $data['count'] }}</td>
                <td>
                  <span class="badge badge-{{ $data['average'] >= 4 ? 'success' : ($data['average'] >= 3 ? 'warning' : 'danger') }}">
                    {{ $data['average'] }}/5.0
                  </span>
                </td>
                <td>
                  @if($data['count'] > 0)
                    @for($i = 1; $i <= 5; $i++)
                      <i class="fa fa-star{{ $i <= $data['average'] ? '' : '-o' }} 
                        {{ $i <= $data['average'] ? 'text-warning' : 'text-muted' }}"></i>
                    @endfor
                  @else
                    <span class="text-muted">No feedback</span>
                  @endif
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endif

<!-- Filters -->
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-filter"></i> Filter Feedback</h3>
      <div class="tile-body">
        <div class="row">
          <div class="col-md-3">
            <div class="form-group">
              <label for="filter_rating">Filter by Rating</label>
              <select class="form-control" id="filter_rating" onchange="filterFeedbacks()">
                <option value="">All Ratings</option>
                <option value="5">5 Stars</option>
                <option value="4">4 Stars</option>
                <option value="3">3 Stars</option>
                <option value="2">2 Stars</option>
                <option value="1">1 Star</option>
              </select>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label for="filter_date_from">From Date</label>
              <input type="date" class="form-control" id="filter_date_from" onchange="filterFeedbacks()">
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label for="filter_date_to">To Date</label>
              <input type="date" class="form-control" id="filter_date_to" onchange="filterFeedbacks()">
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label for="search_feedback">Search</label>
              <input type="text" class="form-control" id="search_feedback" placeholder="Guest name, email, comment..." onkeyup="filterFeedbacks()">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <button class="btn btn-secondary" onclick="resetFilters()">
              <i class="fa fa-refresh"></i> Reset Filters
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Feedback List -->
<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-title-w-btn">
        <h3 class="title">All Feedback</h3>
      </div>
      <div class="tile-body">
        @if($feedbacks->count() > 0)
        <div class="table-responsive">
          <table class="table table-hover table-bordered" id="feedbackTable">
            <thead>
              <tr>
                <th>Date</th>
                <th>Guest</th>
                <th>Booking Reference</th>
                <th>Room</th>
                <th>Overall Rating</th>
                <th>Category Ratings</th>
                <th>Comment</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($feedbacks as $feedback)
              <tr data-rating="{{ $feedback->rating }}" 
                  data-date="{{ $feedback->created_at->format('Y-m-d') }}"
                  data-guest-name="{{ strtolower($feedback->guest_name) }}"
                  data-guest-email="{{ strtolower($feedback->guest_email) }}"
                  data-comment="{{ strtolower($feedback->comment ?? '') }}">
                <td>{{ $feedback->created_at->format('M d, Y') }}<br><small class="text-muted">{{ $feedback->created_at->format('h:i A') }}</small></td>
                <td>
                  <strong>{{ $feedback->guest_name }}</strong><br>
                  <small class="text-muted">{{ $feedback->guest_email }}</small>
                </td>
                <td><strong>{{ $feedback->booking->booking_reference ?? 'N/A' }}</strong></td>
                <td>
                  {{ $feedback->booking->room->room_number ?? 'N/A' }}<br>
                  <small class="text-muted">{{ $feedback->booking->room->room_type ?? 'N/A' }}</small>
                </td>
                <td>
                  <div class="text-center">
                    @for($i = 1; $i <= 5; $i++)
                      <i class="fa fa-star{{ $i <= $feedback->rating ? '' : '-o' }} 
                        {{ $i <= $feedback->rating ? 'text-warning' : 'text-muted' }}"></i>
                    @endfor
                    <br>
                    <span class="badge badge-{{ $feedback->rating >= 4 ? 'success' : ($feedback->rating >= 3 ? 'warning' : 'danger') }}">
                      {{ $feedback->rating }}/5
                    </span>
                  </div>
                </td>
                <td>
                  @if($feedback->categories)
                    @foreach(['room_quality' => 'Room', 'service' => 'Service', 'cleanliness' => 'Clean', 'value' => 'Value'] as $key => $label)
                      @if(isset($feedback->categories[$key]))
                        <div class="mb-1">
                          <small><strong>{{ $label }}:</strong> 
                            @for($i = 1; $i <= 5; $i++)
                              <i class="fa fa-star{{ $i <= $feedback->categories[$key] ? '' : '-o' }} 
                                {{ $i <= $feedback->categories[$key] ? 'text-warning' : 'text-muted' }}" style="font-size: 10px;"></i>
                            @endfor
                            ({{ $feedback->categories[$key] }})
                          </small>
                        </div>
                      @endif
                    @endforeach
                  @else
                    <span class="text-muted">N/A</span>
                  @endif
                </td>
                <td>
                  @if($feedback->comment)
                    <div style="max-width: 300px;">
                      <p class="mb-0" style="font-size: 13px;">{{ Str::limit($feedback->comment, 100) }}</p>
                    </div>
                  @else
                    <span class="text-muted">No comment</span>
                  @endif
                </td>
                <td>
                  <button class="btn btn-sm btn-primary" onclick="viewFeedbackDetails({{ $feedback->id }}, {{ $loop->index }})" title="View Details">
                    <i class="fa fa-eye"></i> View
                  </button>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <div id="noResults" class="alert alert-info text-center" style="display: none;">
          <i class="fa fa-info-circle"></i> No feedback found matching your filters.
        </div>
        @else
        <div class="text-center" style="padding: 50px;">
          <i class="fa fa-star-o fa-5x text-muted mb-3"></i>
          <h3>No Feedback Yet</h3>
          <p class="text-muted">Guest feedback will appear here once guests submit their reviews.</p>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- Feedback Details Modal -->
<div class="modal fade" id="feedbackDetailsModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-star"></i> Feedback Details</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="feedbackDetailsContent">
        <div class="text-center">
          <i class="fa fa-spinner fa-spin fa-2x"></i>
          <p>Loading feedback details...</p>
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
<script>
function filterFeedbacks() {
  const ratingFilter = document.getElementById('filter_rating').value;
  const dateFrom = document.getElementById('filter_date_from').value;
  const dateTo = document.getElementById('filter_date_to').value;
  const searchTerm = document.getElementById('search_feedback').value.toLowerCase();
  
  const rows = document.querySelectorAll('#feedbackTable tbody tr');
  let visibleCount = 0;
  
  rows.forEach(row => {
    const rowRating = row.getAttribute('data-rating');
    const rowDate = row.getAttribute('data-date');
    const guestName = row.getAttribute('data-guest-name');
    const guestEmail = row.getAttribute('data-guest-email');
    const comment = row.getAttribute('data-comment');
    
    let show = true;
    
    // Rating filter
    if (ratingFilter && rowRating !== ratingFilter) {
      show = false;
    }
    
    // Date range filter
    if (dateFrom && rowDate < dateFrom) {
      show = false;
    }
    if (dateTo && rowDate > dateTo) {
      show = false;
    }
    
    // Search filter
    if (searchTerm) {
      if (!guestName.includes(searchTerm) && 
          !guestEmail.includes(searchTerm) && 
          !comment.includes(searchTerm)) {
        show = false;
      }
    }
    
    if (show) {
      row.style.display = '';
      visibleCount++;
    } else {
      row.style.display = 'none';
    }
  });
  
  // Show/hide no results message
  const noResults = document.getElementById('noResults');
  if (noResults) {
    if (visibleCount === 0) {
      noResults.style.display = 'block';
    } else {
      noResults.style.display = 'none';
    }
  }
}

function resetFilters() {
  document.getElementById('filter_rating').value = '';
  document.getElementById('filter_date_from').value = '';
  document.getElementById('filter_date_to').value = '';
  document.getElementById('search_feedback').value = '';
  filterFeedbacks();
}

function viewFeedbackDetails(feedbackId, rowIndex) {
  $('#feedbackDetailsModal').modal('show');
  document.getElementById('feedbackDetailsContent').innerHTML = `
    <div class="text-center">
      <i class="fa fa-spinner fa-spin fa-2x"></i>
      <p>Loading feedback details...</p>
    </div>
  `;
  
  // Get feedback data from the table row
  const rows = document.querySelectorAll('#feedbackTable tbody tr');
  const row = rows[rowIndex];
  
  if (row) {
    const guestName = row.querySelector('td:nth-child(2) strong').textContent;
    const guestEmail = row.querySelector('td:nth-child(2) small').textContent;
    const bookingRef = row.querySelector('td:nth-child(3) strong').textContent;
    const roomInfo = row.querySelector('td:nth-child(4)').textContent.trim();
    const rating = row.getAttribute('data-rating');
    const comment = row.getAttribute('data-comment');
    const date = row.querySelector('td:nth-child(1)').textContent;
    
    // Get category ratings
    const categoryCell = row.querySelector('td:nth-child(6)');
    let categoriesHtml = '';
    if (categoryCell) {
      categoriesHtml = categoryCell.innerHTML;
    }
    
    const detailsHtml = `
      <div class="feedback-details-view">
        <div class="row mb-3">
          <div class="col-md-6">
            <h5 style="color: #e07632; border-bottom: 2px solid #e07632; padding-bottom: 5px;"><i class="fa fa-user"></i> Guest Information</h5>
            <table class="table table-sm table-bordered">
              <tr><td width="40%"><strong>Name:</strong></td><td>${guestName}</td></tr>
              <tr><td><strong>Email:</strong></td><td>${guestEmail}</td></tr>
              <tr><td><strong>Booking Reference:</strong></td><td><strong>${bookingRef}</strong></td></tr>
              <tr><td><strong>Room:</strong></td><td>${roomInfo}</td></tr>
            </table>
          </div>
          <div class="col-md-6">
            <h5 style="color: #e07632; border-bottom: 2px solid #e07632; padding-bottom: 5px;"><i class="fa fa-star"></i> Rating Information</h5>
            <div class="text-center mb-3">
              <h3>Overall Rating</h3>
              <div>
                ${Array.from({length: 5}, (_, i) => 
                  `<i class="fa fa-star${i < parseInt(rating) ? '' : '-o'} ${i < parseInt(rating) ? 'text-warning' : 'text-muted'}" style="font-size: 30px;"></i>`
                ).join('')}
              </div>
              <h4><span class="badge badge-${parseInt(rating) >= 4 ? 'success' : (parseInt(rating) >= 3 ? 'warning' : 'danger')}">${rating}/5</span></h4>
            </div>
            <div>
              <h6><strong>Category Ratings:</strong></h6>
              ${categoriesHtml || '<p class="text-muted">No category ratings provided</p>'}
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <h5 style="color: #e07632; border-bottom: 2px solid #e07632; padding-bottom: 5px;"><i class="fa fa-comment"></i> Guest Comment</h5>
            <div class="p-3 bg-light rounded">
              ${comment && comment !== 'null' ? `<p style="font-size: 14px; line-height: 1.6;">${comment.replace(/&lt;/g, '<').replace(/&gt;/g, '>')}</p>` : '<p class="text-muted">No comment provided</p>'}
            </div>
          </div>
        </div>
        <div class="row mt-3">
          <div class="col-md-12">
            <small class="text-muted"><i class="fa fa-calendar"></i> Submitted on: ${date}</small>
          </div>
        </div>
      </div>
    `;
    
    document.getElementById('feedbackDetailsContent').innerHTML = detailsHtml;
  } else {
    document.getElementById('feedbackDetailsContent').innerHTML = `
      <div class="alert alert-danger">
        <i class="fa fa-exclamation-triangle"></i> Could not load feedback details.
      </div>
    `;
  }
}
</script>
@endsection

