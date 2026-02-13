<?php

namespace App\Http\Controllers;

use App\Models\IssueReport;
use App\Models\Booking;
use App\Services\CurrencyExchangeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class IssueReportController extends Controller
{
    /**
     * Customer: View all their issue reports
     */
    public function customerIndex(Request $request)
    {
        $user = Auth::guard('guest')->user() ?? Auth::user();
        
        if (!$user) {
            abort(403, 'Unauthorized. Please log in.');
        }
        
        $query = IssueReport::where('user_id', $user->id)
            ->with(['booking.room', 'room'])
            ->orderBy('created_at', 'desc');
        
        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        $issues = $query->paginate(15);
        
        $stats = [
            'total' => IssueReport::where('user_id', $user->id)->count(),
            'pending' => IssueReport::where('user_id', $user->id)->where('status', 'pending')->count(),
            'in_progress' => IssueReport::where('user_id', $user->id)->where('status', 'in_progress')->count(),
            'resolved' => IssueReport::where('user_id', $user->id)->where('status', 'resolved')->count(),
        ];
        
        // Check if guest has any checked-in bookings
        $hasCheckedIn = Booking::where('guest_email', $user->email)
            ->where('check_in_status', 'checked_in')
            ->where('status', '!=', 'cancelled')
            ->exists();
        
        $currencyService = new CurrencyExchangeService();
        $exchangeRate = $currencyService->getUsdToTshRate();
        
        return view('dashboard.customer-issues', [
            'role' => 'customer',
            'userName' => $user->name ?? 'Guest User',
            'userRole' => 'Customer',
            'user' => $user,
            'issues' => $issues,
            'stats' => $stats,
            'exchangeRate' => $exchangeRate,
            'hasCheckedIn' => $hasCheckedIn,
        ]);
    }
    
    /**
     * Customer: View a single issue report
     */
    public function customerShow(IssueReport $issue)
    {
        $user = Auth::guard('guest')->user() ?? Auth::user();
        
        if (!$user) {
            abort(403, 'Unauthorized. Please log in.');
        }
        
        // Verify the issue belongs to the logged-in user
        if ($issue->user_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }
        
        $issue->load(['booking.room', 'room', 'user', 'guest']);
        
        $currencyService = new CurrencyExchangeService();
        $exchangeRate = $currencyService->getUsdToTshRate();
        
        return view('dashboard.customer-issue-show', [
            'role' => 'customer',
            'userName' => $user->name ?? 'Guest User',
            'userRole' => 'Customer',
            'issue' => $issue,
            'exchangeRate' => $exchangeRate,
        ]);
    }
    
    /**
     * Reception: View all issue reports
     */
    public function receptionIndex(Request $request)
    {
        $query = IssueReport::with(['user', 'guest', 'booking.room', 'room'])
            ->orderBy('created_at', 'desc');
        
        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        // Filter by priority
        if ($request->has('priority') && $request->priority) {
            $query->where('priority', $request->priority);
        }
        
        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }
        
        $issues = $query->paginate(20);
        
        $stats = [
            'total' => IssueReport::count(),
            'pending' => IssueReport::where('status', 'pending')->count(),
            'in_progress' => IssueReport::where('status', 'in_progress')->count(),
            'resolved' => IssueReport::where('status', 'resolved')->count(),
            'urgent' => IssueReport::where('priority', 'urgent')->where('status', '!=', 'resolved')->count(),
        ];
        
        $currencyService = new CurrencyExchangeService();
        $exchangeRate = $currencyService->getUsdToTshRate();
        
        return view('dashboard.reception-issues', [
            'role' => 'reception',
            'userName' => Auth::user()->name ?? 'Reception Staff',
            'userRole' => 'Reception',
            'issues' => $issues,
            'stats' => $stats,
            'exchangeRate' => $exchangeRate,
        ]);
    }
    
    /**
     * Reception: View a single issue report
     */
    public function receptionShow(IssueReport $issue)
    {
        $issue->load(['user', 'guest', 'booking.room', 'room']);
        
        $currencyService = new CurrencyExchangeService();
        $exchangeRate = $currencyService->getUsdToTshRate();
        
        return view('dashboard.reception-issue-show', [
            'role' => 'reception',
            'userName' => Auth::user()->name ?? 'Reception Staff',
            'userRole' => 'Reception',
            'issue' => $issue,
            'exchangeRate' => $exchangeRate,
        ]);
    }
    
    /**
     * Reception: Update issue status
     */
    public function receptionUpdate(Request $request, IssueReport $issue)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,resolved',
            'admin_notes' => 'nullable|string',
        ]);
        
        $oldStatus = $issue->status;
        $newStatus = $validated['status'];
        $updateData = [
            'status' => $newStatus,
        ];
        
        if (isset($validated['admin_notes'])) {
            $updateData['admin_notes'] = $validated['admin_notes'];
        }
        
        if ($newStatus === 'resolved' && !$issue->resolved_at) {
            $updateData['resolved_at'] = now();
        }
        
        $issue->update($updateData);
        
        // Prepare response data first (before slow operations)
        $responseData = [
            'success' => true,
            'message' => 'Issue updated successfully.',
            'issue' => $issue->fresh(['user', 'guest', 'booking.room', 'room']),
        ];
        
        // Send email notification if status changed (queue it to avoid blocking)
        if ($oldStatus !== $newStatus) {
            try {
                $reporter = $issue->getReporter();
                if ($reporter && $reporter->email) {
                    // Send email immediately
                    // Check if reporter has notifications enabled
                    if ($reporter instanceof \App\Models\Guest && $reporter->isNotificationEnabled('issue_report')) {
                        \Illuminate\Support\Facades\Mail::to($reporter->email)->send(
                            new \App\Mail\IssueStatusUpdateMail($issue->fresh(), $newStatus, $validated['admin_notes'] ?? null)
                        );
                    } elseif (!($reporter instanceof \App\Models\Guest)) {
                        // For non-guest users, send if they have notifications enabled
                        \Illuminate\Support\Facades\Mail::to($reporter->email)->send(
                            new \App\Mail\IssueStatusUpdateMail($issue->fresh(), $newStatus, $validated['admin_notes'] ?? null)
                        );
                    }
                }
            } catch (\Exception $e) {
                // If queue fails, try sending synchronously but don't block
                try {
                    $reporter = $issue->getReporter();
                    if ($reporter && $reporter->email) {
                        // Check if reporter has notifications enabled
                        if ($reporter instanceof \App\Models\Guest && $reporter->isNotificationEnabled('issue_report')) {
                            \Illuminate\Support\Facades\Mail::to($reporter->email)->send(
                                new \App\Mail\IssueStatusUpdateMail($issue->fresh(), $newStatus, $validated['admin_notes'] ?? null)
                            );
                        } elseif (!($reporter instanceof \App\Models\Guest)) {
                            \Illuminate\Support\Facades\Mail::to($reporter->email)->send(
                                new \App\Mail\IssueStatusUpdateMail($issue->fresh(), $newStatus, $validated['admin_notes'] ?? null)
                            );
                        }
                    }
                } catch (\Exception $e2) {
                    Log::error('Failed to send issue status update email: ' . $e2->getMessage());
                }
            }

            // Send email notification to managers and super admins when status changes
            try {
                $managersAndAdmins = \App\Models\Staff::whereIn('role', ['manager', 'super_admin'])
                    ->where('is_active', true)
                    ->get();
                
                $smsService = app(SmsService::class);
                foreach ($managersAndAdmins as $staff) {
                    // Email
                    if ($staff->isNotificationEnabled('issue_report')) {
                        try {
                            \Illuminate\Support\Facades\Mail::to($staff->email)
                                ->send(new \App\Mail\StaffIssueStatusUpdateMail($issue->fresh(), $newStatus, $validated['admin_notes'] ?? null));
                        } catch (\Exception $e) {
                            Log::error('Failed to send issue status update email to staff: ' . $staff->email . ' - ' . $e->getMessage());
                        }
                    }

                    // SMS for managers on high priority status changes
                    if ($staff->phone && in_array($issue->priority, ['high', 'urgent'])) {
                        try {
                            $smsMessage = "Issue Update: {$issue->subject} (Room: " . ($issue->room?->room_number ?? 'N/A') . ") is now " . strtoupper($newStatus);
                            $smsService->sendSms($staff->phone, $smsMessage);
                        } catch (\Exception $e) {
                            Log::error("Failed to send issue status update SMS to manager: " . $e->getMessage());
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::error('Failed to send issue status update emails to managers/admins: ' . $e->getMessage());
            }

            // Send SMS to Reporter if issue is resolved
            if ($newStatus === 'resolved') {
                try {
                    $reporter = $issue->getReporter();
                    if ($reporter && $reporter->phone) {
                        $smsService = app(SmsService::class);
                        $smsMessage = "Hello " . ($reporter->name ?? 'Guest') . ", your reported issue '{$issue->subject}' has been RESOLVED. Thank you for your patience!";
                        $smsService->sendSms($reporter->phone, $smsMessage);
                    }
                } catch (\Exception $e) {
                    Log::error("Failed to send resolution SMS to reporter: " . $e->getMessage());
                }
            }
        }
        
        // Create in-app notification for guest (do this after response if possible, but keep for now)
        try {
            $notificationType = 'issue_' . $newStatus;
            $notificationTitle = 'Issue ' . ucfirst(str_replace('_', ' ', $newStatus));
            $notificationMessage = 'Your issue "' . $issue->subject . '" status has been updated to ' . ucfirst(str_replace('_', ' ', $newStatus)) . '.';
            
            // Only create notification if status changed
            if ($oldStatus !== $newStatus) {
                \App\Models\Notification::updateOrCreate(
                    [
                        'user_id' => $issue->user_id,
                        'notifiable_id' => $issue->id,
                        'notifiable_type' => IssueReport::class,
                        'type' => $notificationType,
                    ],
                    [
                        'title' => $notificationTitle,
                        'message' => $notificationMessage,
                        'icon' => $newStatus === 'resolved' ? 'fa-check-circle' : ($newStatus === 'in_progress' ? 'fa-cog' : 'fa-clock-o'),
                        'color' => $newStatus === 'resolved' ? 'success' : ($newStatus === 'in_progress' ? 'info' : 'warning'),
                        'role' => 'customer',
                        'link' => route('customer.issues.show', $issue),
                        'is_read' => false,
                    ]
                );
            }
            
            // If issue is resolved, mark all related notifications for reception/admin as read
            if ($newStatus === 'resolved') {
                \App\Models\Notification::where('notifiable_type', IssueReport::class)
                    ->where('notifiable_id', $issue->id)
                    ->where('type', 'issue_report')
                    ->whereIn('role', ['reception', 'manager'])
                    ->update(['is_read' => true]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to create issue status notification: ' . $e->getMessage());
        }
        
        // Return response immediately
        return response()->json($responseData);
    }
    
    /**
     * Admin: View all issue reports
     */
    public function adminIndex(Request $request)
    {
        $query = IssueReport::with(['user', 'guest', 'booking.room', 'room'])
            ->orderBy('created_at', 'desc');
        
        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        // Filter by priority
        if ($request->has('priority') && $request->priority) {
            $query->where('priority', $request->priority);
        }
        
        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }
        
        $issues = $query->paginate(20);
        
        $stats = [
            'total' => IssueReport::count(),
            'pending' => IssueReport::where('status', 'pending')->count(),
            'in_progress' => IssueReport::where('status', 'in_progress')->count(),
            'resolved' => IssueReport::where('status', 'resolved')->count(),
            'urgent' => IssueReport::where('priority', 'urgent')->where('status', '!=', 'resolved')->count(),
        ];
        
        $currencyService = new CurrencyExchangeService();
        $exchangeRate = $currencyService->getUsdToTshRate();
        
        return view('dashboard.admin-issues', [
            'role' => 'manager',
            'userName' => Auth::user()->name ?? 'Manager',
            'userRole' => 'Manager',
            'issues' => $issues,
            'stats' => $stats,
            'exchangeRate' => $exchangeRate,
        ]);
    }
    
    /**
     * Admin: View a single issue report
     */
    public function adminShow(IssueReport $issue)
    {
        $issue->load(['user', 'guest', 'booking.room', 'room']);
        
        $currencyService = new CurrencyExchangeService();
        $exchangeRate = $currencyService->getUsdToTshRate();
        
        return view('dashboard.admin-issue-show', [
            'role' => 'manager',
            'userName' => Auth::user()->name ?? 'Manager',
            'userRole' => 'Manager',
            'issue' => $issue,
            'exchangeRate' => $exchangeRate,
        ]);
    }
    
    /**
     * Admin: Update issue status
     */
    public function adminUpdate(Request $request, IssueReport $issue)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,resolved',
            'admin_notes' => 'nullable|string',
        ]);
        
        $updateData = [
            'status' => $validated['status'],
        ];
        
        if (isset($validated['admin_notes'])) {
            $updateData['admin_notes'] = $validated['admin_notes'];
        }
        
        if ($validated['status'] === 'resolved' && !$issue->resolved_at) {
            $updateData['resolved_at'] = now();
            
            // Notify the guest
            try {
                \App\Models\Notification::create([
                    'user_id' => $issue->user_id,
                    'type' => 'issue_resolved',
                    'title' => 'Issue Resolved',
                    'message' => 'Your issue "' . $issue->subject . '" has been resolved.',
                    'icon' => 'fa-check-circle',
                    'color' => 'success',
                    'role' => 'customer',
                    'notifiable_id' => $issue->id,
                    'notifiable_type' => IssueReport::class,
                    'link' => route('customer.issues.show', $issue),
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to create resolution notification: ' . $e->getMessage());
            }
        }
        
        $issue->update($updateData);
        
        return response()->json([
            'success' => true,
            'message' => 'Issue updated successfully.',
            'issue' => $issue->fresh(['user', 'booking.room', 'room']),
        ]);
    }

    /**
     * Store a newly created issue report
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'booking_id' => 'nullable|exists:bookings,id',
                'room_id' => 'nullable|exists:rooms,id',
                'issue_type' => 'required|in:room_issue,service_issue,technical_issue,other',
                'priority' => 'required|in:low,medium,high,urgent',
                'subject' => 'required|string|max:255',
                'description' => 'required|string',
            ]);

            // Get active booking if booking_id is not provided
            $booking = null;
            $room = null;
            
            if ($validated['booking_id']) {
                $booking = Booking::findOrFail($validated['booking_id']);
                $room = $booking->room;
            } elseif ($validated['room_id']) {
                $room = \App\Models\Room::findOrFail($validated['room_id']);
            } else {
                // Try to get the user's active booking
                $user = Auth::guard('guest')->user() ?? Auth::user();
                if ($user) {
                    $activeBooking = Booking::where('guest_email', $user->email)
                        ->where('status', 'confirmed')
                        ->where('check_in_status', 'checked_in')
                        ->orderBy('check_in', 'desc')
                        ->first();
                    
                    if ($activeBooking) {
                        $booking = $activeBooking;
                        $room = $activeBooking->room;
                    }
                }
            }

            $user = Auth::guard('guest')->user() ?? Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Please log in.',
                ], 401);
            }

            $issueReport = IssueReport::create([
                'user_id' => $user->id,
                'booking_id' => $booking?->id,
                'room_id' => $room?->id ?? $validated['room_id'],
                'issue_type' => $validated['issue_type'],
                'priority' => $validated['priority'],
                'subject' => $validated['subject'],
                'description' => $validated['description'],
                'status' => 'pending',
            ]);

            // Create notification for admin and reception
            try {
                $notificationService = new \App\Services\NotificationService();
                
                // Notify manager
                \App\Models\Notification::create([
                    'type' => 'issue_report',
                    'title' => 'New Issue Reported',
                    'message' => ($user->name ?? 'Guest') . ' reported an issue: ' . $validated['subject'],
                    'icon' => 'fa-exclamation-triangle',
                    'color' => 'warning',
                    'role' => 'manager',
                    'notifiable_id' => $issueReport->id,
                    'notifiable_type' => IssueReport::class,
                    'link' => route('admin.issues.show', $issueReport),
                ]);
                
                // Notify reception
                \App\Models\Notification::create([
                    'type' => 'issue_report',
                    'title' => 'New Issue Reported',
                    'message' => ($user->name ?? 'Guest') . ' reported an issue: ' . $validated['subject'],
                    'icon' => 'fa-exclamation-triangle',
                    'color' => 'warning',
                    'role' => 'reception',
                    'notifiable_id' => $issueReport->id,
                    'notifiable_type' => IssueReport::class,
                    'link' => route('reception.issues.show', $issueReport),
                ]);
            } catch (\Exception $e) {
            } catch (\Exception $e) {
                Log::error('Failed to create issue report notification: ' . $e->getMessage());
            }

            // Send email and SMS notification to managers and super admins
            try {
                $managersAndAdmins = \App\Models\Staff::whereIn('role', ['manager', 'super_admin'])
                    ->where('is_active', true)
                    ->get();
                
                $smsService = app(SmsService::class);
                foreach ($managersAndAdmins as $staff) {
                    // Email
                    if ($staff->isNotificationEnabled('issue_report')) {
                        try {
                            \Illuminate\Support\Facades\Mail::to($staff->email)
                                ->send(new \App\Mail\StaffNewIssueReportMail($issueReport->fresh()->load(['booking.room', 'room', 'user'])));
                        } catch (\Exception $e) {
                            Log::error('Failed to send new issue report email to staff: ' . $staff->email . ' - ' . $e->getMessage());
                        }
                    }

                    // SMS for high/urgent priority
                    if ($staff->phone && in_array($validated['priority'], ['high', 'urgent'])) {
                        try {
                            $smsMessage = "URGENT ISSUE: " . ($user->name ?? 'Guest') . " reported: {$validated['subject']}. Priority: " . strtoupper($validated['priority']);
                            $smsService->sendSms($staff->phone, $smsMessage);
                        } catch (\Exception $e) {
                            Log::error("Failed to send issue report SMS to manager: " . $e->getMessage());
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::error('Failed to send new issue report notifications to managers/admins: ' . $e->getMessage());
            }

            // Send SMS notification to Reception for all issues
            try {
                $receptionStaff = \App\Models\Staff::where('role', 'reception')
                    ->where('is_active', true)
                    ->get();
                
                foreach ($receptionStaff as $staff) {
                    if ($staff->phone) {
                        try {
                            $smsService = app(SmsService::class);
                            $smsMessage = "New Issue Report: " . ($user->name ?? 'Guest') . " - {$validated['subject']} (Room: " . ($room?->room_number ?? 'N/A') . ")";
                            $smsService->sendSms($staff->phone, $smsMessage);
                        } catch (\Exception $e) {
                            Log::error("Failed to send issue report SMS to reception: " . $e->getMessage());
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::error('Failed to send issue report SMS to reception: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Issue reported successfully! Our team will address it shortly.',
                'issue' => $issueReport,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Please check the form and correct any errors.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Issue report error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'user' => Auth::guard('guest')->user()?->id ?? Auth::user()?->id,
            ]);
            
            // Provide more helpful error message in development
            $errorMessage = config('app.debug') 
                ? 'Error: ' . $e->getMessage() 
                : 'An error occurred while reporting the issue. Please try again.';
            
            return response()->json([
                'success' => false,
                'message' => $errorMessage,
            ], 500);
        }
    }
}
