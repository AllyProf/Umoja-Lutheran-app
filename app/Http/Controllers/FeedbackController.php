<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;
use App\Models\Feedback;

class FeedbackController extends Controller
{
    /**
     * Show feedback and reviews page
     */
    public function index()
    {
        $user = Auth::guard('guest')->user() ?? Auth::user();
        
        if (!$user) {
            abort(403, 'Unauthorized. Please log in.');
        }
        
        // Get completed bookings for feedback
        // Include bookings that are:
        // 1. Explicitly checked out (check_in_status = 'checked_out') - PRIMARY
        // 2. Status is 'completed'
        // 3. Check-out date has passed (for bookings that may not have been explicitly checked out)
        // Exclude bookings that already have feedback and cancelled bookings
        $bookingsWithFeedback = Feedback::pluck('booking_id')->toArray();
        
        // Get bookings - check both exact email match and case-insensitive match
        $bookings = Booking::where(function($query) use ($user) {
                // Match email exactly or case-insensitively
                $query->where('guest_email', $user->email)
                      ->orWhereRaw('LOWER(guest_email) = ?', [strtolower($user->email)]);
            })
            ->where('status', '!=', 'cancelled') // Exclude cancelled bookings
            ->where(function($query) {
                // Primary: Explicitly checked out (most important)
                $query->where('check_in_status', 'checked_out')
                      // OR status is completed
                      ->orWhere('status', 'completed')
                      // OR check-out date has passed (for bookings that may not have been explicitly checked out)
                      ->orWhere(function($q) {
                          $q->where('check_out', '<', now())
                            ->where('status', '!=', 'pending'); // Exclude pending bookings
                      });
            })
            ->whereNotIn('id', $bookingsWithFeedback) // Exclude bookings with existing feedback
            ->with('room')
            ->orderBy('check_out', 'desc')
            ->get(); // Use get() for dropdown
        
        // Log for debugging
        \Log::info('Feedback page - Found bookings for user', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_name' => $user->name,
            'bookings_count' => $bookings->count(),
            'bookings_with_feedback_count' => count($bookingsWithFeedback),
            'bookings' => $bookings->map(function($b) {
                return [
                    'id' => $b->id,
                    'reference' => $b->booking_reference,
                    'guest_email' => $b->guest_email,
                    'status' => $b->status,
                    'check_in_status' => $b->check_in_status,
                    'check_out' => $b->check_out ? $b->check_out->format('Y-m-d') : null,
                    'check_out_passed' => $b->check_out ? $b->check_out->lt(now()) : false,
                    'checked_out_at' => $b->checked_out_at ? $b->checked_out_at->format('Y-m-d H:i:s') : null,
                ];
            })->toArray(),
        ]);
        
        // Get user's submitted feedback
        $submittedFeedback = Feedback::where('guest_email', $user->email)
            ->with(['booking.room'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('dashboard.customer-feedback', [
            'role' => 'customer',
            'userName' => $user->name ?? 'Guest User',
            'userRole' => 'Customer',
            'user' => $user,
            'bookings' => $bookings,
            'submittedFeedback' => $submittedFeedback,
        ]);
    }

    /**
     * Submit feedback
     */
    public function submit(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'categories' => 'nullable|array',
        ]);

        $user = Auth::guard('guest')->user() ?? Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Please log in.',
            ], 401);
        }
        
        // Verify booking belongs to user and is completed/checked out
        $booking = Booking::where('id', $request->booking_id)
            ->where('guest_email', $user->email)
            ->where(function($query) {
                $query->where('check_in_status', 'checked_out')
                      ->orWhere('status', 'completed')
                      ->orWhere(function($q) {
                          $q->where('check_out', '<', now())
                            ->where('status', '!=', 'pending');
                      });
            })
            ->where('status', '!=', 'cancelled')
            ->firstOrFail();

        // Check if feedback already exists for this booking
        $existingFeedback = Feedback::where('booking_id', $request->booking_id)->first();
        
        if ($existingFeedback) {
            return response()->json([
                'success' => false,
                'message' => 'You have already submitted feedback for this booking.',
            ], 422);
        }

        // Store feedback
        Feedback::create([
            'booking_id' => $request->booking_id,
            'guest_name' => $booking->guest_name,
            'guest_email' => $booking->guest_email,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'categories' => $request->categories ?? [],
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Thank you for your feedback! We appreciate your input.',
        ]);
    }
}




