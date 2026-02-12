@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-key"></i> Roles Management</h1>
    <p>Manage system roles and permissions</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('super_admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">Roles</a></li>
  </ul>
</div>

<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-title-w-btn">
        <h3 class="title"><i class="fa fa-key"></i> System Roles</h3>
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createRoleModal">
          <i class="fa fa-plus"></i> Create New Role
        </button>
      </div>
      <div class="tile-body">
        <div class="table-responsive">
          <table class="table table-hover table-bordered">
            <thead>
              <tr>
                <th>Name</th>
                <th>Display Name</th>
                <th>Description</th>
                <th>Type</th>
                <th>Permissions</th>
                <th>Users</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($roles as $roleItem)
              <tr>
                <td><strong>{{ $roleItem->name }}</strong></td>
                <td>{{ $roleItem->display_name ?: ucfirst(str_replace('_', ' ', $roleItem->name)) }}</td>
                <td>{{ $roleItem->description }}</td>
                <td>
                  @if($roleItem->is_system)
                    <span class="badge badge-danger">System</span>
                  @else
                    <span class="badge badge-info">Custom</span>
                  @endif
                </td>
                <td>
                  <span class="badge badge-primary">{{ $roleItem->permission_count ?? 0 }} Permissions</span>
                </td>
                <td>
                  <span class="badge badge-success">{{ $roleItem->user_count ?? 0 }} Users</span>
                  @if(($roleItem->staff_count ?? 0) > 0 || ($roleItem->guest_count ?? 0) > 0)
                    <br><small class="text-muted">{{ $roleItem->staff_count ?? 0 }} Staff, {{ $roleItem->guest_count ?? 0 }} Guests</small>
                  @endif
                </td>
                <td>
                  <div class="btn-group" role="group">
                    <button type="button" class="btn btn-sm btn-info" 
                            data-toggle="modal" 
                            data-target="#assignPermissionsModal{{ $roleItem->id }}" 
                            title="Assign Permissions">
                      <i class="fa fa-shield"></i>
                    </button>
                    @if(!$roleItem->is_system)
                    <button type="button" class="btn btn-sm btn-primary" 
                            data-toggle="modal" 
                            data-target="#editRoleModal{{ $roleItem->id }}" 
                            title="Edit">
                      <i class="fa fa-edit"></i>
                    </button>
                    <form action="{{ route('super_admin.roles.delete', $roleItem) }}" 
                          method="POST" 
                          style="display: inline-block;"
                          onsubmit="event.preventDefault(); confirmAction('Are you sure? This will remove the role from all users.', 'Delete Role', 'Yes, delete!', 'Cancel').then((result) => { if (result.isConfirmed) { this.submit(); } });">
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
              
              <!-- Assign Permissions Modal -->
              <div class="modal fade" id="assignPermissionsModal{{ $roleItem->id }}" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-lg" role="document">
                  <div class="modal-content">
                    <form action="{{ route('super_admin.roles.assign-permissions', $role) }}" method="POST">
                      @csrf
                      <div class="modal-header">
                        <h5 class="modal-title">Assign Permissions to {{ $roleItem->display_name }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                      <div class="modal-body">
                        <div class="alert alert-info">
                          <i class="fa fa-info-circle"></i> <strong>How Permissions Control Sidebar Menu Visibility:</strong>
                          <ul class="mb-0 mt-2" style="padding-left: 20px;">
                            <li><strong>✅ CHECKED permission</strong> = Menu item <strong>WILL APPEAR</strong> in the sidebar for users with this role</li>
                            <li><strong>❌ UNCHECKED permission</strong> = Menu item <strong>WILL NOT APPEAR</strong> in the sidebar (hidden from users with this role)</li>
                            <li><strong>Super Admin</strong> always sees all menu items regardless of role permissions</li>
                            <li><strong>Changes take effect immediately</strong> - After saving, users with this role will see/hide menu items on their next page load</li>
                          </ul>
                          <p class="mb-0 mt-2"><strong>Examples:</strong></p>
                          <ul class="mb-0 mt-2" style="padding-left: 20px;">
                            <li>Check <strong>"view_users"</strong> → "Users Management" menu appears</li>
                            <li>Check <strong>"manage_rooms"</strong> → "Rooms" menu appears</li>
                            <li>Check <strong>"view_bookings"</strong> → "All Bookings" submenu appears</li>
                            <li>Uncheck any permission → That menu item disappears from sidebar</li>
                          </ul>
                        </div>
                        <div class="mb-3">
                          <button type="button" class="btn btn-sm btn-outline-primary" onclick="toggleAllPermissions({{ $roleItem->id }}, true)">
                            <i class="fa fa-check-square"></i> Check All
                          </button>
                          <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleAllPermissions({{ $roleItem->id }}, false)">
                            <i class="fa fa-square"></i> Uncheck All
                          </button>
                        </div>
                        @php
                          // Use pre-loaded permissions and role permissions from controller
                          $rolePermissionIds = $allRolePermissions[$roleItem->id] ?? [];
                        @endphp
                        @if($allPermissions->count() > 0)
                          @foreach($allPermissions as $group => $permissions)
                          @php
                            // Skip blog group entirely
                            if (stripos($group ?? '', 'blog') !== false) {
                              continue;
                            }
                          @endphp
                          <h6 class="mt-3 mb-2"><strong>{{ $group ?: 'General' }}</strong></h6>
                          @foreach($permissions as $permission)
                          @php
                            // Skip blog permissions
                            if (stripos($permission->name ?? '', 'blog') !== false) {
                              continue;
                            }
                          @endphp
                          @php
                            // Compare as integers to ensure match
                            $isChecked = in_array((int)$permission->id, $rolePermissionIds, true);
                          @endphp
                          @php
                            // Map permissions to sidebar menu items for better UX
                            $menuMapping = [
                              'view_users' => '→ "Users Management" menu',
                              'manage_rooms' => '→ "Rooms" menu (parent)',
                              'view_rooms' => '→ "View All Rooms" submenu',
                              'create_rooms' => '→ "Add Room" submenu',
                              'view_room_status' => '→ "Room Status" submenu',
                              'manage_room_cleaning' => '→ "Rooms Cleaning" submenu',
                              'manage_bookings' => '→ "Bookings & Requests" menu (parent)',
                              'view_bookings' => '→ "All Bookings" submenu',
                              'view_booking_calendar' => '→ "Booking Calendar" submenu',
                              'create_manual_bookings' => '→ "Manual Booking" submenu',
                              'manage_extensions' => '→ "Extension Requests" submenu',
                              'manage_checkin' => '→ "Check In" submenu',
                              'manage_checkout' => '→ "Check Out" submenu',
                              'view_active_reservations' => '→ "Active Reservations" submenu',
                              'view_guests' => '→ "Guests" menu',
                              'manage_services' => '→ "Services & Issues" menu (parent)',
                              'manage_service_requests' => '→ "Service Requests" submenu',
                              'manage_issues' => '→ "Issue Reports" submenu',
                              'view_payments' => '→ "Payments & Reports" menu (parent)',
                              'view_payment_reports' => '→ "Payment Reports" submenu',
                              'view_reports' => '→ "General Reports" submenu',
                              'view_daily_reports' => '→ "Daily Reports" submenu',
                              'view_feedback' => '→ "Feedback & Analytics" menu (parent)',
                              'manage_exchange_rates' => '→ "Exchange Rates" submenu',
                              'manage_settings' => '→ "Settings" menu (parent)',
                              'manage_wifi_settings' => '→ "WiFi Settings" submenu',
                              'manage_hotel_settings' => '→ "Hotel Settings" submenu',
                              'manage_room_settings' => '→ "Room Settings" submenu',
                              'manage_pricing' => '→ "Pricing" submenu',
                            ];
                            $menuItem = $menuMapping[$permission->name] ?? '';
                          @endphp
                          <div class="form-check mb-2 p-2 {{ $isChecked ? 'bg-light border-left border-success' : 'border-left border-secondary' }}" style="border-left-width: 4px !important;">
                            <input class="form-check-input" type="checkbox" 
                                   name="permissions[]" 
                                   value="{{ $permission->id }}" 
                                   id="perm{{ $roleItem->id }}_{{ $permission->id }}"
                                   {{ $isChecked ? 'checked' : '' }}
                                   onchange="updatePermissionStatus({{ $roleItem->id }}, {{ $permission->id }}, this.checked)">
                            <label class="form-check-label" for="perm{{ $roleItem->id }}_{{ $permission->id }}" style="cursor: pointer;">
                              <strong>
                                @if($isChecked)
                                  <span class="text-success"><i class="fa fa-check-circle"></i> {{ $permission->display_name }}</span>
                                @else
                                  <span class="text-muted"><i class="fa fa-circle-o"></i> {{ $permission->display_name }}</span>
                                @endif
                              </strong>
                              <br><small class="text-muted">Permission: <code>{{ $permission->name }}</code></small>
                              @if($menuItem)
                              <br><small class="text-primary"><i class="fa fa-list"></i> <strong>Controls:</strong> {{ $menuItem }}</small>
                              @endif
                              @if($permission->description)
                              <br><small class="text-info"><i class="fa fa-info-circle"></i> {{ $permission->description }}</small>
                              @endif
                              <br><small class="{{ $isChecked ? 'text-success' : 'text-danger' }}">
                                @if($isChecked)
                                  <i class="fa fa-eye"></i> Menu items will be <strong>VISIBLE</strong> in sidebar
                                @else
                                  <i class="fa fa-eye-slash"></i> Menu items will be <strong>HIDDEN</strong> in sidebar
                                @endif
                              </small>
                            </label>
                          </div>
                          @endforeach
                          @endforeach
                        @else
                          <div class="alert alert-warning">
                            <i class="fa fa-exclamation-triangle"></i> No permissions found. Please create permissions first.
                          </div>
                        @endif
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                          <i class="fa fa-save"></i> Save Permissions
                        </button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
              
              <!-- Edit Role Modal -->
              @if(!$roleItem->is_system)
              <div class="modal fade" id="editRoleModal{{ $roleItem->id }}" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                  <div class="modal-content">
                    <form action="{{ route('super_admin.roles.update', $roleItem) }}" method="POST">
                      @csrf
                      @method('PUT')
                      <div class="modal-header">
                        <h5 class="modal-title">Edit Role: {{ $roleItem->name }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                      <div class="modal-body">
                        <div class="form-group">
                          <label>Role Name</label>
                          <input type="text" class="form-control" value="{{ $roleItem->name }}" disabled>
                          <small class="form-text text-muted">Role name cannot be changed</small>
                        </div>
                        <div class="form-group">
                          <label for="display_name{{ $roleItem->id }}">Display Name</label>
                          <input type="text" name="display_name" id="display_name{{ $roleItem->id }}" 
                                 class="form-control" value="{{ $roleItem->display_name }}" required>
                        </div>
                        <div class="form-group">
                          <label for="description{{ $roleItem->id }}">Description</label>
                          <textarea name="description" id="description{{ $roleItem->id }}" 
                                    class="form-control" rows="3">{{ $roleItem->description }}</textarea>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Role</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
              @endif
              @empty
              <tr>
                <td colspan="7" class="text-center">No roles found</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Create Role Modal -->
<div class="modal fade" id="createRoleModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form action="{{ route('super_admin.roles.store') }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Create New Role</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="name">Role Name <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name" class="form-control" required 
                   pattern="[a-z_]+" title="Lowercase letters and underscores only">
            <small class="form-text text-muted">Use lowercase letters and underscores (e.g., content_manager)</small>
          </div>
          <div class="form-group">
            <label for="display_name">Display Name <span class="text-danger">*</span></label>
            <input type="text" name="display_name" id="display_name" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" id="description" class="form-control" rows="3"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Create Role</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
function updatePermissionStatus(roleId, permissionId, isChecked) {
  // Visual feedback - update the label styling
  const label = document.querySelector(`label[for="perm${roleId}_${permissionId}"]`);
  if (!label) return;
  
  const container = label.closest('.form-check');
  if (!container) return;
  
  if (isChecked) {
    container.classList.remove('border-secondary');
    container.classList.add('bg-light', 'border-left', 'border-success');
    const strongSpan = label.querySelector('strong span');
    if (strongSpan) {
      strongSpan.className = 'text-success';
      strongSpan.innerHTML = '<i class="fa fa-check-circle"></i> ' + strongSpan.textContent.replace(/^[^\w]*/, '');
    }
    const statusText = label.querySelector('small:last-of-type');
    if (statusText && statusText.textContent.includes('Menu items')) {
      statusText.className = 'text-success';
      statusText.innerHTML = '<i class="fa fa-eye"></i> Menu items will be <strong>VISIBLE</strong> in sidebar';
    }
  } else {
    container.classList.remove('bg-light', 'border-success');
    container.classList.add('border-left', 'border-secondary');
    const strongSpan = label.querySelector('strong span');
    if (strongSpan) {
      strongSpan.className = 'text-muted';
      strongSpan.innerHTML = '<i class="fa fa-circle-o"></i> ' + strongSpan.textContent.replace(/^[^\w]*/, '');
    }
    const statusText = label.querySelector('small:last-of-type');
    if (statusText && statusText.textContent.includes('Menu items')) {
      statusText.className = 'text-danger';
      statusText.innerHTML = '<i class="fa fa-eye-slash"></i> Menu items will be <strong>HIDDEN</strong> in sidebar';
    }
  }
}

function toggleAllPermissions(roleId, check) {
  const modal = document.getElementById('assignPermissionsModal' + roleId);
  if (!modal) return;
  
  const checkboxes = modal.querySelectorAll('input[type="checkbox"][name="permissions[]"]');
  checkboxes.forEach(checkbox => {
    checkbox.checked = check;
    const permissionId = checkbox.value;
    updatePermissionStatus(roleId, permissionId, check);
  });
}

function toggleGroupPermissions(roleId) {
  const modal = document.getElementById('assignPermissionsModal' + roleId);
  if (!modal) return;
  
  // Get all group headers
  const groups = modal.querySelectorAll('h6.mt-3.mb-2');
  groups.forEach(group => {
    const groupName = group.textContent.trim();
    const groupDiv = group.nextElementSibling;
    if (!groupDiv) return;
    
    // Find all checkboxes in this group
    let allChecked = true;
    const checkboxes = [];
    let current = groupDiv;
    while (current && current.tagName !== 'H6') {
      const checkbox = current.querySelector('input[type="checkbox"]');
      if (checkbox) {
        checkboxes.push(checkbox);
        if (!checkbox.checked) allChecked = false;
      }
      current = current.nextElementSibling;
    }
    
    // Toggle all in group
    const newState = !allChecked;
    checkboxes.forEach(checkbox => {
      checkbox.checked = newState;
      const permissionId = checkbox.value;
      updatePermissionStatus(roleId, permissionId, newState);
    });
  });
}
</script>
@endsection

















