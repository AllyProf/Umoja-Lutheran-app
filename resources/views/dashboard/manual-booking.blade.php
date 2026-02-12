@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-calendar-plus-o"></i> Create Individual Booking</h1>
    <p>Register booking from external platforms or walk-in guests</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ $role === 'reception' ? route('reception.dashboard') : route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ $role === 'reception' ? route('reception.bookings') : route('admin.bookings.index') }}">Bookings</a></li>
    <li class="breadcrumb-item"><a href="#">Individual Booking</a></li>
  </ul>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <!-- Wizard Steps Indicator -->
      <div class="wizard-steps mb-4">
        <div class="step-item active" data-step="1">
          <div class="step-number">1</div>
          <div class="step-label">Guest Info</div>
        </div>
        <div class="step-arrow"><i class="fa fa-arrow-right"></i></div>
        <div class="step-item" data-step="2">
          <div class="step-number">2</div>
          <div class="step-label">Room & Dates</div>
        </div>
        <div class="step-arrow"><i class="fa fa-arrow-right"></i></div>
        <div class="step-item" data-step="3">
          <div class="step-number">3</div>
          <div class="step-label">Payment</div>
        </div>
        <div class="step-arrow"><i class="fa fa-arrow-right"></i></div>
        <div class="step-item" data-step="4">
          <div class="step-number">4</div>
          <div class="step-label">Review</div>
        </div>
      </div>
      
      <form id="manualBookingForm" method="POST" action="{{ $role === 'reception' ? route('reception.bookings.manual.store') : route('admin.bookings.manual.store') }}">
        @csrf
        
        <!-- Step 1: Guest Information -->
        <div class="wizard-step" data-step="1">
          <h4 class="mb-4"><i class="fa fa-user"></i> Guest Information</h4>
          
          <!-- Returning Guest Search -->
          <div class="row mb-4">
            <div class="col-md-12">
              <div class="guest-search-box shadow-sm mb-2" style="background: white; border-radius: 12px; padding: 20px; border: 1px solid #e0e0e0; transition: all 0.3s ease;">
                <div class="search-header d-flex align-items-center mb-3">
                   <div class="search-icon-circle mr-3" style="width: 45px; height: 45px; background: #e0f2f1; color: #009688; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                      <i class="fa fa-user-circle-o"></i>
                   </div>
                   <div>
                      <h6 class="mb-0 font-weight-bold text-dark" style="text-transform: uppercase; letter-spacing: 0.5px;">RETURNING GUEST?</h6>
                      <small class="text-muted">Search by name, email or phone to auto-fill details from existing records</small>
                   </div>
                </div>
                <div class="form-group position-relative mb-0">
                  <div class="input-group search-input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text bg-white border-right-0" style="border-radius: 8px 0 0 8px; color: #009688;"><i class="fa fa-search" id="guestSearchIcon"></i></span>
                    </div>
                    <input type="text" id="guestSearchInput" class="form-control border-left-0" style="height: 48px; font-size: 16px; border-radius: 0 8px 8px 0;" placeholder="Start typing guest name, email or phone number..." autocomplete="off">
                  </div>
                  <div id="guestSearchResults" class="list-group position-absolute w-100 mt-2 shadow-lg" style="display: none; z-index: 1100; max-height: 300px; overflow-y: auto; border-radius: 10px; border: 1px solid #eee;">
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="guest_type">Guest Type <span class="text-danger">*</span></label>
              <select class="form-control" id="guest_type" name="guest_type" required>
                <option value="international">International Guest</option>
                <option value="tanzanian">Tanzanian Guest</option>
              </select>
              <small class="form-text text-muted" id="guest_type_hint"></small>
            </div>
          </div>
          <div class="col-md-6" id="nationality_field_wrapper">
            <div class="form-group">
              <label for="nationality">Nationality <span class="text-danger">*</span></label>
              <div class="nationality-select-wrapper">
                <span id="nationality_flag_prefix" class="nationality-flag-prefix" style="display: none;"></span>
                <select class="form-control" id="nationality" name="nationality" required style="padding-left: 40px;">
                  <option value="">Search and select nationality...</option>
                </select>
              </div>
              <input type="hidden" id="country_code" name="country_code">
              <input type="hidden" id="nationality_hidden" name="nationality_hidden" value="">
              <small class="form-text text-muted">Type to search countries</small>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="full_name">Full Name <span class="text-danger">*</span></label>
              <input class="form-control" type="text" id="full_name" name="full_name" placeholder="Guest full name" required>
              <small class="form-text text-muted">Password will be the first name in CAPITALS</small>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="guest_email">Email <span class="text-danger">*</span></label>
              <input class="form-control" type="email" id="guest_email" name="guest_email" placeholder="guest@example.com" required>
              <small class="form-text text-muted">This will be used as username for guest login</small>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="guest_phone">Phone Number <span class="text-danger">*</span></label>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text" id="phone_country_code" style="min-width: 60px; text-align: center;">+255</span>
                </div>
                <input class="form-control" type="text" id="guest_phone" name="guest_phone" placeholder="Phone number (without country code)" required>
              </div>
              <small class="form-text text-muted">Country code will be auto-filled based on nationality selection</small>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="room_type">Room Type <span class="text-danger">*</span></label>
              <select class="form-control" id="room_type" name="room_type" required>
                <option value="">Select Room Type</option>
                @foreach($roomTypes as $type)
                  <option value="{{ $type }}">{{ $type }}</option>
                @endforeach
              </select>
              <small class="form-text text-muted">Select room type to auto-fill number of guests</small>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="number_of_guests">Number of Guests <span class="text-danger">*</span></label>
              <input class="form-control" type="number" id="number_of_guests" name="number_of_guests" min="1" max="10" value="1" required>
              <small class="form-text text-muted">Auto-filled based on room type selection</small>
            </div>
          </div>
        </div>

          <!-- Navigation Buttons for Step 1 -->
          <div class="wizard-navigation mt-4 pt-3 border-top">
            <button type="button" class="btn btn-primary float-right" onclick="changeWizardStep(1)">
              Next <i class="fa fa-arrow-right"></i>
            </button>
          </div>
        </div>

        <!-- Step 2: Room & Dates -->
        <div class="wizard-step" data-step="2" style="display: none;">
          <h4 class="mb-4"><i class="fa fa-bed"></i> Room & Dates</h4>

          <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="check_in">Check-in Date <span class="text-danger">*</span></label>
              <input class="form-control" type="date" id="check_in" name="check_in" required>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="check_out">Check-out Date <span class="text-danger">*</span></label>
              <input class="form-control" type="date" id="check_out" name="check_out" required>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-3">
            <div class="form-group">
              <label for="check_in_time">Check-in Time <span class="text-danger">*</span></label>
              <input class="form-control" type="time" id="check_in_time" name="check_in_time" value="14:00" required>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label for="check_out_time">Check-out Time <span class="text-danger">*</span></label>
              <input class="form-control" type="time" id="check_out_time" name="check_out_time" value="10:00" required>
            </div>
          </div>
        </div>

        <!-- Available Rooms Cards Display -->
        <div class="row mt-3" id="available_rooms_container" style="display: none;">
          <div class="col-md-12">
            <div class="form-group">
              <label>Available Rooms <span class="text-danger">*</span></label>
              <small class="form-text text-muted d-block mb-3" id="room_select_hint">Please select room type, check-in and check-out dates to see available rooms</small>
              
              <!-- Availability Summary -->
              <div id="availability_summary" class="mb-4" style="display: none;">
                <div class="card" style="border-radius: 10px; border: 1px solid #e0e0e0; background-color: #fcfcfc;">
                  <div class="card-body p-3">
                    <h6 class="mb-3 text-muted" style="font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
                      <i class="fa fa-info-circle text-primary mr-1"></i> Availability Overview
                    </h6>
                    <div class="d-flex flex-wrap gap-2" id="summary_pills">
                      <!-- Summary pills will be dynamically inserted here -->
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- Loading State -->
              <div id="rooms_loading" style="display: none;">
                <div class="text-center py-4">
                  <i class="fa fa-spinner fa-spin fa-3x text-primary"></i>
                  <p class="mt-2">Loading available rooms...</p>
                </div>
              </div>
              
              <!-- Rooms Cards Grid -->
              <div id="rooms_cards_grid" class="row">
                <!-- Room cards will be dynamically inserted here -->
              </div>
              
              <!-- Hidden input for selected room -->
              <input type="hidden" id="room_id" name="room_id" required>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <label for="special_requests">Special Requests / Notes</label>
              <textarea class="form-control" id="special_requests" name="special_requests" rows="3" placeholder="Any special requests or notes about this booking"></textarea>
              <small class="form-text text-muted">Select departments below to notify them about these special requests</small>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <label class="d-flex align-items-center">
                <i class="fa fa-bell text-primary mr-2"></i>
                Notify Departments 
                <small class="text-muted ml-2">(Optional - Select departments to notify about special requests)</small>
              </label>
              
              <div class="alert alert-info mb-3" style="background-color: #e7f3ff; border-left: 4px solid #2196F3;">
                <i class="fa fa-info-circle"></i>
                <strong>How it works:</strong> When you select departments below, they will receive a notification with the guest's special requests/notes. This helps coordinate guest needs across different departments.
              </div>
              
              <div class="row">
                <div class="col-md-4 mb-3">
                  <div class="card department-card" style="border: 2px solid #e0e0e0; border-radius: 8px; transition: all 0.3s; cursor: pointer;" onclick="toggleDepartment('notify_reception')">
                    <div class="card-body text-center p-3">
                      <input class="form-check-input department-checkbox" type="checkbox" id="notify_reception" name="notify_departments[]" value="reception" style="position: absolute; top: 10px; right: 10px; transform: scale(1.3);" onchange="updateDepartmentCard(this)">
                      <div class="mb-2">
                        <i class="fa fa-users fa-3x text-primary"></i>
                      </div>
                      <h6 class="mb-1 font-weight-bold">Reception</h6>
                      <small class="text-muted d-block">Front desk & guest services</small>
                      <div class="mt-2">
                        <span class="badge badge-primary badge-sm" id="reception-badge" style="display: none;">Selected</span>
                      </div>
                    </div>
                  </div>
                </div>
                
                <div class="col-md-4 mb-3">
                  <div class="card department-card" style="border: 2px solid #e0e0e0; border-radius: 8px; transition: all 0.3s; cursor: pointer;" onclick="toggleDepartment('notify_bar')">
                    <div class="card-body text-center p-3">
                      <input class="form-check-input department-checkbox" type="checkbox" id="notify_bar" name="notify_departments[]" value="bar_keeper" style="position: absolute; top: 10px; right: 10px; transform: scale(1.3);" onchange="updateDepartmentCard(this)">
                      <div class="mb-2">
                        <i class="fa fa-glass fa-3x text-warning"></i>
                      </div>
                      <h6 class="mb-1 font-weight-bold">Bar & Drinks</h6>
                      <small class="text-muted d-block">Beverage services & bar</small>
                      <div class="mt-2">
                        <span class="badge badge-warning badge-sm" id="bar-badge" style="display: none;">Selected</span>
                      </div>
                    </div>
                  </div>
                </div>
                
                <div class="col-md-4 mb-3">
                  <div class="card department-card" style="border: 2px solid #e0e0e0; border-radius: 8px; transition: all 0.3s; cursor: pointer;" onclick="toggleDepartment('notify_kitchen')">
                    <div class="card-body text-center p-3">
                      <input class="form-check-input department-checkbox" type="checkbox" id="notify_kitchen" name="notify_departments[]" value="head_chef" style="position: absolute; top: 10px; right: 10px; transform: scale(1.3);" onchange="updateDepartmentCard(this)">
                      <div class="mb-2">
                        <i class="fa fa-cutlery fa-3x text-danger"></i>
                      </div>
                      <h6 class="mb-1 font-weight-bold">Kitchen & Food</h6>
                      <small class="text-muted d-block">Food preparation & dining</small>
                      <div class="mt-2">
                        <span class="badge badge-danger badge-sm" id="kitchen-badge" style="display: none;">Selected</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="mt-2">
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="selectAllDepartments()">
                  <i class="fa fa-check-square"></i> Select All
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary ml-2" onclick="deselectAllDepartments()">
                  <i class="fa fa-square"></i> Deselect All
                </button>
              </div>
              
              <div class="mt-3" id="department-preview" style="display: none;">
                <div class="alert alert-success">
                  <i class="fa fa-check-circle"></i>
                  <strong>Notification Preview:</strong>
                  <span id="preview-text">Selected departments will receive a notification with the guest's special requests.</span>
                </div>
              </div>
            </div>
          </div>
        </div>

          <!-- Navigation Buttons for Step 2 -->
          <div class="wizard-navigation mt-4 pt-3 border-top">
            <button type="button" class="btn btn-secondary" onclick="changeWizardStep(-1)">
              <i class="fa fa-arrow-left"></i> Previous
            </button>
            <button type="button" class="btn btn-primary float-right" onclick="changeWizardStep(1)">
              Next <i class="fa fa-arrow-right"></i>
            </button>
          </div>
        </div>

        <!-- Step 3: Payment Information -->
        <div class="wizard-step" data-step="3" style="display: none;">
          <h4 class="mb-4"><i class="fa fa-dollar"></i> Payment Information</h4>
        
        <div class="alert alert-info" id="calculationHint" style="display: none;">
          <i class="fa fa-info-circle"></i> <strong>Note:</strong> Please select a room and set check-in/check-out dates to see the recommended price.
        </div>



        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="recommended_price">Recommended Price</label>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text" id="recommended_currency">TZS</span>
                </div>
                <input class="form-control" type="number" id="recommended_price" placeholder="0" readonly style="background-color: #f8f9fa;">
              </div>
              <small class="form-text text-muted">Calculated from room price and number of nights</small>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="total_price">Total Price <span class="text-danger">*</span></label>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text" id="total_price_currency">TZS</span>
                </div>
                <input class="form-control" type="number" id="total_price" name="total_price" min="0" placeholder="0" required>
              </div>
              <small class="form-text text-muted">Enter total price (can be lower than recommended for discounts)</small>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="payment_method">Payment Method <span class="text-danger">*</span></label>
              <select class="form-control" id="payment_method" name="payment_method" required>
                <option value="">Select Payment Method</option>
                <option value="online">Online (Booking.com, etc.)</option>
                <option value="cash">Cash</option>
                <option value="bank">Bank Transfer</option>
                <option value="mobile">Mobile Payment</option>
                <option value="card">Card</option>
                <option value="other">Other</option>
              </select>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group" id="payment_provider_wrapper" style="display: none;">
              <label for="payment_provider">Payment Provider <span class="text-danger">*</span></label>
              <select class="form-control" id="payment_provider" name="payment_provider">
                <option value="">Select Provider</option>
                <!-- Options will be populated dynamically via JavaScript -->
              </select>
              <small class="form-text text-muted">Select the payment provider</small>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group" id="payment_reference_wrapper" style="display: none;">
              <label for="payment_reference">Reference Number <span class="text-danger">*</span></label>
              <input class="form-control" type="text" id="payment_reference" name="payment_reference" placeholder="Enter transaction/reference number">
              <small class="form-text text-muted">Enter the transaction or reference number for this payment</small>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="amount_paid">Amount Paid <span class="text-danger">*</span></label>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text" id="amount_paid_currency">TZS</span>
                </div>
                <input class="form-control" type="number" id="amount_paid" name="amount_paid" min="0" placeholder="0" required>
              </div>
              <small class="form-text text-muted">Enter the amount paid</small>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="payment_percentage">Payment Percentage</label>
              <div class="input-group">
                <input class="form-control" type="number" id="payment_percentage" name="payment_percentage" step="0.01" min="0" max="100" placeholder="0.00" readonly style="background-color: #f8f9fa;">
                <div class="input-group-append">
                  <span class="input-group-text">%</span>
                </div>
              </div>
              <small class="form-text text-muted">Calculated automatically based on amount paid</small>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="remaining_amount">Remaining Amount</label>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text" id="remaining_amount_currency">TZS</span>
                </div>
                <input class="form-control" type="number" id="remaining_amount" name="remaining_amount" min="0" placeholder="0" readonly>
              </div>
              <small class="form-text text-muted">Amount to be paid later</small>
            </div>
          </div>
        </div>

          <!-- Navigation Buttons for Step 3 -->
          <div class="wizard-navigation mt-4 pt-3 border-top">
            <button type="button" class="btn btn-secondary" onclick="changeWizardStep(-1)">
              <i class="fa fa-arrow-left"></i> Previous
            </button>
            <button type="button" class="btn btn-primary float-right" onclick="changeWizardStep(1)">
              Next <i class="fa fa-arrow-right"></i>
            </button>
          </div>
        </div>

        <!-- Step 4: Review & Submit -->
        <div class="wizard-step" data-step="4" style="display: none;">
          <h4 class="mb-4"><i class="fa fa-check-circle"></i> Review & Submit</h4>
          
          <div class="alert alert-info mb-4">
            <strong><i class="fa fa-info-circle"></i> Note:</strong> After creating this booking, the guest will receive an email and SMS with:
            <ul class="mb-0 mt-2">
              <li>Booking details (room, dates, payment information)</li>
              <li>Login credentials (Username: Email, Password: First Name from Full Name in CAPITALS)</li>
              <li>Payment percentage and remaining amount</li>
            </ul>
          </div>

          <!-- Review Summary -->
          <div class="review-summary">
            <div class="card mb-3">
              <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fa fa-user"></i> Guest Information</h5>
              </div>
              <div class="card-body">
                <table class="table table-sm mb-0">
                  <tr>
                    <td width="30%"><strong>Guest Type:</strong></td>
                    <td id="review_guest_type">-</td>
                  </tr>
                  <tr id="review_nationality_row" style="display: none;">
                    <td><strong>Nationality:</strong></td>
                    <td id="review_nationality">-</td>
                  </tr>
                  <tr>
                    <td><strong>Full Name:</strong></td>
                    <td id="review_full_name">-</td>
                  </tr>
                  <tr>
                    <td><strong>Email:</strong></td>
                    <td id="review_email">-</td>
                  </tr>
                  <tr>
                    <td><strong>Phone:</strong></td>
                    <td id="review_phone">-</td>
                  </tr>
                  <tr>
                    <td><strong>Number of Guests:</strong></td>
                    <td id="review_guests">-</td>
                  </tr>
                </table>
              </div>
            </div>

            <div class="card mb-3">
              <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fa fa-bed"></i> Room & Dates</h5>
              </div>
              <div class="card-body">
                <table class="table table-sm mb-0">
                  <tr>
                    <td width="30%"><strong>Room:</strong></td>
                    <td id="review_room">-</td>
                  </tr>
                  <tr>
                    <td><strong>Check-in:</strong></td>
                    <td id="review_checkin">-</td>
                  </tr>
                  <tr>
                    <td><strong>Check-out:</strong></td>
                    <td id="review_checkout">-</td>
                  </tr>
                  <tr id="review_special_requests_row" style="display: none;">
                    <td><strong>Special Requests:</strong></td>
                    <td id="review_special_requests">-</td>
                  </tr>
                  <tr id="review_notify_departments_row" style="display: none;">
                    <td><strong>Notify Departments:</strong></td>
                    <td id="review_notify_departments">-</td>
                  </tr>
                </table>
              </div>
            </div>

            <div class="card mb-3">
              <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fa fa-dollar"></i> Payment Information</h5>
              </div>
              <div class="card-body">
                <table class="table table-sm mb-0">

                  <tr>
                    <td><strong>Number of Nights:</strong></td>
                    <td id="review_nights">-</td>
                  </tr>
                  <tr>
                    <td><strong>Recommended Price:</strong></td>
                    <td id="review_recommended_price">-</td>
                  </tr>
                  <tr>
                    <td><strong>Total Price:</strong></td>
                    <td id="review_total_price">-</td>
                  </tr>
                  <tr id="review_discount_row" style="display: none;">
                    <td><strong>Discount:</strong></td>
                    <td id="review_discount" style="color: #28a745; font-weight: bold;">-</td>
                  </tr>
                  <tr>
                    <td><strong>Payment Method:</strong></td>
                    <td id="review_payment_method">-</td>
                  </tr>
                  <tr id="review_payment_provider_row" style="display: none;">
                    <td><strong>Payment Provider:</strong></td>
                    <td id="review_payment_provider">-</td>
                  </tr>
                  <tr id="review_payment_reference_row" style="display: none;">
                    <td><strong>Reference Number:</strong></td>
                    <td id="review_payment_reference">-</td>
                  </tr>
                  <tr>
                    <td><strong>Amount Paid:</strong></td>
                    <td id="review_amount_paid">-</td>
                  </tr>
                  <tr>
                    <td><strong>Payment Percentage:</strong></td>
                    <td id="review_payment_percentage">-</td>
                  </tr>
                  <tr>
                    <td><strong>Remaining Amount:</strong></td>
                    <td id="review_remaining">-</td>
                  </tr>
                </table>
              </div>
            </div>
          </div>

          <!-- Navigation Buttons for Step 4 -->
          <div class="wizard-navigation mt-4 pt-3 border-top">
            <button type="button" class="btn btn-secondary" onclick="changeWizardStep(-1)">
              <i class="fa fa-arrow-left"></i> Previous
            </button>
            <button class="btn btn-success float-right" type="submit">
              <i class="fa fa-check"></i> Create Booking & Send Notifications
            </button>
            <a class="btn btn-secondary mr-2" href="{{ $role === 'reception' ? route('reception.bookings') : route('admin.bookings.index') }}">
              <i class="fa fa-times"></i> Cancel
            </a>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script src="{{ asset('dashboard_assets/js/plugins/sweetalert.min.js') }}"></script>
<script src="{{ asset('dashboard_assets/js/plugins/select2.min.js') }}"></script>
<style>
/* Wizard Styles */
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
  min-height: 400px;
}

.wizard-navigation {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.wizard-navigation .btn {
  min-width: 120px;
}

.review-summary .card {
  border: 1px solid #dee2e6;
  border-radius: 4px;
}

.review-summary .card-header {
  padding: 12px 20px;
  border-bottom: 1px solid rgba(255,255,255,0.2);
}

.review-summary .table {
  margin-bottom: 0;
}

.review-summary .table td {
  padding: 8px 12px;
  border-top: 1px solid #dee2e6;
}

/* Nationality Styles */
.nationality-select-wrapper {
  position: relative;
}
.nationality-select-wrapper select {
  padding-left: 40px;
}
.nationality-flag-prefix {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 18px;
  pointer-events: none;
  z-index: 10;
}
/* Select2 Styles */
.select2-container {
  width: 100% !important;
}
.select2-container--default .select2-selection--single {
  height: 38px;
  border: 1px solid #ced4da;
  border-radius: 4px;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
  line-height: 36px;
  padding-left: 12px;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
  height: 36px;
}
.select2-dropdown {
  border: 1px solid #ced4da;
  border-radius: 4px;
}
.select2-results__option {
  padding: 8px 12px;
}
.select2-container--default .select2-results__option--highlighted[aria-selected] {
  background-color: #940000;
  color: white;
}
.select2-search--dropdown .select2-search__field {
  border: 1px solid #ced4da;
  border-radius: 4px;
  padding: 6px;
}

/* Department Card Styles */
.department-card {
  position: relative;
  min-height: 150px;
  transition: all 0.3s ease;
}

.department-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 6px 12px rgba(0,0,0,0.15) !important;
}

.department-card .card-body {
  position: relative;
}

.department-checkbox {
  cursor: pointer;
  z-index: 10;
}

.department-card h6 {
  font-size: 16px;
  margin-bottom: 5px;
}

.department-card small {
  font-size: 12px;
}

.badge-sm {
  font-size: 10px;
  padding: 4px 8px;
}

#department-preview {
  animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Room Card Styles */
.room-card {
  cursor: pointer;
  transition: all 0.3s ease;
  margin-bottom: 20px;
}

.room-card-inner {
  background: #fff;
  border: 2px solid #e0e0e0;
  border-radius: 12px;
  overflow: hidden;
  transition: all 0.3s ease;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.room-card:hover .room-card-inner {
  transform: translateY(-5px);
  box-shadow: 0 8px 20px rgba(0,0,0,0.15);
  border-color: #940000;
}

.room-card.selected .room-card-inner {
  border-color: #940000;
  border-width: 3px;
  box-shadow: 0 8px 25px rgba(231, 122, 58, 0.3);
  transform: translateY(-5px);
}

.room-image-container {
  position: relative;
  width: 100%;
  height: 140px;
  overflow: hidden;
  background: #f5f5f5;
}

.room-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.room-card:hover .room-image {
  transform: scale(1.05);
}

.room-availability-badge {
  position: absolute;
  top: 10px;
  right: 10px;
  color: white;
  padding: 5px 10px;
  border-radius: 15px;
  font-size: 11px;
  font-weight: 600;
  box-shadow: 0 2px 8px rgba(0,0,0,0.2);
  z-index: 2;
}

.room-availability-badge.available-now {
  background: #28a745;
}

.room-availability-badge.soon-available,
.room-availability-badge.cleaning-required {
  background: #17a2b8;
}

.room-availability-badge.occupied {
  background: #dc3545;
}

.room-card.unselectable {
  cursor: not-allowed;
  opacity: 0.7;
}

.room-card.unselectable:hover .room-card-inner {
  transform: none;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  border-color: #e0e0e0;
}

.room-card.unselectable .room-image-container::after {
  content: 'NOT SELECTABLE';
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%) rotate(-15deg);
  background: rgba(220, 53, 69, 0.9);
  color: white;
  padding: 5px 15px;
  font-weight: bold;
  font-size: 10px;
  border-radius: 4px;
  z-index: 4;
  pointer-events: none;
}

.room-availability-badge i {
  margin-right: 4px;
}

.room-selected-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(231, 122, 58, 0.85);
  color: white;
  display: none;
  align-items: center;
  justify-content: center;
  flex-direction: column;
  z-index: 3;
  animation: fadeIn 0.3s ease;
}

.room-card.selected .room-selected-overlay {
  display: flex;
}

.room-selected-overlay i {
  margin-bottom: 5px;
  animation: scaleIn 0.3s ease;
}

.room-selected-overlay p {
  font-size: 14px;
  font-weight: bold;
  margin: 0;
}

.room-card-body {
  padding: 15px;
}

.room-number {
  font-size: 18px;
  font-weight: bold;
  color: #333;
  margin-bottom: 8px;
  display: flex;
  align-items: center;
  gap: 6px;
}

.room-number i {
  color: #940000;
  font-size: 18px;
}

.room-type-badge {
  display: inline-block;
  background: linear-gradient(135deg, #940000 0%, #d66a2a 100%);
  color: white;
  padding: 6px 14px;
  border-radius: 20px;
  font-size: 13px;
  font-weight: 600;
  margin-bottom: 15px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.room-details {
  margin: 10px 0;
  padding: 10px 0;
  border-top: 1px solid #f0f0f0;
  border-bottom: 1px solid #f0f0f0;
}

.room-detail-item {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 8px;
  font-size: 14px;
  color: #666;
}

.room-detail-item:last-child {
  margin-bottom: 0;
}

.room-detail-item i {
  width: 20px;
  text-align: center;
}

.room-price {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 10px;
  padding-top: 10px;
  border-top: 2px solid #f0f0f0;
}

.price-label {
  font-size: 11px;
  color: #999;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.price-amount {
  font-size: 20px;
  font-weight: bold;
  color: #940000;
}

.room-card.selected .price-amount {
  color: #940000;
}

@keyframes scaleIn {
  from {
    transform: scale(0);
    opacity: 0;
  }
  to {
    transform: scale(1);
    opacity: 1;
  }
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .room-image-container {
    height: 120px;
  }
  
  .room-card-body {
    padding: 12px;
  }
  
  .room-number {
    font-size: 16px;
  }
  
  .price-amount {
    font-size: 18px;
  }
  
  .room-details {
    margin: 8px 0;
    padding: 8px 0;
  }
}
</style>
<script>
// Wizard state
let currentWizardStep = 1;
const totalWizardSteps = 4;

// Update wizard step indicator
function updateWizardStepIndicator() {
  const stepItems = document.querySelectorAll('.step-item');
  stepItems.forEach((item, index) => {
    const step = parseInt(item.getAttribute('data-step'));
    item.classList.remove('active', 'completed');
    
    if (step < currentWizardStep) {
      item.classList.add('completed');
    } else if (step === currentWizardStep) {
      item.classList.add('active');
    }
  });
  
  // Update arrow visibility
  document.querySelectorAll('.step-arrow').forEach((arrow, index) => {
    const stepAfterArrow = index + 1;
    if (stepAfterArrow < currentWizardStep) {
      arrow.style.opacity = '1';
    } else {
      arrow.style.opacity = '0.3';
    }
  });
}

// Validate wizard step
function validateWizardStep(step) {
  if (step === 1) {
    // Validate Guest Information
    const guestType = document.getElementById('guest_type').value;
    const fullName = document.getElementById('full_name').value.trim();
    const email = document.getElementById('guest_email').value.trim();
    const phone = document.getElementById('guest_phone').value.trim();
    const guests = document.getElementById('number_of_guests').value;
    
    if (!guestType) {
      swal({ title: "Validation Error", text: "Please select guest type", type: "error", confirmButtonColor: "#940000" });
      return false;
    }
    
    if (guestType === 'international') {
      const nationality = document.getElementById('nationality').value;
      if (!nationality || nationality === '') {
        swal({ title: "Validation Error", text: "Please select nationality", type: "error", confirmButtonColor: "#940000" });
        return false;
      }
    }
    
    if (!fullName) {
      swal({ title: "Validation Error", text: "Please enter full name", type: "error", confirmButtonColor: "#940000" });
      return false;
    }
    
    if (!email || !email.includes('@')) {
      swal({ title: "Validation Error", text: "Please enter a valid email address", type: "error", confirmButtonColor: "#940000" });
      return false;
    }
    
    if (!phone) {
      swal({ title: "Validation Error", text: "Please enter phone number", type: "error", confirmButtonColor: "#940000" });
      return false;
    }
    
    if (!guests || parseInt(guests) < 1) {
      swal({ title: "Validation Error", text: "Please enter number of guests", type: "error", confirmButtonColor: "#940000" });
      return false;
    }
    
    return true;
  } else if (step === 2) {
    // Validate Room & Dates
    const roomType = document.getElementById('room_type').value;
    const checkIn = document.getElementById('check_in').value;
    const checkOut = document.getElementById('check_out').value;
    const roomId = document.getElementById('room_id').value;
    
    if (!roomType) {
      swal({ title: "Validation Error", text: "Please select room type", type: "error", confirmButtonColor: "#940000" });
      return false;
    }
    
    if (!checkIn) {
      swal({ title: "Validation Error", text: "Please select check-in date", type: "error", confirmButtonColor: "#940000" });
      return false;
    }
    
    if (!checkOut) {
      swal({ title: "Validation Error", text: "Please select check-out date", type: "error", confirmButtonColor: "#940000" });
      return false;
    }
    
    if (!roomId || roomId === '') {
      swal({ title: "Validation Error", text: "Please select an available room", type: "error", confirmButtonColor: "#940000" });
      return false;
    }
    
    return true;
  } else if (step === 3) {
    // Validate Payment Information
    const totalPrice = document.getElementById('total_price').value;
    const paymentMethod = document.getElementById('payment_method').value;
    const amountPaid = document.getElementById('amount_paid').value;
    
    if (!totalPrice || parseFloat(totalPrice) <= 0) {
      swal({ title: "Validation Error", text: "Please enter total price", type: "error", confirmButtonColor: "#940000" });
      return false;
    }
    
    if (!paymentMethod) {
      swal({ title: "Validation Error", text: "Please select payment method", type: "error", confirmButtonColor: "#940000" });
      return false;
    }
    
    if (!amountPaid || parseFloat(amountPaid) <= 0) {
      swal({ title: "Validation Error", text: "Please enter amount paid", type: "error", confirmButtonColor: "#940000" });
      return false;
    }
    
    return true;
  }
  
  return true;
}

// Update review summary
function updateReviewSummary() {
  // Guest Information
  const guestType = document.getElementById('guest_type');
  const nationality = document.getElementById('nationality');
  const fullName = document.getElementById('full_name');
  const email = document.getElementById('guest_email');
  const phone = document.getElementById('guest_phone');
  const guests = document.getElementById('number_of_guests');
  
  document.getElementById('review_guest_type').textContent = guestType.options[guestType.selectedIndex].text;
  
  if (guestType.value === 'international' && nationality.value) {
    document.getElementById('review_nationality_row').style.display = '';
    document.getElementById('review_nationality').textContent = nationality.options[nationality.selectedIndex].textContent;
  } else {
    document.getElementById('review_nationality_row').style.display = 'none';
  }
  
  document.getElementById('review_full_name').textContent = fullName.value || '-';
  document.getElementById('review_email').textContent = email.value || '-';
  document.getElementById('review_phone').textContent = (document.getElementById('phone_country_code').textContent + ' ' + phone.value) || '-';
  document.getElementById('review_guests').textContent = guests.value || '-';
  
  // Room & Dates
  const roomSelect = document.getElementById('room_id');
  const checkIn = document.getElementById('check_in');
  const checkOut = document.getElementById('check_out');
  const checkInTime = document.getElementById('check_in_time');
  const checkOutTime = document.getElementById('check_out_time');
  const specialRequests = document.getElementById('special_requests');
  
  // Get room info from selected card
  const selectedRoomCard = document.querySelector('.room-card.selected');
  if (selectedRoomCard) {
    const roomNumber = selectedRoomCard.querySelector('.room-number')?.textContent?.replace('Room ', '') || '';
    const roomType = selectedRoomCard.querySelector('.room-type-badge')?.textContent || '';
    const roomPrice = selectedRoomCard.getAttribute('data-room-price') || '0';
    document.getElementById('review_room').textContent = `Room ${roomNumber} - ${roomType} (${parseFloat(roomPrice).toLocaleString()} TZS/night)`;
  } else {
    const hiddenRoomInput = document.getElementById('room_id');
    if (hiddenRoomInput && hiddenRoomInput.value) {
      document.getElementById('review_room').textContent = 'Room Selected';
    } else {
      document.getElementById('review_room').textContent = '-';
    }
  }
  
  if (checkIn.value && checkInTime.value) {
    const checkInDate = new Date(checkIn.value);
    document.getElementById('review_checkin').textContent = checkInDate.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) + ' at ' + checkInTime.value;
  } else {
    document.getElementById('review_checkin').textContent = '-';
  }
  
  if (checkOut.value && checkOutTime.value) {
    const checkOutDate = new Date(checkOut.value);
    document.getElementById('review_checkout').textContent = checkOutDate.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) + ' at ' + checkOutTime.value;
  } else {
    document.getElementById('review_checkout').textContent = '-';
  }
  
  if (specialRequests.value) {
    document.getElementById('review_special_requests_row').style.display = '';
    document.getElementById('review_special_requests').textContent = specialRequests.value;
  } else {
    document.getElementById('review_special_requests_row').style.display = 'none';
  }
  
  // Show selected departments
  const notifyDepartments = document.querySelectorAll('input[name="notify_departments[]"]:checked');
  if (notifyDepartments.length > 0) {
    const departmentNames = Array.from(notifyDepartments).map(cb => {
      const label = document.querySelector(`label[for="${cb.id}"]`);
      return label ? label.textContent.trim() : cb.value;
    });
    document.getElementById('review_notify_departments_row').style.display = '';
    document.getElementById('review_notify_departments').textContent = departmentNames.join(', ');
  } else {
    document.getElementById('review_notify_departments_row').style.display = 'none';
  }
  
  // Payment Information - get elements directly
  const recommendedPrice = document.getElementById('recommended_price');
  const totalPrice = document.getElementById('total_price');
  const paymentMethod = document.getElementById('payment_method');
  const paymentProvider = document.getElementById('payment_provider');
  const paymentReference = document.getElementById('payment_reference');
  const amountPaid = document.getElementById('amount_paid');
  const paymentPercentage = document.getElementById('payment_percentage');
  const remainingAmount = document.getElementById('remaining_amount');
  const currencySymbol = 'TZS';
  
  // Calculate nights
  let nights = 0;
  const checkInEl = document.getElementById('check_in');
  const checkOutEl = document.getElementById('check_out');
  if (checkInEl && checkOutEl && checkInEl.value && checkOutEl.value) {
    const checkInDate = new Date(checkInEl.value);
    const checkOutDate = new Date(checkOutEl.value);
    if (!isNaN(checkInDate.getTime()) && !isNaN(checkOutDate.getTime())) {
      const timeDiff = checkOutDate.getTime() - checkInDate.getTime();
      nights = Math.ceil(timeDiff / (1000 * 60 * 60 * 24));
    }
  }
  
  // Nights
  const nightsEl = document.getElementById('review_nights');
  if (nightsEl) {
    nightsEl.textContent = nights > 0 ? nights + ' night(s)' : '-';
  }
  
  // Recommended Price
  const recommendedPriceValue = recommendedPrice ? (parseFloat(recommendedPrice.value) || 0) : 0;
  const recommendedPriceEl = document.getElementById('review_recommended_price');
  if (recommendedPriceEl) {
    recommendedPriceEl.textContent = recommendedPriceValue > 0 ? recommendedPriceValue.toLocaleString() + ' ' + currencySymbol : '-';
  }
  
  // Total Price (from hidden field, which is auto-filled from recommended price)
  const totalPriceValue = totalPrice ? (parseFloat(totalPrice.value) || recommendedPriceValue) : recommendedPriceValue;
  const totalPriceEl = document.getElementById('review_total_price');
  if (totalPriceEl) {
    totalPriceEl.textContent = totalPriceValue > 0 ? totalPriceValue.toLocaleString() + ' ' + currencySymbol : '-';
  }
  
  // Discount calculation
  if (recommendedPriceValue > 0 && totalPriceValue < recommendedPriceValue) {
    const discountAmount = recommendedPriceValue - totalPriceValue;
    const discountPercentage = (discountAmount / recommendedPriceValue) * 100;
    document.getElementById('review_discount_row').style.display = '';
    document.getElementById('review_discount').textContent = discountAmount.toLocaleString() + ' ' + currencySymbol + ' (' + discountPercentage.toFixed(2) + '% off)';
  } else {
    document.getElementById('review_discount_row').style.display = 'none';
  }
  
  // Build payment method display with provider if available
  const paymentMethodEl = document.getElementById('review_payment_method');
  if (paymentMethodEl) {
    if (paymentMethod && paymentMethod.options && paymentMethod.selectedIndex >= 0) {
      let paymentMethodDisplay = paymentMethod.options[paymentMethod.selectedIndex].text || '-';
      paymentMethodEl.textContent = paymentMethodDisplay;
    } else {
      paymentMethodEl.textContent = '-';
    }
  }
  
  // Payment Provider
  const paymentProviderRow = document.getElementById('review_payment_provider_row');
  if (paymentProvider && paymentProvider.value) {
    const providerOption = paymentProvider.options[paymentProvider.selectedIndex];
    if (providerOption && paymentProviderRow) {
      paymentProviderRow.style.display = '';
      const providerEl = document.getElementById('review_payment_provider');
      if (providerEl) {
        providerEl.textContent = providerOption.text;
      }
    } else if (paymentProviderRow) {
      paymentProviderRow.style.display = 'none';
    }
  } else if (paymentProviderRow) {
    paymentProviderRow.style.display = 'none';
  }
  
  // Show/hide payment reference in review
  const paymentReferenceRow = document.getElementById('review_payment_reference_row');
  if (paymentReference && paymentReference.value && paymentMethod && paymentMethod.value !== 'cash') {
    if (paymentReferenceRow) {
      paymentReferenceRow.style.display = '';
      const referenceEl = document.getElementById('review_payment_reference');
      if (referenceEl) {
        referenceEl.textContent = paymentReference.value;
      }
    }
  } else if (paymentReferenceRow) {
    paymentReferenceRow.style.display = 'none';
  }
  
  // Amount Paid
  const amountPaidEl = document.getElementById('review_amount_paid');
  if (amountPaidEl) {
    const amountPaidValue = (amountPaid && amountPaid.value) ? parseFloat(amountPaid.value) : 0;
    amountPaidEl.textContent = amountPaidValue > 0 ? amountPaidValue.toLocaleString() + ' ' + currencySymbol : '-';
  }
  
  // Payment Percentage
  const paymentPercentageEl = document.getElementById('review_payment_percentage');
  if (paymentPercentageEl) {
    const paymentPercentageValue = (paymentPercentage && paymentPercentage.value) ? parseFloat(paymentPercentage.value) : 0;
    paymentPercentageEl.textContent = paymentPercentageValue > 0 ? paymentPercentageValue.toFixed(2) + '%' : '-';
  }
  
  // Remaining Amount
  const remainingAmountEl = document.getElementById('review_remaining');
  if (remainingAmountEl) {
    const remainingAmountValue = (remainingAmount && remainingAmount.value) ? parseFloat(remainingAmount.value) : 0;
    remainingAmountEl.textContent = remainingAmountValue > 0 ? remainingAmountValue.toLocaleString() + ' ' + currencySymbol : '-';
  }
}

// Change wizard step
function changeWizardStep(direction) {
  if (direction > 0) {
    // Moving forward - validate current step
    if (!validateWizardStep(currentWizardStep)) {
      return;
    }
    
    if (currentWizardStep < totalWizardSteps) {
      // Hide current step
      document.querySelector(`.wizard-step[data-step="${currentWizardStep}"]`).style.display = 'none';
      currentWizardStep++;
      
      // Show next step
      document.querySelector(`.wizard-step[data-step="${currentWizardStep}"]`).style.display = 'block';
      
      // Auto-fetch rooms if we're going to step 2
      if (currentWizardStep === 2) {
        if (typeof fetchAvailableRooms === 'function') {
          fetchAvailableRooms();
        }
      }
      
      // Update review summary if we're going to step 4
      if (currentWizardStep === 4) {
        updateReviewSummary();
        // Use setTimeout to ensure DOM is ready and values are calculated
        setTimeout(function() {
          // Recalculate payment values before updating review
          if (typeof calculatePaymentFromAmount === 'function') {
            calculatePaymentFromAmount();
          }
          updateReviewSummary();
        }, 200);
      }
      
      updateWizardStepIndicator();
    }
  } else {
    // Moving backward
    if (currentWizardStep > 1) {
      // Hide current step
      document.querySelector(`.wizard-step[data-step="${currentWizardStep}"]`).style.display = 'none';
      currentWizardStep--;
      
      // Show previous step
      document.querySelector(`.wizard-step[data-step="${currentWizardStep}"]`).style.display = 'block';
      
      updateWizardStepIndicator();
    }
  }
}

document.addEventListener('DOMContentLoaded', function() {
  // Complete country list with flags and codes
  window.countries = [
    { name: 'Afghanistan', flag: '🇦🇫', code: '+93' },
    { name: 'Albania', flag: '🇦🇱', code: '+355' },
    { name: 'Algeria', flag: '🇩🇿', code: '+213' },
    { name: 'Andorra', flag: '🇦🇩', code: '+376' },
    { name: 'Angola', flag: '🇦🇴', code: '+244' },
    { name: 'Antigua and Barbuda', flag: '🇦🇬', code: '+1' },
    { name: 'Argentina', flag: '🇦🇷', code: '+54' },
    { name: 'Armenia', flag: '🇦🇲', code: '+374' },
    { name: 'Australia', flag: '🇦🇺', code: '+61' },
    { name: 'Austria', flag: '🇦🇹', code: '+43' },
    { name: 'Azerbaijan', flag: '🇦🇿', code: '+994' },
    { name: 'Bahamas', flag: '🇧🇸', code: '+1' },
    { name: 'Bahrain', flag: '🇧🇭', code: '+973' },
    { name: 'Bangladesh', flag: '🇧🇩', code: '+880' },
    { name: 'Barbados', flag: '🇧🇧', code: '+1' },
    { name: 'Belarus', flag: '🇧🇾', code: '+375' },
    { name: 'Belgium', flag: '🇧🇪', code: '+32' },
    { name: 'Belize', flag: '🇧🇿', code: '+501' },
    { name: 'Benin', flag: '🇧🇯', code: '+229' },
    { name: 'Bhutan', flag: '🇧🇹', code: '+975' },
    { name: 'Bolivia', flag: '🇧🇴', code: '+591' },
    { name: 'Bosnia and Herzegovina', flag: '🇧🇦', code: '+387' },
    { name: 'Botswana', flag: '🇧🇼', code: '+267' },
    { name: 'Brazil', flag: '🇧🇷', code: '+55' },
    { name: 'Brunei', flag: '🇧🇳', code: '+673' },
    { name: 'Bulgaria', flag: '🇧🇬', code: '+359' },
    { name: 'Burkina Faso', flag: '🇧🇫', code: '+226' },
    { name: 'Burundi', flag: '🇧🇮', code: '+257' },
    { name: 'Cambodia', flag: '🇰🇭', code: '+855' },
    { name: 'Cameroon', flag: '🇨🇲', code: '+237' },
    { name: 'Canada', flag: '🇨🇦', code: '+1' },
    { name: 'Cape Verde', flag: '🇨🇻', code: '+238' },
    { name: 'Central African Republic', flag: '🇨🇫', code: '+236' },
    { name: 'Chad', flag: '🇹🇩', code: '+235' },
    { name: 'Chile', flag: '🇨🇱', code: '+56' },
    { name: 'China', flag: '🇨🇳', code: '+86' },
    { name: 'Colombia', flag: '🇨🇴', code: '+57' },
    { name: 'Comoros', flag: '🇰🇲', code: '+269' },
    { name: 'Congo', flag: '🇨🇬', code: '+242' },
    { name: 'Costa Rica', flag: '🇨🇷', code: '+506' },
    { name: 'Croatia', flag: '🇭🇷', code: '+385' },
    { name: 'Cuba', flag: '🇨🇺', code: '+53' },
    { name: 'Cyprus', flag: '🇨🇾', code: '+357' },
    { name: 'Czech Republic', flag: '🇨🇿', code: '+420' },
    { name: 'Denmark', flag: '🇩🇰', code: '+45' },
    { name: 'Djibouti', flag: '🇩🇯', code: '+253' },
    { name: 'Dominica', flag: '🇩🇲', code: '+1' },
    { name: 'Dominican Republic', flag: '🇩🇴', code: '+1' },
    { name: 'Ecuador', flag: '🇪🇨', code: '+593' },
    { name: 'Egypt', flag: '🇪🇬', code: '+20' },
    { name: 'El Salvador', flag: '🇸🇻', code: '+503' },
    { name: 'Equatorial Guinea', flag: '🇬🇶', code: '+240' },
    { name: 'Eritrea', flag: '🇪🇷', code: '+291' },
    { name: 'Estonia', flag: '🇪🇪', code: '+372' },
    { name: 'Eswatini', flag: '🇸🇿', code: '+268' },
    { name: 'Ethiopia', flag: '🇪🇹', code: '+251' },
    { name: 'Fiji', flag: '🇫🇯', code: '+679' },
    { name: 'Finland', flag: '🇫🇮', code: '+358' },
    { name: 'France', flag: '🇫🇷', code: '+33' },
    { name: 'Gabon', flag: '🇬🇦', code: '+241' },
    { name: 'Gambia', flag: '🇬🇲', code: '+220' },
    { name: 'Georgia', flag: '🇬🇪', code: '+995' },
    { name: 'Germany', flag: '🇩🇪', code: '+49' },
    { name: 'Ghana', flag: '🇬🇭', code: '+233' },
    { name: 'Greece', flag: '🇬🇷', code: '+30' },
    { name: 'Grenada', flag: '🇬🇩', code: '+1' },
    { name: 'Guatemala', flag: '🇬🇹', code: '+502' },
    { name: 'Guinea', flag: '🇬🇳', code: '+224' },
    { name: 'Guinea-Bissau', flag: '🇬🇼', code: '+245' },
    { name: 'Guyana', flag: '🇬🇾', code: '+592' },
    { name: 'Haiti', flag: '🇭🇹', code: '+509' },
    { name: 'Honduras', flag: '🇭🇳', code: '+504' },
    { name: 'Hungary', flag: '🇭🇺', code: '+36' },
    { name: 'Iceland', flag: '🇮🇸', code: '+354' },
    { name: 'India', flag: '🇮🇳', code: '+91' },
    { name: 'Indonesia', flag: '🇮🇩', code: '+62' },
    { name: 'Iran', flag: '🇮🇷', code: '+98' },
    { name: 'Iraq', flag: '🇮🇶', code: '+964' },
    { name: 'Ireland', flag: '🇮🇪', code: '+353' },
    { name: 'Israel', flag: '🇮🇱', code: '+972' },
    { name: 'Italy', flag: '🇮🇹', code: '+39' },
    { name: 'Jamaica', flag: '🇯🇲', code: '+1' },
    { name: 'Japan', flag: '🇯🇵', code: '+81' },
    { name: 'Jordan', flag: '🇯🇴', code: '+962' },
    { name: 'Kazakhstan', flag: '🇰🇿', code: '+7' },
    { name: 'Kenya', flag: '🇰🇪', code: '+254' },
    { name: 'Kiribati', flag: '🇰🇮', code: '+686' },
    { name: 'Kosovo', flag: '🇽🇰', code: '+383' },
    { name: 'Kuwait', flag: '🇰🇼', code: '+965' },
    { name: 'Kyrgyzstan', flag: '🇰🇬', code: '+996' },
    { name: 'Laos', flag: '🇱🇦', code: '+856' },
    { name: 'Latvia', flag: '🇱🇻', code: '+371' },
    { name: 'Lebanon', flag: '🇱🇧', code: '+961' },
    { name: 'Lesotho', flag: '🇱🇸', code: '+266' },
    { name: 'Liberia', flag: '🇱🇷', code: '+231' },
    { name: 'Libya', flag: '🇱🇾', code: '+218' },
    { name: 'Liechtenstein', flag: '🇱🇮', code: '+423' },
    { name: 'Lithuania', flag: '🇱🇹', code: '+370' },
    { name: 'Luxembourg', flag: '🇱🇺', code: '+352' },
    { name: 'Madagascar', flag: '🇲🇬', code: '+261' },
    { name: 'Malawi', flag: '🇲🇼', code: '+265' },
    { name: 'Malaysia', flag: '🇲🇾', code: '+60' },
    { name: 'Maldives', flag: '🇲🇻', code: '+960' },
    { name: 'Mali', flag: '🇲🇱', code: '+223' },
    { name: 'Malta', flag: '🇲🇹', code: '+356' },
    { name: 'Marshall Islands', flag: '🇲🇭', code: '+692' },
    { name: 'Mauritania', flag: '🇲🇷', code: '+222' },
    { name: 'Mauritius', flag: '🇲🇺', code: '+230' },
    { name: 'Mexico', flag: '🇲🇽', code: '+52' },
    { name: 'Micronesia', flag: '🇫🇲', code: '+691' },
    { name: 'Moldova', flag: '🇲🇩', code: '+373' },
    { name: 'Monaco', flag: '🇲🇨', code: '+377' },
    { name: 'Mongolia', flag: '🇲🇳', code: '+976' },
    { name: 'Montenegro', flag: '🇲🇪', code: '+382' },
    { name: 'Morocco', flag: '🇲🇦', code: '+212' },
    { name: 'Mozambique', flag: '🇲🇿', code: '+258' },
    { name: 'Myanmar', flag: '🇲🇲', code: '+95' },
    { name: 'Namibia', flag: '🇳🇦', code: '+264' },
    { name: 'Nauru', flag: '🇳🇷', code: '+674' },
    { name: 'Nepal', flag: '🇳🇵', code: '+977' },
    { name: 'Netherlands', flag: '🇳🇱', code: '+31' },
    { name: 'New Zealand', flag: '🇳🇿', code: '+64' },
    { name: 'Nicaragua', flag: '🇳🇮', code: '+505' },
    { name: 'Niger', flag: '🇳🇪', code: '+227' },
    { name: 'Nigeria', flag: '🇳🇬', code: '+234' },
    { name: 'North Korea', flag: '🇰🇵', code: '+850' },
    { name: 'North Macedonia', flag: '🇲🇰', code: '+389' },
    { name: 'Norway', flag: '🇳🇴', code: '+47' },
    { name: 'Oman', flag: '🇴🇲', code: '+968' },
    { name: 'Pakistan', flag: '🇵🇰', code: '+92' },
    { name: 'Palau', flag: '🇵🇼', code: '+680' },
    { name: 'Palestine', flag: '🇵🇸', code: '+970' },
    { name: 'Panama', flag: '🇵🇦', code: '+507' },
    { name: 'Papua New Guinea', flag: '🇵🇬', code: '+675' },
    { name: 'Paraguay', flag: '🇵🇾', code: '+595' },
    { name: 'Peru', flag: '🇵🇪', code: '+51' },
    { name: 'Philippines', flag: '🇵🇭', code: '+63' },
    { name: 'Poland', flag: '🇵🇱', code: '+48' },
    { name: 'Portugal', flag: '🇵🇹', code: '+351' },
    { name: 'Qatar', flag: '🇶🇦', code: '+974' },
    { name: 'Romania', flag: '🇷🇴', code: '+40' },
    { name: 'Russia', flag: '🇷🇺', code: '+7' },
    { name: 'Rwanda', flag: '🇷🇼', code: '+250' },
    { name: 'Saint Kitts and Nevis', flag: '🇰🇳', code: '+1' },
    { name: 'Saint Lucia', flag: '🇱🇨', code: '+1' },
    { name: 'Saint Vincent and the Grenadines', flag: '🇻🇨', code: '+1' },
    { name: 'Samoa', flag: '🇼🇸', code: '+685' },
    { name: 'San Marino', flag: '🇸🇲', code: '+378' },
    { name: 'Sao Tome and Principe', flag: '🇸🇹', code: '+239' },
    { name: 'Saudi Arabia', flag: '🇸🇦', code: '+966' },
    { name: 'Senegal', flag: '🇸🇳', code: '+221' },
    { name: 'Serbia', flag: '🇷🇸', code: '+381' },
    { name: 'Seychelles', flag: '🇸🇨', code: '+248' },
    { name: 'Sierra Leone', flag: '🇸🇱', code: '+232' },
    { name: 'Singapore', flag: '🇸🇬', code: '+65' },
    { name: 'Slovakia', flag: '🇸🇰', code: '+421' },
    { name: 'Slovenia', flag: '🇸🇮', code: '+386' },
    { name: 'Solomon Islands', flag: '🇸🇧', code: '+677' },
    { name: 'Somalia', flag: '🇸🇴', code: '+252' },
    { name: 'South Africa', flag: '🇿🇦', code: '+27' },
    { name: 'South Korea', flag: '🇰🇷', code: '+82' },
    { name: 'South Sudan', flag: '🇸🇸', code: '+211' },
    { name: 'Spain', flag: '🇪🇸', code: '+34' },
    { name: 'Sri Lanka', flag: '🇱🇰', code: '+94' },
    { name: 'Sudan', flag: '🇸🇩', code: '+249' },
    { name: 'Suriname', flag: '🇸🇷', code: '+597' },
    { name: 'Sweden', flag: '🇸🇪', code: '+46' },
    { name: 'Switzerland', flag: '🇨🇭', code: '+41' },
    { name: 'Syria', flag: '🇸🇾', code: '+963' },
    { name: 'Taiwan', flag: '🇹🇼', code: '+886' },
    { name: 'Tajikistan', flag: '🇹🇯', code: '+992' },
    { name: 'Tanzania', flag: '🇹🇿', code: '+255' },
    { name: 'Thailand', flag: '🇹🇭', code: '+66' },
    { name: 'Timor-Leste', flag: '🇹🇱', code: '+670' },
    { name: 'Togo', flag: '🇹🇬', code: '+228' },
    { name: 'Tonga', flag: '🇹🇴', code: '+676' },
    { name: 'Trinidad and Tobago', flag: '🇹🇹', code: '+1' },
    { name: 'Tunisia', flag: '🇹🇳', code: '+216' },
    { name: 'Turkey', flag: '🇹🇷', code: '+90' },
    { name: 'Turkmenistan', flag: '🇹🇲', code: '+993' },
    { name: 'Tuvalu', flag: '🇹🇻', code: '+688' },
    { name: 'Uganda', flag: '🇺🇬', code: '+256' },
    { name: 'Ukraine', flag: '🇺🇦', code: '+380' },
    { name: 'United Arab Emirates', flag: '🇦🇪', code: '+971' },
    { name: 'United Kingdom', flag: '🇬🇧', code: '+44' },
    { name: 'United States', flag: '🇺🇸', code: '+1' },
    { name: 'Uruguay', flag: '🇺🇾', code: '+598' },
    { name: 'Uzbekistan', flag: '🇺🇿', code: '+998' },
    { name: 'Vanuatu', flag: '🇻🇺', code: '+678' },
    { name: 'Vatican City', flag: '🇻🇦', code: '+39' },
    { name: 'Venezuela', flag: '🇻🇪', code: '+58' },
    { name: 'Vietnam', flag: '🇻🇳', code: '+84' },
    { name: 'Yemen', flag: '🇾🇪', code: '+967' },
    { name: 'Zambia', flag: '🇿🇲', code: '+260' },
    { name: 'Zimbabwe', flag: '🇿🇼', code: '+263' }
  ];

  // Get form elements first (needed for nationality handler)
  const phoneCountryCodeDisplay = document.getElementById('phone_country_code');
  const countryCodeHidden = document.getElementById('country_code');
  const nationalitySelect = document.getElementById('nationality');
  const guestTypeSelect = document.getElementById('guest_type');

  // Define updateCurrencyDisplay function early (needed by nationality handler)
  const updateCurrencyDisplay = function() {
    const currencySymbol = 'TZS';
    const currencySymbols = {
      'recommended_currency': currencySymbol,
      'total_price_currency': currencySymbol,
      'amount_paid_currency': currencySymbol,
      'remaining_amount_currency': currencySymbol
    };
    
    Object.keys(currencySymbols).forEach(id => {
      const el = document.getElementById(id);
      if (el) el.textContent = currencySymbol;
    });
    
    // Hide exchange rate display
    const exchangeRateDisplay = document.getElementById('exchange_rate_display');
    if (exchangeRateDisplay) {
      exchangeRateDisplay.style.display = 'none';
    }
    
    // Hide all TZS/USD conversion displays as only TZS is used
    const tzsElements = ['total_price_tzs', 'amount_paid_tzs', 'remaining_amount_tzs'];
    const usdElements = ['recommended_price_usd'];
    
    tzsElements.forEach(id => {
      const el = document.getElementById(id);
      if (el) el.style.display = 'none';
    });
    
    usdElements.forEach(id => {
      const el = document.getElementById(id);
      if (el) el.style.display = 'none';
    });
    
    // Update currency values when currency changes
    updateCurrencyValues();
  };

  // Populate nationality dropdown with flags (excluding Tanzania for International guests)
  function populateNationalityList(excludeTanzania = true) {
    if (!nationalitySelect) return;
    
    // Clear existing options except placeholder
    const placeholder = nationalitySelect.querySelector('option[value=""]');
    nationalitySelect.innerHTML = '';
    if (placeholder) {
      nationalitySelect.appendChild(placeholder);
    }
    
    window.countries.forEach(country => {
      // Skip Tanzania if excludeTanzania is true
      if (excludeTanzania && country.name === 'Tanzania') {
        return;
      }
      
      const option = document.createElement('option');
      option.value = country.name;
      option.textContent = `${country.flag} ${country.name}`;
      option.setAttribute('data-flag', country.flag);
      option.setAttribute('data-code', country.code);
      nationalitySelect.appendChild(option);
    });
    
    // Always add Tanzania option (even if hidden) so form can submit it for Tanzanian guests
    const tanzaniaCountry = window.countries.find(c => c.name === 'Tanzania');
    if (tanzaniaCountry) {
      const tanzaniaOption = document.createElement('option');
      tanzaniaOption.value = 'Tanzania';
      tanzaniaOption.textContent = `${tanzaniaCountry.flag} ${tanzaniaCountry.name}`;
      tanzaniaOption.setAttribute('data-flag', tanzaniaCountry.flag);
      tanzaniaOption.setAttribute('data-code', tanzaniaCountry.code);
      tanzaniaOption.style.display = excludeTanzania ? 'none' : 'block';
      nationalitySelect.appendChild(tanzaniaOption);
    }
  }
  
  // Initial population (excluding Tanzania from visible list for International guests)
  populateNationalityList(true);

  // Function to toggle nationality field and filter countries based on guest type
  function toggleNationalityField() {
    const guestTypeEl = document.getElementById('guest_type');
    const nationalityWrapper = document.getElementById('nationality_field_wrapper');
    const nationalitySelect = document.getElementById('nationality');
    const nationalityHidden = document.getElementById('nationality_hidden');
    const guestTypeHint = document.getElementById('guest_type_hint');
    
    if (!guestTypeEl || !nationalityWrapper) return;
    
    const isTanzanian = guestTypeEl.value === 'tanzanian';
    
    // Update hint text
    if (guestTypeHint) {
      guestTypeHint.textContent = 'All prices will be displayed in TZS (Tanzanian Shillings)';
      guestTypeHint.style.color = '#28a745';
    }
    
    if (isTanzanian) {
      // Hide nationality field for Tanzanian guests
      nationalityWrapper.style.display = 'none';
      if (nationalitySelect) {
        nationalitySelect.removeAttribute('required');
        nationalitySelect.value = 'Tanzania'; // Set default value
        // Also set hidden input for form submission
        if (nationalityHidden) {
          nationalityHidden.value = 'Tanzania';
        }
        // Update hidden country code for Tanzania
        if (countryCodeHidden) {
          countryCodeHidden.value = '+255';
        }
        if (phoneCountryCodeDisplay) {
          phoneCountryCodeDisplay.textContent = '+255';
        }
      }
    } else {
      // Show nationality field for International guests
      nationalityWrapper.style.display = 'block';
      if (nationalitySelect) {
        nationalitySelect.setAttribute('required', 'required');
        nationalitySelect.value = ''; // Clear selection
        if (nationalityHidden) {
          nationalityHidden.value = '';
        }
      }
    }
    
    // Update currency display
    updateCurrencyDisplay();
  }

  // Function to filter out Tanzania from nationality list for International guests
  function filterNationalityList() {
    const guestTypeEl = document.getElementById('guest_type');
    const nationalitySelect = document.getElementById('nationality');
    
    if (!guestTypeEl || !nationalitySelect) return;
    
    const isTanzanian = guestTypeEl.value === 'tanzanian';
    const currentValue = nationalitySelect.value;
    
    // Remove all options except the placeholder
    const placeholder = nationalitySelect.querySelector('option[value=""]');
    nationalitySelect.innerHTML = '';
    if (placeholder) {
      nationalitySelect.appendChild(placeholder);
    }
    
    // Re-populate with countries, excluding Tanzania for International guests
    countries.forEach(country => {
      // Skip Tanzania if guest type is International
      if (!isTanzanian && country.name === 'Tanzania') {
        return;
      }
      
      const option = document.createElement('option');
      option.value = country.name;
      option.textContent = `${country.flag} ${country.name}`;
      option.setAttribute('data-flag', country.flag);
      option.setAttribute('data-code', country.code);
      nationalitySelect.appendChild(option);
    });
    
    // Re-initialize Select2 if it exists (using jQuery ready to ensure it's available)
    if (typeof jQuery !== 'undefined' && typeof jQuery.fn.select2 !== 'undefined') {
      jQuery(function($) {
        if ($('#nationality').hasClass('select2-hidden-accessible')) {
          $('#nationality').select2('destroy');
        }
        $('#nationality').select2({
          placeholder: 'Search and select nationality...',
          allowClear: false,
          width: '100%'
        });
      });
    }
  }

  // Initialize Select2 for searchable dropdown
  if (typeof jQuery !== 'undefined' && typeof jQuery.fn.select2 !== 'undefined') {
    jQuery(document).ready(function($) {
      $('#nationality').select2({
        placeholder: 'Search and select nationality...',
        allowClear: false,
        width: '100%'
      });

      // Handle Select2 specific select event
      $('#nationality').on('select2:select', function(e) {
        const data = e.params.data;
        const countryName = data.id || $(this).val();
        const selectedOption = $(this).find('option:selected');
        const countryCode = selectedOption.attr('data-code');
        const flag = selectedOption.attr('data-flag');
        
        if (countryCode) {
          if (countryCodeHidden) countryCodeHidden.value = countryCode;
          if (phoneCountryCodeDisplay) phoneCountryCodeDisplay.textContent = countryCode;
        }
        
        if (flag) {
          const flagPrefix = document.getElementById('nationality_flag_prefix');
          if (flagPrefix) {
            flagPrefix.textContent = flag;
            flagPrefix.style.display = 'block';
          }
        } else {
          const flagPrefix = document.getElementById('nationality_flag_prefix');
          if (flagPrefix) flagPrefix.style.display = 'none';
        }
      });

      // Also handle regular change event as fallback
      $('#nationality').on('change', function() {
        const countryName = $(this).val();
        
        if (countryName && countryName !== '') {
          const selectedOption = $(this).find('option:selected');
          const countryCode = selectedOption.attr('data-code');
          const flag = selectedOption.attr('data-flag');
          
          if (countryCode) {
            if (countryCodeHidden) countryCodeHidden.value = countryCode;
            if (phoneCountryCodeDisplay) phoneCountryCodeDisplay.textContent = countryCode;
          }
          
          if (flag) {
            const flagPrefix = document.getElementById('nationality_flag_prefix');
            if (flagPrefix) {
              flagPrefix.textContent = flag;
              flagPrefix.style.display = 'block';
            }
          } else {
            const flagPrefix = document.getElementById('nationality_flag_prefix');
            if (flagPrefix) flagPrefix.style.display = 'none';
          }
        }
      });
    });
  } else {
    // Fallback: Native select with change handler
    if (nationalitySelect) {
      nationalitySelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const countryCode = selectedOption.getAttribute('data-code');
        const flag = selectedOption.getAttribute('data-flag');
        const countryName = this.value;
        
        if (countryCode) {
          if (countryCodeHidden) countryCodeHidden.value = countryCode;
          if (phoneCountryCodeDisplay) phoneCountryCodeDisplay.textContent = countryCode;
        }
        
        if (flag) {
          const flagPrefix = document.getElementById('nationality_flag_prefix');
          if (flagPrefix) {
            flagPrefix.textContent = flag;
            flagPrefix.style.display = 'block';
          }
        } else {
          const flagPrefix = document.getElementById('nationality_flag_prefix');
          if (flagPrefix) flagPrefix.style.display = 'none';
        }
        
        // Auto-update guest type based on nationality
        updateGuestTypeFromNationality(countryName);
      });
    }
  }


  // Get remaining form elements
  const roomTypeSelect = document.getElementById('room_type');
  const roomSelect = document.getElementById('room_id');
  const checkInInput = document.getElementById('check_in');
  const checkOutInput = document.getElementById('check_out');
  const recommendedPriceInput = document.getElementById('recommended_price');
  const totalPriceInput = document.getElementById('total_price');
  const paymentMethodSelect = document.getElementById('payment_method');
  const paymentProviderSelect = document.getElementById('payment_provider');
  const paymentProviderWrapper = document.getElementById('payment_provider_wrapper');
  const paymentReferenceInput = document.getElementById('payment_reference');
  const paymentReferenceWrapper = document.getElementById('payment_reference_wrapper');
  const paymentPercentageInput = document.getElementById('payment_percentage');
  
  // Payment provider options
  const paymentProviders = {
    mobile: [
      { value: 'm_pesa', label: 'M-PESA' },
      { value: 'halopesa', label: 'HALOPESA' },
      { value: 'mix_by_yas', label: 'MIX BY YAS' },
      { value: 'airtel_money', label: 'AIRTEL MONEY' }
    ],
    bank: [
      { value: 'nmb', label: 'NMB Bank' },
      { value: 'crdb', label: 'CRDB Bank' },
      { value: 'equity', label: 'Equity Bank' },
      { value: 'kcb', label: 'KCB Bank' },
      { value: 'stanbic', label: 'Stanbic Bank' },
      { value: 'exim', label: 'Exim Bank' },
      { value: 'barclays', label: 'Barclays Bank' },
      { value: 'diamond', label: 'Diamond Trust Bank' },
      { value: 'other_bank', label: 'Other Bank' }
    ],
    card: [
      { value: 'visa', label: 'Visa' },
      { value: 'mastercard', label: 'Mastercard' },
      { value: 'amex', label: 'American Express' },
      { value: 'other_card', label: 'Other Card' }
    ],
    online: [
      { value: 'booking_com', label: 'Booking.com' },
      { value: 'expedia', label: 'Expedia' },
      { value: 'agoda', label: 'Agoda' },
      { value: 'airbnb', label: 'Airbnb' },
      { value: 'other_online', label: 'Other Online Platform' }
    ]
  };
  const amountPaidInput = document.getElementById('amount_paid');
  const remainingAmountInput = document.getElementById('remaining_amount');
  // const showOtherRoomsBtn = document.getElementById('show_other_rooms_btn'); // Button removed
  const roomSelectHint = document.getElementById('room_select_hint');
  
  // Exchange rate from server - REMOVED as only TZS is used
  // const exchangeRate = {{ $exchangeRate ?? 0 }};
  
  // Update exchange rate display value - REMOVED as only TZS is used
  // if (exchangeRate > 0) {
  //   const exchangeRateValueEl = document.getElementById('exchange_rate_value');
  //   if (exchangeRateValueEl) {
  //     exchangeRateValueEl.textContent = exchangeRate.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
  //   }
  // }
  
  let otherAvailableRooms = [];

  // Set minimum date to today
  const today = new Date().toISOString().split('T')[0];
  checkInInput.setAttribute('min', today);
  checkOutInput.setAttribute('min', today);

  // Recalculate and convert all prices when guest type changes
  function recalculateOnGuestTypeChange() {
    // No currency conversion needed as all prices are exclusively in TZS.
    // Only update currency display to ensure TZS symbol is shown.
    updateCurrencyDisplay();
    
    // If room is already selected, recalculate recommended price (which will be in TZS)
    if (roomSelect.value && checkInInput.value && checkOutInput.value) {
      calculateRecommendedPrice();
    }
    
    // Update currency conversions for display
    updateCurrencyValues();
  }

  // Guest type change handler
  if (guestTypeSelect) {
    guestTypeSelect.addEventListener('change', function() {
      toggleNationalityField();
      filterNationalityList();
      updateCurrencyDisplay();
      recalculateOnGuestTypeChange();
    });
    
    // Initial call to set up the form
    toggleNationalityField();
    filterNationalityList();
    updateCurrencyDisplay();
  }

  // Test function to verify connection (can be removed after testing)
  window.testGuestTypeConnection = function() {
    console.log('Testing guest type connection...');
    console.log('Guest type select exists:', !!document.getElementById('guest_type'));
    console.log('Nationality select exists:', !!document.getElementById('nationality'));
    console.log('updateGuestTypeFromNationality function exists:', typeof updateGuestTypeFromNationality === 'function');
    console.log('updateCurrencyDisplay function exists:', typeof updateCurrencyDisplay === 'function');
    
    // Test with Tanzania
    updateGuestTypeFromNationality('Tanzania');
    console.log('After Tanzania test, guest type value:', document.getElementById('guest_type').value);
    
    // Test with another country
    updateGuestTypeFromNationality('Kenya');
    console.log('After Kenya test, guest type value:', document.getElementById('guest_type').value);
  };
  
  // Initialize wizard
  updateWizardStepIndicator();
  
  // Log when page is ready
  console.log('Manual booking form initialized');
  console.log('Wizard step:', currentWizardStep);

  // Room type capacities for auto-filling number of guests
  const roomTypeCapacities = @json($roomTypeCapacities ?? ['Single' => 1, 'Double' => 2, 'Twins' => 2]);

  // Fetch available rooms when room type and dates are selected
  function fetchAvailableRooms() {
    if (!roomTypeSelect || !checkInInput || !checkOutInput) {
      console.error('Required form elements not found');
      return;
    }

    const roomType = roomTypeSelect.value;
    const checkIn = checkInInput.value;
    const checkOut = checkOutInput.value;
    const roomsContainer = document.getElementById('available_rooms_container');
    const roomsGrid = document.getElementById('rooms_cards_grid');
    const roomsLoading = document.getElementById('rooms_loading');
    const roomSelectHint = document.getElementById('room_select_hint');
    const hiddenRoomInput = document.getElementById('room_id');

    if (!roomsContainer || !roomsGrid || !roomsLoading) return;

    if (!roomType || !checkIn || !checkOut) {
      roomsContainer.style.display = 'none';
      const summaryContainer = document.getElementById('availability_summary');
      if (summaryContainer) summaryContainer.style.display = 'none';
      if (hiddenRoomInput) {
        hiddenRoomInput.value = '';
        hiddenRoomInput.removeAttribute('required');
      }
      return;
    }

    // Validate dates
    const checkInDate = new Date(checkIn + 'T00:00:00');
    const checkOutDate = new Date(checkOut + 'T00:00:00');
    
    if (isNaN(checkInDate.getTime()) || isNaN(checkOutDate.getTime()) || checkOutDate <= checkInDate) {
      roomsContainer.style.display = 'block';
      roomsGrid.innerHTML = '<div class="col-12"><div class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i> Invalid dates. Please select valid check-in and check-out dates.</div></div>';
      hiddenRoomInput.value = '';
      // showOtherRoomsBtn.style.display = 'none'; // Button removed
      return;
    }

    // Show loading
    roomsContainer.style.display = 'block';
    roomsLoading.style.display = 'block';
    roomsGrid.innerHTML = '';
    hiddenRoomInput.value = '';
    hiddenRoomInput.removeAttribute('required');

    // Fetch available rooms
    const route = '{{ $role === "reception" ? route("reception.bookings.available-rooms") : route("admin.bookings.available-rooms") }}';
    fetch(`${route}?room_type=${roomType}&check_in=${checkIn}&check_out=${checkOut}`)
      .then(response => response.json())
      .then(data => {
        roomsLoading.style.display = 'none';
        
        if (data.success) {
          // Display Summary
          const summaryContainer = document.getElementById('availability_summary');
          const summaryPills = document.getElementById('summary_pills');
          
          if (summaryContainer && summaryPills) {
            summaryContainer.style.display = 'block';
            
            // Combine all available rooms safely
            let allAvailable = [];
            if (data.available_rooms) {
              const rooms = Array.isArray(data.available_rooms) ? data.available_rooms : Object.values(data.available_rooms);
              allAvailable = allAvailable.concat(rooms);
            }
            if (data.other_available_rooms) {
              const otherRooms = Array.isArray(data.other_available_rooms) ? data.other_available_rooms : Object.values(data.other_available_rooms);
              allAvailable = allAvailable.concat(otherRooms);
            }
            
            // Count by type
            const counts = {};
            allAvailable.forEach(room => {
              counts[room.room_type] = (counts[room.room_type] || 0) + 1;
            });
            
            // Order types: Single, Double, Twins, then others
            const typesOrder = ['Single', 'Double', 'Twins'];
            const sortedTypes = Object.keys(counts).sort((a, b) => {
              const aIdx = typesOrder.indexOf(a);
              const bIdx = typesOrder.indexOf(b);
              if (aIdx !== -1 && bIdx !== -1) return aIdx - bIdx;
              if (aIdx !== -1) return -1;
              if (bIdx !== -1) return 1;
              return a.localeCompare(b);
            });
            
            summaryPills.innerHTML = '';
            sortedTypes.forEach(type => {
              const count = counts[type];
              const isSelectedType = type === roomType;
              const pill = document.createElement('div');
              pill.className = `d-flex align-items-center mr-3 mb-2 p-2 px-3 ${isSelectedType ? 'bg-primary text-white shadow-sm' : 'bg-light text-dark border'}`;
              pill.style.borderRadius = '30px';
              pill.style.fontSize = '14px';
              pill.style.fontWeight = '500';
              pill.style.cursor = 'pointer';
              pill.style.transition = 'all 0.2s ease-in-out';
              
              const icon = type === 'Single' ? 'user' : (type === 'Double' ? 'users' : 'bed');
              pill.innerHTML = `<i class="fa fa-${icon} mr-2"></i> ${type}: <strong>${count}</strong>`;
              pill.title = isSelectedType ? 'Current selection' : `Switch to ${type} rooms`;
              
              // Add click event to switch type
              pill.onclick = function() {
                if (!isSelectedType) {
                  const select = document.getElementById('room_type');
                  if (select) {
                    select.value = type;
                    select.dispatchEvent(new Event('change'));
                  }
                }
              };
              
              // Add hover effects
              pill.onmouseover = function() {
                this.classList.add('shadow-sm');
                if (!isSelectedType) this.style.borderColor = '#940000';
                this.style.transform = 'translateY(-1px)';
              };
              pill.onmouseout = function() {
                if (!isSelectedType) {
                  this.classList.remove('shadow-sm');
                  this.style.borderColor = '#ddd';
                }
                this.style.transform = 'translateY(0)';
              };
              
              summaryPills.appendChild(pill);
            });
          }

          const rooms = Array.isArray(data.available_rooms) ? data.available_rooms : Object.values(data.available_rooms);
          
          if (rooms.length === 0) {
            roomsGrid.innerHTML = '<div class="col-12"><div class="alert alert-info"><i class="fa fa-info-circle"></i> No rooms available for the selected room type and dates. Please try different dates or room type.</div></div>';
            roomSelectHint.textContent = 'No rooms available for the selected room type and dates';
            if (hiddenRoomInput) hiddenRoomInput.value = '';
          } else {
            roomSelectHint.textContent = `${rooms.length} room(s) available - Select a room below`;
            roomsGrid.innerHTML = '';
            
            // Create room cards
            rooms.forEach(room => {
              const roomCard = createRoomCard(room);
              roomsGrid.appendChild(roomCard);
            });
            
            if (hiddenRoomInput) hiddenRoomInput.setAttribute('required', 'required');
          }
        } else {
          roomsGrid.innerHTML = '<div class="col-12"><div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> Error loading rooms. Please try again.</div></div>';
          hiddenRoomInput.value = '';
        }
      })
      .catch(error => {
        console.error('Error fetching available rooms:', error);
        roomsLoading.style.display = 'none';
        roomsGrid.innerHTML = '<div class="col-12"><div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> Error loading rooms. Please try again.</div></div>';
        hiddenRoomInput.value = '';
      });
  }

  // Create room card element
  function createRoomCard(room) {
    const col = document.createElement('div');
    col.className = 'col-md-4 col-sm-6 mb-4';
    
    // Get room image URL - use pre-defined asset paths
    const defaultImage = '{{ asset("royal-master/image/rooms/room1.jpg") }}';
    const storageBase = '{{ asset("storage") }}';
    let imageUrl = defaultImage;
    
    if (room.image) {
      let imgPath = room.image;
      if (imgPath.startsWith('storage/')) {
        imgPath = imgPath.substring(8);
      } else if (!imgPath.startsWith('rooms/') && !imgPath.startsWith('http') && !imgPath.startsWith('/')) {
        imgPath = 'rooms/' + imgPath;
      }
      imageUrl = imgPath.startsWith('http') ? imgPath : storageBase + '/' + imgPath;
    }
    
    // Determine availability status
    const isAvailableNow = room.status === 'available';
    const canSelect = room.can_select !== false;
    let availabilityBadge = '';
    
    if (isAvailableNow) {
      availabilityBadge = '<div class="room-availability-badge available-now"><i class="fa fa-check-circle"></i> Available</div>';
    } else if (room.status === 'to_be_cleaned') {
      availabilityBadge = '<div class="room-availability-badge cleaning-required"><i class="fa fa-broom"></i> Needs Cleaning</div>';
    } else if (room.status === 'occupied') {
      let checkoutDisplay = 'Today';
      if (room.checkout_date && room.checkout_date !== 'Today') {
        try {
          const dateObj = new Date(room.checkout_date + 'T12:00:00');
          if (!isNaN(dateObj.getTime())) {
            checkoutDisplay = dateObj.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
          }
        } catch (e) {
          checkoutDisplay = room.checkout_date;
        }
      }
      availabilityBadge = '<div class="room-availability-badge occupied"><i class="fa fa-user"></i> Occupied - Leaves ' + checkoutDisplay + '</div>';
    } else {
      let checkoutDateDisplay = room.checkout_date === 'Today' ? 'Today' : (room.checkout_date || '');
      availabilityBadge = '<div class="room-availability-badge soon-available"><i class="fa fa-clock-o"></i> Available ' + checkoutDateDisplay + '</div>';
    }
    
    const card = document.createElement('div');
    card.className = 'room-card' + (canSelect ? '' : ' unselectable');
    card.setAttribute('data-room-id', room.id);
    card.setAttribute('data-room-price', room.price_per_night);
    card.setAttribute('data-room-capacity', room.capacity || 1);
    card.setAttribute('data-can-select', canSelect);
    
    // Build HTML using string concatenation to avoid template literal issues
    let cardHtml = '<div class="room-card-inner">';
    cardHtml += '<div class="room-image-container">';
    cardHtml += '<img src="' + imageUrl + '" alt="Room ' + room.room_number + '" class="room-image" onerror="this.src=\'' + defaultImage + '\'">';
    cardHtml += availabilityBadge;
    cardHtml += '<div class="room-selected-overlay"><i class="fa fa-check-circle fa-2x"></i><p>Selected</p></div>';
    cardHtml += '</div>';
    cardHtml += '<div class="room-card-body">';
    cardHtml += '<div class="room-number"><i class="fa fa-home"></i> Room ' + room.room_number + '</div>';
    cardHtml += '<div class="room-type-badge">' + room.room_type + '</div>';
    cardHtml += '<div class="room-details">';
    cardHtml += '<div class="room-detail-item"><i class="fa fa-users text-primary"></i><span>' + (room.capacity || 1) + ' Guest' + (room.capacity > 1 ? 's' : '') + '</span></div>';
    
    if (room.bed_type) {
      cardHtml += '<div class="room-detail-item"><i class="fa fa-bed text-info"></i><span>' + room.bed_type + '</span></div>';
    }
    
    cardHtml += '</div>';
    cardHtml += '<div class="room-price">';
    cardHtml += '<span class="price-label">Per night</span>';
    cardHtml += '<span class="price-amount">' + parseFloat(room.price_per_night).toLocaleString() + ' TZS</span>';
    cardHtml += '</div>';
    cardHtml += '</div>';
    cardHtml += '</div>';
    
    card.innerHTML = cardHtml;
    
    // Add click handler
    card.addEventListener('click', function() {
      selectRoom(room.id, card);
    });
    
    col.appendChild(card);
    return col;
  }

  // Select room function (with toggle/deselect capability)
  function selectRoom(roomId, cardElement) {
    const canSelect = cardElement.getAttribute('data-can-select') !== 'false';
    if (!canSelect) {
      swal({
        title: "Room Not Ready",
        text: "This room is currently occupied or needs cleaning and cannot be selected for today. Please choose an available room or contact housekeeping.",
        type: "warning",
        confirmButtonColor: "#940000"
      });
      return;
    }

    const hiddenRoomInput = document.getElementById('room_id');
    const isCurrentlySelected = cardElement.classList.contains('selected');
    
    if (isCurrentlySelected) {
      // Deselect if already selected
      cardElement.classList.remove('selected');
      hiddenRoomInput.value = '';
      hiddenRoomInput.removeAttribute('required');
      
      // Clear price calculation
      if (typeof calculateRecommendedPrice === 'function') {
        const recommendedPriceInput = document.getElementById('recommended_price');
        if (recommendedPriceInput) recommendedPriceInput.value = '';
      }
    } else {
      // Remove selected class from all cards
      document.querySelectorAll('.room-card').forEach(card => {
        card.classList.remove('selected');
      });
      
      // Add selected class to clicked card
      cardElement.classList.add('selected');
      
      // Set hidden input value
      hiddenRoomInput.value = roomId;
      hiddenRoomInput.setAttribute('required', 'required');
      
      // Trigger change event
      hiddenRoomInput.dispatchEvent(new Event('change'));
      
      // Get room data
      const roomPrice = cardElement.getAttribute('data-room-price');
      const roomCapacity = cardElement.getAttribute('data-room-capacity');
      
      // Update number of guests if needed
      const numberOfGuestsInput = document.getElementById('number_of_guests');
      if (numberOfGuestsInput && parseInt(numberOfGuestsInput.value) > parseInt(roomCapacity)) {
        numberOfGuestsInput.value = roomCapacity;
      }
      
      // Trigger price calculation
      if (typeof calculateRecommendedPrice === 'function') {
        calculateRecommendedPrice();
      }
    }
  }

  // Show other available rooms (feature disabled - button removed)
  /* showOtherRoomsBtn.addEventListener('click', function() {
    if (otherAvailableRooms.length === 0) return;
    
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.innerHTML = `
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Other Available Rooms</h5>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <div class="row" id="other_rooms_grid">
              ${otherAvailableRooms.map(room => {
                const defaultImage = '{{ asset("royal-master/image/rooms/room1.jpg") }}';
                let imageUrl = defaultImage;
                if (room.image) {
                  let imgPath = room.image;
                  if (imgPath.startsWith('storage/')) {
                    imgPath = imgPath.substring(8);
                  } else if (!imgPath.startsWith('rooms/') && !imgPath.startsWith('http') && !imgPath.startsWith('/')) {
                    imgPath = 'rooms/' + imgPath;
                  }
                  const storageBase = '{{ asset("storage") }}';
                  imageUrl = imgPath.startsWith('http') ? imgPath : storageBase + '/' + imgPath;
                }
                
                const isAvailableNow = room.is_available_now !== false;
                const isSoonAvailable = room.is_soon_available === true;
                let availabilityBadge = '';
                
                if (isAvailableNow) {
                  availabilityBadge = '<div class="room-availability-badge available-now"><i class="fa fa-check-circle"></i> Available</div>';
                } else if (isSoonAvailable) {
                  const checkoutDate = room.checkout_date ? new Date(room.checkout_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' }) : '';
                  availabilityBadge = '<div class="room-availability-badge soon-available"><i class="fa fa-clock-o"></i> Available ' + checkoutDate + '</div>';
                }
                
                return '<div class="col-md-6 mb-3">' +
                  '<div class="room-card" data-room-id="' + room.id + '" data-room-price="' + room.price_per_night + '" data-room-capacity="' + (room.capacity || 1) + '" style="cursor: pointer;">' +
                  '<div class="room-card-inner">' +
                  '<div class="room-image-container">' +
                  '<img src="' + imageUrl + '" alt="Room ' + room.room_number + '" class="room-image" style="height: 120px; object-fit: cover;" onerror="this.src=\'' + defaultImage + '\'">' +
                  availabilityBadge +
                  '<div class="room-selected-overlay"><i class="fa fa-check-circle fa-2x"></i><p>Selected</p></div>' +
                  '</div>' +
                  '<div class="room-card-body">' +
                  '<div class="room-number"><i class="fa fa-home"></i> Room ' + room.room_number + '</div>' +
                  '<div class="room-type-badge">' + room.room_type + '</div>' +
                  '<div class="room-price">' +
                  '<span class="price-label">Per night</span>' +
                  '<span class="price-amount">$' + parseFloat(room.price_per_night).toFixed(2) + '</span>' +
                  '</div>' +
                  '</div>' +
                  '</div>' +
                  '</div>';
              }).join('')}
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary" id="select_other_room_btn">Select This Room</button>
          </div>
        </div>
      </div>
    `;
    document.body.appendChild(modal);
    $(modal).modal('show');
    
    // Add click handlers for other rooms modal cards (after modal is added to DOM)
    setTimeout(function() {
      const otherRoomsCards = document.querySelectorAll('#other_rooms_grid .room-card');
      otherRoomsCards.forEach(card => {
        card.addEventListener('click', function() {
          otherRoomsCards.forEach(c => c.classList.remove('selected'));
          this.classList.add('selected');
        });
      });
    }, 100);
    
    document.getElementById('select_other_room_btn').addEventListener('click', function() {
      const selectedCard = document.querySelector('#other_rooms_grid .room-card.selected');
      if (selectedCard) {
        const roomId = selectedCard.getAttribute('data-room-id');
        const roomPrice = selectedCard.getAttribute('data-room-price');
        const roomCapacity = selectedCard.getAttribute('data-room-capacity');
        
        // Set hidden input
        const hiddenRoomInput = document.getElementById('room_id');
        hiddenRoomInput.value = roomId;
        hiddenRoomInput.dispatchEvent(new Event('change'));
        
        // Update number of guests based on room capacity
        const capacity = parseInt(roomCapacity) || 1;
        document.getElementById('number_of_guests').value = capacity;
        
        // Trigger price calculation
        calculateRecommendedPrice();
      }
      $(modal).modal('hide');
      setTimeout(() => modal.remove(), 500);
    });
  }); */

  // Calculate recommended price when room is selected
  function calculateRecommendedPrice() {
    const hiddenRoomInput = document.getElementById('room_id');
    const roomId = hiddenRoomInput ? hiddenRoomInput.value : null;
    const checkIn = checkInInput.value;
    const checkOut = checkOutInput.value;

    if (!roomId || !checkIn || !checkOut) {
      recommendedPriceInput.value = '';
      return;
    }

    // Get room data from selected card
    const selectedCard = document.querySelector('.room-card.selected');
    if (!selectedCard) {
      recommendedPriceInput.value = '';
      return;
    }
    
    const pricePerNight = parseFloat(selectedCard.getAttribute('data-room-price')) || 0;
    
    if (pricePerNight <= 0) {
      recommendedPriceInput.value = '';
      return;
    }
    
    const checkInDate = new Date(checkIn + 'T00:00:00');
    const checkOutDate = new Date(checkOut + 'T00:00:00');
    
    if (isNaN(checkInDate.getTime()) || isNaN(checkOutDate.getTime())) {
      recommendedPriceInput.value = '';
      return;
    }
    
    // Calculate nights
    const timeDiff = checkOutDate.getTime() - checkInDate.getTime();
    const nights = Math.ceil(timeDiff / (1000 * 60 * 60 * 24));
    
    if (nights > 0 && pricePerNight > 0) {
      const recommendedTZS = nights * pricePerNight;
      recommendedPriceInput.value = recommendedTZS.toFixed(0);
      totalPriceInput.value = recommendedTZS.toFixed(0);
      
      // Recalculate payment percentage and remaining amount if amount paid is already entered
      const amountPaidInput = document.getElementById('amount_paid');
      if (amountPaidInput && amountPaidInput.value) {
        calculatePaymentFromAmount(true); // Skip auto-fill to prevent overwriting total price
      }
    } else {
      recommendedPriceInput.value = '';
    }
  }

  // Calculate payment percentage from amount paid
  function calculatePaymentFromAmount(skipAutoFill = false) {
    const totalPrice = parseFloat(totalPriceInput.value) || 0;
    const amountPaid = parseFloat(amountPaidInput.value) || 0;

    if (!skipAutoFill && (!totalPriceInput.value || totalPriceInput.value === '0' || totalPriceInput.value === '')) {
      const recommendedPrice = parseFloat(recommendedPriceInput.value) || 0;
      if (recommendedPrice > 0) {
        totalPriceInput.value = recommendedPrice.toFixed(0);
        const newTotalPrice = recommendedPrice;
        if (newTotalPrice > 0 && amountPaid > 0) {
          const percentage = (amountPaid / newTotalPrice) * 100;
          paymentPercentageInput.value = percentage.toFixed(2);
          
          const remaining = newTotalPrice - amountPaid;
          remainingAmountInput.value = remaining >= 0 ? remaining.toFixed(0) : '0';
        } else {
          paymentPercentageInput.value = '';
          remainingAmountInput.value = '';
        }
        return;
      }
    }
    
    if (totalPrice > 0 && amountPaid > 0) {
      const percentage = (amountPaid / totalPrice) * 100;
      paymentPercentageInput.value = percentage.toFixed(2);
      
      const remaining = totalPrice - amountPaid;
      remainingAmountInput.value = remaining >= 0 ? remaining.toFixed(0) : '0';
    } else {
      paymentPercentageInput.value = '';
      remainingAmountInput.value = '';
    }
  }

  // Update currency conversion values
  function updateCurrencyValues() {
    // No conversion needed
  }

  // Populate payment provider options based on payment method
  function populatePaymentProvider() {
    if (!paymentProviderSelect || !paymentMethodSelect) return;
    
    const paymentMethod = paymentMethodSelect.value;
    const providers = paymentProviders[paymentMethod] || [];
    
    // Clear existing options
    paymentProviderSelect.innerHTML = '<option value="">Select Provider</option>';
    
    if (providers.length > 0) {
      providers.forEach(provider => {
        const option = document.createElement('option');
        option.value = provider.value;
        option.textContent = provider.label;
        paymentProviderSelect.appendChild(option);
      });
    }
  }

  // Toggle payment provider and reference fields based on payment method
  function togglePaymentFields() {
    if (!paymentMethodSelect) return;
    
    const paymentMethod = paymentMethodSelect.value;
    const requiresProvider = ['mobile', 'bank', 'card', 'online'].includes(paymentMethod);
    const requiresReference = paymentMethod && paymentMethod !== 'cash';
    
    // Show/hide provider field
    if (paymentProviderWrapper) {
      if (requiresProvider) {
        paymentProviderWrapper.style.display = 'block';
        if (paymentProviderSelect) {
          paymentProviderSelect.setAttribute('required', 'required');
        }
        populatePaymentProvider();
      } else {
        paymentProviderWrapper.style.display = 'none';
        if (paymentProviderSelect) {
          paymentProviderSelect.removeAttribute('required');
          paymentProviderSelect.value = '';
        }
      }
    }
    
    // Show/hide reference field
    if (paymentReferenceWrapper && paymentReferenceInput) {
      if (requiresReference) {
        paymentReferenceWrapper.style.display = 'block';
        paymentReferenceInput.setAttribute('required', 'required');
      } else {
        paymentReferenceWrapper.style.display = 'none';
        paymentReferenceInput.removeAttribute('required');
        paymentReferenceInput.value = '';
      }
    }
  }

  // Event listeners
  if (paymentMethodSelect) {
    paymentMethodSelect.addEventListener('change', togglePaymentFields);
    // Initial call to set up the fields
    togglePaymentFields();
  }
  
  roomTypeSelect.addEventListener('change', function() {
    // Update number of guests based on room type capacity
    const selectedRoomType = roomTypeSelect.value;
    if (selectedRoomType && roomTypeCapacities[selectedRoomType]) {
      const capacity = roomTypeCapacities[selectedRoomType];
      document.getElementById('number_of_guests').value = capacity;
    }
    // Fetch available rooms only if check-in and check-out dates are already selected
    const checkIn = document.getElementById('check_in').value;
    const checkOut = document.getElementById('check_out').value;
    if (checkIn && checkOut) {
      fetchAvailableRooms();
    }
  });
  checkInInput.addEventListener('change', function() {
    if (checkInInput.value) {
      const nextDay = new Date(checkInInput.value);
      nextDay.setDate(nextDay.getDate() + 1);
      checkOutInput.setAttribute('min', nextDay.toISOString().split('T')[0]);
    }
    fetchAvailableRooms();
  });
  checkOutInput.addEventListener('change', fetchAvailableRooms);
  
  // Listen for room selection changes (from cards)
  const hiddenRoomInput = document.getElementById('room_id');
  if (hiddenRoomInput) {
    hiddenRoomInput.addEventListener('change', function() {
      calculateRecommendedPrice();
    });
  }
  // When total price is manually edited, don't auto-fill it
  totalPriceInput.addEventListener('input', function() {
    calculatePaymentFromAmount(true); // Skip auto-fill when user is editing
    updateCurrencyValues();
  });
  totalPriceInput.addEventListener('change', function() {
    calculatePaymentFromAmount(true); // Skip auto-fill when user is editing
    updateCurrencyValues();
  });
  amountPaidInput.addEventListener('input', function() {
    calculatePaymentFromAmount(true); // Skip auto-fill when amount paid changes
  });
  amountPaidInput.addEventListener('change', calculatePaymentFromAmount);
  amountPaidInput.addEventListener('keyup', calculatePaymentFromAmount);

  // Form submission
  document.getElementById('manualBookingForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const formData = new FormData(form);
    
    // For Tanzanian guests, ensure nationality is set to Tanzania
    const guestType = document.getElementById('guest_type').value;
    if (guestType === 'tanzanian') {
      const nationalitySelect = document.getElementById('nationality');
      if (nationalitySelect) {
        nationalitySelect.value = 'Tanzania';
        formData.set('nationality', 'Tanzania');
        formData.set('country_code', '+255');
      }
    }
    
    // Ensure total_price is set from recommended_price if not already set
    if (!totalPriceInput.value && recommendedPriceInput.value) {
      totalPriceInput.value = recommendedPriceInput.value;
    }
    
    // Validate required fields
    const hiddenRoomInput = document.getElementById('room_id');
    if (!roomTypeSelect.value || !hiddenRoomInput.value || !totalPriceInput.value || !paymentMethodSelect.value || !amountPaidInput.value) {
      swal({
        title: "Validation Error",
        text: "Please fill in all required fields (Total Price, Payment Method, and Amount Paid)",
        type: "error",
        confirmButtonColor: "#940000"
      });
      return;
    }
    
    // Validate payment provider and reference if required
    const paymentMethod = paymentMethodSelect.value;
    const requiresProvider = ['mobile', 'bank', 'card', 'online'].includes(paymentMethod);
    const requiresReference = paymentMethod && paymentMethod !== 'cash';
    
    if (requiresProvider && (!paymentProviderSelect || !paymentProviderSelect.value)) {
      swal({
        title: "Validation Error",
        text: "Please select a payment provider",
        type: "error",
        confirmButtonColor: "#940000"
      });
      return;
    }
    
    if (requiresReference && (!paymentReferenceInput || !paymentReferenceInput.value || !paymentReferenceInput.value.trim())) {
      swal({
        title: "Validation Error",
        text: "Please enter a reference number for the selected payment method",
        type: "error",
        confirmButtonColor: "#940000"
      });
      return;
    }

    swal({
      title: "Create Booking?",
      text: "This will create the booking, send emails to guest, reception, and manager, and generate a receipt.",
      type: "warning",
      showCancelButton: true,
      confirmButtonColor: "#940000",
      cancelButtonColor: "#d33",
      confirmButtonText: "Yes, create it!",
      cancelButtonText: "Cancel",
      closeOnConfirm: false,
      showLoaderOnConfirm: true
    }, function(isConfirm) {
      if (isConfirm) {
        // Show loading
        swal({
          title: "Processing...",
          text: "Creating booking, sending notifications, and generating receipt",
          type: "info",
          showConfirmButton: false,
          allowOutsideClick: false
        });

        fetch(form.action, {
          method: 'POST',
          body: formData,
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
          }
        })
        .then(async response => {
          const data = await response.json();
          
          if (!response.ok || !data.success) {
            throw new Error(data.message || 'Failed to create booking');
          }
          
          return data;
        })
        .then(data => {
          swal({
            title: "Success!",
            text: data.message || "Booking created successfully! Emails sent and receipt generated.",
            type: "success",
            confirmButtonColor: "#940000",
            confirmButtonText: "View Booking"
          }, function() {
            // Open receipt in new window if available
            if (data.receipt_url) {
              window.open(data.receipt_url, '_blank');
            }
            window.location.href = "{{ $role === 'reception' ? route('reception.bookings') : route('admin.bookings.index') }}";
          });
        })
        .catch(error => {
          swal({
            title: "Error!",
            text: error.message || "An error occurred while creating the booking.",
            type: "error",
            confirmButtonColor: "#940000"
          });
        });
      }
    });
  });
});

  // Department selection functions
  function toggleDepartment(checkboxId) {
    const checkbox = document.getElementById(checkboxId);
    if (checkbox) {
      checkbox.checked = !checkbox.checked;
      updateDepartmentCard(checkbox);
    }
  }

  function updateDepartmentCard(checkbox) {
    const card = checkbox.closest('.department-card');
    const badgeId = checkbox.id.replace('notify_', '') + '-badge';
    const badge = document.getElementById(badgeId);
    
    if (checkbox.checked) {
      card.style.borderColor = getDepartmentColor(checkbox.id);
      card.style.backgroundColor = getDepartmentColor(checkbox.id, true);
      card.style.boxShadow = '0 4px 8px rgba(0,0,0,0.1)';
      if (badge) badge.style.display = 'inline-block';
    } else {
      card.style.borderColor = '#e0e0e0';
      card.style.backgroundColor = '#fff';
      card.style.boxShadow = 'none';
      if (badge) badge.style.display = 'none';
    }
    
    updateDepartmentPreview();
  }

  function getDepartmentColor(checkboxId, isBackground = false) {
    const colors = {
      'notify_reception': isBackground ? '#e3f2fd' : '#2196F3',
      'notify_bar': isBackground ? '#fff3e0' : '#ff9800',
      'notify_kitchen': isBackground ? '#ffebee' : '#f44336'
    };
    return colors[checkboxId] || '#e0e0e0';
  }

  function selectAllDepartments() {
    const checkboxes = document.querySelectorAll('.department-checkbox');
    checkboxes.forEach(cb => {
      cb.checked = true;
      updateDepartmentCard(cb);
    });
  }

  function deselectAllDepartments() {
    const checkboxes = document.querySelectorAll('.department-checkbox');
    checkboxes.forEach(cb => {
      cb.checked = false;
      updateDepartmentCard(cb);
    });
  }

  function updateDepartmentPreview() {
    const selected = document.querySelectorAll('.department-checkbox:checked');
    const previewDiv = document.getElementById('department-preview');
    const previewText = document.getElementById('preview-text');
    
    if (selected.length > 0) {
      const departmentNames = Array.from(selected).map(cb => {
        const label = document.querySelector(`label[for="${cb.id}"]`) || 
                     cb.closest('.card-body').querySelector('h6');
        return label ? label.textContent.trim() : cb.value;
      });
      
      previewDiv.style.display = 'block';
      previewText.textContent = `The following ${selected.length} department(s) will be notified: ${departmentNames.join(', ')}. They will receive a notification with the guest's special requests/notes.`;
    } else {
      previewDiv.style.display = 'none';
    }
  }

  // Initialize department cards on page load
  document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.department-checkbox');
    checkboxes.forEach(cb => {
      updateDepartmentCard(cb);
    });

    // Guest Search Functionality
    const guestSearchInput = document.getElementById('guestSearchInput');
    const guestSearchResults = document.getElementById('guestSearchResults');

    if (guestSearchInput) {
      let debounceTimer;
      guestSearchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        const query = this.value;
        const icon = document.getElementById('guestSearchIcon');
        
        if (query.length < 2) {
          guestSearchResults.style.display = 'none';
          if (icon) icon.className = 'fa fa-search';
          return;
        }

        if (icon) icon.className = 'fa fa-spinner fa-spin';

        debounceTimer = setTimeout(() => {
          fetch(`{{ route('admin.bookings.search.guests') }}?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
              if (icon) icon.className = 'fa fa-search';
              guestSearchResults.innerHTML = '';
              if (data.length > 0) {
                data.forEach(guest => {
                  const item = document.createElement('a');
                  item.href = 'javascript:void(0)';
                  item.className = 'list-group-item list-group-item-action py-3 border-left-0 border-right-0';
                  item.style.transition = 'all 0.2s ease';
                  item.style.borderBottom = '1px solid #f0f0f0';
                  
                  item.innerHTML = `
                    <div class="d-flex w-100 justify-content-between align-items-center">
                      <div style="flex: 1;">
                        <h6 class="mb-1" style="color: #00796b; font-weight: 700; font-size: 15px;">${guest.name}</h6>
                        <p class="mb-1 small text-muted">
                          <i class="fa fa-envelope-o mr-1"></i>${guest.email} 
                          <span class="mx-2" style="opacity: 0.3;">|</span> 
                          <i class="fa fa-phone mr-1"></i>${guest.phone || 'No phone'}
                        </p>
                        <div class="d-flex align-items-center mt-1">
                           ${(guest.nationality || guest.country) ? `<small class="badge badge-light border text-secondary mr-2" style="font-size: 10px; background: #fff;"><i class="fa fa-globe mr-1"></i>${guest.nationality || guest.country}</small>` : ''}
                           <small class="text-muted" style="font-size: 10px;"><i class="fa fa-history mr-1"></i>Last: ${guest.last_booking_date}</small>
                        </div>
                      </div>
                      <div class="text-right ml-3 d-flex flex-column align-items-end">
                        <button type="button" class="btn btn-sm btn-outline-primary mb-1 view-history-btn" style="font-size: 10px; padding: 2px 8px; border-radius: 4px;" onclick="event.stopPropagation(); showLastBookingModal('${encodeURIComponent(JSON.stringify(guest.last_booking_details))}', '${guest.name}')">
                           <i class="fa fa-eye"></i> Details
                        </button>
                        <span class="badge badge-pill" style="background: #e0f2f1; color: #00796b; font-size: 10px; padding: 6px 12px; font-weight: 700; border: 1px solid #b2dfdb;">SELECT</span>
                      </div>
                    </div>
                  `;
                  item.onclick = () => fillGuestData(guest);
                  
                  // Hover effect via JS
                  item.onmouseover = function() { 
                    this.style.backgroundColor = '#f4fbfb'; 
                    this.style.boxShadow = 'inset 4px 0 0 #009688';
                  };
                  item.onmouseout = function() { 
                    this.style.backgroundColor = ''; 
                    this.style.boxShadow = '';
                  };
                  
                  guestSearchResults.appendChild(item);
                });
                guestSearchResults.style.display = 'block';
              } else {
                guestSearchResults.innerHTML = `
                  <div class="list-group-item text-muted text-center py-5">
                    <i class="fa fa-user-times fa-3x mb-3" style="opacity: 0.2;"></i>
                    <p class="mb-0 font-weight-bold">No matching guests found</p>
                    <small>Try searching with a different name or email</small>
                  </div>`;
                guestSearchResults.style.display = 'block';
              }
            })
            .catch(err => {
               if (icon) icon.className = 'fa fa-search';
               console.error('Search error:', err);
            });
        }, 250);
      });

      // Close results when clicking outside
      document.addEventListener('click', function(e) {
        if (!guestSearchInput.contains(e.target) && !guestSearchResults.contains(e.target)) {
          guestSearchResults.style.display = 'none';
        }
      });
    }

    function fillGuestData(guest) {
      console.log('Auto-filling guest data for:', guest.name);
      
      // Basic info
      document.getElementById('full_name').value = guest.name || '';
      document.getElementById('guest_email').value = guest.email || '';
      
      // Determine guest type and nationality logic
      const rawNat = (guest.nationality || guest.country || '').trim();
      const natLower = rawNat.toLowerCase();
      const isTanzanian = natLower === 'tanzania' || natLower === 'tanzanian' || natLower === 'tz';
      
      const typeSelect = document.getElementById('guest_type');
      if (typeSelect) {
        typeSelect.value = isTanzanian ? 'tanzanian' : 'international';
        // Dispatch event to trigger existing UI logic (like phone prefix update)
        typeSelect.dispatchEvent(new Event('change'));
      }

      // Handle phone parsing - remove country code if present since form has it separately
      let phone = guest.phone || '';
      if (phone) {
          // Remove common prefixes
          phone = phone.replace(/^\+/, '');
          
          // Try to stripping matched country code based on detected nationality
          let matchedCountry = null;
          if (isTanzanian) {
             matchedCountry = window.countries.find(c => c.name === 'Tanzania');
          } else if (rawNat) {
             matchedCountry = window.countries.find(c => c.name.toLowerCase() === natLower || c.name.toLowerCase().includes(natLower));
          }
          
          if (matchedCountry) {
              const codeClean = matchedCountry.code.replace('+', '');
              if (phone.startsWith(codeClean)) {
                  phone = phone.substring(codeClean.length);
              }
          }
      }
      document.getElementById('guest_phone').value = phone;
      
      // Auto-fill nationality for International guests
      if (!isTanzanian && rawNat) {
        // Find canonical name in our countries array
        const canonical = window.countries.find(c => c.name.toLowerCase() === natLower || c.name.toLowerCase().includes(natLower));
        const finalValue = canonical ? canonical.name : rawNat;

        setTimeout(() => {
          const natSelect = $('#nationality');
          if (natSelect.length) {
            // Force option existence
            if (!natSelect.find("option[value='" + finalValue + "']").length) {
              natSelect.append(new Option(finalValue, finalValue, true, true));
            }
            natSelect.val(finalValue).trigger('change');
            
            // Also update flag prefix manually to be sure
            if (canonical) {
              const flagPrefix = document.getElementById('nationality_flag_prefix');
              if (flagPrefix) {
                flagPrefix.textContent = canonical.flag;
                flagPrefix.style.display = 'block';
              }
            }
          }
        }, 100);
      }
      
      // UI Success Alert
      if (typeof swal === 'function') {
        swal({
          title: "Guest Recognized!",
          text: `Welcome back, ${guest.name}! Profile details have been auto-filled.`,
          type: "success",
          timer: 2500,
          showConfirmButton: false
        });
      }
      
      // Close results and reset search input
      const guestSearchResults = document.getElementById('guestSearchResults');
      const guestSearchInput = document.getElementById('guestSearchInput');
      if (guestSearchResults) guestSearchResults.style.display = 'none';
      if (guestSearchInput) guestSearchInput.value = '';
    }

    // Modal display for last booking
    window.showLastBookingModal = function(detailsEncoded, name) {
      const detailsStr = decodeURIComponent(detailsEncoded);
      let details = null;
      try { details = JSON.parse(detailsStr); } catch(e) {}
      
      const content = document.getElementById('lastBookingDetailsContent');
      if (!details || details === "null") {
        content.innerHTML = `
          <div class="text-center py-4">
            <i class="fa fa-calendar-times-o fa-3x text-muted mb-3"></i>
            <p class="font-weight-bold">No Booking History</p>
            <small class="text-muted">${name} is a new guest with no previous records recorded in the system.</small>
          </div>`;
      } else {
        content.innerHTML = `
          <div class="guest-info-summary mb-4 text-center">
            <h5 class="font-weight-bold text-dark mb-1">${name}</h5>
            <span class="badge badge-success px-3 py-2" style="border-radius: 20px;">Returning Member</span>
          </div>
          <div class="detail-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <div class="detail-item p-3 border" style="border-radius: 10px; background: #fdfdfd;">
              <small class="text-muted d-block text-uppercase" style="font-size: 10px; letter-spacing: 1px;">Room Number</small>
              <span class="font-weight-bold" style="font-size: 18px; color: #00796b;">${details.room || 'N/A'}</span>
            </div>
            <div class="detail-item p-3 border" style="border-radius: 10px; background: #fdfdfd;">
              <small class="text-muted d-block text-uppercase" style="font-size: 10px; letter-spacing: 1px;">Stay Dates</small>
              <span class="font-weight-bold" style="font-size: 13px;">${details.dates || 'N/A'}</span>
            </div>
            <div class="detail-item p-3 border" style="border-radius: 10px; background: #fdfdfd;">
              <small class="text-muted d-block text-uppercase" style="font-size: 10px; letter-spacing: 1px;">Room Type</small>
              <span class="font-weight-bold">${details.type || 'N/A'}</span>
            </div>
            <div class="detail-item p-3 border" style="border-radius: 10px; background: #fdfdfd;">
              <small class="text-muted d-block text-uppercase" style="font-size: 10px; letter-spacing: 1px;">Total Price Paid</small>
              <span class="font-weight-bold text-success">$${details.total_price || '0.00'}</span>
            </div>
          </div>
          <div class="mt-4 p-3 bg-light border-left border-primary" style="border-radius: 4px; border-left-width: 4px !important;">
            <i class="fa fa-info-circle text-primary mr-2"></i>
            <small class="text-dark">Last reservation status was <strong>${details.status}</strong>. Use these details to provide personalized service.</small>
          </div>
        `;
      }
      $('#lastBookingModal').modal('show');
    };
  });
</script>
@endsection

<!-- Last Booking Details Modal -->
<div class="modal fade" id="lastBookingModal" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 9999;">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content" style="border-radius: 15px; border: none; overflow: hidden; box-shadow: 0 15px 35px rgba(0,0,0,0.2);">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title font-weight-bold"><i class="fa fa-history mr-2"></i> Last Booking Overview</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body p-4" id="lastBookingDetailsContent">
        <!-- Content will be injected here -->
      </div>
      <div class="modal-footer border-0">
        <button type="button" class="btn btn-secondary px-4" style="border-radius: 8px;" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

