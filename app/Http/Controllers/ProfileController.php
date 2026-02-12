<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    /**
     * Show the user profile page
     */
    public function show()
    {
        // Check both guards (staff and guest) since the app uses custom guards
        $user = Auth::guard('staff')->user() ?? Auth::guard('guest')->user();
        
        if (!$user) {
            // If no user is found, redirect to login
            return redirect()->route('login')->with('error', 'Please login to access your profile.');
        }
        
        // Determine role: Staff has a role property, Guest is always 'guest'
        $rawRole = $user instanceof \App\Models\Staff ? ($user->role ?? 'manager') : 'guest';
        
        // Normalize role for sidebar compatibility
        $role = null;
        if ($user instanceof \App\Models\Staff) {
            $normalizedRole = strtolower(str_replace([' ', '_'], '', trim($rawRole)));
            if ($normalizedRole === 'superadmin' || $rawRole === 'super_admin' || strtolower($rawRole) === 'super admin') {
                $role = 'super_admin';
            } elseif ($normalizedRole === 'manager' || $rawRole === 'manager') {
                $role = 'manager';
            } elseif ($normalizedRole === 'reception' || $rawRole === 'reception') {
                $role = 'reception';
            } elseif ($normalizedRole === 'housekeeper' || $rawRole === 'housekeeper') {
                $role = 'housekeeper';
            } elseif ($normalizedRole === 'barkeeper' || $normalizedRole === 'barkeepr' || $rawRole === 'bar_keeper') {
                $role = 'bar_keeper';
            } elseif ($normalizedRole === 'headchef' || $rawRole === 'head_chef') {
                $role = 'head_chef';
            } else {
                $role = 'manager'; // Default fallback
            }
        } else {
            $role = 'customer'; // Guests see customer sidebar
        }
        
        return view('dashboard.page-user', [
            'user' => $user,
            'role' => $role,
            'userName' => $user->name ?? 'User',
            'userRole' => $user instanceof \App\Models\Staff ? ucfirst(str_replace('_', ' ', $role)) : 'Guest',
        ]);
    }

    /**
     * Update profile photo
     */
    public function updatePhoto(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Check both guards (staff and guest) since the app uses custom guards
        $user = Auth::guard('staff')->user() ?? Auth::guard('guest')->user();
        
        if (!$user) {
            return back()->withErrors(['error' => 'User not authenticated.'])->withInput();
        }

        // Delete old photo if exists
        if ($user->profile_photo) {
            // Delete from storage
            if (Storage::disk('public')->exists($user->profile_photo)) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            // Also delete from public directory if exists (Windows compatibility)
            $publicPath = public_path('storage/' . $user->profile_photo);
            if (file_exists($publicPath)) {
                @unlink($publicPath);
            }
        }

        // Store new photo
        $path = $request->file('profile_photo')->store('profile-photos', 'public');
        
        // Sync to public directory for Windows compatibility
        $this->syncFileToPublic($path);
        
        // Update user profile
        $user->update([
            'profile_photo' => $path
        ]);

        return back()->with('success', 'Profile photo updated successfully!');
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ], [
            'current_password.required' => 'Current password is required.',
            'new_password.required' => 'New password is required.',
            'new_password.min' => 'New password must be at least 8 characters.',
            'new_password.confirmed' => 'New password confirmation does not match.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Check both guards (staff and guest) since the app uses custom guards
        $user = Auth::guard('staff')->user() ?? Auth::guard('guest')->user();
        
        if (!$user) {
            return back()->withErrors(['error' => 'User not authenticated.'])->withInput();
        }

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.'])->withInput();
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return back()->with('success', 'Password updated successfully!');
    }

    /**
     * Update profile information (name, email)
     */
    public function updateProfile(Request $request)
    {
        // Check both guards (staff and guest) since the app uses custom guards
        $user = Auth::guard('staff')->user() ?? Auth::guard('guest')->user();
        
        if (!$user) {
            return back()->withErrors(['error' => 'User not authenticated.'])->withInput();
        }

        // Determine which table to use for email uniqueness check
        $table = $user instanceof \App\Models\Staff ? 'staffs' : 'guests';
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:' . $table . ',email,' . $user->id,
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return back()->with('success', 'Profile updated successfully!');
    }

    /**
     * Update guest preferences
     */
    public function updatePreferences(Request $request)
    {
        // Check both guards (staff and guest) since the app uses custom guards
        $user = Auth::guard('staff')->user() ?? Auth::guard('guest')->user();
        
        if (!$user) {
            return back()->withErrors(['error' => 'User not authenticated.'])->withInput();
        }

        $validator = Validator::make($request->all(), [
            'room_preferences' => 'nullable|array',
            'dietary_restrictions' => 'nullable|string|max:500',
            'special_occasions' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user->update([
            'room_preferences' => $request->room_preferences ?? [],
            'dietary_restrictions' => $request->dietary_restrictions,
            'special_occasions' => $request->special_occasions,
        ]);

        return back()->with('success', 'Preferences updated successfully!');
    }

    /**
     * Update notification preferences
     */
    public function updateNotificationPreferences(Request $request)
    {
        // Check both guards (staff and guest) since the app uses custom guards
        $user = Auth::guard('staff')->user() ?? Auth::guard('guest')->user();
        
        if (!$user) {
            return back()->withErrors(['error' => 'User not authenticated.'])->withInput();
        }

        $validator = Validator::make($request->all(), [
            'email_notifications_enabled' => 'nullable|boolean',
            'service_request_notifications' => 'nullable|boolean',
            'issue_report_notifications' => 'nullable|boolean',
            'booking_notifications' => 'nullable|boolean',
            'payment_notifications' => 'nullable|boolean',
            'check_in_out_notifications' => 'nullable|boolean',
            'extension_request_notifications' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Get current preferences or default
        $currentPrefs = $user->notification_preferences ?? [
            'email_notifications_enabled' => true,
            'service_request_notifications' => true,
            'issue_report_notifications' => true,
            'booking_notifications' => true,
            'payment_notifications' => true,
            'check_in_out_notifications' => true,
            'extension_request_notifications' => true,
        ];

        // Update preferences
        $preferences = [
            'email_notifications_enabled' => $request->has('email_notifications_enabled') ? (bool)$request->email_notifications_enabled : ($currentPrefs['email_notifications_enabled'] ?? true),
            'service_request_notifications' => $request->has('service_request_notifications') ? (bool)$request->service_request_notifications : ($currentPrefs['service_request_notifications'] ?? true),
            'issue_report_notifications' => $request->has('issue_report_notifications') ? (bool)$request->issue_report_notifications : ($currentPrefs['issue_report_notifications'] ?? true),
            'booking_notifications' => $request->has('booking_notifications') ? (bool)$request->booking_notifications : ($currentPrefs['booking_notifications'] ?? true),
            'payment_notifications' => $request->has('payment_notifications') ? (bool)$request->payment_notifications : ($currentPrefs['payment_notifications'] ?? true),
            'check_in_out_notifications' => $request->has('check_in_out_notifications') ? (bool)$request->check_in_out_notifications : ($currentPrefs['check_in_out_notifications'] ?? true),
            'extension_request_notifications' => $request->has('extension_request_notifications') ? (bool)$request->extension_request_notifications : ($currentPrefs['extension_request_notifications'] ?? true),
        ];

        $user->update([
            'notification_preferences' => $preferences
        ]);

        return back()->with('success', 'Notification preferences updated successfully!');
    }
    
    /**
     * Sync storage file to public directory (for Windows compatibility)
     * 
     * @param string $filePath Path relative to storage/app/public
     * @return bool
     */
    private function syncFileToPublic($filePath)
    {
        $storagePath = storage_path('app/public/' . $filePath);
        $publicPath = public_path('storage/' . $filePath);
        
        // Only sync if file exists in storage and doesn't exist in public
        if (file_exists($storagePath) && !file_exists($publicPath)) {
            // Create directory if it doesn't exist
            $publicDir = dirname($publicPath);
            if (!is_dir($publicDir)) {
                mkdir($publicDir, 0755, true);
            }
            
            // Copy the file
            return copy($storagePath, $publicPath);
        }
        
        return true;
    }
}
