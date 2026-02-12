@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-shield"></i> Super Administrator Dashboard</h1>
    <p>System Control & Management</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="#">Super Admin Dashboard</a></li>
  </ul>
</div>

<!-- System Statistics -->
<div class="row mb-3">
  <div class="col-md-6 col-lg-3">
    <div class="widget-small primary coloured-icon">
      <i class="icon fa fa-users fa-3x"></i>
      <div class="info">
        <h4>Total Users</h4>
        <p><b>{{ $stats['total_users'] ?? 0 }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-lg-3">
    <div class="widget-small info coloured-icon">
      <i class="icon fa fa-user-secret fa-3x"></i>
      <div class="info">
        <h4>Super Admins</h4>
        <p><b>{{ $stats['super_admins'] ?? 0 }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-lg-3">
    <div class="widget-small warning coloured-icon">
      <i class="icon fa fa-user-tie fa-3x"></i>
      <div class="info">
        <h4>Managers</h4>
        <p><b>{{ $stats['managers'] ?? 0 }}</b></p>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-lg-3">
    <div class="widget-small danger coloured-icon">
      <i class="icon fa fa-key fa-3x"></i>
      <div class="info">
        <h4>Roles</h4>
        <p><b>{{ $stats['total_roles'] ?? 0 }}</b></p>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <!-- Recent Activity Logs -->
  <div class="col-md-6">
    <div class="tile">
      <h3 class="tile-title">Recent Activity Logs</h3>
      <div class="table-responsive">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>User</th>
              <th>Action</th>
              <th>Time</th>
            </tr>
          </thead>
          <tbody>
            @forelse($recentActivities ?? [] as $activity)
            <tr>
              <td>{{ $activity->user->name ?? 'System' }}</td>
              <td><span class="badge badge-info">{{ ucfirst($activity->action) }}</span></td>
              <td>{{ $activity->created_at->diffForHumans() }}</td>
            </tr>
            @empty
            <tr>
              <td colspan="3" class="text-center">No recent activities</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="tile-footer">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
          <div class="mb-2 mb-md-0">
            @if($recentActivities->hasPages())
            <nav aria-label="Page navigation">
              <ul class="pagination pagination-sm">
                @if($recentActivities->onFirstPage())
                  <li class="page-item disabled"><span class="page-link"><i class="fa fa-angle-left"></i> Prev</span></li>
                @else
                  <li class="page-item"><a class="page-link" href="{{ $recentActivities->previousPageUrl() }}"><i class="fa fa-angle-left"></i> Prev</a></li>
                @endif
                @php
                  $currentPage = $recentActivities->currentPage();
                  $lastPage = $recentActivities->lastPage();
                  $startPage = max(1, $currentPage - 2);
                  $endPage = min($lastPage, $currentPage + 2);
                @endphp
                @if($startPage > 1)
                  <li class="page-item"><a class="page-link" href="{{ $recentActivities->url(1) }}">1</a></li>
                  @if($startPage > 2)<li class="page-item disabled"><span class="page-link">...</span></li>@endif
                @endif
                @for($page = $startPage; $page <= $endPage; $page++)
                  <li class="page-item {{ $page == $currentPage ? 'active' : '' }}">
                    @if($page == $currentPage)
                      <span class="page-link">{{ $page }}</span>
                    @else
                      <a class="page-link" href="{{ $recentActivities->url($page) }}">{{ $page }}</a>
                    @endif
                  </li>
                @endfor
                @if($endPage < $lastPage)
                  @if($endPage < $lastPage - 1)<li class="page-item disabled"><span class="page-link">...</span></li>@endif
                  <li class="page-item"><a class="page-link" href="{{ $recentActivities->url($lastPage) }}">{{ $lastPage }}</a></li>
                @endif
                @if($recentActivities->hasMorePages())
                  <li class="page-item"><a class="page-link" href="{{ $recentActivities->nextPageUrl() }}">Next <i class="fa fa-angle-right"></i></a></li>
                @else
                  <li class="page-item disabled"><span class="page-link">Next <i class="fa fa-angle-right"></i></span></li>
                @endif
              </ul>
            </nav>
            @endif
          </div>
          <div>
            <a href="{{ route('super_admin.activity-logs') }}" class="btn btn-primary">View All Logs</a>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <!-- System Logs Summary -->
  <div class="col-md-6">
    <div class="tile">
      <h3 class="tile-title">System Logs (Today)</h3>
      <div class="row">
        <div class="col-4 text-center">
          <div class="widget-small danger">
            <div class="info">
              <h4>Errors</h4>
              <p><b>{{ $systemLogsSummary['error'] ?? 0 }}</b></p>
            </div>
          </div>
        </div>
        <div class="col-4 text-center">
          <div class="widget-small warning">
            <div class="info">
              <h4>Warnings</h4>
              <p><b>{{ $systemLogsSummary['warning'] ?? 0 }}</b></p>
            </div>
          </div>
        </div>
        <div class="col-4 text-center">
          <div class="widget-small info">
            <div class="info">
              <h4>Info</h4>
              <p><b>{{ $systemLogsSummary['info'] ?? 0 }}</b></p>
            </div>
          </div>
        </div>
      </div>
      <div class="tile-footer mt-3">
        <a href="{{ route('super_admin.system-logs') }}" class="btn btn-primary">View System Logs</a>
      </div>
    </div>
  </div>
</div>

<!-- Currently Logged In Users -->
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-user-circle"></i> Currently Active Users ({{ $loggedInUsers->total() ?? 0 }})</h3>
      <div class="tile-body">
        <div class="table-responsive">
          <table class="table table-hover table-bordered">
            <thead>
              <tr>
                <th>User</th>
                <th>Role</th>
                <th>Email</th>
                <th>IP Address</th>
                <th>User Agent</th>
                <th>Last Activity</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($loggedInUsers ?? [] as $session)
              <tr>
                <td><strong>{{ $session['user']->name }}</strong></td>
                <td>
                  @if($session['user']->role == 'super_admin')
                    <span class="badge badge-danger">Super Admin</span>
                  @elseif($session['user']->role == 'manager')
                    <span class="badge badge-warning">Manager</span>
                  @elseif($session['user']->role == 'reception')
                    <span class="badge badge-info">Reception</span>
                  @else
                    <span class="badge badge-success">Customer</span>
                  @endif
                </td>
                <td>{{ $session['user']->email }}</td>
                <td><code>{{ $session['ip_address'] ?? 'N/A' }}</code></td>
                <td><small>{{ Str::limit($session['user_agent'] ?? 'N/A', 50) }}</small></td>
                <td>{{ $session['last_activity']->diffForHumans() }}</td>
                <td><span class="badge badge-success">Active</span></td>
                <td>
                  <button type="button" class="btn btn-sm btn-info" 
                          onclick="showActiveUserDetails({{ $session['user']->id }}, '{{ $session['user']->name }}', '{{ $session['user']->email }}', '{{ $session['user']->role ?? 'guest' }}', '{{ $session['ip_address'] ?? '' }}', '{{ addslashes($session['user_agent'] ?? '') }}', '{{ $session['last_activity']->format('Y-m-d H:i:s') }}', '{{ $session['session_id'] ?? '' }}')">
                    <i class="fa fa-eye"></i> Details
                  </button>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="8" class="text-center">No active users at the moment</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        @if($loggedInUsers->hasPages())
        <div class="tile-footer">
          <nav aria-label="Page navigation">
            <ul class="pagination pagination-sm justify-content-center">
              @if($loggedInUsers->onFirstPage())
                <li class="page-item disabled"><span class="page-link"><i class="fa fa-angle-left"></i> Prev</span></li>
              @else
                <li class="page-item"><a class="page-link" href="{{ $loggedInUsers->previousPageUrl() }}"><i class="fa fa-angle-left"></i> Prev</a></li>
              @endif
              @php
                $currentPage = $loggedInUsers->currentPage();
                $lastPage = $loggedInUsers->lastPage();
                $startPage = max(1, $currentPage - 2);
                $endPage = min($lastPage, $currentPage + 2);
              @endphp
              @if($startPage > 1)
                <li class="page-item"><a class="page-link" href="{{ $loggedInUsers->url(1) }}">1</a></li>
                @if($startPage > 2)<li class="page-item disabled"><span class="page-link">...</span></li>@endif
              @endif
              @for($page = $startPage; $page <= $endPage; $page++)
                <li class="page-item {{ $page == $currentPage ? 'active' : '' }}">
                  @if($page == $currentPage)
                    <span class="page-link">{{ $page }}</span>
                  @else
                    <a class="page-link" href="{{ $loggedInUsers->url($page) }}">{{ $page }}</a>
                  @endif
                </li>
              @endfor
              @if($endPage < $lastPage)
                @if($endPage < $lastPage - 1)<li class="page-item disabled"><span class="page-link">...</span></li>@endif
                <li class="page-item"><a class="page-link" href="{{ $loggedInUsers->url($lastPage) }}">{{ $lastPage }}</a></li>
              @endif
              @if($loggedInUsers->hasMorePages())
                <li class="page-item"><a class="page-link" href="{{ $loggedInUsers->nextPageUrl() }}">Next <i class="fa fa-angle-right"></i></a></li>
              @else
                <li class="page-item disabled"><span class="page-link">Next <i class="fa fa-angle-right"></i></span></li>
              @endif
            </ul>
          </nav>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- Login Statistics & Recent Login/Logout -->
<div class="row mb-3">
  <div class="col-md-4">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-sign-in"></i> Login Statistics</h3>
      <div class="tile-body">
        <div class="widget-small primary">
          <div class="info">
            <h4>Today</h4>
            <p><b>{{ $loginStats['today'] ?? 0 }}</b> logins</p>
          </div>
        </div>
        <div class="widget-small info mt-2">
          <div class="info">
            <h4>This Week</h4>
            <p><b>{{ $loginStats['this_week'] ?? 0 }}</b> logins</p>
          </div>
        </div>
        <div class="widget-small warning mt-2">
          <div class="info">
            <h4>This Month</h4>
            <p><b>{{ $loginStats['this_month'] ?? 0 }}</b> logins</p>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-md-8">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-history"></i> Recent Login/Logout Activity</h3>
      <div class="tile-body">
        <div class="table-responsive">
          <table class="table table-hover table-bordered">
            <thead>
              <tr>
                <th>User</th>
                <th>Action</th>
                <th>IP Address</th>
                <th>Time</th>
              </tr>
            </thead>
            <tbody>
              @forelse($loginLogoutActivities ?? [] as $activity)
              <tr>
                <td>
                  <strong>{{ $activity->user->name ?? 'System' }}</strong><br>
                  <small class="text-muted">{{ $activity->user->email ?? '' }}</small>
                </td>
                <td>
                  @if($activity->action == 'logged_in')
                    <span class="badge badge-success"><i class="fa fa-sign-in"></i> Logged In</span>
                  @else
                    <span class="badge badge-danger"><i class="fa fa-sign-out"></i> Logged Out</span>
                  @endif
                </td>
                <td><code>{{ $activity->ip_address }}</code></td>
                <td>{{ $activity->created_at->format('M d, Y H:i:s') }}<br><small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small></td>
              </tr>
              @empty
              <tr>
                <td colspan="4" class="text-center">No login/logout activity</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        @if($loginLogoutActivities->hasPages())
        <div class="tile-footer">
          <nav aria-label="Page navigation">
            <ul class="pagination pagination-sm justify-content-center">
              @if($loginLogoutActivities->onFirstPage())
                <li class="page-item disabled"><span class="page-link"><i class="fa fa-angle-left"></i> Prev</span></li>
              @else
                <li class="page-item"><a class="page-link" href="{{ $loginLogoutActivities->previousPageUrl() }}"><i class="fa fa-angle-left"></i> Prev</a></li>
              @endif
              @php
                $currentPage = $loginLogoutActivities->currentPage();
                $lastPage = $loginLogoutActivities->lastPage();
                $startPage = max(1, $currentPage - 2);
                $endPage = min($lastPage, $currentPage + 2);
              @endphp
              @if($startPage > 1)
                <li class="page-item"><a class="page-link" href="{{ $loginLogoutActivities->url(1) }}">1</a></li>
                @if($startPage > 2)<li class="page-item disabled"><span class="page-link">...</span></li>@endif
              @endif
              @for($page = $startPage; $page <= $endPage; $page++)
                <li class="page-item {{ $page == $currentPage ? 'active' : '' }}">
                  @if($page == $currentPage)
                    <span class="page-link">{{ $page }}</span>
                  @else
                    <a class="page-link" href="{{ $loginLogoutActivities->url($page) }}">{{ $page }}</a>
                  @endif
                </li>
              @endfor
              @if($endPage < $lastPage)
                @if($endPage < $lastPage - 1)<li class="page-item disabled"><span class="page-link">...</span></li>@endif
                <li class="page-item"><a class="page-link" href="{{ $loginLogoutActivities->url($lastPage) }}">{{ $lastPage }}</a></li>
              @endif
              @if($loginLogoutActivities->hasMorePages())
                <li class="page-item"><a class="page-link" href="{{ $loginLogoutActivities->nextPageUrl() }}">Next <i class="fa fa-angle-right"></i></a></li>
              @else
                <li class="page-item disabled"><span class="page-link">Next <i class="fa fa-angle-right"></i></span></li>
              @endif
            </ul>
          </nav>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- Technical Issues -->
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-exclamation-triangle"></i> Technical Issues (Errors & Critical)</h3>
      <div class="tile-body">
        <div class="table-responsive">
          <table class="table table-hover table-bordered">
            <thead>
              <tr>
                <th>Level</th>
                <th>Channel</th>
                <th>Message</th>
                <th>User</th>
                <th>IP Address</th>
                <th>Time</th>
              </tr>
            </thead>
            <tbody>
              @forelse($technicalIssues ?? [] as $issue)
              <tr>
                <td>
                  @if($issue->level == 'error')
                    <span class="badge badge-danger">ERROR</span>
                  @else
                    <span class="badge badge-dark">CRITICAL</span>
                  @endif
                </td>
                <td><code>{{ $issue->channel ?? 'system' }}</code></td>
                <td>{{ Str::limit($issue->message, 100) }}</td>
                <td>
                  @if($issue->user_id)
                    @php
                      $issueUser = \App\Models\User::find($issue->user_id);
                    @endphp
                    {{ $issueUser->name ?? 'User #' . $issue->user_id }}
                  @else
                    <span class="text-muted">System</span>
                  @endif
                </td>
                <td><code>{{ $issue->ip_address ?? 'N/A' }}</code></td>
                <td>{{ $issue->created_at->format('M d, Y H:i:s') }}<br><small class="text-muted">{{ $issue->created_at->diffForHumans() }}</small></td>
              </tr>
              @empty
              <tr>
                <td colspan="6" class="text-center">No technical issues found</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <div class="tile-footer">
          <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div class="mb-2 mb-md-0">
              @if($technicalIssues->hasPages())
              <nav aria-label="Page navigation">
                <ul class="pagination pagination-sm">
                  @if($technicalIssues->onFirstPage())
                    <li class="page-item disabled"><span class="page-link"><i class="fa fa-angle-left"></i> Prev</span></li>
                  @else
                    <li class="page-item"><a class="page-link" href="{{ $technicalIssues->previousPageUrl() }}"><i class="fa fa-angle-left"></i> Prev</a></li>
                  @endif
                  @php
                    $currentPage = $technicalIssues->currentPage();
                    $lastPage = $technicalIssues->lastPage();
                    $startPage = max(1, $currentPage - 2);
                    $endPage = min($lastPage, $currentPage + 2);
                  @endphp
                  @if($startPage > 1)
                    <li class="page-item"><a class="page-link" href="{{ $technicalIssues->url(1) }}">1</a></li>
                    @if($startPage > 2)<li class="page-item disabled"><span class="page-link">...</span></li>@endif
                  @endif
                  @for($page = $startPage; $page <= $endPage; $page++)
                    <li class="page-item {{ $page == $currentPage ? 'active' : '' }}">
                      @if($page == $currentPage)
                        <span class="page-link">{{ $page }}</span>
                      @else
                        <a class="page-link" href="{{ $technicalIssues->url($page) }}">{{ $page }}</a>
                      @endif
                    </li>
                  @endfor
                  @if($endPage < $lastPage)
                    @if($endPage < $lastPage - 1)<li class="page-item disabled"><span class="page-link">...</span></li>@endif
                    <li class="page-item"><a class="page-link" href="{{ $technicalIssues->url($lastPage) }}">{{ $lastPage }}</a></li>
                  @endif
                  @if($technicalIssues->hasMorePages())
                    <li class="page-item"><a class="page-link" href="{{ $technicalIssues->nextPageUrl() }}">Next <i class="fa fa-angle-right"></i></a></li>
                  @else
                    <li class="page-item disabled"><span class="page-link">Next <i class="fa fa-angle-right"></i></span></li>
                  @endif
                </ul>
              </nav>
              @endif
            </div>
            <div>
              <a href="{{ route('super_admin.system-logs') }}" class="btn btn-primary">View All System Logs</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Users with Last Login -->
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title"><i class="fa fa-users"></i> All Users with Last Login Information ({{ $usersWithLastLogin->total() ?? 0 }})</h3>
      <div class="tile-body">
        <div class="table-responsive">
          <table class="table table-hover table-bordered">
            <thead>
              <tr>
                <th>User</th>
                <th>Role</th>
                <th>Email</th>
                <th>Last Login</th>
                <th>Last Login IP</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              @forelse($usersWithLastLogin ?? [] as $user)
              <tr>
                <td><strong>{{ $user->name }}</strong></td>
                <td>
                  @if($user->role == 'super_admin')
                    <span class="badge badge-danger">Super Admin</span>
                  @elseif($user->role == 'manager')
                    <span class="badge badge-warning">Manager</span>
                  @elseif($user->role == 'reception')
                    <span class="badge badge-info">Reception</span>
                  @else
                    <span class="badge badge-success">Customer</span>
                  @endif
                </td>
                <td>{{ $user->email }}</td>
                <td>
                  @if($user->last_login)
                    {{ \Carbon\Carbon::parse($user->last_login)->format('M d, Y H:i:s') }}<br>
                    <small class="text-muted">{{ \Carbon\Carbon::parse($user->last_login)->diffForHumans() }}</small>
                  @else
                    <span class="text-muted">Never</span>
                  @endif
                </td>
                <td><code>{{ $user->last_login_ip ?? 'N/A' }}</code></td>
                <td>
                  @php
                    // Check if user is active by email (more reliable than ID since Staff and Guest can have same IDs)
                    $isActive = !empty($activeUserEmails[$user->email]);
                  @endphp
                  @if($isActive)
                    <span class="badge badge-success">Active Now</span>
                  @else
                    <span class="badge badge-secondary">Offline</span>
                  @endif
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="6" class="text-center">No users found</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        @if($usersWithLastLogin->hasPages())
        <div class="tile-footer">
          <nav aria-label="Page navigation">
            <ul class="pagination pagination-sm justify-content-center">
              @if($usersWithLastLogin->onFirstPage())
                <li class="page-item disabled"><span class="page-link"><i class="fa fa-angle-left"></i> Prev</span></li>
              @else
                <li class="page-item"><a class="page-link" href="{{ $usersWithLastLogin->previousPageUrl() }}"><i class="fa fa-angle-left"></i> Prev</a></li>
              @endif
              @php
                $currentPage = $usersWithLastLogin->currentPage();
                $lastPage = $usersWithLastLogin->lastPage();
                $startPage = max(1, $currentPage - 2);
                $endPage = min($lastPage, $currentPage + 2);
              @endphp
              @if($startPage > 1)
                <li class="page-item"><a class="page-link" href="{{ $usersWithLastLogin->url(1) }}">1</a></li>
                @if($startPage > 2)<li class="page-item disabled"><span class="page-link">...</span></li>@endif
              @endif
              @for($page = $startPage; $page <= $endPage; $page++)
                <li class="page-item {{ $page == $currentPage ? 'active' : '' }}">
                  @if($page == $currentPage)
                    <span class="page-link">{{ $page }}</span>
                  @else
                    <a class="page-link" href="{{ $usersWithLastLogin->url($page) }}">{{ $page }}</a>
                  @endif
                </li>
              @endfor
              @if($endPage < $lastPage)
                @if($endPage < $lastPage - 1)<li class="page-item disabled"><span class="page-link">...</span></li>@endif
                <li class="page-item"><a class="page-link" href="{{ $usersWithLastLogin->url($lastPage) }}">{{ $lastPage }}</a></li>
              @endif
              @if($usersWithLastLogin->hasMorePages())
                <li class="page-item"><a class="page-link" href="{{ $usersWithLastLogin->nextPageUrl() }}">Next <i class="fa fa-angle-right"></i></a></li>
              @else
                <li class="page-item disabled"><span class="page-link">Next <i class="fa fa-angle-right"></i></span></li>
              @endif
            </ul>
          </nav>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- Quick Actions -->
<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <h3 class="tile-title">Quick Actions</h3>
      <div class="row">
        <div class="col-md-3 mb-3">
          <a href="{{ route('super_admin.users') }}" class="btn btn-block btn-primary">
            <i class="fa fa-users"></i> Manage Users
          </a>
        </div>
        <div class="col-md-3 mb-3">
          <a href="{{ route('super_admin.roles') }}" class="btn btn-block btn-info">
            <i class="fa fa-key"></i> Manage Roles
          </a>
        </div>
        <div class="col-md-3 mb-3">
          <a href="{{ route('super_admin.permissions') }}" class="btn btn-block btn-warning">
            <i class="fa fa-shield"></i> Manage Permissions
          </a>
        </div>
        <div class="col-md-3 mb-3">
          <a href="{{ route('super_admin.activity-logs') }}" class="btn btn-block btn-success">
            <i class="fa fa-history"></i> Activity Logs
          </a>
        </div>
        <div class="col-md-3 mb-3">
          <a href="{{ route('super_admin.system-settings') }}" class="btn btn-block btn-secondary">
            <i class="fa fa-cog"></i> System Settings
          </a>
        </div>
        <div class="col-md-3 mb-3">
          <a href="{{ route('super_admin.failed-login-attempts') }}" class="btn btn-block btn-danger">
            <i class="fa fa-shield"></i> Failed Logins
          </a>
        </div>
        <div class="col-md-3 mb-3">
          <a href="{{ route('super_admin.active-sessions') }}" class="btn btn-block btn-info">
            <i class="fa fa-users"></i> Active Sessions
          </a>
        </div>
        <div class="col-md-3 mb-3">
          <a href="{{ route('super_admin.cache-management') }}" class="btn btn-block btn-warning">
            <i class="fa fa-refresh"></i> Cache Management
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  /* Bootstrap pagination styling - compact size */
  .pagination-sm .page-link {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    line-height: 1.5;
  }
  
  .pagination-sm .page-item:first-child .page-link {
    border-top-left-radius: 0.2rem;
    border-bottom-left-radius: 0.2rem;
  }
  
  .pagination-sm .page-item:last-child .page-link {
    border-top-right-radius: 0.2rem;
    border-bottom-right-radius: 0.2rem;
  }
  
  .pagination-sm .page-item.active .page-link {
    z-index: 2;
    color: #fff;
    background-color: #007bff;
    border-color: #007bff;
  }
  
  .pagination-sm .page-item.disabled .page-link {
    color: #868e96;
    pointer-events: none;
    background-color: #fff;
    border-color: #ddd;
  }
  
  .pagination-sm .page-link i {
    font-size: 0.75rem;
  }
  
  /* Fix tile footer layout for pagination and buttons */
  .tile-footer {
    padding: 15px;
  }
  
  .tile-footer .d-flex {
    gap: 15px;
  }
  
  /* Ensure buttons align properly with pagination */
  .tile-footer .btn {
    white-space: nowrap;
    flex-shrink: 0;
  }
</style>

<!-- Active User Details Modal -->
<div class="modal fade" id="activeUserDetailsModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-user-circle"></i> Active User Session Details</h5>
        <button type="button" class="close" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>
      <div class="modal-body" id="activeUserDetailsContent">
        <!-- Content will be populated by JavaScript -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- IP Address Lookup Modal -->
<div class="modal fade" id="ipLookupModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa fa-map-marker"></i> IP Address Information</h5>
        <button type="button" class="close" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div id="ipLookupContent">
          <div class="text-center">
            <i class="fa fa-spinner fa-spin fa-2x"></i>
            <p>Loading IP information...</p>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
function showActiveUserDetails(userId, userName, userEmail, userRole, ipAddress, userAgent, lastActivity, sessionId) {
  // Show modal
  $('#activeUserDetailsModal').modal('show');
  
  // Determine role badge
  let roleBadge = '';
  let roleText = '';
  if (userRole == 'super_admin') {
    roleBadge = 'badge-danger';
    roleText = 'Super Admin';
  } else if (userRole == 'manager') {
    roleBadge = 'badge-warning';
    roleText = 'Manager';
  } else if (userRole == 'reception') {
    roleBadge = 'badge-info';
    roleText = 'Reception';
  } else {
    roleBadge = 'badge-success';
    roleText = 'Guest';
  }
  
  // Format last activity
  const lastActivityDate = new Date(lastActivity);
  const lastActivityFormatted = lastActivityDate.toLocaleString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit'
  });
  
  // Build HTML content
  let html = `
    <div class="row">
      <div class="col-md-6">
        <h6><i class="fa fa-user"></i> User Information</h6>
        <table class="table table-sm table-bordered">
          <tr>
            <th width="40%">Name:</th>
            <td><strong>${userName}</strong></td>
          </tr>
          <tr>
            <th>Email:</th>
            <td>${userEmail}</td>
          </tr>
          <tr>
            <th>Role:</th>
            <td><span class="badge ${roleBadge}">${roleText}</span></td>
          </tr>
          <tr>
            <th>User ID:</th>
            <td>${userId}</td>
          </tr>
        </table>
      </div>
      <div class="col-md-6">
        <h6><i class="fa fa-clock-o"></i> Session Information</h6>
        <table class="table table-sm table-bordered">
          <tr>
            <th width="40%">Session ID:</th>
            <td><code>${sessionId || 'N/A'}</code></td>
          </tr>
          <tr>
            <th>Last Activity:</th>
            <td>${lastActivityFormatted}</td>
          </tr>
          <tr>
            <th>Status:</th>
            <td><span class="badge badge-success">Active</span></td>
          </tr>
        </table>
      </div>
    </div>
    <div class="row mt-3">
      <div class="col-md-6">
        <h6><i class="fa fa-desktop"></i> Browser Information</h6>
        <table class="table table-sm table-bordered">
          <tr>
            <th width="40%">User Agent:</th>
            <td><small>${userAgent || 'N/A'}</small></td>
          </tr>
        </table>
      </div>
      <div class="col-md-6">
        <h6><i class="fa fa-globe"></i> Network Information</h6>
        <table class="table table-sm table-bordered">
          <tr>
            <th width="40%">IP Address:</th>
            <td><code>${ipAddress || 'N/A'}</code></td>
          </tr>
        </table>
        ${ipAddress && ipAddress !== '127.0.0.1' && ipAddress !== '::1' && ipAddress !== 'N/A' ? `
        <button type="button" class="btn btn-sm btn-primary mt-2" onclick="lookupIpAddress('${ipAddress}')">
          <i class="fa fa-map-marker"></i> View IP Location
        </button>
        ` : ipAddress && (ipAddress === '127.0.0.1' || ipAddress === '::1') ? `
        <div class="alert alert-info mt-2">
          <i class="fa fa-info-circle"></i> This is a localhost IP address (local development/testing).
        </div>
        ` : ''}
      </div>
    </div>
    <div class="row mt-3" id="ipLocationInfo">
      <!-- IP Location information will be loaded here -->
    </div>
  `;
  
  $('#activeUserDetailsContent').html(html);
  
  // Auto-load IP location information
  if (ipAddress && ipAddress !== 'N/A') {
    if (ipAddress === '127.0.0.1' || ipAddress === '::1') {
      // Show localhost information
      $('#ipLocationInfo').html(`
        <div class="col-md-12">
          <h6><i class="fa fa-map-marker"></i> IP Location Information</h6>
          <div class="alert alert-info">
            <strong><i class="fa fa-info-circle"></i> Localhost IP Address</strong><br>
            This IP address (${ipAddress}) is a localhost/loopback address used for local development and testing.<br>
            <strong>Location:</strong> Local Machine<br>
            <strong>Country:</strong> N/A (Local)<br>
            <strong>ISP:</strong> N/A (Local Network)
          </div>
        </div>
      `);
    } else {
      // Load real IP location
      lookupIpAddressForDetails(ipAddress);
    }
  }
}

function lookupIpAddressForDetails(ipAddress) {
  // Check if it's a localhost or private IP address
  const isLocalhost = ipAddress === '127.0.0.1' || ipAddress === '::1' || ipAddress === 'localhost';
  const isPrivateIP = /^(10\.|172\.(1[6-9]|2[0-9]|3[01])\.|192\.168\.)/.test(ipAddress);
  
  if (isLocalhost || isPrivateIP) {
    // Show localhost/private IP information
    $('#ipLocationInfo').html(`
      <div class="col-md-12">
        <h6><i class="fa fa-map-marker"></i> IP Location Information</h6>
        <div class="alert alert-info">
          <strong><i class="fa fa-info-circle"></i> ${isLocalhost ? 'Localhost' : 'Private'} IP Address</strong><br>
          This IP address (${ipAddress}) is a ${isLocalhost ? 'localhost/loopback' : 'private network'} address.<br>
          <strong>Location:</strong> ${isLocalhost ? 'Local Machine' : 'Private Network'}<br>
          <strong>Country:</strong> N/A (${isLocalhost ? 'Local' : 'Private Network'})<br>
          <strong>ISP:</strong> N/A (${isLocalhost ? 'Local Network' : 'Private Network'})<br>
          <strong>Type:</strong> ${isLocalhost ? 'Loopback Address' : 'RFC 1918 Private Address'}
        </div>
      </div>
    `);
    return;
  }
  
  // Show loading state
  $('#ipLocationInfo').html(`
    <div class="col-md-12">
      <h6><i class="fa fa-map-marker"></i> IP Location Information</h6>
      <div class="text-center">
        <i class="fa fa-spinner fa-spin fa-2x"></i>
        <p>Loading IP location information...</p>
      </div>
    </div>
  `);
  
  // Use ipapi.co (free, HTTPS support, 1000 requests/day)
  fetch(`https://ipapi.co/${ipAddress}/json/`)
    .then(response => response.json())
    .then(data => {
      // Check if there's an error in the response
      if (data.error) {
        // Handle reserved/private IP addresses
        if (data.reason && (data.reason.includes('Reserved') || data.reason.includes('Private'))) {
          $('#ipLocationInfo').html(`
            <div class="col-md-12">
              <h6><i class="fa fa-map-marker"></i> IP Location Information</h6>
              <div class="alert alert-info">
                <strong><i class="fa fa-info-circle"></i> Reserved/Private IP Address</strong><br>
                This IP address (${ipAddress}) is a reserved or private network address.<br>
                <strong>Location:</strong> Private Network/Local Network<br>
                <strong>Country:</strong> N/A (Private Network)<br>
                <strong>ISP:</strong> N/A (Private Network)<br>
                <strong>Type:</strong> ${data.reason}
              </div>
            </div>
          `);
        } else {
          $('#ipLocationInfo').html(`
            <div class="col-md-12">
              <h6><i class="fa fa-map-marker"></i> IP Location Information</h6>
              <div class="alert alert-warning">
                <i class="fa fa-exclamation-triangle"></i> Unable to retrieve IP information: ${data.reason || data.error || 'Unknown error'}
              </div>
            </div>
          `);
        }
        return;
      }
      
      // ipapi.co returns data directly (no status field)
      let html = `
        <div class="col-md-12">
          <h6><i class="fa fa-map-marker"></i> IP Location Information</h6>
          <div class="row">
            <div class="col-md-6">
              <h6><i class="fa fa-globe"></i> Location Details</h6>
              <table class="table table-sm table-bordered">
                <tr>
                  <th width="40%">IP Address:</th>
                  <td><code>${data.ip || ipAddress}</code></td>
                </tr>
                <tr>
                  <th>Country:</th>
                  <td><strong>${data.country_name || 'N/A'}</strong> (${data.country_code || 'N/A'})</td>
                </tr>
                <tr>
                  <th>Region:</th>
                  <td>${data.region || 'N/A'} (${data.region_code || 'N/A'})</td>
                </tr>
                <tr>
                  <th>City:</th>
                  <td>${data.city || 'N/A'}</td>
                </tr>
                <tr>
                  <th>ZIP Code:</th>
                  <td>${data.postal || 'N/A'}</td>
                </tr>
                <tr>
                  <th>Coordinates:</th>
                  <td>${data.latitude || 'N/A'}, ${data.longitude || 'N/A'}</td>
                </tr>
                <tr>
                  <th>Timezone:</th>
                  <td>${data.timezone || 'N/A'}</td>
                </tr>
              </table>
              ${data.latitude && data.longitude ? `
              <a href="https://www.google.com/maps?q=${data.latitude},${data.longitude}" target="_blank" class="btn btn-sm btn-primary">
                <i class="fa fa-map"></i> View on Google Maps
              </a>
              ` : ''}
            </div>
            <div class="col-md-6">
              <h6><i class="fa fa-building"></i> Network & ISP Information</h6>
              <table class="table table-sm table-bordered">
                <tr>
                  <th width="40%">ISP:</th>
                  <td><strong>${data.org || data.isp || 'N/A'}</strong></td>
                </tr>
                <tr>
                  <th>Organization:</th>
                  <td>${data.org || 'N/A'}</td>
                </tr>
                <tr>
                  <th>AS Number:</th>
                  <td>${data.asn || 'N/A'}</td>
                </tr>
                <tr>
                  <th>Currency:</th>
                  <td>${data.currency || 'N/A'}</td>
                </tr>
                <tr>
                  <th>Calling Code:</th>
                  <td>${data.country_calling_code || 'N/A'}</td>
                </tr>
              </table>
            </div>
          </div>
        </div>
      `;
      $('#ipLocationInfo').html(html);
    })
    .catch(error => {
      $('#ipLocationInfo').html(`
        <div class="col-md-12">
          <h6><i class="fa fa-map-marker"></i> IP Location Information</h6>
          <div class="alert alert-danger">
            <i class="fa fa-times-circle"></i> Error fetching IP information: ${error.message}
          </div>
        </div>
      `);
    });
}

function lookupIpAddress(ipAddress) {
  // Show modal
  $('#ipLookupModal').modal('show');
  
  // Check if it's a localhost or private IP address
  const isLocalhost = ipAddress === '127.0.0.1' || ipAddress === '::1' || ipAddress === 'localhost';
  const isPrivateIP = /^(10\.|172\.(1[6-9]|2[0-9]|3[01])\.|192\.168\.)/.test(ipAddress);
  
  if (isLocalhost || isPrivateIP) {
    // Show localhost/private IP information
    $('#ipLookupContent').html(`
      <div class="alert alert-info">
        <strong><i class="fa fa-info-circle"></i> ${isLocalhost ? 'Localhost' : 'Private'} IP Address</strong><br>
        This IP address (${ipAddress}) is a ${isLocalhost ? 'localhost/loopback' : 'private network'} address.<br>
        <strong>Location:</strong> ${isLocalhost ? 'Local Machine' : 'Private Network'}<br>
        <strong>Country:</strong> N/A (${isLocalhost ? 'Local' : 'Private Network'})<br>
        <strong>ISP:</strong> N/A (${isLocalhost ? 'Local Network' : 'Private Network'})<br>
        <strong>Type:</strong> ${isLocalhost ? 'Loopback Address' : 'RFC 1918 Private Address'}
      </div>
    `);
    return;
  }
  
  // Reset content
  $('#ipLookupContent').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-2x"></i><p>Loading IP information...</p></div>');
  
  // Use ipapi.co (free, HTTPS support, 1000 requests/day)
  fetch(`https://ipapi.co/${ipAddress}/json/`)
    .then(response => response.json())
    .then(data => {
      // Check if there's an error in the response
      if (data.error) {
        // Handle reserved/private IP addresses
        if (data.reason && (data.reason.includes('Reserved') || data.reason.includes('Private'))) {
          $('#ipLookupContent').html(`
            <div class="alert alert-info">
              <strong><i class="fa fa-info-circle"></i> Reserved/Private IP Address</strong><br>
              This IP address (${ipAddress}) is a reserved or private network address.<br>
              <strong>Location:</strong> Private Network/Local Network<br>
              <strong>Country:</strong> N/A (Private Network)<br>
              <strong>ISP:</strong> N/A (Private Network)<br>
              <strong>Type:</strong> ${data.reason}
            </div>
          `);
        } else {
          $('#ipLookupContent').html(`
            <div class="alert alert-warning">
              <i class="fa fa-exclamation-triangle"></i> Unable to retrieve IP information: ${data.reason || data.error || 'Unknown error'}
            </div>
          `);
        }
        return;
      }
      
      // ipapi.co returns data directly (no status field)
      let html = `
        <div class="row">
          <div class="col-md-6">
            <h6><i class="fa fa-globe"></i> Location Information</h6>
            <table class="table table-sm table-bordered">
              <tr>
                <th width="40%">IP Address:</th>
                <td><code>${data.ip || ipAddress}</code></td>
              </tr>
              <tr>
                <th>Country:</th>
                <td>${data.country_name || 'N/A'} (${data.country_code || 'N/A'})</td>
              </tr>
              <tr>
                <th>Region:</th>
                <td>${data.region || 'N/A'} (${data.region_code || 'N/A'})</td>
              </tr>
              <tr>
                <th>City:</th>
                <td>${data.city || 'N/A'}</td>
              </tr>
              <tr>
                <th>ZIP Code:</th>
                <td>${data.postal || 'N/A'}</td>
              </tr>
              <tr>
                <th>Coordinates:</th>
                <td>${data.latitude || 'N/A'}, ${data.longitude || 'N/A'}</td>
              </tr>
              <tr>
                <th>Timezone:</th>
                <td>${data.timezone || 'N/A'}</td>
              </tr>
            </table>
          </div>
          <div class="col-md-6">
            <h6><i class="fa fa-building"></i> Network Information</h6>
            <table class="table table-sm table-bordered">
              <tr>
                <th width="40%">ISP:</th>
                <td>${data.org || data.isp || 'N/A'}</td>
              </tr>
              <tr>
                <th>Organization:</th>
                <td>${data.org || 'N/A'}</td>
              </tr>
              <tr>
                <th>AS Number:</th>
                <td>${data.asn || 'N/A'}</td>
              </tr>
              <tr>
                <th>Currency:</th>
                <td>${data.currency || 'N/A'}</td>
              </tr>
            </table>
            <div class="mt-3">
              ${data.latitude && data.longitude ? `
              <a href="https://www.google.com/maps?q=${data.latitude},${data.longitude}" target="_blank" class="btn btn-sm btn-primary">
                <i class="fa fa-map"></i> View on Google Maps
              </a>
              ` : ''}
            </div>
          </div>
        </div>
      `;
      $('#ipLookupContent').html(html);
    })
    .catch(error => {
      $('#ipLookupContent').html(`
        <div class="alert alert-danger">
          <i class="fa fa-times-circle"></i> Error fetching IP information: ${error.message}
        </div>
      `);
    });
}
</script>
@endsection

