<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Models\ActivityLog;
use App\Models\User;
use App\Models\Staff;
use App\Models\Guest;
use App\Models\FailedLoginAttempt;
use App\Models\LoginOtp;
use App\Models\SystemLog;
use App\Mail\LoginOtpMail;
use App\Services\SmsService;

class AuthController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm($role = 'manager')
    {
        // If user is already logged in, redirect to dashboard
        $user = Auth::guard('staff')->user() ?? Auth::guard('guest')->user();
        if ($user) {
            $userRole = $user->role ?? 'guest';
            
            // Redirect based on user role
            if ($userRole === 'super_admin') {
                return redirect()->route('super_admin.dashboard');
            } elseif ($userRole === 'manager') {
                return redirect()->route('admin.dashboard');
            } elseif ($userRole === 'reception') {
                return redirect()->route('reception.dashboard');
            } elseif ($userRole === 'bar_keeper') {
                return redirect()->route('bar-keeper.dashboard');
            } elseif ($userRole === 'housekeeper') {
                return redirect()->route('housekeeper.dashboard');
            } elseif ($userRole === 'head_chef') {
                return redirect()->route('chef-master.dashboard');
            } elseif ($userRole === 'waiter') {
                return redirect()->route('waiter.dashboard');
            } elseif ($userRole === 'guest') {
                return redirect()->route('customer.dashboard');
            }
        }
        
        return view('dashboard.page-login', ['role' => $role]);
    }

    /**
     * Show unified login form for all roles
     */
    public function showUnifiedLogin()
    {
        // Always show login page, even if user is already authenticated
        // This allows users to switch accounts or re-login
        return view('dashboard.page-login'); // no role passed → unified
    }

    /**
     * Handle login request
     */
    public function login(Request $request, $role = 'manager')
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Check if IP address is blocked
        $ipAddress = $request->ip();
        if (FailedLoginAttempt::isIpBlocked($ipAddress)) {
            $blockInfo = FailedLoginAttempt::getIpBlockInfo($ipAddress);
            $blockedUntil = $blockInfo && $blockInfo->blocked_until 
                ? $blockInfo->blocked_until->format('Y-m-d H:i:s') 
                : 'indefinitely';
            
            // Log the blocked attempt
            FailedLoginAttempt::create([
                'email' => $request->email,
                'ip_address' => $ipAddress,
                'user_agent' => $request->userAgent(),
                'reason' => 'ip_blocked',
            ]);

            return back()->withErrors([
                'email' => "Your IP address ({$ipAddress}) has been blocked. Access denied until {$blockedUntil}."
            ])->withInput($request->only('email'));
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');
        $user = null;

        // Try to authenticate as Staff first (super_admin, manager, reception)
        $staff = Staff::where('email', $credentials['email'])->first();
        if ($staff) {
            // Check password
            if (Hash::check($credentials['password'], $staff->password)) {
                // Check if staff is active
                if (!$staff->is_active) {
                    return back()->withErrors([
                        'email' => 'Your account has been deactivated. Please contact administrator.'
                    ])->withInput($request->only('email'));
                }
                
                // Log in as staff
                Auth::guard('staff')->login($staff, $remember);
                $user = Auth::guard('staff')->user();
            }
        }
        
        // If staff authentication failed, try Guest
        if (!$user) {
            $guest = Guest::where('email', $credentials['email'])->first();
            if ($guest) {
                // Check password
                if (Hash::check($credentials['password'], $guest->password)) {
                    // Check if guest is active
                    if (!$guest->is_active) {
                        return back()->withErrors([
                            'email' => 'Your account has been deactivated. Please contact administrator.'
                        ])->withInput($request->only('email'));
                    }
                    
                    // Log in as guest
                    Auth::guard('guest')->login($guest, $remember);
                    $user = Auth::guard('guest')->user();
                }
            }
        }
        
        // If both failed, track failed login attempt
        if (!$user) {
            FailedLoginAttempt::record(
                $request->email,
                'wrong_password',
                $request->ip(),
                $request->userAgent()
            );

            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->withInput($request->only('email'));
        }

        // If we get here, user is authenticated
        if ($user) {
            
            // Regenerate session to prevent session fixation attacks
            $request->session()->regenerate();
            
            // Get the NEW session ID after regeneration
            $sessionId = $request->session()->getId();
            
            // Generate new session token
            $sessionToken = \Illuminate\Support\Str::random(60);
            
            // IMPORTANT: Update user with new session token and session ID
            // This will invalidate any previous sessions from other devices
            $user->session_token = hash('sha256', $sessionToken);
            $user->last_session_id = $sessionId;
            $user->save();
            
            // IMPORTANT: Update sessions table with user_id for active session tracking
            // Laravel doesn't automatically set user_id for custom guards
            DB::table('sessions')
                ->where('id', $sessionId)
                ->update([
                    'user_id' => $user->id,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            
            // Store session token in session for validation
            $request->session()->put('user_session_token', $sessionToken);
            
            // Get user role - staff has role property, guest doesn't (defaults to 'guest')
            $userRole = $user->role ?? 'guest';
            
            // Check if user role matches the login route role
            $allowedRoles = [
                'admin' => ['manager'], // admin route uses manager role
                'super_admin' => ['super_admin'],
                'reception' => ['reception'],
                'guest' => ['guest']
            ];
            
            if (isset($allowedRoles[$role]) && !in_array($userRole, $allowedRoles[$role])) {
                Auth::guard('staff')->logout();
                Auth::guard('guest')->logout();
                return back()->withErrors([
                    'email' => 'You do not have permission to access this area.'
                ])->withInput($request->only('email'));
            }
            
            // Clear any invalid intended URL (like API endpoints)
            $intendedUrl = $request->session()->pull('url.intended');
            
            // Only use intended URL if it's a valid dashboard route
            $validDashboardRoutes = [
                route('admin.dashboard'),
                route('super_admin.dashboard'),
                route('reception.dashboard'),
                route('bar-keeper.dashboard'),
                route('housekeeper.dashboard'),
                route('chef-master.dashboard'),
                route('waiter.dashboard'),
                route('customer.dashboard'),
            ];
            
            // Debug: Log the user role for troubleshooting
            \Log::info('Login redirect', [
                'user_role' => $userRole,
                'user_id' => $user->id,
                'user_email' => $user->email
            ]);
            
            // If intended URL is not a valid dashboard, ignore it
            if ($intendedUrl && !in_array($intendedUrl, $validDashboardRoutes)) {
                // Check if it's a valid dashboard path (not API endpoints)
                $isValidPath = str_contains($intendedUrl, '/admin/') || 
                               str_contains($intendedUrl, '/super-admin/') ||
                               str_contains($intendedUrl, '/reception/') || 
                               str_contains($intendedUrl, '/chef-master/') ||
                               str_contains($intendedUrl, '/customer/');
                
                if (!$isValidPath || str_contains($intendedUrl, '/notifications/') || 
                    str_contains($intendedUrl, '/api/') || 
                    str_contains($intendedUrl, '/ajax/')) {
                    $intendedUrl = null;
                }
            }

            // Log login activity
            ActivityLog::create([
                'user_id' => $user->id,
                'user_type' => get_class($user),
                'action' => 'logged_in',
                'description' => "User logged in: {$user->name} ({$user->email})",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
            
            // Redirect based on user role
            if ($userRole === 'super_admin') {
                $redirectUrl = $intendedUrl && in_array($intendedUrl, $validDashboardRoutes) 
                    ? $intendedUrl 
                    : route('super_admin.dashboard');
                return redirect($redirectUrl)->with('success', 'Welcome back, ' . $user->name . '!');
            } elseif ($userRole === 'manager') {
                $redirectUrl = $intendedUrl && in_array($intendedUrl, $validDashboardRoutes) 
                    ? $intendedUrl 
                    : route('admin.dashboard');
                return redirect($redirectUrl)->with('success', 'Welcome back, ' . $user->name . '!');
            } elseif ($userRole === 'reception') {
                $redirectUrl = $intendedUrl && in_array($intendedUrl, $validDashboardRoutes) 
                    ? $intendedUrl 
                    : route('reception.dashboard');
                return redirect($redirectUrl)->with('success', 'Welcome back, ' . $user->name . '!');
            } elseif ($userRole === 'bar_keeper') {
                $redirectUrl = $intendedUrl && in_array($intendedUrl, $validDashboardRoutes) 
                    ? $intendedUrl 
                    : route('bar-keeper.dashboard');
                return redirect($redirectUrl)->with('success', 'Welcome back, ' . $user->name . '!');
            } elseif ($userRole === 'housekeeper') {
                $redirectUrl = $intendedUrl && in_array($intendedUrl, $validDashboardRoutes) 
                    ? $intendedUrl 
                    : route('housekeeper.dashboard');
                return redirect($redirectUrl)->with('success', 'Welcome back, ' . $user->name . '!');
            } elseif ($userRole === 'head_chef') {
                $redirectUrl = $intendedUrl && in_array($intendedUrl, $validDashboardRoutes) 
                    ? $intendedUrl 
                    : route('chef-master.dashboard');
                return redirect($redirectUrl)->with('success', 'Welcome back, ' . $user->name . '!');
            } elseif ($userRole === 'waiter') {
                $redirectUrl = $intendedUrl && in_array($intendedUrl, $validDashboardRoutes) 
                    ? $intendedUrl 
                    : route('waiter.dashboard');
                return redirect($redirectUrl)->with('success', 'Welcome back, ' . $user->name . '!');
            } elseif ($userRole === 'guest') {
                $redirectUrl = $intendedUrl && in_array($intendedUrl, $validDashboardRoutes) 
                    ? $intendedUrl 
                    : route('customer.dashboard');
                return redirect($redirectUrl)->with('success', 'Welcome back, ' . $user->name . '!');
            }
        }
    }

    /**
     * Handle unified login without enforcing a specific route role
     */
    public function loginUnified(Request $request)
    {
        // Log that the method was called with full CSRF debugging info
        \Log::channel('daily')->info('=== LOGIN UNIFIED METHOD CALLED ===', [
            'email' => $request->email ?? 'no email',
            'has_password' => !empty($request->password),
            'ip' => $request->ip(),
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'scheme' => $request->getScheme(),
            'host' => $request->getHost(),
            
            // CSRF Token Debug Info
            'csrf_token_in_form' => $request->has('_token'),
            'csrf_token_value' => $request->has('_token') ? substr($request->input('_token'), 0, 20) . '...' : 'NOT FOUND',
            'csrf_token_header' => $request->header('X-CSRF-TOKEN') ? substr($request->header('X-CSRF-TOKEN'), 0, 20) . '...' : 'NOT FOUND',
            'session_token' => $request->session()->token() ? substr($request->session()->token(), 0, 20) . '...' : 'NOT FOUND',
            'session_id' => $request->session()->getId(),
            'session_started' => $request->session()->isStarted(),
            
            // Configuration
            'app_url' => config('app.url'),
            'app_url_scheme' => config('app.url') ? parse_url(config('app.url'), PHP_URL_SCHEME) : 'NOT SET',
            'session_domain' => config('session.domain'),
            'session_secure' => config('session.secure'),
            'session_driver' => config('session.driver'),
            
            // Cookies
            'session_cookie_present' => $request->hasCookie(config('session.cookie')),
            'session_cookie_name' => config('session.cookie'),
        ]);
        
        try {
            // Validate email and password (OTP disabled)
            $validationRules = [
                'email' => 'required|email',
                'password' => 'required|string',
            ];
            
            $request->validate($validationRules);
            \Log::info('Validation passed - Direct login (OTP disabled)');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed', ['errors' => $e->errors()]);
            return back()->withErrors($e->errors())->withInput($request->only('email'));
        } catch (\Exception $e) {
            \Log::error('Login validation error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withErrors([
                'email' => 'An error occurred during login. Please try again.'
            ])->withInput($request->only('email'));
        }

        try {
            // Check if IP address is blocked
            $ipAddress = $request->ip();
            if (FailedLoginAttempt::isIpBlocked($ipAddress)) {
                $blockInfo = FailedLoginAttempt::getIpBlockInfo($ipAddress);
                $blockedUntil = $blockInfo && $blockInfo->blocked_until 
                    ? $blockInfo->blocked_until->format('Y-m-d H:i:s') 
                    : 'indefinitely';
                
                // Log the blocked attempt
                FailedLoginAttempt::create([
                    'email' => $request->email,
                    'ip_address' => $ipAddress,
                    'user_agent' => $request->userAgent(),
                    'reason' => 'ip_blocked',
                ]);

                return back()->withErrors([
                    'email' => "Your IP address ({$ipAddress}) has been blocked. Access denied until {$blockedUntil}."
                ])->withInput($request->only('email'));
            }

            // Step 1: Validate credentials and login directly (OTP DISABLED)
            $credentials = $request->only('email', 'password');
            $user = null;
            $userType = null;

            \Log::info('Login attempt started - Direct login (OTP disabled)', ['email' => $credentials['email']]);

            // Try to authenticate as Staff first (super_admin, manager, reception)
            $staff = Staff::where('email', $credentials['email'])->first();
            if ($staff) {
                \Log::info('Staff found', ['email' => $credentials['email'], 'staff_id' => $staff->id]);
                // Check password
                if (Hash::check($credentials['password'], $staff->password)) {
                    \Log::info('Staff password correct', ['email' => $credentials['email']]);
                    // Check if staff is active
                    if (!$staff->is_active) {
                        return back()->withErrors([
                            'email' => 'Your account has been deactivated. Please contact administrator.'
                        ])->withInput($request->only('email'));
                    }
                    $user = $staff;
                    $userType = 'staff';
                } else {
                    \Log::warning('Staff password incorrect', ['email' => $credentials['email']]);
                }
            } else {
                \Log::info('Staff not found', ['email' => $credentials['email']]);
            }
            
            // If staff authentication failed, try Guest
            if (!$user) {
                $guest = Guest::where('email', $credentials['email'])->first();
                if ($guest) {
                    \Log::info('Guest found', ['email' => $credentials['email'], 'guest_id' => $guest->id]);
                    // Check password
                    if (Hash::check($credentials['password'], $guest->password)) {
                        \Log::info('Guest password correct', ['email' => $credentials['email']]);
                        // Check if guest is active
                        if (!$guest->is_active) {
                            return back()->withErrors([
                                'email' => 'Your account has been deactivated. Please contact administrator.'
                            ])->withInput($request->only('email'));
                        }
                        $user = $guest;
                        $userType = 'guest';
                    } else {
                        \Log::warning('Guest password incorrect', ['email' => $credentials['email']]);
                    }
                } else {
                    \Log::info('Guest not found', ['email' => $credentials['email']]);
                }
            }
            
            // If both failed, track failed login attempt
            if (!$user) {
                \Log::warning('Login failed - no user authenticated', ['email' => $credentials['email']]);
                
                FailedLoginAttempt::record(
                    $request->email,
                    'wrong_password',
                    $request->ip(),
                    $request->userAgent()
                );

                return back()->withErrors([
                    'email' => 'The provided credentials do not match our records.',
                ])->withInput($request->only('email'));
            }

            // Credentials are valid - Login directly (OTP DISABLED)
            \Log::info('Logging in user directly - OTP disabled', [
                'user_id' => $user->id,
                'user_type' => $userType
            ]);

            // Log in the user
            $remember = $request->has('remember');
            if ($userType === 'staff') {
                Auth::guard('staff')->login($user, $remember);
            } else {
                Auth::guard('guest')->login($user, $remember);
            }

            // Regenerate session to prevent session fixation attacks
            $request->session()->regenerate();
            
            // Get the NEW session ID after regeneration
            $sessionId = $request->session()->getId();
            
            // Generate new session token
            $sessionToken = \Illuminate\Support\Str::random(60);
            
            // Update user with new session token and session ID
            $user->session_token = hash('sha256', $sessionToken);
            $user->last_session_id = $sessionId;
            $user->save();
            
            // Update sessions table with user_id
            DB::table('sessions')
                ->where('id', $sessionId)
                ->update([
                    'user_id' => $user->id,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            
            // Store session token in session
            $request->session()->put('user_session_token', $sessionToken);

            // Get user role
            $userRole = $user->role ?? 'guest';

            // Get intended URL
            $intendedUrl = $request->session()->pull('url.intended');
            $validDashboardRoutes = [
                route('admin.dashboard'),
                route('super_admin.dashboard'),
                route('reception.dashboard'),
                route('bar-keeper.dashboard'),
                route('housekeeper.dashboard'),
                route('chef-master.dashboard'),
                route('waiter.dashboard'),
                route('customer.dashboard'),
            ];
            
            if ($intendedUrl && !in_array($intendedUrl, $validDashboardRoutes)) {
                $isValidPath = str_contains($intendedUrl, '/admin/') || 
                               str_contains($intendedUrl, '/super-admin/') ||
                               str_contains($intendedUrl, '/reception/') || 
                               str_contains($intendedUrl, '/bar-keeper/') ||
                               str_contains($intendedUrl, '/chef-master/') ||
                               str_contains($intendedUrl, '/customer/');
                
                if (!$isValidPath || str_contains($intendedUrl, '/notifications/') || 
                    str_contains($intendedUrl, '/api/') || 
                    str_contains($intendedUrl, '/ajax/')) {
                    $intendedUrl = null;
                }
            }

            // Log login activity
            ActivityLog::create([
                'user_id' => $user->id,
                'user_type' => get_class($user),
                'action' => 'logged_in',
                'description' => "User logged in: {$user->name} ({$user->email})",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Redirect based on user role
            if ($userRole === 'super_admin') {
                $redirectUrl = $intendedUrl && in_array($intendedUrl, $validDashboardRoutes) 
                    ? $intendedUrl 
                    : route('super_admin.dashboard');
                return redirect($redirectUrl)->with('success', 'Welcome back, ' . $user->name . '!');
            } elseif ($userRole === 'manager') {
                $redirectUrl = $intendedUrl && in_array($intendedUrl, $validDashboardRoutes) 
                    ? $intendedUrl 
                    : route('admin.dashboard');
                return redirect($redirectUrl)->with('success', 'Welcome back, ' . $user->name . '!');
            } elseif ($userRole === 'reception') {
                $redirectUrl = $intendedUrl && in_array($intendedUrl, $validDashboardRoutes) 
                    ? $intendedUrl 
                    : route('reception.dashboard');
                return redirect($redirectUrl)->with('success', 'Welcome back, ' . $user->name . '!');
            } elseif ($userRole === 'bar_keeper') {
                $redirectUrl = $intendedUrl && in_array($intendedUrl, $validDashboardRoutes) 
                    ? $intendedUrl 
                    : route('bar-keeper.dashboard');
                return redirect($redirectUrl)->with('success', 'Welcome back, ' . $user->name . '!');
            } elseif ($userRole === 'housekeeper') {
                $redirectUrl = $intendedUrl && in_array($intendedUrl, $validDashboardRoutes) 
                    ? $intendedUrl 
                    : route('housekeeper.dashboard');
                return redirect($redirectUrl)->with('success', 'Welcome back, ' . $user->name . '!');
            } elseif ($userRole === 'head_chef') {
                $redirectUrl = $intendedUrl && in_array($intendedUrl, $validDashboardRoutes) 
                    ? $intendedUrl 
                    : route('chef-master.dashboard');
                return redirect($redirectUrl)->with('success', 'Welcome back, ' . $user->name . '!');
            } elseif ($userRole === 'waiter') {
                $redirectUrl = $intendedUrl && in_array($intendedUrl, $validDashboardRoutes) 
                    ? $intendedUrl 
                    : route('waiter.dashboard');
                return redirect($redirectUrl)->with('success', 'Welcome back, ' . $user->name . '!');
            } else {
                $redirectUrl = $intendedUrl && in_array($intendedUrl, $validDashboardRoutes) 
                    ? $intendedUrl 
                    : route('customer.dashboard');
                return redirect($redirectUrl)->with('success', 'Welcome back, ' . $user->name . '!');
            }

        } catch (\Exception $e) {
            \Log::error('Login error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'email' => $request->email ?? 'unknown',
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return back()->withErrors([
                'email' => 'An error occurred during login. Please try again. If the problem persists, contact support.',
            ])->withInput($request->only('email'));
        }
    }

    /**
     * Verify OTP and complete login
     */
    public function verifyOtp(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'otp' => 'required|string|size:6',
            ]);

            $email = $request->email;
            $otp = $request->otp;
            $ipAddress = $request->ip();

            \Log::info('OTP Verification Attempt', [
                'email' => $email,
                'otp_length' => strlen($otp),
                'ip' => $ipAddress
            ]);

            // Verify session data exists
            $sessionEmail = $request->session()->get('login_email');
            $remember = $request->session()->get('login_remember', false);
            $userType = $request->session()->get('login_user_type');
            $userId = $request->session()->get('login_user_id');

            \Log::info('Session Data Check', [
                'session_email' => $sessionEmail,
                'request_email' => $email,
                'user_type' => $userType,
                'user_id' => $userId,
                'emails_match' => $sessionEmail === $email
            ]);

            if (!$sessionEmail || $sessionEmail !== $email) {
                \Log::warning('Session mismatch in OTP verification', [
                    'session_email' => $sessionEmail,
                    'request_email' => $email
                ]);
                return back()->withErrors([
                    'otp' => 'Invalid session. Please start the login process again.'
                ])->withInput($request->only('email'));
            }

            if (!$userType || !$userId) {
                \Log::warning('Missing user type or ID in session', [
                    'user_type' => $userType,
                    'user_id' => $userId
                ]);
                return back()->withErrors([
                    'otp' => 'Session expired. Please start the login process again.'
                ])->withInput($request->only('email'));
            }

            // Verify OTP
            $loginOtp = LoginOtp::verify($email, $otp, $ipAddress);

            if (!$loginOtp) {
                \Log::warning('OTP verification failed', [
                    'email' => $email,
                    'otp' => $otp,
                    'ip' => $ipAddress
                ]);

                FailedLoginAttempt::record(
                    $email,
                    'invalid_otp',
                    $ipAddress,
                    $request->userAgent()
                );

                return back()->withErrors([
                    'otp' => 'Invalid or expired verification code. Please try again.'
                ])->withInput($request->only('email'))->with('show_otp', true);
            }

            \Log::info('OTP verified successfully', [
                'email' => $email,
                'user_type' => $userType,
                'user_id' => $userId
            ]);

            // Get user based on type
            $user = null;
            if ($userType === 'staff') {
                $user = Staff::find($userId);
            } elseif ($userType === 'guest') {
                $user = Guest::find($userId);
            }

            if (!$user) {
                \Log::error('User not found during OTP verification', [
                    'user_type' => $userType,
                    'user_id' => $userId
                ]);
                return back()->withErrors([
                    'otp' => 'User not found. Please start the login process again.'
                ])->withInput($request->only('email'));
            }

            // Check if user is still active
            if (!$user->is_active) {
                return back()->withErrors([
                    'otp' => 'Your account has been deactivated. Please contact administrator.'
                ])->withInput($request->only('email'));
            }

            // Log in the user
            if ($userType === 'staff') {
                Auth::guard('staff')->login($user, $remember);
            } else {
                Auth::guard('guest')->login($user, $remember);
            }

            // Regenerate session to prevent session fixation attacks
            $request->session()->regenerate();
            
            // Get the NEW session ID after regeneration
            $sessionId = $request->session()->getId();
            
            // Generate new session token
            $sessionToken = \Illuminate\Support\Str::random(60);
            
            // Update user with new session token and session ID
            $user->session_token = hash('sha256', $sessionToken);
            $user->last_session_id = $sessionId;
            $user->save();
            
            // Update sessions table with user_id
            DB::table('sessions')
                ->where('id', $sessionId)
                ->update([
                    'user_id' => $user->id,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            
            // Store session token in session
            $request->session()->put('user_session_token', $sessionToken);
            
            // Clear login session data
            $request->session()->forget(['login_email', 'login_remember', 'login_user_type', 'login_user_id']);

            // Get user role
            $userRole = $user->role ?? 'guest';
            $normalizedRole = strtolower(str_replace([' ', '_'], '', trim($userRole)));

            // Get intended URL
            $intendedUrl = $request->session()->pull('url.intended');
            $validDashboardRoutes = [
                        route('admin.dashboard'),
                        route('super_admin.dashboard'),
                        route('reception.dashboard'),
                        route('bar-keeper.dashboard'),
                        route('housekeeper.dashboard'),
                        route('housekeeper.dashboard'),
                        route('chef-master.dashboard'),
                        route('waiter.dashboard'),
                        route('customer.dashboard'),
                    ];
            
            if ($intendedUrl && !in_array($intendedUrl, $validDashboardRoutes)) {
                $isValidPath = str_contains($intendedUrl, '/admin/') || 
                               str_contains($intendedUrl, '/super-admin/') ||
                               str_contains($intendedUrl, '/reception/') || 
                               str_contains($intendedUrl, '/bar-keeper/') ||
                               str_contains($intendedUrl, '/chef-master/') ||
                               str_contains($intendedUrl, '/customer/');
                
                if (!$isValidPath || str_contains($intendedUrl, '/notifications/') || 
                    str_contains($intendedUrl, '/api/') || 
                    str_contains($intendedUrl, '/ajax/')) {
                    $intendedUrl = null;
                }
            }

            // Log login activity
            ActivityLog::create([
                'user_id' => $user->id,
                'user_type' => get_class($user),
                'action' => 'logged_in',
                'description' => "User logged in with OTP: {$user->name} ({$user->email})",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Redirect based on user role
            if ($normalizedRole === 'superadmin' || $userRole === 'super_admin' || strtolower($userRole) === 'super admin') {
                $redirectUrl = $intendedUrl && in_array($intendedUrl, $validDashboardRoutes) 
                    ? $intendedUrl 
                    : route('super_admin.dashboard');
                return redirect($redirectUrl)->with('success', 'Welcome back, ' . $user->name . '!');
            } elseif ($normalizedRole === 'manager' || $userRole === 'manager') {
                $redirectUrl = $intendedUrl && in_array($intendedUrl, $validDashboardRoutes) 
                    ? $intendedUrl 
                    : route('admin.dashboard');
                return redirect($redirectUrl)->with('success', 'Welcome back, ' . $user->name . '!');
            } elseif ($normalizedRole === 'reception' || $userRole === 'reception') {
                $redirectUrl = $intendedUrl && in_array($intendedUrl, $validDashboardRoutes) 
                    ? $intendedUrl 
                    : route('reception.dashboard');
                return redirect($redirectUrl)->with('success', 'Welcome back, ' . $user->name . '!');
            } elseif ($normalizedRole === 'barkeeper' || $normalizedRole === 'barkeepr' || $userRole === 'bar_keeper' || strtolower($userRole) === 'bar keeper' || strtolower($userRole) === 'bartender') {
                $redirectUrl = $intendedUrl && in_array($intendedUrl, $validDashboardRoutes) 
                    ? $intendedUrl 
                    : route('bar-keeper.dashboard');
                return redirect($redirectUrl)->with('success', 'Welcome back, ' . $user->name . '!');
            } elseif ($normalizedRole === 'housekeeper' || $userRole === 'housekeeper') {
                $redirectUrl = $intendedUrl && in_array($intendedUrl, $validDashboardRoutes) 
                    ? $intendedUrl 
                    : route('housekeeper.dashboard');
                return redirect($redirectUrl)->with('success', 'Welcome back, ' . $user->name . '!');
            } elseif ($normalizedRole === 'headchef' || $normalizedRole === 'head_chef') {
                $redirectUrl = $intendedUrl && in_array($intendedUrl, $validDashboardRoutes) 
                    ? $intendedUrl 
                    : route('chef-master.dashboard');
                return redirect($redirectUrl)->with('success', 'Welcome back, ' . $user->name . '!');
            } elseif ($normalizedRole === 'waiter') {
                $redirectUrl = $intendedUrl && in_array($intendedUrl, $validDashboardRoutes) 
                    ? $intendedUrl 
                    : route('waiter.dashboard');
                return redirect($redirectUrl)->with('success', 'Welcome back, ' . $user->name . '!');
            } else {
                $redirectUrl = $intendedUrl && in_array($intendedUrl, $validDashboardRoutes) 
                    ? $intendedUrl 
                    : route('customer.dashboard');
                return redirect($redirectUrl)->with('success', 'Welcome back, ' . $user->name . '!');
            }
        } catch (\Exception $e) {
            \Log::error('OTP verification error', [
                'error' => $e->getMessage(),
                'email' => $request->email ?? 'unknown',
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withErrors([
                'otp' => 'An error occurred during login: ' . $e->getMessage() . '. Please try again.'
            ])->withInput($request->only('email'))->with('show_otp', true);
        }
    }

    /**
     * Resend OTP
     */
    public function resendOtp(Request $request)
    {
        $email = $request->session()->get('login_email');
        $userType = $request->session()->get('login_user_type');
        $userId = $request->session()->get('login_user_id');

        if (!$email || !$userType || !$userId) {
            return back()->withErrors([
                'email' => 'Session expired. Please start the login process again.'
            ]);
        }

        // Get user
        $user = null;
        if ($userType === 'staff') {
            $user = Staff::find($userId);
        } elseif ($userType === 'guest') {
            $user = Guest::find($userId);
        }

        if (!$user || !$user->is_active) {
            return back()->withErrors([
                'email' => 'User not found or inactive.'
            ]);
        }

        try {
            $loginOtp = LoginOtp::createForPhone(
                $user->phone ?? $email, // Use phone if available, fallback to email (though SmsService needs digits)
                $userType,
                $userId,
                $request->ip(),
                $email
            );

            \Log::info('Resending OTP via SMS', [
                'email' => $email,
                'phone' => $user->phone,
                'otp' => $loginOtp->otp
            ]);

            if ($user->phone) {
                $smsService = app(SmsService::class);
                $message = "Your " . config('app.name') . " verification code is: " . $loginOtp->otp;
                $smsResult = $smsService->sendSms($user->phone, $message);
                
                if (!$smsResult['success']) {
                    \Log::error('SMS Resend failed', ['error' => $smsResult['error'] ?? 'Unknown error']);
                    return back()->withErrors([
                        'otp' => 'Failed to send verification code via SMS. Please contact support.'
                    ])->with('show_otp', true);
                }
                
                \Log::info('OTP resend SMS sent successfully', ['phone' => $user->phone]);
            } else {
                // Fallback to email if phone is missing? 
                // The user said "use sms instead of email", so maybe phone is mandatory now.
                \Log::warning('User has no phone number for SMS OTP', ['user_id' => $user->id]);
                return back()->withErrors([
                    'email' => 'No phone number associated with this account. Please contact administrator.'
                ]);
            }

            // Log to system logs with verification code in context
            SystemLog::log('info', "Login verification code resent to user: {$user->name} ({$email})", 'security', [
                'user_id' => $userId,
                'user_email' => $email,
                'user_type' => $userType,
                'user_name' => $user->name,
                'verification_code' => $loginOtp->otp,
                'action' => 'verification_code_resent',
                'expires_at' => $loginOtp->expires_at->toDateTimeString(),
            ]);

            return back()->with([
                'success' => 'A new verification code has been sent to your phone number via SMS.',
                'show_otp' => true
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to resend OTP', [
                'error' => $e->getMessage(),
                'email' => $email,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withErrors([
                'otp' => 'Failed to resend verification code: ' . $e->getMessage() . '. Please try again.'
            ])->with('show_otp', true);
        }
    }

    /**
     * Send login OTP via SMS
     */
    private function sendLoginOtpSms($user, $userType, $request)
    {
        if (!$user->phone) {
            return [
                'success' => false,
                'message' => 'Your account has no phone number associated. Please contact administrator.'
            ];
        }

        try {
            // Create OTP record for phone
            $loginOtp = LoginOtp::createForPhone(
                $user->phone,
                $userType,
                $user->id,
                $request->ip(),
                $user->email
            );

            // Log the OTP for debugging (remove in production)
            \Log::info('Generated Login OTP via SMS', [
                'user_id' => $user->id,
                'phone' => $user->phone,
                'otp' => $loginOtp->otp
            ]);

            // Send SMS
            $smsService = app(SmsService::class);
            $message = "Your " . config('app.name') . " verification code is: " . $loginOtp->otp;
            $smsResult = $smsService->sendSms($user->phone, $message);

            if (!$smsResult['success']) {
                \Log::error('Login OTP SMS failed', ['error' => $smsResult['error'] ?? 'Unknown error']);
                return [
                    'success' => false,
                    'message' => 'Failed to send verification code via SMS. Please try again.'
                ];
            }

            // Store session data for verification
            $request->session()->put('login_email', $user->email);
            $request->session()->put('login_remember', $request->has('remember'));
            $request->session()->put('login_user_type', $userType);
            $request->session()->put('login_user_id', $user->id);

            return [
                'success' => true,
                'message' => 'A verification code has been sent to your phone number.'
            ];
        } catch (\Exception $e) {
            \Log::error('Error in sendLoginOtpSms', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [
                'success' => false,
                'message' => 'An error occurred while sending the verification code.'
            ];
        }
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        // Log logout activity before clearing session
        $user = Auth::guard('staff')->user() ?? Auth::guard('guest')->user();
        if ($user) {
            ActivityLog::create([
                'user_id' => $user->id,
                'user_type' => get_class($user),
                'action' => 'logged_out',
                'description' => "User logged out: {$user->name} ({$user->email})",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
            
            // Clear session token from user record
            $user->session_token = null;
            $user->last_session_id = null;
            $user->save();
        }
        
        // Logout from both guards
        Auth::guard('staff')->logout();
        Auth::guard('guest')->logout();
        
        // Invalidate session
        $request->session()->invalidate();
        
        // Regenerate CSRF token
        $request->session()->regenerateToken();
        
        // Redirect to unified login page
        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }
}
