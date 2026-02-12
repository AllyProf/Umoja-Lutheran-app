<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NewsletterController extends Controller
{
    /**
     * Subscribe to newsletter
     */
    public function subscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide a valid email address.',
            ], 422);
        }

        $email = $request->input('email');

        // Check if email already exists
        $existing = NewsletterSubscription::where('email', $email)->first();

        if ($existing) {
            if ($existing->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'This email is already subscribed to our newsletter.',
                ], 409);
            } else {
                // Reactivate subscription
                $existing->update([
                    'is_active' => true,
                    'subscribed_at' => now(),
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Thank you! Your subscription has been reactivated.',
                ]);
            }
        }

        // Create new subscription
        NewsletterSubscription::create([
            'email' => $email,
            'is_active' => true,
            'subscribed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Thank you for subscribing! You will receive our latest offers and news.',
        ]);
    }

    /**
     * Display newsletter subscriptions (Admin)
     */
    public function index(Request $request)
    {
        $query = NewsletterSubscription::query();

        // Filter by status
        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Search by email
        if ($request->has('search') && $request->search) {
            $query->where('email', 'like', '%' . $request->search . '%');
        }

        // Sort
        $sortBy = $request->get('sort_by', 'subscribed_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $subscriptions = $query->paginate(20);

        // Statistics
        $totalSubscriptions = NewsletterSubscription::count();
        $activeSubscriptions = NewsletterSubscription::where('is_active', true)->count();
        $inactiveSubscriptions = NewsletterSubscription::where('is_active', false)->count();
        $thisMonthSubscriptions = NewsletterSubscription::whereMonth('subscribed_at', now()->month)
            ->whereYear('subscribed_at', now()->year)
            ->count();

        return view('dashboard.admin-newsletter-subscriptions', [
            'subscriptions' => $subscriptions,
            'totalSubscriptions' => $totalSubscriptions,
            'activeSubscriptions' => $activeSubscriptions,
            'inactiveSubscriptions' => $inactiveSubscriptions,
            'thisMonthSubscriptions' => $thisMonthSubscriptions,
            'role' => 'manager',
            'userName' => auth()->guard('staff')->user()->name ?? 'Admin',
            'userRole' => 'Manager',
            'currentAuthUser' => auth()->guard('staff')->user() ?? auth()->guard('guest')->user(),
        ]);
    }

    /**
     * Toggle subscription status
     */
    public function toggleStatus($id)
    {
        $subscription = NewsletterSubscription::findOrFail($id);
        $subscription->is_active = !$subscription->is_active;
        $subscription->save();

        return redirect()->route('admin.newsletter.subscriptions')
            ->with('success', 'Subscription status updated successfully.');
    }

    /**
     * Delete subscription
     */
    public function destroy($id)
    {
        $subscription = NewsletterSubscription::findOrFail($id);
        $subscription->delete();

        return redirect()->route('admin.newsletter.subscriptions')
            ->with('success', 'Subscription deleted successfully.');
    }

    /**
     * Export subscriptions to CSV
     */
    public function export()
    {
        $subscriptions = NewsletterSubscription::where('is_active', true)
            ->orderBy('subscribed_at', 'desc')
            ->get();

        $filename = 'newsletter_subscriptions_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($subscriptions) {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, ['Email', 'Subscribed At', 'Status']);
            
            // Data rows
            foreach ($subscriptions as $subscription) {
                fputcsv($file, [
                    $subscription->email,
                    $subscription->subscribed_at->format('Y-m-d H:i:s'),
                    $subscription->is_active ? 'Active' : 'Inactive',
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
