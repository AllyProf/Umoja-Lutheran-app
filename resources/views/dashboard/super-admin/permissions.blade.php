@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-shield"></i> Permissions Management</h1>
    <p>Manage system permissions</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('super_admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">Permissions</a></li>
  </ul>
</div>

<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-title-w-btn">
        <h3 class="title"><i class="fa fa-shield"></i> System Permissions</h3>
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createPermissionModal">
          <i class="fa fa-plus"></i> Create New Permission
        </button>
      </div>
      <div class="tile-body">
        @foreach($groups as $group)
        <div class="mb-4">
          <h5 class="mb-3">{{ $group ?: 'General Permissions' }}</h5>
          <div class="table-responsive">
            <table class="table table-hover table-bordered">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Display Name</th>
                  <th>Description</th>
                  <th>Assigned to Roles</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach($permissions->where('group', $group) as $permission)
                <tr>
                  <td><code>{{ $permission->name }}</code></td>
                  <td><strong>{{ $permission->display_name }}</strong></td>
                  <td>{{ $permission->description }}</td>
                  <td>
                    @if($permission->roles->count() > 0)
                      @foreach($permission->roles as $assignedRole)
                        <span class="badge badge-info">{{ $assignedRole->display_name }}</span>
                      @endforeach
                    @else
                      <span class="text-muted">Not assigned</span>
                    @endif
                  </td>
                  <td>
                    <button type="button" class="btn btn-sm btn-primary" 
                            data-toggle="modal" 
                            data-target="#editPermissionModal{{ $permission->id }}" 
                            title="Edit">
                      <i class="fa fa-edit"></i>
                    </button>
                  </td>
                </tr>
                
                <!-- Edit Permission Modal -->
                <div class="modal fade" id="editPermissionModal{{ $permission->id }}" tabindex="-1" role="dialog">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <form action="{{ route('super_admin.permissions.update', $permission) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                          <h5 class="modal-title">Edit Permission: {{ $permission->name }}</h5>
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                          </button>
                        </div>
                        <div class="modal-body">
                          <div class="form-group">
                            <label>Permission Name</label>
                            <input type="text" class="form-control" value="{{ $permission->name }}" disabled>
                            <small class="form-text text-muted">Permission name cannot be changed</small>
                          </div>
                          <div class="form-group">
                            <label for="display_name{{ $permission->id }}">Display Name</label>
                            <input type="text" name="display_name" id="display_name{{ $permission->id }}" 
                                   class="form-control" value="{{ $permission->display_name }}" required>
                          </div>
                          <div class="form-group">
                            <label for="description{{ $permission->id }}">Description</label>
                            <textarea name="description" id="description{{ $permission->id }}" 
                                      class="form-control" rows="3">{{ $permission->description }}</textarea>
                          </div>
                          <div class="form-group">
                            <label for="group{{ $permission->id }}">Group</label>
                            <input type="text" name="group" id="group{{ $permission->id }}" 
                                   class="form-control" value="{{ $permission->group }}" 
                                   placeholder="e.g., Users, Bookings, Rooms">
                          </div>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                          <button type="submit" class="btn btn-primary">Update Permission</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
        @endforeach
        
        @if($permissions->whereNull('group')->count() > 0)
        <div class="mb-4">
          <h5 class="mb-3">General Permissions</h5>
          <div class="table-responsive">
            <table class="table table-hover table-bordered">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Display Name</th>
                  <th>Description</th>
                  <th>Assigned to Roles</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach($permissions->whereNull('group') as $permission)
                <tr>
                  <td><code>{{ $permission->name }}</code></td>
                  <td><strong>{{ $permission->display_name }}</strong></td>
                  <td>{{ $permission->description }}</td>
                  <td>
                    @if($permission->roles->count() > 0)
                      @foreach($permission->roles as $assignedRole)
                        <span class="badge badge-info">{{ $assignedRole->display_name }}</span>
                      @endforeach
                    @else
                      <span class="text-muted">Not assigned</span>
                    @endif
                  </td>
                  <td>
                    <button type="button" class="btn btn-sm btn-primary" 
                            data-toggle="modal" 
                            data-target="#editPermissionModal{{ $permission->id }}" 
                            title="Edit">
                      <i class="fa fa-edit"></i>
                    </button>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- Create Permission Modal -->
<div class="modal fade" id="createPermissionModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form action="{{ route('super_admin.permissions.store') }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Create New Permission</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="name">Permission Name <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name" class="form-control" required 
                   pattern="[a-z_]+" title="Lowercase letters and underscores only">
            <small class="form-text text-muted">Use lowercase letters and underscores (e.g., manage_bookings)</small>
          </div>
          <div class="form-group">
            <label for="display_name">Display Name <span class="text-danger">*</span></label>
            <input type="text" name="display_name" id="display_name" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" id="description" class="form-control" rows="3"></textarea>
          </div>
          <div class="form-group">
            <label for="group">Group</label>
            <input type="text" name="group" id="group" class="form-control" 
                   placeholder="e.g., Users, Bookings, Rooms">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Create Permission</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

