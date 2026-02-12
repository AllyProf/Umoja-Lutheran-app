@extends('dashboard.layouts.app')

@section('styles')
<style>
/* Mobile Responsive Styles */
@media (max-width: 768px) {
    /* Filters Stack */
    .form-inline .form-group {
        display: block !important;
        margin-bottom: 10px;
        margin-right: 0 !important;
        width: 100%;
    }
    .form-inline select, .form-inline button, .form-inline a {
        display: block;
        width: 100%;
        margin-left: 0 !important;
        margin-bottom: 10px;
    }
    
    /* Stats Grid 2x2 */
    .mb-3 .col-md-3 {
        flex: 0 0 50%;
        max-width: 50%;
        padding: 5px;
    }
    .widget-small {
        margin-bottom: 10px;
        padding: 10px 5px;
    }
    .widget-small .icon {
        min-width: 40px;
        width: 40px;
        font-size: 20px;
    }
    
    /* Table to Card View */
    .table-responsive {
        border: 0;
    }
    .table thead {
        display: none;
    }
    .table tr {
        display: block;
        margin-bottom: 1rem;
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    .table td {
        display: block;
        text-align: right;
        padding: 10px;
        border-bottom: 1px solid #eee;
        font-size: 14px;
    }
    .table td:last-child {
        border-bottom: 0;
        text-align: center;
        background: #f8f9fa;
    }
    .table td::before {
        content: attr(data-label);
        float: left;
        font-weight: bold;
        text-transform: uppercase;
        font-size: 12px;
        color: #6c757d;
    }
    
    /* Checkbox positioning */
    .table td:first-child {
        text-align: left;
        background: #f0f4f8;
        border-bottom: 0;
        padding: 8px 15px;
    }
    .table td:first-child::before {
        content: "SELECT";
        margin-right: 10px;
        margin-top: 2px;
    }
    
    /* Department Headers */
    .department-group h4 {
        font-size: 16px;
    }
    
    /* Bulk Actions */
    .tile-title-w-btn {
        flex-direction: column;
        align-items: flex-start;
    }
    .tile-title-w-btn p {
        width: 100%;
        margin-top: 10px;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .tile-title-w-btn button {
        width: 100%;
        margin-left: 0 !important;
    }
}
</style>
@endsection

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-shopping-cart"></i> Purchase Requests</h1>
    <p>Manage purchase requests from staff</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">Purchase Requests</a></li>
  </ul>
</div>

@if($deadline)
<div class="row mb-3">
  <div class="col-md-12">
    <div class="alert alert-info d-flex flex-column flex-md-row justify-content-between align-items-center text-center text-md-left">
      <div class="mb-2 mb-md-0">
        <i class="fa fa-info-circle"></i> 
        <strong>Purchase Deadline:</strong> 
        Next purchase is <strong>{{ ucfirst($deadline->day_of_week) }}</strong> at <strong>{{ \Carbon\Carbon::parse($deadline->deadline_time)->format('H:i') }}</strong>.
      </div>
      <div>
        <a href="{{ route('admin.purchase-requests.deadline') }}" class="btn btn-sm btn-warning btn-block">
          <i class="fa fa-edit"></i> Edit Deadline
        </a>
      </div>
    </div>
  </div>
</div>
@endif

<!-- Statistics Cards -->
<div class="row mb-3 d-flex flex-wrap">
  <div class="col-6 col-md-3">
    <div class="widget-small warning coloured-icon">
      <i class="icon fa fa-clock-o fa-2x"></i>
      <div class="info">
        <h4>Pending</h4>
        <p><b>{{ $stats['pending'] ?? 0 }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="widget-small success coloured-icon">
      <i class="icon fa fa-check-circle fa-2x"></i>
      <div class="info">
        <h4>Approved</h4>
        <p><b>{{ $stats['approved'] ?? 0 }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="widget-small info coloured-icon">
      <i class="icon fa fa-shopping-bag fa-2x"></i>
      <div class="info">
        <h4>Purchased</h4>
        <p><b>{{ $stats['purchased'] ?? 0 }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="widget-small primary coloured-icon">
      <i class="icon fa fa-list fa-2x"></i>
      <div class="info">
        <h4>Total</h4>
        <p><b>{{ $stats['total'] ?? 0 }}</b></p>
      </div>
    </div>
  </div>
</div>

<!-- Tab Navigation -->
<div class="row mb-3">
  <div class="col-md-12">
    <ul class="nav nav-tabs nav-justified flex-column flex-sm-row" style="border-bottom: 2px solid #dee2e6;">
      <li class="nav-item">
        <a class="nav-link {{ $tab === 'new' ? 'active' : '' }}" href="{{ route('admin.purchase-requests.index', ['tab' => 'new']) }}" style="{{ $tab === 'new' ? 'background: #e07632; color: #fff; border-color: #e07632;' : 'color: #666;' }}">
          <i class="fa fa-plus-circle"></i> New Requested
          <span class="badge badge-{{ $tab === 'new' ? 'light' : 'secondary' }} ml-1">{{ $stats['pending'] + $stats['approved'] }}</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ $tab === 'rejected' ? 'active' : '' }}" href="{{ route('admin.purchase-requests.index', ['tab' => 'rejected']) }}" style="{{ $tab === 'rejected' ? 'background: #dc3545; color: #fff; border-color: #dc3545;' : 'color: #666;' }}">
          <i class="fa fa-times-circle"></i> Rejected
          <span class="badge badge-{{ $tab === 'rejected' ? 'light' : 'secondary' }} ml-1">{{ $stats['rejected'] }}</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ $tab === 'completed' ? 'active' : '' }}" href="{{ route('admin.purchase-requests.index', ['tab' => 'completed']) }}" style="{{ $tab === 'completed' ? 'background: #28a745; color: #fff; border-color: #28a745;' : 'color: #666;' }}">
          <i class="fa fa-check-circle"></i> Completed Purchase
          <span class="badge badge-{{ $tab === 'completed' ? 'light' : 'secondary' }} ml-1">{{ $stats['purchased'] }}</span>
        </a>
      </li>
    </ul>
  </div>
</div>

<!-- Filters -->
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-body">
        <form method="GET" action="{{ route('admin.purchase-requests.index') }}" class="form-inline">
          <input type="hidden" name="tab" value="{{ $tab }}">
          <div class="form-group mr-3">
            <label for="status" class="mr-2">Status:</label>
            <select class="form-control" id="status" name="status">
              <option value="">All</option>
              @if($tab === 'new')
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="on_list" {{ request('status') === 'on_list' ? 'selected' : '' }}>On List</option>
              @elseif($tab === 'rejected')
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
              @else
                <option value="purchased" {{ request('status') === 'purchased' ? 'selected' : '' }}>Purchased</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="received" {{ request('status') === 'received' ? 'selected' : '' }}>Received</option>
              @endif
            </select>
          </div>
          <div class="form-group mr-3">
            <label for="priority" class="mr-2">Priority:</label>
            <select class="form-control" id="priority" name="priority">
              <option value="">All</option>
              <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
              <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
              <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
              <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
            </select>
          </div>
          <button type="submit" class="btn btn-primary">
            <i class="fa fa-filter"></i> Filter
          </button>
          <a href="{{ route('admin.purchase-requests.index', ['tab' => $tab]) }}" class="btn btn-secondary ml-2">
            <i class="fa fa-refresh"></i> Reset
          </a>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Purchase Requests Table - Grouped by Category -->
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-title-w-btn">
        <h3 class="title"><i class="fa fa-list"></i> 
          @if($tab === 'new') New Requested Items @elseif($tab === 'rejected') Rejected Requests @else Completed Purchase History @endif
        </h3>
        <p>
          @if($tab === 'new')
          <button class="btn btn-primary icon-btn" id="bulkApproveBtn" disabled>
            <i class="fa fa-check-circle"></i> Approve Selected
          </button>
          <button class="btn btn-success icon-btn ml-2" id="addToShoppingListBtn" disabled>
            <i class="fa fa-shopping-bag"></i> Add Selected to Shopping List
          </button>
          @endif
        </p>
      </div>
      <div class="tile-body">
        @if(isset($groupedRequests) && $groupedRequests->count() > 0)
          @php
            // Define department order and colors
            $departmentOrder = ['Housekeeping', 'Reception', 'Bar', 'Food'];
            $departmentColors = [
              'Housekeeping' => '#e07632',
              'Reception' => '#17a2b8',
              'Bar' => '#28a745',
              'Food' => '#ffc107'
            ];
          @endphp
          @foreach($departmentOrder as $dept)
            @if(isset($groupedRequests[$dept]) && $groupedRequests[$dept]->count() > 0)
              @php $departmentRequests = $groupedRequests[$dept]; @endphp
              <div class="department-group mb-4" style="border: 1px solid #dee2e6; border-radius: 8px; padding: 15px; background: #f8f9fa;">
                <h4 class="mb-3" style="color: {{ $departmentColors[$dept] ?? '#333' }}; border-bottom: 2px solid {{ $departmentColors[$dept] ?? '#333' }}; padding-bottom: 10px;">
                  <i class="fa fa-building"></i> {{ $dept }} Department
                  <span class="badge badge-primary ml-2">{{ $departmentRequests->count() }} item(s)</span>
                </h4>
                
                @php
                  // Group items within department by category for better organization
                  $categoryGroups = $departmentRequests->groupBy(function($request) {
                    return $request->category ? ucfirst(str_replace('_', ' ', $request->category)) : 'Other';
                  });
                @endphp
                
                @foreach($categoryGroups as $category => $categoryRequests)
                  @if($categoryGroups->count() > 1)
                  <h5 class="mt-3 mb-2" style="color: #666; font-size: 14px; font-weight: 600;">
                    <i class="fa fa-tag"></i> {{ $category }}
                  </h5>
                  @endif
                  
                  <div class="table-responsive">
                    <table class="table table-hover table-bordered table-sm">
                      <thead>
                        <tr>
                          <th style="width: 40px;">
                            <input type="checkbox" class="department-select-all" data-department="{{ $dept }}">
                          </th>
                          <th>Requested By</th>
                          <th>Item Name</th>
                          <th>Category</th>
                          <th>Quantity</th>
                          <th>Priority</th>
                          <th>Status</th>
                          <th>Reason</th>
                          <th>Submitted</th>
                          <th>Last Edited</th>
                          <th>Changes</th>
                          <th>Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($categoryRequests as $request)
                        <tr>
                          <td>
                            @if($request->status === 'pending' || $request->status === 'approved')
                              <input type="checkbox" class="request-checkbox" value="{{ $request->id }}" data-department="{{ $dept }}" data-status="{{ $request->status }}">
                            @endif
                          </td>
                          <td data-label="Requested By">
                            <strong>{{ $request->requestedBy->name ?? 'N/A' }}</strong>
                            <br><small class="text-muted">{{ $request->requestedBy->getDepartmentName() ?? 'Staff' }}</small>
                          </td>
                          <td data-label="Item Name"><strong>{{ $request->item_name }}</strong></td>
                          <td data-label="Category">{{ $request->category ? ucfirst(str_replace('_', ' ', $request->category)) : 'N/A' }}</td>
                          <td data-label="Quantity">
                            <span class="editable-quantity" data-request-id="{{ $request->id }}" data-field="quantity">
                              {{ number_format($request->quantity, 0) }}
                            </span> 
                            <span class="editable-unit" data-request-id="{{ $request->id }}" data-field="unit">
                              {{ $request->unit === 'bottles' ? 'PIC' : $request->unit }}
                            </span>
                          </td>
                          <td data-label="Priority">
                            @if($request->priority === 'urgent')
                              <span class="badge badge-danger">Urgent</span>
                            @elseif($request->priority === 'high')
                              <span class="badge badge-warning">High</span>
                            @elseif($request->priority === 'medium')
                              <span class="badge badge-info">Medium</span>
                            @else
                              <span class="badge badge-secondary">Low</span>
                            @endif
                          </td>
                          <td data-label="Status">
                            @if($request->status === 'pending')
                              <span class="badge badge-warning">Pending</span>
                            @elseif($request->status === 'approved')
                              <span class="badge badge-success">Approved</span>
                            @elseif($request->status === 'on_list')
                              <span class="badge badge-primary">On List</span>
                            @elseif($request->status === 'rejected')
                              <span class="badge badge-danger">Rejected</span>
                            @elseif($request->status === 'purchased')
                              <span class="badge badge-info">Purchased</span>
                            @elseif($request->status === 'completed')
                              <span class="badge badge-success">Completed</span>
                            @endif
                          </td>
                          <td data-label="Reason">{{ Str::limit($request->reason, 30) }}</td>
                          <td data-label="Submitted">{{ $request->created_at->format('M d, Y H:i') }}</td>
                          <td data-label="Last Edited">
                            @if($request->last_edited_at)
                              <small class="text-muted">
                                <i class="fa fa-edit"></i> {{ $request->editor->name ?? 'Unknown' }}<br>
                                <i class="fa fa-clock-o"></i> {{ $request->last_edited_at->format('M d, Y H:i') }}
                              </small>
                            @else
                              <small class="text-muted">Not edited</small>
                            @endif
                          </td>
                          <td data-label="Changes">
                            @if($request->last_changes && count($request->last_changes) > 0)
                              <div class="changes-list" style="max-width: 250px; font-size: 11px;">
                                @foreach($request->last_changes as $change)
                                  <div class="mb-1">
                                    <strong>{{ $change['field'] }}:</strong><br>
                                    <span class="text-danger" style="text-decoration: line-through;">{{ $change['old'] }}</span>
                                    <i class="fa fa-arrow-right text-muted mx-1"></i>
                                    <span class="text-success">{{ $change['new'] }}</span>
                                  </div>
                                @endforeach
                              </div>
                            @else
                              <small class="text-muted">-</small>
                            @endif
                          </td>
                          <td data-label="Actions">
                            <div class="btn-group" role="group">
                              @if($request->status === 'pending')
                                <button class="btn btn-sm btn-success approve-btn" data-request-id="{{ $request->id }}" title="Approve">
                                  <i class="fa fa-check"></i>
                                </button>
                                <button class="btn btn-sm btn-danger reject-btn" data-request-id="{{ $request->id }}" title="Reject">
                                  <i class="fa fa-times"></i>
                                </button>
                              @endif
                              <button class="btn btn-sm btn-info edit-btn" data-request-id="{{ $request->id }}" title="Edit">
                                <i class="fa fa-edit"></i>
                              </button>
                            </div>
                          </td>
                        </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                @endforeach
              </div>
            @endif
          @endforeach
          
          @foreach($groupedRequests as $dept => $departmentRequests)
            @if(!in_array($dept, $departmentOrder))
              <div class="department-group mb-4" style="border: 1px solid #dee2e6; border-radius: 8px; padding: 15px; background: #f8f9fa;">
                <h4 class="mb-3" style="color: #333; border-bottom: 2px solid #333; padding-bottom: 10px;">
                  <i class="fa fa-building"></i> {{ $dept }} Department
                  <span class="badge badge-primary ml-2">{{ $departmentRequests->count() }} item(s)</span>
                </h4>
                
                @php
                  $categoryGroups = $departmentRequests->groupBy(function($request) {
                    return $request->category ? ucfirst(str_replace('_', ' ', $request->category)) : 'Other';
                  });
                @endphp
                
                @foreach($categoryGroups as $category => $categoryRequests)
                  @if($categoryGroups->count() > 1)
                  <h5 class="mt-3 mb-2" style="color: #666; font-size: 14px; font-weight: 600;">
                    <i class="fa fa-tag"></i> {{ $category }}
                  </h5>
                  @endif
                  
                  <div class="table-responsive">
                    <table class="table table-hover table-bordered table-sm">
                      <thead>
                        <tr>
                          <th style="width: 40px;">
                            <input type="checkbox" class="department-select-all" data-department="{{ $dept }}">
                          </th>
                          <th>Requested By</th>
                          <th>Item Name</th>
                          <th>Category</th>
                          <th>Quantity</th>
                          <th>Priority</th>
                          <th>Status</th>
                          <th>Reason</th>
                          <th>Submitted</th>
                          <th>Last Edited</th>
                          <th>Changes</th>
                          <th>Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($categoryRequests as $request)
                        <tr>
                          <td>
                            @if($request->status === 'pending' || $request->status === 'approved')
                              <input type="checkbox" class="request-checkbox" value="{{ $request->id }}" data-department="{{ $dept }}" data-status="{{ $request->status }}">
                            @endif
                          </td>
                          <td>
                            <strong>{{ $request->requestedBy->name ?? 'N/A' }}</strong>
                            <br><small class="text-muted">{{ $request->requestedBy->getDepartmentName() ?? 'Staff' }}</small>
                          </td>
                          <td><strong>{{ $request->item_name }}</strong></td>
                          <td>{{ $request->category ? ucfirst(str_replace('_', ' ', $request->category)) : 'N/A' }}</td>
                          <td>
                            <span class="editable-quantity" data-request-id="{{ $request->id }}" data-field="quantity">
                              {{ number_format($request->quantity, 0) }}
                            </span> 
                            <span class="editable-unit" data-request-id="{{ $request->id }}" data-field="unit">
                              {{ $request->unit === 'bottles' ? 'PIC' : $request->unit }}
                            </span>
                          </td>
                          <td>
                            @if($request->priority === 'urgent')
                              <span class="badge badge-danger">Urgent</span>
                            @elseif($request->priority === 'high')
                              <span class="badge badge-warning">High</span>
                            @elseif($request->priority === 'medium')
                              <span class="badge badge-info">Medium</span>
                            @else
                              <span class="badge badge-secondary">Low</span>
                            @endif
                          </td>
                          <td>
                            @if($request->status === 'pending')
                              <span class="badge badge-warning">Pending</span>
                            @elseif($request->status === 'approved')
                              <span class="badge badge-success">Approved</span>
                            @elseif($request->status === 'on_list')
                              <span class="badge badge-primary">On List</span>
                            @elseif($request->status === 'rejected')
                              <span class="badge badge-danger">Rejected</span>
                            @elseif($request->status === 'purchased')
                              <span class="badge badge-info">Purchased</span>
                            @elseif($request->status === 'completed')
                              <span class="badge badge-success">Completed</span>
                            @endif
                          </td>
                          <td>{{ Str::limit($request->reason, 30) }}</td>
                          <td>{{ $request->created_at->format('M d, Y H:i') }}</td>
                          <td>
                            @if($request->last_edited_at)
                              <small class="text-muted">
                                <i class="fa fa-edit"></i> {{ $request->editor->name ?? 'Unknown' }}<br>
                                <i class="fa fa-clock-o"></i> {{ $request->last_edited_at->format('M d, Y H:i') }}
                              </small>
                            @else
                              <small class="text-muted">Not edited</small>
                            @endif
                          </td>
                          <td>
                            @if($request->last_changes && count($request->last_changes) > 0)
                              <div class="changes-list" style="max-width: 250px; font-size: 11px;">
                                @foreach($request->last_changes as $change)
                                  <div class="mb-1">
                                    <strong>{{ $change['field'] }}:</strong><br>
                                    <span class="text-danger" style="text-decoration: line-through;">{{ $change['old'] }}</span>
                                    <i class="fa fa-arrow-right text-muted mx-1"></i>
                                    <span class="text-success">{{ $change['new'] }}</span>
                                  </div>
                                @endforeach
                              </div>
                            @else
                              <small class="text-muted">-</small>
                            @endif
                          </td>
                          <td>
                            <div class="btn-group" role="group">
                              @if($request->status === 'pending')
                                <button class="btn btn-sm btn-success approve-btn" data-request-id="{{ $request->id }}" title="Approve">
                                  <i class="fa fa-check"></i>
                                </button>
                                <button class="btn btn-sm btn-danger reject-btn" data-request-id="{{ $request->id }}" title="Reject">
                                  <i class="fa fa-times"></i>
                                </button>
                              @endif
                              <button class="btn btn-sm btn-info edit-btn" data-request-id="{{ $request->id }}" title="Edit">
                                <i class="fa fa-edit"></i>
                              </button>
                            </div>
                          </td>
                        </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                @endforeach
              </div>
            @endif
          @endforeach
        @elseif($requests->count() > 0)
        <!-- Fallback to old table format if groupedRequests not available -->
        <div class="table-responsive">
          <table class="table table-hover table-bordered">
            <thead>
              <tr>
                <th>
                  <input type="checkbox" id="selectAll">
                </th>
                <th>Requested By</th>
                <th>Item Name</th>
                <th>Category</th>
                <th>Quantity</th>
                <th>Priority</th>
                <th>Status</th>
                <th>Reason</th>
                <th>Submitted</th>
                <th>Last Edited</th>
                <th>Changes</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($requests as $request)
              <tr>
                <td>
                  @if($request->status === 'pending' || $request->status === 'approved')
                    <input type="checkbox" class="request-checkbox" value="{{ $request->id }}" data-status="{{ $request->status }}">
                  @endif
                </td>
                <td>
                  <strong>{{ $request->requestedBy->name }}</strong>
                  <br><small class="text-muted">{{ $request->requestedBy->getDepartmentName() ?? 'Staff' }}</small>
                </td>
                <td><strong>{{ $request->item_name }}</strong></td>
                <td>{{ $request->category ? ucfirst(str_replace('_', ' ', $request->category)) : 'N/A' }}</td>
                <td>{{ number_format($request->quantity, 0) }} {{ $request->unit === 'bottles' ? 'PIC' : $request->unit }}</td>
                <td>
                  @if($request->priority === 'urgent')
                    <span class="badge badge-danger">Urgent</span>
                  @elseif($request->priority === 'high')
                    <span class="badge badge-warning">High</span>
                  @elseif($request->priority === 'medium')
                    <span class="badge badge-info">Medium</span>
                  @else
                    <span class="badge badge-secondary">Low</span>
                  @endif
                </td>
                <td>
                  @if($request->status === 'pending')
                    <span class="badge badge-warning">Pending</span>
                  @elseif($request->status === 'approved')
                    <span class="badge badge-success">Approved</span>
                  @elseif($request->status === 'on_list')
                    <span class="badge badge-primary">On List</span>
                  @elseif($request->status === 'rejected')
                    <span class="badge badge-danger">Rejected</span>
                  @elseif($request->status === 'purchased')
                    <span class="badge badge-info">Purchased</span>
                  @elseif($request->status === 'completed')
                    <span class="badge badge-success">Completed</span>
                  @endif
                </td>
                <td>{{ Str::limit($request->reason, 30) }}</td>
                <td>{{ $request->created_at->format('M d, Y H:i') }}</td>
                <td>
                  @if($request->last_edited_at)
                    <small class="text-muted">
                      <i class="fa fa-edit"></i> {{ $request->editor->name ?? 'Unknown' }}<br>
                      <i class="fa fa-clock-o"></i> {{ $request->last_edited_at->format('M d, Y H:i') }}
                    </small>
                  @else
                    <small class="text-muted">Not edited</small>
                  @endif
                </td>
                <td>
                  @if($request->last_changes && count($request->last_changes) > 0)
                    <div class="changes-list" style="max-width: 250px; font-size: 11px;">
                      @foreach($request->last_changes as $change)
                        <div class="mb-1">
                          <strong>{{ $change['field'] }}:</strong><br>
                          <span class="text-danger" style="text-decoration: line-through;">{{ $change['old'] }}</span>
                          <i class="fa fa-arrow-right text-muted mx-1"></i>
                          <span class="text-success">{{ $change['new'] }}</span>
                        </div>
                      @endforeach
                    </div>
                  @else
                    <small class="text-muted">-</small>
                  @endif
                </td>
                <td>
                  @if($request->status === 'pending')
                    <button class="btn btn-sm btn-success approve-btn" data-request-id="{{ $request->id }}">
                      <i class="fa fa-check"></i> Approve
                    </button>
                    <button class="btn btn-sm btn-danger reject-btn" data-request-id="{{ $request->id }}">
                      <i class="fa fa-times"></i> Reject
                    </button>
                  @endif
                  <button class="btn btn-sm btn-info edit-btn" data-request-id="{{ $request->id }}">
                    <i class="fa fa-edit"></i>
                  </button>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        
        <div class="mt-3">
          {{ $requests->links() }}
        </div>
        @else
        <div class="text-center" style="padding: 50px;">
          <i class="fa fa-shopping-cart fa-4x text-muted mb-3"></i>
          <h3>No Purchase Requests</h3>
          <p class="text-muted">There are no purchase requests matching your filters.</p>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-times-circle"></i> Reject Purchase Request</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="rejectForm">
        <div class="modal-body">
          <input type="hidden" id="reject_request_id" name="request_id">
          <div class="form-group">
            <label for="rejection_reason">Rejection Reason <span class="text-danger">*</span></label>
            <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3" required placeholder="Please provide a reason for rejection..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger">
            <i class="fa fa-times"></i> Reject Request
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-edit"></i> Edit Purchase Request</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="editForm">
        <div class="modal-body">
          <input type="hidden" id="edit_request_id" name="request_id">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Requested By</label>
                <input type="text" class="form-control" id="edit_requested_by" readonly style="background: #f5f5f5;">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Category</label>
                <input type="text" class="form-control" id="edit_category" readonly style="background: #f5f5f5;">
              </div>
            </div>
          </div>
          <div class="form-group">
            <label for="edit_item_name">Item Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="edit_item_name" name="item_name" required>
          </div>
          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label for="edit_quantity">Quantity <span class="text-danger">*</span></label>
                <input type="number" step="1" class="form-control" id="edit_quantity" name="quantity" required min="1">
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="edit_unit">Unit <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="edit_unit" name="unit" required>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label for="edit_priority">Priority <span class="text-danger">*</span></label>
                <select class="form-control" id="edit_priority" name="priority" required>
                  <option value="low">Low</option>
                  <option value="medium">Medium</option>
                  <option value="high">High</option>
                  <option value="urgent">Urgent</option>
                </select>
              </div>
            </div>
          </div>
          <div class="form-group">
            <label for="edit_reason">Reason</label>
            <textarea class="form-control" id="edit_reason" name="reason" rows="3"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">
            <i class="fa fa-save"></i> Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('dashboard_assets/js/plugins/sweetalert.min.js') }}"></script>
<script>
$(document).ready(function() {
    // Department select all checkboxes
    $('.department-select-all').on('change', function() {
        var department = $(this).data('department');
        var isChecked = $(this).prop('checked');
        $('.request-checkbox[data-department="' + department + '"]').prop('checked', isChecked);
        updateButtons();
    });
    
    // Select All checkbox (for fallback table)
    $('#selectAll').on('change', function() {
        $('.request-checkbox').prop('checked', $(this).prop('checked'));
        updateButtons();
    });
    
    // Individual checkboxes
    $(document).on('change', '.request-checkbox', function() {
        updateButtons();
        var department = $(this).data('department');
        if (department) {
            var departmentCheckboxes = $('.request-checkbox[data-department="' + department + '"]');
            var departmentSelectAll = $('.department-select-all[data-department="' + department + '"]');
            departmentSelectAll.prop('checked', departmentCheckboxes.length === departmentCheckboxes.filter(':checked').length);
        }
        $('#selectAll').prop('checked', $('.request-checkbox:checked').length === $('.request-checkbox').length);
    });
    
    function updateButtons() {
        var checkedBoxes = $('.request-checkbox:checked');
        var checkedCount = checkedBoxes.length;
        
        // Check if any checked requests are pending (for bulk approve)
        var hasPending = false;
        checkedBoxes.each(function() {
            if ($(this).data('status') === 'pending') {
                hasPending = true;
                return false; // break loop
            }
        });
        
        // Check if any checked requests are approved (for shopping list)
        var hasApproved = false;
        checkedBoxes.each(function() {
            if ($(this).data('status') === 'approved') {
                hasApproved = true;
                return false; // break loop
            }
        });
        
        // Enable/disable bulk approve button (only if pending requests are selected)
        $('#bulkApproveBtn').prop('disabled', !hasPending || checkedCount === 0);
        
        // Enable/disable add to shopping list button (only if approved requests are selected)
        $('#addToShoppingListBtn').prop('disabled', !hasApproved || checkedCount === 0);
    }
    
    function updateAddToShoppingListButton() {
        updateButtons();
    }
    
    // Edit button
    $(document).on('click', '.edit-btn', function() {
        var requestId = $(this).data('request-id');
        var row = $(this).closest('tr');
        
        // Get data from table row
        var requestedBy = row.find('td').eq(1).find('strong').text();
        var departmentGroup = row.closest('.department-group');
        var category = row.find('td').eq(3).text().trim();
        var itemName = row.find('td').eq(2).find('strong').text();
        var quantityText = row.find('td').eq(4).text().trim();
        var quantity = quantityText.split(' ')[0];
        var unit = quantityText.split(' ').slice(1).join(' ');
        var priorityBadge = row.find('.badge').first();
        var priority = priorityBadge.hasClass('badge-danger') ? 'urgent' : 
                      (priorityBadge.hasClass('badge-warning') ? 'high' : 
                      (priorityBadge.hasClass('badge-info') ? 'medium' : 'low'));
        var reason = row.find('td').eq(7).text().trim();
        
        // Fetch full request data via AJAX
        $.ajax({
            url: '{{ route('admin.purchase-requests.show', ':id') }}'.replace(':id', requestId),
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    $('#edit_request_id').val(requestId);
                    $('#edit_requested_by').val(response.requested_by || requestedBy);
                    $('#edit_category').val(response.category ? response.category.replace('_', ' ') : category);
                    $('#edit_item_name').val(response.item_name || itemName);
                    $('#edit_quantity').val(response.quantity || quantity);
                    $('#edit_unit').val(response.unit || unit);
                    $('#edit_priority').val(response.priority || priority);
                    $('#edit_reason').val(response.reason || reason);
                    $('#editModal').modal('show');
                }
            },
            error: function() {
                // Fallback to row data
                $('#edit_request_id').val(requestId);
                $('#edit_requested_by').val(requestedBy);
                $('#edit_category').val(category);
                $('#edit_item_name').val(itemName);
                $('#edit_quantity').val(quantity);
                $('#edit_unit').val(unit);
                $('#edit_priority').val(priority);
                $('#edit_reason').val(reason);
                $('#editModal').modal('show');
            }
        });
    });
    
    // Edit form submission
    $('#editForm').on('submit', function(e) {
        e.preventDefault();
        
        var requestId = $('#edit_request_id').val();
        var formData = {
            _token: '{{ csrf_token() }}',
            _method: 'PUT',
            item_name: $('#edit_item_name').val(),
            quantity: $('#edit_quantity').val(),
            unit: $('#edit_unit').val(),
            priority: $('#edit_priority').val(),
            reason: $('#edit_reason').val()
        };
        
        $.ajax({
            url: '{{ route('admin.purchase-requests.update', ':id') }}'.replace(':id', requestId),
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    swal({
                        title: "Success!",
                        text: response.message,
                        type: "success",
                        timer: 2000,
                        showConfirmButton: false
                    });
                    $('#editModal').modal('hide');
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                }
            },
            error: function(xhr) {
                var errorMsg = xhr.responseJSON?.message || 'Failed to update request.';
                swal("Error!", errorMsg, "error");
            }
        });
    });
    
    // Bulk approve requests
    $('#bulkApproveBtn').on('click', function() {
        var selectedIds = [];
        $('.request-checkbox:checked').each(function() {
            var status = $(this).data('status');
            if (status === 'pending') {
                selectedIds.push($(this).val());
            }
        });
        
        if (selectedIds.length === 0) {
            swal("Error!", "Please select at least one pending request to approve.", "error");
            return;
        }
        
        swal({
            title: "Approve Requests?",
            text: "Are you sure you want to approve " + selectedIds.length + " request(s)?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#28a745",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Yes, approve them!",
            cancelButtonText: "Cancel"
        }, function(isConfirm) {
            if (isConfirm) {
                $.ajax({
                    url: '{{ route("admin.purchase-requests.bulk-approve") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        request_ids: selectedIds
                    },
                    success: function(response) {
                        if (response.success) {
                            swal({
                                title: "Success!",
                                text: response.message,
                                type: "success",
                                timer: 2000,
                                showConfirmButton: false
                            });
                            setTimeout(function() {
                                location.reload();
                            }, 2000);
                        }
                    },
                    error: function(xhr) {
                        var errorMsg = xhr.responseJSON?.message || 'Failed to approve requests.';
                        swal("Error!", errorMsg, "error");
                    }
                });
            }
        });
    });
    
    // Approve request (single)
    $(document).on('click', '.approve-btn', function() {
        var requestId = $(this).data('request-id');
        
        $.ajax({
            url: '/manager/purchase-requests/' + requestId + '/approve',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    swal({
                        title: "Success!",
                        text: response.message,
                        type: "success",
                        timer: 2000,
                        showConfirmButton: false
                    });
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                }
            },
            error: function(xhr) {
                var errorMsg = xhr.responseJSON?.message || 'Failed to approve request.';
                swal("Error!", errorMsg, "error");
            }
        });
    });
    
    // Reject request
    $('.reject-btn').on('click', function() {
        var requestId = $(this).data('request-id');
        $('#reject_request_id').val(requestId);
        $('#rejection_reason').val('');
        $('#rejectModal').modal('show');
    });
    
    $('#rejectForm').on('submit', function(e) {
        e.preventDefault();
        
        var requestId = $('#reject_request_id').val();
        var rejectionReason = $('#rejection_reason').val();
        
        $.ajax({
            url: '/manager/purchase-requests/' + requestId + '/reject',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                rejection_reason: rejectionReason
            },
            success: function(response) {
                if (response.success) {
                    swal({
                        title: "Success!",
                        text: response.message,
                        type: "success",
                        timer: 2000,
                        showConfirmButton: false
                    });
                    $('#rejectModal').modal('hide');
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                }
            },
            error: function(xhr) {
                var errorMsg = xhr.responseJSON?.message || 'Failed to reject request.';
                swal("Error!", errorMsg, "error");
            }
        });
    });
    
    // Add to shopping list
    $('#addToShoppingListBtn').on('click', function() {
        var selectedIds = [];
        $('.request-checkbox:checked').each(function() {
            selectedIds.push($(this).val());
        });
        
        if (selectedIds.length === 0) {
            swal("Error!", "Please select at least one approved request.", "error");
            return;
        }
        
        $.ajax({
            url: '{{ route("admin.purchase-requests.add-to-shopping-list") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                request_ids: selectedIds
            },
            success: function(response) {
                if (response.success) {
                    // Redirect to shopping list create page with pre-filled data
                    window.location.href = response.redirect_url || '{{ route("admin.restaurants.shopping-list.create") }}';
                }
            },
            error: function(xhr) {
                var errorMsg = xhr.responseJSON?.message || 'Failed to add requests to shopping list.';
                swal("Error!", errorMsg, "error");
            }
        });
    });
});
</script>
@endsection
