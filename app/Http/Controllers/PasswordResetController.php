<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Staff;
use App\Models\Guest;
use App\Mail\PasswordResetMail;
use App\Models\ActivityLog;
use App\Models\SystemLog;
use App\Services\SmsService;

class PasswordResetController extends Controller
{
    /**
     * Handle forgot password request
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $email = $request->email;
        $user = null;
        $userType = null;

        // Check if email exists in Staff table
        $staff = Staff::where('email', $email)->first();
        if ($staff) {
            // Check if staff is active
            if (!$staff->is_active) {
                return back()->withErrors([
                    'email' => 'Your account has been deactivated. Please contact administrator.'
                ])->withInput($request->only('email'));
            }
            $user = $staff;
            $userType = 'staff';
        } else {
            // Check if email exists in Guest table
            $guest = Guest::where('email', $email)->first();
            if ($guest) {
                // Check if guest is active
                if (!$guest->is_active) {
                    return back()->withErrors([
                        'email' => 'Your account has been deactivated. Please contact administrator.'
                    ])->withInput($request->only('email'));
                }
                $user = $guest;
                $userType = 'guest';
            }
        }

        // Check if email exists
        if (!$user) {
            // Log the attempt for security monitoring
            Log::warning('Password reset requested for non-existent email', [
                'email' => $email,
                'ip_address' => $request->ip(),
            ]);
            
            // Return error message - email does not exist
            // Add flag to show forgot password form
            return back()->withErrors([
                'email' => 'This email address does not exist in our system.'
            ])->withInput($request->only('email'))->with('show_forgot_password', true);
        }

        // Generate a secure random password (8 characters: uppercase, lowercase, numbers)
        $newPassword = $this->generateSecurePassword();

        // Hash the password
        $hashedPassword = Hash::make($newPassword);

        // Update password in database
        $tableName = $userType === 'staff' ? 'staffs' : 'guests';
        $updated = DB::table($tableName)
            ->where('id', $user->id)
            ->update(['password' => $hashedPassword]);

        if ($updated === 0) {
            Log::error('Password reset failed - database update failed', [
                'user_id' => $user->id,
                'user_type' => $userType,
                'email' => $email,
            ]);

            return back()->withErrors([
                'email' => 'Failed to reset password. Please try again or contact support.'
            ])->withInput($request->only('email'))->with('show_forgot_password', true);
        }

        // Record password reset in password_resets table for tracking
        DB::table('password_resets')->insert([
            'email' => $email,
            'user_type' => $userType,
            'user_id' => $user->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Send SMS with new password
        try {
            if ($user->phone) {
                $smsService = app(SmsService::class);
                $message = "Hello {$user->name}, your password for " . config('app.name') . " has been reset. Your new password is: {$newPassword}";
                $smsResult = $smsService->sendSms($user->phone, $message);
                
                if (!$smsResult['success']) {
                    Log::error('Password reset SMS failed', ['error' => $smsResult['error'] ?? 'Unknown error']);
                    return back()->withErrors([
                        'email' => 'Password reset failed to send SMS. Please contact support.'
                    ])->withInput($request->only('email'))->with('show_forgot_password', true);
                }
                
                Log::info('Password reset SMS sent', [
                    'user_id' => $user->id,
                    'user_type' => $userType,
                    'email' => $email,
                    'phone' => $user->phone
                ]);

                // Log activity
                ActivityLog::create([
                    'user_id' => $user->id,
                    'user_type' => $userType === 'staff' ? Staff::class : Guest::class,
                    'action' => 'password_reset_requested',
                    'description' => "Password reset requested and new password sent via SMS: {$user->name} ({$user->phone})",
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);

                // Log to system logs with new password in context
                SystemLog::log('info', "Password reset requested and new password generated for user: {$user->name} ({$email})", 'security', [
                    'user_id' => $user->id,
                    'user_email' => $email,
                    'user_phone' => $user->phone,
                    'user_type' => $userType,
                    'user_name' => $user->name,
                    'new_password' => $newPassword,
                    'action' => 'password_reset_requested',
                ]);
            } else {
                Log::warning('User has no phone number for password reset SMS', ['user_id' => $user->id]);
                return back()->withErrors([
                    'email' => 'Your account has no phone number associated. Please contact administrator to reset your password.'
                ])->withInput($request->only('email'))->with('show_forgot_password', true);
            }

        } catch (\Exception $e) {
            Log::error('Failed to send password reset SMS', [
                'error' => $e->getMessage(),
                'email' => $email,
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withErrors([
                'email' => 'Password was reset but failed to send SMS. Please contact support.'
            ])->withInput($request->only('email'))->with('show_forgot_password', true);
        }

        return back()->with('success', 'A new password has been sent to your phone number via SMS. Please check your messages and use it to login.');
    }

    /**
     * Generate a secure random password
     * Format: 8 characters with uppercase, lowercase, and numbers
     */
    private function generateSecurePassword(): string
    {
        $uppercase = 'ABCDEFGHJKLMNPQRSTUVWXYZ'; // Exclude I and O for clarity
        $lowercase = 'abcdefghijkmnpqrstuvwxyz'; // Exclude l and o for clarity
        $numbers = '23456789'; // Exclude 0 and 1 for clarity
        
        $password = '';
        
        // Ensure at least one of each type
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        
        // Fill the rest randomly
        $all = $uppercase . $lowercase . $numbers;
        for ($i = strlen($password); $i < 8; $i++) {
            $password .= $all[random_int(0, strlen($all) - 1)];
        }
        
        // Shuffle to randomize position
        return str_shuffle($password);
    }
}

