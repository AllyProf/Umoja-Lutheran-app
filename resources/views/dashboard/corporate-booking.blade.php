@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-building"></i> Corporate Booking</h1>
    <p>Create booking for company/corporate guests</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.bookings.index') }}">Bookings</a></li>
    <li class="breadcrumb-item"><a href="#">Corporate Booking</a></li>
  </ul>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <!-- Wizard Steps Indicator -->
      <div class="wizard-steps mb-4">
        <div class="step-item active" data-step="1">
          <div class="step-number">1</div>
          <div class="step-label">Company Info</div>
        </div>
        <div class="step-arrow"><i class="fa fa-arrow-right"></i></div>
        <div class="step-item" data-step="2">
          <div class="step-number">2</div>
          <div class="step-label">Guider Info</div>
        </div>
        <div class="step-arrow"><i class="fa fa-arrow-right"></i></div>
        <div class="step-item" data-step="3">
          <div class="step-number">3</div>
          <div class="step-label">Booking Details</div>
        </div>
        <div class="step-arrow"><i class="fa fa-arrow-right"></i></div>
        <div class="step-item" data-step="4">
          <div class="step-number">4</div>
          <div class="step-label">Select Rooms</div>
        </div>
        <div class="step-arrow"><i class="fa fa-arrow-right"></i></div>
        <div class="step-item" data-step="5">
          <div class="step-number">5</div>
          <div class="step-label">Assign Guests</div>
        </div>
        <div class="step-arrow"><i class="fa fa-arrow-right"></i></div>
        <div class="step-item" data-step="6">
          <div class="step-number">6</div>
          <div class="step-label">Payment</div>
        </div>
        <div class="step-arrow"><i class="fa fa-arrow-right"></i></div>
        <div class="step-item" data-step="7">
          <div class="step-number">7</div>
          <div class="step-label">Preview</div>
        </div>
      </div>
      
      <form id="corporateBookingForm" method="POST" action="{{ route('admin.bookings.corporate.store') }}" novalidate>
        @csrf
        
        <!-- Step 1: Company Information -->
        <div class="wizard-step" data-step="1">
          <h4 class="mb-4"><i class="fa fa-building"></i> Company Information</h4>

          <!-- Returning Company Search -->
          <div class="row mb-4">
            <div class="col-md-12">
              <div class="guest-search-box shadow-sm mb-2" style="background: white; border-radius: 12px; padding: 20px; border: 1px solid #e0e0e0; transition: all 0.3s ease;">
                <div class="search-header d-flex align-items-center mb-3">
                   <div class="search-icon-circle mr-3" style="width: 45px; height: 45px; background: #e0f2f1; color: #009688; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                      <i class="fa fa-building-o"></i>
                   </div>
                   <div>
                      <h6 class="mb-0 font-weight-bold text-dark" style="text-transform: uppercase; letter-spacing: 0.5px;">RETURNING COMPANY?</h6>
                      <small class="text-muted">Search by company name, email or phone to auto-fill details</small>
                   </div>
                </div>
                <div class="form-group position-relative mb-0">
                  <div class="input-group search-input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text bg-white border-right-0" style="border-radius: 8px 0 0 8px; color: #009688;"><i class="fa fa-search" id="companySearchIcon"></i></span>
                    </div>
                    <input type="text" id="companySearchInput" class="form-control border-left-0" style="height: 48px; font-size: 16px; border-radius: 0 8px 8px 0;" placeholder="Start typing company name, email or phone to auto-fill..." autocomplete="off">
                  </div>
                  <div id="companySearchResults" class="list-group position-absolute w-100 mt-2 shadow-lg" style="display: none; z-index: 1100; max-height: 300px; overflow-y: auto; border-radius: 10px; border: 1px solid #eee;">
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="company_name">Company Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="company_name" name="company_name" data-required="true">
                <small class="form-text text-muted">Enter the company or organization name</small>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="company_email">Company Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="company_email" name="company_email" data-required="true">
                <small class="form-text text-muted">Email for invoices and communications</small>
              </div>
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="company_phone">Company Phone</label>
                <input type="text" class="form-control" id="company_phone" name="company_phone">
                <small class="form-text text-muted">Contact phone number (optional)</small>
              </div>
            </div>
          </div>

          <!-- Navigation Buttons -->
          <div class="wizard-navigation mt-4 pt-3 border-top">
            <button type="button" class="btn btn-primary float-right" onclick="changeWizardStep(2)">
              Next <i class="fa fa-arrow-right"></i>
            </button>
          </div>
        </div>

        <!-- Step 2: Guider/Leader Information -->
        <div class="wizard-step" data-step="2" style="display: none;">
          <h4 class="mb-4"><i class="fa fa-user"></i> Guider/Leader Information</h4>
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="guider_name">Full Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="guider_name" name="guider_name" data-required="true">
                <small class="form-text text-muted">Name of the group leader/guider</small>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="guider_email">Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="guider_email" name="guider_email" data-required="true">
                <small class="form-text text-muted">Email address for the guider</small>
              </div>
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="guider_phone">Phone Number <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="guider_phone" name="guider_phone" data-required="true">
                <small class="form-text text-muted">Contact phone number</small>
              </div>
            </div>
          </div>

          <!-- Navigation Buttons -->
          <div class="wizard-navigation mt-4 pt-3 border-top">
            <button type="button" class="btn btn-secondary float-left" onclick="changeWizardStep(1)">
              <i class="fa fa-arrow-left"></i> Previous
            </button>
            <button type="button" class="btn btn-primary float-right" onclick="changeWizardStep(3)">
              Next <i class="fa fa-arrow-right"></i>
            </button>
          </div>
        </div>

        <!-- Step 3: Booking Requirements -->
        <div class="wizard-step" data-step="3" style="display: none;">
          <h4 class="mb-4"><i class="fa fa-calendar"></i> Booking Requirements</h4>
          
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label for="number_of_guests">Number of Guests <span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="number_of_guests" name="number_of_guests" min="1" max="50" data-required="true">
                <small class="form-text text-muted">Total number of guests in the group</small>
              </div>
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-12">
              <label>Room Types Needed <span class="text-danger">*</span></label>
              <div class="card">
                <div class="card-body">
                  <p class="text-muted mb-3">Specify how many rooms of each type you need:</p>
                  <div class="row">
                    @foreach($roomTypes as $type)
                    <div class="col-md-4 mb-3">
                      <div class="form-group">
                        <label for="room_type_{{ strtolower($type) }}_qty">{{ $type }} Rooms</label>
                        <input type="number" class="form-control room-type-qty" 
                               id="room_type_{{ strtolower($type) }}_qty" 
                               name="room_type_{{ strtolower($type) }}_qty" 
                               min="0" max="20" value="0" 
                               data-room-type="{{ $type }}"
                               data-capacity="1">
                        <small class="form-text text-muted">Each room fits 1 assigned guest</small>
                      </div>
                    </div>
                    @endforeach
                  </div>
                  <div class="alert alert-info mt-3" id="room_types_summary" style="display: none;">
                    <div><strong>Total Rooms Selected:</strong> <span id="total_rooms_count">0</span></div>
                    <div><strong>Total Capacity:</strong> <span id="total_capacity_count">0</span> guests</div>
                    <div class="mt-2">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="sync_guest_count" checked>
                            <label class="custom-control-label" for="sync_guest_count">Auto-update number of guests based on capacity</label>
                        </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="check_in">Check-in Date <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="check_in" name="check_in" data-required="true">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="check_out">Check-out Date <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="check_out" name="check_out" data-required="true">
              </div>
            </div>
          </div>
          
          <div class="row mt-3">
            <div class="col-md-12">
              <div class="form-group">
                <label for="general_notes">General Notes for All Guests</label>
                <textarea class="form-control" id="general_notes" name="general_notes" rows="3" placeholder="Any general notes or instructions that apply to all guests in this booking"></textarea>
                <small class="form-text text-muted">These notes will be included in all guest confirmations</small>
              </div>
            </div>
          </div>

          <!-- Availability Summary -->
          <div id="step3_availability_summary" class="mt-4 mb-4" style="display: none;">
            <div class="card" style="border-radius: 10px; border: 1px solid #e0e0e0; background-color: #fcfcfc; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
              <div class="card-header bg-white py-3">
                <h6 class="mb-0 text-primary" style="font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">
                  <i class="fa fa-bar-chart mr-2"></i> All Hotel Room Statistics
                </h6>
              </div>
              <div class="card-body p-0">
                <div class="table-responsive">
                  <table class="table table-hover mb-0" style="font-size: 14px;">
                    <thead class="bg-light">
                      <tr>
                        <th class="pl-4">Room Type</th>
                        <th class="text-center">Needed</th>
                        <th class="text-center">Available Now</th>
                        <th class="text-center">Soon Available</th>
                        <th class="text-center">Total Available</th>
                        <th class="pr-4 text-center">Status</th>
                      </tr>
                    </thead>
                    <tbody id="step3_summary_table_body">
                      <!-- Summary rows will be dynamically inserted here -->
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>

          <!-- Navigation Buttons -->
          <div class="wizard-navigation mt-4 pt-3 border-top">
            <button type="button" class="btn btn-secondary float-left" onclick="changeWizardStep(2)">
              <i class="fa fa-arrow-left"></i> Previous
            </button>
            <button type="button" class="btn btn-primary float-right" onclick="searchAvailableRooms()">
              Search Rooms <i class="fa fa-search"></i>
            </button>
          </div>
        </div>

        <!-- Step 4: Room Selection -->
        <div class="wizard-step" data-step="4" style="display: none;">
          <h4 class="mb-4"><i class="fa fa-bed"></i> Select Rooms</h4>
          
          <div id="rooms_loading" style="display: none;">
            <div class="text-center py-4">
              <i class="fa fa-spinner fa-spin fa-3x text-primary"></i>
              <p class="mt-2">Searching for available rooms...</p>
            </div>
          </div>
          
          <div id="rooms_container" style="display: none;">
            <div class="alert alert-info mb-3">
              <i class="fa fa-info-circle"></i> Click on a room card to assign guests to it. Rooms are grouped by availability status.
            </div>
            
            <div id="rooms_grid" class="row">
              <!-- Room cards will be dynamically inserted here -->
            </div>
            
            <div class="mt-3" id="guest_progress_wrapper">
              <div class="alert alert-warning" id="guest_progress_container">
                <strong>Guest Assignment Progress:</strong>
                <span id="guest_assignment_progress">0 of <span id="total_guests_count">0</span> guests assigned</span>
              </div>
            </div>
          </div>

          <!-- Navigation Buttons -->
          <div class="wizard-navigation mt-4 pt-3 border-top">
            <button type="button" class="btn btn-secondary float-left" onclick="changeWizardStep(3)">
              <i class="fa fa-arrow-left"></i> Previous
            </button>
            <button type="button" class="btn btn-primary float-right" id="proceed_to_preview_btn" onclick="changeWizardStep(6)" style="display: none;">
              Proceed to Payment <i class="fa fa-arrow-right"></i>
            </button>
          </div>
        </div>

        <!-- Step 5: Guest Assignment (handled via modals) -->
        <!-- This step is handled by clicking room cards which open modals -->

        <!-- Step 6: Payment -->
        <div class="wizard-step" data-step="6" style="display: none;">
          <h4 class="mb-4"><i class="fa fa-credit-card"></i> Payment Information</h4>
          
          <div class="alert alert-info mb-4">
            <i class="fa fa-info-circle"></i> 
            <strong>Payment Breakdown:</strong>
            <ul class="mb-0 mt-2">
              <li><strong>Company Charges (Room + Company Services):</strong> <span id="payment_company_total_tzs">0.00</span> TZS</li>
              <li><strong>Self-Paid Charges (Services Only):</strong> <span id="payment_self_total_tzs">0.00</span> TZS</li>
              <li><strong>Total Amount:</strong> <span id="payment_total_amount_tzs">0.00</span> TZS</li>
            </ul>

          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="recommended_price">Recommended Price (TZS)</label>
                <div class="input-group">
                  <input class="form-control" type="number" id="recommended_price" step="0.01" min="0" placeholder="0.00" readonly style="background-color: #f8f9fa;">
                  <div class="input-group-append">
                    <span class="input-group-text">TZS</span>
                  </div>
                </div>
                <small class="form-text text-muted">Calculated from room price and number of nights (TZS)</small>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="total_price">Total Price (TZS) <span class="text-danger">*</span></label>
                <div class="input-group">
                  <input class="form-control" type="number" id="total_price" name="total_price" step="0.01" min="0" placeholder="0.00" data-required="true">
                  <div class="input-group-append">
                    <span class="input-group-text">TZS</span>
                  </div>
                </div>
                <small class="form-text text-muted">Enter total price in TZS (can be lower than recommended for discounts)</small>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="payment_method">Payment Method <span class="text-danger">*</span></label>
                <select class="form-control" id="payment_method" name="payment_method" data-required="true">
                  <option value="">Select Payment Method</option>
                  <option value="cash">Cash</option>
                  <option value="bank">Bank Transfer</option>
                  <option value="mobile">Mobile Money</option>
                  <option value="card">Card Payment</option>
                  <option value="online">Online Payment</option>
                  <option value="other">Other</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group" id="payment_provider_group" style="display: none;">
                <label for="payment_provider">Payment Provider <span class="text-danger">*</span></label>
                <select class="form-control" id="payment_provider" name="payment_provider">
                  <option value="">Select Provider</option>
                </select>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="payment_reference">Payment Reference/Transaction ID</label>
                <input type="text" class="form-control" id="payment_reference" name="payment_reference" placeholder="Transaction ID, Receipt Number, etc.">
                <small class="form-text text-muted">Required for bank, mobile, card, and online payments</small>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="amount_paid">Amount Paid <span class="text-danger">*</span></label>
                <div class="input-group">
                  <input class="form-control" type="number" id="amount_paid" name="amount_paid" step="0.01" min="0" placeholder="0.00" data-required="true">
                  <div class="input-group-append">
                    <span class="input-group-text">TZS</span>
                  </div>
                </div>
                <small class="form-text text-muted">Enter the amount paid in TZS</small>
              </div>
            </div>
          </div>

          <div class="row">
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
            <div class="col-md-6">
              <div class="form-group">
                <label for="remaining_amount">Remaining Amount</label>
                <div class="input-group">
                  <input class="form-control" type="number" id="remaining_amount" name="remaining_amount" step="0.01" min="0" placeholder="0.00" readonly>
                  <div class="input-group-append">
                    <span class="input-group-text">TZS</span>
                  </div>
                </div>
                <small class="form-text text-muted">Amount to be paid later (TZS)</small>
              </div>
            </div>
          </div>

          <!-- Navigation Buttons -->
          <div class="wizard-navigation mt-4 pt-3 border-top">
            <button type="button" class="btn btn-secondary float-left" onclick="changeWizardStep(4)">
              <i class="fa fa-arrow-left"></i> Previous
            </button>
            <button type="button" class="btn btn-primary float-right" onclick="changeWizardStep(7)">
              Proceed to Preview <i class="fa fa-arrow-right"></i>
            </button>
          </div>
        </div>

        <!-- Step 7: Preview -->
        <div class="wizard-step" data-step="7" style="display: none;">
          <h4 class="mb-4"><i class="fa fa-eye"></i> Preview & Submit</h4>
          
          <div id="preview_content">
            <!-- Preview content will be dynamically generated -->
            <div class="alert alert-info">
              <i class="fa fa-info-circle"></i> Preview content will be generated here
            </div>
          </div>

          <!-- Navigation Buttons -->
          <div class="wizard-navigation mt-4 pt-3 border-top">
            <button type="button" class="btn btn-secondary float-left" onclick="changeWizardStep(6)">
              <i class="fa fa-arrow-left"></i> Previous
            </button>
            <button type="submit" class="btn btn-success float-right">
              <i class="fa fa-check"></i> Submit Booking & Process Payment
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Guest Assignment Modal -->
<div class="modal fade" id="guestAssignmentModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-user-plus"></i> Assign Guest to Room <span id="modal_room_number"></span></h5>
        <button type="button" class="close" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>
      <div class="modal-body" style="max-height: 70vh; overflow-y: auto; overflow-x: visible; position: relative;">
        <form id="guestAssignmentForm">
          <input type="hidden" id="modal_room_id" name="room_id">
          
          <div class="form-group">
            <label for="guest_full_name">Full Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="guest_full_name" name="guest_full_name" required>
          </div>
          
          <div class="form-group">
            <label for="guest_email">Email <span class="text-danger">*</span></label>
            <input type="email" class="form-control" id="guest_email" name="guest_email" data-required="true">
          </div>
          
          <div class="form-group">
            <label for="guest_phone">Phone Number <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="guest_phone" name="guest_phone" data-required="true">
          </div>
          
          <div class="form-group">
            <label for="guest_country">Country <span class="text-danger">*</span></label>
            <select class="form-control" id="guest_country" name="guest_country" data-required="true" style="width: 100%;">
              <option value="">Select a country...</option>
            </select>
            <small class="form-text text-muted">Select guest's country</small>
          </div>
          
          <div class="form-group">
            <label for="payment_responsibility">Service Payment Responsibility <span class="text-danger">*</span></label>
            <select class="form-control" id="payment_responsibility" name="payment_responsibility" data-required="true">
              <option value="company">Billed to Company</option>
              <option value="self">Self-Paid</option>
            </select>
            <small class="form-text text-muted"><strong>Note:</strong> Room charges are always paid by the company. This selection applies only to hotel services (food, drinks, etc.) used during the stay.</small>
          </div>
          
          <div class="form-group">
            <label for="guest_special_requests">Special Requests / Notes</label>
            <textarea class="form-control" id="guest_special_requests" name="guest_special_requests" rows="3" placeholder="Any special requests or notes for this guest"></textarea>
            <small class="form-text text-muted">Select departments below to notify them about these special requests</small>
          </div>
          
          <div class="form-group">
            <label class="d-flex align-items-center">
              <i class="fa fa-bell text-primary mr-2"></i>
              Notify Departments 
              <small class="text-muted ml-2">(Optional - Select departments to notify about special requests)</small>
            </label>
            
            <div class="row">
              <div class="col-md-4 mb-3">
                <div class="card department-card" style="border: 2px solid #e0e0e0; border-radius: 8px; transition: all 0.3s; cursor: pointer;" onclick="toggleGuestDepartment('notify_guest_reception')">
                  <div class="card-body text-center p-3">
                    <input class="form-check-input department-checkbox" type="checkbox" id="notify_guest_reception" name="guest_notify_departments[]" value="reception" style="position: absolute; top: 10px; right: 10px; transform: scale(1.3);" onchange="updateGuestDepartmentCard(this)">
                    <div class="mb-2">
                      <i class="fa fa-users fa-3x text-primary"></i>
                    </div>
                    <h6 class="mb-1 font-weight-bold">Reception</h6>
                    <small class="text-muted d-block">Front desk & guest services</small>
                    <div class="mt-2">
                      <span class="badge badge-primary badge-sm" id="guest-reception-badge" style="display: none;">Selected</span>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="col-md-4 mb-3">
                <div class="card department-card" style="border: 2px solid #e0e0e0; border-radius: 8px; transition: all 0.3s; cursor: pointer;" onclick="toggleGuestDepartment('notify_guest_bar')">
                  <div class="card-body text-center p-3">
                    <input class="form-check-input department-checkbox" type="checkbox" id="notify_guest_bar" name="guest_notify_departments[]" value="bar_keeper" style="position: absolute; top: 10px; right: 10px; transform: scale(1.3);" onchange="updateGuestDepartmentCard(this)">
                    <div class="mb-2">
                      <i class="fa fa-glass fa-3x text-warning"></i>
                    </div>
                    <h6 class="mb-1 font-weight-bold">Bar & Drinks</h6>
                    <small class="text-muted d-block">Beverage services & bar</small>
                    <div class="mt-2">
                      <span class="badge badge-warning badge-sm" id="guest-bar-badge" style="display: none;">Selected</span>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="col-md-4 mb-3">
                <div class="card department-card" style="border: 2px solid #e0e0e0; border-radius: 8px; transition: all 0.3s; cursor: pointer;" onclick="toggleGuestDepartment('notify_guest_kitchen')">
                  <div class="card-body text-center p-3">
                    <input class="form-check-input department-checkbox" type="checkbox" id="notify_guest_kitchen" name="guest_notify_departments[]" value="head_chef" style="position: absolute; top: 10px; right: 10px; transform: scale(1.3);" onchange="updateGuestDepartmentCard(this)">
                    <div class="mb-2">
                      <i class="fa fa-cutlery fa-3x text-danger"></i>
                    </div>
                    <h6 class="mb-1 font-weight-bold">Kitchen & Food</h6>
                    <small class="text-muted d-block">Food preparation & dining</small>
                    <div class="mt-2">
                      <span class="badge badge-danger badge-sm" id="guest-kitchen-badge" style="display: none;">Selected</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="mt-2">
              <button type="button" class="btn btn-sm btn-outline-secondary" onclick="selectAllGuestDepartments()">
                <i class="fa fa-check-square"></i> Select All
              </button>
              <button type="button" class="btn btn-sm btn-outline-secondary ml-2" onclick="deselectAllGuestDepartments()">
                <i class="fa fa-square"></i> Deselect All
              </button>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="saveGuestAssignment()">
          <i class="fa fa-save"></i> Save Guest
        </button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<style>
.wizard-steps {
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 30px;
  flex-wrap: wrap;
}

.step-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  position: relative;
}

.step-number {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: #e0e0e0;
  color: #666;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  margin-bottom: 8px;
  transition: all 0.3s;
}

.step-item.active .step-number {
  background: #940000;
  color: white;
}

.step-item.completed .step-number {
  background: #28a745;
  color: white;
}

.step-label {
  font-size: 12px;
  color: #666;
  text-align: center;
}

.step-item.active .step-label {
  color: #940000;
  font-weight: 600;
}

.step-arrow {
  margin: 0 15px;
  color: #ccc;
  font-size: 18px;
}

.wizard-step {
  min-height: 400px;
}

.wizard-navigation {
  display: flex;
  justify-content: space-between;
}

/* Room Cards Styles (matching manual booking) */
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

.room-card.has-guest .room-card-inner {
  border-color: #28a745;
  border-width: 3px;
  box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);
}

.room-image-container {
  position: relative;
  width: 100%;
  height: 120px;
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

.room-guest-list {
  padding: 8px 0;
  border-top: 1px dashed #eee;
  margin-top: 8px;
}

.room-guest-item {
  font-size: 12px;
  background: #f8f9fa;
  border-radius: 4px;
  padding: 4px 8px;
  margin-bottom: 4px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.remove-guest-btn {
  color: #dc3545;
  cursor: pointer;
  padding: 0 4px;
}

.room-card-body {
  padding: 12px 15px;
}

.room-number {
  font-size: 16px;
  font-weight: bold;
  color: #333;
  margin-bottom: 6px;
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
  padding: 4px 10px;
  border-radius: 12px;
  font-size: 11px;
  font-weight: 600;
  margin-bottom: 8px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.room-details {
  margin: 8px 0;
  padding: 8px 0;
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
  margin-top: 8px;
  padding-top: 8px;
  border-top: 2px solid #f0f0f0;
}

.price-label {
  font-size: 11px;
  color: #999;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.price-amount {
  font-size: 18px;
  font-weight: bold;
  color: #940000;
}

.room-guest-info {
  margin-top: 8px;
  padding-top: 8px;
  border-top: 1px solid #e0e0f0;
  background: #f8f9fa;
  padding: 8px;
  border-radius: 6px;
  font-size: 12px;
}

.room-guest-info .badge {
  margin-right: 5px;
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

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
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

/* Select2 dropdown styling for modal - ensure it appears above modal */
/* Select2 dropdown styling - ensure it appears correctly in modal */
.select2-container {
  z-index: auto;
}

.select2-container--open {
  z-index: 10050 !important;
}

.select2-dropdown {
  z-index: 10050 !important;
  position: absolute !important;
}

/* Ensure Select2 dropdown appears above modal backdrop */
.modal.show .select2-container {
  z-index: 10050 !important;
}

.modal.show .select2-container--open {
  z-index: 10050 !important;
}

.modal.show .select2-dropdown {
  z-index: 10050 !important;
  position: absolute !important;
}

/* Fix for Select2 dropdown positioning in modal */
#guestAssignmentModal {
  overflow: visible !important; /* Allow dropdown to overflow modal */
}

#guestAssignmentModal .modal-dialog {
  position: relative !important; /* Ensure modal-dialog can contain absolutely positioned dropdown */
  overflow: visible !important; /* Allow dropdown to overflow */
}

#guestAssignmentModal .modal-content {
  position: relative !important; /* Ensure modal-content can contain absolutely positioned dropdown */
  overflow: visible !important; /* Allow dropdown to overflow */
}

#guestAssignmentModal .modal-body {
  position: relative !important; /* Ensure modal-body can contain absolutely positioned dropdown */
  overflow-y: auto !important; /* Keep scrolling for content */
  overflow-x: visible !important; /* Allow dropdown to overflow horizontally */
}

#guestAssignmentModal .select2-container {
  position: relative;
  z-index: auto;
}

#guestAssignmentModal .select2-container--open {
  z-index: 10050 !important;
}

#guestAssignmentModal .select2-dropdown {
  z-index: 10050 !important;
  position: absolute !important;
  display: block !important;
}

/* Ensure dropdown appears correctly in all possible parents */
#guestAssignmentModal .modal-body .select2-dropdown,
#guestAssignmentModal .modal-dialog .select2-dropdown,
#guestAssignmentModal .modal-content .select2-dropdown {
  z-index: 10050 !important;
  position: absolute !important;
  display: block !important;
}

/* Modal body scrollable */
#guestAssignmentModal .modal-body {
  max-height: 70vh;
  overflow-y: auto;
  padding-right: 15px;
}

/* Invalid field styling */
.form-control.is-invalid {
  border-color: #dc3545;
  padding-right: calc(1.5em + 0.75rem);
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='m5.8 3.6 .4.4.4-.4'/%3e%3cpath d='m6.2 8.4-.4-.4-.4.4'/%3e%3c/svg%3e");
  background-repeat: no-repeat;
  background-position: right calc(0.375em + 0.1875rem) center;
  background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}

.form-control.is-invalid:focus {
  border-color: #dc3545;
  box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}
</style>
@endsection

@section('scripts')
<script>
// Store booking data
let bookingData = {
  company: {},
  guider: {},
  booking: {},
  rooms: [],
  guests: []
};

let exchangeRate = 1; // Always using TZS for internal calculations now
const defaultImage = '{{ asset("royal-master/image/rooms/room1.jpg") }}';
const storageBase = '{{ asset("storage") }}';

// Add event listeners for room quantity inputs when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
  const qtyInputs = document.querySelectorAll('.room-type-qty');
  qtyInputs.forEach(input => {
    input.addEventListener('input', calculateTotalRooms);
    input.addEventListener('change', calculateTotalRooms);
  });
  
  // Initial calculation
  if (typeof calculateTotalRooms === 'function') {
    calculateTotalRooms();
  }

  // Clear summary when criteria change
  const clearSummary = () => {
    const summaryContainer = document.getElementById('step3_availability_summary');
    if (summaryContainer) summaryContainer.style.display = 'none';
  };

  document.getElementById('check_in').addEventListener('change', clearSummary);
  document.getElementById('check_out').addEventListener('change', clearSummary);
  document.getElementById('number_of_guests').addEventListener('change', clearSummary);
  document.querySelectorAll('.room-type-qty').forEach(input => {
    input.addEventListener('change', clearSummary);
  });
});

// Make changeWizardStep globally accessible
// Validate current step before allowing navigation
function validateStep(currentStep) {
  const stepElement = document.querySelector(`.wizard-step[data-step="${currentStep}"]`);
  if (!stepElement) return true;
  
  // Get all required fields in the current step
  const requiredFields = stepElement.querySelectorAll('[data-required="true"]');
  let isValid = true;
  let firstInvalidField = null;
  let errorMessages = [];
  
  requiredFields.forEach(field => {
    const value = field.value.trim();
    let fieldValid = true;
    let errorMessage = '';
    
    // Check if field is empty
    if (!value) {
      fieldValid = false;
      const label = stepElement.querySelector(`label[for="${field.id}"]`);
      let fieldName = '';
      if (label) {
        fieldName = label.textContent.replace(/\*/g, '').trim();
      } else {
        // Generate field name from ID
        fieldName = field.id.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
      }
      // Use specific field name for guider_phone to avoid confusion
      if (field.id === 'guider_phone') {
        errorMessage = 'Guider Phone Number is required';
      } else {
        errorMessage = `${fieldName} is required`;
      }
    }
    
    // Additional validation for email fields (only if not empty)
    if (field.type === 'email' && value && !field.checkValidity()) {
      fieldValid = false;
      errorMessage = 'Email format is invalid';
    }
    
    // Additional validation for number fields (only if not empty)
    if (field.type === 'number' && value && (isNaN(value) || parseFloat(value) <= 0)) {
      fieldValid = false;
      const label = stepElement.querySelector(`label[for="${field.id}"]`);
      const fieldName = label ? label.textContent.replace(/\*/g, '').trim() : field.name;
      errorMessage = `${fieldName} must be greater than 0`;
    }
    
    if (!fieldValid) {
      isValid = false;
      field.classList.add('is-invalid');
      if (!firstInvalidField) {
        firstInvalidField = field;
      }
      // Only add error message if not already added (avoid duplicates)
      if (errorMessage && !errorMessages.some(msg => {
        // Check for duplicate messages
        const msgLower = msg.toLowerCase();
        const errorLower = errorMessage.toLowerCase();
        return msgLower === errorLower || 
               (msgLower.includes('phone') && errorLower.includes('phone')) ||
               (msgLower.includes('guider') && errorLower.includes('guider'));
      })) {
        errorMessages.push(errorMessage);
      }
    } else {
      field.classList.remove('is-invalid');
    }
  });
  
  // Step-specific validations (only for complex validations, basic required checks are done above)
  if (currentStep === 1) {
    // Company Information - additional email format validation
    const companyEmail = document.getElementById('company_email');
    if (companyEmail.value.trim() && !companyEmail.checkValidity()) {
      isValid = false;
      if (!errorMessages.some(msg => msg.includes('Company Email'))) {
        errorMessages.push('Company Email format is invalid');
      }
      companyEmail.classList.add('is-invalid');
    }
  } else if (currentStep === 2) {
    // Guider Information - additional email format validation
    const guiderEmail = document.getElementById('guider_email');
    if (guiderEmail.value.trim() && !guiderEmail.checkValidity()) {
      isValid = false;
      if (!errorMessages.some(msg => msg.includes('Guider Email'))) {
        errorMessages.push('Guider Email format is invalid');
      }
      guiderEmail.classList.add('is-invalid');
    }
  } else if (currentStep === 3) {
    // Booking Requirements
    const numberOfGuests = document.getElementById('number_of_guests').value.trim();
    const checkIn = document.getElementById('check_in').value.trim();
    const checkOut = document.getElementById('check_out').value.trim();
    
    if (!numberOfGuests || parseInt(numberOfGuests) < 1) {
      isValid = false;
      errorMessages.push('Number of Guests is required and must be at least 1');
      document.getElementById('number_of_guests').classList.add('is-invalid');
    }
    if (!checkIn) {
      isValid = false;
      errorMessages.push('Check-in Date is required');
      document.getElementById('check_in').classList.add('is-invalid');
    }
    if (!checkOut) {
      isValid = false;
      errorMessages.push('Check-out Date is required');
      document.getElementById('check_out').classList.add('is-invalid');
    }
    
    // Check if at least one room type is selected
    const roomTypeInputs = document.querySelectorAll('.room-type-qty');
    let totalRooms = 0;
    roomTypeInputs.forEach(input => {
      const qty = parseInt(input.value) || 0;
      totalRooms += qty;
    });
    
    if (totalRooms === 0) {
      isValid = false;
      errorMessages.push('Please select at least one room type');
    }
    
    // Validate dates
    if (checkIn && checkOut) {
      const checkInDate = new Date(checkIn);
      const checkOutDate = new Date(checkOut);
      if (checkOutDate <= checkInDate) {
        isValid = false;
        errorMessages.push('Check-out date must be after check-in date');
        document.getElementById('check_out').classList.add('is-invalid');
      }
    }
  } else if (currentStep === 4) {
    // Room Selection - require that rooms have been searched and guests assigned
    if (!bookingData.rooms || bookingData.rooms.length === 0) {
      isValid = false;
      errorMessages.push('Please search and select rooms first');
    }
    
    // Require at least one guest to be assigned to a room
    const totalGuests = bookingData.booking?.number_of_guests || 0;
    const assignedGuests = bookingData.guests?.length || 0;
    
    if (totalGuests > 0 && assignedGuests === 0) {
      isValid = false;
      errorMessages.push('Please assign at least one guest to a room before proceeding');
    }
    
    if (totalGuests === 0) {
      isValid = false;
      errorMessages.push('Number of guests must be set. Please go back to Booking Requirements step.');
    }
  } else if (currentStep === 6) {
    // Payment Information
    const totalPrice = document.getElementById('total_price').value.trim();
    const paymentMethod = document.getElementById('payment_method').value.trim();
    const amountPaid = document.getElementById('amount_paid').value.trim();
    const paymentReference = document.getElementById('payment_reference').value.trim();
    
    if (!totalPrice || parseFloat(totalPrice) <= 0) {
      isValid = false;
      errorMessages.push('Total Price is required');
      document.getElementById('total_price').classList.add('is-invalid');
    }
    if (!paymentMethod) {
      isValid = false;
      errorMessages.push('Payment Method is required');
      document.getElementById('payment_method').classList.add('is-invalid');
    }
    
    // Payment reference is required ONLY for non-cash methods
    const nonCashMethods = ['online', 'bank', 'mobile', 'card', 'other'];
    if (nonCashMethods.includes(paymentMethod) && !paymentReference) {
      isValid = false;
      errorMessages.push('Payment Reference is required for ' + paymentMethod);
      document.getElementById('payment_reference').classList.add('is-invalid');
    } else {
      document.getElementById('payment_reference').classList.remove('is-invalid');
    }
  }
  
  if (!isValid) {
    // Show error message
    Swal.fire({
      icon: 'error',
      title: 'Validation Error',
      html: '<ul style="text-align: left;"><li>' + errorMessages.join('</li><li>') + '</li></ul>',
      confirmButtonColor: '#940000',
      confirmButtonText: 'OK'
    });
    
    // Focus on first invalid field
    if (firstInvalidField) {
      setTimeout(() => {
        firstInvalidField.focus();
        firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }, 300);
    }
  }
  
  return isValid;
}

window.changeWizardStep = function(step) {
  // Get current step number
  let currentStep = null;
  document.querySelectorAll('.wizard-step').forEach(el => {
    if (el.style.display !== 'none' && el.getAttribute('data-step')) {
      currentStep = parseInt(el.getAttribute('data-step'));
    }
  });
  
  // If moving forward, validate current step first
  if (currentStep && step > currentStep) {
    if (!validateStep(currentStep)) {
      return; // Don't proceed if validation fails
    }
    
    // Save current step data before moving forward
    if (currentStep === 1) {
      bookingData.company = {
        name: document.getElementById('company_name').value.trim(),
        email: document.getElementById('company_email').value.trim(),
        phone: document.getElementById('company_phone').value.trim()
      };
    } else if (currentStep === 2) {
      bookingData.guider = {
        name: document.getElementById('guider_name').value.trim(),
        email: document.getElementById('guider_email').value.trim(),
        phone: document.getElementById('guider_phone').value.trim()
      };
    } else if (currentStep === 3) {
      bookingData.booking = {
        number_of_guests: parseInt(document.getElementById('number_of_guests').value) || 0,
        check_in: document.getElementById('check_in').value,
        check_out: document.getElementById('check_out').value
      };
    }
  }
  
  // Hide all steps
  document.querySelectorAll('.wizard-step').forEach(el => {
    el.style.display = 'none';
  });
  
  // Show current step
  const targetStep = document.querySelector(`.wizard-step[data-step="${step}"]`);
  if (targetStep) {
    targetStep.style.display = 'block';
  }
  
  // Update step indicators
  document.querySelectorAll('.step-item').forEach((el, index) => {
    const stepNum = index + 1;
    el.classList.remove('active', 'completed');
    if (stepNum < step) {
      el.classList.add('completed');
    } else if (stepNum === step) {
      el.classList.add('active');
    }
  });
  
  // Special handling for guest assignment progress
  if (step === 4) {
    updateGuestAssignmentProgress();
  }
  
  // Special handling for preview and payment
  if (step === 6) {
    generatePreview();
    updatePaymentSummary();
  }
}

// Calculate total rooms and capacity selected
function calculateTotalRooms() {
  const qtyInputs = document.querySelectorAll('.room-type-qty');
  let totalRooms = 0;
  let totalCapacity = 0;
  const roomTypesNeeded = {};
  
  qtyInputs.forEach(input => {
    const qty = parseInt(input.value) || 0;
    const capacity = parseInt(input.getAttribute('data-capacity')) || 1;
    const roomType = input.getAttribute('data-room-type');
    
    if (qty > 0) {
      totalRooms += qty;
      totalCapacity += qty; // Standardized to 1 guest per room for assignment
      roomTypesNeeded[roomType] = qty;
    }
  });
  
  const summaryEl = document.getElementById('room_types_summary');
  const totalRoomsCountEl = document.getElementById('total_rooms_count');
  const totalCapacityCountEl = document.getElementById('total_capacity_count');
  const guestCountInput = document.getElementById('number_of_guests');
  const syncCheckbox = document.getElementById('sync_guest_count');
  
  if (summaryEl && totalRoomsCountEl && totalCapacityCountEl) {
    if (totalRooms > 0) {
      summaryEl.style.display = 'block';
      totalRoomsCountEl.textContent = totalRooms;
      totalCapacityCountEl.textContent = totalCapacity;
      
      // Auto-update guest count if checkbox is checked
      if (syncCheckbox && syncCheckbox.checked && guestCountInput) {
        guestCountInput.value = totalCapacity;
      }
    } else {
      summaryEl.style.display = 'none';
    }
  }
  
  return { total: totalRooms, capacity: totalCapacity, roomTypesNeeded };
}

// Search available rooms
function searchAvailableRooms() {
  // Validate Step 3 before searching
  if (!validateStep(3)) {
    return;
  }
  
  const checkIn = document.getElementById('check_in').value;
  const checkOut = document.getElementById('check_out').value;
  const numberOfGuests = parseInt(document.getElementById('number_of_guests').value);
  const { total: totalRooms, roomTypesNeeded } = calculateTotalRooms();
  
  // Store booking data
  bookingData.booking = {
    number_of_guests: numberOfGuests,
    check_in: checkIn,
    check_out: checkOut
  };
  
  // Validate dates
  const checkInDate = new Date(checkIn + 'T00:00:00');
  const checkOutDate = new Date(checkOut + 'T00:00:00');
  
  if (isNaN(checkInDate.getTime()) || isNaN(checkOutDate.getTime()) || checkOutDate <= checkInDate) {
      Swal.fire({
        icon: 'error',
        title: 'Invalid Dates',
        text: 'Invalid dates. Please select valid check-in and check-out dates.',
        confirmButtonColor: '#940000',
        confirmButtonText: 'OK'
      });
    return;
  }
  
  // Store booking data
  bookingData.booking = {
    check_in: checkIn,
    check_out: checkOut,
    room_types_needed: roomTypesNeeded,
    total_rooms_needed: totalRooms,
    number_of_guests: numberOfGuests
  };
  
  // Store company and guider data
  bookingData.company = {
    name: document.getElementById('company_name').value,
    email: document.getElementById('company_email').value,
    phone: document.getElementById('company_phone').value
  };
  
  bookingData.guider = {
    name: document.getElementById('guider_name').value,
    email: document.getElementById('guider_email').value,
    phone: document.getElementById('guider_phone').value
  };
  
  // Show loading
  document.getElementById('rooms_loading').style.display = 'block';
  document.getElementById('rooms_container').style.display = 'none';
  
  // Build room types array for API - Always include all possible types for statistics
  const allPossibleTypes = ['Single', 'Double', 'Twins'];
  const roomTypesParam = allPossibleTypes.map(type => 'room_types[]=' + encodeURIComponent(type)).join('&');
  const route = '{{ route("admin.bookings.corporate.available-rooms") }}';
  
  fetch(`${route}?${roomTypesParam}&check_in=${checkIn}&check_out=${checkOut}`)
    .then(response => response.json())
    .then(data => {
      document.getElementById('rooms_loading').style.display = 'none';
      
      if (data.success) {
        // Group rooms by type
        const roomsByType = {};
        data.available_rooms.forEach(room => {
          if (!roomsByType[room.room_type]) {
            roomsByType[room.room_type] = [];
          }
          roomsByType[room.room_type].push(room);
        });

        // Calculate statistics for ALL room types
        const roomStatus = {};
        allPossibleTypes.forEach(type => {
          const needed = roomTypesNeeded[type] || 0;
          const available = (roomsByType[type] || []).filter(r => r.is_available_now).length;
          const soon = (roomsByType[type] || []).filter(r => r.is_soon_available).length;
          
          roomStatus[type] = {
            needed: needed,
            available_now: available,
            soon_available: soon,
            total_available: available + soon
          };
        });

        // Populate Step 3 Summary Table
        const summaryContainer = document.getElementById('step3_availability_summary');
        const summaryTableBody = document.getElementById('step3_summary_table_body');
        
        if (summaryContainer && summaryTableBody) {
          summaryContainer.style.display = 'block';
          summaryTableBody.innerHTML = '';
          
          allPossibleTypes.forEach(type => {
            const status = roomStatus[type];
            const hasEnough = status.available_now >= status.needed;
            const canMeetWithSoon = (status.available_now + status.soon_available) >= status.needed;
            
            let statusBadge = '';
            if (status.needed === 0) {
              statusBadge = '<span class="badge badge-light" style="color: #999; border: 1px solid #ddd;">Optional</span>';
            } else if (hasEnough) {
              statusBadge = '<span class="badge badge-success"><i class="fa fa-check"></i> Sufficient</span>';
            } else if (canMeetWithSoon) {
              statusBadge = '<span class="badge badge-warning text-white"><i class="fa fa-clock-o"></i> Partial</span>';
            } else {
              statusBadge = '<span class="badge badge-danger"><i class="fa fa-times"></i> Insufficient</span>';
            }
            
            const row = document.createElement('tr');
            row.innerHTML = `
              <td class="pl-4"><strong>${type}</strong></td>
              <td class="text-center">${status.needed}</td>
              <td class="text-center text-success" style="font-weight: 600;">${status.available_now}</td>
              <td class="text-center text-warning" style="font-weight: 600;">${status.soon_available}</td>
              <td class="text-center font-weight-bold">${status.total_available}</td>
              <td class="pr-4 text-center">${statusBadge}</td>
            `;
            summaryTableBody.appendChild(row);
          });
        }

        // Store overall data
        bookingData.rooms = data.available_rooms;
        bookingData.room_status = roomStatus;
        
        // Render room selection grid in Step 4
        displayRoomCards(data.available_rooms, roomStatus, roomTypesNeeded);
        
        document.getElementById('rooms_container').style.display = 'block';
        changeWizardStep(4);
        
        // Show proceed button
        const proceedBtn = document.getElementById('proceed_to_preview_btn');
        if (proceedBtn) {
          proceedBtn.style.display = 'block';
        }
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Error loading rooms. Please try again.',
          confirmButtonColor: '#940000',
          confirmButtonText: 'OK'
        });
      }
    })
    .catch(error => {
      console.error('Error fetching available rooms:', error);
      document.getElementById('rooms_loading').style.display = 'none';
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'Error loading rooms. Please try again.',
        confirmButtonColor: '#940000',
        confirmButtonText: 'OK'
      });
    });
}

// Display room cards with status information
function displayRoomCards(rooms, roomStatus, roomTypesNeeded) {
  const roomsGrid = document.getElementById('rooms_grid');
  roomsGrid.innerHTML = '';
  
  // Show room status summary
  if (roomStatus && Object.keys(roomStatus).length > 0) {
    let statusHtml = '<div class="col-12 mb-4">';
    statusHtml += '<div class="card border-info">';
    statusHtml += '<div class="card-header bg-info text-white"><h5 class="mb-0"><i class="fa fa-info-circle"></i> Room Availability Summary</h5></div>';
    statusHtml += '<div class="card-body">';
    statusHtml += '<table class="table table-sm mb-0">';
    statusHtml += '<thead><tr><th>Room Type</th><th>Needed</th><th>Available Now</th><th>Soon Available</th><th>Status</th></tr></thead>';
    statusHtml += '<tbody>';
    
    Object.keys(roomStatus).forEach(type => {
      const status = roomStatus[type];
      const hasEnough = status.available_now >= status.needed;
      const canMeetWithSoon = (status.available_now + status.soon_available) >= status.needed;
      
      let statusBadge = '';
      if (hasEnough) {
        statusBadge = '<span class="badge badge-success">Sufficient</span>';
      } else if (canMeetWithSoon) {
        statusBadge = '<span class="badge badge-warning">Some Soon Available</span>';
      } else {
        statusBadge = '<span class="badge badge-danger">Insufficient</span>';
      }
      
      statusHtml += '<tr>';
      statusHtml += '<td><strong>' + type + '</strong></td>';
      statusHtml += '<td>' + status.needed + '</td>';
      statusHtml += '<td><span class="badge badge-success">' + status.available_now + '</span></td>';
      statusHtml += '<td><span class="badge badge-warning">' + status.soon_available + '</span></td>';
      statusHtml += '<td>' + statusBadge + '</td>';
      statusHtml += '</tr>';
    });
    
    statusHtml += '</tbody></table>';
    statusHtml += '</div></div></div>';
    
    roomsGrid.innerHTML = statusHtml;
  }
  
  // Group rooms by availability status
  const availableNowRooms = rooms.filter(r => r.is_available_now);
  const soonAvailableRooms = rooms.filter(r => r.is_soon_available);
  
  // Display available now rooms
  if (availableNowRooms.length > 0) {
    const sectionDiv = document.createElement('div');
    sectionDiv.className = 'col-12 mb-3';
    sectionDiv.innerHTML = '<h5 class="text-success"><i class="fa fa-check-circle"></i> Available Now (' + availableNowRooms.length + ' rooms)</h5>';
    roomsGrid.appendChild(sectionDiv);
    
    const rowDiv = document.createElement('div');
    rowDiv.className = 'row';
    // Ensure proper flex layout for single cards - prevents narrow display
    rowDiv.style.display = 'flex';
    rowDiv.style.flexWrap = 'wrap';
    
    availableNowRooms.forEach(room => {
      const roomCard = createRoomCard(room);
      rowDiv.appendChild(roomCard);
    });
    
    roomsGrid.appendChild(rowDiv);
  }
  
  // Display soon available rooms
  if (soonAvailableRooms.length > 0) {
    const sectionDiv = document.createElement('div');
    sectionDiv.className = 'col-12 mb-3 mt-4';
    sectionDiv.innerHTML = '<h5 class="text-warning"><i class="fa fa-clock-o"></i> Soon Available (' + soonAvailableRooms.length + ' rooms)</h5>';
    roomsGrid.appendChild(sectionDiv);
    
    const rowDiv = document.createElement('div');
    rowDiv.className = 'row';
    // Ensure proper flex layout for single cards - prevents narrow display
    rowDiv.style.display = 'flex';
    rowDiv.style.flexWrap = 'wrap';
    
    soonAvailableRooms.forEach(room => {
      const roomCard = createRoomCard(room);
      rowDiv.appendChild(roomCard);
    });
    
    roomsGrid.appendChild(rowDiv);
  }
  
  // Show message if no rooms
  if (rooms.length === 0) {
    roomsGrid.innerHTML += '<div class="col-12"><div class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i> No rooms available for the selected room types and dates. Please try different dates or room types.</div></div>';
  }
  
  // Always show proceed button after rooms are displayed
  const proceedBtn = document.getElementById('proceed_to_preview_btn');
  if (proceedBtn) {
    proceedBtn.style.display = 'block';
  }
  
  updateGuestAssignmentProgress();
}

// Create room card element (matching manual booking dimensions)
function createRoomCard(room) {
  const col = document.createElement('div');
  col.className = 'col-md-4 col-sm-6 mb-4';
  // Ensure minimum width for single cards
  col.style.minWidth = '280px';
  col.style.flex = '0 0 auto';
  
  // Get room image URL
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
  
  // Determine selection availability
  const canSelect = room.can_select !== false;

  const card = document.createElement('div');
  card.className = 'room-card' + (canSelect ? '' : ' unselectable');
  card.setAttribute('data-room-id', room.id);
  card.setAttribute('data-room-price', room.price_per_night);
  card.setAttribute('data-room-capacity', room.capacity || 1);
  card.setAttribute('data-can-select', canSelect);
  
  // Check if room has assigned guest
  const hasGuest = bookingData.guests.some(g => g.room_id == room.id);
  
  // Determine availability status
  const isAvailableNow = room.status === 'available';
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
  } else if (room.is_soon_available) {
    let checkoutDateDisplay = room.checkout_date === 'Today' ? 'Today' : (room.checkout_date || '');
    availabilityBadge = '<div class="room-availability-badge soon-available"><i class="fa fa-clock-o"></i> Available ' + checkoutDateDisplay + '</div>';
  }
  
  // Build HTML (matching manual booking structure)
  let cardHtml = '<div class="room-card-inner">';
  cardHtml += '<div class="room-image-container">';
  cardHtml += '<img src="' + imageUrl + '" alt="Room ' + room.room_number + '" class="room-image" onerror="this.src=\'' + defaultImage + '\'">';
  cardHtml += availabilityBadge;
  cardHtml += '</div>';
  cardHtml += '<div class="room-card-body">';
  cardHtml += '<div class="room-number"><i class="fa fa-home"></i> Room ' + room.room_number + '</div>';
  cardHtml += '<div class="room-type-badge">' + room.room_type + '</div>';
  cardHtml += '<div class="room-details">';
  cardHtml += '<div class="room-detail-item"><i class="fa fa-users text-primary"></i><span>' + (room.capacity || 1) + ' Guest' + (room.capacity > 1 ? 's' : '') + '</span></div>';
  
  if (room.bed_type) {
    cardHtml += '<div class="room-detail-item"><i class="fa fa-bed text-info"></i><span>' + room.bed_type + '</span></div>';
  }
  
  if (room.floor_location) {
    cardHtml += '<div class="room-detail-item"><i class="fa fa-building text-secondary"></i><span>Floor ' + room.floor_location + '</span></div>';
  }
  
  cardHtml += '</div>';
  cardHtml += '<div class="room-price">';
  cardHtml += '<span class="price-label">Per night</span>';
  const roomPriceUSD = parseFloat(room.price_per_night) || 0;
  const roomPriceTZS = roomPriceUSD * exchangeRate;
  cardHtml += '<span class="price-amount">' + roomPriceTZS.toLocaleString('en-US', {minimumFractionDigits: 0, maximumFractionDigits: 0}) + ' TZS</span>';
  cardHtml += '</div>';
  
  // Show guest slots and info
  // We now only allow 1 guest per room regardless of room type (1 room = 1 booking)
  const roomCapacityForAssignment = 1; 
  const guestsInRoom = bookingData.guests.filter(g => g.room_id == room.id);
  
  cardHtml += '<div class="room-guest-list">';
  
  // Show assigned guest (limit to 1)
  if (guestsInRoom.length > 0) {
    const g = guestsInRoom[0];
    cardHtml += '<div class="room-guest-item assigned" style="border-left: 3px solid #28a745;">';
    cardHtml += '<span><i class="fa fa-user-circle text-success"></i> ' + g.full_name + '</span>';
    cardHtml += '<i class="fa fa-times-circle remove-guest-btn" onclick="removeGuestFromRoom(event, \'' + room.id + '\', 0)" title="Remove guest"></i>';
    cardHtml += '</div>';
  } else {
    // Show single empty slot
    cardHtml += '<div class="room-guest-item empty" style="border-left: 3px solid #dee2e6; color: #999; font-style: italic; cursor: pointer;">';
    cardHtml += '<span><i class="fa fa-user-plus"></i> Assign guest</span>';
    cardHtml += '</div>';
  }
  
  cardHtml += '</div>';
  
  cardHtml += '</div>';
  cardHtml += '</div>';
  
  card.innerHTML = cardHtml;
  
  // Add click handler
  card.addEventListener('click', function(e) {
    // Don't open modal if clicking on remove button
    if (e.target.classList.contains('remove-guest-btn')) return;
    
    if (!canSelect) {
      Swal.fire({
        icon: 'warning',
        title: 'Room Not Ready',
        text: 'This room is currently occupied or needs cleaning and cannot be selected for today.',
        confirmButtonColor: '#940000'
      });
      return;
    }
    
    // Check if room has reached capacity (Now strictly 1 per room)
    const roomCapacityForAssignment = 1;
    const currentGuests = bookingData.guests.filter(g => g.room_id == room.id).length;
    
    // Also check total guests limit
    const totalGuestsNeeded = bookingData.booking.number_of_guests || 0;
    const totalAssigned = bookingData.guests.length;
    
    if (totalAssigned >= totalGuestsNeeded) {
      Swal.fire({
        icon: 'info',
        title: 'Limit Reached',
        text: 'You have already assigned the total number of guests (' + totalGuestsNeeded + ').',
        confirmButtonColor: '#940000'
      });
      return;
    }
    
    if (currentGuests >= roomCapacityForAssignment) {
      Swal.fire({
        icon: 'warning',
        title: 'Room Occupied',
        text: 'This room is already assigned to a guest.',
        confirmButtonColor: '#940000'
      });
      return;
    }
    
    openGuestModal(room.id, room.room_number);
  });
  
  col.appendChild(card);
  return col;
}

// Global removal function
window.removeGuestFromRoom = function(event, roomId, index) {
  event.stopPropagation();
  const guestsInRoom = bookingData.guests.filter(g => g.room_id == roomId);
  const guestToRemove = guestsInRoom[index];
  
  if (guestToRemove) {
    const globalIndex = bookingData.guests.indexOf(guestToRemove);
    if (globalIndex !== -1) {
      bookingData.guests.splice(globalIndex, 1);
      updateGuestAssignmentProgress();
      displayRoomCards(bookingData.rooms, bookingData.room_status, bookingData.booking.room_types_needed);
    }
  }
};

// Open guest assignment modal
function openGuestModal(roomId, roomNumber) {
  document.getElementById('modal_room_id').value = roomId;
  document.getElementById('modal_room_number').textContent = roomNumber;
  
  // Always reset form for a new guest assignment
  const form = document.getElementById('guestAssignmentForm');
  form.reset();
  
  // Clear department checkboxes
  document.querySelectorAll('input[name="guest_notify_departments[]"]').forEach(cb => {
    cb.checked = false;
    updateGuestDepartmentCard(cb);
  });
  
  // Show modal
  $('#guestAssignmentModal').modal('show');
  
  // Populate country dropdown
  setTimeout(function() {
    populateCountryDropdown();
  }, 100);
}

// Save guest assignment
function saveGuestAssignment() {
  const form = document.getElementById('guestAssignmentForm');
  if (!form.checkValidity()) {
    form.reportValidity();
    return;
  }
  
  const roomId = document.getElementById('modal_room_id').value;
  // Get selected departments
  const notifyDepartments = [];
  document.querySelectorAll('input[name="guest_notify_departments[]"]:checked').forEach(cb => {
    notifyDepartments.push(cb.value);
  });
  
  const guestData = {
    room_id: roomId,
    full_name: document.getElementById('guest_full_name').value,
    email: document.getElementById('guest_email').value,
    phone: document.getElementById('guest_phone').value,
    country: document.getElementById('guest_country').value,
    payment_responsibility: document.getElementById('payment_responsibility').value,
    special_requests: document.getElementById('guest_special_requests').value,
    notify_departments: notifyDepartments
  };
  
  // Always push - we allow multiple guests per room up to capacity
  bookingData.guests.push(guestData);
  
  // Update UI
  updateGuestAssignmentProgress();
  displayRoomCards(bookingData.rooms, bookingData.room_status, bookingData.booking.room_types_needed);
  
  $('#guestAssignmentModal').modal('hide');
}

// Update guest assignment progress
function updateGuestAssignmentProgress() {
  // Check if we're on step 4 (room selection step)
  const step4 = document.querySelector('.wizard-step[data-step="4"]');
  if (!step4 || step4.style.display === 'none') {
    return; // Don't update if not on room selection step
  }
  
  const totalGuestsNeeded = bookingData.booking.number_of_guests || 0;
  const assignedGuestsCount = bookingData.guests.length;
  
  // Calculate total capacity of selected rooms
  let totalCapacitySelected = 0;
  if (bookingData.rooms && bookingData.rooms.length > 0) {
    // Only count rooms that are actually selected/displayed (based on roomStatus logic)
    // Actually, it's easier to just sum capacities of all rooms in bookingData.rooms 
    // but only if they were "needed" for the booking.
    // For simplicity, let's just use the count from calculateTotalRooms if available
    const { capacity } = calculateTotalRooms();
    totalCapacitySelected = capacity;
  }
  
  const totalGuestsCountEl = document.getElementById('total_guests_count');
  const guestAssignmentProgressEl = document.getElementById('guest_assignment_progress');
  const proceedBtn = document.getElementById('proceed_to_preview_btn');
  
  if (totalGuestsCountEl) {
    totalGuestsCountEl.textContent = totalGuestsNeeded;
  }
  
  if (guestAssignmentProgressEl) {
    let progressText = `${assignedGuestsCount} of ${totalGuestsNeeded} guests assigned`;
    if (totalCapacitySelected > 0) {
      progressText += ` (Rooms fits up to ${totalCapacitySelected} guests)`;
    }
    guestAssignmentProgressEl.textContent = progressText;
    
    // Update container class based on status
    const container = document.getElementById('guest_progress_container');
    if (container) {
        if (assignedGuestsCount >= totalGuestsNeeded) {
            container.classList.remove('alert-warning', 'alert-info');
            container.classList.add('alert-success');
        } else if (assignedGuestsCount > 0) {
            container.classList.remove('alert-warning', 'alert-success');
            container.classList.add('alert-info');
        } else {
            container.classList.remove('alert-info', 'alert-success');
            container.classList.add('alert-warning');
        }
    }
  }
  
  // Show proceed button only if at least one guest is assigned
  if (proceedBtn) {
    if (assignedGuestsCount > 0) {
      proceedBtn.style.display = 'block';
      
      // Update button text based on assignment status
      if (assignedGuestsCount >= totalGuestsNeeded) {
        proceedBtn.innerHTML = 'Proceed to Payment <i class="fa fa-arrow-right"></i>';
        proceedBtn.classList.remove('btn-warning', 'btn-secondary');
        proceedBtn.classList.add('btn-primary');
        proceedBtn.disabled = false;
      } else {
        proceedBtn.innerHTML = 'Proceed to Preview (' + assignedGuestsCount + '/' + totalGuestsNeeded + ' assigned) <i class="fa fa-arrow-right"></i>';
        proceedBtn.classList.remove('btn-primary', 'btn-secondary');
        proceedBtn.classList.add('btn-warning');
        proceedBtn.disabled = false;
      }
    } else {
      // Hide or disable button if no guests assigned
      proceedBtn.style.display = 'block';
      proceedBtn.innerHTML = 'Assign Guests to Rooms First <i class="fa fa-exclamation-triangle"></i>';
      proceedBtn.classList.remove('btn-primary', 'btn-warning');
      proceedBtn.classList.add('btn-secondary');
      proceedBtn.disabled = true;
    }
  }
}

// Generate preview
function generatePreview() {
  const previewContent = document.getElementById('preview_content');
  
  // Check if rooms are selected
  if (!bookingData.rooms || bookingData.rooms.length === 0) {
    previewContent.innerHTML = '<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Please select rooms first.</div>';
    return;
  }
  
  // Show warning if not all guests are assigned, but allow proceeding
  if (bookingData.guests.length < bookingData.booking.number_of_guests) {
    const unassignedCount = bookingData.booking.number_of_guests - bookingData.guests.length;
    previewContent.innerHTML = '<div class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i> Warning: ' + unassignedCount + ' guest(s) not yet assigned to rooms. You can proceed, but guests should be assigned before finalizing the booking.</div>';
    // Don't return - allow preview to be generated
  }
  
  // Calculate costs
  // Calculate number of nights from check-in and check-out dates
  const checkIn = bookingData.booking.check_in;
  const checkOut = bookingData.booking.check_out;
  const checkInDate = new Date(checkIn);
  const checkOutDate = new Date(checkOut);
  const nights = Math.ceil((checkOutDate - checkInDate) / (1000 * 60 * 60 * 24));
  
  let totalCompanyCost = 0;
  let totalSelfPaidCost = 0;
  
  bookingData.guests.forEach(guest => {
    const room = bookingData.rooms.find(r => r.id == guest.room_id);
    if (room) {
      // Room charges are always paid in TZS (converted from USD)
      const roomPriceUSD = parseFloat(room.price_per_night) || 0;
      const roomCostTZS = roomPriceUSD * exchangeRate * nights; 
      totalCompanyCost += roomCostTZS; 
      
      // Payment responsibility matches business logic (all in TZS)
      // For now, we don't calculate service costs here, but the structure is ready
    }
  });
  
  let previewHtml = '<div class="row">';
  
  // Company Information
  previewHtml += '<div class="col-md-6 mb-3">';
  previewHtml += '<div class="card">';
  previewHtml += '<div class="card-header bg-primary text-white"><h5 class="mb-0"><i class="fa fa-building"></i> Company Information</h5></div>';
  previewHtml += '<div class="card-body">';
  previewHtml += '<p><strong>Name:</strong> ' + bookingData.company.name + '</p>';
  previewHtml += '<p><strong>Email:</strong> ' + bookingData.company.email + '</p>';
  previewHtml += '<p><strong>Phone:</strong> ' + bookingData.company.phone + '</p>';
  previewHtml += '</div></div></div>';
  
  // Guider Information
  previewHtml += '<div class="col-md-6 mb-3">';
  previewHtml += '<div class="card">';
  previewHtml += '<div class="card-header bg-info text-white"><h5 class="mb-0"><i class="fa fa-user"></i> Guider/Leader</h5></div>';
  previewHtml += '<div class="card-body">';
  previewHtml += '<p><strong>Name:</strong> ' + bookingData.guider.name + '</p>';
  previewHtml += '<p><strong>Email:</strong> ' + bookingData.guider.email + '</p>';
  previewHtml += '<p><strong>Phone:</strong> ' + bookingData.guider.phone + '</p>';
  previewHtml += '</div></div></div>';
  
  // Booking Details
  previewHtml += '<div class="col-md-12 mb-3">';
  previewHtml += '<div class="card">';
  previewHtml += '<div class="card-header bg-success text-white"><h5 class="mb-0"><i class="fa fa-calendar"></i> Booking Details</h5></div>';
  previewHtml += '<div class="card-body">';
  previewHtml += '<p><strong>Check-in:</strong> ' + new Date(bookingData.booking.check_in).toLocaleDateString() + '</p>';
  previewHtml += '<p><strong>Check-out:</strong> ' + new Date(bookingData.booking.check_out).toLocaleDateString() + '</p>';
  previewHtml += '<p><strong>Number of Nights:</strong> ' + nights + '</p>';
  previewHtml += '<p><strong>Total Guests:</strong> ' + bookingData.booking.number_of_guests + '</p>';
  const generalNotes = document.getElementById('general_notes');
  if (generalNotes && generalNotes.value) {
    previewHtml += '<p><strong>General Notes:</strong> ' + generalNotes.value + '</p>';
  }
  previewHtml += '</div></div></div>';
  
  // Guests & Rooms
  previewHtml += '<div class="col-md-12 mb-3">';
  previewHtml += '<div class="card">';
  previewHtml += '<div class="card-header bg-warning text-white"><h5 class="mb-0"><i class="fa fa-users"></i> Guests & Room Assignments</h5></div>';
  previewHtml += '<div class="card-body">';
  previewHtml += '<table class="table table-bordered">';
  previewHtml += '<thead><tr><th>Guest Name</th><th>Email</th><th>Phone</th><th>Room</th><th>Payment</th></tr></thead>';
  previewHtml += '<tbody>';
  
  bookingData.guests.forEach(guest => {
    const room = bookingData.rooms.find(r => r.id == guest.room_id);
    previewHtml += '<tr>';
    previewHtml += '<td>' + guest.full_name + '</td>';
    previewHtml += '<td>' + guest.email + '</td>';
    previewHtml += '<td>' + guest.phone + '</td>';
    previewHtml += '<td>Room ' + (room ? room.room_number : 'N/A') + ' (' + (room ? room.room_type : 'N/A') + ')</td>';
    previewHtml += '<td><span class="badge badge-' + (guest.payment_responsibility === 'company' ? 'info' : 'warning') + '">';
    previewHtml += guest.payment_responsibility === 'company' ? 'Company Paid' : 'Self-Paid';
    previewHtml += '</span></td>';
    previewHtml += '</tr>';
  });
  
  previewHtml += '</tbody></table></div></div></div>';
  
  previewHtml += '<div class="col-md-12 mb-3">';
  previewHtml += '<div class="card">';
  previewHtml += '<div class="card-header bg-danger text-white"><h5 class="mb-0"><i class="fa fa-money"></i> Cost Summary (TZS)</h5></div>';
  previewHtml += '<div class="card-body">';
  const totalBookingValueTZS = totalCompanyCost + totalSelfPaidCost;
  previewHtml += '<p><strong>Company Charges (Room + Company Services):</strong> ' + totalCompanyCost.toLocaleString('en-US', {minimumFractionDigits: 0, maximumFractionDigits: 0}) + ' TZS</p>';
  previewHtml += '<p><strong>Self-Paid Charges (Services Only):</strong> ' + totalSelfPaidCost.toLocaleString('en-US', {minimumFractionDigits: 0, maximumFractionDigits: 0}) + ' TZS</p>';
  previewHtml += '<p style="font-size: 1.2em; border-top: 1px solid #eee; padding-top: 10px;"><strong>Total Booking Value:</strong> ' + totalBookingValueTZS.toLocaleString('en-US', {minimumFractionDigits: 0, maximumFractionDigits: 0}) + ' TZS</p>';
  previewHtml += '</div></div></div>';
  
  previewHtml += '</div>';
  
  previewContent.innerHTML = previewHtml;
  
  // Store costs in booking data
  bookingData.booking.total_company_cost = totalCompanyCost;
  bookingData.booking.total_self_paid_cost = totalSelfPaidCost;
  bookingData.booking.total_cost = totalCompanyCost + totalSelfPaidCost;
  bookingData.booking.nights = nights;
  
  // Update payment summary when previewing
  updatePaymentSummary();
}

// Form submission
document.getElementById('corporateBookingForm').addEventListener('submit', function(e) {
  e.preventDefault();
  
  if (bookingData.guests.length < bookingData.booking.number_of_guests) {
    const { capacity } = calculateTotalRooms();
    Swal.fire({
      icon: 'question',
      title: 'Incomplete Assignment',
      text: 'You have only assigned ' + bookingData.guests.length + ' of ' + bookingData.booking.number_of_guests + ' guest names. (Your selected rooms can fit up to ' + capacity + ' guests). Do you want to proceed with current assignments?',
      showCancelButton: true,
      confirmButtonColor: '#940000',
      confirmButtonText: 'Yes, proceed',
      cancelButtonText: 'No, assign more'
    }).then((result) => {
      if (result.isConfirmed) {
        processSubmission();
      }
    });
    return;
  }
  
  processSubmission();
});

function processSubmission() {
  const form = document.getElementById('corporateBookingForm');
  // Prepare form data
  const formData = new FormData();
  formData.append('_token', '{{ csrf_token() }}');
  formData.append('company_name', bookingData.company.name);
  formData.append('company_email', bookingData.company.email);
  formData.append('company_phone', bookingData.company.phone);
  formData.append('guider_name', bookingData.guider.name);
  formData.append('guider_email', bookingData.guider.email);
  formData.append('guider_phone', bookingData.guider.phone);
  formData.append('check_in', bookingData.booking.check_in);
  formData.append('check_out', bookingData.booking.check_out);
  formData.append('number_of_guests', bookingData.booking.number_of_guests);
  formData.append('general_notes', document.getElementById('general_notes').value || '');
  formData.append('guests', JSON.stringify(bookingData.guests));
  formData.append('rooms', JSON.stringify(bookingData.rooms));
  
  // Add payment information
  const paymentMethod = document.getElementById('payment_method').value;
  const paymentProvider = document.getElementById('payment_provider').value;
  const paymentReference = document.getElementById('payment_reference').value;
  const amountPaid = parseFloat(document.getElementById('amount_paid').value) || 0;
  const totalPrice = parseFloat(document.getElementById('total_price').value) || 0;
  const recommendedPrice = parseFloat(document.getElementById('recommended_price').value) || 0;
  
  formData.append('payment_method', paymentMethod);
  formData.append('payment_provider', paymentProvider);
  formData.append('payment_reference', paymentReference);
  formData.append('amount_paid', amountPaid);
  formData.append('total_price', totalPrice);
  formData.append('recommended_price', recommendedPrice);
  
  // Show loading
  const submitBtn = form.querySelector('button[type="submit"]');
  const originalText = submitBtn.innerHTML;
  submitBtn.disabled = true;
  submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Submitting...';
  
  fetch('{{ route("admin.bookings.corporate.store") }}', {
    method: 'POST',
    body: formData,
    headers: {
      'X-Requested-With': 'XMLHttpRequest',
      'Accept': 'application/json'
    }
  })
    .then(async response => {
      // Check if response is JSON
      const contentType = response.headers.get('content-type');
      if (!contentType || !contentType.includes('application/json')) {
        const text = await response.text();
        let errorMessage = 'Server returned an error';
        try {
          // Try to extract error message from HTML
          const parser = new DOMParser();
          const doc = parser.parseFromString(text, 'text/html');
          const errorElement = doc.querySelector('.exception-message, .error-message, h1');
          if (errorElement) {
            errorMessage = errorElement.textContent.trim();
          }
        } catch (e) {
          errorMessage = text.substring(0, 200);
        }
        throw new Error(errorMessage);
      }
      return response.json();
    })
    .then(data => {
      if (data.success) {
        let receiptMessage = 'Corporate booking created successfully!\n\n';
        receiptMessage += data.bookings_count + ' booking(s) created.\n';
        receiptMessage += 'Emails sent to company, guider, and all guests.\n\n';
        if (data.receipt_url) {
          receiptMessage += 'Receipts have been generated.';
        }
        
        Swal.fire({
          icon: 'success',
          title: 'Success!',
          html: receiptMessage.replace(/\n/g, '<br>'),
          confirmButtonColor: '#940000',
          confirmButtonText: 'View Receipt',
          showCancelButton: true,
          cancelButtonText: 'Go to Bookings',
          cancelButtonColor: '#6c757d'
        }).then((result) => {
          if (result.isConfirmed && data.receipt_url) {
            // Open receipt in new window
            window.open(data.receipt_url, '_blank');
            // Also redirect to bookings after a short delay
            setTimeout(() => {
              window.location.href = '{{ route("admin.bookings.index") }}';
            }, 1000);
          } else {
            window.location.href = '{{ route("admin.bookings.index") }}';
          }
        });
      } else {
        let errorMessage = data.message || 'Error creating booking. Please try again.';
        if (data.errors) {
          const errorList = Object.values(data.errors).flat();
          errorMessage += '\n\n' + errorList.join('\n');
        }
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: errorMessage,
          confirmButtonColor: '#940000',
          confirmButtonText: 'OK'
        });
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
      }
    })
    .catch(error => {
      console.error('Error:', error);
      Swal.fire({
        icon: 'error',
        title: 'Error',
        html: 'Error creating booking: ' + error.message + '<br><br>Please check the console for more details.',
        confirmButtonColor: '#940000',
        confirmButtonText: 'OK'
      });
      submitBtn.disabled = false;
      submitBtn.innerHTML = originalText;
    });
}
</script>
<script src="{{ asset('dashboard_assets/js/plugins/select2.min.js') }}"></script>
<script>
// Simple dropdown - no Select2 needed

// Complete country list with flags
const countries = [
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

// Custom country dropdown (replacing Select2 for better modal compatibility)
// Populate country dropdown with all countries
function populateCountryDropdown() {
  const countrySelect = document.getElementById('guest_country');
  if (!countrySelect) return;
  
  // Clear existing options except the first one
  countrySelect.innerHTML = '<option value="">Select a country...</option>';
  
  // Add all countries
  countries.forEach(country => {
    const option = document.createElement('option');
    option.value = country.name;
    option.textContent = country.flag + ' ' + country.name;
    countrySelect.appendChild(option);
  });
}

// Populate country dropdown when modal is shown
jQuery(document).ready(function($) {
  // Populate countries on page load
  populateCountryDropdown();
  
  $('#guestAssignmentModal').on('shown.bs.modal', function() {
    // Ensure countries are populated
    populateCountryDropdown();
    
    // Pre-fill country if editing existing guest
    setTimeout(function() {
      const existingGuest = bookingData.guests.find(g => {
        const modalRoomId = document.getElementById('modal_room_id');
        return modalRoomId && g.room_id == modalRoomId.value;
      });
      
      if (existingGuest && existingGuest.country) {
        const countrySelect = document.getElementById('guest_country');
        if (countrySelect) {
          countrySelect.value = existingGuest.country;
        }
      }
    }, 100);
  });
  
  // Clean up when modal is hidden
  $('#guestAssignmentModal').on('hidden.bs.modal', function() {
    // Reset country dropdown
    const countrySelect = document.getElementById('guest_country');
    if (countrySelect) {
      countrySelect.value = '';
    }
  });
});

// Guest Department Notification Functions
function toggleGuestDepartment(checkboxId) {
  const checkbox = document.getElementById(checkboxId);
  if (checkbox) {
    checkbox.checked = !checkbox.checked;
    updateGuestDepartmentCard(checkbox);
  }
}

function updateGuestDepartmentCard(checkbox) {
  const card = checkbox.closest('.department-card');
  const badgeId = checkbox.id.replace('notify_guest_', 'guest-') + '-badge';
  const badge = document.getElementById(badgeId);
  
  if (checkbox.checked) {
    card.style.borderColor = '#2196F3';
    card.style.backgroundColor = '#f0f8ff';
    if (badge) badge.style.display = 'inline-block';
  } else {
    card.style.borderColor = '#e0e0e0';
    card.style.backgroundColor = 'white';
    if (badge) badge.style.display = 'none';
  }
}

function selectAllGuestDepartments() {
  document.querySelectorAll('input[name="guest_notify_departments[]"]').forEach(cb => {
    cb.checked = true;
    updateGuestDepartmentCard(cb);
  });
}

function deselectAllGuestDepartments() {
  document.querySelectorAll('input[name="guest_notify_departments[]"]').forEach(cb => {
    cb.checked = false;
    updateGuestDepartmentCard(cb);
  });
}

// Payment Calculation Functions
function updatePaymentSummary() {
  const totalCompanyCostTZS = bookingData.booking.total_company_cost || 0;
  const totalSelfPaidCostTZS = bookingData.booking.total_self_paid_cost || 0;
  const totalAmountTZS = totalCompanyCostTZS + totalSelfPaidCostTZS;
  
  // Update payment breakdown (TZS)
  const companyTotalTzsEl = document.getElementById('payment_company_total_tzs');
  const selfTotalTzsEl = document.getElementById('payment_self_total_tzs');
  const totalAmountTzsEl = document.getElementById('payment_total_amount_tzs');
  
  if (companyTotalTzsEl) companyTotalTzsEl.textContent = totalCompanyCostTZS.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
  if (selfTotalTzsEl) selfTotalTzsEl.textContent = totalSelfPaidCostTZS.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
  if (totalAmountTzsEl) totalAmountTzsEl.textContent = totalAmountTZS.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
  
  // Set recommended price (TZS)
  const recommendedPriceEl = document.getElementById('recommended_price');
  const totalPriceEl = document.getElementById('total_price');
  
  if (recommendedPriceEl) {
    recommendedPriceEl.value = totalAmountTZS.toFixed(2);
  }
  
  // Set total price default to recommended if empty
  if (totalPriceEl && !totalPriceEl.value) {
    totalPriceEl.value = totalAmountTZS.toFixed(2);
  }
  
  // Calculate payment percentage and remaining amount
  calculatePaymentDetails();
}

function calculatePaymentDetails() {
  const totalPriceTZS = parseFloat(document.getElementById('total_price').value) || 0;
  const amountPaidTZS = parseFloat(document.getElementById('amount_paid').value) || 0;
  
  // Calculate payment percentage
  const paymentPercentage = totalPriceTZS > 0 ? ((amountPaidTZS / totalPriceTZS) * 100) : 0;
  const paymentPercentageEl = document.getElementById('payment_percentage');
  if (paymentPercentageEl) paymentPercentageEl.value = paymentPercentage.toFixed(2);
  
  // Calculate remaining amount (TZS)
  const remainingAmountTZS = Math.max(0, totalPriceTZS - amountPaidTZS);
  const remainingAmountEl = document.getElementById('remaining_amount');
  if (remainingAmountEl) remainingAmountEl.value = remainingAmountTZS.toFixed(2);
}

// Event listeners for payment calculations
document.addEventListener('DOMContentLoaded', function() {
  const totalPriceEl = document.getElementById('total_price');
  const amountPaidEl = document.getElementById('amount_paid');
  
  if (totalPriceEl) {
    totalPriceEl.addEventListener('input', calculatePaymentDetails);
  }
  
  if (amountPaidEl) {
    amountPaidEl.addEventListener('input', calculatePaymentDetails);
  }
  
  // Payment providers data
  const paymentProviders = {
    'mobile': ['M-Pesa', 'Tigo Pesa', 'Airtel Money', 'HaloPesa', 'Ezy Pesa'],
    'bank': ['CRDB Bank', 'NMB Bank', 'NBC Bank', 'Stanbic Bank', 'Absa Bank', 'KCB Bank', 'Equity Bank', 'Diamond Trust Bank'],
    'card': ['Visa', 'MasterCard', 'American Express', 'UnionPay'],
    'online': ['PayPal', 'Stripe', 'DPO Group', 'Pesapal']
  };

  // Payment method change handler
  const paymentMethodEl = document.getElementById('payment_method');
  const paymentProviderGroup = document.getElementById('payment_provider_group');
  const paymentProviderSelect = document.getElementById('payment_provider');
  
  if (paymentMethodEl && paymentProviderGroup && paymentProviderSelect) {
    paymentMethodEl.addEventListener('change', function() {
      const method = this.value;
      
      // Clear existing options
      paymentProviderSelect.innerHTML = '<option value="">Select Provider</option>';
      
      if (paymentProviders[method]) {
        paymentProviderGroup.style.display = 'block';
        paymentProviderSelect.required = true;
        
        // Populate providers
        paymentProviders[method].forEach(provider => {
          const option = document.createElement('option');
          option.value = provider;
          option.textContent = provider;
          paymentProviderSelect.appendChild(option);
        });
      } else {
        paymentProviderGroup.style.display = 'none';
        paymentProviderSelect.required = false;
      }
    });
  }

  // Company Search Functionality
  const companySearchInput = document.getElementById('companySearchInput');
  const companySearchResults = document.getElementById('companySearchResults');

  if (companySearchInput) {
    let debounceTimer;
    companySearchInput.addEventListener('input', function() {
      clearTimeout(debounceTimer);
      const query = this.value;
      
      if (query.length < 2) {
        companySearchResults.style.display = 'none';
        return;
      }

      debounceTimer = setTimeout(() => {
        fetch(`{{ route('admin.bookings.search.companies') }}?q=${encodeURIComponent(query)}`)
          .then(response => response.json())
          .then(data => {
            companySearchResults.innerHTML = '';
            if (data.length > 0) {
              data.forEach(company => {
                const item = document.createElement('a');
                item.href = 'javascript:void(0)';
                item.className = 'list-group-item list-group-item-action';
                item.innerHTML = `
                  <div class="d-flex w-100 justify-content-between">
                    <h6 class="mb-1">${company.name}</h6>
                    <small class="text-info"><i class="fa fa-building"></i> Company</small>
                  </div>
                  <p class="mb-1 small text-muted"><i class="fa fa-envelope"></i> ${company.email} | <i class="fa fa-phone"></i> ${company.phone || 'N/A'}</p>
                `;
                item.onclick = () => fillCompanyData(company);
                companySearchResults.appendChild(item);
              });
              companySearchResults.style.display = 'block';
            } else {
              companySearchResults.innerHTML = '<div class="list-group-item text-muted">No companies found</div>';
              companySearchResults.style.display = 'block';
            }
          });
      }, 300);
    });

    // Close results when clicking outside
    document.addEventListener('click', function(e) {
      if (!companySearchInput.contains(e.target) && !companySearchResults.contains(e.target)) {
        companySearchResults.style.display = 'none';
      }
    });
  }

  window.fillCompanyData = function(company) {
    document.getElementById('company_name').value = company.name;
    document.getElementById('company_email').value = company.email;
    document.getElementById('company_phone').value = company.phone || '';
    
    // Also fill guider info if it's the same person or if we have it
    if (company.contact_person) {
       document.getElementById('guider_name').value = company.contact_person;
    }
    if (company.guider_email) {
       document.getElementById('guider_email').value = company.guider_email;
    }
    if (company.guider_phone) {
       document.getElementById('guider_phone').value = company.guider_phone;
    }

    companySearchResults.style.display = 'none';
    companySearchInput.value = '';
    
    // Show success message
    // Show success message
    if (typeof Swal !== 'undefined') {
      Swal.fire({
        title: "Company Recognized!",
        text: `Welcome back, ${company.name}! Their details have been auto-filled.`,
        icon: "success",
        timer: 3000,
        showConfirmButton: false
      });
    }
  }
});
</script>
@endsection
