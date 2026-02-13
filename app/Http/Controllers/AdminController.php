<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Booking;
use App\Models\Room;
use App\Models\ServiceRequest;
use App\Models\HotelSetting;
use App\Models\Feedback;
use App\Models\Service;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockTransfer;
use App\Models\StockReceipt;
use App\Services\CurrencyExchangeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * Display admin dashboard with statistics
     */
    public function dashboard()
    {
        try {
            $user = auth()->guard('staff')->user() ?? auth()->guard('guest')->user();
            
            // Redirect Head Chef to their specific dashboard
            if ($user && \App\Services\RolePermissionService::hasRole($user, 'head_chef')) {
                return redirect()->route('chef-master.dashboard');
            }
            
            // Redirect Bar Keeper to their specific dashboard
            if ($user && \App\Services\RolePermissionService::hasRole($user, 'bar_keeper')) {
                return redirect()->route('bar-keeper.dashboard');
            }

            // Get today's date range
            $today = Carbon::today();
            $thisMonth = Carbon::now()->startOfMonth();
            $lastMonth = Carbon::now()->subMonth()->startOfMonth();
            
            // Get exchange rate for currency conversion
            $exchangeRate = 2500; // Default fallback rate
            try {
                $currencyService = new CurrencyExchangeService();
                $exchangeRate = $currencyService->getUsdToTshRate();
            } catch (\Exception $e) {
                \Log::warning('Failed to get exchange rate, using default', ['error' => $e->getMessage()]);
                // Use default rate if service fails
            }
            
            // Calculate total revenue (bookings + service requests)
            // Include both paid and partial payments
            $totalBookingRevenueTZS = Booking::whereIn('payment_status', ['paid', 'partial'])
                ->whereNotNull('amount_paid')
                ->where('amount_paid', '>', 0)
                ->get()
                ->sum(function($booking) use ($exchangeRate) {
                    return ($booking->amount_paid ?? 0) * ($booking->locked_exchange_rate ?? $exchangeRate);
                });
            $totalServiceRevenueTZS = ServiceRequest::where('status', 'completed')->sum('total_price_tsh');
            
            // Calculate Day Services Revenue (Sum amount_paid or amount based on guest_type)
            $totalDayServiceRevenueTZS = \App\Models\DayService::where('payment_status', 'paid')->get()->sum(function($s) use ($exchangeRate) {
                // If amount_paid is null, fallback to amount. Guest type determines TZS or USD.
                $amount = $s->amount_paid ?? $s->amount ?? 0;
                return $s->guest_type === 'tanzanian' ? $amount : ($amount * ($s->exchange_rate ?? $exchangeRate));
            });

            $totalRevenueTZS = $totalBookingRevenueTZS + $totalServiceRevenueTZS + $totalDayServiceRevenueTZS;
            
            // Calculate today's revenue (use paid_at if available, otherwise created_at)
            $todayBookingRevenueTZS = Booking::whereIn('payment_status', ['paid', 'partial'])
                ->whereNotNull('amount_paid')
                ->where('amount_paid', '>', 0)
                ->where(function($q) use ($today) {
                    $q->whereDate('paid_at', $today)
                      ->orWhere(function($subQ) use ($today) {
                          $subQ->whereNull('paid_at')
                               ->whereDate('created_at', $today);
                      });
                })
                ->get()
                ->sum(function($booking) use ($exchangeRate) {
                    return ($booking->amount_paid ?? 0) * ($booking->locked_exchange_rate ?? $exchangeRate);
                });
            $todayServiceRevenueTZS = ServiceRequest::where('status', 'completed')
                ->whereDate('completed_at', $today)
                ->sum('total_price_tsh');

            $todayDayServiceRevenueTZS = \App\Models\DayService::where('payment_status', 'paid')
                ->whereDate('paid_at', $today)
                ->get()->sum(function($s) use ($exchangeRate) {
                    $amount = $s->amount_paid ?? $s->amount ?? 0;
                    return $s->guest_type === 'tanzanian' ? $amount : ($amount * ($s->exchange_rate ?? $exchangeRate));
                });

            $todayRevenueTZS = $todayBookingRevenueTZS + $todayServiceRevenueTZS + $todayDayServiceRevenueTZS;
            
            // Calculate this month's revenue
            $monthBookingRevenueTZS = Booking::whereIn('payment_status', ['paid', 'partial'])
                ->whereNotNull('amount_paid')
                ->where('amount_paid', '>', 0)
                ->where(function($q) use ($thisMonth) {
                    $q->where('paid_at', '>=', $thisMonth)
                      ->orWhere(function($subQ) use ($thisMonth) {
                          $subQ->whereNull('paid_at')
                               ->where('created_at', '>=', $thisMonth);
                      });
                })
                ->get()
                ->sum(function($booking) use ($exchangeRate) {
                    return ($booking->amount_paid ?? 0) * ($booking->locked_exchange_rate ?? $exchangeRate);
                });
            $monthServiceRevenueTZS = ServiceRequest::where('status', 'completed')
                ->where('completed_at', '>=', $thisMonth)
                ->sum('total_price_tsh');

            $monthDayServiceRevenueTZS = \App\Models\DayService::where('payment_status', 'paid')
                ->where('paid_at', '>=', $thisMonth)
                ->get()->sum(function($s) use ($exchangeRate) {
                    $amount = $s->amount_paid ?? $s->amount ?? 0;
                    return $s->guest_type === 'tanzanian' ? $amount : ($amount * ($s->exchange_rate ?? $exchangeRate));
                });

            $monthRevenueTZS = $monthBookingRevenueTZS + $monthServiceRevenueTZS + $monthDayServiceRevenueTZS;
            
            // Statistics
            $stats = [
                'total_users' => \App\Models\Staff::count() + \App\Models\Guest::count(),
                'total_rooms' => Room::count(),
                'total_bookings' => Booking::count(),
                'total_revenue' => $totalRevenueTZS,
                
                // Today's stats
                'today_bookings' => Booking::whereDate('created_at', $today)->count(),
                'today_revenue' => $todayRevenueTZS,
                
                // This month's stats
                'month_bookings' => Booking::where('created_at', '>=', $thisMonth)->count(),
                'month_revenue' => $monthRevenueTZS,
                
                // Booking status counts
                'pending_bookings' => Booking::where('status', 'pending')->count(),
                'confirmed_bookings' => Booking::where('status', 'confirmed')->count(),
                'cancelled_bookings' => Booking::where('status', 'cancelled')->count(),
                'completed_bookings' => Booking::where('status', 'completed')->count(),
                
                // Payment status (include partial payments as "paid" for counting purposes)
                'paid_bookings' => Booking::whereIn('payment_status', ['paid', 'partial'])
                    ->whereNotNull('amount_paid')
                    ->where('amount_paid', '>', 0)
                    ->count(),
                // Unpaid bookings: only pending payments (bookings with no payment received)
                'unpaid_bookings' => Booking::where('payment_status', 'pending')->count(),
                
                // Service requests
                'pending_requests' => ServiceRequest::where('status', 'pending')->count(),
                'approved_requests' => ServiceRequest::where('status', 'approved')->count(),
                
                // Purchase requests
                'pending_purchase_requests' => \App\Models\PurchaseRequest::where('status', 'pending')->count(),
                
                // Extension requests
                'pending_extensions' => Booking::where('extension_status', 'pending')->count(),
                
                // Total active guests (currently checked in)
                'active_guests' => Booking::where('check_in_status', 'checked_in')
                    ->where('check_out', '>=', $today)
                    ->sum('number_of_guests') ?: Booking::where('check_in_status', 'checked_in')->count(),
            ];
            
            // Recent bookings (include all, including expired - they'll be handled in the view)
            // Get recent bookings - group corporate bookings by company
            $allRecentBookings = Booking::with(['room', 'company'])
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get();
            
            // Separate corporate and individual bookings
            $corporateBookings = $allRecentBookings->where('is_corporate_booking', true);
            $individualBookings = $allRecentBookings->where('is_corporate_booking', false);
            
            // Group corporate bookings by company
            $groupedCorporateBookings = collect();
            $companyIds = $corporateBookings->whereNotNull('company_id')->pluck('company_id')->unique();
            
            foreach ($companyIds as $companyId) {
                $companyBookings = $corporateBookings->where('company_id', $companyId);
                if ($companyBookings->count() > 0) {
                    $groupedCorporateBookings->push([
                        'company' => $companyBookings->first()->company,
                        'bookings' => $companyBookings,
                        'first_booking' => $companyBookings->first(),
                        'is_grouped' => true,
                    ]);
                }
            }
            
            // Combine grouped corporate bookings with individual bookings, limit to 10
            $recentBookings = $groupedCorporateBookings->take(5)->merge($individualBookings->take(5))->take(10);
            
            // Get pending extension requests
            $pendingExtensions = Booking::where('extension_status', 'pending')
                ->with(['room'])
                ->orderBy('extension_requested_at', 'asc')
                ->get();
            
            // Revenue chart data (last 6 months) - in TZS
            $revenueData = [];
            for ($i = 5; $i >= 0; $i--) {
                $month = Carbon::now()->subMonths($i);
                $monthStart = $month->copy()->startOfMonth();
                $monthEnd = $month->copy()->endOfMonth();
                
                $monthBookingRevenueUSD = Booking::whereBetween('created_at', [$monthStart, $monthEnd])
                    ->where('payment_status', 'paid')
                    ->get()
                    ->sum(function($booking) {
                        return $booking->amount_paid ?? $booking->total_price ?? 0;
                    });
                $monthServiceRevenueTZS = ServiceRequest::where('status', 'completed')
                    ->whereBetween('completed_at', [$monthStart, $monthEnd])
                    ->sum('total_price_tsh');

                $monthDayServiceRevenueTZS = \App\Models\DayService::where('payment_status', 'paid')
                    ->whereBetween('paid_at', [$monthStart, $monthEnd])
                    ->get()->sum(function($s) use ($exchangeRate) {
                        $amount = $s->amount_paid ?? $s->amount ?? 0;
                        return $s->guest_type === 'tanzanian' ? $amount : ($amount * ($s->exchange_rate ?? $exchangeRate));
                    });

                $monthRevenueTZS = ($monthBookingRevenueUSD * $exchangeRate) + $monthServiceRevenueTZS + $monthDayServiceRevenueTZS;
                
                $revenueData[] = [
                    'month' => $month->format('M Y'),
                    'revenue' => $monthRevenueTZS
                ];
            }
            
            // Booking status chart data
            $bookingStatusData = [
                'Pending' => Booking::where('status', 'pending')->count(),
                'Confirmed' => Booking::where('status', 'confirmed')->count(),
                'Completed' => Booking::where('status', 'completed')->count(),
                'Cancelled' => Booking::where('status', 'cancelled')->count(),
            ];
            
            return view('dashboard.index', [
                'role' => 'manager',
                'userName' => $user->name ?? 'Manager',
                'userRole' => 'Manager',
                'stats' => $stats,
                'recentBookings' => $recentBookings,
                'exchangeRate' => $exchangeRate,
                'pendingExtensions' => $pendingExtensions,
                'revenueData' => $revenueData,
                'bookingStatusData' => $bookingStatusData,
            ]);
        } catch (\Exception $e) {
            \Log::error('Admin dashboard error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            // Return error view with minimal data
            $user = auth()->guard('staff')->user() ?? auth()->guard('guest')->user();
            return view('dashboard.index', [
                'role' => 'manager',
                'userName' => $user->name ?? 'Manager',
                'userRole' => 'Manager',
                'stats' => [],
                'recentBookings' => collect(),
                'exchangeRate' => 2500,
                'pendingExtensions' => collect(),
                'revenueData' => [],
                'bookingStatusData' => [],
                'error' => 'An error occurred while loading the dashboard. Please check the logs.',
            ]);
        }
    }
    
    /**
     * Show extension requests page
     */
    public function extensionRequests(Request $request)
    {
        $query = \App\Models\Booking::with('room')
            ->whereNotNull('extension_status')
            ->orderBy('extension_requested_at', 'desc');

        // Filter by extension status
        if ($request->has('status') && $request->status) {
            $query->where('extension_status', $request->status);
        }

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('booking_reference', 'like', "%{$search}%")
                  ->orWhere('guest_name', 'like', "%{$search}%")
                  ->orWhere('guest_email', 'like', "%{$search}%");
            });
        }

        $extensions = $query->paginate(20);

        // Statistics
        $stats = [
            'pending' => \App\Models\Booking::where('extension_status', 'pending')->count(),
            'approved' => \App\Models\Booking::where('extension_status', 'approved')->count(),
            'rejected' => \App\Models\Booking::where('extension_status', 'rejected')->count(),
            'total' => \App\Models\Booking::whereNotNull('extension_status')->count(),
        ];

        $currencyService = new \App\Services\CurrencyExchangeService();
        $exchangeRate = $currencyService->getUsdToTshRate();

        return view('dashboard.admin-extension-requests', [
            'role' => 'manager',
            'userName' => (auth()->guard('staff')->user() ?? auth()->guard('guest')->user())->name ?? 'Manager',
            'userRole' => 'Manager',
            'extensions' => $extensions,
            'stats' => $stats,
            'exchangeRate' => $exchangeRate,
        ]);
    }
    
    /**
     * Display all users (employees and customers)
     */
    public function users(Request $request)
    {
        $tab = $request->get('tab', 'employees'); // Default to employees tab
        
        // Get employees (staff)
        $employeesQuery = \App\Models\Staff::query();
        
        // Filter employees
        if ($request->has('search_employee') && $request->search_employee) {
            $search = $request->search_employee;
            $employeesQuery->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('role', 'like', "%{$search}%");
            });
        }
        
        // Get customers (guests)
        $customersQuery = \App\Models\Guest::query();
        
        // Filter customers
        if ($request->has('search_customer') && $request->search_customer) {
            $search = $request->search_customer;
            $customersQuery->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        // Get employees with pagination
        $employees = $employeesQuery->orderBy('created_at', 'desc')->paginate(5, ['*'], 'employees_page');
        
        // Get customers with booking statistics
        $customers = $customersQuery->orderBy('created_at', 'desc')->paginate(5, ['*'], 'customers_page');
        
        // Get exchange rate for currency conversion
        $currencyService = new CurrencyExchangeService();
        $exchangeRate = $currencyService->getUsdToTshRate();
        
        // Calculate customer statistics (include partial payments)
        foreach ($customers as $customer) {
            // Match bookings by email first, then by name as fallback
            $customerBookings = Booking::where(function($query) use ($customer) {
                $query->where('guest_email', $customer->email)
                      ->orWhere('guest_name', 'like', '%' . $customer->name . '%');
            })->get();
            
            // Total spent: include both paid and partial payments (convert USD to TZS)
            $paidBookings = $customerBookings->where('payment_status', 'paid');
            $partialBookings = $customerBookings->where('payment_status', 'partial');
            $totalSpentUSD = $paidBookings->sum(function($booking) {
                return $booking->amount_paid ?? $booking->total_price ?? 0;
            }) + $partialBookings->sum(function($booking) {
                return $booking->amount_paid ?? 0;
            });
            $customer->total_spent = $totalSpentUSD * $exchangeRate;
            
            // Total bookings count
            $customer->total_bookings = $customerBookings->count();
            
            // Paid bookings: include both paid and partial (any booking with payment received)
            $customer->paid_bookings = $customerBookings->whereIn('payment_status', ['paid', 'partial'])
                ->filter(function($booking) {
                    return ($booking->amount_paid ?? 0) > 0;
                })
                ->count();
            
            // First and last booking
            $lastBooking = $customerBookings->sortByDesc('created_at')->first();
            $customer->last_booking = $lastBooking;
            $customer->first_booking = $customerBookings->sortBy('created_at')->first();
            
            // Set company if it was a corporate booking
            if ($lastBooking && $lastBooking->is_corporate_booking && $lastBooking->company_id) {
                $customer->company = $lastBooking->company;
            } else {
                $customer->company = null;
            }
        }
        
        $stats = [
            'total' => \App\Models\Staff::count() + \App\Models\Guest::count(),
            'managers' => \App\Models\Staff::where('role', 'manager')->count(),
            'reception' => \App\Models\Staff::where('role', 'reception')->count(),
            'guests' => \App\Models\Guest::whereHas('bookings')->count(),
            'employees' => \App\Models\Staff::whereIn('role', ['manager', 'reception'])->count(),
        ];
        
        return view('dashboard.admin-users', [
            'role' => 'manager',
            'userName' => (auth()->guard('staff')->user() ?? auth()->guard('guest')->user())->name ?? 'Manager',
            'userRole' => 'Manager',
            'employees' => $employees,
            'guests' => $customers,
            'stats' => $stats,
            'tab' => $tab,
            'filters' => $request->only(['search_employee', 'search_customer']),
        ]);
    }
    
    /**
     * Display all payments
     */
    public function payments(Request $request)
    {
        // Show all bookings with payments (paid or partial)
        $query = Booking::with('room')
            ->whereIn('payment_status', ['paid', 'partial'])
            ->whereNotNull('amount_paid')
            ->where('amount_paid', '>', 0)
            ->orderByRaw('COALESCE(paid_at, created_at) DESC');

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('booking_reference', 'like', "%{$search}%")
                  ->orWhere('guest_name', 'like', "%{$search}%")
                  ->orWhere('guest_email', 'like', "%{$search}%");
            });
        }

        // Filter by payment method
        if ($request->has('payment_method') && $request->payment_method) {
            $query->where('payment_method', $request->payment_method);
        }

        // Filter by payment status
        if ($request->has('payment_status') && $request->payment_status && $request->payment_status !== 'all') {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by date range (use paid_at if available, otherwise created_at)
        if ($request->has('date_from') && $request->date_from) {
            $query->where(function($q) use ($request) {
                $q->whereDate('paid_at', '>=', $request->date_from)
                  ->orWhere(function($subQ) use ($request) {
                      $subQ->whereNull('paid_at')
                           ->whereDate('created_at', '>=', $request->date_from);
                  });
            });
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->where(function($q) use ($request) {
                $q->whereDate('paid_at', '<=', $request->date_to)
                  ->orWhere(function($subQ) use ($request) {
                      $subQ->whereNull('paid_at')
                           ->whereDate('created_at', '<=', $request->date_to);
                  });
            });
        }

        $payments = $query->paginate(20);

        $currencyService = new CurrencyExchangeService();
        $exchangeRate = $currencyService->getUsdToTshRate();

        // Calculate statistics (include both paid and partial payments)
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        
        // Total revenue (all time) - include partial payments
        $bookingRevenueTZS = Booking::whereIn('payment_status', ['paid', 'partial'])
            ->whereNotNull('amount_paid')
            ->where('amount_paid', '>', 0)
            ->get()
            ->sum(function($booking) use ($exchangeRate) {
                return ($booking->amount_paid ?? 0) * ($booking->locked_exchange_rate ?? $exchangeRate);
            });
        
        // Add all-time Service Revenue
        $serviceRevenueTZS = ServiceRequest::where('status', 'completed')->sum('total_price_tsh');

        // Add all-time Day Service Revenue
        $dayServiceRevenueTZS = \App\Models\DayService::where('payment_status', 'paid')->get()->sum(function($s) use ($exchangeRate) {
            $amount = $s->amount_paid ?? $s->amount ?? 0;
            return $s->guest_type === 'tanzanian' ? $amount : ($amount * ($s->exchange_rate ?? $exchangeRate));
        });

        $totalRevenueTZS = $bookingRevenueTZS + $serviceRevenueTZS + $dayServiceRevenueTZS;
        $totalRevenueUSD = $exchangeRate > 0 ? ($totalRevenueTZS / $exchangeRate) : 0;
        
        // Today's revenue
        $todayBookingTZS = Booking::whereIn('payment_status', ['paid', 'partial'])
            ->whereNotNull('amount_paid')
            ->where('amount_paid', '>', 0)
            ->where(function($q) use ($today) {
                $q->whereDate('paid_at', $today)
                  ->orWhere(function($subQ) use ($today) {
                      $subQ->whereNull('paid_at')
                           ->whereDate('created_at', $today);
                  });
            })
            ->get()
            ->sum(function($booking) use ($exchangeRate) {
                return ($booking->amount_paid ?? 0) * ($booking->locked_exchange_rate ?? $exchangeRate);
            });
            
        $todayServiceTZS = ServiceRequest::where('status', 'completed')
            ->whereDate('completed_at', $today)
            ->sum('total_price_tsh');

        $todayDayServiceTZS = \App\Models\DayService::where('payment_status', 'paid')
            ->whereDate('paid_at', $today)
            ->get()->sum(function($s) use ($exchangeRate) {
                $amount = $s->amount_paid ?? $s->amount ?? 0;
                return $s->guest_type === 'tanzanian' ? $amount : ($amount * ($s->exchange_rate ?? $exchangeRate));
            });

        $todayRevenueTZS = $todayBookingTZS + $todayServiceTZS + $todayDayServiceTZS;
        $todayRevenueUSD = $exchangeRate > 0 ? ($todayRevenueTZS / $exchangeRate) : 0;
        
        // This month's revenue
        $monthBookingTZS = Booking::whereIn('payment_status', ['paid', 'partial'])
            ->whereNotNull('amount_paid')
            ->where('amount_paid', '>', 0)
            ->where(function($q) use ($thisMonth) {
                $q->where('paid_at', '>=', $thisMonth)
                  ->orWhere(function($subQ) use ($thisMonth) {
                      $subQ->whereNull('paid_at')
                           ->where('created_at', '>=', $thisMonth);
                  });
            })
            ->get()
            ->sum(function($booking) use ($exchangeRate) {
                return ($booking->amount_paid ?? 0) * ($booking->locked_exchange_rate ?? $exchangeRate);
            });

        $monthServiceTZS = ServiceRequest::where('status', 'completed')
            ->where('completed_at', '>=', $thisMonth)
            ->sum('total_price_tsh');

        $monthDayServiceTZS = \App\Models\DayService::where('payment_status', 'paid')
            ->where('paid_at', '>=', $thisMonth)
            ->get()->sum(function($s) use ($exchangeRate) {
                $amount = $s->amount_paid ?? $s->amount ?? 0;
                return $s->guest_type === 'tanzanian' ? $amount : ($amount * ($s->exchange_rate ?? $exchangeRate));
            });

        $monthRevenueTZS = $monthBookingTZS + $monthServiceTZS + $monthDayServiceTZS;
        $monthRevenueUSD = $exchangeRate > 0 ? ($monthRevenueTZS / $exchangeRate) : 0;
        
        // Total payments count
        $totalPayments = Booking::whereIn('payment_status', ['paid', 'partial'])
            ->whereNotNull('amount_paid')
            ->where('amount_paid', '>', 0)
            ->count();
        
        // Pending and partial payment stats
        $pendingPayments = Booking::where('payment_status', 'pending')
            ->whereNotNull('total_price')
            ->count();
        $pendingAmount = Booking::where('payment_status', 'pending')
            ->sum('total_price');
        $partialPayments = Booking::where('payment_status', 'partial')
            ->whereNotNull('amount_paid')
            ->where('amount_paid', '>', 0)
            ->count();
        $partialAmount = Booking::where('payment_status', 'partial')
            ->get()
            ->sum(function($booking) {
                return ($booking->total_price ?? 0) - ($booking->amount_paid ?? 0);
            });

        $stats = [
            'total_revenue' => $totalRevenueTZS,
            'today_revenue' => $todayRevenueTZS,
            'month_revenue' => $monthRevenueTZS,
            'total_payments' => $totalPayments,
            'pending_payments' => $pendingPayments,
            'pending_amount' => $pendingAmount,
            'partial_payments' => $partialPayments,
            'partial_amount' => $partialAmount,
        ];

        return view('dashboard.admin-payments', [
            'role' => 'manager',
            'userName' => (auth()->guard('staff')->user() ?? auth()->guard('guest')->user())->name ?? 'Manager',
            'userRole' => 'Manager',
            'payments' => $payments,
            'exchangeRate' => $exchangeRate,
            'totalRevenueUSD' => $totalRevenueUSD,
            'totalRevenueTZS' => $totalRevenueTZS,
            'stats' => $stats,
            'filters' => $request->only(['search', 'payment_method', 'payment_status', 'date_from', 'date_to']),
        ]);
    }
    
    /**
     * Display payment reports
     */
    public function paymentReports(Request $request)
    {
        $currencyService = new CurrencyExchangeService();
        $exchangeRate = $currencyService->getUsdToTshRate();

        // Get date range (default to last 30 days)
        $startDate = $request->get('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        // Get payments in date range (include both paid and partial payments)
        // Convert dates to Carbon instances for proper comparison
        $startDateCarbon = Carbon::parse($startDate)->startOfDay();
        $endDateCarbon = Carbon::parse($endDate)->endOfDay();
        
        $payments = Booking::whereIn('payment_status', ['paid', 'partial'])
            ->whereNotNull('amount_paid')
            ->where('amount_paid', '>', 0)
            ->where(function($q) use ($startDateCarbon, $endDateCarbon) {
                // Use paid_at if available, otherwise created_at
                $q->where(function($subQ) use ($startDateCarbon, $endDateCarbon) {
                    $subQ->whereNotNull('paid_at')
                         ->whereDate('paid_at', '>=', $startDateCarbon)
                         ->whereDate('paid_at', '<=', $endDateCarbon);
                })->orWhere(function($subQ) use ($startDateCarbon, $endDateCarbon) {
                    $subQ->whereNull('paid_at')
                         ->whereDate('created_at', '>=', $startDateCarbon)
                         ->whereDate('created_at', '<=', $endDateCarbon);
                });
            })
            ->with('room')
            ->orderByRaw('COALESCE(paid_at, created_at) DESC')
            ->get();

        // Calculate statistics
        $totalRevenueUSD = $payments->sum(function($booking) {
            return $booking->amount_paid ?? $booking->total_price ?? 0;
        });
        $totalRevenueTZS = $totalRevenueUSD * $exchangeRate;
        $totalPayments = $payments->count();
        $averagePayment = $totalPayments > 0 ? $totalRevenueTZS / $totalPayments : 0;
        
        $stats = [
            'total_payments' => $totalPayments,
            'total_revenue' => $totalRevenueTZS,
            'total_revenue_usd' => $totalRevenueUSD,
            'total_revenue_tzs' => $totalRevenueTZS,
            'average_payment' => $averagePayment,
            'paypal_payments' => $payments->where('payment_method', 'paypal')->count(),
            'cash_payments' => $payments->where('payment_method', 'cash')->count(),
            'manual_payments' => $payments->where('payment_method', 'manual')->count(),
        ];

        // Calculate daily revenue for chart (use paid_at if available, otherwise created_at)
        $dailyRevenue = $payments->groupBy(function($payment) {
            $date = $payment->paid_at ?? $payment->created_at;
            return Carbon::parse($date)->format('M d');
        })->map(function($dayPayments) use ($exchangeRate) {
            return round($dayPayments->sum(function($payment) use ($exchangeRate) {
                return ($payment->amount_paid ?? $payment->total_price ?? 0) * $exchangeRate;
            }), 0);
        });

        return view('dashboard.admin-payment-reports', [
            'role' => 'manager',
            'userName' => (auth()->guard('staff')->user() ?? auth()->guard('guest')->user())->name ?? 'Manager',
            'userRole' => 'Manager',
            'payments' => $payments,
            'stats' => $stats,
            'exchangeRate' => $exchangeRate,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'dailyRevenue' => $dailyRevenue,
        ]);
    }
    
    /**
     * Display general reports
     */
    public function reports(Request $request)
    {
        $currencyService = new CurrencyExchangeService();
        $exchangeRate = $currencyService->getUsdToTshRate();

        // Get report type and date parameters
        $reportType = $request->get('report_type', 'monthly');
        $reportDate = $request->get('date', today()->format('Y-m-d'));
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Calculate date range based on report type
        $dateRange = $this->calculateDateRange($reportType, $reportDate, $startDate, $endDate);
        $dateFromCarbon = $dateRange['start'];
        $dateToCarbon = $dateRange['end'];
        $dateFrom = $dateFromCarbon->format('Y-m-d');
        $dateTo = $dateToCarbon->format('Y-m-d');

        // Get bookings in date range
        $bookings = Booking::whereBetween('created_at', [$dateFromCarbon, $dateToCarbon])
            ->with('room')
            ->get();

        // Calculate paid bookings revenue (include both paid and partial payments)
        // Query directly from database for accurate count and sum
        $paidBookingsQuery = Booking::whereBetween('created_at', [$dateFromCarbon, $dateToCarbon])
            ->whereIn('payment_status', ['paid', 'partial'])
            ->whereNotNull('amount_paid')
            ->where('amount_paid', '>', 0);
        
        $totalRevenueTZS = Booking::whereIn('payment_status', ['paid', 'partial'])
            ->whereNotNull('amount_paid')
            ->where('amount_paid', '>', 0)
            ->where(function($q) use ($dateFromCarbon, $dateToCarbon) {
                $q->whereBetween('paid_at', [$dateFromCarbon, $dateToCarbon])
                  ->orWhere(function($subQ) use ($dateFromCarbon, $dateToCarbon) {
                      $subQ->whereNull('paid_at')
                           ->whereBetween('created_at', [$dateFromCarbon, $dateToCarbon]);
                  });
            })
            ->get()
            ->sum(function($booking) use ($exchangeRate) {
                return ($booking->amount_paid ?? 0) * ($booking->locked_exchange_rate ?? $exchangeRate);
            });
        $paidBookingsCount = $paidBookingsQuery->count();

        // Calculate monthly stats
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();
        
        $monthBookings = Booking::where('created_at', '>=', $thisMonth)->count();
        $lastMonthBookings = Booking::whereBetween('created_at', [$lastMonth, $lastMonthEnd])->count();
        
        // This month revenue (include partial payments)
        $monthBookingsData = Booking::whereIn('payment_status', ['paid', 'partial'])
            ->whereNotNull('amount_paid')
            ->where('amount_paid', '>', 0)
            ->where(function($q) use ($thisMonth) {
                $q->where('paid_at', '>=', $thisMonth)
                  ->orWhere(function($subQ) use ($thisMonth) {
                      $subQ->whereNull('paid_at')
                           ->where('created_at', '>=', $thisMonth);
                  });
            })
            ->get();
        $monthRevenueTZS = $monthBookingsData->sum(function($booking) use ($exchangeRate) {
            return ($booking->amount_paid ?? 0) * ($booking->locked_exchange_rate ?? $exchangeRate);
        });
        
        // Last month revenue (include partial payments)
        $lastMonthBookingsData = Booking::whereIn('payment_status', ['paid', 'partial'])
            ->whereNotNull('amount_paid')
            ->where('amount_paid', '>', 0)
            ->where(function($q) use ($lastMonth, $lastMonthEnd) {
                $q->whereBetween('paid_at', [$lastMonth, $lastMonthEnd])
                  ->orWhere(function($subQ) use ($lastMonth, $lastMonthEnd) {
                      $subQ->whereNull('paid_at')
                           ->whereBetween('created_at', [$lastMonth, $lastMonthEnd]);
                  });
            })
            ->get();
        $lastMonthRevenueTZS = $lastMonthBookingsData->sum(function($booking) use ($exchangeRate) {
            return ($booking->amount_paid ?? 0) * ($booking->locked_exchange_rate ?? $exchangeRate);
        });

        // Calculate top performing rooms (include partial payments)
        $topRoomsData = Booking::whereBetween('created_at', [$dateFromCarbon, $dateToCarbon])
            ->whereIn('payment_status', ['paid', 'partial'])
            ->whereNotNull('amount_paid')
            ->where('amount_paid', '>', 0)
            ->get()
            ->groupBy('room_id')
            ->map(function ($bookings, $roomId) use ($exchangeRate) {
                return (object) [
                    'room_id' => $roomId,
                    'booking_count' => $bookings->count(),
                    'total_revenue' => $bookings->sum(function ($b) use ($exchangeRate) {
                        return ($b->amount_paid ?? 0) * ($b->locked_exchange_rate ?? $exchangeRate);
                    })
                ];
            })
            ->sortByDesc('total_revenue')
            ->take(10);
        
        $topRooms = $topRoomsData->map(function($item) {
            $room = Room::find($item->room_id);
            $item->room = $room;
            return $item;
        });

        // Calculate total rooms and occupancy rate
        $totalRooms = Room::count();
        $occupiedRooms = Room::where('status', 'occupied')->count();
        $occupancyRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100, 1) : 0;

        // Calculate average booking value
        $averageBookingValue = $paidBookingsCount > 0 ? round($totalRevenueTZS / $paidBookingsCount, 0) : 0;

        // Get operational statistics (check-in/check-out)
        $operationalStats = [
            'checked_in' => Booking::whereBetween('checked_in_at', [$dateFromCarbon, $dateToCarbon])->count(),
            'checked_out' => Booking::whereBetween('checked_out_at', [$dateFromCarbon, $dateToCarbon])->count(),
        ];

        // Get service requests statistics
        $serviceRequests = [
            'total' => \App\Models\ServiceRequest::whereBetween('requested_at', [$dateFromCarbon, $dateToCarbon])->count(),
            'pending' => \App\Models\ServiceRequest::whereBetween('requested_at', [$dateFromCarbon, $dateToCarbon])
                ->where('status', 'pending')
                ->count(),
            'approved' => \App\Models\ServiceRequest::whereBetween('requested_at', [$dateFromCarbon, $dateToCarbon])
                ->where('status', 'approved')
                ->count(),
            'completed' => \App\Models\ServiceRequest::whereBetween('completed_at', [$dateFromCarbon, $dateToCarbon])
                ->where('status', 'completed')
                ->count(),
            'revenue' => \App\Models\ServiceRequest::whereBetween('completed_at', [$dateFromCarbon, $dateToCarbon])
                ->where('status', 'completed')
                ->sum('total_price_tsh'),
        ];

        // Get day services statistics
        $dayServicesAll = \App\Models\DayService::whereBetween('service_date', [$dateFromCarbon, $dateToCarbon])->get();
        
        $dayServicesStats = [
            'total' => $dayServicesAll->count(),
            'paid' => $dayServicesAll->where('payment_status', 'paid')->count(),
            'pending' => $dayServicesAll->where('payment_status', 'pending')->count(),
        ];

        // Calculate day services revenue (convert to TZS)
        $dayServicesRevenue = 0;
        $paidDayServices = $dayServicesAll->where('payment_status', 'paid');
        foreach ($paidDayServices as $service) {
            if ($service->guest_type === 'tanzanian') {
                $dayServicesRevenue += $service->amount_paid ?? $service->amount ?? 0;
            } else {
                $serviceExchangeRate = $service->exchange_rate ?? $exchangeRate;
                $amountInTzs = ($service->amount_paid ?? $service->amount ?? 0) * $serviceExchangeRate;
                $dayServicesRevenue += $amountInTzs;
            }
        }

        // Day services by type
        $swimmingServices = $dayServicesAll->filter(function($service) {
            return $service->service_type === 'swimming';
        });
        $swimmingWithBucketServices = $dayServicesAll->filter(function($service) {
            return in_array($service->service_type, ['swimming_with_bucket', 'swimming-with-bucket']);
        });
        $ceremonyServices = $dayServicesAll->filter(function($service) {
            return in_array($service->service_type, ['ceremony', 'ceremory']) || 
                   str_contains(strtolower($service->service_type ?? ''), 'ceremony') ||
                   str_contains(strtolower($service->service_type ?? ''), 'birthday') ||
                   str_contains(strtolower($service->service_type ?? ''), 'package');
        });

        $dayServicesByType = [
            'swimming' => [
                'total' => $swimmingServices->count(),
                'paid' => $swimmingServices->where('payment_status', 'paid')->count(),
                'revenue' => $swimmingServices->where('payment_status', 'paid')->sum(function($s) use ($exchangeRate) {
                    return $s->guest_type === 'tanzanian' ? ($s->amount_paid ?? $s->amount ?? 0) : (($s->amount_paid ?? $s->amount ?? 0) * ($s->exchange_rate ?? $exchangeRate));
                }),
            ],
            'swimming_with_bucket' => [
                'total' => $swimmingWithBucketServices->count(),
                'paid' => $swimmingWithBucketServices->where('payment_status', 'paid')->count(),
                'revenue' => $swimmingWithBucketServices->where('payment_status', 'paid')->sum(function($s) use ($exchangeRate) {
                    return $s->guest_type === 'tanzanian' ? ($s->amount_paid ?? $s->amount ?? 0) : (($s->amount_paid ?? $s->amount ?? 0) * ($s->exchange_rate ?? $exchangeRate));
                }),
            ],
            'ceremony' => [
                'total' => $ceremonyServices->count(),
                'paid' => $ceremonyServices->where('payment_status', 'paid')->count(),
                'revenue' => $ceremonyServices->where('payment_status', 'paid')->sum(function($s) use ($exchangeRate) {
                    return $s->guest_type === 'tanzanian' ? ($s->amount_paid ?? $s->amount ?? 0) : (($s->amount_paid ?? $s->amount ?? 0) * ($s->exchange_rate ?? $exchangeRate));
                }),
            ],
        ];

        // Get recent bookings
        $recentBookings = Booking::with('room')
            ->whereBetween('created_at', [$dateFromCarbon, $dateToCarbon])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Calculate total revenue (bookings + day services + service requests)
        $totalRevenueAll = $totalRevenueTZS + $dayServicesRevenue + $serviceRequests['revenue'];

        // Calculate statistics first (needed for recommendations)
        $totalBookingsCount = $bookings->count();
        $confirmedBookingsCount = $bookings->where('status', 'confirmed')->count();
        $cancelledBookingsCount = $bookings->where('status', 'cancelled')->count();
        
        $stats = [
            'total_bookings' => $totalBookingsCount,
            'confirmed_bookings' => $confirmedBookingsCount,
            'cancelled_bookings' => $cancelledBookingsCount,
            'total_revenue' => $totalRevenueTZS,
            'total_revenue_usd' => $totalRevenueUSD,
            'total_revenue_tzs' => $totalRevenueTZS,
            'total_rooms' => $totalRooms,
            'occupancy_rate' => $occupancyRate,
            'month_bookings' => $monthBookings,
            'last_month_bookings' => $lastMonthBookings,
            'month_revenue' => $monthRevenueTZS,
            'last_month_revenue' => $lastMonthRevenueTZS,
            'average_booking_value' => $averageBookingValue,
        ];

        // Generate recommendations based on data
        $recommendations = [];
        
        if ($occupancyRate < 50) {
            $recommendations[] = [
                'type' => 'warning',
                'icon' => 'fa-bed',
                'title' => 'Low Occupancy Rate',
                'message' => "Current occupancy rate is {$occupancyRate}%. Consider promotional offers or marketing campaigns to increase bookings.",
            ];
        }
        
        if ($cancelledBookingsCount > 0 && $totalBookingsCount > 0 && ($cancelledBookingsCount / $totalBookingsCount) > 0.2) {
            $cancellationRate = round(($cancelledBookingsCount / $totalBookingsCount) * 100, 1);
            $recommendations[] = [
                'type' => 'danger',
                'icon' => 'fa-times-circle',
                'title' => 'High Cancellation Rate',
                'message' => "Cancellation rate is {$cancellationRate}%. Review booking policies and guest communication to reduce cancellations.",
            ];
        }
        
        if (isset($dayServicesStats['total']) && $dayServicesStats['total'] > 0 && isset($dayServicesStats['paid']) && ($dayServicesStats['paid'] / $dayServicesStats['total']) < 0.8) {
            $paymentRate = round(($dayServicesStats['paid'] / $dayServicesStats['total']) * 100, 1);
            $recommendations[] = [
                'type' => 'info',
                'icon' => 'fa-credit-card',
                'title' => 'Day Services Payment Collection',
                'message' => "Only {$paymentRate}% of day services are paid. Focus on collecting pending payments to improve cash flow.",
            ];
        }
        
        if (isset($serviceRequests['pending']) && $serviceRequests['pending'] > 0 && isset($serviceRequests['completed']) && $serviceRequests['pending'] > $serviceRequests['completed']) {
            $recommendations[] = [
                'type' => 'warning',
                'icon' => 'fa-clock-o',
                'title' => 'Pending Service Requests',
                'message' => "You have {$serviceRequests['pending']} pending service requests. Prioritize completing service requests to improve guest satisfaction.",
            ];
        }
        
        if ($monthRevenueTZS < $lastMonthRevenueTZS && $lastMonthRevenueTZS > 0) {
            $revenueChange = round((($monthRevenueTZS - $lastMonthRevenueTZS) / $lastMonthRevenueTZS) * 100, 1);
            $recommendations[] = [
                'type' => 'warning',
                'icon' => 'fa-line-chart',
                'title' => 'Revenue Decline',
                'message' => "Monthly revenue decreased by {$revenueChange}% compared to last month. Analyze booking patterns and adjust pricing strategy.",
            ];
        }
        
        if (empty($recommendations)) {
            $recommendations[] = [
                'type' => 'success',
                'icon' => 'fa-check-circle',
                'title' => 'Good Performance',
                'message' => "All metrics are within acceptable ranges. Continue monitoring and maintain current operational standards.",
            ];
        }

        return view('dashboard.admin-reports', [
            'role' => 'manager',
            'userName' => (auth()->guard('staff')->user() ?? auth()->guard('guest')->user())->name ?? 'Manager',
            'userRole' => 'Manager',
            'bookings' => $bookings,
            'stats' => $stats,
            'exchangeRate' => $exchangeRate,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'topRooms' => $topRooms,
            'operationalStats' => $operationalStats,
            'serviceRequests' => $serviceRequests,
            'dayServicesStats' => $dayServicesStats,
            'dayServicesRevenue' => $dayServicesRevenue,
            'dayServicesByType' => $dayServicesByType,
            'recentBookings' => $recentBookings,
            'totalRevenueAll' => $totalRevenueAll,
            'recommendations' => $recommendations,
            'reportType' => $reportType,
            'reportDate' => $reportDate,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'dateRange' => $dateRange,
        ]);
    }
    
    /**
     * Calculate date range based on report type
     */
    private function calculateDateRange($reportType, $reportDate = null, $startDate = null, $endDate = null)
    {
        $today = Carbon::today();
        
        switch ($reportType) {
            case 'daily':
                $date = $reportDate ? Carbon::parse($reportDate) : $today;
                return [
                    'start' => $date->copy()->startOfDay(),
                    'end' => $date->copy()->endOfDay(),
                    'label' => $date->format('F d, Y')
                ];
                
            case 'weekly':
                $date = $reportDate ? Carbon::parse($reportDate) : $today;
                return [
                    'start' => $date->copy()->startOfWeek(),
                    'end' => $date->copy()->endOfWeek(),
                    'label' => $date->copy()->startOfWeek()->format('M d') . ' - ' . $date->copy()->endOfWeek()->format('M d, Y')
                ];
                
            case 'monthly':
                $date = $reportDate ? Carbon::parse($reportDate) : $today;
                return [
                    'start' => $date->copy()->startOfMonth(),
                    'end' => $date->copy()->endOfMonth(),
                    'label' => $date->format('F Y')
                ];
                
            case 'yearly':
                $date = $reportDate ? Carbon::parse($reportDate) : $today;
                return [
                    'start' => $date->copy()->startOfYear(),
                    'end' => $date->copy()->endOfYear(),
                    'label' => $date->format('Y')
                ];
                
            case 'custom':
                $start = $startDate ? Carbon::parse($startDate)->startOfDay() : $today->copy()->subDays(30);
                $end = $endDate ? Carbon::parse($endDate)->endOfDay() : $today;
                return [
                    'start' => $start,
                    'end' => $end,
                    'label' => $start->format('M d, Y') . ' - ' . $end->format('M d, Y')
                ];
                
            default:
                // Default to last 30 days
                return [
                    'start' => $today->copy()->subDays(30)->startOfDay(),
                    'end' => $today->copy()->endOfDay(),
                    'label' => 'Last 30 Days'
                ];
        }
    }
    
    /**
     * Display WiFi settings page
     */
    public function wifiSettings()
    {
        $rooms = Room::all();
        $settings = HotelSetting::first();

        // Get hotel-wide WiFi settings
        $hotelWifiNetworkName = HotelSetting::getWifiNetworkName();
        $hotelWifiPassword = HotelSetting::getWifiPassword();

        return view('dashboard.admin-wifi-settings', [
            'role' => 'manager',
            'userName' => (auth()->guard('staff')->user() ?? auth()->guard('guest')->user())->name ?? 'Manager',
            'userRole' => 'Manager',
            'rooms' => $rooms,
            'settings' => $settings,
            'hotelWifiNetworkName' => $hotelWifiNetworkName,
            'hotelWifiPassword' => $hotelWifiPassword,
        ]);
    }
    
    /**
     * Update WiFi settings
     */
    public function updateWifiSettings(Request $request)
    {
        try {
            $request->validate([
                'wifi_network_name' => 'required|string|max:255',
                'wifi_password' => 'required|string|max:255',
            ]);

            // Update room WiFi settings
            if ($request->has('room_wifi')) {
                foreach ($request->room_wifi as $roomId => $wifiData) {
                    $room = Room::find($roomId);
                    if ($room) {
                        $room->wifi_network_name = $wifiData['network_name'] ?? null;
                        $room->wifi_password = $wifiData['password'] ?? null;
                        $room->save();
                    }
                }
            }

            // Update global WiFi settings using key-value structure
            HotelSetting::setValue('wifi_network_name', $request->wifi_network_name);
            HotelSetting::setValue('wifi_password', $request->wifi_password);

            return response()->json([
                'success' => true,
                'message' => 'Hotel WiFi settings updated successfully.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Update room-specific WiFi settings
     */
    public function updateRoomWifiSettings(Request $request, Room $room)
    {
        $request->validate([
            'wifi_network_name' => 'nullable|string|max:255',
            'wifi_password' => 'nullable|string|max:255',
        ]);

        $room->wifi_network_name = $request->wifi_network_name;
        $room->wifi_password = $request->wifi_password;
        $room->save();

        return response()->json([
            'success' => true,
            'message' => 'Room WiFi settings updated successfully.',
        ]);
    }
    
    /**
     * Display hotel settings page
     */
    public function hotelSettings()
    {
        $settings = HotelSetting::firstOrCreate([]);

        return view('dashboard.admin-hotel-settings', [
            'role' => 'manager',
            'userName' => (auth()->guard('staff')->user() ?? auth()->guard('guest')->user())->name ?? 'Manager',
            'userRole' => 'Manager',
            'settings' => $settings,
        ]);
    }
    
    
    /**
     * Display room settings page
     */
    public function roomSettings()
    {
        $rooms = Room::all();

        // Collect all unique amenities from all rooms
        $allAmenities = collect();
        foreach ($rooms as $room) {
            if ($room->amenities) {
                $amenities = is_array($room->amenities) 
                    ? $room->amenities 
                    : (json_decode($room->amenities, true) ?? []);
                
                if (is_array($amenities)) {
                    $allAmenities = $allAmenities->merge($amenities);
                }
            }
        }
        // Get unique amenities and sort them
        $allAmenities = $allAmenities->unique()->filter(function($amenity) {
            return !empty($amenity) && $amenity !== 'null' && $amenity !== 'undefined';
        })->values()->sort()->values();

        return view('dashboard.admin-room-settings', [
            'role' => 'manager',
            'userName' => (auth()->guard('staff')->user() ?? auth()->guard('guest')->user())->name ?? 'Manager',
            'userRole' => 'Manager',
            'rooms' => $rooms,
            'allAmenities' => $allAmenities,
        ]);
    }
    
    /**
     * Display pricing settings page
     */
    public function pricingSettings()
    {
        $rooms = Room::all();

        return view('dashboard.admin-pricing-settings', [
            'role' => 'manager',
            'userName' => (auth()->guard('staff')->user() ?? auth()->guard('guest')->user())->name ?? 'Manager',
            'userRole' => 'Manager',
            'rooms' => $rooms,
        ]);
    }
    
    /**
     * Display feedback analysis page
     */
    public function feedbackAnalysis()
    {
        $feedbacks = Feedback::with('booking')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Calculate total feedbacks
        $totalFeedbacks = Feedback::count();
        
        // Calculate average rating
        $averageRating = Feedback::avg('rating') ?? 0;
        $averageRating = round($averageRating, 1);
        
        // Get recent feedbacks (last 30 days)
        $recentFeedbacks = Feedback::where('created_at', '>=', Carbon::now()->subDays(30))->get();
        
        // Rating distribution (1-5 stars)
        $ratingDistribution = [
            5 => Feedback::where('rating', 5)->count(),
            4 => Feedback::where('rating', 4)->count(),
            3 => Feedback::where('rating', 3)->count(),
            2 => Feedback::where('rating', 2)->count(),
            1 => Feedback::where('rating', 1)->count(),
        ];
        
        // Calculate category averages
        $allFeedbacks = Feedback::whereNotNull('categories')->get();
        $categoryTotals = [
            'room_quality' => 0,
            'service' => 0,
            'cleanliness' => 0,
            'value' => 0,
        ];
        $categoryCounts = [
            'room_quality' => 0,
            'service' => 0,
            'cleanliness' => 0,
            'value' => 0,
        ];
        
        foreach ($allFeedbacks as $feedback) {
            if ($feedback->categories && is_array($feedback->categories)) {
                foreach (['room_quality', 'service', 'cleanliness', 'value'] as $category) {
                    if (isset($feedback->categories[$category]) && is_numeric($feedback->categories[$category])) {
                        $categoryTotals[$category] += $feedback->categories[$category];
                        $categoryCounts[$category]++;
                    }
                }
            }
        }
        
        $categoryAverages = [
            'room_quality' => $categoryCounts['room_quality'] > 0 ? round($categoryTotals['room_quality'] / $categoryCounts['room_quality'], 1) : 0,
            'service' => $categoryCounts['service'] > 0 ? round($categoryTotals['service'] / $categoryCounts['service'], 1) : 0,
            'cleanliness' => $categoryCounts['cleanliness'] > 0 ? round($categoryTotals['cleanliness'] / $categoryCounts['cleanliness'], 1) : 0,
            'value' => $categoryCounts['value'] > 0 ? round($categoryTotals['value'] / $categoryCounts['value'], 1) : 0,
        ];
        
        // Calculate monthly trend (last 6 months)
        $monthlyTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthStart = Carbon::now()->subMonths($i)->startOfMonth();
            $monthEnd = Carbon::now()->subMonths($i)->endOfMonth();
            $monthName = $monthStart->format('M Y');
            
            $monthFeedbacks = Feedback::whereBetween('created_at', [$monthStart, $monthEnd])->get();
            $monthCount = $monthFeedbacks->count();
            $monthAverage = $monthCount > 0 ? round($monthFeedbacks->avg('rating'), 1) : 0;
            
            $monthlyTrend[$monthName] = [
                'count' => $monthCount,
                'average' => $monthAverage,
            ];
        }

        return view('dashboard.admin-feedback-analysis', [
            'role' => 'manager',
            'userName' => (auth()->guard('staff')->user() ?? auth()->guard('guest')->user())->name ?? 'Manager',
            'userRole' => 'Manager',
            'feedbacks' => $feedbacks,
            'totalFeedbacks' => $totalFeedbacks,
            'averageRating' => $averageRating,
            'recentFeedbacks' => $recentFeedbacks,
            'ratingDistribution' => $ratingDistribution,
            'categoryAverages' => $categoryAverages,
            'monthlyTrend' => $monthlyTrend,
        ]);
    }
    
    /**
     * Display services management page
     */
    public function services()
    {
        $services = Service::all();

        return view('dashboard.admin-services', [
            'role' => 'manager',
            'userName' => (auth()->guard('staff')->user() ?? auth()->guard('guest')->user())->name ?? 'Manager',
            'userRole' => 'Manager',
            'services' => $services,
        ]);
    }
    
    /**
     * Show service form (create/edit)
     */
    public function serviceForm($id = null)
    {
        $service = $id ? Service::findOrFail($id) : null;

        return view('dashboard.admin-service-form', [
            'role' => 'manager',
            'userName' => (auth()->guard('staff')->user() ?? auth()->guard('guest')->user())->name ?? 'Manager',
            'userRole' => 'Manager',
            'service' => $service,
        ]);
    }
    
    /**
     * Show the form for creating a new service
     */
    public function createService()
    {
        return view('dashboard.admin-service-form', [
            'role' => 'manager',
            'userName' => (auth()->guard('staff')->user() ?? auth()->guard('guest')->user())->name ?? 'Manager',
            'userRole' => 'Manager',
            'service' => null,
        ]);
    }
    
    /**
     * Update Hotel Settings
     */
    public function updateHotelSettings(Request $request)
    {
        $validated = $request->validate([
            // Basic Information
            'hotel_name' => 'nullable|string|max:255',
            'hotel_address' => 'nullable|string|max:500',
            'hotel_phone' => 'nullable|string|max:50',
            'hotel_email' => 'nullable|email|max:255',
            'hotel_website' => 'nullable|url|max:255',
            
            // Check-in/Check-out Times
            'default_checkin_time' => 'nullable|date_format:H:i',
            'default_checkout_time' => 'nullable|date_format:H:i',
            
            // Booking Settings
            'min_stay_nights' => 'nullable|integer|min:1',
            'max_stay_nights' => 'nullable|integer|min:1',
            'booking_expiration_hours' => 'nullable|integer|min:1',
            'auto_cancel_unpaid' => 'nullable|boolean',
            
            // Policies
            'cancellation_policy' => 'nullable|string',
            'terms_conditions' => 'nullable|string',
            'privacy_policy' => 'nullable|string',
            
            // Contact Information
            'support_email' => 'nullable|email|max:255',
            'support_phone' => 'nullable|string|max:50',
            
            // Currency & Exchange
            'base_currency' => 'nullable|string|max:10',
            'exchange_rate_usd_to_tzs' => 'nullable|numeric|min:0',
            
            // Tax & Fees
            'tax_rate_percentage' => 'nullable|numeric|min:0|max:100',
            'service_charge_percentage' => 'nullable|numeric|min:0|max:100',
            'city_tax_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        // Save all settings
        foreach ($validated as $key => $value) {
            if ($value !== null) {
                HotelSetting::setValue($key, $value);
            }
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Hotel settings updated successfully.'
            ]);
        }

        return redirect()->route('admin.settings.hotel')
            ->with('success', 'Hotel settings updated successfully.');
    }
    

    /**
     * Update Room Settings
     */
    public function updateRoomSettings(Request $request)
    {
        $validated = $request->validate([
            // Default Times
            'default_room_checkin_time' => 'nullable|date_format:H:i',
            'default_room_checkout_time' => 'nullable|date_format:H:i',
            
            // Default Amenities
            'default_amenities' => 'nullable|array',
            'default_amenities.*' => 'string|max:255',
            
            // Room Status Rules
            'auto_update_status_after_checkout' => 'nullable|boolean',
            'auto_maintenance_after_days' => 'nullable|integer|min:0',
            
            // Cleaning Settings
            'default_cleaning_duration_hours' => 'nullable|numeric|min:0|max:24',
            'auto_cleaning_status' => 'nullable|boolean',
            
            // Maintenance Settings
            'maintenance_duration_hours' => 'nullable|numeric|min:0|max:168',
            'auto_maintenance_trigger' => 'nullable|boolean',
            
            // Default Pricing by Room Type
            'default_price_single' => 'nullable|numeric|min:0',
            'default_price_double' => 'nullable|numeric|min:0',
            'default_price_twins' => 'nullable|numeric|min:0',
        ]);

        // Save all settings
        foreach ($validated as $key => $value) {
            if ($value !== null) {
                if (is_array($value)) {
                    HotelSetting::setValue($key, json_encode($value));
                } else {
                    HotelSetting::setValue($key, $value);
                }
            }
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Room settings updated successfully.'
            ]);
        }

        return redirect()->route('admin.settings.rooms')
            ->with('success', 'Room settings updated successfully.');
    }

    /**
     * Update Pricing Settings
     */
    public function updatePricingSettings(Request $request)
    {
        $validated = $request->validate([
            // Exchange Rate
            'exchange_rate_usd_to_tzs' => 'nullable|numeric|min:0',
            'auto_update_exchange_rate' => 'nullable|boolean',
            
            // Tax Rates
            'vat_percentage' => 'nullable|numeric|min:0|max:100',
            'service_tax_percentage' => 'nullable|numeric|min:0|max:100',
            'city_tax_percentage' => 'nullable|numeric|min:0|max:100',
            
            // Service Charges
            'service_charge_type' => 'nullable|in:percentage,fixed',
            'service_charge_percentage' => 'nullable|numeric|min:0|max:100',
            'service_charge_fixed' => 'nullable|numeric|min:0',
            
            // Season Management
            'peak_season_start_month' => 'nullable|integer|min:1|max:12',
            'peak_season_start_day' => 'nullable|integer|min:1|max:31',
            'peak_season_end_month' => 'nullable|integer|min:1|max:12',
            'peak_season_end_day' => 'nullable|integer|min:1|max:31',
            'peak_season_multiplier' => 'nullable|numeric|min:1',
            'off_season_multiplier' => 'nullable|numeric|min:0|max:1',
            
            // Discount Rules
            'early_bird_discount_percentage' => 'nullable|numeric|min:0|max:100',
            'early_bird_days_advance' => 'nullable|integer|min:1',
            'long_stay_discount_percentage' => 'nullable|numeric|min:0|max:100',
            'long_stay_min_nights' => 'nullable|integer|min:1',
            'last_minute_discount_percentage' => 'nullable|numeric|min:0|max:100',
            'last_minute_max_days' => 'nullable|integer|min:1',
            
            // Dynamic Pricing
            'weekend_multiplier' => 'nullable|numeric|min:1',
            'holiday_multiplier' => 'nullable|numeric|min:1',
            'enable_dynamic_pricing' => 'nullable|boolean',
        ]);

        // Save all settings
        foreach ($validated as $key => $value) {
            if ($value !== null) {
                HotelSetting::setValue($key, $value);
            }
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Pricing settings updated successfully.'
            ]);
        }

        return redirect()->route('admin.settings.pricing')
            ->with('success', 'Pricing settings updated successfully.');
    }

    /**
     * Store new service
     */
    public function storeService(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|max:255',
            'price_tsh' => 'nullable|numeric|min:0',
            'is_free_for_internal' => 'nullable|boolean',
            'age_group' => 'required|in:adult,child,both',
            'child_price_tsh' => 'nullable|numeric|min:0',
            'unit' => 'required|string|max:255',
            'is_active' => 'nullable|boolean',
            'requires_approval' => 'nullable|boolean',
            'required_fields' => 'nullable|array',
        ]);

        // If not free for internal guests, price is required based on age group
        if (!$request->has('is_free_for_internal') || !$request->is_free_for_internal) {
            $ageGroup = $request->input('age_group', 'both');
            if ($ageGroup === 'adult' || $ageGroup === 'both') {
                $request->validate([
                    'price_tsh' => 'required|numeric|min:0',
                ]);
            }
            if ($ageGroup === 'child') {
                $request->validate([
                    'child_price_tsh' => 'required|numeric|min:0',
                ]);
                $validated['price_tsh'] = $validated['child_price_tsh'] ?? 0;
            }
        } else {
            // If free for internal guests, set prices to 0
            $validated['price_tsh'] = 0;
            $validated['child_price_tsh'] = 0;
        }

        // Process required_fields if provided
        if ($request->has('required_fields') && is_array($request->required_fields)) {
            $requiredFields = [];
            foreach ($request->required_fields as $field) {
                if (!empty($field['name']) && !empty($field['label'])) {
                    $requiredFields[] = [
                        'name' => $field['name'],
                        'label' => $field['label'],
                        'type' => $field['type'] ?? 'text',
                        'required' => isset($field['required']) ? (bool)$field['required'] : false,
                        'placeholder' => $field['placeholder'] ?? null,
                        'min' => isset($field['min']) ? (int)$field['min'] : null,
                        'max' => isset($field['max']) ? (int)$field['max'] : null,
                        'default' => $field['default'] ?? null,
                    ];
                }
            }
            $validated['required_fields'] = !empty($requiredFields) ? $requiredFields : null;
        } else {
            $validated['required_fields'] = null;
        }

        $validated['is_active'] = $request->has('is_active') ? true : false;
        $validated['requires_approval'] = $request->has('requires_approval') ? true : false;
        $validated['is_free_for_internal'] = $request->has('is_free_for_internal') ? true : false;

        Service::create($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Service created successfully.'
            ]);
        }

        return redirect()->route('admin.services.index')
            ->with('success', 'Service created successfully.');
    }

    /**
     * Show Edit Service form
     */
    public function editService(Service $service)
    {
        return view('dashboard.admin-service-form', [
            'role' => 'manager',
            'userName' => (auth()->guard('staff')->user() ?? auth()->guard('guest')->user())->name ?? 'Manager',
            'userRole' => 'Manager',
            'service' => $service,
        ]);
    }

    /**
     * Update service
     */
    public function updateService(Request $request, Service $service)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|max:255',
            'price_tsh' => 'nullable|numeric|min:0',
            'is_free_for_internal' => 'nullable|boolean',
            'age_group' => 'required|in:adult,child,both',
            'child_price_tsh' => 'nullable|numeric|min:0',
            'unit' => 'required|string|max:255',
            'is_active' => 'nullable|boolean',
            'requires_approval' => 'nullable|boolean',
            'required_fields' => 'nullable|array',
        ]);

        // If not free for internal guests, price is required based on age group
        if (!$request->has('is_free_for_internal') || !$request->is_free_for_internal) {
            $ageGroup = $request->input('age_group', 'both');
            if ($ageGroup === 'adult' || $ageGroup === 'both') {
                $request->validate([
                    'price_tsh' => 'required|numeric|min:0',
                ]);
            }
            if ($ageGroup === 'child') {
                $request->validate([
                    'child_price_tsh' => 'required|numeric|min:0',
                ]);
                $validated['price_tsh'] = $validated['child_price_tsh'] ?? 0;
            }
        } else {
            // If free for internal guests, set prices to 0
            $validated['price_tsh'] = 0;
            $validated['child_price_tsh'] = 0;
        }

        // Process required_fields if provided
        if ($request->has('required_fields') && is_array($request->required_fields)) {
            $requiredFields = [];
            foreach ($request->required_fields as $field) {
                if (!empty($field['name']) && !empty($field['label'])) {
                    $requiredFields[] = [
                        'name' => $field['name'],
                        'label' => $field['label'],
                        'type' => $field['type'] ?? 'text',
                        'required' => isset($field['required']) ? (bool)$field['required'] : false,
                        'placeholder' => $field['placeholder'] ?? null,
                        'min' => isset($field['min']) ? (int)$field['min'] : null,
                        'max' => isset($field['max']) ? (int)$field['max'] : null,
                        'default' => $field['default'] ?? null,
                    ];
                }
            }
            $validated['required_fields'] = !empty($requiredFields) ? $requiredFields : null;
        } else {
            $validated['required_fields'] = null;
        }

        $validated['is_active'] = $request->has('is_active') ? true : false;
        $validated['requires_approval'] = $request->has('requires_approval') ? true : false;
        $validated['is_free_for_internal'] = $request->has('is_free_for_internal') ? true : false;

        $service->update($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Service updated successfully.'
            ]);
        }

        return redirect()->route('admin.services.index')
            ->with('success', 'Service updated successfully.');
    }

    /**
     * Delete service
     */
    public function deleteService(Service $service)
    {
        // Check if service has any requests
        if ($service->serviceRequests()->count() > 0) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete service that has existing requests. Deactivate it instead.'
                ], 422);
            }
            return redirect()->route('admin.services.index')
                ->with('error', 'Cannot delete service that has existing requests. Deactivate it instead.');
        }

        $service->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Service deleted successfully.'
            ]);
        }

        return redirect()->route('admin.services.index')
            ->with('success', 'Service deleted successfully.');
    }
    /**
     * Restaurant/Bar Operational Reports
     */
    public function restaurantReports(Request $request)
    {
        $dateType = $request->get('date_type', 'daily');
        $customDate = $request->get('date', now()->format('Y-m-d'));
        
        // precise date filtering
        $startDate = now();
        $endDate = now();
        
        switch ($dateType) {
            case 'daily':
                $startDate = \Carbon\Carbon::parse($customDate)->startOfDay();
                $endDate = \Carbon\Carbon::parse($customDate)->endOfDay();
                break;
            case 'weekly':
                $startDate = \Carbon\Carbon::parse($customDate)->startOfWeek();
                $endDate = \Carbon\Carbon::parse($customDate)->endOfWeek();
                break;
            case 'monthly':
                $startDate = \Carbon\Carbon::parse($customDate)->startOfMonth();
                $endDate = \Carbon\Carbon::parse($customDate)->endOfMonth();
                break;
            case 'yearly':
                $startDate = \Carbon\Carbon::parse($customDate)->startOfYear();
                $endDate = \Carbon\Carbon::parse($customDate)->endOfYear();
                break;
        }
        
        // 1. Received Stock (Transfers In - Global)
        // We look for transfers received by ANYONE (or ideally filter by bar/restaurant staff if needed, for now all completed transfers logic)
        $receivedTransfers = \App\Models\StockTransfer::with(['product', 'productVariant', 'receivedBy'])
            ->where('status', 'completed')
            ->whereBetween('received_at', [$startDate, $endDate])
            ->orderBy('received_at', 'desc')
            ->get();
            
        $totalReceivedBottles = 0;
        $totalReceivedCrates = 0; // Approximate
        
        foreach ($receivedTransfers as $transfer) {
            $itemsPerPackage = $transfer->productVariant->items_per_package ?? 1;
            
            if ($transfer->quantity_unit === 'packages') {
                $bottles = $transfer->quantity_transferred * $itemsPerPackage;
                $totalReceivedCrates += $transfer->quantity_transferred;
                $totalReceivedBottles += $bottles;
            } else {
                // bottles
                $totalReceivedBottles += $transfer->quantity_transferred;
                $totalReceivedCrates += ($transfer->quantity_transferred / $itemsPerPackage);
            }
        }
        
        // 2. Sold Items (Service Requests - Global Restaurant/Bar)
        // We filter by valid bar/restaurant categories
        $barCategories = ['alcoholic_beverage', 'non_alcoholic_beverage', 'water', 'juices', 'energy_drinks'];
        $kitchenCategories = ['food', 'restaurant'];
        
        $staffUser = auth()->guard('staff')->user();
        $isHeadChef = $staffUser && ($staffUser->registration_id === 'HEADCHEF' || \App\Services\RolePermissionService::hasRole($staffUser, 'head_chef'));
        $targetCategories = $isHeadChef ? $kitchenCategories : array_merge($barCategories, $kitchenCategories);

        $soldOrders = \App\Models\ServiceRequest::with(['service', 'booking', 'approvedBy'])
            ->whereHas('service', function($q) use ($targetCategories) {
                $q->whereIn('category', $targetCategories);
            })
            ->where('status', 'completed')
            // Filter by completion time (sales time)
            ->whereBetween('completed_at', [$startDate, $endDate]) 
            ->orderBy('completed_at', 'desc')
            ->get();
            
        $totalSoldItems = $soldOrders->sum('quantity');
        $totalRevenue = $soldOrders->sum('total_price_tsh');
        
        // 3. Issues / Expired / Broken (Global)
        $issues = \App\Models\IssueReport::with('user')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('issue_type', ['expired', 'damage']) // Only stock related issues
            ->get();
            
        $totalExpired = $issues->where('issue_type', 'expired')->count(); 
        $totalBroken = $issues->where('issue_type', 'damage')->count();
        
        // 4. Physical Stock (Global Calculated)
        // We fetch ALL completed transfers EVER (to get total IN)
        // and ALL completed sales EVER (to get total OUT)
        // Then we match them by name to estimate current stock.
        
        $allTransfers = \App\Models\StockTransfer::with(['product', 'productVariant'])
            ->where('status', 'completed')
            ->get();
            
        $stockMap = [];
        
        foreach ($allTransfers as $transfer) {
            $key = $transfer->product_id . '_' . $transfer->product_variant_id;
            
            if (!isset($stockMap[$key])) {
                $stockMap[$key] = [
                    'product_id' => $transfer->product_id,
                    'variant_id' => $transfer->product_variant_id,
                    'product_name' => $transfer->product->name ?? 'Unknown',
                    'variant_name' => $transfer->productVariant->measurement ?? '',
                    'unit' => $transfer->productVariant->packaging ?? 'units',
                    'total_received' => 0,
                    'total_sold' => 0,
                    'current_stock' => 0,
                ];
            }
            
            // Normalize to bottles/items
            $itemsPerPackage = $transfer->productVariant->items_per_package ?? 1;
            $quantity = ($transfer->quantity_unit === 'packages') 
                ? $transfer->quantity_transferred * $itemsPerPackage 
                : $transfer->quantity_transferred;
                
            $stockMap[$key]['total_received'] += $quantity;
        }
        
        // All sales for estimation
        $allSales = \App\Models\ServiceRequest::with('service')
            ->whereHas('service', function($q) use ($barCategories, $kitchenCategories) {
                $q->whereIn('category', array_merge($barCategories, $kitchenCategories));
            })
            ->where('status', 'completed')
            ->get();
            
        foreach ($allSales as $sale) {
            $metadata = $sale->service_specific_data;
            // Use the most specific name available
            $serviceName = strtolower($metadata['item_name'] ?? $sale->service->name ?? '');
            
            foreach ($stockMap as $key => &$stockItem) {
                $productName = strtolower($stockItem['product_name']);
                $variantName = strtolower($stockItem['variant_name']);
                
                // Matches if service name contains both product and variant (e.g., "Coca Cola 600ml")
                // Or if it's a direct match to product name
                if (str_contains($serviceName, $productName)) {
                    // If there is a variant, try to be specific
                    if (!empty($variantName)) {
                        if (str_contains($serviceName, $variantName)) {
                            $stockItem['total_sold'] += $sale->quantity;
                            break; // Stop looking for this sale once matched to a specific variant
                        }
                    } else {
                        // No variant info in stock, match on product name
                        $stockItem['total_sold'] += $sale->quantity;
                        break;
                    }
                }
            }
        }
        
        // Calculate balance
        foreach ($stockMap as &$item) {
            $item['current_stock'] = $item['total_received'] - $item['total_sold'];
        }
        
        $physicalStock = collect($stockMap)->sortBy('product_name');
        
        // 5. Ingredient Tracking (Consumption from Recipes)
        $ingredientConsumption = DB::table('recipe_consumptions')
            ->join('products', 'recipe_consumptions.product_id', '=', 'products.id')
            ->whereBetween('recipe_consumptions.created_at', [$startDate, $endDate])
            ->select(
                'products.id as product_id',
                'products.name', 
                'recipe_consumptions.unit', 
                DB::raw('SUM(quantity_consumed) as total_consumed')
            )
            ->groupBy('products.id', 'products.name', 'recipe_consumptions.unit')
            ->orderBy('products.name')
            ->get();
            
        // Calculate available stock for consumed ingredients
        foreach ($ingredientConsumption as $ingredient) {
            // Get total received from stock receipts
            $receiptsReceived = DB::table('stock_receipts')
                ->join('product_variants', 'stock_receipts.product_variant_id', '=', 'product_variants.id')
                ->where('stock_receipts.product_id', $ingredient->product_id)
                ->select(DB::raw('SUM(stock_receipts.quantity_received_packages * product_variants.items_per_package) as total_received'))
                ->first()->total_received ?? 0;
            // Get total from shopping list purchases
            $shoppingListReceived = DB::table('shopping_list_items')
                ->where('product_id', $ingredient->product_id)
                ->where('is_purchased', true)
                ->sum('purchased_quantity');

            $totalReceived = (float)$receiptsReceived + (float)$shoppingListReceived;

            // Get total consumed (all time)
            $totalConsumed = DB::table('recipe_consumptions')
                ->where('product_id', $ingredient->product_id)
                ->sum('quantity_consumed');

            $ingredient->available_stock = max(0, $totalReceived - $totalConsumed);
        }
        
        // 6. All Kitchen Ingredients Inventory (regardless of usage)
        $allKitchenIngredients = DB::table('products')
            ->where(function($q) {
                $q->where('category', 'food')
                  ->orWhere('type', 'kitchen');
            })
            ->orderBy('name')
            ->get()
            ->map(function($product) {
                // Get total received from stock receipts
                $receiptsReceived = DB::table('stock_receipts')
                    ->join('product_variants', 'stock_receipts.product_variant_id', '=', 'product_variants.id')
                    ->where('stock_receipts.product_id', $product->id)
                    ->select(DB::raw('SUM(stock_receipts.quantity_received_packages * product_variants.items_per_package) as total_received'))
                    ->first()->total_received ?? 0;

                // Get total from shopping list purchases
                $shoppingListReceived = DB::table('shopping_list_items')
                    ->where('product_id', $product->id)
                    ->where('is_purchased', true)
                    ->sum('purchased_quantity');

                $totalReceived = (float)$receiptsReceived + (float)$shoppingListReceived;

                // Get total consumed
                $totalConsumed = DB::table('recipe_consumptions')
                    ->where('product_id', $product->id)
                    ->sum('quantity_consumed');

                // Get unit from first variant or default
                $variant = DB::table('product_variants')
                    ->where('product_id', $product->id)
                    ->first();
                
                $unit = $variant ? $variant->measurement : 'units';

                return (object)[
                    'name' => $product->name,
                    'category' => $product->category_name ?? 'Kitchen',
                    'total_received' => $totalReceived,
                    'total_consumed' => $totalConsumed,
                    'available_stock' => max(0, $totalReceived - $totalConsumed),
                    'unit' => $unit,
                    'image' => $product->image
                ];
            })
            ->filter(function($item) {
                // Only show items that have been received at least once
                return $item->total_received > 0;
            });
            
        $staffUser = auth()->guard('staff')->user();
        $userName = $staffUser->name ?? 'Guest';
        $userRole = 'Staff';
        
        if ($staffUser) {
            $userRole = match($staffUser->role) {
                'manager' => 'Manager',
                'reception' => 'Receptionist',
                'head_chef' => 'Head Chef',
                'bar_keeper' => 'Bar Keeper',
                default => ucfirst($staffUser->role)
            };
        }

        return view('dashboard.admin-restaurant-reports', [
            'role' => $staffUser->role ?? 'manager',
            'userName' => $userName,
            'userRole' => $userRole,
            'dateType' => $dateType,
            'customDate' => $customDate,
            'receivedTransfers' => $receivedTransfers,
            'totalReceivedBottles' => $totalReceivedBottles,
            'totalReceivedCrates' => $totalReceivedCrates,
            'soldOrders' => $soldOrders,
            'totalSoldItems' => $totalSoldItems,
            'totalRevenue' => $totalRevenue,
            'issues' => $issues,
            'totalExpired' => $totalExpired,
            'totalBroken' => $totalBroken,
            'physicalStock' => $physicalStock,
            'ingredientConsumption' => $ingredientConsumption,
            'allKitchenIngredients' => $allKitchenIngredients
        ]);
    }
    /**
     * Global Restaurant & Bar Stock Overview
     */
    public function stock(Request $request)
    {
        $barCategories = ['alcoholic_beverage', 'non_alcoholic_beverage', 'water', 'juices', 'energy_drinks', 'food', 'restaurant'];
        
        // Fetch ALL completed transfers (Global IN to Bar)
        $allTransfers = StockTransfer::with(['product', 'productVariant'])
            ->where('status', 'completed')
            ->get();
            
        $stockMap = [];
        
        foreach ($allTransfers as $transfer) {
            $key = $transfer->product_id . '_' . $transfer->product_variant_id;
            
            if (!isset($stockMap[$key])) {
                $stockMap[$key] = [
                    'product_id' => $transfer->product_id,
                    'variant_id' => $transfer->product_variant_id,
                    'product_name' => $transfer->product->name ?? 'Unknown',
                    'product_image' => $transfer->product->image,
                    'variant_name' => $transfer->productVariant->measurement ?? '',
                    'packaging' => $transfer->productVariant->packaging ?? '',
                    'items_per_package' => $transfer->productVariant->items_per_package ?? 1,
                    'unit' => $transfer->productVariant->packaging ?? 'units',
                    'total_received' => 0,
                    'total_sold' => 0,
                    'current_stock' => 0,
                    'generated_revenue' => 0,
                    'selling_price' => 0,
                ];
            }
            
            $itemsPerPackage = $transfer->productVariant->items_per_package ?? 1;
            $quantity = ($transfer->quantity_unit === 'packages') 
                ? $transfer->quantity_transferred * $itemsPerPackage 
                : $transfer->quantity_transferred;
                
            $stockMap[$key]['total_received'] += $quantity;
        }
        
        // Fetch ALL sales (Global OUT)
        $allSales = \App\Models\ServiceRequest::with('service')
            ->whereHas('service', function($q) use ($barCategories) {
                $q->whereIn('category', $barCategories);
            })
            ->where('status', 'completed')
            ->get();
            
        foreach ($allSales as $sale) {
            $meta = $sale->service_specific_data;
            $matched = false;
            
            // 1. Precise match using product/variant IDs (Preferred)
            if (isset($meta['product_id']) && isset($meta['product_variant_id'])) {
                $key = $meta['product_id'] . '_' . $meta['product_variant_id'];
                if (isset($stockMap[$key])) {
                    $stockMap[$key]['total_sold'] += $sale->quantity;
                    $stockItem = &$stockMap[$key];
                    $stockItem['generated_revenue'] += $sale->total_price_tsh;
                    
                    if ($stockItem['selling_price'] == 0 && $sale->service) {
                        $stockItem['selling_price'] = $sale->unit_price_tsh;
                    }
                    $matched = true;
                }
            }
            
            // 2. Fuzzy match fallback
            if (!$matched) {
                $serviceName = strtolower($sale->service->name ?? '');
                $itemName = strtolower($meta['item_name'] ?? '');
                
                foreach ($stockMap as $key => &$stockItem) {
                    $productName = strtolower($stockItem['product_name']);
                    
                    if (($itemName && str_contains($itemName, $productName)) || 
                        ($serviceName && str_contains($serviceName, $productName)) || 
                        str_contains($productName, $serviceName)) {
                        $stockItem['total_sold'] += $sale->quantity;
                        $stockItem['generated_revenue'] += $sale->total_price_tsh;
                        
                        if ($stockItem['selling_price'] == 0) {
                            $stockItem['selling_price'] = $sale->unit_price_tsh;
                        }
                    }
                }
            }
        }
        
        // Fallback for selling price from Receipts
        foreach ($stockMap as $key => &$stockItem) {
            if ($stockItem['selling_price'] == 0) {
                 $latestReceipt = \App\Models\StockReceipt::where('product_id', $stockItem['product_id'])
                    ->where('product_variant_id', $stockItem['variant_id'])
                    ->orderBy('received_date', 'desc')
                    ->first();
                    
                 if ($latestReceipt) {
                     $stockItem['selling_price'] = $latestReceipt->selling_price_per_bottle;
                 }
            }
        }
        
        // Calculate breakdown
        foreach ($stockMap as &$item) {
            $item['current_stock'] = $item['total_received'] - $item['total_sold'];
            
            $itemsPerPkg = $item['items_per_package'] > 0 ? $item['items_per_package'] : 1;
            
            if ($itemsPerPkg > 1) {
                $crates = floor($item['current_stock'] / $itemsPerPkg);
                $bottles = $item['current_stock'] % $itemsPerPkg;
                
                $item['stock_breakdown'] = "{$crates} " . ucfirst($item['packaging']);
                if ($bottles > 0) {
                    $item['stock_breakdown'] .= " & {$bottles} Bottles";
                }
                $item['total_bottles_display'] = "({$item['current_stock']} Total)";
            } else {
                $item['stock_breakdown'] = "{$item['current_stock']} Units";
                $item['total_bottles_display'] = "";
            }
        }
        
        $allStock = collect($stockMap)->sortBy('product_name');
        
        return view('dashboard.admin-stock', compact('allStock'));
    }
}

