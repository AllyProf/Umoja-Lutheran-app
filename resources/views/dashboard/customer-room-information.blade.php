@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-bed"></i> Room Information</h1>
    <p>View details of your booked rooms</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="#">Room Information</a></li>
  </ul>
</div>

@if($rooms->count() > 0)
<div class="row">
  @foreach($rooms as $room)
  @php
    $roomBookings = $bookings->where('room_id', $room->id);
    $currentBooking = $roomBookings->where('status', 'confirmed')->where('check_in_status', '!=', 'checked_out')->first();
  @endphp
  <div class="col-md-6 mb-4">
    <div class="tile" style="box-shadow: 0 2px 8px rgba(0,0,0,0.1); border: none; border-radius: 8px; overflow: hidden;">
      <div class="tile-title-w-btn" style="background: linear-gradient(135deg, #940000 0%, #d66a2a 100%); color: white; padding: 15px 20px; margin: 0;">
        <h3 class="tile-title" style="color: white; margin: 0; font-weight: 600;">
          <i class="fa fa-bed"></i> {{ $room->room_type ?? 'N/A' }}
        </h3>
        <div class="btn-group">
          <span class="badge badge-light" style="font-size: 14px; padding: 6px 12px; color: #940000; font-weight: 600;">Room {{ $room->room_number ?? 'N/A' }}</span>
        </div>
      </div>
      <div class="tile-body" style="padding: 20px;">
        <!-- Room Images -->
        @if($room->images && is_array($room->images) && count($room->images) > 0)
        <div class="mb-4">
          <div class="row">
            @foreach(array_slice($room->images, 0, 4) as $image)
            <div class="col-6 mb-2">
              <img src="{{ asset('storage/' . $image) }}" alt="Room Photo" 
                   class="img-fluid" 
                   style="width: 100%; height: 150px; object-fit: cover; border-radius: 8px; cursor: pointer; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"
                   onclick="window.open('{{ asset('storage/' . $image) }}', '_blank')"
                   onmouseover="this.style.transform='scale(1.02)'; this.style.transition='all 0.2s';"
                   onmouseout="this.style.transform='scale(1)';">
            </div>
            @endforeach
          </div>
          @if(count($room->images) > 4)
          <div class="text-center mt-2">
            <a href="{{ route('booking.index') }}?room_id={{ $room->id }}" class="btn btn-sm btn-outline-primary">
              <i class="fa fa-images"></i> View All {{ count($room->images) }} Photos
            </a>
          </div>
          @endif
        </div>
        @endif
        
        <!-- Room Details Section -->
        <div class="mb-4">
          <h5 style="color: #940000; font-weight: 600; margin-bottom: 15px; padding-bottom: 8px; border-bottom: 2px solid #940000;">
            <i class="fa fa-info-circle"></i> Room Details
          </h5>
          <div class="row">
            <div class="col-md-6">
              <table class="table table-sm table-borderless mb-0">
                <tr>
                  <td style="width: 140px; vertical-align: middle;"><strong><i class="fa fa-home" style="color: #940000;"></i> Room Number:</strong></td>
                  <td><span class="badge badge-primary" style="font-size: 13px; padding: 6px 12px;">{{ $room->room_number ?? 'N/A' }}</span></td>
                </tr>
                @if($room->capacity)
                <tr>
                  <td style="vertical-align: middle;"><strong><i class="fa fa-users" style="color: #940000;"></i> Capacity:</strong></td>
                  <td>{{ $room->capacity }} Guest(s)</td>
                </tr>
                @endif
                @if($room->bed_type)
                <tr>
                  <td style="vertical-align: middle;"><strong><i class="fa fa-bed" style="color: #940000;"></i> Bed Type:</strong></td>
                  <td>{{ $room->bed_type }}</td>
                </tr>
                @endif
              </table>
            </div>
            <div class="col-md-6">
              <table class="table table-sm table-borderless mb-0">
                @if($room->floor_location)
                <tr>
                  <td style="width: 140px; vertical-align: middle;"><strong><i class="fa fa-building" style="color: #940000;"></i> Floor:</strong></td>
                  <td>{{ $room->floor_location }}</td>
                </tr>
                @endif
                @if($room->bathroom_type)
                <tr>
                  <td style="vertical-align: middle;"><strong><i class="fa fa-shower" style="color: #940000;"></i> Bathroom:</strong></td>
                  <td>{{ $room->bathroom_type }}</td>
                </tr>
                @endif
                @if($room->price_per_night)
                <tr>
                  <td style="vertical-align: middle;"><strong><i class="fa fa-dollar" style="color: #940000;"></i> Price/Night:</strong></td>
                  <td><strong>${{ number_format($room->price_per_night, 2) }}</strong></td>
                </tr>
                @endif
              </table>
            </div>
          </div>
        </div>
        
        <!-- Description -->
        @if($room->description)
        <div class="mb-4">
          <h5 style="color: #940000; font-weight: 600; margin-bottom: 15px; padding-bottom: 8px; border-bottom: 2px solid #940000;">
            <i class="fa fa-file-text"></i> Description
          </h5>
          <p style="line-height: 1.6; color: #555; text-align: justify; margin: 0;">{{ $room->description }}</p>
        </div>
        @endif
        
        <!-- Amenities Section -->
        @if($room->amenities && is_array($room->amenities) && count($room->amenities) > 0)
        <div class="mb-4">
          <h5 style="color: #940000; font-weight: 600; margin-bottom: 15px; padding-bottom: 8px; border-bottom: 2px solid #940000;">
            <i class="fa fa-star"></i> Amenities
          </h5>
          <div class="row">
            @foreach($room->amenities as $amenity)
            <div class="col-md-6 col-sm-6 mb-2">
              <span class="badge badge-secondary" style="font-size: 13px; padding: 8px 12px; border-radius: 5px; display: inline-block; width: 100%;">
                <i class="fa fa-check-circle"></i> {{ $amenity }}
              </span>
            </div>
            @endforeach
          </div>
        </div>
        @endif
        
        <!-- Room Features -->
        @if($room->pet_friendly || $room->smoking_allowed)
        <div class="mb-4">
          <h5 style="color: #940000; font-weight: 600; margin-bottom: 15px; padding-bottom: 8px; border-bottom: 2px solid #940000;">
            <i class="fa fa-tags"></i> Special Features
          </h5>
          <div class="row">
            @if($room->pet_friendly)
            <div class="col-md-6 mb-2">
              <span class="badge badge-success" style="font-size: 13px; padding: 8px 12px;">
                <i class="fa fa-paw"></i> Pet Friendly
              </span>
            </div>
            @endif
            @if($room->smoking_allowed)
            <div class="col-md-6 mb-2">
              <span class="badge badge-warning" style="font-size: 13px; padding: 8px 12px;">
                <i class="fa fa-smoking"></i> Smoking Allowed
              </span>
            </div>
            @endif
          </div>
        </div>
        @endif
        
        <!-- Current Booking Info -->
        @if($currentBooking)
        <div class="mb-4">
          <hr style="margin: 20px 0; border-color: #e0e0e0;">
          <div class="alert alert-info" style="border-left: 4px solid #17a2b8; border-radius: 5px; margin: 0;">
            <h6 style="margin: 0 0 8px 0; font-weight: 600;">
              <i class="fa fa-calendar-check-o"></i> Current Booking
            </h6>
            <p style="margin: 5px 0;">
              <strong>Reference:</strong> <code>{{ $currentBooking->booking_reference }}</code><br>
              <strong>Check-in:</strong> {{ $currentBooking->check_in->format('M d, Y') }}<br>
              <strong>Check-out:</strong> {{ $currentBooking->check_out->format('M d, Y') }}
            </p>
          </div>
        </div>
        @endif
        
        <!-- Actions -->
        <div class="mt-4" style="border-top: 1px solid #e0e0e0; padding-top: 15px;">
          <button type="button" 
                  class="btn btn-primary" 
                  style="font-weight: 600; padding: 10px 20px; border-radius: 5px;"
                  onclick="showRoomDetailsModal({{ $room->id }})"
                  data-room-id="{{ $room->id }}"
                  data-room-type="{{ $room->room_type ?? 'N/A' }}"
                  data-room-number="{{ $room->room_number ?? 'N/A' }}"
                  data-capacity="{{ $room->capacity ?? '' }}"
                  data-bed-type="{{ $room->bed_type ?? '' }}"
                  data-floor="{{ $room->floor_location ?? '' }}"
                  data-bathroom="{{ $room->bathroom_type ?? '' }}"
                  data-price="{{ $room->price_per_night ?? '' }}"
                  data-description="{{ $room->description ?? '' }}"
                  data-amenities="{{ json_encode($room->amenities ?? []) }}"
                  data-pet-friendly="{{ $room->pet_friendly ? '1' : '0' }}"
                  data-smoking="{{ $room->smoking_allowed ? '1' : '0' }}"
                  data-images="{{ json_encode($room->images ?? []) }}">
            <i class="fa fa-eye"></i> View Room Details
          </button>
          @if($currentBooking)
          <button type="button" 
                  class="btn btn-info" 
                  style="font-weight: 600; padding: 10px 20px; border-radius: 5px;"
                  onclick="viewBookingDetails({{ $currentBooking->id }})">
            <i class="fa fa-calendar"></i> View Booking
          </button>
          @endif
        </div>
      </div>
    </div>
  </div>
  @endforeach
</div>
@else
<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-body text-center" style="padding: 50px;">
        <i class="fa fa-bed fa-5x text-muted mb-3"></i>
        <h3>No Room Information</h3>
        <p class="text-muted">You don't have any room bookings yet. Book a room to see room information here.</p>
        <a href="{{ route('booking.index') }}" class="btn btn-primary">
          <i class="fa fa-plus"></i> Book a Room
        </a>
      </div>
    </div>
  </div>
</div>
@endif

<!-- Room Details Modal -->
<div class="modal fade" id="roomDetailsModal" tabindex="-1" role="dialog" aria-labelledby="roomDetailsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document" style="max-width: 900px;">
    <div class="modal-content" style="border-radius: 10px; border: none; box-shadow: 0 5px 20px rgba(0,0,0,0.2);">
      <div class="modal-header" style="background: linear-gradient(135deg, #940000 0%, #d66a2a 100%); color: white; border-radius: 10px 10px 0 0; padding: 20px;">
        <h4 class="modal-title" id="roomDetailsModalLabel" style="color: white; font-weight: 600;">
          <i class="fa fa-bed"></i> <span id="modalRoomType">Room Details</span>
        </h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white; opacity: 0.9;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" style="padding: 25px; max-height: 70vh; overflow-y: auto;">
        <div id="roomDetailsContent">
          <!-- Content will be populated by JavaScript -->
        </div>
      </div>
      <div class="modal-footer" style="border-top: 1px solid #e0e0e0; padding: 15px 25px;">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" style="font-weight: 600; padding: 8px 20px; border-radius: 5px;">
          <i class="fa fa-times"></i> Close
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Booking Details Modal -->
<div class="modal fade" id="bookingDetailsModal" tabindex="-1" role="dialog" aria-labelledby="bookingDetailsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document" style="max-width: 800px;">
    <div class="modal-content" style="border-radius: 10px; border: none; box-shadow: 0 5px 20px rgba(0,0,0,0.2);">
      <div class="modal-header" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); color: white; border-radius: 10px 10px 0 0; padding: 20px;">
        <h4 class="modal-title" id="bookingDetailsModalLabel" style="color: white; font-weight: 600;">
          <i class="fa fa-calendar-check-o"></i> Booking Details
        </h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white; opacity: 0.9;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" style="padding: 25px; max-height: 70vh; overflow-y: auto;">
        <div id="bookingDetailsContent">
          <!-- Content will be populated by JavaScript -->
        </div>
      </div>
      <div class="modal-footer" style="border-top: 1px solid #e0e0e0; padding: 15px 25px;">
        <button type="button" class="btn btn-secondary" data-dismiss="modal" style="font-weight: 600; padding: 8px 20px; border-radius: 5px;">
          <i class="fa fa-times"></i> Close
        </button>
      </div>
    </div>
  </div>
</div>

<style>
  .modal-body h5 {
    color: #940000;
    font-weight: 600;
    margin-bottom: 15px;
    padding-bottom: 8px;
    border-bottom: 2px solid #940000;
  }
  .modal-body .info-row {
    padding: 10px 0;
    border-bottom: 1px solid #f0f0f0;
  }
  .modal-body .info-row:last-child {
    border-bottom: none;
  }
  .modal-body .info-label {
    font-weight: 600;
    color: #555;
    width: 150px;
    display: inline-block;
  }
  .modal-body .info-value {
    color: #333;
  }
  .room-image-gallery {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 10px;
    margin-top: 15px;
  }
  .room-image-gallery img {
    width: 100%;
    height: 120px;
    object-fit: cover;
    border-radius: 8px;
    cursor: pointer;
    transition: transform 0.2s;
  }
  .room-image-gallery img:hover {
    transform: scale(1.05);
  }
  
  /* Mobile Responsive Styles */
  @media (max-width: 768px) {
    /* Room Cards */
    .col-md-6 {
      flex: 0 0 100%;
      max-width: 100%;
      margin-bottom: 20px;
    }
    
    /* Tile Title */
    .tile-title-w-btn {
      flex-direction: column;
      align-items: flex-start !important;
      gap: 10px;
    }
    
    .tile-title-w-btn .tile-title {
      font-size: 18px !important;
      margin-bottom: 10px;
    }
    
    .tile-title-w-btn .btn-group {
      width: 100%;
    }
    
    /* Room Images */
    .col-6 {
      flex: 0 0 50%;
      max-width: 50%;
    }
    
    .col-6 img {
      height: 120px !important;
    }
    
    /* Room Details Tables */
    .col-md-6 .table {
      font-size: 13px;
    }
    
    .col-md-6 .table td {
      padding: 8px 5px;
      font-size: 12px;
    }
    
    .col-md-6 .table td strong {
      font-size: 12px;
    }
    
    .col-md-6 .table td i {
      font-size: 14px;
    }
    
    /* Stack room details columns on mobile */
    .row .col-md-6 {
      flex: 0 0 100%;
      max-width: 100%;
      margin-bottom: 15px;
    }
    
    /* Description */
    .mb-4 p {
      font-size: 13px;
      text-align: left !important;
    }
    
    /* Amenities */
    .col-md-6.col-sm-6 {
      flex: 0 0 100%;
      max-width: 100%;
    }
    
    .col-md-6.col-sm-6 .badge {
      width: 100%;
      text-align: left;
    }
    
    /* Special Features */
    .col-md-6.mb-2 {
      flex: 0 0 100%;
      max-width: 100%;
    }
    
    /* Current Booking Alert */
    .alert {
      font-size: 13px;
      padding: 12px;
    }
    
    .alert h6 {
      font-size: 14px;
    }
    
    .alert p {
      font-size: 12px;
      margin: 5px 0 !important;
    }
    
    /* Action Buttons */
    .mt-4 .btn {
      width: 100%;
      margin-bottom: 10px;
      font-size: 14px;
      padding: 12px 20px;
    }
    
    .mt-4 .btn:last-child {
      margin-bottom: 0;
    }
    
    /* Section Headings */
    h5 {
      font-size: 16px !important;
    }
    
    /* Modals */
    .modal-dialog {
      margin: 10px;
      max-width: calc(100% - 20px);
    }
    
    .modal-body {
      padding: 15px !important;
      max-height: 70vh;
    }
    
    .modal-body h4 {
      font-size: 18px;
    }
    
    .modal-body h5 {
      font-size: 15px !important;
    }
    
    .modal-body .info-label {
      width: 100%;
      display: block;
      margin-bottom: 5px;
    }
    
    .modal-body .info-value {
      display: block;
      margin-bottom: 10px;
    }
    
    .modal-body .row .col-md-6 {
      flex: 0 0 100%;
      max-width: 100%;
      margin-bottom: 15px;
    }
    
    .room-image-gallery {
      grid-template-columns: repeat(2, 1fr);
      gap: 8px;
    }
    
    .room-image-gallery img {
      height: 100px;
    }
    
    /* Empty State */
    .text-center {
      padding: 30px 15px !important;
    }
    
    .text-center h3 {
      font-size: 20px;
    }
    
    .text-center p {
      font-size: 14px;
    }
    
    .text-center .fa-5x {
      font-size: 3rem !important;
    }
  }
  
  @media (max-width: 480px) {
    /* Smaller mobile adjustments */
    .tile-title {
      font-size: 16px !important;
    }
    
    .col-6 img {
      height: 100px !important;
    }
    
    .room-image-gallery {
      grid-template-columns: 1fr;
    }
    
    .room-image-gallery img {
      height: 150px;
    }
    
    .modal-body {
      padding: 12px !important;
    }
    
    .modal-body h4 {
      font-size: 16px;
    }
  }
</style>

<script>
const exchangeRate = {{ $exchangeRate ?? 2500 }};

function showRoomDetailsModal(roomId) {
  const button = document.querySelector(`button[data-room-id="${roomId}"]`);
  if (!button) return;
  
  const room = {
    id: button.getAttribute('data-room-id'),
    type: button.getAttribute('data-room-type'),
    number: button.getAttribute('data-room-number'),
    capacity: button.getAttribute('data-capacity'),
    bedType: button.getAttribute('data-bed-type'),
    floor: button.getAttribute('data-floor'),
    bathroom: button.getAttribute('data-bathroom'),
    price: button.getAttribute('data-price'),
    description: button.getAttribute('data-description'),
    amenities: JSON.parse(button.getAttribute('data-amenities') || '[]'),
    petFriendly: button.getAttribute('data-pet-friendly') === '1',
    smoking: button.getAttribute('data-smoking') === '1',
    images: JSON.parse(button.getAttribute('data-images') || '[]')
  };
  
  // Update modal title
  document.getElementById('modalRoomType').textContent = room.type + ' - Room ' + room.number;
  
  // Build content HTML
  let contentHtml = '';
  
  // Images
  if (room.images && room.images.length > 0) {
    contentHtml += '<div class="mb-4">';
    contentHtml += '<h5><i class="fa fa-image"></i> Room Images</h5>';
    contentHtml += '<div class="room-image-gallery">';
    room.images.forEach(image => {
      const imageUrl = '{{ asset("storage/") }}/' + image;
      contentHtml += `<img src="${imageUrl}" alt="Room Image" onclick="window.open('${imageUrl}', '_blank')">`;
    });
    contentHtml += '</div>';
    contentHtml += '</div>';
  }
  
  // Room Details
  contentHtml += '<div class="mb-4">';
  contentHtml += '<h5><i class="fa fa-info-circle"></i> Room Information</h5>';
  contentHtml += '<div class="row">';
  contentHtml += '<div class="col-md-6">';
  contentHtml += '<div class="info-row"><span class="info-label"><i class="fa fa-home" style="color: #940000;"></i> Room Number:</span><span class="info-value"><strong>' + room.number + '</strong></span></div>';
  if (room.capacity) {
    contentHtml += '<div class="info-row"><span class="info-label"><i class="fa fa-users" style="color: #940000;"></i> Capacity:</span><span class="info-value">' + room.capacity + ' Guest(s)</span></div>';
  }
  if (room.bedType) {
    contentHtml += '<div class="info-row"><span class="info-label"><i class="fa fa-bed" style="color: #940000;"></i> Bed Type:</span><span class="info-value">' + room.bedType + '</span></div>';
  }
  contentHtml += '</div>';
  contentHtml += '<div class="col-md-6">';
  if (room.floor) {
    contentHtml += '<div class="info-row"><span class="info-label"><i class="fa fa-building" style="color: #940000;"></i> Floor:</span><span class="info-value">' + room.floor + '</span></div>';
  }
  if (room.bathroom) {
    contentHtml += '<div class="info-row"><span class="info-label"><i class="fa fa-shower" style="color: #940000;"></i> Bathroom:</span><span class="info-value">' + room.bathroom + '</span></div>';
  }
  if (room.price) {
    const priceTzs = (parseFloat(room.price) * exchangeRate).toLocaleString('en-US');
    contentHtml += '<div class="info-row"><span class="info-label"><i class="fa fa-dollar" style="color: #940000;"></i> Price/Night:</span><span class="info-value"><strong>$' + parseFloat(room.price).toFixed(2) + '</strong> ≈ <strong>' + priceTzs + ' TZS</strong></span></div>';
  }
  contentHtml += '</div>';
  contentHtml += '</div>';
  contentHtml += '</div>';
  
  // Description
  if (room.description) {
    contentHtml += '<div class="mb-4">';
    contentHtml += '<h5><i class="fa fa-file-text"></i> Description</h5>';
    contentHtml += '<p style="line-height: 1.6; color: #555; text-align: justify;">' + room.description + '</p>';
    contentHtml += '</div>';
  }
  
  // Amenities
  if (room.amenities && room.amenities.length > 0) {
    contentHtml += '<div class="mb-4">';
    contentHtml += '<h5><i class="fa fa-star"></i> Amenities</h5>';
    contentHtml += '<div class="row">';
    room.amenities.forEach(amenity => {
      contentHtml += '<div class="col-md-6 mb-2">';
      contentHtml += '<span class="badge badge-secondary" style="font-size: 13px; padding: 8px 12px; border-radius: 5px; display: inline-block; width: 100%;">';
      contentHtml += '<i class="fa fa-check-circle"></i> ' + amenity;
      contentHtml += '</span>';
      contentHtml += '</div>';
    });
    contentHtml += '</div>';
    contentHtml += '</div>';
  }
  
  // Special Features
  if (room.petFriendly || room.smoking) {
    contentHtml += '<div class="mb-4">';
    contentHtml += '<h5><i class="fa fa-tags"></i> Special Features</h5>';
    contentHtml += '<div class="row">';
    if (room.petFriendly) {
      contentHtml += '<div class="col-md-6 mb-2">';
      contentHtml += '<span class="badge badge-success" style="font-size: 13px; padding: 8px 12px;"><i class="fa fa-paw"></i> Pet Friendly</span>';
      contentHtml += '</div>';
    }
    if (room.smoking) {
      contentHtml += '<div class="col-md-6 mb-2">';
      contentHtml += '<span class="badge badge-warning" style="font-size: 13px; padding: 8px 12px;"><i class="fa fa-smoking"></i> Smoking Allowed</span>';
      contentHtml += '</div>';
    }
    contentHtml += '</div>';
    contentHtml += '</div>';
  }
  
  document.getElementById('roomDetailsContent').innerHTML = contentHtml;
  $('#roomDetailsModal').modal('show');
}

function viewBookingDetails(bookingId) {
    // Show modal with loading state
    $('#bookingDetailsModal').modal('show');
    document.getElementById('bookingDetailsContent').innerHTML = `
        <div class="text-center">
            <i class="fa fa-spinner fa-spin fa-2x"></i>
            <p>Loading booking details...</p>
        </div>
    `;
    
    fetch('{{ route("customer.bookings.show", ":id") }}'.replace(':id', bookingId), {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const booking = data.booking;
            const room = booking.room || {};
            const exchangeRate = booking.locked_exchange_rate || {{ $exchangeRate ?? 2500 }};
            const fallbackImage = '{{ asset("landing_page_assets/img/bg-img/1.jpg") }}';
            
            // Corporate Logic
            const company = booking.company || {};
            const isCorporate = booking.is_corporate_booking == 1 || booking.company_id != null;
            const companyPays = isCorporate && booking.payment_responsibility === 'company';
            const selfPays = isCorporate && booking.payment_responsibility === 'self';

            const detailsHtml = `
                <div class="booking-details-view">
                    <!-- Top Status Bar -->
                    <div class="d-flex justify-content-between align-items-center mb-4 bg-light p-3 rounded">
                        <div>
                           <h4 class="mb-0" style="color: #e07632;">${booking.guest_name || 'Guest'}</h4>
                           <span class="text-muted small"><i class="fa fa-hashtag"></i> ${booking.booking_reference}</span>
                        </div>
                        <div class="text-right">
                           ${booking.status === 'confirmed' ? '<span class="badge badge-success p-2">Confirmed</span>' : ''}
                           ${booking.status === 'pending' ? '<span class="badge badge-warning p-2">Pending</span>' : ''}
                           ${booking.status === 'cancelled' ? '<span class="badge badge-danger p-2">Cancelled</span>' : ''}
                           ${booking.status === 'completed' ? '<span class="badge badge-info p-2">Completed</span>' : ''}
                           <div class="mt-1 small text-muted">
                              ${booking.check_in_status === 'checked_in' ? '<i class="fa fa-check-circle text-success"></i> Checked In' : 
                                booking.check_in_status === 'checked_out' ? '<i class="fa fa-check-circle text-secondary"></i> Checked Out' : 
                                '<i class="fa fa-clock-o"></i> Status: ' + (booking.check_in_status === 'pending' ? 'Not Checked In' : (booking.check_in_status || 'Pending'))}
                           </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Left Column -->
                        <div class="col-md-6">
                            
                            <!-- Guest Details Card -->
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header bg-white border-bottom-0 pb-0">
                                    <h6 class="text-uppercase text-muted mb-0" style="font-size: 0.75rem; letter-spacing: 1px;">Guest Details</h6>
                                </div>
                                <div class="card-body pt-2">
                                   <div class="d-flex align-items-center mb-3">
                                      <div class="mr-3 text-center" style="width: 40px;">
                                        <i class="fa fa-user-circle fa-2x text-muted"></i>
                                      </div>
                                      <div>
                                        <div class="font-weight-bold">${booking.first_name || ''} ${booking.last_name || ''}</div>
                                        <div class="text-muted small">${booking.guest_email}</div>
                                      </div>
                                   </div>
                                   <div class="row mb-2">
                                     <div class="col-1 text-center"><i class="fa fa-phone text-muted"></i></div>
                                     <div class="col-11">${booking.guest_phone || 'N/A'}</div>
                                   </div>
                                   <div class="row mb-2">
                                     <div class="col-1 text-center"><i class="fa fa-globe text-muted"></i></div>
                                     <div class="col-11">${booking.country || 'N/A'}</div>
                                   </div>
                                   <div class="row">
                                     <div class="col-1 text-center"><i class="fa fa-users text-muted"></i></div>
                                     <div class="col-11">${booking.number_of_guests || 1} Person(s)</div>
                                   </div>
                                </div>
                            </div>

                            <!-- Stay Dates Card -->
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header bg-white border-bottom-0 pb-0">
                                    <h6 class="text-uppercase text-muted mb-0" style="font-size: 0.75rem; letter-spacing: 1px;">Stay Information</h6>
                                </div>
                                <div class="card-body pt-2">
                                   <div class="d-flex justify-content-between align-items-center bg-light rounded p-3 mb-2">
                                      <div class="text-center">
                                         <div class="text-muted small text-uppercase">Check In</div>
                                         <div class="font-weight-bold" style="color: #e07632;">${booking.check_in ? new Date(booking.check_in).toLocaleDateString('en-US', {day: 'numeric', month: 'short', year: 'numeric'}) : 'N/A'}</div>
                                      </div>
                                      <div class="text-muted"><i class="fa fa-arrow-right"></i></div>
                                      <div class="text-center">
                                         <div class="text-muted small text-uppercase">Check Out</div>
                                         <div class="font-weight-bold" style="color: #e07632;">${booking.check_out ? new Date(booking.check_out).toLocaleDateString('en-US', {day: 'numeric', month: 'short', year: 'numeric'}) : 'N/A'}</div>
                                      </div>
                                   </div>
                                   
                                   ${room.images && room.images.length > 0 ? `
                                   <div class="mt-3 text-center">
                                      ${(() => {
                                          let imgPath = room.images[0];
                                          if (imgPath.startsWith('rooms/') || imgPath.startsWith('storage/rooms/')) {
                                              imgPath = imgPath.replace(/^storage\//, '');
                                          } else if (!imgPath.startsWith('http') && !imgPath.startsWith('/')) {
                                              imgPath = 'rooms/' + imgPath;
                                          }
                                          const storageBase = '{{ asset("storage") }}';
                                          const imageUrl = imgPath.startsWith('http') ? imgPath : storageBase + '/' + imgPath;
                                          return '<img src="' + imageUrl + '" alt="Room Image" class="img-fluid rounded shadow-sm" style="max-height: 150px; object-fit: cover; width: 100%;">';
                                      })()}
                                   </div>
                                   ` : ''}
                                </div>
                            </div>

                        </div>

                        <!-- Right Column -->
                        <div class="col-md-6">
                            
                             <!-- Room Details Card -->
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header bg-white border-bottom-0 pb-0">
                                    <h6 class="text-uppercase text-muted mb-0" style="font-size: 0.75rem; letter-spacing: 1px;">Room Assigned</h6>
                                </div>
                                <div class="card-body pt-2">
                                   <div class="d-flex align-items-center mb-3">
                                      <div class="mr-3 text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background-color: #e07632;">
                                        <i class="fa fa-bed"></i>
                                      </div>
                                      <div>
                                         <div class="h5 mb-0">${room.room_number || 'Unassigned'}</div>
                                         <div class="text-muted small">${room.room_type || 'Standard'}</div>
                                      </div>
                                   </div>
                                   <div class="d-flex justify-content-between small text-muted border-top pt-2">
                                      <span>Price per Night:</span>
                                      <span class="font-weight-bold">$${parseFloat(room.price_per_night || 0).toFixed(2)}</span>
                                   </div>
                                </div>
                            </div>

                            <!-- Payment Card -->
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header bg-white border-bottom-0 pb-0">
                                    <h6 class="text-uppercase text-muted mb-0" style="font-size: 0.75rem; letter-spacing: 1px;">Payment & Billing</h6>
                                </div>
                                <div class="card-body pt-2">
                                   
                                   ${companyPays ? `
                                        <div class="alert alert-info small mb-3">
                                            <i class="fa fa-building-o"></i> <strong>Corporate Booking</strong><br>
                                            Room charges are billed directly to <strong>${company.name || 'the Company'}</strong>.
                                        </div>
                                   ` : ''}

                                   ${selfPays ? `
                                        <div class="alert alert-warning small mb-3">
                                            <i class="fa fa-user"></i> <strong>Self-Pay Corporate Booking</strong><br>
                                            You are responsible for these charges.
                                        </div>
                                   ` : ''}

                                   <div class="d-flex justify-content-between mb-2">
                                      <span class="text-muted">Total Price:</span>
                                      <span class="font-weight-bold h5 mb-0" style="color: #e07632;">$${parseFloat(booking.total_price || 0).toFixed(2)}</span>
                                   </div>
                                   <div class="text-right text-muted small mb-3">
                                      ≈ ${(parseFloat(booking.total_price || 0) * exchangeRate).toLocaleString()} TZS
                                   </div>

                                   <div class="d-flex justify-content-between mb-2 small">
                                      <span class="text-muted">Amount Paid:</span>
                                      <span class="text-success font-weight-bold">-$${parseFloat(booking.amount_paid || 0).toFixed(2)}</span>
                                   </div>
                                   
                                   ${booking.payment_status === 'partial' ? `
                                   <hr class="my-2">
                                   <div class="d-flex justify-content-between align-items-end">
                                      <span class="font-weight-bold">Remaining:</span>
                                      <span class="h6 mb-0 text-danger">$${parseFloat((booking.total_price || 0) - (booking.amount_paid || 0)).toFixed(2)}</span>
                                   </div>
                                   
                                   ${!companyPays ? `
                                       ${selfPays ? `<small class="badge badge-light border border-warning text-dark mb-2">Self-Pay (Services)</small>` : ''}
                                       <div class="progress mt-2" style="height: 6px;">
                                          <div class="progress-bar bg-info" role="progressbar" style="width: ${parseFloat(booking.payment_percentage || 0)}%;" aria-valuenow="${parseFloat(booking.payment_percentage || 0)}" aria-valuemin="0" aria-valuemax="100"></div>
                                       </div>
                                       <small class="text-muted text-right d-block mt-1">${parseFloat(booking.payment_percentage || 0).toFixed(0)}% Paid</small>
                                   ` : '<small class="text-muted d-block mt-1 text-right">Payable by Company</small>'}
                                   ` : ''}
                                   
                                   ${booking.payment_status === 'pending' && !companyPays ? `
                                   <hr class="my-2">
                                   <div class="alert alert-warning py-2 mb-0 small">
                                      <i class="fa fa-exclamation-circle"></i> Payment is pending.
                                   </div>
                                   ` : ''}

                                   <div class="bg-light p-2 rounded mt-3 small">
                                      <div class="d-flex justify-content-between">
                                          <span>Status:</span>
                                          <strong>${booking.payment_status ? booking.payment_status.toUpperCase() : 'N/A'}</strong>
                                      </div>
                                      <div class="d-flex justify-content-between mt-1">
                                          <span>Method:</span>
                                          <span>${booking.payment_method || 'N/A'}</span>
                                      </div>
                                      ${booking.paid_at ? `
                                      <div class="d-flex justify-content-between mt-1 text-muted">
                                          <span>Paid On:</span>
                                          <span>${new Date(booking.paid_at).toLocaleDateString()}</span>
                                      </div>` : ''}
                                   </div>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            `;
            
            document.getElementById('bookingDetailsContent').innerHTML = detailsHtml;
        } else {
            document.getElementById('bookingDetailsContent').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fa fa-exclamation-triangle"></i> Failed to load booking details.
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('bookingDetailsContent').innerHTML = `
            <div class="alert alert-danger">
                <i class="fa fa-exclamation-triangle"></i> An error occurred while loading booking details.
            </div>
        `;
    });
}
</script>
@endsection





