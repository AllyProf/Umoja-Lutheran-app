@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-users"></i> Users Management</h1>
    <p>Manage employees and guests</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">Users</a></li>
  </ul>
</div>

<!-- Statistics Cards -->
<div class="row mb-3">
  <div class="col-md-3">
    <div class="widget-small info coloured-icon">
      <i class="icon fa fa-users fa-2x"></i>
      <div class="info">
        <h4>Total Users</h4>
        <p><b>{{ $stats['total'] ?? 0 }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="widget-small danger coloured-icon">
      <i class="icon fa fa-user-secret fa-2x"></i>
      <div class="info">
        <h4>Employees</h4>
        <p><b>{{ $stats['employees'] ?? 0 }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="widget-small warning coloured-icon">
      <i class="icon fa fa-user-md fa-2x"></i>
      <div class="info">
        <h4>Managers</h4>
        <p><b>{{ $stats['managers'] ?? 0 }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="widget-small success coloured-icon">
      <i class="icon fa fa-user fa-2x"></i>
      <div class="info">
        <h4>Guests</h4>
        <p><b>{{ $stats['guests'] ?? 0 }}</b></p>
      </div>
    </div>
  </div>
</div>

<!-- Tabs Navigation -->
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <ul class="nav nav-tabs" id="userTabs" role="tablist">
        <li class="nav-item">
          <a class="nav-link {{ ($tab ?? 'guests') == 'guests' ? 'active' : '' }}" 
             id="guests-tab" 
             data-toggle="tab" 
             href="#guests" 
             role="tab" 
             aria-controls="guests" 
             aria-selected="{{ ($tab ?? 'guests') == 'guests' ? 'true' : 'false' }}">
            <i class="fa fa-users"></i> Guests
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ ($tab ?? '') == 'employees' ? 'active' : '' }}" 
             id="employees-tab" 
             data-toggle="tab" 
             href="#employees" 
             role="tab" 
             aria-controls="employees" 
             aria-selected="{{ ($tab ?? '') == 'employees' ? 'true' : 'false' }}">
            <i class="fa fa-user-secret"></i> Employees
          </a>
        </li>
      </ul>
    </div>
  </div>
</div>

<!-- Tab Content -->
<div class="tab-content" id="userTabsContent">
  <!-- Guests Tab -->
  <div class="tab-pane fade {{ ($tab ?? 'guests') == 'guests' ? 'show active' : '' }}" 
       id="guests" 
       role="tabpanel" 
       aria-labelledby="guests-tab">
    
    <!-- Search Filter for Guests -->
    <div class="row mb-3">
      <div class="col-md-12">
        <div class="tile">
          <div class="tile-body">
            <div class="row">
              <div class="col-md-10">
                <div class="form-group">
                  <label for="search_guest">Search Guests</label>
                  <input type="text" id="search_guest" class="form-control" 
                         placeholder="Search by name or email..." 
                         onkeyup="filterGuests()">
                </div>
              </div>
              <div class="col-md-2">
                <div class="form-group">
                  <label>&nbsp;</label>
                  <button type="button" class="btn btn-secondary btn-block" onclick="resetGuestFilters()">
                    <i class="fa fa-refresh"></i> Reset
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Guests Table -->
    <div class="row">
      <div class="col-md-12">
        <div class="tile">
          <div class="tile-title-w-btn">
            <h3 class="title"><i class="fa fa-users"></i> Guests</h3>
          </div>
          <div class="tile-body">
            <div class="table-responsive">
              <table class="table table-hover table-bordered">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Type / Company</th>
                    <th>Bookings</th>
                    <th>Total Spent</th>
                    <th>Activity Range</th>
                    <th>Member Since</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($guests as $guest)
                  <tr class="guest-row"
                      data-guest-name="{{ strtolower($guest->name) }}"
                      data-guest-email="{{ strtolower($guest->email) }}">
                    <td>
                      <strong>{{ $guest->name }}</strong>
                    </td>
                    <td>{{ $guest->email }}</td>
                    <td>
                      @if($guest->company)
                        <span class="badge badge-primary" style="font-size: 11px;">
                            <i class="fa fa-building"></i> {{ $guest->company->name }}
                        </span>
                        <br><small class="text-muted">Corporate Guest</small>
                      @else
                        <span class="badge badge-secondary" style="font-size: 11px;">Individual</span>
                      @endif
                    </td>
                    <td>
                      <div class="d-flex flex-column">
                        <span class="text-info" style="font-weight: 600;">{{ $guest->total_bookings ?? 0 }} Total</span>
                        <small class="text-success">{{ $guest->paid_bookings ?? 0 }} Paid</small>
                      </div>
                    </td>
                    <td>
                      <strong class="text-dark">{{ number_format($guest->total_spent ?? 0, 0) }} TZS</strong>
                    </td>
                    <td>
                      @if($guest->first_booking && $guest->last_booking)
                        <small><b>First:</b> {{ \Carbon\Carbon::parse($guest->first_booking->created_at)->format('M d, Y') }}</small>
                        <br>
                        <small><b>Last:</b> {{ \Carbon\Carbon::parse($guest->last_booking->created_at)->format('M d, Y') }}</small>
                      @else
                        <span class="text-muted">No activity</span>
                      @endif
                    </td>
                    <td>{{ $guest->created_at->format('M d, Y') }}</td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="7" class="text-center">
                      <i class="fa fa-users fa-3x text-muted mb-2"></i>
                      <p>No guests found</p>
                    </td>
                  </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
            
            <!-- Pagination -->
            <div class="mt-3">
              {{ $guests->appends(['tab' => 'guests', 'search_guest' => request('search_guest')])->links() }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Employees Tab -->
  <div class="tab-pane fade {{ ($tab ?? '') == 'employees' ? 'show active' : '' }}" 
       id="employees" 
       role="tabpanel" 
       aria-labelledby="employees-tab">
    
    <!-- Search Filter for Employees -->
    <div class="row mb-3">
      <div class="col-md-12">
        <div class="tile">
          <div class="tile-body">
            <div class="row">
              <div class="col-md-10">
                <div class="form-group">
                  <label for="search_employee">Search Employees</label>
                  <input type="text" id="search_employee" class="form-control" 
                         placeholder="Search by name or email..." 
                         onkeyup="filterEmployees()">
                </div>
              </div>
              <div class="col-md-2">
                <div class="form-group">
                  <label>&nbsp;</label>
                  <button type="button" class="btn btn-secondary btn-block" onclick="resetEmployeeFilters()">
                    <i class="fa fa-refresh"></i> Reset
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Employees Table -->
    <div class="row">
      <div class="col-md-12">
        <div class="tile">
          <div class="tile-title-w-btn">
            <h3 class="title"><i class="fa fa-user-secret"></i> Employees</h3>
          </div>
          <div class="tile-body">
            <div class="table-responsive">
              <table class="table table-hover table-bordered">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Registered</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($employees as $employee)
                  <tr class="employee-row"
                      data-employee-name="{{ strtolower($employee->name) }}"
                      data-employee-email="{{ strtolower($employee->email) }}"
                      data-employee-role="{{ strtolower($employee->role) }}">
                    <td>{{ $employee->id }}</td>
                    <td><strong>{{ $employee->name }}</strong></td>
                    <td>{{ $employee->email }}</td>
                    <td>
                      @php
                        $userRoleNormalized = strtolower(str_replace([' ', '_'], '', trim($employee->role ?? '')));
                      @endphp
                      @if($employee->role == 'manager' || $userRoleNormalized === 'manager')
                        <span class="badge badge-warning"><i class="fa fa-user-tie"></i> Manager</span>
                      @elseif($employee->role == 'super_admin' || $userRoleNormalized === 'superadmin' || strtolower($employee->role ?? '') === 'super admin')
                        <span class="badge badge-danger"><i class="fa fa-shield"></i> Super Admin</span>
                      @elseif($employee->role == 'reception' || $userRoleNormalized === 'reception' || strtolower($employee->role ?? '') === 'reception')
                        <span class="badge badge-info"><i class="fa fa-user-md"></i> Reception</span>
                      @else
                        <span class="badge badge-secondary"><i class="fa fa-user"></i> {{ $employee->role ?? 'Employee' }}</span>
                      @endif
                    </td>
                    <td>{{ $employee->created_at->format('M d, Y') }}</td>
                    <td>
                      <span class="badge badge-success">Active</span>
                    </td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="6" class="text-center">
                      <i class="fa fa-user-secret fa-3x text-muted mb-2"></i>
                      <p>No employees found</p>
                    </td>
                  </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
            
            <!-- Pagination -->
            <div class="mt-3">
              {{ $employees->appends(['tab' => 'employees', 'search_employee' => request('search_employee')])->links() }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script>
  // Handle tab switching via URL parameter
  $(document).ready(function() {
    // Update tab links to include tab parameter
    $('#guests-tab').on('click', function() {
      var url = new URL(window.location.href);
      url.searchParams.set('tab', 'guests');
      window.history.pushState({}, '', url);
    });
    
    $('#employees-tab').on('click', function() {
      var url = new URL(window.location.href);
      url.searchParams.set('tab', 'employees');
      window.history.pushState({}, '', url);
    });
  });

  function filterGuests() {
    const searchInput = document.getElementById('search_guest').value.toLowerCase();
    
    const rows = document.querySelectorAll('.guest-row');
    let visibleCount = 0;
    
    rows.forEach(row => {
      const guestName = row.getAttribute('data-guest-name');
      const guestEmail = row.getAttribute('data-guest-email');
      
      let show = true;
      
      // Search filter
      if (searchInput) {
        if (!guestName.includes(searchInput) && 
            !guestEmail.includes(searchInput)) {
          show = false;
        }
      }
      
      row.style.display = show ? '' : 'none';
      if (show) visibleCount++;
    });
    
    // Show/hide "no results" message
    const tbody = document.querySelector('#guests .table-responsive tbody');
    if (tbody) {
      let noResultsRow = tbody.querySelector('.no-results-row');
      
      if (visibleCount === 0 && searchInput) {
        if (!noResultsRow) {
          noResultsRow = document.createElement('tr');
          noResultsRow.className = 'no-results-row';
          noResultsRow.innerHTML = `
            <td colspan="7" class="text-center">
              <i class="fa fa-search fa-3x text-muted mb-2"></i>
              <p>No guests found matching "${searchInput}"</p>
            </td>
          `;
          tbody.appendChild(noResultsRow);
        }
      } else {
        if (noResultsRow) {
          noResultsRow.remove();
        }
      }
    }
  }

  function resetGuestFilters() {
    document.getElementById('search_guest').value = '';
    filterGuests();
  }

  function filterEmployees() {
    const searchInput = document.getElementById('search_employee').value.toLowerCase();
    
    const rows = document.querySelectorAll('.employee-row');
    let visibleCount = 0;
    
    rows.forEach(row => {
      const employeeName = row.getAttribute('data-employee-name');
      const employeeEmail = row.getAttribute('data-employee-email');
      const employeeRole = row.getAttribute('data-employee-role');
      
      let show = true;
      
      // Search filter
      if (searchInput) {
        if (!employeeName.includes(searchInput) && 
            !employeeEmail.includes(searchInput) &&
            !employeeRole.includes(searchInput)) {
          show = false;
        }
      }
      
      row.style.display = show ? '' : 'none';
      if (show) visibleCount++;
    });
    
    // Show/hide "no results" message
    const tbody = document.querySelector('#employees .table-responsive tbody');
    if (tbody) {
      let noResultsRow = tbody.querySelector('.no-results-row');
      
      if (visibleCount === 0 && searchInput) {
        if (!noResultsRow) {
          noResultsRow = document.createElement('tr');
          noResultsRow.className = 'no-results-row';
          noResultsRow.innerHTML = `
            <td colspan="6" class="text-center">
              <i class="fa fa-search fa-3x text-muted mb-2"></i>
              <p>No employees found matching "${searchInput}"</p>
            </td>
          `;
          tbody.appendChild(noResultsRow);
        }
      } else {
        if (noResultsRow) {
          noResultsRow.remove();
        }
      }
    }
  }

  function resetEmployeeFilters() {
    document.getElementById('search_employee').value = '';
    filterEmployees();
  }
</script>
@endsection
