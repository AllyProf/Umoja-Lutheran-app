@extends('dashboard.layouts.app')

@section('content')
<div class="app-title">
  <div>
    <h1><i class="fa fa-file-text-o"></i> System Logs</h1>
    <p>View system events and errors</p>
  </div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
    <li class="breadcrumb-item"><a href="{{ route('super_admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="#">System Logs</a></li>
  </ul>
</div>

<!-- Filters -->
<div class="row mb-3">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-body">
        <form method="GET" action="{{ route('super_admin.system-logs') }}" class="row">
          <div class="col-md-3">
            <div class="form-group">
              <label for="level">Log Level</label>
              <select name="level" id="level" class="form-control">
                <option value="">All Levels</option>
                @foreach($levels as $level)
                <option value="{{ $level }}" {{ request('level') == $level ? 'selected' : '' }}>
                  {{ ucfirst($level) }}
                </option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label for="channel">Channel</label>
              <select name="channel" id="channel" class="form-control">
                <option value="">All Channels</option>
                @foreach($channels as $channel)
                <option value="{{ $channel }}" {{ request('channel') == $channel ? 'selected' : '' }}>
                  {{ ucfirst($channel) }}
                </option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group">
              <label for="date_from">Date From</label>
              <input type="date" name="date_from" id="date_from" class="form-control" 
                     value="{{ request('date_from') }}">
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group">
              <label for="date_to">Date To</label>
              <input type="date" name="date_to" id="date_to" class="form-control" 
                     value="{{ request('date_to') }}">
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group">
              <label>&nbsp;</label>
              <button type="submit" class="btn btn-primary btn-block">
                <i class="fa fa-search"></i> Filter
              </button>
            </div>
          </div>
        </form>
        <div class="mt-2">
          <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#clearLogsModal">
            <i class="fa fa-trash"></i> Clear Old Logs
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- System Logs Table -->
<div class="row">
  <div class="col-md-12">
    <div class="tile">
      <div class="tile-title-w-btn">
        <h3 class="title"><i class="fa fa-file-text-o"></i> System Logs ({{ $logs->total() }} records)</h3>
        <div class="btn-group">
          <input type="text" id="logSearchInput" class="form-control" placeholder="Search logs..." style="width: 250px; margin-right: 10px;">
          <span class="badge badge-info" id="searchResultCount" style="display: none; padding: 8px 12px; font-size: 14px;"></span>
        </div>
      </div>
      <div class="tile-body">
        <div class="table-responsive">
          <table class="table table-hover table-bordered">
            <thead>
              <tr>
                <th>Time</th>
                <th>Level</th>
                <th>Channel</th>
                <th>Message</th>
                <th>User</th>
                <th>IP Address</th>
                <th>Context</th>
              </tr>
            </thead>
            <tbody id="logsTableBody">
              @forelse($logs as $log)
              @php
                // Build searchable text
                $searchText = strtolower(
                  $log->created_at->format('M d, Y H:i:s') . ' ' .
                  $log->level . ' ' .
                  $log->channel . ' ' .
                  $log->message . ' ' .
                  ($log->ip_address ?? '') . ' '
                );
                
                // Add user information
                if ($log->user_id) {
                  $user = \App\Models\Staff::find($log->user_id);
                  $userType = 'staff';
                  if (!$user) {
                    $user = \App\Models\Guest::find($log->user_id);
                    $userType = 'guest';
                  }
                  if ($user) {
                    $searchText .= strtolower($user->name . ' ' . $user->email . ' ' . $userType . ' ');
                  }
                  if ($log->context && isset($log->context['user_email'])) {
                    $searchText .= strtolower($log->context['user_email'] . ' ');
                  }
                  if ($log->context && isset($log->context['user_name'])) {
                    $searchText .= strtolower($log->context['user_name'] . ' ');
                  }
                } else {
                  $searchText .= 'system ';
                }
                
                // Add context data if available
                if ($log->context && is_array($log->context)) {
                  $searchText .= strtolower(json_encode($log->context) . ' ');
                }
              @endphp
              <tr class="log-row" data-search-text="{{ trim($searchText) }}">
                <td>{{ $log->created_at->format('M d, Y H:i:s') }}</td>
                <td>
                  @if($log->level == 'error' || $log->level == 'critical')
                    <span class="badge badge-danger">{{ strtoupper($log->level) }}</span>
                  @elseif($log->level == 'warning')
                    <span class="badge badge-warning">{{ strtoupper($log->level) }}</span>
                  @else
                    <span class="badge badge-info">{{ strtoupper($log->level) }}</span>
                  @endif
                </td>
                <td><code>{{ $log->channel }}</code></td>
                <td>{{ $log->message }}</td>
                <td>
                  @if($log->user_id)
                    @php
                      // Check both Staff and Guest tables (handle ID collisions)
                      $user = \App\Models\Staff::find($log->user_id);
                      $userType = 'staff';
                      if (!$user) {
                        $user = \App\Models\Guest::find($log->user_id);
                        $userType = 'guest';
                      }
                      // If context has user info, use that to identify correct user
                      if (!$user && $log->context && isset($log->context['user_email'])) {
                        $user = \App\Models\Staff::where('email', $log->context['user_email'])->first();
                        if (!$user) {
                          $user = \App\Models\Guest::where('email', $log->context['user_email'])->first();
                        }
                      }
                    @endphp
                    @if($user)
                      <strong>{{ $user->name }}</strong><br>
                      <small class="text-muted">{{ $user->email }}</small><br>
                      <small class="badge badge-secondary">{{ ucfirst($userType) }}</small>
                    @else
                      <span class="text-muted">User #{{ $log->user_id }}</span>
                      @if($log->context && isset($log->context['user_email']))
                        <br><small class="text-muted">({{ $log->context['user_email'] }})</small>
                      @endif
                    @endif
                  @else
                    <span class="text-muted">System</span>
                  @endif
                </td>
                <td><small>{{ $log->ip_address }}</small></td>
                <td>
                  @php
                    $hasContext = $log->context && (is_array($log->context) ? count($log->context) > 0 : !empty($log->context));
                    $isPasswordReset = str_contains(strtolower($log->message), 'password') && 
                                       (str_contains(strtolower($log->message), 'reset') || 
                                        str_contains(strtolower($log->message), 'generated'));
                    $isVerificationCode = str_contains(strtolower($log->message), 'verification code') || 
                                          ($log->context && isset($log->context['verification_code']));
                  @endphp
                  @if($hasContext || $isPasswordReset || $isVerificationCode)
                  <button type="button" class="btn btn-sm btn-info" 
                          data-toggle="modal" 
                          data-target="#contextModal{{ $log->id }}">
                    <i class="fa fa-eye"></i> View
                  </button>
                  @else
                  <span class="text-muted">-</span>
                  @endif
                </td>
              </tr>
              
              <!-- Context Modal -->
              @php
                $hasContext = $log->context && (is_array($log->context) ? count($log->context) > 0 : !empty($log->context));
                $isPasswordReset = str_contains(strtolower($log->message), 'password') && 
                                   (str_contains(strtolower($log->message), 'reset') || 
                                    str_contains(strtolower($log->message), 'generated'));
                $isVerificationCode = str_contains(strtolower($log->message), 'verification code') || 
                                      ($log->context && isset($log->context['verification_code']));
              @endphp
              @if($hasContext || $isPasswordReset || $isVerificationCode)
              <div class="modal fade" id="contextModal{{ $log->id }}" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-lg" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title">Log Context</h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>
                    <div class="modal-body">
                      @if($isVerificationCode && $log->context && isset($log->context['verification_code']))
                        <!-- Verification Code Log - Show Code Prominently -->
                        <div class="alert alert-success">
                          <h5><i class="fa fa-shield"></i> Verification Code Sent</h5>
                          <p class="mb-2"><strong>Verification Code:</strong></p>
                          <div class="bg-light p-3 mb-3 rounded">
                            <code style="font-size: 24px; font-weight: bold; color: #28a745; letter-spacing: 4px; font-family: 'Courier New', monospace;">{{ $log->context['verification_code'] }}</code>
                            <button type="button" class="btn btn-sm btn-secondary ml-2" onclick="copyPassword('{{ $log->context['verification_code'] }}')">
                              <i class="fa fa-copy"></i> Copy
                            </button>
                          </div>
                          @if(isset($log->context['user_name']))
                            <p><strong>User:</strong> {{ $log->context['user_name'] }}</p>
                          @endif
                          @if(isset($log->context['user_email']))
                            <p><strong>Email:</strong> {{ $log->context['user_email'] }}</p>
                          @endif
                          @if(isset($log->context['user_type']))
                            <p><strong>Type:</strong> {{ ucfirst($log->context['user_type']) }}</p>
                          @endif
                          @if(isset($log->context['expires_at']))
                            <p><strong>Expires At:</strong> {{ \Carbon\Carbon::parse($log->context['expires_at'])->format('M d, Y H:i:s') }}</p>
                          @endif
                          @if(isset($log->context['action']))
                            <p><strong>Action:</strong> {{ ucfirst(str_replace('_', ' ', $log->context['action'])) }}</p>
                          @endif
                        </div>
                        <hr>
                        <h6>Full Context:</h6>
                      @elseif($isPasswordReset)
                        @if($log->context && isset($log->context['new_password']))
                          <!-- Password Reset Log - Show Password Prominently -->
                          <div class="alert alert-warning">
                            <h5><i class="fa fa-key"></i> Password Reset Request</h5>
                            <p class="mb-2"><strong>New Password Generated:</strong></p>
                            <div class="bg-light p-3 mb-3 rounded">
                              <code style="font-size: 18px; font-weight: bold; color: #940000;">{{ $log->context['new_password'] }}</code>
                              <button type="button" class="btn btn-sm btn-secondary ml-2" onclick="copyPassword('{{ $log->context['new_password'] }}')">
                                <i class="fa fa-copy"></i> Copy
                              </button>
                            </div>
                            @if(isset($log->context['user_name']))
                              <p><strong>User:</strong> {{ $log->context['user_name'] }}</p>
                            @endif
                            @if(isset($log->context['user_email']))
                              <p><strong>Email:</strong> {{ $log->context['user_email'] }}</p>
                            @endif
                            @if(isset($log->context['user_type']))
                              <p><strong>Type:</strong> {{ ucfirst($log->context['user_type']) }}</p>
                            @endif
                          </div>
                          <hr>
                          <h6>Full Context:</h6>
                        @else
                          <!-- Password Reset Log but password not in context (old log entry) -->
                          <div class="alert alert-info">
                            <h5><i class="fa fa-key"></i> Password Reset Log</h5>
                            <p><strong>Note:</strong> This log entry was created before password tracking was enabled. The password is not available in the system logs.</p>
                            <p>For new password resets, the password will be displayed here.</p>
                          </div>
                          <hr>
                          @if($hasContext)
                            <h6>Context:</h6>
                          @endif
                        @endif
                      @endif
                      @if($hasContext)
                        <pre class="bg-light p-3">{{ json_encode($log->context, JSON_PRETTY_PRINT) }}</pre>
                      @elseif(!$isPasswordReset)
                        <p class="text-muted">No context available for this log entry.</p>
                      @endif
                      @if($log->context && isset($log->context['user_id']))
                        @php
                          // Try to identify user from context
                          $contextUserId = $log->context['user_id'];
                          $contextUserEmail = $log->context['user_email'] ?? null;
                          $contextUserType = $log->context['user_type'] ?? null;
                          
                          // If email is in context, use it to find user
                          if ($contextUserEmail) {
                            $contextUser = \App\Models\Staff::where('email', $contextUserEmail)->first();
                            if (!$contextUser) {
                              $contextUser = \App\Models\Guest::where('email', $contextUserEmail)->first();
                            }
                          } else {
                            // Fallback to ID lookup
                            $contextUser = \App\Models\Staff::find($contextUserId);
                            if (!$contextUser) {
                              $contextUser = \App\Models\Guest::find($contextUserId);
                            }
                          }
                        @endphp
                        @if($contextUser && !isset($log->context['new_password']))
                          <div class="mt-3 p-3 bg-info text-white rounded">
                            <strong>Identified User:</strong><br>
                            Name: {{ $contextUser->name }}<br>
                            Email: {{ $contextUser->email }}<br>
                            Type: {{ $contextUserType ?? (class_basename($contextUser) == 'Staff' ? 'Staff' : 'Guest') }}
                          </div>
                        @endif
                      @endif
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                  </div>
                </div>
              </div>
              @endif
              @empty
              <tr>
                <td colspan="7" class="text-center">No system logs found</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        
        <!-- Pagination -->
        @if($logs->hasPages())
        <div class="mt-3">
          <nav aria-label="Page navigation">
            <ul class="pagination pagination-sm justify-content-center">
              {{-- Previous Page Link --}}
              @if($logs->onFirstPage())
                <li class="page-item disabled">
                  <span class="page-link">
                    <i class="fa fa-angle-left"></i> Prev
                  </span>
                </li>
              @else
                <li class="page-item">
                  <a class="page-link" href="{{ $logs->previousPageUrl() }}" rel="prev">
                    <i class="fa fa-angle-left"></i> Prev
                  </a>
                </li>
              @endif

              {{-- Pagination Elements --}}
              @php
                $currentPage = $logs->currentPage();
                $lastPage = $logs->lastPage();
                $startPage = max(1, $currentPage - 2);
                $endPage = min($lastPage, $currentPage + 2);
              @endphp

              {{-- First page --}}
              @if($startPage > 1)
                <li class="page-item">
                  <a class="page-link" href="{{ $logs->url(1) }}">1</a>
                </li>
                @if($startPage > 2)
                  <li class="page-item disabled">
                    <span class="page-link">...</span>
                  </li>
                @endif
              @endif

              {{-- Page range around current page --}}
              @for($page = $startPage; $page <= $endPage; $page++)
                @if($page == $currentPage)
                  <li class="page-item active">
                    <span class="page-link">{{ $page }}</span>
                  </li>
                @else
                  <li class="page-item">
                    <a class="page-link" href="{{ $logs->url($page) }}">{{ $page }}</a>
                  </li>
                @endif
              @endfor

              {{-- Last page --}}
              @if($endPage < $lastPage)
                @if($endPage < $lastPage - 1)
                  <li class="page-item disabled">
                    <span class="page-link">...</span>
                  </li>
                @endif
                <li class="page-item">
                  <a class="page-link" href="{{ $logs->url($lastPage) }}">{{ $lastPage }}</a>
                </li>
              @endif

              {{-- Next Page Link --}}
              @if($logs->hasMorePages())
                <li class="page-item">
                  <a class="page-link" href="{{ $logs->nextPageUrl() }}" rel="next">
                    Next <i class="fa fa-angle-right"></i>
                  </a>
                </li>
              @else
                <li class="page-item disabled">
                  <span class="page-link">
                    Next <i class="fa fa-angle-right"></i>
                  </span>
                </li>
              @endif
            </ul>
          </nav>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- Clear Logs Modal -->
<div class="modal fade" id="clearLogsModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form action="{{ route('super_admin.logs.clear') }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Clear Old Logs</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="type">Log Type</label>
            <select name="type" id="type" class="form-control" required>
              <option value="activity">Activity Logs</option>
              <option value="system">System Logs</option>
              <option value="both">Both</option>
            </select>
          </div>
          <div class="form-group">
            <label for="days">Older Than (Days)</label>
            <input type="number" name="days" id="days" class="form-control" 
                   value="30" min="1" max="365" required>
            <small class="form-text text-danger">This action cannot be undone!</small>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger">Clear Logs</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
function copyPassword(password) {
    // Create a temporary textarea element
    const textarea = document.createElement('textarea');
    textarea.value = password;
    textarea.style.position = 'fixed';
    textarea.style.opacity = '0';
    document.body.appendChild(textarea);
    textarea.select();
    
    try {
        document.execCommand('copy');
        // Show success message
        const btn = event.target.closest('button');
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fa fa-check"></i> Copied!';
        btn.classList.remove('btn-secondary');
        btn.classList.add('btn-success');
        
        setTimeout(() => {
            btn.innerHTML = originalHtml;
            btn.classList.remove('btn-success');
            btn.classList.add('btn-secondary');
        }, 2000);
    } catch (err) {
        console.error('Failed to copy password:', err);
        alert('Failed to copy password. Please copy manually: ' + password);
    }
    
    document.body.removeChild(textarea);
}

// Client-side search functionality - using jQuery for better compatibility
$(document).ready(function() {
    var searchInput = $('#logSearchInput');
    var searchResultCount = $('#searchResultCount');
    
    if (searchInput.length === 0) {
        console.error('Search input not found');
        return;
    }
    
    function performSearch() {
        var searchTerm = searchInput.val().toLowerCase().trim();
        var logRows = $('#logsTableBody .log-row');
        var visibleCount = 0;
        
        logRows.each(function() {
            var row = $(this);
            var searchText = row.attr('data-search-text') || '';
            
            if (searchTerm === '' || searchText.indexOf(searchTerm) !== -1) {
                row.show();
                visibleCount++;
            } else {
                row.hide();
            }
        });
        
        // Update search result count
        if (searchTerm !== '') {
            searchResultCount.show();
            if (visibleCount === 0) {
                searchResultCount.removeClass('badge-info').addClass('badge-warning');
                searchResultCount.text('No results found');
            } else {
                searchResultCount.removeClass('badge-warning').addClass('badge-info');
                searchResultCount.text(visibleCount + ' result' + (visibleCount !== 1 ? 's' : ''));
            }
        } else {
            searchResultCount.hide();
        }
    }
    
    // Add event listeners
    searchInput.on('input keyup', performSearch);
    
    // Clear search on Escape key
    searchInput.on('keydown', function(e) {
        if (e.key === 'Escape' || e.keyCode === 27) {
            $(this).val('');
            performSearch();
            $(this).focus();
        }
    });
    
    console.log('Search functionality initialized');
});
</script>
@endsection

