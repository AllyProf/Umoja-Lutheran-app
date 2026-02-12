<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
use App\Models\Booking;
use App\Models\ServiceRequest;
use App\Models\IssueReport;
use App\Models\HousekeepingInventoryItem;

use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Paginator::useBootstrapFour();
        
        // Only force HTTPS if APP_URL uses HTTPS and we're in production
        // This prevents CSRF token mismatch when site is accessed via HTTP
        $appUrl = config('app.url');
        
        // Log the configuration for debugging (only in production with debug enabled)
        if (app()->environment('production') && config('app.debug')) {
            \Log::info('AppServiceProvider URL Scheme Configuration', [
                'app_env' => app()->environment(),
                'app_url' => $appUrl,
                'app_url_scheme' => $appUrl ? parse_url($appUrl, PHP_URL_SCHEME) : 'none',
                'session_secure_cookie' => config('session.secure'),
                'session_domain' => config('session.domain'),
            ]);
        }
        
        if (app()->environment('production') && $appUrl && str_starts_with($appUrl, 'https://')) {
            URL::forceScheme('https');
        } elseif (app()->environment('local')) {
            URL::forceScheme('http');
        }
        // If production but APP_URL is HTTP, don't force HTTPS (let it use HTTP)
        // This ensures CSRF tokens are generated with the correct protocol

        // Share sidebar badge counts for customer
        View::composer('dashboard.partials.sidebar-customer', function ($view) {
            $badges = [
                'notifications' => 0,
                'extensions' => 0,
                'payments' => 0,
                'issues' => 0,
                'service_requests' => 0,
            ];

            $user = Auth::guard('guest')->user();
            if ($user) {

                // Unread notifications count
                $badges['notifications'] = Notification::forUser($user)
                    ->unread()
                    ->count();

                // Pending extension requests
                $badges['extensions'] = Booking::where('guest_email', $user->email)
                    ->where('extension_status', 'pending')
                    ->count();

                // Outstanding payments (bookings with outstanding balance)
                $activeBookings = Booking::where('guest_email', $user->email)
                    ->where('status', 'confirmed')
                    ->whereIn('payment_status', ['partial', 'pending'])
                    ->count();
                $badges['payments'] = $activeBookings;

                // Unresolved issues
                $badges['issues'] = IssueReport::where('user_id', $user->id)
                    ->where('status', '!=', 'resolved')
                    ->count();

                // Pending service requests
                $badges['service_requests'] = ServiceRequest::whereHas('booking', function($query) use ($user) {
                        $query->where('guest_email', $user->email);
                    })
                    ->where('status', 'pending')
                    ->count();
            }

            $view->with('sidebarBadges', $badges);
        });

        // Share sidebar badge counts for admin
        View::composer('dashboard.partials.sidebar-admin', function ($view) {
            $badges = [
                'notifications' => 0,
                'extensions' => 0,
                'service_requests' => 0,
                'issues' => 0,
                'room_issues' => 0,
            ];

            $user = Auth::guard('staff')->user();
            if ($user && ($user->role === 'manager' || $user->role === 'super_admin')) {

                // Unread notifications count
                $badges['notifications'] = Notification::forUser($user)
                    ->unread()
                    ->count();

                // Pending extension requests
                $badges['extensions'] = Booking::where('extension_status', 'pending')
                    ->count();

                // Pending service requests
                $badges['service_requests'] = ServiceRequest::where('status', 'pending')
                    ->count();

                // Unresolved issues
                $badges['issues'] = IssueReport::where('status', '!=', 'resolved')
                    ->count();

                // Pending/In-Progress room issues
                $badges['room_issues'] = \App\Models\RoomIssue::whereIn('status', ['reported', 'in_progress'])
                    ->count();
            }

            $view->with('sidebarBadges', $badges);
        });

        // Share sidebar badge counts for reception
        View::composer('dashboard.partials.sidebar-reception', function ($view) {
            $badges = [
                'notifications' => 0,
                'extensions' => 0,
                'service_requests' => 0,
                'issues' => 0,
                'room_issues' => 0,
            ];

            $user = Auth::guard('staff')->user();
            if ($user && $user->role === 'reception') {

                // Unread notifications count
                $badges['notifications'] = Notification::forUser($user)
                    ->unread()
                    ->count();

                // Pending extension requests
                $badges['extensions'] = Booking::where('extension_status', 'pending')
                    ->count();

                // Pending service requests
                $badges['service_requests'] = ServiceRequest::where('status', 'pending')
                    ->count();

                // Unresolved issues (pending and in_progress)
                $badges['issues'] = IssueReport::whereIn('status', ['pending', 'in_progress'])
                    ->count();

                // Pending/In-Progress room issues
                $badges['room_issues'] = \App\Models\RoomIssue::whereIn('status', ['reported', 'in_progress'])
                    ->count();
            }

            $view->with('sidebarBadges', $badges);
        });

        // Share current user to all dashboard views for permission checking
        View::composer('dashboard.*', function ($view) {
            $user = Auth::guard('staff')->user() ?? Auth::guard('guest')->user();
            $view->with('currentUser', $user);
        });

        // Share notifications to dashboard layout
        View::composer('dashboard.layouts.app', function ($view) {
            $user = Auth::guard('staff')->user() ?? Auth::guard('guest')->user();
            
            if ($user) {
                // Get recent notifications (last 10)
                $notifications = Notification::forUser($user)
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
                    ->get();
                
                // Get unread count
                $unreadNotificationCount = Notification::forUser($user)
                    ->unread()
                    ->count();
                
                // Debug logging (remove in production)
                if (config('app.debug')) {
                    \Log::debug('Notification query for user', [
                        'user_id' => $user->id,
                        'user_role' => $user->role ?? 'N/A',
                        'unread_count' => $unreadNotificationCount,
                        'notifications_found' => $notifications->count(),
                        'notification_ids' => $notifications->pluck('id')->toArray()
                    ]);
                }
                
                // Determine user role for header dropdown
                $userRole = null;
                if ($user instanceof \App\Models\Staff) {
                    $rawRole = $user->role ?? '';
                    $normalizedRole = strtolower(str_replace([' ', '_'], '', trim($rawRole)));
                    
                    if ($normalizedRole === 'superadmin' || strtolower($rawRole) === 'super_admin' || strtolower($rawRole) === 'super admin') {
                        $userRole = 'super_admin';
                    } elseif ($normalizedRole === 'manager' || strtolower($rawRole) === 'manager') {
                        $userRole = 'manager';
                    } elseif ($normalizedRole === 'reception' || strtolower($rawRole) === 'reception') {
                        $userRole = 'reception';
                    } elseif ($normalizedRole === 'barkeeper' || $normalizedRole === 'barkeepr' || strtolower($rawRole) === 'bar_keeper' || strtolower($rawRole) === 'bar keeper' || strtolower($rawRole) === 'bartender') {
                        $userRole = 'bar_keeper';
                    } elseif ($normalizedRole === 'headchef' || strtolower($rawRole) === 'head_chef' || strtolower($rawRole) === 'head chef') {
                        $userRole = 'head_chef';
                    } elseif ($normalizedRole === 'housekeeper' || strtolower($rawRole) === 'housekeeper') {
                        $userRole = 'housekeeper';
                    }
                } elseif ($user instanceof \App\Models\Guest) {
                    $userRole = 'customer';
                }
                
                // Get low stock housekeeping items count
                $lowStockHousekeepingCount = 0;
                $lowStockHousekeepingItems = collect([]);
                
                if (in_array($userRole, ['housekeeper', 'manager', 'super_admin'])) {
                    $lowStockHousekeepingItems = HousekeepingInventoryItem::whereRaw('current_stock <= minimum_stock')->get();
                    $lowStockHousekeepingCount = $lowStockHousekeepingItems->count();
                }

                $view->with([
                    'notifications' => $notifications,
                    'unreadNotificationCount' => $unreadNotificationCount,
                    'role' => $userRole,
                    'userName' => $user->name ?? 'User',
                    'lowStockHousekeepingCount' => $lowStockHousekeepingCount,
                    'lowStockHousekeepingItems' => $lowStockHousekeepingItems,
                ]);
            } else {
                $view->with([
                    'notifications' => collect([]),
                    'unreadNotificationCount' => 0,
                    'role' => null,
                    'userName' => 'User',
                ]);
            }
        });
    }

    public function register()
    {

































        

    }
}

