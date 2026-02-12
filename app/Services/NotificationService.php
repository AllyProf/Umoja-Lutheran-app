<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Booking;
use App\Models\ServiceRequest;
// Removed User import - now using Staff and Guest models
use Carbon\Carbon;
use Illuminate\Support\Str;

class NotificationService
{
    /**
     * Generate a relative URL from a route name
     */
    private function getRelativeUrl(string $routeName, array $parameters = []): string
    {
        $fullUrl = route($routeName, $parameters);
        // Extract just the path portion (remove protocol and domain)
        $parsed = parse_url($fullUrl);
        $path = $parsed['path'] ?? '/';
        // Add query string if present
        if (isset($parsed['query'])) {
            $path .= '?' . $parsed['query'];
        }
        return $path;
    }
    
    /**
     * Create a notification for new booking
     */
    public function createBookingNotification(Booking $booking): void
    {
        // Notify manager
        Notification::create([
            'type' => 'booking',
            'title' => 'New Booking Received',
            'message' => "New booking from {$booking->guest_name} for Room {$booking->room->room_number}",
            'icon' => 'fa-calendar-check-o',
            'color' => 'primary',
            'role' => 'manager',
            'notifiable_id' => $booking->id,
            'notifiable_type' => Booking::class,
            'link' => $this->getRelativeUrl('admin.bookings.index') . '?ref=' . $booking->booking_reference,
        ]);
        
        // Notify reception
        Notification::create([
            'type' => 'booking',
            'title' => 'New Booking Received',
            'message' => "New booking from {$booking->guest_name} for Room {$booking->room->room_number}",
            'icon' => 'fa-calendar-check-o',
            'color' => 'primary',
            'role' => 'reception',
            'notifiable_id' => $booking->id,
            'notifiable_type' => Booking::class,
            'link' => $this->getRelativeUrl('reception.bookings') . '?ref=' . $booking->booking_reference,
        ]);
    }

    /**
     * Create a notification for payment completion
     */
    public function createPaymentNotification(Booking $booking, $user = null): void
    {
        $message = "Payment completed for booking #{$booking->booking_reference} by {$booking->guest_name}";
        
        // Notify manager
        Notification::create([
            'type' => 'payment',
            'title' => 'Payment Completed',
            'message' => $message,
            'icon' => 'fa-check',
            'color' => 'success',
            'role' => 'manager',
            'notifiable_id' => $booking->id,
            'notifiable_type' => Booking::class,
            'link' => $this->getRelativeUrl('admin.bookings.index') . '?ref=' . $booking->booking_reference,
        ]);
        
        // Notify reception
        Notification::create([
            'type' => 'payment',
            'title' => 'Payment Completed',
            'message' => $message,
            'icon' => 'fa-check',
            'color' => 'success',
            'role' => 'reception',
            'notifiable_id' => $booking->id,
            'notifiable_type' => Booking::class,
            'link' => $this->getRelativeUrl('reception.bookings') . '?ref=' . $booking->booking_reference,
        ]);

        // Notify customer if they have an account
        if ($user && ($user->role === 'customer' || $user->role === 'guest')) {
            Notification::create([
                'type' => 'payment',
                'title' => 'Payment Completed',
                'message' => "Your payment for booking #{$booking->booking_reference} has been completed",
                'icon' => 'fa-check',
                'color' => 'success',
                'user_id' => $user->id,
                'role' => 'customer',
                'notifiable_id' => $booking->id,
                'notifiable_type' => Booking::class,
                'link' => route('customer.payments'),
            ]);
        }
    }

    /**
     * Create a notification for new service request (for reception)
     */
    public function createServiceRequestNotification(ServiceRequest $serviceRequest): void
    {
        $booking = $serviceRequest->booking;
        $service = $serviceRequest->service;
        
        Notification::create([
            'type' => 'service_request',
            'title' => 'New Service Request',
            'message' => "Service request for {$service->name} from {$booking->guest_name} (Room {$booking->room->room_number})",
            'icon' => 'fa-list',
            'color' => 'info',
            'role' => 'reception',
            'notifiable_id' => $serviceRequest->id,
            'notifiable_type' => ServiceRequest::class,
            'link' => $this->getRelativeUrl('reception.service-requests'),
        ]);
    }

    /**
     * Create a confirmation notification for customer when they submit a service request
     */
    public function createServiceRequestConfirmationNotification(ServiceRequest $serviceRequest, $user = null): void
    {
        if (!$user) {
            return;
        }

        $service = $serviceRequest->service;
        
        // All requests start as pending and require reception approval
        Notification::create([
            'type' => 'service_request',
            'title' => 'Service Request Submitted',
            'message' => "Your service request for {$service->name} has been submitted and is pending approval from reception. It will be added to your bill once approved.",
            'icon' => 'fa-clock-o',
            'color' => 'info',
            'user_id' => $user->id,
            'role' => 'customer',
            'notifiable_id' => $serviceRequest->id,
            'notifiable_type' => ServiceRequest::class,
            'link' => $this->getRelativeUrl('customer.dashboard'),
        ]);
    }

    /**
     * Create a notification when service request status is updated
     */
    public function createServiceRequestStatusUpdateNotification(ServiceRequest $serviceRequest, $user, string $status): void
    {
        $service = $serviceRequest->service;
        $messages = [
            'approved' => "Your service request for {$service->name} has been approved and added to your bill",
            'completed' => "Your service request for {$service->name} has been completed",
            'cancelled' => "Your service request for {$service->name} has been cancelled",
        ];
        
        $icons = [
            'approved' => 'fa-check',
            'completed' => 'fa-check-circle',
            'cancelled' => 'fa-times',
        ];
        
        $colors = [
            'approved' => 'success',
            'completed' => 'success',
            'cancelled' => 'warning',
        ];
        
        Notification::create([
            'type' => 'service_request',
            'title' => 'Service Request ' . ucfirst($status),
            'message' => $messages[$status] ?? "Your service request for {$service->name} status has been updated",
            'icon' => $icons[$status] ?? 'fa-info-circle',
            'color' => $colors[$status] ?? 'info',
            'user_id' => $user->id,
            'role' => 'customer',
            'notifiable_id' => $serviceRequest->id,
            'notifiable_type' => ServiceRequest::class,
            'link' => $this->getRelativeUrl('customer.dashboard'),
        ]);
    }

    /**
     * Create a notification for room maintenance
     */
    public function createMaintenanceNotification(int $roomId, string $roomNumber, string $message): void
    {
        foreach (['manager', 'reception'] as $role) {
            Notification::create([
                'type' => 'maintenance',
                'title' => 'Room Maintenance Required',
                'message' => "Room {$roomNumber}: {$message}",
                'icon' => 'fa-exclamation',
                'color' => 'danger',
                'role' => $role,
                'notifiable_id' => $roomId,
                'notifiable_type' => \App\Models\Room::class,
                'link' => $this->getRelativeUrl('admin.rooms.index'),
            ]);
        }
    }

    /**
     * Create notifications when a service catalog item is updated
     */
    public function createServiceCatalogUpdateNotification(\App\Models\ServiceCatalog $serviceCatalog, $editor, array $oldValues = []): void
    {
        $serviceName = $serviceCatalog->service_name;
        $editorName = $editor->name ?? 'Manager';
        
        // Determine which roles should be notified based on service type
        $serviceKey = strtolower($serviceCatalog->service_key ?? '');
        $rolesToNotify = ['reception']; // Reception always needs to know
        
        // Add department-specific roles based on service type
        if (str_contains($serviceKey, 'bar') || str_contains($serviceKey, 'drink') || str_contains($serviceKey, 'beverage')) {
            $rolesToNotify[] = 'bar_keeper';
        }
        
        if (str_contains($serviceKey, 'food') || str_contains($serviceKey, 'restaurant') || str_contains($serviceKey, 'kitchen') || str_contains($serviceKey, 'meal')) {
            $rolesToNotify[] = 'head_chef';
        }
        
        // Build change message
        $changes = [];
        if (isset($oldValues['service_name']) && $oldValues['service_name'] !== $serviceCatalog->service_name) {
            $changes[] = "name changed from '{$oldValues['service_name']}' to '{$serviceCatalog->service_name}'";
        }
        if (isset($oldValues['price_tanzanian']) && $oldValues['price_tanzanian'] != $serviceCatalog->price_tanzanian) {
            $changes[] = "Tanzanian price changed from " . number_format($oldValues['price_tanzanian'], 2) . " to " . number_format($serviceCatalog->price_tanzanian, 2);
        }
        if (isset($oldValues['price_international']) && $oldValues['price_international'] != $serviceCatalog->price_international) {
            $changes[] = "International price changed";
        }
        if (isset($oldValues['is_active']) && $oldValues['is_active'] != $serviceCatalog->is_active) {
            $status = $serviceCatalog->is_active ? 'activated' : 'deactivated';
            $changes[] = "service {$status}";
        }
        
        $changeMessage = !empty($changes) ? " Changes: " . implode(', ', $changes) : "";
        
        // Create notifications for each role
        foreach ($rolesToNotify as $role) {
            Notification::create([
                'type' => 'service_catalog',
                'title' => 'Service Updated',
                'message' => "Service '{$serviceName}' has been updated by {$editorName}.{$changeMessage}",
                'icon' => 'fa-edit',
                'color' => 'warning',
                'role' => $role,
                'notifiable_id' => $serviceCatalog->id,
                'notifiable_type' => \App\Models\ServiceCatalog::class,
                'link' => $this->getRelativeUrl('admin.service-catalog.index'),
            ]);
        }
    }

    /**
     * Get notifications for a user
     */
    public function getNotificationsForUser($user, int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return Notification::forUser($user)
            ->unread()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get unread notification count for a user
     */
    public function getUnreadCount($user): int
    {
        return Notification::forUser($user)
            ->unread()
            ->count();
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(int $notificationId, $user): bool
    {
        $notification = Notification::where('id', $notificationId)
            ->forUser($user)
            ->first();

        if ($notification) {
            $notification->markAsRead();
            return true;
        }

        return false;
    }

    /**
     * Mark all notifications as read for a user
     */
    public function markAllAsRead($user): int
    {
        return Notification::forUser($user)
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    /**
     * Get actionable notifications (notifications that require action)
     * Only returns unread notifications that need action
     */
    public function getActionableNotifications($user): \Illuminate\Database\Eloquent\Collection
    {
        // Define action-required types based on user role
        $actionRequiredTypes = [];
        
        // Get user role - handle both Staff and Guest models
        $userRole = $user->role ?? 'guest';
        
        if ($userRole === 'reception') {
            // Reception needs to act on: service requests (pending), new bookings, extension requests, issue reports
            $actionRequiredTypes = ['service_request', 'booking', 'extension_request', 'issue_report'];
        } elseif ($userRole === 'manager' || $userRole === 'super_admin') {
            // Admin needs to act on: new bookings, maintenance, extension requests, issue reports
            $actionRequiredTypes = ['booking', 'maintenance', 'extension_request', 'issue_report'];
        } elseif ($userRole === 'bar_keeper') {
            // Bar keeper needs to act on: booking notifications (for special requests), service requests
            $actionRequiredTypes = ['booking', 'service_request'];
        } elseif ($userRole === 'head_chef') {
            // Head chef needs to act on: booking notifications (for special requests), service requests
            $actionRequiredTypes = ['booking', 'service_request'];
        } elseif ($userRole === 'guest' || $userRole === 'customer') {
            // Customers see status updates as actionable so they pop up
            $actionRequiredTypes = ['service_request', 'extension_request', 'payment', 'issue_report'];
        }
        
        if (empty($actionRequiredTypes)) {
            return Notification::whereIn('type', [])->get();
        }
        
        $notifications = Notification::forUser($user)
            ->whereIn('type', $actionRequiredTypes)
            ->unread()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // Filter out notifications for resolved issues
        return $notifications->filter(function($notification) {
            // If it's an issue_report notification, check if the issue is resolved
            if ($notification->notifiable_type === \App\Models\IssueReport::class && $notification->notifiable_id) {
                $issue = \App\Models\IssueReport::find($notification->notifiable_id);
                if ($issue && $issue->status === 'resolved') {
                    return false; // Don't show resolved issues
                }
            }
            return true;
        });
    }
    
    /**
     * Mark notification as read when action is taken
     * This is called when a service request is approved/rejected, extension is handled, etc.
     */
    public function markNotificationAsReadByNotifiable(string $notifiableType, int $notifiableId, string $type, ?string $role = null): void
    {
        $query = Notification::where('notifiable_type', $notifiableType)
            ->where('notifiable_id', $notifiableId)
            ->where('type', $type)
            ->unread();
        
        if ($role) {
            $query->where('role', $role);
        }
        
        $query->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }
}

