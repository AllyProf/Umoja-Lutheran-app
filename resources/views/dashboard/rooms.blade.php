@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-bed"></i> Room Management</h1>
    <p>{{ isset($room) ? 'Edit Room' : 'Add New Room to Umoj Lutheran Hostel' }}</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ isset($role) && $role === 'super_admin' ? route('super_admin.dashboard') : route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">{{ isset($room) ? 'Edit Room' : 'Add Room' }}</a></li>
  </ul>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title">{{ isset($room) ? 'Edit Room' : 'Add New Room' }}</h3>
      
      <!-- Progress Steps -->
      <div class="wizard-steps mb-4">
        <div class="step-item active" data-step="1">
          <div class="step-number">1</div>
          <div class="step-label">Basic Info</div>
        </div>
        <div class="step-arrow"><i class="fa fa-arrow-right"></i></div>
        <div class="step-item" data-step="2">
          <div class="step-number">2</div>
          <div class="step-label">Pricing</div>
        </div>
        <div class="step-arrow"><i class="fa fa-arrow-right"></i></div>
        <div class="step-item" data-step="3">
          <div class="step-number">3</div>
          <div class="step-label">Amenities</div>
        </div>
        <div class="step-arrow"><i class="fa fa-arrow-right"></i></div>
        <div class="step-item" data-step="4">
          <div class="step-number">4</div>
          <div class="step-label">Status & Images</div>
        </div>
        <div class="step-arrow"><i class="fa fa-arrow-right"></i></div>
        <div class="step-item" data-step="5">
          <div class="step-number">5</div>
          <div class="step-label">Preview</div>
        </div>
      </div>

      <form id="roomForm" method="POST" action="{{ isset($room) ? route('admin.rooms.update', $room) : route('admin.rooms.store') }}" enctype="multipart/form-data">
        @csrf
        @if(isset($room))
          @method('PUT')
        @endif
        
        <!-- Step 1: Basic Room Info -->
        <div class="wizard-step" data-step="1">
          <h4 class="mb-4"><i class="fa fa-info-circle"></i> Basic Room Information</h4>
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="room_type">Room Type <span class="text-danger">*</span></label>
                <select class="form-control" id="room_type" name="room_type" required>
                  <option value="">Select Room Type</option>
                  <option value="Single" {{ (isset($room) && $room->room_type == 'Single') ? 'selected' : '' }}>Single</option>
                  <option value="Double" {{ (isset($room) && $room->room_type == 'Double') ? 'selected' : '' }}>Double</option>
                  <option value="Twins" {{ (isset($room) && $room->room_type == 'Twins') ? 'selected' : '' }}>Standard Twin Room</option>
                </select>
                <small class="form-text text-muted">Select room type first to enable bulk creation options</small>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="room_number">Room Number / Name <span class="text-danger">*</span></label>
                <input class="form-control" type="text" id="room_number" name="room_number" placeholder="e.g., 101, Deluxe Suite" value="{{ $room->room_number ?? '' }}" required>
                <small class="form-text text-muted" id="room_number_help_text">Enter a unique room number or name</small>
              </div>
            </div>
          </div>

          @if(!isset($room))
          <!-- Bulk Creation Option (for all room types) -->
          <div class="row mb-3" id="bulk_creation_section" style="display: none;">
            <div class="col-md-12">
              <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                  <i class="fa fa-layer-group"></i> <strong>Bulk Room Creation</strong>
                  <small class="float-right" id="bulk_room_type_label">Create multiple rooms at once</small>
                </div>
                <div class="card-body">
                  <!-- Step 1: Enable Bulk Creation -->
                  <div class="form-group mb-3">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" id="enable_bulk_create" name="enable_bulk_create" value="1">
                      <label class="form-check-label" for="enable_bulk_create">
                        <strong id="bulk_create_label">Create Multiple Rooms</strong>
                      </label>
                    </div>
                  </div>

                  <!-- Step 2: Quantity Input -->
                  <div class="row mb-3" id="quantity_section" style="display: none;">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="bulk_quantity" id="bulk_quantity_label">How many rooms? <span class="text-danger">*</span></label>
                        <input class="form-control" type="number" id="bulk_quantity" name="bulk_quantity" min="2" max="50" value="2" placeholder="e.g., 3 or 4">
                        <small class="form-text text-muted">Enter the number of rooms you want to create (minimum 2)</small>
                      </div>
                    </div>
                  </div>

                  <!-- Step 3: Assignment Method -->
                  <div class="row mb-3" id="assignment_method_section" style="display: none;">
                    <div class="col-md-12">
                      <div class="form-group">
                        <label><strong>Room Number Assignment Method:</strong></label>
                        <div class="btn-group btn-group-toggle" data-toggle="buttons" style="width: 100%;">
                          <label class="btn btn-outline-primary" style="flex: 1;">
                            <input type="radio" name="assignment_method" id="auto_generate" value="auto" autocomplete="off">
                            <i class="fa fa-magic"></i> Auto-Generate (Sequential)
                          </label>
                          <label class="btn btn-outline-primary active" style="flex: 1;">
                            <input type="radio" name="assignment_method" id="manual_assign" value="manual" checked autocomplete="off">
                            <i class="fa fa-edit"></i> Manual Assignment
                          </label>
                        </div>
                        <small class="form-text text-muted mt-2">Choose how room numbers will be assigned. <strong>Manual</strong> allows non-sequential numbers (e.g., 100, 204, 4046)</small>
                      </div>
                    </div>
                  </div>

                  <!-- Step 4a: Auto-Generate Fields -->
                  <div class="row mb-3" id="auto_generate_section" style="display: none;">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="starting_room_number">Starting Room Number <span class="text-danger">*</span></label>
                        <input class="form-control" type="text" id="starting_room_number" name="starting_room_number" placeholder="e.g., 201" value="">
                        <small class="form-text text-muted">System will create sequential room numbers starting from this number. Existing room numbers will be automatically skipped.</small>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label>Preview Room Numbers:</label>
                        <div class="alert alert-info" id="auto_preview" style="margin-bottom: 0;">
                          <small>Enter starting room number to see preview</small>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Step 4b: Manual Assignment Fields -->
                  <div class="row mb-3" id="manual_assign_section" style="display: block;">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="manual_room_numbers">Room Numbers <span class="text-danger">*</span></label>
                        <input class="form-control" type="text" id="manual_room_numbers" name="manual_room_numbers" placeholder="e.g., 100, 204, 4046 or 201, 203, 205" value="">
                        <small class="form-text text-muted">Enter room numbers separated by commas. Can be non-sequential (e.g., 100, 204, 4046). Existing room numbers will be automatically skipped.</small>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label>Preview Room Numbers:</label>
                        <div class="alert alert-info" id="manual_preview" style="margin-bottom: 0;">
                          <small>Enter room numbers to see preview</small>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          @endif

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="capacity">Capacity (Max Guests) <span class="text-danger">*</span></label>
                <input class="form-control" type="number" id="capacity" name="capacity" min="1" max="10" placeholder="Maximum number of guests" value="{{ $room->capacity ?? '' }}" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="bed_type">Bed Type <span class="text-danger">*</span></label>
                <select class="form-control" id="bed_type" name="bed_type" required>
                  <option value="">Select Bed Type</option>
                  <option value="King" {{ (isset($room) && $room->bed_type == 'King') ? 'selected' : '' }}>King</option>
                  <option value="Queen" {{ (isset($room) && $room->bed_type == 'Queen') ? 'selected' : '' }}>Queen</option>
                  <option value="Twin" {{ (isset($room) && $room->bed_type == 'Twin') ? 'selected' : '' }}>Twin</option>
                  <option value="Bunk" {{ (isset($room) && $room->bed_type == 'Bunk') ? 'selected' : '' }}>Bunk</option>
                  <option value="Single" {{ (isset($room) && $room->bed_type == 'Single') ? 'selected' : '' }}>Single</option>
                </select>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" rows="4" placeholder="Short description of the room, amenities, or view">{{ $room->description ?? '' }}</textarea>
              </div>
            </div>
          </div>
          
          <!-- Navigation Buttons for Step 1 -->
          <div class="wizard-navigation mt-4 pt-3 border-top">
            <button type="button" class="btn btn-primary float-right" onclick="changeStep(1)">
              Next <i class="fa fa-arrow-right"></i>
            </button>
          </div>
        </div>

        <!-- Step 2: Pricing Info -->
        <div class="wizard-step" data-step="2" style="display: none;">
          <h4 class="mb-4"><i class="fa fa-money"></i> Pricing Information (TZS)</h4>
          
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label for="price_per_night">Price per Night (TZS) <span class="text-danger">*</span></label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">TZS</span>
                  </div>
                  <input class="form-control" type="number" id="price_per_night" name="price_per_night" min="0" placeholder="0" value="{{ $room->price_per_night ?? '' }}" required>
                </div>
                <small class="form-text text-muted">Enter the room price in Tanzanian Shillings</small>
              </div>
            </div>
          </div>


          <!-- Navigation Buttons for Step 2 -->
          <div class="wizard-navigation mt-4 pt-3 border-top">
            <button type="button" class="btn btn-secondary" onclick="changeStep(-1)">
              <i class="fa fa-arrow-left"></i> Previous
            </button>
            <button type="button" class="btn btn-primary float-right" onclick="changeStep(1)">
              Next <i class="fa fa-arrow-right"></i>
            </button>
          </div>
        </div>

        <!-- Step 3: Amenities & Features -->
        <div class="wizard-step" data-step="3" style="display: none;">
          <h4 class="mb-4"><i class="fa fa-star"></i> Amenities & Features</h4>
          
          <div class="mb-3">
            <button type="button" class="btn btn-sm btn-outline-primary" id="checkAllAmenities" onclick="toggleAllAmenities()">
              <i class="fa fa-check-square"></i> Check All
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="uncheckAllAmenities" onclick="toggleAllAmenities(false)" style="display: none;">
              <i class="fa fa-square"></i> Uncheck All
            </button>
          </div>
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="checkbox-inline">
                  <input type="checkbox" id="wifi" name="amenities[]" value="Free Wi-Fi" class="amenity-checkbox"> <i class="fa fa-wifi"></i> Free Wi-Fi
                </label>
              </div>
              <div class="form-group">
                <label class="checkbox-inline">
                  <input type="checkbox" id="ac_heating" name="amenities[]" value="Air-Conditioning" class="amenity-checkbox"> <i class="fa fa-thermometer-half"></i> Air-Conditioning
                </label>
              </div>
              <div class="form-group">
                <label class="checkbox-inline">
                  <input type="checkbox" id="tv" name="amenities[]" value="Smart TV Screen" class="amenity-checkbox"> <i class="fa fa-tv"></i> Smart TV Screen
                </label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label class="checkbox-inline">
                  <input type="checkbox" id="telephone" name="amenities[]" value="Telephone extension" class="amenity-checkbox"> <i class="fa fa-phone"></i> Telephone extension
                </label>
              </div>
              <div class="form-group">
                <label class="checkbox-inline">
                  <input type="checkbox" id="tea_coffee" name="amenities[]" value="Tea and Coffee facility" class="amenity-checkbox"> <i class="fa fa-coffee"></i> Tea and Coffee facility
                </label>
              </div>
              <div class="form-group">
                <label class="checkbox-inline">
                  <input type="checkbox" id="hair_dryer" name="amenities[]" value="Hair dryer" class="amenity-checkbox"> <i class="fa fa-fan"></i> Hair dryer
                </label>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="checkin_time">Check-in Time</label>
                <input class="form-control" type="time" id="checkin_time" name="checkin_time" value="{{ isset($room) && $room->checkin_time ? substr($room->checkin_time, 0, 5) : '14:00' }}">
                <small class="form-text text-muted">Default check-in time (e.g., 2 PM)</small>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="checkout_time">Check-out Time</label>
                <input class="form-control" type="time" id="checkout_time" name="checkout_time" value="{{ isset($room) && $room->checkout_time ? substr($room->checkout_time, 0, 5) : '10:00' }}">
                <small class="form-text text-muted">Default check-out time (e.g., 10 AM)</small>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label for="special_notes">Special Notes</label>
            <textarea class="form-control" id="special_notes" name="special_notes" rows="3" placeholder="e.g., pet-friendly, smoking allowed, special requests"></textarea>
          </div>

          <div class="form-group">
            <label for="wifi_password">WiFi Password</label>
            <input class="form-control" type="text" id="wifi_password" name="wifi_password" placeholder="e.g., Umoj Lutheran Hostel2024" maxlength="255">
            <small class="form-text text-muted">WiFi password for guests staying in this room</small>
          </div>

          <!-- Navigation Buttons for Step 3 -->
          <div class="wizard-navigation mt-4 pt-3 border-top">
            <button type="button" class="btn btn-secondary" onclick="changeStep(-1)">
              <i class="fa fa-arrow-left"></i> Previous
            </button>
            <button type="button" class="btn btn-primary float-right" onclick="changeStep(1)">
              Next <i class="fa fa-arrow-right"></i>
            </button>
          </div>
        </div>

        <!-- Step 4: Availability & Status -->
        <div class="wizard-step" data-step="4" style="display: none;">
          <h4 class="mb-4"><i class="fa fa-check-circle"></i> Status & Images</h4>
          
          <div class="form-group">
            <label for="room_images">Room Images</label>
            <div class="image-upload-container">
              <div class="upload-area" id="uploadArea">
                <i class="fa fa-cloud-upload"></i>
                <p><strong>Click or drag images here to upload</strong></p>
                <p class="text-muted" style="font-size: 12px;">Supports: JPEG, PNG, GIF (Max 5MB per image)</p>
                <input type="file" id="room_images" name="room_images[]" multiple accept="image/*" style="display: none;">
                <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('room_images').click()">
                  <i class="fa fa-plus"></i> Select Images
                </button>
              </div>
              <div id="imagePreview" class="mt-4"></div>
              <div id="imageCount" class="mt-2 text-muted"></div>
            </div>
          </div>
          
          <!-- Navigation Buttons for Step 4 -->
          <div class="wizard-navigation mt-4 pt-3 border-top">
            <button type="button" class="btn btn-secondary" onclick="changeStep(-1)">
              <i class="fa fa-arrow-left"></i> Previous
            </button>
            <button type="button" class="btn btn-primary float-right" onclick="changeStep(1)">
              Next <i class="fa fa-arrow-right"></i>
            </button>
          </div>
        </div>

        <!-- Step 5: Preview -->
        <div class="wizard-step" data-step="5" style="display: none;">
          <h4 class="mb-4"><i class="fa fa-eye"></i> Preview Information</h4>
          
          <div class="preview-container">
            <div class="preview-section">
              <h5><i class="fa fa-info-circle"></i> Basic Information</h5>
              <table class="table table-bordered">
                <tr><th width="30%">Room Number/Name:</th><td id="preview_room_number">-</td></tr>
                <tr><th>Room Type:</th><td id="preview_room_type">-</td></tr>
                <tr><th>Capacity:</th><td id="preview_capacity">-</td></tr>
                <tr><th>Bed Type:</th><td id="preview_bed_type">-</td></tr>
                <tr><th>Description:</th><td id="preview_description" style="white-space: pre-wrap; word-wrap: break-word;">-</td></tr>
              </table>
            </div>

            <div class="preview-section mt-4">
              <h5><i class="fa fa-dollar"></i> Pricing Information</h5>
              <table class="table table-bordered">
                <tr><th width="30%">Price per Night:</th><td id="preview_price_per_night">-</td></tr>
              </table>
            </div>

            <div class="preview-section mt-4">
              <h5><i class="fa fa-star"></i> Amenities & Features</h5>
              <div id="preview_amenities" class="amenities-list mb-3"></div>
              <table class="table table-bordered">
                <tr><th width="30%">Check-in Time:</th><td id="preview_checkin_time">-</td></tr>
                <tr><th>Check-out Time:</th><td id="preview_checkout_time">-</td></tr>
                <tr><th>Special Notes:</th><td id="preview_special_notes" style="white-space: pre-wrap; word-wrap: break-word;">-</td></tr>
              </table>
            </div>

            <div class="preview-section mt-4">
              <h5><i class="fa fa-check-circle"></i> Images</h5>
              <div id="preview_images" class="mt-3" style="max-width: 600px; margin: 15px auto 0;"></div>
            </div>
          </div>

          <!-- Navigation Buttons -->
          <div class="wizard-navigation mt-4 pt-3 border-top">
            <button type="button" class="btn btn-secondary" onclick="changeStep(-1)">
              <i class="fa fa-arrow-left"></i> Previous
            </button>
            <button type="submit" class="btn btn-success float-right" id="submitBtn">
              <i class="fa fa-check"></i> Submit Room
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('dashboard_assets/js/plugins/sweetalert.min.js') }}"></script>
<style>
.wizard-steps {
  display: flex;
  justify-content: space-between;
  margin-bottom: 30px;
  padding: 20px 0;
  border-bottom: 2px solid #e0e0e0;
}

.step-item {
  flex: 1;
  text-align: center;
  position: relative;
  opacity: 0.5;
  transition: all 0.3s ease;
}

.step-item.active {
  opacity: 1;
}

.step-item.completed {
  opacity: 1;
}

.step-item.completed .step-number {
  background-color: #28a745;
  color: white;
}

.step-arrow {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0 10px;
  color: #940000;
  font-size: 20px;
  opacity: 0.3;
  transition: opacity 0.3s ease;
  margin-top: 25px;
}

.step-number {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  background-color: #e0e0e0;
  color: #666;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
  font-weight: bold;
  margin: 0 auto 10px;
  transition: all 0.3s ease;
}

.step-item.active .step-number {
  background-color: #940000;
  color: white;
  box-shadow: 0 0 0 4px rgba(231, 122, 58, 0.2);
}

.step-label {
  font-size: 14px;
  color: #666;
  font-weight: 500;
}

.step-item.active .step-label {
  color: #940000;
  font-weight: 600;
}

.wizard-step {
  animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.checkbox-inline {
  display: block;
  padding: 10px;
  margin-bottom: 10px;
  border: 1px solid #e0e0e0;
  border-radius: 4px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.checkbox-inline:hover {
  background-color: #f5f5f5;
  border-color: #940000;
}

.checkbox-inline input[type="checkbox"] {
  margin-right: 8px;
}

.checkbox-inline i {
  margin-right: 5px;
  color: #940000;
}

.image-upload-container {
  width: 100%;
}

.upload-area {
  border: 2px dashed #940000;
  border-radius: 8px;
  padding: 20px;
  text-align: center;
  background-color: #fff8f4;
  cursor: pointer;
  transition: all 0.3s ease;
  max-width: 500px;
  margin: 0 auto;
}

.upload-area:hover {
  border-color: #d06a2f;
  background-color: #fff5ee;
}

.upload-area.dragover {
  border-color: #940000;
  background-color: #fff5ee;
  transform: scale(1.01);
}

.upload-area i {
  color: #940000;
  margin-bottom: 8px;
  font-size: 2rem !important;
}

.upload-area p {
  margin: 5px 0;
  font-size: 14px;
}

.upload-area .btn {
  margin-top: 10px;
  padding: 6px 20px;
  font-size: 14px;
}

#imagePreview {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
  gap: 15px;
  margin-top: 15px;
}

.image-preview-item {
  position: relative;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  transition: transform 0.3s ease;
  max-width: 100%;
}

.image-preview-item:hover {
  transform: translateY(-3px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.image-preview-item img {
  width: 100%;
  height: 150px;
  object-fit: cover;
  display: block;
}

.image-preview-item .remove-btn {
  position: absolute;
  top: 5px;
  right: 5px;
  background-color: rgba(220, 53, 69, 0.9);
  color: white;
  border: none;
  border-radius: 50%;
  width: 30px;
  height: 30px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  transition: all 0.3s ease;
}

.image-preview-item .remove-btn:hover {
  background-color: #dc3545;
  transform: scale(1.1);
}

.image-preview-item .image-info {
  padding: 6px;
  background-color: white;
  font-size: 11px;
  color: #666;
  text-overflow: ellipsis;
  overflow: hidden;
  white-space: nowrap;
}

.image-preview-item .remove-btn {
  width: 25px;
  height: 25px;
  font-size: 12px;
}

.form-group label span.text-danger {
  color: #dc3545;
}

.preview-container {
  background-color: #f8f9fa;
  padding: 20px;
  border-radius: 8px;
}

.preview-section {
  background-color: white;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.preview-section h5 {
  color: #940000;
  margin-bottom: 15px;
  padding-bottom: 10px;
  border-bottom: 2px solid #940000;
}

.amenities-list {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  margin-bottom: 15px;
}

.amenities-list .badge {
  padding: 8px 12px;
  font-size: 14px;
  background-color: #940000;
  color: white;
}

.wizard-navigation {
  clear: both;
}

/* Mobile Responsive Styles */
@media (max-width: 768px) {
  /* App Title - Mobile */
  .app-title h1 {
    font-size: 20px !important;
    line-height: 1.3;
  }
  
  .app-title p {
    font-size: 13px !important;
  }
  
  .app-title .breadcrumb {
    font-size: 12px;
    margin-top: 5px;
  }
  
  /* Tile - Mobile */
  .tile {
    padding: 15px !important;
    margin-bottom: 20px;
  }
  
  .tile-title {
    font-size: 18px !important;
    margin-bottom: 20px;
  }
  
  /* Wizard Steps - Mobile */
  .wizard-steps {
    flex-wrap: wrap;
    padding: 15px 0;
    margin-bottom: 20px;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }
  
  .step-item {
    min-width: 60px;
    flex: 0 0 auto;
    margin: 0 5px;
  }
  
  .step-number {
    width: 40px !important;
    height: 40px !important;
    font-size: 16px !important;
    margin: 0 auto 5px !important;
  }
  
  .step-label {
    font-size: 11px !important;
    line-height: 1.2;
  }
  
  .step-arrow {
    display: none !important;
  }
  
  /* Wizard Step Headings - Mobile */
  .wizard-step h4 {
    font-size: 18px !important;
    margin-bottom: 20px !important;
  }
  
  /* Form Groups - Mobile */
  .form-group {
    margin-bottom: 20px;
  }
  
  .form-group label {
    font-size: 14px;
    margin-bottom: 8px;
    font-weight: 500;
  }
  
  .form-control {
    font-size: 14px;
    padding: 10px 12px;
  }
  
  /* Select Dropdowns - Mobile */
  select.form-control {
    font-size: 16px !important; /* Prevents zoom on iOS and ensures visibility */
    padding: 12px 40px 12px 12px !important;
    height: auto !important;
    min-height: 48px !important; /* Touch-friendly size */
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    background-color: #ffffff !important;
    border: 2px solid #ced4da !important;
    border-radius: 4px !important;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 16 16'%3E%3Cpath fill='%23333' d='M8 11L3 6h10z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 12px center;
    background-size: 16px;
    color: #495057 !important;
    font-weight: 400;
    cursor: pointer;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
  }
  
  select.form-control:focus {
    border-color: #940000 !important;
    outline: 0;
    box-shadow: 0 0 0 0.2rem rgba(231, 122, 58, 0.25) !important;
  }
  
  select.form-control option {
    padding: 10px;
    font-size: 16px;
  }
  
  .form-control::placeholder {
    font-size: 13px;
  }
  
  .form-text {
    font-size: 12px;
    margin-top: 5px;
  }
  
  /* Columns - Stack on Mobile */
  .row .col-md-6,
  .row .col-md-12 {
    margin-bottom: 0;
  }
  
  /* Button Groups - Mobile */
  .btn-group-toggle {
    flex-direction: column;
  }
  
  .btn-group-toggle .btn {
    width: 100% !important;
    margin-bottom: 10px;
    border-radius: 4px !important;
  }
  
  .btn-group-toggle .btn:last-child {
    margin-bottom: 0;
  }
  
  /* Navigation Buttons - Mobile */
  .wizard-navigation {
    display: flex;
    flex-direction: column-reverse;
    gap: 10px;
    margin-top: 20px;
    padding-top: 15px;
  }
  
  .wizard-navigation .btn {
    width: 100%;
    margin: 0 !important;
    padding: 12px 20px;
    font-size: 15px;
  }
  
  .wizard-navigation .float-right,
  .wizard-navigation .float-left {
    float: none !important;
  }
  
  /* Bulk Creation Card - Mobile */
  .card-header {
    padding: 12px 15px;
    font-size: 14px;
  }
  
  .card-header .float-right {
    float: none !important;
    display: block;
    margin-top: 5px;
    font-size: 12px;
  }
  
  .card-body {
    padding: 15px;
  }
  
  /* Checkbox Inline - Mobile */
  .checkbox-inline {
    padding: 12px;
    font-size: 14px;
  }
  
  /* Upload Area - Mobile */
  .upload-area {
    padding: 20px 15px !important;
    max-width: 100% !important;
  }
  
  .upload-area i {
    font-size: 1.5rem !important;
  }
  
  .upload-area p {
    font-size: 13px !important;
    margin: 8px 0;
  }
  
  .upload-area .btn {
    padding: 8px 16px;
    font-size: 13px;
  }
  
  /* Image Preview Grid - Mobile */
  #imagePreview {
    grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)) !important;
    gap: 10px !important;
  }
  
  .image-preview-item img {
    height: 100px !important;
  }
  
  .image-preview-item .remove-btn {
    width: 24px !important;
    height: 24px !important;
    font-size: 11px !important;
  }
  
  /* Preview Section - Mobile */
  .preview-container {
    padding: 15px !important;
  }
  
  .preview-section {
    padding: 15px !important;
    margin-bottom: 15px;
  }
  
  .preview-section h5 {
    font-size: 16px !important;
    margin-bottom: 12px !important;
  }
  
  .preview-section .table {
    font-size: 13px;
  }
  
  .preview-section .table th {
    width: 40% !important;
    padding: 8px !important;
    font-size: 12px;
    white-space: normal;
    word-break: break-word;
  }
  
  .preview-section .table td {
    padding: 8px !important;
    font-size: 12px;
    word-break: break-word;
  }
  
  .amenities-list {
    gap: 8px;
  }
  
  .amenities-list .badge {
    padding: 6px 10px;
    font-size: 12px;
  }
  
  /* Input Groups - Mobile */
  .input-group {
    flex-wrap: wrap;
  }
  
  .input-group-prepend {
    width: 100%;
    margin-bottom: 8px;
  }
  
  .input-group-prepend .input-group-text {
    width: 100%;
    justify-content: center;
    border-radius: 4px 4px 0 0;
  }
  
  .input-group .form-control {
    border-radius: 0 0 4px 4px;
  }
  
  /* Alert Boxes - Mobile */
  .alert {
    padding: 12px 15px !important;
    font-size: 13px;
  }
  
  /* Check All/Uncheck All Buttons - Mobile */
  .mb-3 .btn-sm {
    width: 100%;
    margin-bottom: 10px;
  }
  
  .mb-3 .btn-sm:last-child {
    margin-bottom: 0;
  }
}

/* Very Small Mobile Devices */
@media (max-width: 480px) {
  /* Wizard Steps - Very Small */
  .wizard-steps {
    padding: 10px 0;
    margin-bottom: 15px;
  }
  
  .step-item {
    min-width: 50px;
    margin: 0 3px;
  }
  
  .step-number {
    width: 35px !important;
    height: 35px !important;
    font-size: 14px !important;
  }
  
  .step-label {
    font-size: 10px !important;
  }
  
  /* Tile - Very Small */
  .tile {
    padding: 12px !important;
  }
  
  .tile-title {
    font-size: 16px !important;
  }
  
  /* Form Controls - Very Small */
  .form-control {
    font-size: 16px; /* Prevents zoom on iOS */
    padding: 12px;
  }
  
  /* Select Dropdowns - Very Small Mobile */
  select.form-control {
    font-size: 16px !important;
    padding: 14px 45px 14px 14px !important;
    min-height: 50px !important;
    background-position: right 14px center;
    background-size: 18px;
  }
  
  /* Buttons - Very Small */
  .wizard-navigation .btn {
    padding: 14px 20px;
    font-size: 16px;
  }
  
  /* Preview Tables - Very Small */
  .preview-section .table {
    font-size: 12px;
  }
  
  .preview-section .table th,
  .preview-section .table td {
    padding: 6px !important;
    font-size: 11px;
    display: block;
    width: 100% !important;
    text-align: left;
  }
  
  .preview-section .table th {
    background-color: #f8f9fa;
    font-weight: 600;
    border-bottom: none;
    padding-bottom: 5px !important;
  }
  
  .preview-section .table td {
    border-top: none;
    padding-top: 5px !important;
    padding-bottom: 10px !important;
    border-bottom: 1px solid #dee2e6;
  }
  
  .preview-section .table tr:last-child td {
    border-bottom: none;
  }
}
</style>

<script>
let currentStep = 1;
const totalSteps = 5;
// Exchange rate logic removed as only TZS is used exclusively


function updateStepIndicator() {
  const stepItems = document.querySelectorAll('.step-item');
  stepItems.forEach((item, index) => {
    const step = parseInt(item.getAttribute('data-step'));
    item.classList.remove('active', 'completed');
    
    if (step < currentStep) {
      item.classList.add('completed');
    } else if (step === currentStep) {
      item.classList.add('active');
    }
  });
  
  // Update arrow visibility
  document.querySelectorAll('.step-arrow').forEach((arrow, index) => {
    const stepAfterArrow = index + 1;
    if (stepAfterArrow <= currentStep) {
      arrow.style.opacity = '1';
    } else {
      arrow.style.opacity = '0.3';
    }
  });
}

function changeStep(direction) {
  if (direction > 0) {
    // Validate current step before moving forward
    if (!validateStep(currentStep)) {
      return;
    }
    
    if (currentStep < totalSteps) {
      document.querySelector(`.wizard-step[data-step="${currentStep}"]`).style.display = 'none';
      currentStep++;
      document.querySelector(`.wizard-step[data-step="${currentStep}"]`).style.display = 'block';
      updateStepIndicator();
      updateButtons();
    }
  } else {
    if (currentStep > 1) {
      document.querySelector(`.wizard-step[data-step="${currentStep}"]`).style.display = 'none';
      currentStep--;
      document.querySelector(`.wizard-step[data-step="${currentStep}"]`).style.display = 'block';
      updateStepIndicator();
      updateButtons();
    }
  }
}

function updateButtons() {
  // Buttons are now inside each step, so we don't need to manage them here
  if (currentStep === totalSteps) {
    updatePreview();
  }
}



// Update preview with all form data
function updatePreview() {
  // Basic Info
  const roomNumber = document.getElementById('room_number').value.trim();
  document.getElementById('preview_room_number').textContent = roomNumber || '-';
  
  const roomType = document.getElementById('room_type').value;
  document.getElementById('preview_room_type').textContent = roomType || '-';
  
  const capacity = document.getElementById('capacity').value;
  document.getElementById('preview_capacity').textContent = capacity ? capacity + ' guest(s)' : '-';
  
  const bedType = document.getElementById('bed_type').value;
  document.getElementById('preview_bed_type').textContent = bedType || '-';
  
  const description = document.getElementById('description').value.trim();
  document.getElementById('preview_description').textContent = description || '-';

  // Pricing
  const pricePerNight = document.getElementById('price_per_night').value;
  if (pricePerNight && parseFloat(pricePerNight) > 0) {
    document.getElementById('preview_price_per_night').innerHTML = 
      `TZS ${parseFloat(pricePerNight).toLocaleString()}`;
  } else {
    document.getElementById('preview_price_per_night').textContent = '-';
  }

  // Amenities
  const amenities = Array.from(document.querySelectorAll('input[name="amenities[]"]:checked')).map(cb => cb.value);
  const amenitiesContainer = document.getElementById('preview_amenities');
  amenitiesContainer.innerHTML = '';
  if (amenities.length > 0) {
    amenities.forEach(amenity => {
      const badge = document.createElement('span');
      badge.className = 'badge';
      badge.style.marginRight = '5px';
      badge.style.marginBottom = '5px';
      badge.textContent = amenity;
      amenitiesContainer.appendChild(badge);
    });
  } else {
    amenitiesContainer.innerHTML = '<span class="text-muted">No amenities selected</span>';
  }

  // Check-in/Check-out Times
  const checkinTime = document.getElementById('checkin_time').value;
  if (checkinTime) {
    const time = checkinTime.split(':');
    const hours = parseInt(time[0]);
    const minutes = time[1];
    const ampm = hours >= 12 ? 'PM' : 'AM';
    const displayHours = hours % 12 || 12;
    document.getElementById('preview_checkin_time').textContent = `${displayHours}:${minutes} ${ampm}`;
  } else {
    document.getElementById('preview_checkin_time').textContent = '-';
  }

  const checkoutTime = document.getElementById('checkout_time').value;
  if (checkoutTime) {
    const time = checkoutTime.split(':');
    const hours = parseInt(time[0]);
    const minutes = time[1];
    const ampm = hours >= 12 ? 'PM' : 'AM';
    const displayHours = hours % 12 || 12;
    document.getElementById('preview_checkout_time').textContent = `${displayHours}:${minutes} ${ampm}`;
  } else {
    document.getElementById('preview_checkout_time').textContent = '-';
  }

  // Special Notes
  const specialNotes = document.getElementById('special_notes').value.trim();
  document.getElementById('preview_special_notes').textContent = specialNotes || '-';

  // Status (removed - no longer needed)

  // Images
  const imagePreview = document.getElementById('preview_images');
  if (uploadedFiles && uploadedFiles.length > 0) {
    const container = document.createElement('div');
    container.style.maxWidth = '500px';
    container.style.margin = '0 auto';
    
    const heading = document.createElement('h6');
    heading.style.marginBottom = '15px';
    heading.style.color = '#940000';
    heading.textContent = 'Uploaded Images (' + uploadedFiles.length + '):';
    container.appendChild(heading);
    
    const grid = document.createElement('div');
    grid.style.display = 'grid';
    grid.style.gridTemplateColumns = 'repeat(auto-fill, minmax(100px, 1fr))';
    grid.style.gap = '10px';
    grid.style.maxWidth = '100%';
    
    uploadedFiles.forEach((fileData, index) => {
      const previewImg = document.createElement('img');
      previewImg.src = fileData.data;
      previewImg.alt = fileData.name;
      previewImg.style.width = '100%';
      previewImg.style.height = '100px';
      previewImg.style.objectFit = 'cover';
      previewImg.style.borderRadius = '6px';
      previewImg.style.border = '2px solid #e0e0e0';
      previewImg.style.boxShadow = '0 2px 4px rgba(0,0,0,0.1)';
      grid.appendChild(previewImg);
    });
    
    container.appendChild(grid);
    imagePreview.innerHTML = '';
    imagePreview.appendChild(container);
  } else {
    imagePreview.innerHTML = '<span class="text-muted">No images uploaded</span>';
  }
}

function validateStep(step) {
  const stepElement = document.querySelector(`.wizard-step[data-step="${step}"]`);
  const requiredFields = stepElement.querySelectorAll('[required]');
  let isValid = true;
  
  requiredFields.forEach(field => {
    if (!field.value.trim()) {
      field.classList.add('is-invalid');
      isValid = false;
    } else {
      field.classList.remove('is-invalid');
    }
  });
  
  if (!isValid) {
    swal({
      title: "Validation Error",
      text: "Please fill in all required fields",
      type: "error",
      confirmButtonColor: "#940000"
    }, function() {
      // Focus on first invalid field
      const firstInvalid = stepElement.querySelector('.is-invalid');
      if (firstInvalid) {
        firstInvalid.focus();
        firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
    });
  }
  
  return isValid;
}

// Professional image upload with drag & drop
let uploadedFiles = [];

function setupImageUpload() {
  const fileInput = document.getElementById('room_images');
  const uploadArea = document.getElementById('uploadArea');
  const preview = document.getElementById('imagePreview');
  const imageCount = document.getElementById('imageCount');

  // Click to upload
  uploadArea.addEventListener('click', () => {
    fileInput.click();
  });

  // Drag and drop
  uploadArea.addEventListener('dragover', (e) => {
    e.preventDefault();
    uploadArea.classList.add('dragover');
  });

  uploadArea.addEventListener('dragleave', () => {
    uploadArea.classList.remove('dragover');
  });

  uploadArea.addEventListener('drop', (e) => {
    e.preventDefault();
    uploadArea.classList.remove('dragover');
    const files = Array.from(e.dataTransfer.files);
    handleFiles(files);
  });

  // File input change
  fileInput.addEventListener('change', (e) => {
    const files = Array.from(e.target.files);
    handleFiles(files);
  });

  function handleFiles(files) {
    files.forEach(file => {
      if (file.type.startsWith('image/')) {
        // Check file size (5MB max)
        if (file.size > 5 * 1024 * 1024) {
          swal({
            title: "File too large",
            text: `${file.name} is larger than 5MB. Please choose a smaller file.`,
            type: "error",
            confirmButtonColor: "#940000"
          });
          return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
          const fileData = {
            name: file.name,
            size: file.size,
            data: e.target.result,
            file: file
          };
          uploadedFiles.push(fileData);
          displayImage(fileData, uploadedFiles.length - 1);
          updateImageCount();
        };
        reader.readAsDataURL(file);
      }
    });
  }

  function displayImage(fileData, index) {
    const item = document.createElement('div');
    item.className = 'image-preview-item';
    item.innerHTML = `
      <img src="${fileData.data}" alt="${fileData.name}">
      <button type="button" class="remove-btn" onclick="removeImage(${index})" title="Remove image">
        <i class="fa fa-times"></i>
      </button>
      <div class="image-info">${fileData.name}</div>
    `;
    preview.appendChild(item);
  }

  function updateImageCount() {
    const count = uploadedFiles.length;
    if (count > 0) {
      imageCount.textContent = `${count} image${count > 1 ? 's' : ''} selected`;
      imageCount.style.color = '#940000';
      imageCount.style.fontWeight = '500';
    } else {
      imageCount.textContent = '';
    }
  }

  window.removeImage = function(index) {
    uploadedFiles.splice(index, 1);
    preview.innerHTML = '';
    uploadedFiles.forEach((fileData, idx) => {
      displayImage(fileData, idx);
    });
    updateImageCount();
    
    // Update file input
    const dataTransfer = new DataTransfer();
    uploadedFiles.forEach(fileData => {
      dataTransfer.items.add(fileData.file);
    });
    fileInput.files = dataTransfer.files;
  };
}

// Initialize image upload when page loads and populate form if editing
document.addEventListener('DOMContentLoaded', function() {
  setupImageUpload();
  
  // ============================================
  // BULK ROOM CREATION LOGIC
  // ============================================
  @if(!isset($room))
  const roomTypeSelect = document.getElementById('room_type');
  const bulkCreationSection = document.getElementById('bulk_creation_section');
  const enableBulkCreate = document.getElementById('enable_bulk_create');
  const quantitySection = document.getElementById('quantity_section');
  const bulkQuantity = document.getElementById('bulk_quantity');
  const assignmentMethodSection = document.getElementById('assignment_method_section');
  const autoGenerateSection = document.getElementById('auto_generate_section');
  const manualAssignSection = document.getElementById('manual_assign_section');
  const startingRoomNumber = document.getElementById('starting_room_number');
  const manualRoomNumbers = document.getElementById('manual_room_numbers');
  const autoPreview = document.getElementById('auto_preview');
  const manualPreview = document.getElementById('manual_preview');
  const roomNumberField = document.getElementById('room_number');
  const autoGenerateRadio = document.getElementById('auto_generate');
  const manualAssignRadio = document.getElementById('manual_assign');

  // Get room type display name
  function getRoomTypeDisplayName(roomType) {
    const typeNames = {
      'Single': 'Single Room',
      'Double': 'Double Room',
      'Twins': 'Standard Twin Room'
    };
    return typeNames[roomType] || 'Room';
  }

  // Update bulk creation labels based on room type
  function updateBulkCreationLabels() {
    const roomType = roomTypeSelect.value;
    if (!roomType) {
      bulkCreationSection.style.display = 'none';
      return;
    }
    
    const displayName = getRoomTypeDisplayName(roomType);
    const pluralName = displayName + 's';
    
    // Update labels
    const bulkRoomTypeLabel = document.getElementById('bulk_room_type_label');
    const bulkCreateLabel = document.getElementById('bulk_create_label');
    const bulkQuantityLabel = document.getElementById('bulk_quantity_label');
    
    if (bulkRoomTypeLabel) bulkRoomTypeLabel.textContent = 'Create multiple ' + pluralName.toLowerCase() + ' at once';
    if (bulkCreateLabel) bulkCreateLabel.textContent = 'Create Multiple ' + pluralName;
    if (bulkQuantityLabel) bulkQuantityLabel.innerHTML = 'How many ' + pluralName + '? <span class="text-danger">*</span>';
  }

  // Show/hide bulk creation section based on room type
  function toggleBulkCreationSection() {
    if (!roomTypeSelect || !bulkCreationSection) {
      console.log('Bulk creation elements not found:', {
        roomTypeSelect: !!roomTypeSelect,
        bulkCreationSection: !!bulkCreationSection
      });
      return;
    }
    
    const selectedRoomType = roomTypeSelect.value;
    if (selectedRoomType) {
      bulkCreationSection.style.display = 'block';
      updateBulkCreationLabels();
    } else {
      bulkCreationSection.style.display = 'none';
      // Reset bulk creation when no room type selected
      if (enableBulkCreate) enableBulkCreate.checked = false;
      resetBulkCreationFields();
    }
  }

  // Reset all bulk creation fields
  function resetBulkCreationFields() {
    if (enableBulkCreate) enableBulkCreate.checked = false;
    if (quantitySection) quantitySection.style.display = 'none';
    if (assignmentMethodSection) assignmentMethodSection.style.display = 'none';
    if (autoGenerateSection) autoGenerateSection.style.display = 'none';
    if (manualAssignSection) manualAssignSection.style.display = 'none';
    if (bulkQuantity) {
      bulkQuantity.value = '2';
      bulkQuantity.required = false;
    }
    if (startingRoomNumber) {
      startingRoomNumber.value = '';
      startingRoomNumber.required = false;
    }
    if (manualRoomNumbers) {
      manualRoomNumbers.value = '';
      manualRoomNumbers.required = false;
    }
    if (roomNumberField) {
      roomNumberField.required = true;
      roomNumberField.disabled = false;
    }
    
    // Update help text
    const roomNumberHelpText = document.getElementById('room_number_help_text');
    if (roomNumberHelpText) {
      roomNumberHelpText.textContent = 'Enter a unique room number or name';
    }
    updatePreviews();
  }

  // Toggle bulk creation checkbox
  if (enableBulkCreate) {
    enableBulkCreate.addEventListener('change', function() {
      if (this.checked) {
        quantitySection.style.display = 'block';
        bulkQuantity.required = true;
        roomNumberField.required = false;
        roomNumberField.disabled = true;
        roomNumberField.value = '';
        
        // Update help text
        const roomNumberHelpText = document.getElementById('room_number_help_text');
        if (roomNumberHelpText) {
          roomNumberHelpText.textContent = 'Room numbers will be specified in bulk creation fields below';
        }
        
        checkQuantityAndShowMethod();
      } else {
        resetBulkCreationFields();
      }
    });
  }

  // Check quantity and show assignment method section
  function checkQuantityAndShowMethod() {
    const quantity = parseInt(bulkQuantity.value) || 0;
    if (quantity >= 2 && enableBulkCreate.checked) {
      assignmentMethodSection.style.display = 'block';
      // Wait a bit for Bootstrap to process the button toggle, then update
      setTimeout(function() {
        updateAssignmentMethod();
      }, 50);
    } else {
      assignmentMethodSection.style.display = 'none';
      autoGenerateSection.style.display = 'none';
      manualAssignSection.style.display = 'none';
    }
  }

  // Update assignment method sections
  function updateAssignmentMethod() {
    console.log('Updating assignment method:', {
      autoChecked: autoGenerateRadio ? autoGenerateRadio.checked : false,
      manualChecked: manualAssignRadio ? manualAssignRadio.checked : false
    });
    
    if (autoGenerateRadio && autoGenerateRadio.checked) {
      console.log('Showing auto-generate section');
      if (autoGenerateSection) {
        autoGenerateSection.style.display = 'block';
      }
      if (manualAssignSection) {
        manualAssignSection.style.display = 'none';
      }
      if (startingRoomNumber) {
        startingRoomNumber.required = true;
      }
      if (manualRoomNumbers) {
        manualRoomNumbers.required = false;
        manualRoomNumbers.value = '';
      }
      updateAutoPreview();
    } else if (manualAssignRadio && manualAssignRadio.checked) {
      console.log('Showing manual assignment section');
      if (autoGenerateSection) {
        autoGenerateSection.style.display = 'none';
      }
      if (manualAssignSection) {
        manualAssignSection.style.display = 'block';
      }
      if (startingRoomNumber) {
        startingRoomNumber.required = false;
        startingRoomNumber.value = '';
      }
      if (manualRoomNumbers) {
        manualRoomNumbers.required = true;
      }
      updateManualPreview();
    }
  }
  
  // Initialize: Set manual as default and show it
  if (manualAssignRadio && autoGenerateRadio) {
    // Set manual as default
    manualAssignRadio.checked = true;
    autoGenerateRadio.checked = false;
    
    if (manualAssignSection) {
      manualAssignSection.style.display = 'block';
      if (manualRoomNumbers) manualRoomNumbers.required = true;
    }
    if (autoGenerateSection) {
      autoGenerateSection.style.display = 'none';
      if (startingRoomNumber) startingRoomNumber.required = false;
    }
  }

  // Update auto-generate preview
  function updateAutoPreview() {
    const startNum = startingRoomNumber.value.trim();
    const quantity = parseInt(bulkQuantity.value) || 0;
    
    if (!startNum || quantity < 2) {
      autoPreview.innerHTML = '<small>Enter starting room number to see preview</small>';
      return;
    }

    // Extract numeric part and prefix
    const match = startNum.match(/^([^0-9]*)(\d+)$/);
    if (!match) {
      autoPreview.innerHTML = '<small class="text-danger">Invalid room number format</small>';
      return;
    }

    const prefix = match[1];
    const startNumInt = parseInt(match[2]);
    const existingRooms = @json(\App\Models\Room::pluck('room_number')->toArray());
    
    let previewNumbers = [];
    let currentNum = startNumInt;
    let skipped = 0;
    
    while (previewNumbers.length < quantity && skipped < 100) {
      const roomNum = prefix + currentNum;
      if (!existingRooms.includes(roomNum)) {
        previewNumbers.push(roomNum);
      }
      currentNum++;
      skipped++;
    }

    if (previewNumbers.length < quantity) {
      autoPreview.innerHTML = '<small class="text-warning">Could only generate ' + previewNumbers.length + ' unique room numbers. Some may already exist.</small>';
    } else {
      autoPreview.innerHTML = '<strong>Preview:</strong><br><small>' + previewNumbers.join(', ') + '</small>';
    }
  }

  // Update manual assignment preview
  function updateManualPreview() {
    const input = manualRoomNumbers.value.trim();
    const quantity = parseInt(bulkQuantity.value) || 0;
    
    if (!input) {
      manualPreview.innerHTML = '<small>Enter room numbers separated by commas (e.g., 100, 204, 4046)</small>';
      return;
    }

    // Check if input contains invalid separators (dots, semicolons, etc.)
    if (input.includes('.') || input.includes(';') || input.includes('|')) {
      manualPreview.innerHTML = '<small class="text-danger"><strong>Error:</strong> Please use commas (,) to separate room numbers. Example: 100, 204, 4046</small>';
      return;
    }

    // Only split by comma
    const inputNumbers = input.split(',').map(n => n.trim()).filter(n => n);
    const existingRooms = @json(\App\Models\Room::pluck('room_number')->toArray());
    
    let validNumbers = [];
    let duplicateNumbers = [];
    let emptyNumbers = [];
    
    inputNumbers.forEach(num => {
      if (num.length === 0) {
        emptyNumbers.push(num);
        return;
      }
      if (existingRooms.includes(num)) {
        duplicateNumbers.push(num);
      } else if (validNumbers.includes(num)) {
        duplicateNumbers.push(num);
      } else {
        validNumbers.push(num);
      }
    });

    let previewHtml = '';
    
    // Check if exact quantity is provided
    if (inputNumbers.length > quantity) {
      previewHtml += '<small class="text-danger"><strong>Error:</strong> You entered ' + inputNumbers.length + ' room numbers, but only need ' + quantity + '. Please remove ' + (inputNumbers.length - quantity) + ' number(s).</small><br>';
    }
    
    if (validNumbers.length > 0) {
      const statusClass = validNumbers.length === quantity ? 'text-success' : 'text-info';
      previewHtml += '<strong>Valid (' + validNumbers.length + '/' + quantity + '):</strong><br><small class="' + statusClass + '">' + validNumbers.join(', ') + '</small>';
    }
    
    if (duplicateNumbers.length > 0) {
      previewHtml += '<br><strong>Already Exist (will be skipped):</strong><br><small class="text-warning">' + duplicateNumbers.join(', ') + '</small>';
    }
    
    if (validNumbers.length < quantity) {
      previewHtml += '<br><small class="text-danger">Need ' + (quantity - validNumbers.length) + ' more valid room number(s)</small>';
    } else if (validNumbers.length === quantity && inputNumbers.length === quantity) {
      previewHtml += '<br><small class="text-success"><strong>✓ Ready to create ' + quantity + ' room(s)</strong></small>';
    }
    
    manualPreview.innerHTML = previewHtml || '<small>Enter room numbers separated by commas (e.g., 100, 204, 4046)</small>';
  }

  // Update all previews
  function updatePreviews() {
    updateAutoPreview();
    updateManualPreview();
  }

  // Event listeners
  if (roomTypeSelect && bulkCreationSection) {
    // Show/hide on room type change
    roomTypeSelect.addEventListener('change', function() {
      console.log('Room type changed to:', this.value);
      toggleBulkCreationSection();
      updateBulkCreationLabels();
    });
    
    // Check on page load - wait a bit for DOM to be ready
    setTimeout(function() {
      console.log('Initializing bulk creation section');
      toggleBulkCreationSection();
      updateBulkCreationLabels();
    }, 200);
  } else {
    console.error('Bulk creation elements not found on page load');
  }

  if (bulkQuantity) {
    bulkQuantity.addEventListener('input', function() {
      checkQuantityAndShowMethod();
      updatePreviews();
    });
  }

  if (startingRoomNumber) {
    startingRoomNumber.addEventListener('input', updateAutoPreview);
  }

  if (manualRoomNumbers) {
    manualRoomNumbers.addEventListener('input', function() {
      // Validate input format - only allow commas as separators
      let value = this.value;
      
      // Replace common invalid separators with commas (for user convenience)
      if (value.includes('.')) {
        value = value.replace(/\./g, ',');
        this.value = value;
      }
      if (value.includes(';')) {
        value = value.replace(/;/g, ',');
        this.value = value;
      }
      if (value.includes('|')) {
        value = value.replace(/\|/g, ',');
        this.value = value;
      }
      
      updateManualPreview();
    });
    
    // Also validate on blur
    manualRoomNumbers.addEventListener('blur', function() {
      const input = this.value.trim();
      const quantity = parseInt(bulkQuantity.value) || 0;
      
      if (input && quantity > 0) {
        const inputNumbers = input.split(',').map(n => n.trim()).filter(n => n);
        if (inputNumbers.length !== quantity) {
          alert('Please enter exactly ' + quantity + ' room number(s) separated by commas.\n\nExample: 100, 204, 4046');
          this.focus();
        }
      }
    });
  }

  // Event listeners for assignment method radio buttons
  if (autoGenerateRadio) {
    autoGenerateRadio.addEventListener('change', function() {
      console.log('Auto-generate selected');
      updateAssignmentMethod();
    });
    
    // Also listen on the parent label for Bootstrap button toggle
    const autoGenerateLabel = autoGenerateRadio.closest('label');
    if (autoGenerateLabel) {
      autoGenerateLabel.addEventListener('click', function() {
        setTimeout(function() {
          updateAssignmentMethod();
        }, 10);
      });
    }
  }

  if (manualAssignRadio) {
    manualAssignRadio.addEventListener('change', function() {
      console.log('Manual assignment selected');
      updateAssignmentMethod();
    });
    
    // Also listen on the parent label for Bootstrap button toggle
    const manualAssignLabel = manualAssignRadio.closest('label');
    if (manualAssignLabel) {
      manualAssignLabel.addEventListener('click', function() {
        setTimeout(function() {
          updateAssignmentMethod();
        }, 10);
      });
    }
  }
  @endif
  
  // Populate form fields if editing
  @if(isset($room))
    const room = @json($room);
    
    // Populate basic fields
    if (room.extra_guest_fee) {
      document.getElementById('extra_guest_fee').value = room.extra_guest_fee;
    }
    if (room.sku_code) {
      document.getElementById('sku_code').value = room.sku_code;
    }
    if (room.discount_percentage) {
      document.getElementById('discount_percentage').value = room.discount_percentage;
    }
    if (room.promo_code) {
      document.getElementById('promo_code').value = room.promo_code;
    }
    if (room.bathroom_type) {
      document.getElementById('bathroom_type').value = room.bathroom_type;
    }
    if (room.checkin_time) {
      // Format time to H:i format (remove seconds if present)
      const checkinTime = room.checkin_time.length > 5 ? room.checkin_time.substring(0, 5) : room.checkin_time;
      document.getElementById('checkin_time').value = checkinTime;
    }
    if (room.checkout_time) {
      // Format time to H:i format (remove seconds if present)
      const checkoutTime = room.checkout_time.length > 5 ? room.checkout_time.substring(0, 5) : room.checkout_time;
      document.getElementById('checkout_time').value = checkoutTime;
    }
    if (room.special_notes) {
      document.getElementById('special_notes').value = room.special_notes;
    }
    if (room.wifi_password) {
      document.getElementById('wifi_password').value = room.wifi_password;
    }
    if (room.wifi_network_name) {
      document.getElementById('wifi_network_name').value = room.wifi_network_name;
    }
    if (room.status) {
      document.getElementById('room_status').value = room.status;
    }
    
    // Populate checkboxes
    if (room.pet_friendly) {
      document.getElementById('pet_friendly').checked = true;
    }
    if (room.smoking_allowed) {
      document.getElementById('smoking_allowed').checked = true;
    }
    
    // Populate amenities checkboxes
    if (room.amenities && Array.isArray(room.amenities)) {
      room.amenities.forEach(amenity => {
        const checkbox = document.querySelector(`input[name="amenities[]"][value="${amenity}"]`);
        if (checkbox) {
          checkbox.checked = true;
        }
      });
    }
    
    // Update price conversions
    updatePriceConversions();
  @endif
  
  // Setup amenities check all functionality
  const checkboxes = document.querySelectorAll('.amenity-checkbox');
  const checkAllBtn = document.getElementById('checkAllAmenities');
  const uncheckAllBtn = document.getElementById('uncheckAllAmenities');
  
  if (checkboxes.length > 0) {
    checkboxes.forEach(checkbox => {
      checkbox.addEventListener('change', function() {
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        const noneChecked = Array.from(checkboxes).every(cb => !cb.checked);
        
        if (allChecked) {
          checkAllBtn.style.display = 'none';
          uncheckAllBtn.style.display = 'inline-block';
        } else if (noneChecked) {
          checkAllBtn.style.display = 'inline-block';
          uncheckAllBtn.style.display = 'none';
        }
      });
    });
    
    // Initialize button state based on current checkbox states
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    if (allChecked) {
      checkAllBtn.style.display = 'none';
      uncheckAllBtn.style.display = 'inline-block';
    }
  }
});

// Form submission
document.getElementById('roomForm').addEventListener('submit', function(e) {
  e.preventDefault();
  
  if (!validateStep(currentStep)) {
    changeStep(0); // Go back to show validation errors
    return;
  }
  
  const form = this;
  
  swal({
    title: "Are you sure?",
    text: "{{ isset($room) ? 'Do you want to update this room?' : 'Do you want to add this room?' }}",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor: "#940000",
    cancelButtonColor: "#d33",
    confirmButtonText: "{{ isset($room) ? 'Yes, update it!' : 'Yes, add it!' }}",
    cancelButtonText: "Cancel",
    closeOnConfirm: false,
    showLoaderOnConfirm: true
  }, function(isConfirm) {
    if (isConfirm) {
      // Show loading spinner
      swal({
        title: "Processing...",
        text: "Please wait while we save the room data",
        type: "info",
        showConfirmButton: false,
        allowOutsideClick: false,
        allowEscapeKey: false,
        closeOnClickOutside: false
      });
      
      // Add loading spinner overlay
      const loadingSpinner = document.createElement('div');
      loadingSpinner.id = 'formLoadingSpinner';
      loadingSpinner.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 99999; display: flex; align-items: center; justify-content: center;';
      loadingSpinner.innerHTML = '<div style="background: white; padding: 30px; border-radius: 8px; text-align: center;"><i class="fa fa-spinner fa-spin fa-3x" style="color: #940000;"></i><p style="margin-top: 15px; font-size: 16px;">Saving room data...</p></div>';
      document.body.appendChild(loadingSpinner);

      // Create FormData object
      const formData = new FormData(form);
      
      // Add amenities as array
      const amenities = Array.from(document.querySelectorAll('input[name="amenities[]"]:checked')).map(cb => cb.value);
      amenities.forEach((amenity, index) => {
        formData.append(`amenities[${index}]`, amenity);
      });

      // Submit via AJAX
      fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || document.querySelector('input[name="_token"]').value
        }
      })
      .then(async response => {
        const data = await response.json();
        // Remove loading spinner
        const spinner = document.getElementById('formLoadingSpinner');
        if (spinner) spinner.remove();
        
        if (!response.ok || !data.success) {
          // Handle validation errors
          if (data.errors) {
            const errorMessages = Object.values(data.errors).flat().join('<br>');
            swal({
              title: "Validation Error!",
              html: true,
              text: errorMessages,
              type: "error",
              confirmButtonColor: "#940000"
            });
            return;
          }
          swal({
            title: "Error!",
            text: data.message || "Failed to create room. Please try again.",
            type: "error",
            confirmButtonColor: "#940000"
          });
          return;
        }
        
        // Success
        const successMessage = data.message || "{{ isset($room) ? 'Room has been updated successfully!' : 'Room has been added successfully!' }}";
        const roomCount = data.rooms ? data.rooms.length : 1;
        
        swal({
          title: "Success!",
          text: successMessage,
          type: "success",
          confirmButtonColor: "#940000",
          confirmButtonText: roomCount > 1 ? "View All Rooms" : "View Rooms List"
        }, function() {
          // Redirect to rooms list (same route for both super admin and manager)
          window.location.href = "{{ route('admin.rooms.index') }}";
        });
      })
      .catch(error => {
        console.error('Error:', error);
        // Remove loading spinner
        const spinner = document.getElementById('formLoadingSpinner');
        if (spinner) spinner.remove();
        
        swal({
          title: "Error!",
          html: true,
          text: error.message || "An error occurred while saving the room. Please try again.",
          type: "error",
          confirmButtonColor: "#940000"
        });
      });
    }
  });
});

// Toggle all amenities checkboxes
function toggleAllAmenities(checkAll = true) {
  const checkboxes = document.querySelectorAll('.amenity-checkbox');
  const checkAllBtn = document.getElementById('checkAllAmenities');
  const uncheckAllBtn = document.getElementById('uncheckAllAmenities');
  
  checkboxes.forEach(checkbox => {
    checkbox.checked = checkAll;
  });
  
  // Toggle button visibility
  if (checkAll) {
    checkAllBtn.style.display = 'none';
    uncheckAllBtn.style.display = 'inline-block';
  } else {
    checkAllBtn.style.display = 'inline-block';
    uncheckAllBtn.style.display = 'none';
  }
}

// Initialize
updateStepIndicator();
updateButtons();
</script>
@endsection

