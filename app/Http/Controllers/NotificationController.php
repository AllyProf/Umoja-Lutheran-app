<?php

namespace App\Http\Controllers;

use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead($id)
    {
        $user = auth()->guard('staff')->user() ?? auth()->guard('guest')->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }
        $success = $this->notificationService->markAsRead($id, $user);

        return response()->json([
            'success' => $success
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $user = auth()->guard('staff')->user() ?? auth()->guard('guest')->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }
        $count = $this->notificationService->markAllAsRead($user);

        return response()->json([
            'success' => true,
            'count' => $count
        ]);
    }

    /**
     * Get actionable notifications (notifications that require action)
     * This endpoint should only be accessed via AJAX, not as a direct browser request
     */
    public function getActionable(Request $request)
    {
        try {
            // If this is not an AJAX request, redirect to appropriate dashboard
            if (!$request->ajax() && !$request->wantsJson()) {
                $user = Auth::user();
                $userRole = $user->role ?? 'customer';
                
                if ($userRole === 'manager') {
                    return redirect()->route('admin.dashboard');
                } elseif ($userRole === 'reception') {
                    return redirect()->route('reception.dashboard');
                }
                return redirect()->route('customer.dashboard');
            }
            
            $user = auth()->guard('staff')->user() ?? auth()->guard('guest')->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }
            
            $notifications = $this->notificationService->getActionableNotifications($user);

            return response()->json([
                'success' => true,
                'notifications' => $notifications->map(function($notification) {
                    return [
                        'id' => $notification->id,
                        'type' => $notification->type,
                        'title' => $notification->title,
                        'message' => $notification->message,
                        'color' => $notification->color,
                        'link' => $notification->link,
                        'is_read' => $notification->is_read,
                    ];
                })
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching actionable notifications', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error fetching notifications',
                'notifications' => []
            ], 500);
        }
    }

    /**
     * Show notifications center page
     */
    public function index(Request $request)
    {
        $user = auth()->guard('staff')->user() ?? auth()->guard('guest')->user();
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Get all notifications for the user
        $query = \App\Models\Notification::forUser($user)
            ->orderBy('created_at', 'desc');
        
        // Filter by read status
        if ($request->has('filter')) {
            if ($request->filter === 'unread') {
                $query->unread();
            } elseif ($request->filter === 'read') {
                $query->where('is_read', true);
            }
        }
        
        // Filter by type
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }
        
        $notifications = $query->paginate(20);
        
        // Get statistics
        $stats = [
            'total' => \App\Models\Notification::forUser($user)->count(),
            'unread' => \App\Models\Notification::forUser($user)->unread()->count(),
            'read' => \App\Models\Notification::forUser($user)->where('is_read', true)->count(),
        ];
        
        return view('dashboard.customer-notifications', [
            'role' => 'customer',
            'userName' => $user->name ?? 'Guest User',
            'userRole' => 'Customer',
            'user' => $user,
            'notifications' => $notifications,
            'stats' => $stats,
        ]);
    }
}
