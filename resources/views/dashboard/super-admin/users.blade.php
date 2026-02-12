@extends('dashboard.layouts.app')

@section('content')
<style>
  .nav-tabs {
    border-bottom: 2px solid #dee2e6;
    margin-bottom: 0;
  }
  
  .nav-tabs .nav-link {
    color: #495057;
    border: none;
    border-bottom: 3px solid transparent;
    padding: 12px 20px;
    font-weight: 500;
    transition: all 0.3s ease;
  }
  
  .nav-tabs .nav-link:hover {
    border-bottom-color: #007bff;
    color: #007bff;
  }
  
  .nav-tabs .nav-link.active {
    color: #007bff;
    background-color: transparent;
    border-bottom-color: #007bff;
    font-weight: 600;
  }
  
  .nav-tabs .nav-link .badge {
    margin-left: 8px;
    font-size: 0.75rem;
    padding: 2px 6px;
  }
  
  .nav-tabs .nav-link.active .badge {
    background-color: #007bff;
  }
</style>

<div class="app-title">
  <div>
    <h1><i class="fa fa-users"></i> Users Management</h1>
    <p>Manage all system users</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('super_admin.dashboard') }}">Dashboard</a></li>
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
        <p><b>{{ $users->total() ?? 0 }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="widget-small danger coloured-icon">
      <i class="icon fa fa-shield fa-2x"></i>
      <div class="info">
        <h4>Super Admins</h4>
        <p><b>{{ $roleCounts['super_admin'] ?? 0 }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="widget-small warning coloured-icon">
      <i class="icon fa fa-user-tie fa-2x"></i>
      <div class="info">
        <h4>Managers</h4>
        <p><b>{{ $roleCounts['manager'] ?? 0 }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="widget-small success coloured-icon">
      <i class="icon fa fa-user fa-2x"></i>
      <div class="info">
        <h4>Guests</h4>
        <p><b>{{ $roleCounts['guest'] ?? 0 }}</b></p>
      </div>
    </div>
  </div>
</div>

<!-- Filters and Actions -->
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-body">
        <form method="GET" action="{{ route('super_admin.users') }}" class="row">
          <input type="hidden" name="tab" value="{{ $activeTab }}">
          @if($activeTab === 'employees')
          <div class="col-md-4">
            <div class="form-group">
              <label for="role">Filter by Role</label>
              <select name="role" id="role" class="form-control">
                <option value="">All Roles</option>
                <option value="super_admin" {{ request('role') == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                <option value="manager" {{ request('role') == 'manager' ? 'selected' : '' }}>Manager</option>
                <option value="reception" {{ request('role') == 'reception' ? 'selected' : '' }}>Reception</option>
                <option value="waiter" {{ request('role') == 'waiter' ? 'selected' : '' }}>Waiter</option>
                <option value="housekeeper" {{ request('role') == 'housekeeper' ? 'selected' : '' }}>Housekeeper</option>
                <option value="bar_keeper" {{ request('role') == 'bar_keeper' ? 'selected' : '' }}>Bar Keeper</option>
                <option value="head_chef" {{ request('role') == 'head_chef' ? 'selected' : '' }}>Head Chef</option>
              </select>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="search">Search</label>
              <input type="text" name="search" id="search" class="form-control" 
                     placeholder="Search by name or email..." value="{{ request('search') }}">
            </div>
          </div>
          @else
          <div class="col-md-10">
            <div class="form-group">
              <label for="search">Search</label>
              <input type="text" name="search" id="search" class="form-control" 
                     placeholder="Search by name or email..." value="{{ request('search') }}">
            </div>
          </div>
          @endif
          <div class="col-md-2">
            <div class="form-group">
              <label>&nbsp;</label>
              <button type="submit" class="btn btn-primary btn-block">
                <i class="fa fa-search"></i> Filter
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Tabs Navigation -->
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-body">
        <ul class="nav nav-tabs" id="userTabs" role="tablist">
          <li class="nav-item" role="presentation">
            <a class="nav-link {{ $activeTab === 'employees' ? 'active' : '' }}" 
               href="{{ route('super_admin.users', array_merge(request()->except('page'), ['tab' => 'employees'])) }}" 
               id="employees-tab">
              <i class="fa fa-user-tie"></i> Umoj Lutheran Hostel Staffs 
              <span class="badge badge-primary">{{ $activeTab === 'employees' ? $users->total() : ($roleCounts['total_employees'] ?? 0) }}</span>
            </a>
          </li>
          <li class="nav-item" role="presentation">
            <a class="nav-link {{ $activeTab === 'guests' ? 'active' : '' }}" 
               href="{{ route('super_admin.users', array_merge(request()->except('page'), ['tab' => 'guests'])) }}" 
               id="guests-tab">
              <i class="fa fa-user"></i> Umoj Lutheran Hostel Guests 
              <span class="badge badge-success">{{ $activeTab === 'guests' ? $users->total() : ($roleCounts['total_guests'] ?? 0) }}</span>
            </a>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>

<!-- Users Table -->
<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-title-w-btn">
        <h3 class="title">
          <i class="fa fa-users"></i> 
          @if($activeTab === 'employees')
            Umoj Lutheran Hostel Staffs
          @else
            Umoj Lutheran Hostel Guests
          @endif
        </h3>
        <p>
          <a href="{{ route('super_admin.users.create') }}" class="btn btn-primary">
            <i class="fa fa-plus"></i> Create New User
          </a>
        </p>
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
                <th>Status</th>
                <th>Registered</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($users as $user)
              <tr>
                <td>{{ $user->id }}</td>
                <td><strong>{{ $user->name }}</strong></td>
                <td>{{ $user->email }}</td>
                <td>
                  @if($activeTab === 'employees')
                    @php
                      $userRoleNormalized = strtolower(str_replace([' ', '_'], '', trim($user->role ?? '')));
                    @endphp
                    @if($userRoleNormalized === 'superadmin' || $user->role == 'super_admin' || strtolower($user->role ?? '') === 'super admin')
                      <span class="badge badge-danger"><i class="fa fa-shield"></i> Super Admin</span>
                    @elseif($user->role == 'manager' || $userRoleNormalized === 'manager')
                      <span class="badge badge-warning"><i class="fa fa-user-tie"></i> Manager</span>
                    @elseif($user->role == 'reception' || $userRoleNormalized === 'reception' || strtolower($user->role ?? '') === 'reception')
                      <span class="badge badge-info"><i class="fa fa-user-md"></i> Reception</span>
                    @elseif($user->role == 'waiter' || $userRoleNormalized === 'waiter')
                      <span class="badge badge-primary" style="background-color: #6f42c1;"><i class="fa fa-concierge-bell"></i> Waiter</span>
                    @elseif($user->role == 'bar_keeper' || $userRoleNormalized === 'barkeeper')
                      <span class="badge badge-dark"><i class="fa fa-glass"></i> Bar Keeper</span>
                    @elseif($user->role == 'head_chef' || $userRoleNormalized === 'headchef')
                      <span class="badge badge-secondary" style="background-color: #e83e8c;"><i class="fa fa-cutlery"></i> Head Chef</span>
                    @elseif($user->role == 'housekeeper' || $userRoleNormalized === 'housekeeper')
                      <span class="badge badge-info" style="background-color: #20c997;"><i class="fa fa-bed"></i> Housekeeper</span>
                    @else
                      <span class="badge badge-secondary"><i class="fa fa-user"></i> {{ $user->role ?? 'Employee' }}</span>
                    @endif
                  @else
                    <span class="badge badge-success"><i class="fa fa-user"></i> Guest</span>
                  @endif
                </td>
                <td>
                  @if($user->is_active)
                    <span class="badge badge-success">Active</span>
                  @else
                    <span class="badge badge-secondary">Inactive</span>
                  @endif
                </td>
                <td>{{ $user->created_at->format('M d, Y') }}</td>
                <td>
                  <div class="btn-group" role="group">
                    <a href="{{ route('super_admin.users.edit', $user->id) }}" class="btn btn-sm btn-primary" title="Edit">
                      <i class="fa fa-edit"></i>
                    </a>
                    <button type="button" class="btn btn-sm btn-warning" 
                            onclick="autoResetPassword({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}')" 
                            title="Auto Generate & Reset Password">
                      <i class="fa fa-key"></i>
                    </button>
                    @php
                      $isSuperAdmin = method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin();
                      $authUser = auth()->guard('staff')->user() ?? auth()->guard('guest')->user();
                      $isCurrentUser = $authUser && $user->id === $authUser->id;
                    @endphp
                    @if(!$isSuperAdmin && !$isCurrentUser)
                    <form action="{{ route('super_admin.users.delete', $user->id) }}" 
                          method="POST" 
                          style="display: inline-block;"
                          onsubmit="event.preventDefault(); confirmAction('Are you sure you want to delete this user?', 'Delete User', 'Yes, delete!', 'Cancel').then((result) => { if (result.isConfirmed) { this.submit(); } });">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                        <i class="fa fa-trash"></i>
                      </button>
                    </form>
                    @endif
                  </div>
                </td>
              </tr>
              
              @empty
              <tr>
                <td colspan="7" class="text-center">No users found</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        
        <!-- Pagination -->
        <div class="mt-3">
          {{ $users->links() }}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
function autoResetPassword(userId, userName, userEmail) {
  confirmAction(
    `Are you sure you want to auto-generate a new password for ${userName}? The new password will be displayed after reset.`,
    'Auto Generate Password',
    'Yes, generate password!',
    'Cancel'
  ).then((result) => {
    if (result.isConfirmed) {
      // Show loading
      Swal.fire({
        title: 'Generating Password...',
        text: 'Please wait while we generate a new password.',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        willOpen: () => {
          Swal.showLoading();
        }
      });

      // Submit the form via AJAX - include email to help identify user in case of ID collision
      fetch(`/super-admin/users/${userId}/reset-password`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
          email: userEmail  // Include email to help identify correct user
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Show the generated password
          Swal.fire({
            icon: 'success',
            title: 'Password Generated Successfully!',
            html: `
              <div class="text-left">
                <p><strong>User:</strong> ${userName}</p>
                <p><strong>Email:</strong> ${userEmail}</p>
                <hr>
                <p><strong>New Password:</strong></p>
                <div class="alert alert-info">
                  <code id="generated-password" style="font-size: 18px; font-weight: bold; color: #940000; letter-spacing: 2px;">${data.password}</code>
                </div>
                <p class="text-danger"><small><i class="fa fa-exclamation-triangle"></i> Please copy this password and share it securely with the user. It will not be shown again.</small></p>
                <button onclick="copyPassword('${data.password.replace(/'/g, "\\'")}')" class="btn btn-primary btn-block">
                  <i class="fa fa-copy"></i> Copy Password
                </button>
                <p class="text-muted mt-2"><small>Password Length: ${data.password.length} characters</small></p>
              </div>
            `,
            confirmButtonColor: '#940000',
            confirmButtonText: 'Done',
            width: '500px'
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: data.message || 'Failed to generate password. Please try again.',
            confirmButtonColor: '#940000'
          });
        }
      })
      .catch(error => {
        console.error('Error:', error);
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'An error occurred while generating the password. Please try again.',
          confirmButtonColor: '#940000'
        });
      });
    }
  });
}

function copyPassword(password) {
  const textarea = document.createElement('textarea');
  textarea.value = password;
  document.body.appendChild(textarea);
  textarea.select();
  document.execCommand('copy');
  document.body.removeChild(textarea);
  
  Swal.fire({
    icon: 'success',
    title: 'Copied!',
    text: 'Password copied to clipboard',
    timer: 2000,
    showConfirmButton: false,
    confirmButtonColor: '#940000'
  });
}

// Show generated password if redirected from reset
@if(session('generated_password'))
  Swal.fire({
    icon: 'success',
    title: 'Password Generated Successfully!',
    html: `
      <div class="text-left">
        <p><strong>User:</strong> {{ session('user_email') }}</p>
        <hr>
        <p><strong>New Password:</strong></p>
        <div class="alert alert-info">
          <code style="font-size: 18px; font-weight: bold; color: #940000;">{{ session('generated_password') }}</code>
        </div>
        <p class="text-danger"><small><i class="fa fa-exclamation-triangle"></i> Please copy this password and share it securely with the user. It will not be shown again.</small></p>
        <button onclick="copyPassword('{{ session('generated_password') }}')" class="btn btn-primary btn-block">
          <i class="fa fa-copy"></i> Copy Password
        </button>
      </div>
    `,
    confirmButtonColor: '#940000',
    confirmButtonText: 'Done',
    width: '500px'
  });
@endif
</script>
@endsection

