@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-list"></i> Day Services</h1>
    <p>View and manage all day services</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ $role === 'reception' ? route('reception.dashboard') : route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">Day Services</a></li>
  </ul>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-title-w-btn mb-3">
        <h3 class="title">All Day Services</h3>
        <div class="btn-group">
          <div class="btn-group">
            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="fa fa-plus"></i> Register Service
            </button>
            <div class="dropdown-menu">
              <a class="dropdown-item" href="{{ $role === 'reception' ? route('reception.day-services.swimming') : route('admin.day-services.swimming') }}">
                <i class="fa fa-tint"></i> Swimming Service
              </a>
              <a class="dropdown-item" href="{{ $role === 'reception' ? route('reception.day-services.ceremony') : route('admin.day-services.ceremony') }}">
                <i class="fa fa-birthday-cake"></i> Ceremony Service
              </a>
            </div>
          </div>
          <a class="btn btn-warning" href="{{ $role === 'reception' ? route('reception.day-services.pending') : route('admin.day-services.pending') }}">
            <i class="fa fa-clock-o"></i> Pending Payments
          </a>
        </div>
      </div>
      
      <!-- Service Type Tabs -->
      <div class="mb-4">
        <ul class="nav nav-tabs nav-tabs-lg" id="serviceTypeTabs" role="tablist" style="border-bottom: 2px solid #940000;">
          <li class="nav-item">
            <a class="nav-link {{ !request('tab') || request('tab') == 'all' ? 'active' : '' }}" 
               href="{{ $role === 'reception' ? route('reception.day-services.index', array_merge(request()->except('tab'), ['tab' => 'all'])) : route('admin.day-services.index', array_merge(request()->except('tab'), ['tab' => 'all'])) }}"
               style="color: #666; font-weight: 600; padding: 12px 24px; border: none; border-bottom: 3px solid transparent; {{ !request('tab') || request('tab') == 'all' ? 'border-bottom-color: #940000; color: #940000;' : '' }}">
              <i class="fa fa-list"></i> All Services
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ request('tab') == 'swimming' ? 'active' : '' }}" 
               href="{{ $role === 'reception' ? route('reception.day-services.index', array_merge(request()->except('tab'), ['tab' => 'swimming'])) : route('admin.day-services.index', array_merge(request()->except('tab'), ['tab' => 'swimming'])) }}"
               style="color: #666; font-weight: 600; padding: 12px 24px; border: none; border-bottom: 3px solid transparent; {{ request('tab') == 'swimming' ? 'border-bottom-color: #940000; color: #940000;' : '' }}">
              <i class="fa fa-tint"></i> Swimming
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ request('tab') == 'swimming_with_bucket' || request('tab') == 'swimming-with-bucket' ? 'active' : '' }}" 
               href="{{ $role === 'reception' ? route('reception.day-services.index', array_merge(request()->except('tab'), ['tab' => 'swimming_with_bucket'])) : route('admin.day-services.index', array_merge(request()->except('tab'), ['tab' => 'swimming_with_bucket'])) }}"
               style="color: #666; font-weight: 600; padding: 12px 24px; border: none; border-bottom: 3px solid transparent; {{ (request('tab') == 'swimming_with_bucket' || request('tab') == 'swimming-with-bucket') ? 'border-bottom-color: #940000; color: #940000;' : '' }}">
              <i class="fa fa-tint"></i> Swimming with Floating Trey
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ request('tab') == 'ceremony' || request('tab') == 'ceremory' ? 'active' : '' }}" 
               href="{{ $role === 'reception' ? route('reception.day-services.index', array_merge(request()->except('tab'), ['tab' => 'ceremony'])) : route('admin.day-services.index', array_merge(request()->except('tab'), ['tab' => 'ceremony'])) }}"
               style="color: #666; font-weight: 600; padding: 12px 24px; border: none; border-bottom: 3px solid transparent; {{ (request('tab') == 'ceremony' || request('tab') == 'ceremory') ? 'border-bottom-color: #940000; color: #940000;' : '' }}">
              <i class="fa fa-birthday-cake"></i> Ceremony
            </a>
          </li>
        </ul>
      </div>

      <!-- Statistics Cards -->
      <div class="row mb-4">
        <div class="col-md-3">
          <div class="widget-small primary coloured-icon">
            <i class="icon fa fa-list fa-3x"></i>
            <div class="info">
              <h4>Total Services</h4>
              <p><b>{{ number_format($statistics['total_services'] ?? 0) }}</b></p>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="widget-small info coloured-icon">
            <i class="icon fa fa-check-circle fa-3x"></i>
            <div class="info">
              <h4>Paid Services</h4>
              <p><b>{{ number_format($statistics['paid_services'] ?? 0) }}</b></p>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="widget-small warning coloured-icon">
            <i class="icon fa fa-clock-o fa-3x"></i>
            <div class="info">
              <h4>Pending Services</h4>
              <p><b>{{ number_format($statistics['pending_services'] ?? 0) }}</b></p>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="widget-small success coloured-icon">
            <i class="icon fa fa-money fa-3x"></i>
            <div class="info">
              <h4 style="color: #000;">Total Revenue</h4>
              <p><b style="color: #000;">{{ number_format($statistics['total_revenue'] ?? 0, 2) }} TZS</b></p>
            </div>
          </div>
        </div>
      </div>

      @if(($statistics['pending_amount'] ?? 0) > 0)
      <div class="row mb-3">
        <div class="col-md-12">
          <div class="alert alert-warning">
            <i class="fa fa-exclamation-triangle"></i> <strong>Pending Amount:</strong> {{ number_format($statistics['pending_amount'] ?? 0, 2) }} TZS ({{ $statistics['pending_services'] ?? 0 }} service(s) awaiting payment)
          </div>
        </div>
      </div>
      @endif
      
      <!-- Filters -->
      <div class="row mb-3">
        <div class="col-md-12">
          <div class="tile">
            <div class="tile-body">
              <form method="GET" action="#" onsubmit="return false;">
                <div class="row">
                  <div class="col-md-2">
                    <div class="form-group">
                      <label for="payment_status"><strong>Payment Status:</strong></label>
                      <select id="payment_status" name="payment_status" class="form-control" onchange="applyFilters()">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-2">
                    <div class="form-group">
                      <label for="date_from"><strong>Date From:</strong></label>
                      <input type="date" id="date_from" name="date_from" class="form-control" value="{{ request('date_from') }}" onchange="applyFilters()">
                    </div>
                  </div>
                  <div class="col-md-2">
                    <div class="form-group">
                      <label for="date_to"><strong>Date To:</strong></label>
                      <input type="date" id="date_to" name="date_to" class="form-control" value="{{ request('date_to') }}" onchange="applyFilters()">
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="search"><strong>Search:</strong></label>
                      <input type="text" id="search" name="search" class="form-control" 
                             placeholder="Search by name, reference..." 
                             value="{{ request('search') }}"
                             oninput="debounceSearch()">
                      <small class="form-text text-muted">Type to search in real-time</small>
                    </div>
                  </div>
                  <div class="col-md-2">
                    <div class="form-group">
                      <label>&nbsp;</label>
                      <button type="button" class="btn btn-secondary btn-block" onclick="resetFilters()">
                        <i class="fa fa-refresh"></i> Reset
                      </button>
                    </div>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      
      @if($dayServices->count() > 0)
      <!-- Desktop Table View -->
      <div class="table-responsive">
        <table class="table table-hover table-bordered">
          <thead>
            <tr>
              <th>Reference</th>
              <th>Service Type</th>
              <th>Guest Name</th>
              <th>Phone</th>
              <th>Date & Time</th>
              <th>People</th>
              <th>Amount</th>
              <th>Status</th>
              <th>Payment Status</th>
              <th>Payment Method</th>
              <th>Registered By</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($dayServices as $service)
            <tr>
              <td><strong>{{ $service->service_reference }}</strong></td>
              <td>
                <span class="badge badge-info">{{ $service->service_type_name }}</span>
              </td>
              <td>{{ $service->guest_name }}</td>
              <td>{{ str_replace('+255+255', '+255', $service->guest_phone ?? 'N/A') }}</td>
              <td data-date="{{ $service->service_date->format('Y-m-d') }}">
                {{ $service->service_date->format('M d, Y') }}<br>
                <small class="text-muted">{{ $service->service_time }}</small>
              </td>
              <td>{{ $service->number_of_people }}</td>
              <td>
                @if($service->guest_type === 'tanzanian')
                  {{ number_format($service->amount, 2) }} TZS
                @else
                  ${{ number_format($service->amount, 2) }}
                  @if($service->exchange_rate)
                    <br><small class="text-muted">≈ {{ number_format($service->amount * $service->exchange_rate, 2) }} TZS</small>
                  @endif
                @endif
                
                @if($service->discount_amount > 0)
                  <div class="small text-danger mt-1">
                    <span style="text-decoration: line-through; color: #999;">
                      {{ number_format($service->amount + $service->discount_amount) }}
                    </span>
                    <br>
                    -{{ number_format($service->discount_amount) }} (Disc.)
                  </div>
                @endif
              </td>
              <td>
                @if($service->payment_status === 'paid')
                  <span class="badge badge-success">Paid</span>
                @else
                  <span class="badge badge-warning">Pending</span>
                @endif
              </td>
              <td>
                @php
                  $pmClass = 'secondary';
                  $pm = strtolower($service->payment_method ?? '');
                  if($pm === 'cash') $pmClass = 'info';
                  elseif($pm === 'card') $pmClass = 'primary';
                  elseif($pm === 'mobile') $pmClass = 'warning';
                  elseif($pm === 'bank') $pmClass = 'dark';
                  elseif($pm === 'online') $pmClass = 'success';
                @endphp
                <span class="badge badge-{{ $pmClass }}">{{ $service->payment_method_name ?? 'N/A' }}</span>
                @if($service->payment_provider)
                  <br><small class="text-muted">{{ $service->payment_provider }}</small>
                @endif
              </td>
              <td>
                @php
                  $barUsage = $service->serviceRequests->sum('total_price_tsh');
                  $barPaid = $service->serviceRequests->where('payment_status', 'paid')->sum('total_price_tsh');
                  $barUnpaid = $service->serviceRequests->where('payment_status', 'pending')->sum('total_price_tsh');
                @endphp
                @if($barUsage > 0)
                  <div class="small">
                    <div>Total: <strong>{{ number_format($barUsage) }}</strong></div>
                    @if($barPaid > 0)
                      <div class="text-success">Paid: {{ number_format($barPaid) }}</div>
                    @endif
                    @if($barUnpaid > 0)
                      <div class="text-danger font-weight-bold">Unpaid: {{ number_format($barUnpaid) }}</div>
                    @endif
                  </div>
                @else
                  <span class="text-muted italic small">None</span>
                @endif
              </td>
              <td>{{ $service->registeredBy->name ?? 'N/A' }}</td>
              <td>
                <div class="btn-group">
                  <button class="btn btn-sm btn-info" onclick="viewService({{ $service->id }})" title="View Details">
                    <i class="fa fa-eye"></i>
                  </button>
                  @php
                    $serviceKey = strtolower($service->service_type ?? '');
                    $isCeremonyService = str_contains($serviceKey, 'ceremony') || 
                                        str_contains($serviceKey, 'ceremory') || 
                                        str_contains($serviceKey, 'birthday') || 
                                        str_contains($serviceKey, 'package');
                  @endphp
                  @if($isCeremonyService)
                  <button class="btn btn-sm btn-primary" onclick="editServiceItems({{ $service->id }})" title="Edit Items & Prices">
                    <i class="fa fa-edit"></i>
                  </button>
                  <a href="{{ $role === 'reception' ? route('reception.day-services.docket', $service) : route('admin.day-services.docket', $service) }}" 
                     class="btn btn-sm btn-secondary" target="_blank" title="Print Bill/Docket">
                    <i class="fa fa-print"></i>
                  </a>
                  @endif
                  @if($service->payment_status === 'paid')
                  <a href="{{ $role === 'reception' ? route('reception.day-services.receipt', $service) : route('admin.day-services.receipt', $service) }}" 
                     class="btn btn-sm btn-success" target="_blank" title="Download Receipt">
                    <i class="fa fa-download"></i>
                  </a>
                  @endif

                  @php
                      $hasUnpaidWeb = $isCeremonyService && $service->serviceRequests->where('payment_status', 'pending')->count() > 0;
                  @endphp

                  @if($service->payment_status !== 'paid')
                    @if($service->service_type === 'restaurant' || $service->service_type === 'bar' || $isCeremonyService)
                      <button class="btn btn-sm btn-warning" onclick="processPayment({{ $service->id }})" title="Process Payment (Service Fee)">
                        <i class="fa fa-money"></i>
                      </button>
                    @endif
                  @endif

                  @if($hasUnpaidWeb)
                    <button class="btn btn-sm btn-success" onclick="openSettleUsageModal({{ $service->id }}, '{{ addslashes($service->guest_name ?? '') }}', {{ $service->serviceRequests->where('payment_status', 'pending')->sum('total_price_tsh') }})" title="Settle Usage: {{ number_format($service->serviceRequests->where('payment_status', 'pending')->sum('total_price_tsh')) }} TZS">
                      <i class="fa fa-cutlery"></i>
                    </button>
                  @endif
                </div>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      
      <!-- Pagination -->
      <div class="d-flex justify-content-center mt-3">
        {{ $dayServices->links() }}
      </div>
      @else
      <div class="text-center" style="padding: 50px;">
        <i class="fa fa-list fa-5x text-muted mb-3"></i>
        <h3>No Day Services Found</h3>
        <p class="text-muted">No day services match your filters.</p>
        <p class="text-muted">Use the "Register Service" button above to create a new service.</p>
      </div>
      @endif
    </div>
  </div>
</div>

<!-- Service Details Modal -->
<div class="modal fade" id="serviceDetailsModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background: #940000; color: white;">
        <h5 class="modal-title">Service Details</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="serviceDetailsContent">
        <!-- Content will be loaded here -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Process Payment Modal -->
<div class="modal fade" id="processPaymentModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background: #ffc107; color: white;">
        <h5 class="modal-title"><i class="fa fa-money"></i> Process Payment</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="paymentForm">
          <input type="hidden" id="payment_service_id" name="service_id">

          <div class="form-group">
            <label for="payment_amount">Amount <span class="text-danger">*</span></label>
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text" id="payment_currency_symbol">TZS</span>
              </div>
              <input class="form-control" type="number" id="payment_amount" name="amount" step="0.01" min="0" required>
            </div>
          </div>
          <div class="form-group">
            <label for="payment_method">Payment Method <span class="text-danger">*</span></label>
            <select class="form-control" id="payment_method" name="payment_method" required>
              <option value="">Select payment method...</option>
              <option value="cash">Cash</option>
              <option value="card">Card</option>
              <option value="mobile">Mobile Money</option>
              <option value="bank">Bank Transfer</option>
              <option value="online">Online Payment</option>
              <option value="other">Other</option>
            </select>
          </div>
          <div class="row" id="payment_provider_section" style="display: none;">
            <div class="col-md-6">
              <div class="form-group">
                <label for="payment_provider">Payment Provider</label>
                <select class="form-control" id="payment_provider" name="payment_provider">
                  <option value="">Select provider...</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="payment_reference">Reference Number</label>
                <input class="form-control" type="text" id="payment_reference" name="payment_reference" placeholder="Enter transaction reference">
              </div>
            </div>
          </div>
          <div class="form-group">
            <label for="payment_amount_paid">Amount Paid <span class="text-danger">*</span></label>
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text" id="payment_paid_currency_symbol">TZS</span>
              </div>
              <input class="form-control" type="number" id="payment_amount_paid" name="amount_paid" step="0.01" min="0" required>
            </div>
          </div>
          <div id="paymentAlert"></div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-warning" onclick="submitPayment()">
          <i class="fa fa-check"></i> Process Payment
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Add Additional Items Modal -->
<div class="modal fade" id="addItemsModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background: #28a745; color: white;">
        <h5 class="modal-title"><i class="fa fa-plus"></i> Add Additional Items</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="addItemsForm">
          <input type="hidden" id="add_items_service_id" name="service_id">
          
          <div class="alert alert-info">
            <i class="fa fa-info-circle"></i> Add additional items that were not included in the original ceremony package.
          </div>
          
          <div id="additionalItemsContainer">
            <!-- Additional items will be added here dynamically -->
          </div>
          
          <div class="row mt-3">
            <div class="col-md-12">
              <button type="button" class="btn btn-sm btn-outline-primary" onclick="addItemRow()">
                <i class="fa fa-plus"></i> Add Item
              </button>
            </div>
          </div>
          
          <div class="row mt-3">
            <div class="col-md-6">
              <div class="form-group">
                <label for="additional_total_amount">Total Additional Amount <span class="text-danger">*</span></label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="add_items_currency_symbol">TZS</span>
                  </div>
                  <input class="form-control" type="number" id="additional_total_amount" name="additional_amount" 
                         step="0.01" min="0" required readonly>
                </div>
                <small class="form-text text-muted">Calculated from items above</small>
              </div>
            </div>
          </div>
          
          <h5 class="mt-4 mb-3"><i class="fa fa-money"></i> Payment Information</h5>
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="add_items_payment_method">Payment Method <span class="text-danger">*</span></label>
                <select class="form-control" id="add_items_payment_method" name="payment_method" required onchange="toggleAddItemsPaymentProvider()">
                  <option value="">Select payment method...</option>
                  <option value="cash">Cash</option>
                  <option value="card">Card</option>
                  <option value="mobile">Mobile Money</option>
                  <option value="bank">Bank Transfer</option>
                  <option value="online">Online Payment</option>
                  <option value="other">Other</option>
                </select>
              </div>
            </div>
          </div>
          
          <div class="row" id="add_items_payment_provider_section" style="display: none;">
            <div class="col-md-6">
              <div class="form-group">
                <label for="add_items_payment_provider">Payment Provider / Platform <span id="add_items_provider_required" class="text-danger"></span></label>
                <select class="form-control" id="add_items_payment_provider" name="payment_provider">
                  <option value="">Select provider...</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="add_items_payment_reference">Reference Number / Transaction ID <span id="add_items_reference_required" class="text-danger"></span></label>
                <input class="form-control" type="text" id="add_items_payment_reference" name="payment_reference" placeholder="Enter transaction reference">
              </div>
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="add_items_amount_paid">Amount Paid <span class="text-danger">*</span></label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="add_items_paid_currency_symbol">TZS</span>
                  </div>
                  <input class="form-control" type="number" id="add_items_amount_paid" name="additional_payment" 
                         step="0.01" min="0" required>
                </div>
              </div>
            </div>
          </div>
          
          <div id="addItemsAlert"></div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-success" onclick="submitAddItems()">
          <i class="fa fa-check"></i> Add Items & Process Payment
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Edit Service Items Modal -->
<div class="modal fade" id="editItemsModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background: #007bff; color: white;">
        <h5 class="modal-title"><i class="fa fa-edit"></i> Edit Service Items & Prices</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="editItemsForm">
          <input type="hidden" id="edit_items_service_id" name="service_id">
          
          <div class="alert alert-info">
            <i class="fa fa-info-circle"></i> Update prices for existing items or add new items that were not included in the original ceremony package.
          </div>
          
          <h5 class="mb-3"><i class="fa fa-gift"></i> Package Items</h5>
          <div id="packageItemsContainer">
            <!-- Package items will be loaded here dynamically -->
          </div>
          
          <h5 class="mt-4 mb-3"><i class="fa fa-plus-circle"></i> Additional Items</h5>
          <div id="editAdditionalItemsContainer">
            <!-- Additional items will be loaded here dynamically -->
          </div>
          
          <div class="row mt-3">
            <div class="col-md-12">
              <button type="button" class="btn btn-sm btn-outline-primary" onclick="addEditItemRow()">
                <i class="fa fa-plus"></i> Add New Item
              </button>
            </div>
          </div>
          
          <div class="row mt-3">
            <div class="col-md-6">
              <div class="form-group">
                <label for="edit_total_amount"><strong>Total Amount <span class="text-danger">*</span></strong></label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="edit_items_currency_symbol">TZS</span>
                  </div>
                  <input class="form-control" type="number" id="edit_total_amount" name="total_amount" 
                         step="0.01" min="0" required readonly>
                </div>
                <small class="form-text text-muted">Calculated from all items above</small>
              </div>
            </div>
          </div>
          
          <h5 class="mt-4 mb-3"><i class="fa fa-money"></i> Payment Information (if amount changed)</h5>
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="edit_items_payment_method">Payment Method</label>
                <select class="form-control" id="edit_items_payment_method" name="payment_method" onchange="toggleEditItemsPaymentProvider()">
                  <option value="">Select payment method (if updating payment)...</option>
                  <option value="cash">Cash</option>
                  <option value="card">Card</option>
                  <option value="mobile">Mobile Money</option>
                  <option value="bank">Bank Transfer</option>
                  <option value="online">Online Payment</option>
                  <option value="other">Other</option>
                </select>
                <small class="form-text text-muted">Optional: Only fill if updating payment method</small>
              </div>
            </div>
          </div>
          
          <div class="row" id="edit_items_payment_provider_section" style="display: none;">
            <div class="col-md-6">
              <div class="form-group">
                <label for="edit_items_payment_provider">Payment Provider / Platform</label>
                <select class="form-control" id="edit_items_payment_provider" name="payment_provider">
                  <option value="">Select provider...</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="edit_items_payment_reference">Reference Number / Transaction ID</label>
                <input class="form-control" type="text" id="edit_items_payment_reference" name="payment_reference" placeholder="Enter transaction reference">
              </div>
            </div>
          </div>
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="edit_items_amount_paid">Amount Paid</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text" id="edit_items_paid_currency_symbol">TZS</span>
                  </div>
                  <input class="form-control" type="number" id="edit_items_amount_paid" name="amount_paid" 
                         step="0.01" min="0">
                </div>
                <small class="form-text text-muted">Optional: Only fill if updating payment amount</small>
              </div>
            </div>
          </div>
          
          <div id="editItemsAlert"></div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="submitEditItemsBtn" onclick="submitEditItems()">
          <i class="fa fa-save"></i> Save Changes
        </button>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script src="{{ asset('dashboard_assets/js/plugins/sweetalert.min.js') }}"></script>
<script>
// Client-side search and filtering (no page refresh)
let searchTimeout;

function debounceSearch() {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(function() {
    filterTable();
  }, 300); // Wait 300ms after user stops typing
}

function applyFilters() {
  filterTable();
}

function filterTable() {
  const paymentStatus = document.getElementById('payment_status').value.toLowerCase();
  const dateFrom = document.getElementById('date_from').value;
  const dateTo = document.getElementById('date_to').value;
  const search = document.getElementById('search').value.toLowerCase().trim();
  
  const tableRows = document.querySelectorAll('table tbody tr');
  let visibleCount = 0;
  
  tableRows.forEach(row => {
    let show = true;
    
    // Payment status filter
    if (paymentStatus) {
      const statusCell = row.querySelector('td:nth-child(8)'); // Payment Status column
      if (statusCell) {
        const statusText = statusCell.textContent.toLowerCase().trim();
        if (!statusText.includes(paymentStatus)) {
          show = false;
        }
      }
    }
    
    // Date filter - check data attribute or extract from text
    if (dateFrom || dateTo) {
      const dateCell = row.querySelector('td:nth-child(5)'); // Date column
      if (dateCell) {
        // Try to get date from data attribute first
        const dateValue = dateCell.getAttribute('data-date') || dateCell.textContent.trim();
        const cellDate = dateValue ? new Date(dateValue) : null;
        
        if (cellDate && !isNaN(cellDate.getTime())) {
          if (dateFrom) {
            const fromDate = new Date(dateFrom);
            fromDate.setHours(0, 0, 0, 0);
            if (cellDate < fromDate) {
              show = false;
            }
          }
          if (dateTo) {
            const toDate = new Date(dateTo);
            toDate.setHours(23, 59, 59, 999);
            if (cellDate > toDate) {
              show = false;
            }
          }
        }
      }
    }
    
    // Search filter
    if (search) {
      const cells = row.querySelectorAll('td');
      let found = false;
      cells.forEach(cell => {
        const cellText = cell.textContent.toLowerCase().trim();
        if (cellText.includes(search)) {
          found = true;
        }
      });
      if (!found) {
        show = false;
      }
    }
    
    // Show/hide row
    if (show) {
      row.style.display = '';
      visibleCount++;
    } else {
      row.style.display = 'none';
    }
  });
  
  // Show/hide empty message
  const emptyMessage = document.querySelector('.no-results-message');
  if (visibleCount === 0 && (paymentStatus || dateFrom || dateTo || search)) {
    if (!emptyMessage) {
      const table = document.querySelector('table');
      if (table) {
        const emptyRow = document.createElement('tr');
        emptyRow.className = 'no-results-message';
        emptyRow.innerHTML = '<td colspan="11" style="text-align: center; padding: 40px; color: #999;"><i class="fa fa-search"></i> No services match your filters.</td>';
        table.querySelector('tbody').appendChild(emptyRow);
      }
    }
  } else {
    if (emptyMessage) {
      emptyMessage.remove();
    }
  }
}

function extractDateFromText(text) {
  // Try to extract date from text like "Jan 15, 2024"
  const dateMatch = text.match(/(\w+)\s+(\d+),\s+(\d+)/);
  if (dateMatch) {
    const monthNames = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];
    const month = monthNames.indexOf(dateMatch[1].toLowerCase());
    const day = parseInt(dateMatch[2]);
    const year = parseInt(dateMatch[3]);
    if (month !== -1) {
      return new Date(year, month, day);
    }
  }
  return null;
}

function resetFilters() {
  document.getElementById('payment_status').value = '';
  document.getElementById('date_from').value = '';
  document.getElementById('date_to').value = '';
  document.getElementById('search').value = '';
  filterTable();
}

function viewService(serviceId) {
  $('#serviceDetailsModal').modal('show');
  document.getElementById('serviceDetailsContent').innerHTML = `
    <div class="text-center">
      <i class="fa fa-spinner fa-spin fa-2x"></i>
      <p>Loading service details...</p>
    </div>
  `;
  
  @php
    $showRoute = ($role === 'reception') ? 'reception.day-services.show' : 'admin.day-services.show';
  @endphp
  
  fetch('{{ route($showRoute, ":id") }}'.replace(':id', serviceId), {
    method: 'GET',
    headers: {
      'Accept': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    }
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      const service = data.day_service;
      const serviceDate = new Date(service.service_date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
      
      // Format service time
      let serviceTime = 'N/A';
      if (service.service_time) {
        try {
          const timeDate = new Date(service.service_time);
          if (!isNaN(timeDate.getTime())) {
            serviceTime = timeDate.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
          } else {
            // Try parsing as time string
            const timeStr = service.service_time.toString();
            if (timeStr.includes(':')) {
              const parts = timeStr.split(':');
              if (parts.length >= 2) {
                const hours = parseInt(parts[0]);
                const minutes = parseInt(parts[1]);
                const ampm = hours >= 12 ? 'PM' : 'AM';
                const displayHours = hours % 12 || 12;
                serviceTime = `${displayHours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')} ${ampm}`;
              }
            }
          }
        } catch (e) {
          serviceTime = service.service_time;
        }
      }
      
      // Get service type name
      let serviceTypeName = service.service_type_name || service.service_type || 'Unknown';
      const serviceKey = (service.service_type || '').toLowerCase();
      if (serviceKey.includes('ceremony') || serviceKey.includes('ceremory') || serviceKey.includes('birthday') || serviceKey.includes('package')) {
        serviceTypeName = 'Ceremony/Birthday Package';
      } else if (serviceKey === 'swimming') {
        serviceTypeName = 'Swimming/Pool Access';
      } else if (!service.service_type_name) {
        serviceTypeName = service.service_type ? service.service_type.charAt(0).toUpperCase() + service.service_type.slice(1) : 'Unknown';
      }
      
      // Get payment method name
      let paymentMethodName = service.payment_method_name || 'N/A';
      if (paymentMethodName === 'N/A' && service.payment_method) {
        const paymentMethods = {
          'cash': 'Cash',
          'card': 'Card',
          'mobile': 'Mobile Money',
          'bank': 'Bank Transfer',
          'online': 'Online Payment',
          'other': 'Other'
        };
        paymentMethodName = paymentMethods[service.payment_method.toLowerCase()] || service.payment_method;
      }
      
      // Check if this is a ceremony service
      const isCeremonyService = serviceKey.includes('ceremony') || 
                              serviceKey.includes('ceremory') || 
                              serviceKey.includes('birthday') || 
                              serviceKey.includes('package');
      
      // Build package & consumption items display
      let packageItemsHtml = '';
      const itemLabels = {
        'food': 'Food',
        'swimming': 'Swimming',
        'drinks': 'Drinks',
        'photos': 'Photos',
        'decoration': 'Decoration'
      };
      
      packageItemsHtml = '<tr><td><strong>Items & Usage:</strong></td><td><ul class="mb-0">';
      
      // 1. Add Package Items
      if (service.package_items && typeof service.package_items === 'object') {
        for (const [key, price] of Object.entries(service.package_items)) {
          const label = itemLabels[key] || key.charAt(0).toUpperCase() + key.slice(1);
          packageItemsHtml += `<li><i class="fa fa-gift mr-1 text-info" title="Package Item"></i> ${label}: ${service.guest_type === 'tanzanian' ? parseFloat(price).toFixed(2) + ' TZS' : '$' + parseFloat(price).toFixed(2)}</li>`;
        }
      }

      // 2. Add Bar/Restaurant Consumption Items
      if (service.service_requests && service.service_requests.length > 0) {
          service.service_requests.forEach(req => {
              const itemName = (req.service_specific_data && req.service_specific_data.item_name) 
                               ? req.service_specific_data.item_name 
                               : (req.service ? req.service.name : 'Consumption Item');
              const total = parseFloat(req.total_price_tsh).toLocaleString();
              packageItemsHtml += `<li><i class="fa fa-beer mr-1 text-primary" title="Bar Usage"></i> ${itemName} (x${req.quantity}): ${total} TZS</li>`;
          });
      }

      if (packageItemsHtml.includes('<li>')) {
          packageItemsHtml += '</ul></td></tr>';
      } else {
          packageItemsHtml = '<tr><td><strong>Items:</strong></td><td><span class="text-muted italic">None recorded</span></td></tr>';
      }
      
      // Add Items button removed - use Edit Items button in the table instead
      const addItemsButton = '';
      
      document.getElementById('serviceDetailsContent').innerHTML = `
        <div class="row">
          <div class="col-md-6">
            <h5><i class="fa fa-info-circle"></i> Service Information</h5>
            <table class="table table-sm table-bordered">
              <tr><td><strong>Reference:</strong></td><td>${service.service_reference}</td></tr>
              <tr><td><strong>Service Type:</strong></td><td><span class="badge badge-info">${serviceTypeName}</span></td></tr>
              <tr><td><strong>Date:</strong></td><td>${serviceDate}</td></tr>
              <tr><td><strong>Time:</strong></td><td>${serviceTime}</td></tr>
              <tr><td><strong>Number of People:</strong></td><td>${service.number_of_people}</td></tr>
              ${packageItemsHtml}
              ${service.items_ordered ? `<tr><td><strong>Items Ordered:</strong></td><td>${service.items_ordered}</td></tr>` : ''}
            </table>
          </div>
          <div class="col-md-6">
            <h5><i class="fa fa-user"></i> Guest Information</h5>
            <table class="table table-sm table-bordered">
              <tr><td><strong>Name:</strong></td><td>${service.guest_name}</td></tr>
              <tr><td><strong>Phone:</strong></td><td>${service.guest_phone || 'N/A'}</td></tr>
              <tr><td><strong>Email:</strong></td><td>${service.guest_email || 'N/A'}</td></tr>
              <tr><td><strong>Guest Type:</strong></td><td>${service.guest_type === 'tanzanian' ? 'Tanzanian' : 'International'}</td></tr>
            </table>
          </div>
        </div>
        
        <!-- Bar/Restaurant Consumption Section -->
        <div class="row mt-3">
          <div class="col-md-12">
            <h5><i class="fa fa-cutlery"></i> Bar & Restaurant Consumption</h5>
            <div class="table-responsive">
              <table class="table table-sm table-bordered">
                <thead class="bg-light">
                  <tr>
                    <th>Item</th>
                    <th>Detail</th>
                    <th>Qty</th>
                    <th class="text-right">Price (TZS)</th>
                    <th class="text-right">Total (TZS)</th>
                  </tr>
                </thead>
                <tbody id="barConsumptionBody">
                  <!-- Will be filled by JS -->
                </tbody>
                <tfoot>
                  <tr>
                    <td colspan="4" class="text-right">Total Consumption:</td>
                    <td class="text-right" id="barUsageTotal">0 TZS</td>
                  </tr>
                  <tr class="text-success">
                    <td colspan="4" class="text-right">Amount Paid:</td>
                    <td class="text-right" id="barPaidTotal">0 TZS</td>
                  </tr>
                  <tr class="text-danger font-weight-bold" style="font-size: 1.1em; background: #fff5f5;">
                    <td colspan="4" class="text-right">AMOUNT UNPAID:</td>
                    <td class="text-right" id="barUnpaidTotal">0 TZS</td>
                  </tr>
                </tfoot>
              </table>
            </div>
            <div id="noUsageMessage" class="text-muted italic small" style="display: none;">
              No bar/restaurant usage recorded for this ceremony yet.
            </div>
            <div id="settleButtonContainer" class="mt-3" style="display: none;">
              <button class="btn btn-success btn-block" onclick="openManagerCeremonyPaymentModal()">
                <i class="fa fa-money mr-2"></i>Settle Unpaid Consumption
              </button>
            </div>
          </div>
        </div>

        <div class="row mt-3">
          <div class="col-md-12">
            <h5><i class="fa fa-dollar"></i> Payment Information (Registration)</h5>
            <table class="table table-sm table-bordered">
              <tr><td><strong>Registration Amount:</strong></td><td>${service.guest_type === 'tanzanian' ? service.amount + ' TZS' : '$' + parseFloat(service.amount).toFixed(2) + (service.exchange_rate ? ' (≈ ' + (service.amount * service.exchange_rate).toFixed(2) + ' TZS)' : '')}</td></tr>
              <tr><td><strong>Payment Status:</strong></td><td><span class="badge badge-${service.payment_status === 'paid' ? 'success' : 'warning'}">${service.payment_status === 'paid' ? 'Paid' : 'Pending'}</span></td></tr>
              <tr><td><strong>Payment Method:</strong></td><td>${paymentMethodName}</td></tr>
              ${service.payment_provider ? `<tr><td><strong>Provider:</strong></td><td>${service.payment_provider}</td></tr>` : ''}
              ${service.payment_reference ? `<tr><td><strong>Reference:</strong></td><td>${service.payment_reference}</td></tr>` : ''}
              ${service.amount_paid ? `<tr><td><strong>Amount Paid:</strong></td><td>${service.guest_type === 'tanzanian' ? service.amount_paid + ' TZS' : '$' + parseFloat(service.amount_paid).toFixed(2)}</td></tr>` : ''}
              ${service.paid_at ? `<tr><td><strong>Paid At:</strong></td><td>${new Date(service.paid_at).toLocaleString()}</td></tr>` : ''}
            </table>
          </div>
        </div>

        ${service.notes ? `<div class="row mt-3"><div class="col-md-12"><p><strong>Notes:</strong> ${service.notes}</p></div></div>` : ''}
        ${addItemsButton}
      `;

      // Fill bar consumption table
      const consumptionBody = document.getElementById('barConsumptionBody');
      const totalDisplay = document.getElementById('barUsageTotal');
      const noUsageMsg = document.getElementById('noUsageMessage');
      
      if (service.service_requests && service.service_requests.length > 0) {
          let consumptionHtml = '';
          let totalUsage = 0;
          
          service.service_requests.forEach(req => {
              const itemName = (req.service_specific_data && req.service_specific_data.item_name) 
                               ? req.service_specific_data.item_name 
                               : (req.service ? req.service.name : 'Unknown Item');
              
              const qty = req.quantity || 0;
              const unitPrice = parseFloat(req.unit_price_tsh || 0);
              const total = parseFloat(req.total_price_tsh || 0);
              totalUsage += total;
              
              consumptionHtml += `
                  <tr>
                      <td><strong>${itemName}</strong></td>
                      <td><small class="text-muted">${req.guest_request || '-'}</small></td>
                      <td>${qty}</td>
                      <td class="text-right">${unitPrice.toLocaleString()}</td>
                      <td class="text-right font-weight-bold">${total.toLocaleString()}</td>
                  </tr>
              `;
          });
          
          consumptionBody.innerHTML = consumptionHtml;
          totalDisplay.innerText = totalUsage.toLocaleString() + ' TZS';
          
          // Set paid/unpaid totals
          let paidUsage = 0;
          let unpaidUsage = 0;
          service.service_requests.forEach(req => {
              const total = parseFloat(req.total_price_tsh || 0);
              if (req.payment_status === 'paid') {
                  paidUsage += total;
              } else {
                  unpaidUsage += total;
              }
          });
          
          document.getElementById('barPaidTotal').innerText = paidUsage.toLocaleString() + ' TZS';
          document.getElementById('barUnpaidTotal').innerText = unpaidUsage.toLocaleString() + ' TZS';
          
          // Show/hide settle button based on unpaid amount
          const settleButtonContainer = document.getElementById('settleButtonContainer');
          if (unpaidUsage > 0) {
              settleButtonContainer.style.display = 'block';
              // Store ceremony context for payment modal
              window.currentCeremonyContext = {
                  dayServiceId: service.id,
                  guestName: service.guest_name,
                  unpaidAmount: unpaidUsage
              };
          } else {
              settleButtonContainer.style.display = 'none';
          }
          
          noUsageMsg.style.display = 'none';
      } else {
          consumptionBody.innerHTML = '<tr><td colspan="5" class="text-center py-3 text-muted">No usage recorded.</td></tr>';
          totalDisplay.innerText = '0 TZS';
          document.getElementById('barPaidTotal').innerText = '0 TZS';
          document.getElementById('barUnpaidTotal').innerText = '0 TZS';
          document.getElementById('settleButtonContainer').style.display = 'none';
          
          if (isCeremonyService) {
              noUsageMsg.style.display = 'block';
          }
      }
      
      // Store service data globally for add items modal
      window.currentServiceData = service;
    }
  })
  .catch(error => {
    console.error('Error:', error);
    document.getElementById('serviceDetailsContent').innerHTML = `
      <div class="alert alert-danger">An error occurred while loading service details.</div>
    `;
  });
}

function processPayment(serviceId) {
  $('#processPaymentModal').modal('show');
  document.getElementById('payment_service_id').value = serviceId;
  document.getElementById('paymentForm').reset();
  document.getElementById('paymentAlert').innerHTML = '';
  
  // Load service details
  @php
    $showRoute = ($role === 'reception') ? 'reception.day-services.show' : 'admin.day-services.show';
  @endphp
  
  fetch('{{ route($showRoute, ":id") }}'.replace(':id', serviceId), {
    method: 'GET',
    headers: {
      'Accept': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    }
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      const service = data.day_service;
      // Calculate unpaid consumption
      let unpaidConsumption = 0;
      if (service.service_requests && service.service_requests.length > 0) {
         service.service_requests.forEach(req => {
            if (req.payment_status !== 'paid') {
               unpaidConsumption += parseFloat(req.total_price_tsh || 0);
            }
         });
      }

      let amountToPay = parseFloat(service.amount || 0);
      if (service.payment_status === 'paid') {
         amountToPay = unpaidConsumption;
      }
      
      document.getElementById('payment_amount').value = amountToPay.toFixed(2);
      
      const currencySymbol = service.guest_type === 'tanzanian' ? 'TZS' : 'USD';
      document.getElementById('payment_currency_symbol').textContent = currencySymbol;
      document.getElementById('payment_paid_currency_symbol').textContent = currencySymbol;
    }
  });
}

// Payment method change handler
document.getElementById('payment_method').addEventListener('change', function() {
  const paymentMethod = this.value;
  const providerSection = document.getElementById('payment_provider_section');
  
  if (paymentMethod === 'mobile') {
    providerSection.style.display = 'block';
    document.getElementById('payment_provider').innerHTML = `
      <option value="">Select provider...</option>
      <option value="M-PESA">M-PESA</option>
      <option value="HALOPESA">HALOPESA</option>
      <option value="MIXX BY YAS">MIXX BY YAS</option>
      <option value="AIRTEL MONEY">AIRTEL MONEY</option>
    `;
  } else if (paymentMethod === 'bank') {
    providerSection.style.display = 'block';
    document.getElementById('payment_provider').innerHTML = `
      <option value="">Select provider...</option>
      <option value="NMB">NMB</option>
      <option value="CRDB">CRDB</option>
      <option value="KCB">KCB Bank</option>
      <option value="NBC">NBC</option>
      <option value="EXIM">EXIM</option>
    `;
  } else {
    providerSection.style.display = 'none';
    document.getElementById('payment_provider').value = '';
    document.getElementById('payment_reference').value = '';
  }
});

function submitPayment() {
  const form = document.getElementById('paymentForm');
  const alertDiv = document.getElementById('paymentAlert');
  const submitBtn = event.target;
  const originalText = submitBtn.innerHTML;
  
  if (!form.checkValidity()) {
    form.reportValidity();
    return;
  }
  
  submitBtn.disabled = true;
  submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Processing...';
  
  const serviceId = document.getElementById('payment_service_id').value;
  const formData = {
    items_ordered: document.getElementById('payment_items_ordered') ? document.getElementById('payment_items_ordered').value : null,
    amount: document.getElementById('payment_amount').value,
    payment_method: document.getElementById('payment_method').value,
    payment_provider: document.getElementById('payment_provider').value,
    payment_reference: document.getElementById('payment_reference').value,
    amount_paid: document.getElementById('payment_amount_paid').value,
  };
  
  @php
    $paymentRoute = ($role === 'reception') ? 'reception.day-services.payment' : 'admin.day-services.payment';
  @endphp
  
  fetch('{{ route($paymentRoute, ":id") }}'.replace(':id', serviceId), {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}',
      'Accept': 'application/json'
    },
    body: JSON.stringify(formData)
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
        $('#processPaymentModal').modal('hide');
        if (data.receipt_url) {
          window.open(data.receipt_url, '_blank');
        }
        location.reload();
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
}

let additionalItemCounter = 0;

function openAddItemsModal(serviceId) {
  $('#addItemsModal').modal('show');
  document.getElementById('add_items_service_id').value = serviceId;
  document.getElementById('addItemsForm').reset();
  document.getElementById('additionalItemsContainer').innerHTML = '';
  document.getElementById('addItemsAlert').innerHTML = '';
  additionalItemCounter = 0;
  
  // Get service data
  const service = window.currentServiceData;
  if (service) {
    // Set currency symbol based on guest type
    const currencySymbol = service.guest_type === 'tanzanian' ? 'TZS' : 'USD';
    document.getElementById('add_items_currency_symbol').textContent = currencySymbol;
    document.getElementById('add_items_paid_currency_symbol').textContent = currencySymbol;
  }
  
  // Add first item row
  addItemRow();
}

function addItemRow() {
  additionalItemCounter++;
  const container = document.getElementById('additionalItemsContainer');
  
  const itemHtml = `
    <div class="row additional-item-row mb-2" id="add_item_row_${additionalItemCounter}">
      <div class="col-md-5">
        <div class="form-group">
          <label for="add_item_name_${additionalItemCounter}">Item Name <span class="text-danger">*</span></label>
          <input class="form-control additional-item-name" type="text" 
                 id="add_item_name_${additionalItemCounter}" 
                 name="additional_items[${additionalItemCounter}][name]" 
                 placeholder="Enter item name" required oninput="calculateAdditionalTotal()">
        </div>
      </div>
      <div class="col-md-5">
        <div class="form-group">
          <label for="add_item_price_${additionalItemCounter}">Price <span class="text-danger">*</span></label>
          <div class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text">TZS</span>
            </div>
            <input class="form-control additional-item-price" type="number" 
                   id="add_item_price_${additionalItemCounter}" 
                   name="additional_items[${additionalItemCounter}][price]" 
                   step="0.01" min="0" value="0" 
                   required oninput="calculateAdditionalTotal()">
          </div>
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">
          <label>&nbsp;</label>
          <button type="button" class="btn btn-sm btn-danger btn-block" onclick="removeItemRow(${additionalItemCounter})">
            <i class="fa fa-times"></i>
          </button>
        </div>
      </div>
    </div>
  `;
  
  container.insertAdjacentHTML('beforeend', itemHtml);
}

function removeItemRow(itemId) {
  const itemRow = document.getElementById('add_item_row_' + itemId);
  if (itemRow) {
    itemRow.remove();
    calculateAdditionalTotal();
  }
}

function calculateAdditionalTotal() {
  let total = 0;
  const priceInputs = document.querySelectorAll('.additional-item-price');
  priceInputs.forEach(input => {
    const price = parseFloat(input.value) || 0;
    total += price;
  });
  
  const totalInput = document.getElementById('additional_total_amount');
  if (totalInput) {
    totalInput.value = total.toFixed(2);
  }
  
  // Auto-update amount paid if empty
  const amountPaidInput = document.getElementById('add_items_amount_paid');
  if (amountPaidInput && (!amountPaidInput.value || parseFloat(amountPaidInput.value) === 0)) {
    amountPaidInput.value = total.toFixed(2);
  }
}

function toggleAddItemsPaymentProvider() {
  const paymentMethod = document.getElementById('add_items_payment_method').value;
  const paymentProviderSection = document.getElementById('add_items_payment_provider_section');
  const paymentProviderSelect = document.getElementById('add_items_payment_provider');
  const paymentReferenceInput = document.getElementById('add_items_payment_reference');
  
  if (!paymentMethod || !paymentProviderSection || !paymentProviderSelect || !paymentReferenceInput) return;
  
  // Reset
  paymentProviderSelect.required = false;
  paymentReferenceInput.required = false;
  paymentReferenceInput.placeholder = "Enter transaction reference";
  document.getElementById('add_items_provider_required').textContent = '';
  document.getElementById('add_items_reference_required').textContent = '';

  if (paymentMethod === 'mobile' || paymentMethod === 'bank' || paymentMethod === 'card' || paymentMethod === 'online') {
    paymentProviderSection.style.display = 'block';
    paymentProviderSelect.innerHTML = '<option value="">Select provider...</option>';
    
    if (paymentMethod === 'mobile') {
      paymentProviderSelect.innerHTML += `
        <option value="m-pesa">M-PESA</option>
        <option value="halopesa">HALOPESA</option>
        <option value="mixx-by-yas">MIXX BY YAS</option>
        <option value="airtel-money">AIRTEL MONEY</option>
      `;
      document.getElementById('add_items_provider_required').textContent = '*';
      document.getElementById('add_items_reference_required').textContent = '*';
      paymentReferenceInput.required = true;
      paymentProviderSelect.required = true;
    } else if (paymentMethod === 'bank') {
      paymentProviderSelect.innerHTML += `
        <option value="nmb">NMB</option>
        <option value="crdb">CRDB</option>
        <option value="kcb">KCB Bank</option>
        <option value="exim">Exim Bank</option>
        <option value="equity">Equity Bank</option>
        <option value="stanbic">Stanbic Bank</option>
      `;
      document.getElementById('add_items_provider_required').textContent = '*';
      document.getElementById('add_items_reference_required').textContent = '*';
      paymentReferenceInput.required = true;
      paymentProviderSelect.required = true;
    } else if (paymentMethod === 'card') {
      paymentProviderSelect.innerHTML += `
        <option value="visa">Visa</option>
        <option value="mastercard">Mastercard</option>
        <option value="amex">American Express</option>
      `;
      document.getElementById('add_items_provider_required').textContent = '*';
      paymentProviderSelect.required = true;
    } else if (paymentMethod === 'online') {
      paymentProviderSelect.innerHTML += `
        <option value="paypal">PayPal</option>
        <option value="stripe">Stripe</option>
        <option value="booking-com">Booking.com</option>
        <option value="other">Other Platform</option>
      `;
      document.getElementById('add_items_provider_required').textContent = '*';
      document.getElementById('add_items_reference_required').textContent = '*';
      paymentReferenceInput.required = true;
      paymentProviderSelect.required = true;
    }
  } else {
    paymentProviderSection.style.display = 'none';
    paymentProviderSelect.value = '';
    paymentReferenceInput.value = '';
  }
}

function submitAddItems() {
  const form = document.getElementById('addItemsForm');
  if (!form.checkValidity()) {
    form.reportValidity();
    return;
  }
  
  const serviceId = document.getElementById('add_items_service_id').value;
  const paymentMethod = document.getElementById('add_items_payment_method').value;
  const paymentProvider = document.getElementById('add_items_payment_provider').value;
  const paymentReference = document.getElementById('add_items_payment_reference').value;
  
  // Validate payment method and provider/reference if required
  if (paymentMethod === 'mobile' || paymentMethod === 'bank' || paymentMethod === 'online') {
    if (!paymentProvider) {
      swal({
        title: "Payment Provider Required",
        text: "Please select a payment provider for the chosen method.",
        type: "error",
        confirmButtonColor: "#d33"
      });
      return;
    }
    if (!paymentReference) {
      swal({
        title: "Reference Number Required",
        text: "Please enter a reference number for the chosen payment method.",
        type: "error",
        confirmButtonColor: "#d33"
      });
      return;
    }
  } else if (paymentMethod === 'card') {
    if (!paymentProvider) {
      swal({
        title: "Payment Provider Required",
        text: "Please select a payment provider for card payment.",
        type: "error",
        confirmButtonColor: "#d33"
      });
      return;
    }
  }
  
  // Collect additional items
  const additionalItemsData = {};
  document.querySelectorAll('.additional-item-row').forEach(row => {
    const nameInput = row.querySelector('.additional-item-name');
    const priceInput = row.querySelector('.additional-item-price');
    if (nameInput && priceInput) {
      const name = nameInput.value.trim();
      const price = parseFloat(priceInput.value) || 0;
      if (name && price > 0) {
        additionalItemsData[name] = price;
      }
    }
  });
  
  if (Object.keys(additionalItemsData).length === 0) {
    swal({
      title: "No Items Added",
      text: "Please add at least one item with a price.",
      type: "error",
      confirmButtonColor: "#d33"
    });
    return;
  }
  
  const formData = new FormData(form);
  formData.append('additional_items', JSON.stringify(additionalItemsData));
  
  const submitBtn = form.querySelector('button[type="button"]');
  const originalText = submitBtn.innerHTML;
  submitBtn.disabled = true;
  submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Processing...';
  
  const alertDiv = document.getElementById('addItemsAlert');
  alertDiv.innerHTML = '';
  
  @php
    $addItemsRoute = ($role === 'reception') ? 'reception.day-services.add-items' : 'admin.day-services.add-items';
  @endphp
  
  fetch('{{ route($addItemsRoute, ":id") }}'.replace(':id', serviceId), {
    method: 'POST',
    body: formData,
    headers: {
      'X-Requested-With': 'XMLHttpRequest',
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    }
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      swal({
        title: "Success!",
        text: data.message || "Additional items added successfully!",
        type: "success",
        confirmButtonColor: "#28a745"
      }, function() {
        $('#addItemsModal').modal('hide');
        if (data.receipt_url) {
          window.open(data.receipt_url, '_blank');
        }
        location.reload();
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
}

let editItemCounter = 0;

function editServiceItems(serviceId) {
  $('#editItemsModal').modal('show');
  document.getElementById('edit_items_service_id').value = serviceId;
  document.getElementById('editItemsForm').reset();
  document.getElementById('editItemsAlert').innerHTML = '';
  editItemCounter = 0;
  
  // Load service details
  @php
    $showRoute = ($role === 'reception') ? 'reception.day-services.show' : 'admin.day-services.show';
  @endphp
  
  fetch('{{ route($showRoute, ":id") }}'.replace(':id', serviceId), {
    method: 'GET',
    headers: {
      'Accept': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    }
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      const service = data.day_service;
      
      // Set currency symbol based on guest type
      const currencySymbol = service.guest_type === 'tanzanian' ? 'TZS' : 'USD';
      document.getElementById('edit_items_currency_symbol').textContent = currencySymbol;
      document.getElementById('edit_items_paid_currency_symbol').textContent = currencySymbol;
      
      // Load package items
      const packageItemsContainer = document.getElementById('packageItemsContainer');
      packageItemsContainer.innerHTML = '';
      
      const packageItemLabels = {
        'food': 'Food',
        'swimming': 'Swimming',
        'drinks': 'Drinks',
        'photos': 'Photos',
        'decoration': 'Decoration'
      };
      
      if (service.package_items && typeof service.package_items === 'object') {
        for (const [key, price] of Object.entries(service.package_items)) {
          // Skip if it's already an additional item (has a custom name)
          if (packageItemLabels[key] || key.toLowerCase().match(/^(food|swimming|drinks|photos|decoration)$/i)) {
            const label = packageItemLabels[key] || key.charAt(0).toUpperCase() + key.slice(1);
            const itemHtml = `
              <div class="row package-item-row mb-2" data-item-key="${key}">
                <div class="col-md-4">
                  <div class="form-group mb-0">
                    <label>${label}</label>
                    <input type="hidden" name="package_items[${key}][name]" value="${key}">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group mb-0">
                    <label>Price <span class="text-danger">*</span></label>
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">${currencySymbol}</span>
                      </div>
                      <input class="form-control package-item-price" type="number" 
                             name="package_items[${key}][price]" 
                             step="0.01" min="0" value="${parseFloat(price) || 0}" 
                             required oninput="calculateEditTotal()">
                    </div>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group mb-0">
                    <label>&nbsp;</label>
                    <button type="button" class="btn btn-sm btn-danger btn-block" onclick="removeEditItemRow(this)" disabled title="Cannot remove package items">
                      <i class="fa fa-times"></i>
                    </button>
                  </div>
                </div>
              </div>
            `;
            packageItemsContainer.insertAdjacentHTML('beforeend', itemHtml);
          }
        }
      }
      
      // Load additional items (items not in packageItemLabels)
      const additionalItemsContainer = document.getElementById('editAdditionalItemsContainer');
      additionalItemsContainer.innerHTML = '';
      
      if (service.package_items && typeof service.package_items === 'object') {
        for (const [key, price] of Object.entries(service.package_items)) {
          // If it's not a predefined package item, treat it as additional
          if (!packageItemLabels[key] && !key.toLowerCase().match(/^(food|swimming|drinks|photos|decoration)$/i)) {
            editItemCounter++;
            const itemHtml = `
              <div class="row additional-item-row mb-2" id="edit_item_row_${editItemCounter}" data-item-name="${key}">
                <div class="col-md-4">
                  <div class="form-group mb-0">
                    <label>Item Name <span class="text-danger">*</span></label>
                    <input class="form-control edit-additional-item-name" type="text" 
                           name="additional_items[${editItemCounter}][name]" 
                           value="${key}" required oninput="calculateEditTotal()">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group mb-0">
                    <label>Price <span class="text-danger">*</span></label>
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">${currencySymbol}</span>
                      </div>
                      <input class="form-control edit-additional-item-price" type="number" 
                             name="additional_items[${editItemCounter}][price]" 
                             step="0.01" min="0" value="${parseFloat(price) || 0}" 
                             required oninput="calculateEditTotal()">
                    </div>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group mb-0">
                    <label>&nbsp;</label>
                    <button type="button" class="btn btn-sm btn-danger btn-block" onclick="removeEditItemRow(this)">
                      <i class="fa fa-times"></i>
                    </button>
                  </div>
                </div>
              </div>
            `;
            additionalItemsContainer.insertAdjacentHTML('beforeend', itemHtml);
          }
        }
      }
      
      // Set current amount and amount paid
      document.getElementById('edit_total_amount').value = parseFloat(service.amount || 0).toFixed(2);
      document.getElementById('edit_items_amount_paid').value = parseFloat(service.amount_paid || 0).toFixed(2);
      document.getElementById('edit_items_payment_method').value = service.payment_method || '';
      
      // Recalculate total
      calculateEditTotal();
      
      // Store service data globally
      window.currentEditServiceData = service;
    }
  })
  .catch(error => {
    console.error('Error:', error);
    document.getElementById('editItemsAlert').innerHTML = '<div class="alert alert-danger">An error occurred while loading service details.</div>';
  });
}

function addEditItemRow() {
  editItemCounter++;
  const container = document.getElementById('editAdditionalItemsContainer');
  const service = window.currentEditServiceData;
  const currencySymbol = service && service.guest_type === 'tanzanian' ? 'TZS' : 'USD';
  
  const itemHtml = `
    <div class="row additional-item-row mb-2" id="edit_item_row_${editItemCounter}">
      <div class="col-md-4">
        <div class="form-group mb-0">
          <label>Item Name <span class="text-danger">*</span></label>
          <input class="form-control edit-additional-item-name" type="text" 
                 name="additional_items[${editItemCounter}][name]" 
                 placeholder="Enter item name" required oninput="calculateEditTotal()">
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group mb-0">
          <label>Price <span class="text-danger">*</span></label>
          <div class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text">${currencySymbol}</span>
            </div>
            <input class="form-control edit-additional-item-price" type="number" 
                   name="additional_items[${editItemCounter}][price]" 
                   step="0.01" min="0" value="0" 
                   required oninput="calculateEditTotal()">
          </div>
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group mb-0">
          <label>&nbsp;</label>
          <button type="button" class="btn btn-sm btn-danger btn-block" onclick="removeEditItemRow(this)">
            <i class="fa fa-times"></i>
          </button>
        </div>
      </div>
    </div>
  `;
  
  container.insertAdjacentHTML('beforeend', itemHtml);
}

function removeEditItemRow(button) {
  const row = button.closest('.row');
  if (row) {
    row.remove();
    calculateEditTotal();
  }
}

function calculateEditTotal() {
  let total = 0;
  
  // Calculate from package items
  const packagePriceInputs = document.querySelectorAll('.package-item-price');
  packagePriceInputs.forEach(input => {
    const price = parseFloat(input.value) || 0;
    total += price;
  });
  
  // Calculate from additional items
  const additionalPriceInputs = document.querySelectorAll('.edit-additional-item-price');
  additionalPriceInputs.forEach(input => {
    const price = parseFloat(input.value) || 0;
    total += price;
  });
  
  const totalInput = document.getElementById('edit_total_amount');
  if (totalInput) {
    totalInput.value = total.toFixed(2);
  }
}

function toggleEditItemsPaymentProvider() {
  const paymentMethod = document.getElementById('edit_items_payment_method').value;
  const paymentProviderSection = document.getElementById('edit_items_payment_provider_section');
  const paymentProviderSelect = document.getElementById('edit_items_payment_provider');
  
  if (!paymentMethod || !paymentProviderSection || !paymentProviderSelect) return;
  
  if (paymentMethod === 'mobile' || paymentMethod === 'bank' || paymentMethod === 'card' || paymentMethod === 'online') {
    paymentProviderSection.style.display = 'block';
    paymentProviderSelect.innerHTML = '<option value="">Select provider...</option>';
    
    if (paymentMethod === 'mobile') {
      paymentProviderSelect.innerHTML += `
        <option value="m-pesa">M-PESA</option>
        <option value="halopesa">HALOPESA</option>
        <option value="mixx-by-yas">MIXX BY YAS</option>
        <option value="airtel-money">AIRTEL MONEY</option>
      `;
    } else if (paymentMethod === 'bank') {
      paymentProviderSelect.innerHTML += `
        <option value="nmb">NMB</option>
        <option value="crdb">CRDB</option>
        <option value="kcb">KCB Bank</option>
        <option value="exim">Exim Bank</option>
        <option value="equity">Equity Bank</option>
        <option value="stanbic">Stanbic Bank</option>
      `;
    } else if (paymentMethod === 'card') {
      paymentProviderSelect.innerHTML += `
        <option value="visa">Visa</option>
        <option value="mastercard">Mastercard</option>
        <option value="amex">American Express</option>
      `;
    } else if (paymentMethod === 'online') {
      paymentProviderSelect.innerHTML += `
        <option value="paypal">PayPal</option>
        <option value="stripe">Stripe</option>
        <option value="booking-com">Booking.com</option>
        <option value="other">Other Platform</option>
      `;
    }
  } else {
    paymentProviderSection.style.display = 'none';
    paymentProviderSelect.value = '';
    document.getElementById('edit_items_payment_reference').value = '';
  }
}

function submitEditItems() {
  const form = document.getElementById('editItemsForm');
  if (!form.checkValidity()) {
    form.reportValidity();
    return;
  }
  
  const serviceId = document.getElementById('edit_items_service_id').value;
  const totalAmount = parseFloat(document.getElementById('edit_total_amount').value) || 0;
  const paymentMethod = document.getElementById('edit_items_payment_method').value;
  const paymentProvider = document.getElementById('edit_items_payment_provider').value;
  const paymentReference = document.getElementById('edit_items_payment_reference').value;
  const amountPaid = document.getElementById('edit_items_amount_paid').value ? parseFloat(document.getElementById('edit_items_amount_paid').value) : null;
  
  // Collect package items
  const packageItemsData = {};
  document.querySelectorAll('.package-item-row').forEach(row => {
    const key = row.getAttribute('data-item-key');
    const priceInput = row.querySelector('.package-item-price');
    if (key && priceInput) {
      const price = parseFloat(priceInput.value) || 0;
      packageItemsData[key] = price;
    }
  });
  
  // Collect additional items
  const additionalItemsData = {};
  document.querySelectorAll('.additional-item-row').forEach(row => {
    const nameInput = row.querySelector('.edit-additional-item-name');
    const priceInput = row.querySelector('.edit-additional-item-price');
    if (nameInput && priceInput) {
      const name = nameInput.value.trim();
      const price = parseFloat(priceInput.value) || 0;
      if (name && price > 0) {
        additionalItemsData[name] = price;
      }
    }
  });
  
  // Merge package and additional items
  const allItems = { ...packageItemsData, ...additionalItemsData };
  
  if (Object.keys(allItems).length === 0) {
    swal({
      title: "No Items",
      text: "Please add at least one item with a price.",
      type: "error",
      confirmButtonColor: "#d33"
    });
    return;
  }
  
  const formData = {
    package_items: packageItemsData,
    additional_items: additionalItemsData,
    total_amount: totalAmount,
    payment_method: paymentMethod || null,
    payment_provider: paymentProvider || null,
    payment_reference: paymentReference || null,
    amount_paid: amountPaid
  };
  
  const submitBtn = document.getElementById('submitEditItemsBtn') || form.querySelector('button.btn-primary');
  if (!submitBtn) {
    console.error('Submit button not found');
    return;
  }
  const originalText = submitBtn.innerHTML;
  submitBtn.disabled = true;
  submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Saving...';
  
  const alertDiv = document.getElementById('editItemsAlert');
  alertDiv.innerHTML = '';
  
  @php
    $updateItemsRoute = ($role === 'reception') ? 'reception.day-services.update-items' : 'admin.day-services.update-items';
  @endphp
  
  fetch('{{ route($updateItemsRoute, ":id") }}'.replace(':id', serviceId), {
    method: 'PUT',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': '{{ csrf_token() }}',
      'Accept': 'application/json'
    },
    body: JSON.stringify(formData)
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      swal({
        title: "Success!",
        text: data.message || "Service items updated successfully!",
        type: "success",
        confirmButtonColor: "#28a745"
      }, function() {
        $('#editItemsModal').modal('hide');
        location.reload();
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
}

function openSettleUsageModal(id, name, amount) {
    document.getElementById('managerCeremonyDayServiceId').value = id;
    document.getElementById('managerCeremonyGuestName').innerText = name;
    document.getElementById('managerCeremonyUnpaidAmount').innerText = amount.toLocaleString() + ' TZS';
    
    // Reset fields
    document.getElementById('managerCeremonyPaymentMethod').value = 'cash';
    document.getElementById('managerCeremonyPaymentReference').value = '';
    toggleManagerCeremonyRefField();
    
    $('#managerCeremonyPaymentModal').modal('show');
}

function openManagerCeremonyPaymentModal() {
    if (!window.currentCeremonyContext) {
        swal("Error", "Ceremony context not found. Please try again.", "error");
        return;
    }
    
    const context = window.currentCeremonyContext;
    document.getElementById('managerCeremonyDayServiceId').value = context.dayServiceId;
    document.getElementById('managerCeremonyGuestName').innerText = context.guestName;
    document.getElementById('managerCeremonyUnpaidAmount').innerText = context.unpaidAmount.toLocaleString() + ' TZS';
    document.getElementById('managerCeremonyPaymentMethod').value = 'cash';
    document.getElementById('managerCeremonyPaymentReference').value = '';
    toggleManagerCeremonyRefField();
    $('#managerCeremonyPaymentModal').modal('show');
}

function toggleManagerCeremonyRefField() {
    const method = document.getElementById('managerCeremonyPaymentMethod').value;
    const container = document.getElementById('managerCeremonyRefFieldContainer');
    if (method === 'cash') {
        container.style.display = 'none';
    } else {
        container.style.display = 'block';
    }
}

function submitManagerCeremonyPayment() {
    const dayServiceId = document.getElementById('managerCeremonyDayServiceId').value;
    const method = document.getElementById('managerCeremonyPaymentMethod').value;
    const reference = document.getElementById('managerCeremonyPaymentReference').value.trim();
    
    if (method !== 'cash' && !reference) {
        swal("Missing Info", "Please enter a reference number for " + method.replace('_', ' ').toUpperCase(), "warning");
        return;
    }

    swal({
        title: "Confirm Settlement",
        text: "Mark all unpaid consumption for this ceremony as paid?",
        type: "info",
        showCancelButton: true,
        confirmButtonColor: "#28a745",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Yes, Settle!",
        cancelButtonText: "Cancel",
        closeOnConfirm: false
    }, function(isConfirm) {
        if (isConfirm) {
            const url = `/customer/ceremonies/settle-usage`;
            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    day_service_id: dayServiceId,
                    payment_method: method,
                    payment_reference: reference
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    swal({
                        title: "Success!",
                        text: data.message + ` (${data.count} items settled)`,
                        type: "success",
                        timer: 2000,
                        showConfirmButton: false
                    });
                    setTimeout(() => location.reload(), 1500);
                } else {
                    swal("Error!", data.message, "error");
                }
            })
            .catch(error => {
                console.error('Error:', error);
                swal("Error!", "Failed to settle payment. Please try again.", "error");
            });
        }
    });
}
</script>

<!-- Manager Ceremony Payment Modal -->
<div class="modal fade" id="managerCeremonyPaymentModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title"><i class="fa fa-money mr-2"></i>Settle Ceremony Consumption</h5>
        <button type="button" class="close text-white" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="managerCeremonyDayServiceId">
        <div class="alert alert-info">
          <strong id="managerCeremonyGuestName"></strong><br>
          <small>Total Unpaid Consumption: <strong id="managerCeremonyUnpaidAmount"></strong></small>
        </div>
        <div class="form-group">
          <label>Payment Method <span class="text-danger">*</span></label>
          <select class="form-control" id="managerCeremonyPaymentMethod" onchange="toggleManagerCeremonyRefField()">
            <option value="cash">Cash</option>
            <option value="mpesa">M-Pesa</option>
            <option value="halopesa">Halopesa</option>
            <option value="airtel_money">Airtel Money</option>
            <option value="mixx_by_yass">Mixx by Yass</option>
            <option value="nmb">NMB Bank</option>
            <option value="crdb">CRDB Bank</option>
            <option value="kcb">KCB Bank</option>
          </select>
        </div>
        <div class="form-group" id="managerCeremonyRefFieldContainer" style="display: none;">
          <label>Reference Number <span class="text-danger">*</span></label>
          <input type="text" class="form-control" id="managerCeremonyPaymentReference" placeholder="Enter transaction reference">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-success" onclick="submitManagerCeremonyPayment()"><i class="fa fa-check mr-1"></i>Settle Payment</button>
      </div>
    </div>
  </div>
</div>
@endsection

