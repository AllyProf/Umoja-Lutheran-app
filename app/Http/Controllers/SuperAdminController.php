<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Staff;
use App\Models\Guest;
use App\Models\Role;
use App\Models\Permission;
use App\Models\ActivityLog;
use App\Models\SystemLog;
use App\Models\Booking;
use App\Models\Room;
use App\Models\FailedLoginAttempt;
use App\Models\HotelSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SuperAdminController extends Controller
{
    /**
     * Display super admin dashboard
     */
    public function dashboard(Request $request)
    {
        $user = auth()->guard('staff')->user() ?? auth()->guard('guest')->user();
        $currentSessionId = $request->session()->getId();
        
        // System statistics
        $stats = [
            'total_users' => Staff::count() + Guest::count(),
            'super_admins' => Staff::where('role', 'super_admin')->count(),
            'managers' => Staff::where('role', 'manager')->count(),
            'reception' => Staff::where('role', 'reception')->count(),
            'guests' => Guest::count(),
            'total_roles' => Role::count(),
            'total_permissions' => Permission::count(),
            'total_bookings' => Booking::count(),
            'total_rooms' => Room::count(),
        ];
        
        // Recent activity logs with pagination
        $recentActivities = ActivityLog::orderBy('created_at', 'desc')
            ->paginate(3, ['*'], 'activities_page');
        
        // System logs summary
        $systemLogsSummary = [
            'error' => SystemLog::where('level', 'error')->whereDate('created_at', Carbon::today())->count(),
            'warning' => SystemLog::where('level', 'warning')->whereDate('created_at', Carbon::today())->count(),
            'info' => SystemLog::where('level', 'info')->whereDate('created_at', Carbon::today())->count(),
        ];
        
        // User activity by role
        $userActivityByRole = collect();
        try {
            // Try to get user_type from column if it exists
            $todayLogs = ActivityLog::whereDate('created_at', Carbon::today())->get();
            
            foreach ($todayLogs as $log) {
                $userType = null;
                
                // First try to get from user_type column if it exists and has value
                if (isset($log->user_type) && $log->user_type) {
                    $userType = $log->user_type;
                } elseif ($log->user_id) {
                    // Fallback: derive from user_id by checking Staff and Guest tables
                    $staff = Staff::find($log->user_id);
                    $userType = $staff ? $staff->role : 'guest';
                } else {
                    $userType = 'system';
                }
                
                $currentCount = $userActivityByRole->get($userType, 0);
                $userActivityByRole->put($userType, $currentCount + 1);
            }
        } catch (\Exception $e) {
            // If query fails, return empty collection
            Log::warning('Failed to get user activity by role: ' . $e->getMessage());
        }
        
        // Get currently logged in users from sessions table with pagination
        // First get all active sessions for status checking
        $allActiveSessions = DB::table('sessions')
            ->where('last_activity', '>', now()->subMinutes(config('session.lifetime', 120))->timestamp)
            ->orderBy('last_activity', 'desc')
            ->get();
        
        // Get all active session IDs for matching
        $activeSessionIds = $allActiveSessions->pluck('id')->toArray();
        
        // Get all logged in users for status checking
        // Use email as key since it's unique across Staff and Guest
        $allLoggedInUsers = [];
        $activeUserEmails = []; // Map of active user emails for quick lookup
        
        // Method 1: Check sessions table by user_id (if stored)
        foreach ($allActiveSessions as $session) {
            if ($session->user_id) {
                // Try to find in Staff first, then Guest
                $sessionUser = Staff::find($session->user_id) ?? Guest::find($session->user_id);
                if ($sessionUser) {
                    $allLoggedInUsers[] = [
                        'user' => $sessionUser,
                        'ip_address' => $session->ip_address,
                        'user_agent' => $session->user_agent,
                        'last_activity' => Carbon::createFromTimestamp($session->last_activity),
                        'session_id' => $session->id,
                    ];
                    // Add email to active users map for quick status checking
                    $activeUserEmails[$sessionUser->email] = true;
                }
            }
        }
        
        // Method 2: Check Staff and Guest tables for users with active last_session_id
        // This is more reliable since we store last_session_id in user tables
        if (!empty($activeSessionIds)) {
            $staffWithActiveSessions = Staff::whereNotNull('last_session_id')
                ->whereNotNull('session_token')
                ->whereIn('last_session_id', $activeSessionIds)
                ->get();
            
            foreach ($staffWithActiveSessions as $staff) {
                if (!isset($activeUserEmails[$staff->email])) {
                    // Find the session for this user to get IP and user agent
                    $userSession = $allActiveSessions->firstWhere('id', $staff->last_session_id);
                    $allLoggedInUsers[] = [
                        'user' => $staff,
                        'ip_address' => $userSession->ip_address ?? null,
                        'user_agent' => $userSession->user_agent ?? null,
                        'last_activity' => $userSession ? Carbon::createFromTimestamp($userSession->last_activity) : now(),
                        'session_id' => $staff->last_session_id,
                    ];
                    $activeUserEmails[$staff->email] = true;
                }
            }
            
            $guestsWithActiveSessions = Guest::whereNotNull('last_session_id')
                ->whereNotNull('session_token')
                ->whereIn('last_session_id', $activeSessionIds)
                ->get();
            
            foreach ($guestsWithActiveSessions as $guest) {
                if (!isset($activeUserEmails[$guest->email])) {
                    // Find the session for this user to get IP and user agent
                    $userSession = $allActiveSessions->firstWhere('id', $guest->last_session_id);
                    $allLoggedInUsers[] = [
                        'user' => $guest,
                        'ip_address' => $userSession->ip_address ?? null,
                        'user_agent' => $userSession->user_agent ?? null,
                        'last_activity' => $userSession ? Carbon::createFromTimestamp($userSession->last_activity) : now(),
                        'session_id' => $guest->last_session_id,
                    ];
                    $activeUserEmails[$guest->email] = true;
                }
            }
        }
        
        // Method 3: Fallback - check if current user has session_token and last_session_id
        // This ensures the current logged-in user always shows as active
        if ($user && $user->session_token && $user->last_session_id) {
            if (!isset($activeUserEmails[$user->email])) {
                // Find the session for this user
                $userSession = $allActiveSessions->firstWhere('id', $user->last_session_id);
                $allLoggedInUsers[] = [
                    'user' => $user,
                    'ip_address' => $userSession->ip_address ?? $request->ip(),
                    'user_agent' => $userSession->user_agent ?? $request->userAgent(),
                    'last_activity' => $userSession ? Carbon::createFromTimestamp($userSession->last_activity) : now(),
                    'session_id' => $user->last_session_id,
                ];
                $activeUserEmails[$user->email] = true;
            }
        }
        
        // Now get paginated version for display using the comprehensive $allLoggedInUsers
        // Use the same data we collected above, but paginate it
        $perPage = 3;
        $currentPage = $request->get('active_users_page', 1);
        $total = count($allLoggedInUsers);
        
        // Sort by last_activity (most recent first)
        usort($allLoggedInUsers, function($a, $b) {
            return $b['last_activity']->timestamp <=> $a['last_activity']->timestamp;
        });
        
        // Get the items for current page
        $items = array_slice($allLoggedInUsers, ($currentPage - 1) * $perPage, $perPage);
        
        // Create paginated collection
        $loggedInUsersPaginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'pageName' => 'active_users_page']
        );
        
        // Get recent login/logout activities with pagination
        $loginLogoutActivities = ActivityLog::whereIn('action', ['logged_in', 'logged_out'])
            ->orderBy('created_at', 'desc')
            ->paginate(3, ['*'], 'login_activities_page');
        
        // Get staff with their last login time
        $staffWithLastLogin = Staff::select([
                'staffs.*',
                DB::raw('(SELECT MAX(created_at) FROM activity_logs WHERE activity_logs.user_id = staffs.id AND activity_logs.action = "logged_in") as last_login'),
                DB::raw('(SELECT ip_address FROM activity_logs WHERE activity_logs.user_id = staffs.id AND activity_logs.action = "logged_in" ORDER BY created_at DESC LIMIT 1) as last_login_ip')
            ])
            ->orderByRaw('ISNULL(last_login), last_login DESC');
        
        // Get guests with their last login time
        $guestsWithLastLogin = Guest::select([
                'guests.*',
                DB::raw('(SELECT MAX(created_at) FROM activity_logs WHERE activity_logs.user_id = guests.id AND activity_logs.action = "logged_in") as last_login'),
                DB::raw('(SELECT ip_address FROM activity_logs WHERE activity_logs.user_id = guests.id AND activity_logs.action = "logged_in" ORDER BY created_at DESC LIMIT 1) as last_login_ip')
            ])
            ->orderByRaw('ISNULL(last_login), last_login DESC');
        
        // Combine staff and guests with pagination
        // We need to get all, combine, sort, then paginate manually
        $allUsers = $staffWithLastLogin->get()->concat($guestsWithLastLogin->get())->sortByDesc('last_login')->values();
        $perPage = 3;
        $currentPage = $request->get('users_page', 1);
        $items = $allUsers->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $usersWithLastLogin = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $allUsers->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'pageName' => 'users_page']
        );
        
        // Get technical issues (errors and critical system logs) with pagination
        $technicalIssues = SystemLog::whereIn('level', ['error', 'critical'])
            ->orderBy('created_at', 'desc')
            ->paginate(3, ['*'], 'technical_issues_page');
        
        // Get login statistics
        $loginStats = [
            'today' => ActivityLog::where('action', 'logged_in')
                ->whereDate('created_at', Carbon::today())
                ->count(),
            'this_week' => ActivityLog::where('action', 'logged_in')
                ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                ->count(),
            'this_month' => ActivityLog::where('action', 'logged_in')
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->count(),
        ];
        
        return view('dashboard.super-admin.dashboard', [
            'role' => 'super_admin',
            'userName' => $user->name ?? 'Super Admin',
            'userRole' => 'Super Administrator',
            'stats' => $stats,
            'recentActivities' => $recentActivities,
            'systemLogsSummary' => $systemLogsSummary,
            'userActivityByRole' => $userActivityByRole,
            'loggedInUsers' => $loggedInUsersPaginated,
            'allLoggedInUsers' => $allLoggedInUsers, // Full collection for status checking
            'activeUserEmails' => $activeUserEmails, // Map of active user emails for quick lookup
            'loginLogoutActivities' => $loginLogoutActivities,
            'usersWithLastLogin' => $usersWithLastLogin,
            'technicalIssues' => $technicalIssues,
            'loginStats' => $loginStats,
        ]);
    }
    
    /**
     * Display all users management (Staff and Guests)
     */
    public function users(Request $request)
    {
        // Determine active tab (default to 'employees')
        $activeTab = $request->get('tab', 'employees');
        
        // Get staff (employees)
        $staffQuery = Staff::query();
        
        // Get guests
        $guestQuery = Guest::query();
        
        // Filter by role (for staff only)
        if ($request->has('role') && $request->role && $activeTab === 'employees') {
            $role = $request->role;
            if ($role === 'super_admin') {
                // Handle both 'super_admin' and 'Super Admin' formats
                $staffQuery->where(function($q) {
                    $q->where('role', 'super_admin')
                      ->orWhere('role', 'Super Admin')
                      ->orWhereRaw('LOWER(REPLACE(role, " ", "_")) = ?', ['super_admin']);
                });
            } elseif ($role === 'reception') {
                // Handle case-insensitive matching for reception
                $staffQuery->where(function($q) {
                    $q->where('role', 'reception')
                      ->orWhere('role', 'Reception')
                      ->orWhereRaw('LOWER(TRIM(role)) = ?', ['reception'])
                      ->orWhereRaw('LOWER(role) LIKE ?', ['%reception%']);
                });
            } elseif ($role === 'manager' || $role === 'waiter' || $role === 'housekeeper' || $role === 'bar_keeper' || $role === 'head_chef') {
                $staffQuery->where('role', $role);
            }
        }
        
        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $staffQuery->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
            $guestQuery->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        // Get paginated results based on active tab
        $perPage = 20;
        $page = $request->get('page', 1);
        
        if ($activeTab === 'guests') {
            // Show only guests
            $users = $guestQuery->orderBy('created_at', 'desc')->paginate($perPage);
            // Count for guests tab (matching the filtered query)
            $totalGuests = $guestQuery->count();
        } else {
            // Show only employees (staff)
            $users = $staffQuery->orderBy('created_at', 'desc')->paginate($perPage);
            // Count for employees tab (matching the filtered query)
            $totalEmployees = $staffQuery->count();
        }
        
        // Append query parameters to pagination links
        $users->appends($request->except('page'));
        
        // Get role counts (for statistics cards - these show all records)
        $roleCounts = [
            'super_admin' => Staff::where('role', 'super_admin')->orWhere('role', 'like', '%Super Admin%')->count(),
            'manager' => Staff::where('role', 'manager')->count(),
            'reception' => Staff::where('role', 'reception')->count(),
            'waiter' => Staff::where('role', 'waiter')->count(),
            'housekeeper' => Staff::where('role', 'housekeeper')->count(),
            'guest' => Guest::count(),
            'total_employees' => isset($totalEmployees) ? $totalEmployees : Staff::count(),
            'total_guests' => isset($totalGuests) ? $totalGuests : Guest::count(),
        ];
        
        return view('dashboard.super-admin.users', [
            'role' => 'super_admin',
            'userName' => (auth()->guard('staff')->user() ?? auth()->guard('guest')->user())->name ?? 'Super Admin',
            'userRole' => 'Super Administrator',
            'users' => $users,
            'roleCounts' => $roleCounts,
            'filters' => $request->only(['role', 'search', 'tab']),
            'activeTab' => $activeTab,
        ]);
    }
    
    /**
     * Show create user form
     */
    public function createUser()
    {
        $roles = Role::where('is_system', false)->orWhereIn('name', ['manager', 'reception', 'guest', 'waiter'])->get();
        
        return view('dashboard.super-admin.user-form', [
            'role' => 'super_admin',
            'userName' => (auth()->guard('staff')->user() ?? auth()->guard('guest')->user())->name ?? 'Super Admin',
            'userRole' => 'Super Administrator',
            'user' => null,
            'roles' => $roles,
        ]);
    }
    
    /**
     * Store new user
     */
    public function storeUser(Request $request)
    {
        // Validation rules - password is optional for staff (will be auto-generated)
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'role' => 'required|string|in:super_admin,manager,reception,guest,bar_keeper,head_chef,housekeeper,waiter',
            'is_active' => 'boolean',
        ];
        
        // For staff accounts, password is not required (will use first name)
        // For guest accounts, we'll generate a default password too
        // Password is completely optional - if provided, it will be used; otherwise auto-generated
        if ($request->has('password') && $request->password) {
            $rules['password'] = 'string|min:8|confirmed';
        }
        
        $validated = $request->validate($rules);
        
        // Determine which table to check for unique email
        $emailExists = Staff::where('email', $validated['email'])->exists() || Guest::where('email', $validated['email'])->exists();
        
        if ($emailExists) {
            return back()->withErrors(['email' => 'The email has already been taken.'])->withInput();
        }
        
        // Extract first name and use it as default password for all accounts
        $nameParts = explode(' ', trim($validated['name']));
        $firstName = !empty($nameParts[0]) ? $nameParts[0] : $validated['name'];
        $defaultPassword = strtoupper($firstName);
        
        // Use provided password if given, otherwise use first name
        $password = $request->has('password') && $request->password 
            ? ($validated['password'] ?? $defaultPassword)
            : $defaultPassword;
        
        // Create Staff or Guest based on role
        if (in_array($validated['role'], ['super_admin', 'manager', 'reception', 'bar_keeper', 'head_chef', 'housekeeper', 'waiter'])) {
            $user = Staff::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($password),
                'role' => $validated['role'],
                'is_active' => $request->has('is_active') ? $validated['is_active'] : true,
            ]);
            
            // Send welcome email to staff with credentials
            try {
                Mail::to($user->email)->send(new \App\Mail\StaffWelcomeMail($user, $defaultPassword, $validated['role']));
                Log::info('Staff welcome email sent', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'role' => $validated['role'],
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send staff welcome email', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'error' => $e->getMessage(),
                ]);
                // Don't fail the user creation if email fails
            }
        } else {
            $user = Guest::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($password),
                'is_active' => $request->has('is_active') ? $validated['is_active'] : true,
            ]);
            
            // Send welcome email to guest with credentials
            try {
                Mail::to($user->email)->send(new \App\Mail\WelcomeMail($user, $defaultPassword));
                Log::info('Guest welcome email sent', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send guest welcome email', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'error' => $e->getMessage(),
                ]);
                // Don't fail the user creation if email fails
            }
        }
        
        // Log activity
        $role = $validated['role'];
        ActivityLog::log('created', $user, "Created user: {$user->name} ({$user->email}) with role: {$role}");
        SystemLog::log('info', "User created: {$user->name} ({$user->email})", 'user_management', [
            'user_id' => $user->id, 
            'role' => $role,
            'email_sent' => in_array($role, ['super_admin', 'manager', 'reception', 'bar_keeper', 'head_chef', 'housekeeper', 'waiter']),
        ]);
        
        return redirect()->route('super_admin.users')->with('success', 'User created successfully. Welcome email sent with credentials.');
    }
    
    /**
     * Show edit user form
     */
    public function editUser(Request $request, $id)
    {
        // Try to find as Staff first
        $user = Staff::find($id);
        $userType = 'staff';
        
        // If not found, try Guest
        if (!$user) {
            $user = Guest::find($id);
            $userType = 'guest';
        }
        
        if (!$user) {
            return redirect()->route('super_admin.users')->with('error', 'User not found.');
        }
        
        // Prevent editing super admin unless it's yourself
        if ($userType === 'staff' && $user->isSuperAdmin() && $user->id !== auth()->guard('staff')->id()) {
            return redirect()->route('super_admin.users')->with('error', 'Cannot edit other super admin accounts.');
        }
        
        $roles = Role::where('is_system', false)->orWhereIn('name', ['manager', 'reception', 'guest'])->get();
        
        return view('dashboard.super-admin.user-form', [
            'role' => 'super_admin',
            'userName' => auth()->guard('staff')->user()->name ?? 'Super Admin',
            'userRole' => 'Super Administrator',
            'user' => $user,
            'userType' => $userType,
            'roles' => $roles,
        ]);
    }
    
    /**
     * Update user
     */
    public function updateUser(Request $request, $id)
    {
        // Try to find as Staff first
        $user = Staff::find($id);
        $userType = 'staff';
        
        // If not found, try Guest
        if (!$user) {
            $user = Guest::find($id);
            $userType = 'guest';
        }
        
        if (!$user) {
            return redirect()->route('super_admin.users')->with('error', 'User not found.');
        }
        
        // Prevent editing super admin unless it's yourself
        if ($userType === 'staff' && $user->isSuperAdmin() && $user->id !== auth()->guard('staff')->id()) {
            return redirect()->route('super_admin.users')->with('error', 'Cannot edit other super admin accounts.');
        }
        
        // Build validation rules
        $rules = [
            'name' => 'required|string|max:255',
            'password' => 'nullable|string|min:8',
            'is_active' => 'boolean',
        ];
        
        // Only require password confirmation if password is provided
        if ($request->filled('password')) {
            $rules['password'] = 'required|string|min:8|confirmed';
        }
        
        // Email uniqueness check
        if ($userType === 'staff') {
            $rules['email'] = 'required|email|unique:staffs,email,' . $user->id . '|unique:guests,email';
            $rules['role'] = 'required|string|in:super_admin,manager,reception,bar_keeper,head_chef,housekeeper,waiter';
        } else {
            $rules['email'] = 'required|email|unique:guests,email,' . $user->id . '|unique:staffs,email';
        }
        
        $validated = $request->validate($rules);
        
        $oldValues = $user->only(['name', 'email', 'is_active']);
        if ($userType === 'staff') {
            $oldValues['role'] = $user->role;
        }
        
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        if ($userType === 'staff') {
            $user->role = $validated['role'];
        }
        $user->is_active = $request->has('is_active') ? $validated['is_active'] : false;
        
        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }
        
        $user->save();
        
        // Log activity
        $newValues = $user->only(['name', 'email', 'is_active']);
        if ($userType === 'staff') {
            $newValues['role'] = $user->role;
        }
        ActivityLog::log('updated', $user, "Updated user: {$user->name} ({$user->email})", $oldValues, $newValues);
        SystemLog::log('info', "User updated: {$user->name} ({$user->email})", 'user_management', ['user_id' => $user->id]);
        
        return redirect()->route('super_admin.users')->with('success', 'User updated successfully.');
    }
    
    /**
     * Auto-generate and reset user password
     */
    public function resetPassword(Request $request, $id)
    {
        // CRITICAL FIX: Check BOTH tables and determine which one has the user
        // This handles ID collisions between Staff and Guest tables
        $staffUser = Staff::find($id);
        $guestUser = Guest::find($id);
        
        // Determine which user exists (prioritize the one that was actually requested)
        // If both exist, we need to check the email or use a different method
        if ($staffUser && $guestUser) {
            // ID collision! Check if email is provided in request to determine which one
            $requestedEmail = $request->input('email') ?? $request->get('email');
            
            if ($requestedEmail) {
                if ($staffUser->email === $requestedEmail) {
                    $user = $staffUser;
                    $userType = 'staff';
                } elseif ($guestUser->email === $requestedEmail) {
                    $user = $guestUser;
                    $userType = 'guest';
                } else {
                    // Email doesn't match either, log error
                    \Log::error('ID collision detected but email mismatch', [
                        'id' => $id,
                        'requested_email' => $requestedEmail,
                        'staff_email' => $staffUser->email,
                        'guest_email' => $guestUser->email,
                    ]);
                    // Default to guest if email provided doesn't match staff
                    $user = $guestUser;
                    $userType = 'guest';
                }
            } else {
                // No email provided, check which table the user was likely from
                // Try to get from users list context - but for now, default to guest
                \Log::warning('ID collision detected without email context', [
                    'id' => $id,
                    'staff_email' => $staffUser->email,
                    'guest_email' => $guestUser->email,
                ]);
                // Default to guest (less privileged)
                $user = $guestUser;
                $userType = 'guest';
            }
        } elseif ($staffUser) {
            $user = $staffUser;
            $userType = 'staff';
        } elseif ($guestUser) {
            $user = $guestUser;
            $userType = 'guest';
        } else {
            if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'User not found.'], 404);
            }
            return redirect()->route('super_admin.users')->with('error', 'User not found.');
        }
        
        \Log::info('User identified for password reset', [
            'id' => $id,
            'user_type' => $userType,
            'user_email' => $user->email,
            'user_name' => $user->name,
            'id_collision' => ($staffUser && $guestUser),
        ]);
        
        // Auto-generate a secure random password (5 characters)
        $newPassword = \Illuminate\Support\Str::random(5);
        
        // Log the generated password before saving
        \Log::info('Password reset initiated', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_type' => get_class($user),
            'generated_password' => $newPassword,
        ]);
        
        // FIXED APPROACH: Use direct DB update to bypass Eloquent's 'hashed' cast
        // The 'hashed' cast in Staff/Guest models would double-hash if we use Eloquent
        // Hash the password manually and save directly to database
        $hashedPassword = \Illuminate\Support\Facades\Hash::make($newPassword);
        $tableName = $userType === 'staff' ? 'staffs' : 'guests';
        
        // Update password directly in database (bypasses Eloquent casts)
        $updated = \DB::table($tableName)
            ->where('id', $user->id)
            ->update(['password' => $hashedPassword]);
        
        if ($updated === 0) {
            \Log::error('Password update failed - no rows affected', [
                'user_id' => $user->id,
                'table' => $tableName,
            ]);
            
            if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Failed to update password.'], 500);
            }
            return redirect()->route('super_admin.users')->with('error', 'Failed to update password.');
        }
        
        // CRITICAL: Verify the password was saved correctly using raw DB value (most reliable)
        // We use raw DB value because Eloquent's 'hashed' cast might interfere when reading
        $rawPassword = \DB::table($tableName)->where('id', $id)->value('password');
        $passwordVerified = \Illuminate\Support\Facades\Hash::check($newPassword, $rawPassword);
        
        \Log::info('Password reset completed', [
            'user_id' => $id,
            'user_email' => $user->email,
            'password_verified' => $passwordVerified,
            'generated_password' => $newPassword,
        ]);
        
        if (!$passwordVerified) {
            \Log::error('CRITICAL: Password verification FAILED after reset!', [
                'user_id' => $id,
                'generated_password' => $newPassword,
                'raw_db_hash' => $rawPassword ? substr($rawPassword, 0, 60) : 'NULL',
            ]);
            
            if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Password was generated but verification failed. Please try again.'], 500);
            }
            return redirect()->route('super_admin.users')->with('error', 'Password was generated but verification failed. Please try again.');
        }
        
        // CRITICAL: Store the ORIGINAL user data BEFORE clearing cache
        // This ensures we log the correct user whose password was reset
        $originalUserData = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'type' => $userType,
        ];
        
        \Log::info('User data for logging', [
            'original_user_id' => $originalUserData['id'],
            'original_user_name' => $originalUserData['name'],
            'original_user_email' => $originalUserData['email'],
            'original_user_type' => $originalUserData['type'],
        ]);
        
        // Clear model cache to ensure fresh data
        if ($userType === 'staff') {
            Staff::clearBootedModels();
        } else {
            Guest::clearBootedModels();
        }
        
        // Get fresh instance for verification (but we'll use original data for logging)
        if ($userType === 'staff') {
            $freshUser = Staff::find($id);
        } else {
            $freshUser = Guest::find($id);
        }
        
        // Verify fresh user matches original
        if ($freshUser && ($freshUser->id !== $originalUserData['id'] || $freshUser->email !== $originalUserData['email'])) {
            \Log::error('User mismatch after cache clear!', [
                'original_id' => $originalUserData['id'],
                'original_email' => $originalUserData['email'],
                'fresh_id' => $freshUser->id,
                'fresh_email' => $freshUser->email,
            ]);
        }
        
        // Use original user data for logging to ensure accuracy
        $userForLogging = $freshUser ?? $user;
        
        if (!$userForLogging) {
            \Log::error('Failed to retrieve user after password update', [
                'user_id' => $id,
                'user_type' => $userType,
            ]);
            
            if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Failed to retrieve user after password update.'], 500);
            }
            return redirect()->route('super_admin.users')->with('error', 'Failed to retrieve user after password update.');
        }
        
        // Log activity with the CORRECT user (using original data to ensure accuracy)
        $authUser = auth()->guard('staff')->user();
        // Store old password hash (masked) and new password info in details
        $oldValues = ['password' => '***hidden***'];
        $newValues = ['password' => '***auto-generated***', 'password_length' => strlen($newPassword)];
        
        // Use original user data for description to ensure correct user is logged
        $logDescription = "Auto-generated and reset password for user: {$originalUserData['name']} ({$originalUserData['email']})";
        
        \Log::info('Creating activity log', [
            'user_id' => $userForLogging->id,
            'user_name' => $userForLogging->name,
            'user_email' => $userForLogging->email,
            'log_description' => $logDescription,
        ]);
        
        ActivityLog::log('reset_password', $userForLogging, $logDescription, $oldValues, $newValues);
        
        SystemLog::log('warning', "Password auto-generated and reset for user: {$originalUserData['name']} ({$originalUserData['email']})", 'security', [
            'user_id' => $originalUserData['id'],
            'user_email' => $originalUserData['email'], // Include email to help identify user in case of ID collision
            'user_type' => $originalUserData['type'], // Include user type (staff/guest)
            'user_name' => $originalUserData['name'],
            'new_password' => $newPassword, // Include the new password so admin can view it
            'action_by' => $authUser ? $authUser->id : null,
            'action_by_email' => $authUser ? $authUser->email : null,
            'action' => 'password_reset_by_admin',
        ]);
        
        // If AJAX request, return JSON
        if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Password auto-generated and reset successfully for {$originalUserData['name']}.",
                'password' => $newPassword,
                'user_email' => $originalUserData['email'],
                'user_name' => $originalUserData['name'],
                'verified' => true,
            ]);
        }
        
        // Return the generated password so it can be displayed to the admin
        return redirect()->back()->with('success', "Password auto-generated and reset successfully for {$user->name}.")->with('generated_password', $newPassword)->with('user_email', $user->email);
    }
    
    /**
     * Delete user
     */
    public function deleteUser($id)
    {
        // Try to find as Staff first
        $user = Staff::find($id);
        $userType = 'staff';
        
        // If not found, try Guest
        if (!$user) {
            $user = Guest::find($id);
            $userType = 'guest';
        }
        
        if (!$user) {
            return redirect()->route('super_admin.users')->with('error', 'User not found.');
        }
        
        // Prevent deleting super admin
        if ($userType === 'staff' && $user->isSuperAdmin()) {
            return redirect()->route('super_admin.users')->with('error', 'Cannot delete super admin accounts.');
        }
        
        // Prevent deleting yourself
        $authUser = auth()->guard('staff')->user();
        if ($authUser && $user->id === $authUser->id) {
            return redirect()->route('super_admin.users')->with('error', 'Cannot delete your own account.');
        }
        
        $userName = $user->name;
        $userEmail = $user->email;
        
        // Log before deletion
        ActivityLog::log('deleted', null, "Deleted user: {$userName} ({$userEmail})");
        SystemLog::log('warning', "User deleted: {$userName} ({$userEmail})", 'user_management', ['user_id' => $user->id]);
        
        $user->delete();
        
        return redirect()->route('super_admin.users')->with('success', 'User deleted successfully.');
    }
    
    /**
     * Display roles management
     */
    public function roles()
    {
        // Get all roles with optimized queries
        $roles = Role::orderBy('name')->get();
        
        // Pre-calculate permission counts and user counts for all roles in one query
        $roleIds = $roles->pluck('id');
        
        // Get all permission counts in one query
        $permissionCounts = \DB::table('role_permission')
            ->whereIn('role_id', $roleIds)
            ->select('role_id', \DB::raw('COUNT(*) as count'))
            ->groupBy('role_id')
            ->pluck('count', 'role_id')
            ->toArray();
        
        // Get all staff role counts in optimized queries
        $staffRoleCounts = [];
        $allStaffRoles = \App\Models\Staff::select('role')
            ->whereNotNull('role')
            ->get()
            ->groupBy(function($staff) {
                return strtolower(str_replace([' ', '_'], '', trim($staff->role)));
            });
        
        // Get guest count (only for guest role)
        $guestCount = \App\Models\Guest::count();
        
        // Attach counts to roles
        foreach ($roles as $role) {
            $role->permission_count = $permissionCounts[$role->id] ?? 0;
            
            // Calculate user count efficiently
            $roleNameNormalized = strtolower(str_replace([' ', '_'], '', trim($role->name)));
            $staffCount = 0;
            
            // Count staff with matching role
            if ($allStaffRoles->has($roleNameNormalized)) {
                $staffCount = $allStaffRoles->get($roleNameNormalized)->count();
            } else {
                // Fallback: check exact match
                $staffCount = \App\Models\Staff::where('role', $role->name)->count();
            }
            
            // Add guest count if this is the guest role
            if ($roleNameNormalized === 'guest') {
                $role->user_count = $staffCount + $guestCount;
                $role->staff_count = $staffCount;
                $role->guest_count = $guestCount;
            } else {
                $role->user_count = $staffCount;
                $role->staff_count = $staffCount;
                $role->guest_count = 0;
            }
        }
        
        // Pre-load all permissions grouped by group for the modals (optimize modal queries)
        // Filter out blog permissions as they are no longer used
        $allPermissions = \App\Models\Permission::where('name', 'not like', '%blog%')
            ->where(function($query) {
                $query->where('group', 'not like', '%blog%')
                      ->where('group', '!=', 'Blog');
            })
            ->orderBy('group')
            ->orderBy('name')
            ->get()
            ->groupBy('group');
        
        // Pre-load role-permission mappings for all roles
        $allRolePermissions = \DB::table('role_permission')
            ->whereIn('role_id', $roleIds)
            ->get()
            ->groupBy('role_id')
            ->map(function($permissions) {
                return $permissions->pluck('permission_id')->map('intval')->toArray();
            })
            ->toArray();
        
        return view('dashboard.super-admin.roles', [
            'role' => 'super_admin',
            'userName' => (auth()->guard('staff')->user() ?? auth()->guard('guest')->user())->name ?? 'Super Admin',
            'userRole' => 'Super Administrator',
            'roles' => $roles,
            'allPermissions' => $allPermissions,
            'allRolePermissions' => $allRolePermissions,
        ]);
    }
    
    /**
     * Store new role
     */
    public function storeRole(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        
        $role = Role::create($validated);
        
        ActivityLog::log('created', $role, "Created role: {$role->name}");
        SystemLog::log('info', "Role created: {$role->name}", 'role_management');
        
        return redirect()->route('super_admin.roles')->with('success', 'Role created successfully.');
    }
    
    /**
     * Update role
     */
    public function updateRole(Request $request, Role $role)
    {
        // Prevent editing system roles
        if ($role->is_system) {
            return redirect()->route('super_admin.roles')->with('error', 'Cannot edit system roles.');
        }
        
        $validated = $request->validate([
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        
        $role->update($validated);
        
        ActivityLog::log('updated', $role, "Updated role: {$role->name}");
        
        return redirect()->route('super_admin.roles')->with('success', 'Role updated successfully.');
    }
    
    /**
     * Delete role
     */
    public function deleteRole(Role $role)
    {
        // Prevent deleting system roles
        if ($role->is_system) {
            return redirect()->route('super_admin.roles')->with('error', 'Cannot delete system roles.');
        }
        
        // Check if role has users (staff or guests)
        $staffCount = Staff::where('role', $role->name)->count();
        $userCount = User::where('role', $role->name)->count();
        
        if ($staffCount > 0 || $userCount > 0) {
            return redirect()->route('super_admin.roles')->with('error', "Cannot delete role with assigned users. ({$staffCount} staff, {$userCount} guests)");
        }
        
        $roleName = $role->name;
        $role->delete();
        
        ActivityLog::log('deleted', null, "Deleted role: {$roleName}");
        SystemLog::log('warning', "Role deleted: {$roleName}", 'role_management');
        
        return redirect()->route('super_admin.roles')->with('success', 'Role deleted successfully.');
    }
    
    /**
     * Display permissions management
     */
    public function permissions()
    {
        // Filter out blog permissions as they are no longer used
        $permissions = Permission::with('roles')
            ->where('name', 'not like', '%blog%')
            ->where(function($query) {
                $query->where('group', 'not like', '%blog%')
                      ->where('group', '!=', 'Blog');
            })
            ->orderBy('group')
            ->orderBy('name')
            ->get();
        // Filter out blog group
        $groups = Permission::where('name', 'not like', '%blog%')
            ->where(function($query) {
                $query->where('group', 'not like', '%blog%')
                      ->where('group', '!=', 'Blog');
            })
            ->distinct()
            ->pluck('group')
            ->filter();
        
        return view('dashboard.super-admin.permissions', [
            'role' => 'super_admin',
            'userName' => (auth()->guard('staff')->user() ?? auth()->guard('guest')->user())->name ?? 'Super Admin',
            'userRole' => 'Super Administrator',
            'permissions' => $permissions,
            'groups' => $groups,
        ]);
    }
    
    /**
     * Store new permission
     */
    public function storePermission(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'group' => 'nullable|string|max:255',
        ]);
        
        $permission = Permission::create($validated);
        
        ActivityLog::log('created', $permission, "Created permission: {$permission->name}");
        
        return redirect()->route('super_admin.permissions')->with('success', 'Permission created successfully.');
    }
    
    /**
     * Update permission
     */
    public function updatePermission(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'group' => 'nullable|string|max:255',
        ]);
        
        $permission->update($validated);
        
        ActivityLog::log('updated', $permission, "Updated permission: {$permission->name}");
        
        return redirect()->route('super_admin.permissions')->with('success', 'Permission updated successfully.');
    }
    
    /**
     * Assign permissions to role
     */
    public function assignPermissions(Request $request, Role $role)
    {
        $validated = $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);
        
        // Sync permissions (empty array means no permissions)
        $permissions = $validated['permissions'] ?? [];
        
        // Convert string IDs to integers if needed
        $permissionIds = array_map('intval', $permissions);
        
        // Log before sync for debugging
        Log::info("Assigning permissions to role ID {$role->id} ({$role->name}): " . json_encode($permissionIds));
        
        // Sync permissions - this will replace all existing permissions
        $synced = $role->permissions()->sync($permissionIds);
        
        // Log the sync result
        Log::info("Sync result for role {$role->id}: attached=" . count($synced['attached'] ?? []) . ", detached=" . count($synced['detached'] ?? []) . ", updated=" . count($synced['updated'] ?? []));
        
        // Verify the permissions were saved by querying the database directly
        $savedCount = DB::table('role_permission')
            ->where('role_id', $role->id)
            ->count();
        
        Log::info("Verified permissions count for role {$role->id} after sync: {$savedCount}");
        
        // Refresh the role to get updated permissions
        $role->refresh();
        $role->load('permissions');
        
        // Clear cache to ensure permission changes are reflected immediately
        // Note: Using Cache::flush() instead of tags since not all cache drivers support tagging
        try {
            Cache::flush();
        } catch (\Exception $e) {
            // If cache flush fails, continue anyway - permissions are still updated
            Log::warning('Cache flush failed after permission update: ' . $e->getMessage());
        }
        
        // Clear any relationship cache for the role
        $role->unsetRelation('permissions');
        
        ActivityLog::log('updated', $role, "Updated permissions for role: {$role->name} ({$savedCount} permissions assigned)");
        
        $attachedCount = count($synced['attached'] ?? []);
        $detachedCount = count($synced['detached'] ?? []);
        
        $message = $savedCount > 0 
            ? "✅ Permissions assigned successfully! {$savedCount} permission(s) are now active for this role. Sidebar menu items will appear/hide based on these permissions. Users with this role should refresh their dashboard to see the changes."
            : "⚠️ Permissions updated. No permissions are currently assigned to this role. All sidebar menu items (except Dashboard) will be hidden for users with this role.";
        
        if ($attachedCount > 0 || $detachedCount > 0) {
            $changes = [];
            if ($attachedCount > 0) {
                $changes[] = "{$attachedCount} permission(s) added";
            }
            if ($detachedCount > 0) {
                $changes[] = "{$detachedCount} permission(s) removed";
            }
            $message .= " (" . implode(", ", $changes) . ")";
        }
        
        return redirect()->route('super_admin.roles')->with('success', $message);
    }
    
    /**
     * Display activity logs
     */
    public function activityLogs(Request $request)
    {
        $query = ActivityLog::query();
        
        // Filter by user
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }
        
        // Filter by action
        if ($request->has('action') && $request->action) {
            $query->where('action', $request->action);
        }
        
        // Filter by model
        if ($request->has('model_type') && $request->model_type) {
            $query->where('model_type', $request->model_type);
        }
        
        // Date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Get all logs for client-side filtering (no pagination)
        $logs = $query->orderBy('created_at', 'desc')->get();
        
        // Get filter options - combine Staff and Guest users
        $staffUsers = Staff::select('id', 'name', 'email')->get()->map(function($user) {
            return ['id' => $user->id, 'name' => $user->name, 'email' => $user->email];
        });
        $guestUsers = Guest::select('id', 'name', 'email')->get()->map(function($user) {
            return ['id' => $user->id, 'name' => $user->name, 'email' => $user->email];
        });
        $users = $staffUsers->concat($guestUsers)->sortBy('name')->values();
        $actions = ActivityLog::distinct()->pluck('action')->sort();
        $modelTypes = ActivityLog::distinct()->pluck('model_type')->filter()->sort();
        
        return view('dashboard.super-admin.activity-logs', [
            'role' => 'super_admin',
            'userName' => (auth()->guard('staff')->user() ?? auth()->guard('guest')->user())->name ?? 'Super Admin',
            'userRole' => 'Super Administrator',
            'logs' => $logs,
            'users' => $users,
            'actions' => $actions,
            'modelTypes' => $modelTypes,
        ]);
    }
    
    /**
     * Display system logs
     */
    public function systemLogs(Request $request)
    {
        $query = SystemLog::query();
        
        // Filter by level
        if ($request->has('level') && $request->level) {
            $query->where('level', $request->level);
        }
        
        // Filter by channel
        if ($request->has('channel') && $request->channel) {
            $query->where('channel', $request->channel);
        }
        
        // Date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $logs = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Get filter options
        $levels = SystemLog::distinct()->pluck('level')->sort();
        $channels = SystemLog::distinct()->pluck('channel')->filter()->sort();
        
        return view('dashboard.super-admin.system-logs', [
            'role' => 'super_admin',
            'userName' => (auth()->guard('staff')->user() ?? auth()->guard('guest')->user())->name ?? 'Super Admin',
            'userRole' => 'Super Administrator',
            'logs' => $logs,
            'levels' => $levels,
            'channels' => $channels,
            'filters' => $request->only(['level', 'channel', 'date_from', 'date_to']),
        ]);
    }
    
    /**
     * Export activity logs
     */
    public function exportActivityLogs(Request $request)
    {
        $query = ActivityLog::query();
        
        // Apply same filters as activityLogs method
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }
        
        if ($request->has('action') && $request->action) {
            $query->where('action', $request->action);
        }
        
        if ($request->has('model_type') && $request->model_type) {
            $query->where('model_type', $request->model_type);
        }
        
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $logs = $query->orderBy('created_at', 'desc')->get();
        
        // Generate CSV filename with timestamp
        $filename = 'activity_logs_' . now()->format('Y-m-d_His') . '.csv';
        
        // Set headers for CSV download
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        // Create CSV content
        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8 to ensure Excel displays correctly
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // CSV Headers
            fputcsv($file, [
                'Timestamp',
                'User Name',
                'User Email',
                'Action',
                'Model Type',
                'Model ID',
                'Description',
                'IP Address',
                'User Agent',
                'Old Values',
                'New Values'
            ]);
            
            // CSV Data
            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->created_at->format('Y-m-d H:i:s'),
                    $log->user ? $log->user->name : 'System',
                    $log->user ? $log->user->email : 'N/A',
                    ucfirst($log->action),
                    $log->model_type ? class_basename($log->model_type) : 'N/A',
                    $log->model_id ?? 'N/A',
                    $log->description ?? '',
                    $log->ip_address ?? 'N/A',
                    $log->user_agent ?? 'N/A',
                    $log->old_values ? json_encode($log->old_values, JSON_UNESCAPED_UNICODE) : '',
                    $log->new_values ? json_encode($log->new_values, JSON_UNESCAPED_UNICODE) : ''
                ]);
            }
            
            fclose($file);
        };
        
        // Log the export activity
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'exported',
            'description' => 'Exported activity logs to CSV',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Clear old logs
     */
    public function clearLogs(Request $request)
    {
        try {
            $validated = $request->validate([
                'type' => 'required|in:activity,system,both',
                'days' => 'required|integer|min:1|max:365',
            ]);
            
            $date = Carbon::now()->subDays($validated['days']);
            $deletedCount = 0;
            $activityCount = 0;
            $systemCount = 0;
            
            // Count records before deletion
            if ($validated['type'] === 'activity' || $validated['type'] === 'both') {
                $activityCount = ActivityLog::where('created_at', '<', $date)->count();
            }
            
            if ($validated['type'] === 'system' || $validated['type'] === 'both') {
                $systemCount = SystemLog::where('created_at', '<', $date)->count();
            }
            
            $deletedCount = $activityCount + $systemCount;
            
            // Delete the logs
            if ($validated['type'] === 'activity' || $validated['type'] === 'both') {
                ActivityLog::where('created_at', '<', $date)->delete();
            }
            
            if ($validated['type'] === 'system' || $validated['type'] === 'both') {
                SystemLog::where('created_at', '<', $date)->delete();
            }
            
            // Log the clearing activity AFTER deletion (so it's not deleted)
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'cleared_logs',
                'description' => "Cleared {$validated['type']} logs older than {$validated['days']} days ({$deletedCount} records deleted)",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
            
            // Log to system logs (only if not clearing system logs)
            if ($validated['type'] !== 'system') {
                try {
                    SystemLog::log('info', "Logs cleared: {$validated['type']} older than {$validated['days']} days ({$deletedCount} records)", 'maintenance');
                } catch (\Exception $e) {
                    // If system log fails, continue anyway
                    Log::warning('Failed to log system log entry: ' . $e->getMessage());
                }
            }
            
            $message = "Successfully cleared {$deletedCount} log records older than {$validated['days']} days.";
            return redirect()->back()->with('success', $message);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Error clearing logs: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all(),
            ]);
            
            return redirect()->back()->with('error', 'Failed to clear logs: ' . $e->getMessage());
        }
    }

    // ==================== NEW FEATURES ====================

    /**
     * System Settings Management
     */
    public function systemSettings()
    {
        $settings = HotelSetting::all()->pluck('value', 'key');
        
        return view('dashboard.super-admin.system-settings', [
            'role' => 'super_admin',
            'userName' => (auth()->guard('staff')->user() ?? auth()->guard('guest')->user())->name ?? 'Super Admin',
            'userRole' => 'Super Administrator',
            'settings' => $settings,
        ]);
    }

    public function updateSystemSettings(Request $request)
    {
        $validated = $request->validate([
            'app_name' => 'nullable|string|max:255',
            'app_url' => 'nullable|url|max:255',
            'app_timezone' => 'nullable|string|max:50',
            'app_locale' => 'nullable|string|max:10',
            'session_lifetime' => 'nullable|integer|min:1|max:1440',
            'max_login_attempts' => 'nullable|integer|min:1|max:20',
            'lockout_duration' => 'nullable|integer|min:1|max:1440',
        ]);

        foreach ($validated as $key => $value) {
            if ($value !== null) {
                HotelSetting::setValue($key, $value);
            }
        }

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'updated',
            'model_type' => HotelSetting::class,
            'description' => 'Updated system settings',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', 'System settings updated successfully.');
    }

    /**
     * Failed Login Attempts
     */
    public function failedLoginAttempts(Request $request)
    {
        $query = FailedLoginAttempt::orderBy('created_at', 'desc');

        if ($request->has('email') && $request->email) {
            $query->where('email', 'like', "%{$request->email}%");
        }

        if ($request->has('ip_address') && $request->ip_address) {
            $query->where('ip_address', 'like', "%{$request->ip_address}%");
        }

        if ($request->has('blocked') && $request->blocked !== '') {
            $query->where('blocked', $request->blocked);
        }

        $attempts = $query->paginate(20);

        $stats = [
            'total' => FailedLoginAttempt::count(),
            'today' => FailedLoginAttempt::whereDate('created_at', Carbon::today())->count(),
            'blocked' => FailedLoginAttempt::where('blocked', true)->count(),
            'unique_ips' => FailedLoginAttempt::distinct('ip_address')->count('ip_address'),
        ];

        return view('dashboard.super-admin.failed-login-attempts', [
            'role' => 'super_admin',
            'userName' => (auth()->guard('staff')->user() ?? auth()->guard('guest')->user())->name ?? 'Super Admin',
            'userRole' => 'Super Administrator',
            'attempts' => $attempts,
            'stats' => $stats,
            'filters' => $request->only(['email', 'ip_address', 'blocked']),
        ]);
    }

    public function blockIp(Request $request, $ipAddress)
    {
        FailedLoginAttempt::where('ip_address', $ipAddress)
            ->update([
                'blocked' => true,
                'blocked_until' => now()->addDays($request->days ?? 7),
            ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'blocked_ip',
            'description' => "Blocked IP address: {$ipAddress}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', "IP address {$ipAddress} has been blocked.");
    }

    public function unblockIp($ipAddress)
    {
        FailedLoginAttempt::where('ip_address', $ipAddress)
            ->update([
                'blocked' => false,
                'blocked_until' => null,
            ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'unblocked_ip',
            'description' => "Unblocked IP address: {$ipAddress}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return back()->with('success', "IP address {$ipAddress} has been unblocked.");
    }

    /**
     * Force Logout Users
     */
    public function activeSessions()
    {
        // Get all active sessions from sessions table
        $allActiveSessions = DB::table('sessions')
            ->where('last_activity', '>', now()->subMinutes(config('session.lifetime', 120))->timestamp)
            ->orderBy('last_activity', 'desc')
            ->get();

        // Get all active session IDs for matching
        $activeSessionIds = $allActiveSessions->pluck('id')->toArray();
        
        $sessions = [];
        $processedEmails = []; // Track processed emails to avoid duplicates

        // Method 1: Check sessions table by user_id (if stored)
        foreach ($allActiveSessions as $session) {
            if ($session->user_id) {
                // Try to find in Staff first, then Guest
                $sessionUser = Staff::find($session->user_id) ?? Guest::find($session->user_id);
                if ($sessionUser && !isset($processedEmails[$sessionUser->email])) {
                    $sessions[] = [
                        'session_id' => $session->id,
                        'user' => $sessionUser,
                        'ip_address' => $session->ip_address,
                        'user_agent' => $session->user_agent,
                        'last_activity' => Carbon::createFromTimestamp($session->last_activity),
                    ];
                    $processedEmails[$sessionUser->email] = true;
                }
            }
        }

        // Method 2: Check Staff and Guest tables for users with active last_session_id
        // This is more reliable since we store last_session_id in user tables
        if (!empty($activeSessionIds)) {
            $staffWithActiveSessions = Staff::whereNotNull('last_session_id')
                ->whereNotNull('session_token')
                ->whereIn('last_session_id', $activeSessionIds)
                ->get();
            
            foreach ($staffWithActiveSessions as $staff) {
                if (!isset($processedEmails[$staff->email])) {
                    // Find the session for this user
                    $userSession = $allActiveSessions->firstWhere('id', $staff->last_session_id);
                    
                    $sessions[] = [
                        'session_id' => $staff->last_session_id,
                        'user' => $staff,
                        'ip_address' => $userSession->ip_address ?? null,
                        'user_agent' => $userSession->user_agent ?? null,
                        'last_activity' => $userSession ? Carbon::createFromTimestamp($userSession->last_activity) : now(),
                    ];
                    $processedEmails[$staff->email] = true;
                }
            }
            
            $guestsWithActiveSessions = Guest::whereNotNull('last_session_id')
                ->whereNotNull('session_token')
                ->whereIn('last_session_id', $activeSessionIds)
                ->get();
            
            foreach ($guestsWithActiveSessions as $guest) {
                if (!isset($processedEmails[$guest->email])) {
                    // Find the session for this user
                    $userSession = $allActiveSessions->firstWhere('id', $guest->last_session_id);
                    
                    $sessions[] = [
                        'session_id' => $guest->last_session_id,
                        'user' => $guest,
                        'ip_address' => $userSession->ip_address ?? null,
                        'user_agent' => $userSession->user_agent ?? null,
                        'last_activity' => $userSession ? Carbon::createFromTimestamp($userSession->last_activity) : now(),
                    ];
                    $processedEmails[$guest->email] = true;
                }
            }
        }

        // Sort by last activity (most recent first)
        usort($sessions, function($a, $b) {
            return $b['last_activity']->timestamp <=> $a['last_activity']->timestamp;
        });

        return view('dashboard.super-admin.active-sessions', [
            'role' => 'super_admin',
            'userName' => (auth()->guard('staff')->user() ?? auth()->guard('guest')->user())->name ?? 'Super Admin',
            'userRole' => 'Super Administrator',
            'sessions' => $sessions,
        ]);
    }

    public function forceLogout($sessionId)
    {
        // Get user before deleting session
        $session = DB::table('sessions')->where('id', $sessionId)->first();
        
        // Delete the session
        DB::table('sessions')->where('id', $sessionId)->delete();

        // Clear user session token if user exists
        if ($session && $session->user_id) {
            User::where('id', $session->user_id)->update([
                'session_token' => null,
                'last_session_id' => null,
            ]);
        }

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'force_logout',
            'description' => "Force logged out session: {$sessionId}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return back()->with('success', 'User has been force logged out successfully.');
    }

    public function forceLogoutUser($userId)
    {
        $user = User::findOrFail($userId);
        
        // Clear user session token
        $user->update([
            'session_token' => null,
            'last_session_id' => null,
        ]);

        // Delete all sessions for this user
        DB::table('sessions')->where('user_id', $userId)->delete();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'force_logout_user',
            'model_type' => User::class,
            'model_id' => $userId,
            'description' => "Force logged out user: {$user->name} ({$user->email})",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return back()->with('success', "User {$user->name} has been force logged out from all devices.");
    }

    /**
     * Cache Management
     */
    public function cacheManagement()
    {
        $cacheStats = [
            'config_cache' => File::exists(base_path('bootstrap/cache/config.php')),
            'route_cache' => File::exists(base_path('bootstrap/cache/routes-v7.php')),
            'view_cache' => count(File::glob(storage_path('framework/views/*.php'))),
        ];

        return view('dashboard.super-admin.cache-management', [
            'role' => 'super_admin',
            'userName' => (auth()->guard('staff')->user() ?? auth()->guard('guest')->user())->name ?? 'Super Admin',
            'userRole' => 'Super Administrator',
            'cacheStats' => $cacheStats,
        ]);
    }

    public function clearCache(Request $request)
    {
        $type = $request->type ?? 'all';

        try {
            switch ($type) {
                case 'config':
                    Artisan::call('config:clear');
                    $message = 'Configuration cache cleared successfully.';
                    break;
                case 'route':
                    Artisan::call('route:clear');
                    $message = 'Route cache cleared successfully.';
                    break;
                case 'view':
                    Artisan::call('view:clear');
                    $message = 'View cache cleared successfully.';
                    break;
                case 'cache':
                    Artisan::call('cache:clear');
                    $message = 'Application cache cleared successfully.';
                    break;
                case 'all':
                    Artisan::call('optimize:clear');
                    $message = 'All caches cleared successfully.';
                    break;
                default:
                    return back()->with('error', 'Invalid cache type.');
            }

            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'cleared_cache',
                'description' => "Cleared {$type} cache",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return back()->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', 'Error clearing cache: ' . $e->getMessage());
        }
    }

    /**
     * Maintenance Mode
     */
    public function maintenanceMode()
    {
        $isDown = File::exists(storage_path('framework/down')) || File::exists(storage_path('framework/maintenance.php'));
        $maintenanceMessage = HotelSetting::getValue('maintenance_message', 'System is under maintenance. Please check back later.');

        return view('dashboard.super-admin.maintenance-mode', [
            'role' => 'super_admin',
            'userName' => (auth()->guard('staff')->user() ?? auth()->guard('guest')->user())->name ?? 'Super Admin',
            'userRole' => 'Super Administrator',
            'isDown' => $isDown,
            'maintenanceMessage' => $maintenanceMessage,
        ]);
    }

    public function toggleMaintenanceMode(Request $request)
    {
        $isDown = File::exists(storage_path('framework/down'));

        try {
            if ($isDown) {
                // Remove maintenance files (both formats)
                if (File::exists(storage_path('framework/down'))) {
                    File::delete(storage_path('framework/down'));
                }
                if (File::exists(storage_path('framework/maintenance.php'))) {
                    File::delete(storage_path('framework/maintenance.php'));
                }
                $message = 'Maintenance mode disabled. System is now live.';
                $action = 'disabled_maintenance';
            } else {
                $maintenanceMessage = $request->message ?? 'System is under maintenance. Please check back later.';
                // Create maintenance file with message (JSON format for Laravel 11)
                File::put(
                    storage_path('framework/down'),
                    json_encode([
                        'time' => now()->getTimestamp(),
                        'retry' => 60,
                        'message' => $maintenanceMessage,
                    ], JSON_PRETTY_PRINT)
                );
                // Also create the old format for compatibility
                $maintenancePhp = "<?php\n\nhttp_response_code(503);\nheader('Retry-After: 60');\nheader('Content-Type: text/html; charset=utf-8');\n\necho '<!DOCTYPE html>\n<html>\n<head>\n    <meta charset=\"utf-8\">\n    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">\n    <title>Service Unavailable</title>\n    <style>\n        body { font-family: -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, sans-serif; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; background: #f5f5f5; }\n        .container { text-align: center; padding: 2rem; background: white; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); max-width: 500px; }\n        h1 { color: #333; margin-bottom: 1rem; }\n        p { color: #666; line-height: 1.6; }\n    </style>\n</head>\n<body>\n    <div class=\"container\">\n        <h1>Service Unavailable</h1>\n        <p>" . addslashes($maintenanceMessage) . "</p>\n    </div>\n</body>\n</html>';\n";
                File::put(storage_path('framework/maintenance.php'), $maintenancePhp);
                HotelSetting::setValue('maintenance_message', $maintenanceMessage);
                $message = 'Maintenance mode enabled. System is now offline. Super admins can still access.';
                $action = 'enabled_maintenance';
            }

            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => $action,
                'description' => $message,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return back()->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', 'Error toggling maintenance mode: ' . $e->getMessage());
        }
    }

    /**
     * Display system health and server resources
     */
    public function systemHealth()
    {
        // Get server resource information
        $serverInfo = $this->getServerResources();
        
        // Get database statistics
        $dbStats = $this->getDatabaseStats();
        
        // Get PHP information
        $phpInfo = [
            'version' => PHP_VERSION,
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'current_memory_usage' => $this->formatBytes(memory_get_usage(true)),
            'peak_memory_usage' => $this->formatBytes(memory_get_peak_usage(true)),
        ];
        
        return view('dashboard.super-admin.system-health', [
            'role' => 'super_admin',
            'userName' => (auth()->guard('staff')->user() ?? auth()->guard('guest')->user())->name ?? 'Super Admin',
            'userRole' => 'Super Administrator',
            'serverInfo' => $serverInfo,
            'dbStats' => $dbStats,
            'phpInfo' => $phpInfo,
        ]);
    }
    
    /**
     * Get server resource information
     */
    private function getServerResources()
    {
        $info = [
            'os' => PHP_OS,
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'php_version' => PHP_VERSION,
            'server_time' => now()->format('Y-m-d H:i:s'),
            'timezone' => date_default_timezone_get(),
        ];
        
        // Memory Information
        $info['memory'] = [
            'total' => $this->getTotalMemory(),
            'free' => $this->getFreeMemory(),
            'used' => $this->getUsedMemory(),
            'usage_percent' => $this->getMemoryUsagePercent(),
        ];
        
        // CPU Information (if available)
        $info['cpu'] = $this->getCpuInfo();
        
        // Disk Information
        $info['disk'] = $this->getDiskInfo();
        
        // Uptime (if available)
        $info['uptime'] = $this->getUptime();
        
        return $info;
    }
    
    /**
     * Get total system memory
     */
    private function getTotalMemory()
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // Windows - TotalPhysicalMemory returns bytes
            $output = @\shell_exec('wmic computersystem get TotalPhysicalMemory 2>nul');
            if ($output) {
                $lines = array_filter(array_map('trim', explode("\n", $output)));
                foreach ($lines as $line) {
                    if (is_numeric($line) && $line > 0) {
                        return $this->formatBytes((int)$line);
                    }
                }
            }
        } else {
            // Linux/Unix
            $meminfo = @file_get_contents('/proc/meminfo');
            if ($meminfo) {
                preg_match('/MemTotal:\s+(\d+)\s+kB/', $meminfo, $matches);
                if (isset($matches[1])) {
                    return $this->formatBytes($matches[1] * 1024);
                }
            }
        }
        return 'N/A';
    }
    
    /**
     * Get free system memory
     */
    private function getFreeMemory()
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // Windows - FreePhysicalMemory returns KB
            $output = @\shell_exec('wmic OS get FreePhysicalMemory 2>nul');
            if ($output) {
                $lines = array_filter(array_map('trim', explode("\n", $output)));
                foreach ($lines as $line) {
                    if (is_numeric($line) && $line > 0) {
                        return $this->formatBytes((int)$line * 1024);
                    }
                }
            }
        } else {
            // Linux/Unix
            $meminfo = @file_get_contents('/proc/meminfo');
            if ($meminfo) {
                preg_match('/MemAvailable:\s+(\d+)\s+kB/', $meminfo, $matches);
                if (isset($matches[1])) {
                    return $this->formatBytes($matches[1] * 1024);
                }
            }
        }
        return 'N/A';
    }
    
    /**
     * Get used system memory
     */
    private function getUsedMemory()
    {
        $total = $this->getTotalMemoryBytes();
        $free = $this->getFreeMemoryBytes();
        if ($total > 0 && $free > 0) {
            return $this->formatBytes($total - $free);
        }
        return 'N/A';
    }
    
    /**
     * Get memory usage percentage
     */
    private function getMemoryUsagePercent()
    {
        $total = $this->getTotalMemoryBytes();
        $free = $this->getFreeMemoryBytes();
        if ($total > 0 && $free > 0) {
            return round((($total - $free) / $total) * 100, 2);
        }
        return 0;
    }
    
    /**
     * Get total memory in bytes
     */
    private function getTotalMemoryBytes()
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // Windows - TotalPhysicalMemory returns bytes
            $output = @\shell_exec('wmic computersystem get TotalPhysicalMemory 2>nul');
            if ($output) {
                $lines = array_filter(array_map('trim', explode("\n", $output)));
                foreach ($lines as $line) {
                    if (is_numeric($line) && $line > 0) {
                        return (int)$line;
                    }
                }
            }
        } else {
            $meminfo = @file_get_contents('/proc/meminfo');
            if ($meminfo) {
                preg_match('/MemTotal:\s+(\d+)\s+kB/', $meminfo, $matches);
                if (isset($matches[1])) {
                    return (int)$matches[1] * 1024;
                }
            }
        }
        return 0;
    }
    
    /**
     * Get free memory in bytes
     */
    private function getFreeMemoryBytes()
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // Windows - FreePhysicalMemory returns KB
            $output = @\shell_exec('wmic OS get FreePhysicalMemory 2>nul');
            if ($output) {
                $lines = array_filter(array_map('trim', explode("\n", $output)));
                foreach ($lines as $line) {
                    if (is_numeric($line) && $line > 0) {
                        return (int)$line * 1024;
                    }
                }
            }
        } else {
            $meminfo = @file_get_contents('/proc/meminfo');
            if ($meminfo) {
                preg_match('/MemAvailable:\s+(\d+)\s+kB/', $meminfo, $matches);
                if (isset($matches[1])) {
                    return (int)$matches[1] * 1024;
                }
            }
        }
        return 0;
    }
    
    /**
     * Get CPU information
     */
    private function getCpuInfo()
    {
        $info = [
            'usage_percent' => 'N/A',
            'cores' => 'N/A',
            'model' => 'N/A',
        ];
        
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // Windows - Get CPU cores
            $output = \shell_exec('wmic cpu get NumberOfCores 2>nul');
            if ($output) {
                $cores = trim(explode("\n", $output)[1]);
                $info['cores'] = $cores ?: 'N/A';
            }
            
            // Windows - Get CPU model
            $output = \shell_exec('wmic cpu get Name 2>nul');
            if ($output) {
                $model = trim(explode("\n", $output)[1]);
                $info['model'] = $model ?: 'N/A';
            }
            
            // Windows - CPU usage (requires additional tools or WMI queries)
            $info['usage_percent'] = 'N/A (Requires additional monitoring)';
        } else {
            // Linux - Get CPU cores
            $cores = @\shell_exec('nproc');
            if ($cores) {
                $info['cores'] = trim($cores);
            }
            
            // Linux - Get CPU model
            $model = @\shell_exec("grep 'model name' /proc/cpuinfo | head -1 | cut -d':' -f2");
            if ($model) {
                $info['model'] = trim($model);
            }
            
            // Linux - Get CPU usage
            $load = sys_getloadavg();
            if ($load) {
                $info['usage_percent'] = round(($load[0] / $info['cores']) * 100, 2);
            }
        }
        
        return $info;
    }
    
    /**
     * Get disk information
     */
    private function getDiskInfo()
    {
        $diskPath = base_path();
        $total = disk_total_space($diskPath);
        $free = disk_free_space($diskPath);
        $used = $total - $free;
        
        return [
            'total' => $this->formatBytes($total),
            'free' => $this->formatBytes($free),
            'used' => $this->formatBytes($used),
            'usage_percent' => $total > 0 ? round(($used / $total) * 100, 2) : 0,
            'path' => $diskPath,
        ];
    }
    
    /**
     * Get system uptime
     */
    private function getUptime()
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // Windows
            $output = \shell_exec('wmic os get lastbootuptime 2>nul');
            if ($output) {
                $bootTime = trim(explode("\n", $output)[1]);
                if ($bootTime) {
                    // Parse Windows boot time format
                    $year = substr($bootTime, 0, 4);
                    $month = substr($bootTime, 4, 2);
                    $day = substr($bootTime, 6, 2);
                    $hour = substr($bootTime, 8, 2);
                    $minute = substr($bootTime, 10, 2);
                    $second = substr($bootTime, 12, 2);
                    
                    $bootTimestamp = strtotime("{$year}-{$month}-{$day} {$hour}:{$minute}:{$second}");
                    $uptime = time() - $bootTimestamp;
                    return $this->formatUptime($uptime);
                }
            }
        } else {
            // Linux
            $uptime = @file_get_contents('/proc/uptime');
            if ($uptime) {
                $seconds = (float)explode(' ', $uptime)[0];
                return $this->formatUptime($seconds);
            }
        }
        return 'N/A';
    }
    
    /**
     * Format uptime
     */
    private function formatUptime($seconds)
    {
        $days = floor($seconds / 86400);
        $hours = floor(($seconds % 86400) / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        
        $result = [];
        if ($days > 0) $result[] = $days . ' day' . ($days > 1 ? 's' : '');
        if ($hours > 0) $result[] = $hours . ' hour' . ($hours > 1 ? 's' : '');
        if ($minutes > 0) $result[] = $minutes . ' minute' . ($minutes > 1 ? 's' : '');
        
        return !empty($result) ? implode(', ', $result) : 'Less than a minute';
    }
    
    /**
     * Get database statistics
     */
    private function getDatabaseStats()
    {
        try {
            $dbName = DB::connection()->getDatabaseName();
            
            // Get database size
            $dbSize = DB::select("SELECT 
                ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'size_mb'
                FROM information_schema.tables 
                WHERE table_schema = ?", [$dbName]);
            
            $size = $dbSize[0]->size_mb ?? 0;
            
            // Get table counts
            $tables = DB::select("SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = ?", [$dbName]);
            $tableCount = $tables[0]->count ?? 0;
            
            // Get total records
            $totalRecords = 0;
            $tableList = DB::select("SELECT table_name FROM information_schema.tables WHERE table_schema = ?", [$dbName]);
            foreach ($tableList as $table) {
                try {
                    $count = DB::table($table->table_name)->count();
                    $totalRecords += $count;
                } catch (\Exception $e) {
                    // Skip tables that can't be counted
                }
            }
            
            return [
                'name' => $dbName,
                'size' => $this->formatBytes($size * 1024 * 1024),
                'size_mb' => $size,
                'table_count' => $tableCount,
                'total_records' => number_format($totalRecords),
            ];
        } catch (\Exception $e) {
            return [
                'name' => 'Unknown',
                'size' => 'N/A',
                'size_mb' => 0,
                'table_count' => 0,
                'total_records' => 0,
            ];
        }
    }
    
    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        if ($bytes === 0 || $bytes === 'N/A') {
            return '0 B';
        }
        
        $bytes = (float)$bytes;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
