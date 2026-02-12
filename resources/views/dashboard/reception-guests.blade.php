@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-user"></i> Guests</h1>
    <p>View all guest information</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('reception.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">Guests</a></li>
  </ul>
</div>

<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-title-w-btn mb-3">
        <h3 class="title">Guest Directory</h3>
      </div>
      
      <!-- Search Filter -->
      <div class="row mb-3">
        <div class="col-md-10 col-12 mb-2 mb-md-0">
          <input type="text" class="form-control" id="searchInput" placeholder="Search by name, email, or phone..." onkeyup="filterGuests()" style="font-size: 16px;">
        </div>
        <div class="col-md-2 col-12">
          <button class="btn btn-secondary btn-block" onclick="resetGuestFilters()">
            <i class="fa fa-refresh"></i> Reset
          </button>
        </div>
      </div>
      
      <div class="tile-body">
        @if($guests->count() > 0)
        <!-- Desktop Table View -->
        <div class="table-responsive">
          <table class="table table-hover table-bordered" id="guestsTable">
            <thead>
              <tr>
                <th>Guest Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Country</th>
                <th>Total Bookings</th>
                <th>Last Booking</th>
              </tr>
            </thead>
            <tbody>
              @foreach($guests as $guest)
              <tr class="guest-row"
                  data-guest-name="{{ strtolower($guest->guest_name) }}"
                  data-guest-email="{{ strtolower($guest->guest_email) }}"
                  data-guest-phone="{{ strtolower($guest->guest_phone ?? '') }}">
                <td><strong>{{ $guest->guest_name }}</strong></td>
                <td>{{ $guest->guest_email }}</td>
                <td>
                  @if($guest->guest_phone)
                    @php
                      $phone = trim($guest->guest_phone);
                      // Check if phone already starts with + or country code to avoid duplication
                      if (!empty($guest->country_code)) {
                        $countryCode = trim($guest->country_code);
                        // If phone doesn't start with + or country code, add country code
                        if (!str_starts_with($phone, '+') && !str_starts_with($phone, $countryCode)) {
                          $phone = $countryCode . $phone;
                        }
                      }
                    @endphp
                    {{ $phone }}
                  @else
                    <span class="text-muted">N/A</span>
                  @endif
                </td>
                <td>{{ $guest->country ?? 'N/A' }}</td>
                <td><span class="badge badge-info">{{ $guest->total_bookings }}</span></td>
                <td>{{ \Carbon\Carbon::parse($guest->last_booking)->format('M d, Y') }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        
        <!-- Mobile Card View -->
        <div class="mobile-guests-cards">
          @foreach($guests as $guest)
          @php
            $phone = $guest->guest_phone ? trim($guest->guest_phone) : '';
            if ($phone && !empty($guest->country_code)) {
              $countryCode = trim($guest->country_code);
              if (!str_starts_with($phone, '+') && !str_starts_with($phone, $countryCode)) {
                $phone = $countryCode . $phone;
              }
            }
          @endphp
          <div class="mobile-guest-card guest-row" style="background: white; border: 1px solid #dee2e6; border-radius: 8px; padding: 15px; margin-bottom: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"
               data-guest-name="{{ strtolower($guest->guest_name) }}"
               data-guest-email="{{ strtolower($guest->guest_email) }}"
               data-guest-phone="{{ strtolower($guest->guest_phone ?? '') }}">
            <div style="border-bottom: 2px solid #940000; padding-bottom: 10px; margin-bottom: 15px;">
              <h5 style="color: #940000; font-size: 18px; font-weight: 600; margin: 0;">{{ $guest->guest_name }}</h5>
            </div>
            
            <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
              <span style="font-weight: 600; color: #495057; font-size: 14px; flex: 0 0 40%;">Email:</span>
              <span style="text-align: right; flex: 1; font-size: 14px; word-break: break-word;">{{ $guest->guest_email }}</span>
            </div>
            
            <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
              <span style="font-weight: 600; color: #495057; font-size: 14px; flex: 0 0 40%;">Phone:</span>
              <span style="text-align: right; flex: 1; font-size: 14px;">
                @if($phone)
                  <a href="tel:{{ $phone }}" style="color: #28a745; text-decoration: none;">
                    {{ $phone }}
                  </a>
                @else
                  <span class="text-muted">N/A</span>
                @endif
              </span>
            </div>
            
            <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
              <span style="font-weight: 600; color: #495057; font-size: 14px; flex: 0 0 40%;">Country:</span>
              <span style="text-align: right; flex: 1; font-size: 14px;">{{ $guest->country ?? 'N/A' }}</span>
            </div>
            
            <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
              <span style="font-weight: 600; color: #495057; font-size: 14px; flex: 0 0 40%;">Total Bookings:</span>
              <span style="text-align: right; flex: 1;">
                <span class="badge badge-info" style="font-size: 14px;">{{ $guest->total_bookings }}</span>
              </span>
            </div>
            
            <div style="display: flex; justify-content: space-between; padding: 10px 0;">
              <span style="font-weight: 600; color: #495057; font-size: 14px; flex: 0 0 40%;">Last Booking:</span>
              <span style="text-align: right; flex: 1; font-size: 14px;">{{ \Carbon\Carbon::parse($guest->last_booking)->format('M d, Y') }}</span>
            </div>
          </div>
          @endforeach
        </div>
        
        <div class="d-flex justify-content-center mt-3">
          {{ $guests->links() }}
        </div>
        @else
        <div class="text-center" style="padding: 50px;">
          <i class="fa fa-user fa-5x text-muted mb-3"></i>
          <h3>No Guests Found</h3>
          <p class="text-muted">No guests match your search criteria.</p>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<style>
/* Mobile Responsive Styles */
@media (max-width: 767px) {
  /* Filters */
  .row .col-md-10,
  .row .col-md-2 {
    margin-bottom: 10px;
  }
  
  /* Table - Hide on Mobile */
  #guestsTable {
    display: none;
  }
  
  /* Mobile Cards - Show on Mobile */
  .mobile-guests-cards {
    display: block;
  }
  
  /* Pagination */
  .pagination {
    justify-content: center;
    flex-wrap: wrap;
  }
  
  .pagination .page-link {
    padding: 8px 12px;
    font-size: 14px;
  }
}

/* Desktop - Hide mobile cards */
@media (min-width: 768px) {
  .mobile-guests-cards {
    display: none;
  }
  
  #guestsTable {
    display: table;
  }
}

/* Very Small Screens */
@media (max-width: 480px) {
  .mobile-guest-card {
    padding: 12px !important;
  }
  
  .mobile-guest-card h5 {
    font-size: 16px !important;
  }
}
</style>
<script>
function filterGuests() {
  const searchInput = document.getElementById('searchInput').value.toLowerCase();
  
  // Filter both table rows and mobile cards
  const rows = document.querySelectorAll('.guest-row');
  rows.forEach(row => {
    const guestName = row.getAttribute('data-guest-name');
    const guestEmail = row.getAttribute('data-guest-email');
    const guestPhone = row.getAttribute('data-guest-phone');
    
    let show = true;
    
    // Search filter
    if (searchInput) {
      if (!guestName.includes(searchInput) && 
          !guestEmail.includes(searchInput) && 
          !guestPhone.includes(searchInput)) {
        show = false;
      }
    }
    
    row.style.display = show ? '' : 'none';
  });
}

function resetGuestFilters() {
  document.getElementById('searchInput').value = '';
  filterGuests();
}
</script>
@endsection




