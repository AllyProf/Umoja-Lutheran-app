<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use App\Models\ServiceRequest;
use App\Models\DayService;
use App\Models\Feedback;
use App\Models\StockReceipt;
use App\Models\Product;
use App\Models\Recipe;
use App\Services\CurrencyExchangeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReportController extends Controller
{
    protected $currencyService;
    protected $exchangeRate;

    public function __construct()
    {
        $this->currencyService = new CurrencyExchangeService();
        $this->exchangeRate = $this->currencyService->getUsdToTshRate();
    }

    /**
     * Reports Dashboard - Main landing page for all reports
     */
    public function index()
    {
        $user = Auth::guard('staff')->user() ?? Auth::guard('guest')->user();
        
        if (!$user) {
            abort(403, 'Unauthorized access');
        }

        // Get quick stats for today
        $today = Carbon::today();
        $todayBookings = Booking::whereDate('created_at', $today)->count();
        $todayRevenueUSD = Booking::whereDate('created_at', $today)
            ->whereIn('payment_status', ['paid', 'partial'])
            ->whereNotNull('amount_paid')
            ->where('amount_paid', '>', 0)
            ->sum('amount_paid');
        $todayBookingRevenueTZS = $todayRevenueUSD * $this->exchangeRate;

        // Add Service Revenue (Bar/Kitchen)
        $todayServiceRevenueTZS = ServiceRequest::whereDate('requested_at', $today)
            ->where('status', 'completed')
            ->sum('total_price_tsh');

        // Add Day Service Revenue
    $todayDayServiceRevenueTZS = \App\Models\DayService::whereDate('service_date', $today)
        ->where('payment_status', 'paid')
        ->get()
        ->sum(function($ds) {
             // If exchange rate is recorded (International), amount is USD -> Convert to TZS
             // If no exchange rate (Local), amount is TZS already -> Use as is
             return $ds->exchange_rate 
                ? ($ds->amount_paid ?? $ds->amount) * $ds->exchange_rate 
                : ($ds->amount_paid ?? $ds->amount);
        });

    // TOTAL Today
    $todayRevenueTZS = $todayBookingRevenueTZS + $todayServiceRevenueTZS + $todayDayServiceRevenueTZS;
    $todayRevenueUSD = $this->exchangeRate > 0 ? ($todayRevenueTZS / $this->exchangeRate) : 0;
        
        $totalRooms = Room::count();
        $occupiedRooms = Room::where('status', 'occupied')->count();
        $occupancyRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100, 1) : 0;
        
        $thisMonth = Carbon::now()->startOfMonth();
        $monthRevenueUSD = Booking::where('created_at', '>=', $thisMonth)
            ->whereIn('payment_status', ['paid', 'partial'])
            ->whereNotNull('amount_paid')
            ->where('amount_paid', '>', 0)
            ->sum('amount_paid');
        $monthBookingRevenueTZS = $monthRevenueUSD * $this->exchangeRate;

        // Add Month Service Revenue
        $monthServiceRevenueTZS = ServiceRequest::where('completed_at', '>=', $thisMonth)
            ->where('status', 'completed')
            ->sum('total_price_tsh');

        // Add Month Day Service Revenue
        $monthDayServiceRevenueTZS = \App\Models\DayService::where('service_date', '>=', $thisMonth)
            ->where('payment_status', 'paid')
            ->get()
            ->sum(function($ds) {
                 return $ds->exchange_rate 
                    ? ($ds->amount_paid ?? $ds->amount) * $ds->exchange_rate 
                    : ($ds->amount_paid ?? $ds->amount);
            });

        $monthRevenueTZS = $monthBookingRevenueTZS + $monthServiceRevenueTZS + $monthDayServiceRevenueTZS;
        // Recalculate USD for display based on total TZS
        $monthRevenueUSD = $this->exchangeRate > 0 ? ($monthRevenueTZS / $this->exchangeRate) : 0;

        // Chart Data (Last 7 Days)
        $chartLabels = [];
        $chartRevenue = [];
        $chartBookings = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $chartLabels[] = $date->format('D d');
            
            // Revenue (TZS)
            $dailyUSD = Booking::whereDate('created_at', $date)
                ->whereIn('payment_status', ['paid', 'partial'])
                ->sum('amount_paid');
            $chartRevenue[] = $dailyUSD * $this->exchangeRate;
            
            // Bookings
            $chartBookings[] = Booking::whereDate('created_at', $date)->count();
        }

        return view('dashboard.reports.index', [
            'role' => $user->role ?? 'manager',
            'userName' => $user->name ?? 'Manager',
            'userRole' => ucfirst($user->role ?? 'Manager'),
            'exchangeRate' => $this->exchangeRate,
            'todayBookings' => $todayBookings,
            'todayRevenueUSD' => $todayRevenueUSD,
            'todayRevenueTZS' => $todayRevenueTZS,
            'totalRooms' => $totalRooms,
            'occupiedRooms' => $occupiedRooms,
            'occupancyRate' => $occupancyRate,
            'monthRevenueUSD' => $monthRevenueUSD,
            'monthRevenueTZS' => $monthRevenueTZS,
            'chartLabels' => $chartLabels,
            'chartRevenue' => $chartRevenue,
            'chartBookings' => $chartBookings,
        ]);
    }

    /**
     * Check if user has access to reports
     */
    private function checkAccess()
    {
        $user = Auth::guard('staff')->user() ?? Auth::guard('guest')->user();
        
        if (!$user) {
            abort(403, 'Unauthorized access');
        }

        // Managers and Super Admins have full access
        if ($user->role === 'manager' || $user->role === 'super_admin') {
            return true;
        }

        // Reception can access operational reports only
        if ($user->role === 'reception') {
            return true; // Reception can view reports
        }

        abort(403, 'You do not have permission to access this report');
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
                if ($startDate && $endDate) {
                    return [
                        'start' => Carbon::parse($startDate)->startOfDay(),
                        'end' => Carbon::parse($endDate)->endOfDay(),
                        'label' => Carbon::parse($startDate)->format('M d') . ' - ' . Carbon::parse($endDate)->format('M d, Y')
                    ];
                }
                // Fallback to current month
                return [
                    'start' => $today->copy()->startOfMonth(),
                    'end' => $today->copy()->endOfMonth(),
                    'label' => $today->format('F Y')
                ];
                
            default:
                return [
                    'start' => $today->copy()->startOfMonth(),
                    'end' => $today->copy()->endOfMonth(),
                    'label' => $today->format('F Y')
                ];
        }
    }

    /**
     * Revenue Breakdown Report
     */
    public function revenueBreakdown(Request $request)
    {
        $this->checkAccess();
        
        $reportType = $request->get('report_type', 'monthly');
        $reportDate = $request->get('date', today()->format('Y-m-d'));
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $dateRange = $this->calculateDateRange($reportType, $reportDate, $startDate, $endDate);
        $start = $dateRange['start'];
        $end = $dateRange['end'];

        // Room bookings revenue
        $roomBookings = Booking::whereBetween('created_at', [$start, $end])
            ->whereIn('payment_status', ['paid', 'partial'])
            ->whereNotNull('amount_paid')
            ->where('amount_paid', '>', 0)
            ->get();
        $roomRevenueUSD = $roomBookings->sum('amount_paid');
        $roomRevenueTZS = $roomRevenueUSD * $this->exchangeRate;

        // Restaurant/Bar service requests revenue
        $serviceRequests = ServiceRequest::whereBetween('completed_at', [$start, $end])
            ->where('status', 'completed')
            ->get();
        $serviceRevenueTZS = $serviceRequests->sum('total_price_tsh');

        // Day services revenue
        $dayServices = DayService::whereBetween('service_date', [$start, $end])
            ->where('payment_status', 'paid')
            ->get();
        $dayServiceRevenueTZS = $dayServices->sum(function($ds) {
             // If exchange rate is recorded (International), amount is USD -> Convert to TZS
             // If no exchange rate (Local), amount is TZS already -> Use as is
             return $ds->exchange_rate 
                ? ($ds->amount_paid ?? $ds->amount) * $ds->exchange_rate 
                : ($ds->amount_paid ?? $ds->amount);
        });

        // Revenue by payment method - COMPREHENSIVE (Bookings + Services + Day Services)
        $paymentMethods = [];
        
        // Add Room Bookings
        foreach ($roomBookings as $b) {
            $method = ucfirst(trim($b->payment_method ?: 'Cash'));
            if (!isset($paymentMethods[$method])) {
                $paymentMethods[$method] = ['count' => 0, 'revenue_tzs' => 0];
            }
            $paymentMethods[$method]['count']++;
            $paymentMethods[$method]['revenue_tzs'] += ($b->amount_paid * $this->exchangeRate);
        }

        // Add Service Requests (Categorize all completed requests)
        foreach ($serviceRequests as $sr) {
            $method = ucfirst(trim($sr->payment_method ?: 'Cash'));
            if (!isset($paymentMethods[$method])) {
                $paymentMethods[$method] = ['count' => 0, 'revenue_tzs' => 0];
            }
            $paymentMethods[$method]['count']++;
            $paymentMethods[$method]['revenue_tzs'] += $sr->total_price_tsh;
        }

        // Add Day Services
        foreach ($dayServices as $ds) {
            $method = ucfirst(trim($ds->payment_method ?: 'Cash'));
            if (!isset($paymentMethods[$method])) {
                $paymentMethods[$method] = ['count' => 0, 'revenue_tzs' => 0];
            }
            $paymentMethods[$method]['count']++;
            $rev = $ds->exchange_rate ? ($ds->amount_paid ?? $ds->amount) * $ds->exchange_rate : ($ds->amount_paid ?? $ds->amount);
            $paymentMethods[$method]['revenue_tzs'] += $rev;
        }

        $revenueByPaymentMethod = collect($paymentMethods);

        // Revenue by guest type (Bookings + Services + Day Services)
        $guestTypeData = [];
        foreach ($roomBookings as $b) {
            $type = ucfirst(trim($b->guest_type ?: 'Tanzanian'));
            if (!isset($guestTypeData[$type])) {
                $guestTypeData[$type] = ['count' => 0, 'revenue_tzs' => 0];
            }
            $guestTypeData[$type]['count']++;
            $guestTypeData[$type]['revenue_tzs'] += ($b->amount_paid * $this->exchangeRate);
        }
        
        // Add Service Requests to Guest Type (Inherit from booking if available)
        foreach ($serviceRequests as $sr) {
            $type = 'Tanzanian'; // Default for walk-ins
            if ($sr->booking) {
                $type = ucfirst(trim($sr->booking->guest_type ?: 'Tanzanian'));
            }
            if (!isset($guestTypeData[$type])) {
                $guestTypeData[$type] = ['count' => 0, 'revenue_tzs' => 0];
            }
            $guestTypeData[$type]['count']++;
            $guestTypeData[$type]['revenue_tzs'] += $sr->total_price_tsh;
        }

        foreach ($dayServices as $ds) {
            $type = ucfirst(trim($ds->guest_type ?: 'Tanzanian'));
            if (!isset($guestTypeData[$type])) {
                $guestTypeData[$type] = ['count' => 0, 'revenue_tzs' => 0];
            }
            $guestTypeData[$type]['count']++;
            $rev = $ds->exchange_rate ? ($ds->amount_paid ?? $ds->amount) * $ds->exchange_rate : ($ds->amount_paid ?? $ds->amount);
            $guestTypeData[$type]['revenue_tzs'] += $rev;
        }
        $revenueByGuestType = collect($guestTypeData);

        // Daily revenue trend (All Sources)
        $trendData = [];
        
        // Add Booking Revenue to trend
        foreach ($roomBookings as $b) {
            $date = Carbon::parse($b->paid_at ?? $b->created_at)->format('Y-m-d');
            $trendData[$date] = ($trendData[$date] ?? 0) + ($b->amount_paid * $this->exchangeRate);
        }
        // Add Service Revenue to trend
        foreach ($serviceRequests as $sr) {
            $date = $sr->completed_at ? $sr->completed_at->format('Y-m-d') : $sr->created_at->format('Y-m-d');
            $trendData[$date] = ($trendData[$date] ?? 0) + $sr->total_price_tsh;
        }
        // Add Day Service Revenue to trend
        foreach ($dayServices as $ds) {
            $date = $ds->service_date->format('Y-m-d');
            $rev = $ds->exchange_rate ? ($ds->amount_paid ?? $ds->amount) * $ds->exchange_rate : ($ds->amount_paid ?? $ds->amount);
            $trendData[$date] = ($trendData[$date] ?? 0) + $rev;
        }

        $dailyRevenue = collect($trendData)->sortKeys();

        $totalRevenueTZS = $roomRevenueTZS + $serviceRevenueTZS + $dayServiceRevenueTZS;
        $totalRevenueUSD = $this->exchangeRate > 0 ? ($totalRevenueTZS / $this->exchangeRate) : 0;

        $user = Auth::guard('staff')->user() ?? Auth::guard('guest')->user();
        
        return view('dashboard.reports.revenue-breakdown', [
            'role' => $user->role ?? 'manager',
            'userName' => $user->name ?? 'Manager',
            'userRole' => ucfirst($user->role ?? 'Manager'),
            'reportType' => $reportType,
            'reportDate' => $reportDate,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'dateRange' => $dateRange,
            'exchangeRate' => $this->exchangeRate,
            'roomRevenueTZS' => $roomRevenueTZS,
            'serviceRevenueTZS' => $serviceRevenueTZS,
            'dayServiceRevenueTZS' => $dayServiceRevenueTZS,
            'totalRevenueTZS' => $totalRevenueTZS,
            'totalRevenueUSD' => $totalRevenueUSD,
            'revenueByPaymentMethod' => $revenueByPaymentMethod,
            'revenueByGuestType' => $revenueByGuestType,
            'dailyRevenue' => $dailyRevenue,
        ]);
    }

    /**
     * Profitability Analysis Report
     */
    public function profitability(Request $request)
    {
        $this->checkAccess();
        
        $reportType = $request->get('report_type', 'monthly');
        $reportDate = $request->get('date', today()->format('Y-m-d'));
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $dateRange = $this->calculateDateRange($reportType, $reportDate, $startDate, $endDate);
        $start = $dateRange['start'];
        $end = $dateRange['end'];

        // Total revenue
        $bookings = Booking::whereBetween('created_at', [$start, $end])
            ->whereIn('payment_status', ['paid', 'partial'])
            ->whereNotNull('amount_paid')
            ->where('amount_paid', '>', 0)
            ->get();
        $totalRevenueUSD = $bookings->sum('amount_paid');
        $totalRevenueTZS = $totalRevenueUSD * $this->exchangeRate;

        // Service requests revenue
        $serviceRequests = ServiceRequest::whereBetween('completed_at', [$start, $end])
            ->where('status', 'completed')
            ->get();
        $serviceRevenueTZS = $serviceRequests->sum('total_price_tsh');

        // Day services revenue
        $dayServices = DayService::whereBetween('service_date', [$start, $end])
            ->where('payment_status', 'paid')
            ->get();
        $dayServiceRevenueTZS = $dayServices->sum(function($ds) {
             // If exchange rate is recorded (International), amount is USD -> Convert to TZS
             // If no exchange rate (Local), amount is TZS already -> Use as is
             return $ds->exchange_rate 
                ? ($ds->amount_paid ?? $ds->amount) * $ds->exchange_rate 
                : ($ds->amount_paid ?? $ds->amount);
        });

        // Note: In the simplified menu system, COGS is tracked manually via shopping lists.
        // We will placeholder COGS at 0 for now as 'ingredients' relationship is removed.
        $cogsTZS = 0;

        // Total operational revenue
        $totalHotelIncomeTZS = $totalRevenueTZS + $serviceRevenueTZS + $dayServiceRevenueTZS;

        // Gross profit (Rooms + Services + Day Services)
        $grossProfitTZS = $totalHotelIncomeTZS - $cogsTZS;
        $grossProfitMargin = $totalHotelIncomeTZS > 0 
            ? round(($grossProfitTZS / $totalHotelIncomeTZS) * 100, 2) 
            : 0;

        // Profitability by room type
        $profitabilityByRoomType = $bookings->groupBy(function($booking) {
            return $booking->room->room_type ?? 'Unknown';
        })->map(function($group) {
            $revenue = $group->sum('amount_paid') * $this->exchangeRate;
            return [
                'count' => $group->count(),
                'revenue' => $revenue,
                'average_revenue' => $group->count() > 0 ? $revenue / $group->count() : 0
            ];
        });

        $user = Auth::guard('staff')->user() ?? Auth::guard('guest')->user();
        
        return view('dashboard.reports.profitability', [
            'role' => $user->role ?? 'manager',
            'userName' => $user->name ?? 'Manager',
            'userRole' => ucfirst($user->role ?? 'Manager'),
            'reportType' => $reportType,
            'reportDate' => $reportDate,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'dateRange' => $dateRange,
            'exchangeRate' => $this->exchangeRate,
            'roomRevenueTZS' => $totalRevenueTZS, // Standard room revenue
            'serviceRevenueTZS' => $serviceRevenueTZS,
            'dayServiceRevenueTZS' => $dayServiceRevenueTZS,
            'totalHotelIncomeTZS' => $totalHotelIncomeTZS, // Total of ALL
            'cogsTZS' => $cogsTZS,
            'grossProfitTZS' => $grossProfitTZS,
            'grossProfitMargin' => $grossProfitMargin,
            'profitabilityByRoomType' => $profitabilityByRoomType,
        ]);
    }

    /**
     * Cash Flow Report
     */
    public function cashFlow(Request $request)
    {
        $this->checkAccess();
        
        $reportType = $request->get('report_type', 'monthly');
        $reportDate = $request->get('date', today()->format('Y-m-d'));
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $dateRange = $this->calculateDateRange($reportType, $reportDate, $startDate, $endDate);
        $start = $dateRange['start'];
        $end = $dateRange['end'];

        // 1. Get Room Bookings revenue
        $roomBookings = Booking::whereBetween('created_at', [$start, $end])
            ->whereIn('payment_status', ['paid', 'partial'])
            ->whereNotNull('amount_paid')
            ->where('amount_paid', '>', 0)
            ->get();

        // 2. Get Service Requests revenue
        $serviceRequests = ServiceRequest::whereBetween('completed_at', [$start, $end])
            ->where('status', 'completed')
            ->get();

        // 3. Get Day Services revenue
        $dayServices = DayService::whereBetween('service_date', [$start, $end])
            ->where('payment_status', 'paid')
            ->get();

        // Aggregated Payment Method logic (matching revenueBreakdown)
        $paymentMethods = [];
        
        // Add Bookings
        foreach ($roomBookings as $b) {
            $method = ucfirst(trim($b->payment_method ?: 'Cash'));
            if (!isset($paymentMethods[$method])) {
                $paymentMethods[$method] = ['count' => 0, 'revenue_tzs' => 0];
            }
            $paymentMethods[$method]['count']++;
            $paymentMethods[$method]['revenue_tzs'] += ($b->amount_paid * $this->exchangeRate);
        }

        // Add Services
        foreach ($serviceRequests as $sr) {
            $method = ucfirst(trim($sr->payment_method ?: 'Cash'));
            if (!isset($paymentMethods[$method])) {
                $paymentMethods[$method] = ['count' => 0, 'revenue_tzs' => 0];
            }
            $paymentMethods[$method]['count']++;
            $paymentMethods[$method]['revenue_tzs'] += $sr->total_price_tsh;
        }

        // Add Day Services
        foreach ($dayServices as $ds) {
            $method = ucfirst(trim($ds->payment_method ?: 'Cash'));
            if (!isset($paymentMethods[$method])) {
                $paymentMethods[$method] = ['count' => 0, 'revenue_tzs' => 0];
            }
            $paymentMethods[$method]['count']++;
            $rev = $ds->exchange_rate ? ($ds->amount_paid ?? $ds->amount) * $ds->exchange_rate : ($ds->amount_paid ?? $ds->amount);
            $paymentMethods[$method]['revenue_tzs'] += $rev;
        }

        // Summarize Cash vs Non-Cash
        $cashCollectionsTZS = 0;
        $nonCashCollectionsTZS = 0;
        foreach ($paymentMethods as $method => $data) {
            if (strtolower($method) === 'cash') {
                $cashCollectionsTZS += $data['revenue_tzs'];
            } else {
                $nonCashCollectionsTZS += $data['revenue_tzs'];
            }
        }

        // Pending payments (from Bookings)
        $pendingPayments = Booking::whereBetween('created_at', [$start, $end])
            ->where('payment_status', 'pending')
            ->get();
        $pendingAmountTZS = $pendingPayments->sum('total_price') * $this->exchangeRate;

        // Outstanding receivables (partial payments from Bookings)
        $partialPayments = Booking::whereBetween('created_at', [$start, $end])
            ->where('payment_status', 'partial')
            ->whereNotNull('amount_paid')
            ->get();
        $outstandingTZS = $partialPayments->sum(function($booking) {
            return (($booking->total_price ?? 0) - ($booking->amount_paid ?? 0)) * $this->exchangeRate;
        });

        // Payment method distribution for view
        $paymentMethodDistribution = collect($paymentMethods)->map(function($data, $method) {
            return [
                'method' => $method,
                'count' => $data['count'],
                'total_tzs' => $data['revenue_tzs']
            ];
        })->values();

        // Daily cash flow trend (All sources)
        $trendData = [];
        foreach ($roomBookings as $b) {
            $date = Carbon::parse($b->paid_at ?? $b->created_at)->format('Y-m-d');
            $trendData[$date] = ($trendData[$date] ?? 0) + ($b->amount_paid * $this->exchangeRate);
        }
        foreach ($serviceRequests as $sr) {
            $date = $sr->completed_at ? $sr->completed_at->format('Y-m-d') : $sr->created_at->format('Y-m-d');
            $trendData[$date] = ($trendData[$date] ?? 0) + $sr->total_price_tsh;
        }
        foreach ($dayServices as $ds) {
            $date = $ds->service_date->format('Y-m-d');
            $rev = $ds->exchange_rate ? ($ds->amount_paid ?? $ds->amount) * $ds->exchange_rate : ($ds->amount_paid ?? $ds->amount);
            $trendData[$date] = ($trendData[$date] ?? 0) + $rev;
        }

        $dailyCashFlow = collect($trendData)->sortKeys();

        $user = Auth::guard('staff')->user() ?? Auth::guard('guest')->user();
        
        return view('dashboard.reports.cash-flow', [
            'role' => $user->role ?? 'manager',
            'userName' => $user->name ?? 'Manager',
            'userRole' => ucfirst($user->role ?? 'Manager'),
            'reportType' => $reportType,
            'reportDate' => $reportDate,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'dateRange' => $dateRange,
            'exchangeRate' => $this->exchangeRate,
            'cashCollectionsTZS' => $cashCollectionsTZS,
            'nonCashCollectionsTZS' => $nonCashCollectionsTZS,
            'pendingAmountTZS' => $pendingAmountTZS,
            'outstandingTZS' => $outstandingTZS,
            'dailyCashFlow' => $dailyCashFlow,
            'paymentMethodDistribution' => $paymentMethodDistribution,
        ]);
    }

    /**
     * Revenue Forecast Report
     */
    public function revenueForecast(Request $request)
    {
        $this->checkAccess();
        
        // Get historical data (last 6 months)
        $sixMonthsAgo = Carbon::now()->subMonths(6)->startOfMonth();
        $currentMonth = Carbon::now()->startOfMonth();

        // Historical monthly revenue
        $historicalRevenue = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthStart = Carbon::now()->subMonths($i)->startOfMonth();
            $monthEnd = Carbon::now()->subMonths($i)->endOfMonth();
            
            $monthBookings = Booking::whereBetween('created_at', [$monthStart, $monthEnd])
                ->whereIn('payment_status', ['paid', 'partial'])
                ->whereNotNull('amount_paid')
                ->where('amount_paid', '>', 0)
                ->get();
            
            $monthRevenueUSD = $monthBookings->sum('amount_paid');
            $monthRevenueTZS = $monthRevenueUSD * $this->exchangeRate;
            
            $historicalRevenue[] = [
                'month' => $monthStart->format('M Y'),
                'revenue' => $monthRevenueTZS,
                'bookings' => $monthBookings->count()
            ];
        }

        // Calculate average growth rate
        $revenues = array_column($historicalRevenue, 'revenue');
        $growthRates = [];
        for ($i = 1; $i < count($revenues); $i++) {
            if ($revenues[$i-1] > 0) {
                $growthRates[] = (($revenues[$i] - $revenues[$i-1]) / $revenues[$i-1]) * 100;
            }
        }
        $averageGrowthRate = count($growthRates) > 0 ? array_sum($growthRates) / count($growthRates) : 0;

        // Current month revenue (partial)
        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthBookings = Booking::where('created_at', '>=', $currentMonthStart)
            ->whereIn('payment_status', ['paid', 'partial'])
            ->whereNotNull('amount_paid')
            ->where('amount_paid', '>', 0)
            ->get();
        $currentMonthRevenueUSD = $currentMonthBookings->sum('amount_paid');
        $currentMonthRevenueTZS = $currentMonthRevenueUSD * $this->exchangeRate;
        $daysInMonth = Carbon::now()->daysInMonth;
        $daysElapsed = Carbon::now()->day;
        $projectedCurrentMonthRevenue = $daysElapsed > 0 
            ? ($currentMonthRevenueTZS / $daysElapsed) * $daysInMonth 
            : 0;

        // Forecast next 3 months
        $forecast = [];
        $lastRevenue = $revenues[count($revenues) - 1] ?? $currentMonthRevenueTZS;
        for ($i = 1; $i <= 3; $i++) {
            $forecastMonth = Carbon::now()->addMonths($i);
            $forecastRevenue = $lastRevenue * (1 + ($averageGrowthRate / 100));
            $lastRevenue = $forecastRevenue;
            
            $forecast[] = [
                'month' => $forecastMonth->format('M Y'),
                'revenue' => round($forecastRevenue, 0),
                'confidence' => $i === 1 ? 'High' : ($i === 2 ? 'Medium' : 'Low')
            ];
        }

        // Booking pipeline (upcoming bookings)
        $upcomingBookings = Booking::where('check_in', '>=', Carbon::now())
            ->where('status', '!=', 'cancelled')
            ->whereIn('payment_status', ['paid', 'partial', 'pending'])
            ->get();
        $pipelineRevenueUSD = $upcomingBookings->sum('total_price');
        $pipelineRevenueTZS = $pipelineRevenueUSD * $this->exchangeRate;

        $user = Auth::guard('staff')->user() ?? Auth::guard('guest')->user();
        
        return view('dashboard.reports.revenue-forecast', [
            'role' => $user->role ?? 'manager',
            'userName' => $user->name ?? 'Manager',
            'userRole' => ucfirst($user->role ?? 'Manager'),
            'exchangeRate' => $this->exchangeRate,
            'historicalRevenue' => $historicalRevenue,
            'averageGrowthRate' => round($averageGrowthRate, 2),
            'currentMonthRevenueTZS' => $currentMonthRevenueTZS,
            'projectedCurrentMonthRevenue' => round($projectedCurrentMonthRevenue, 0),
            'forecast' => $forecast,
            'pipelineRevenueTZS' => $pipelineRevenueTZS,
            'upcomingBookingsCount' => $upcomingBookings->count(),
        ]);
    }

    /**
     * Guest Satisfaction Report
     */
    public function guestSatisfaction(Request $request)
    {
        $this->checkAccess();
        
        $reportType = $request->get('report_type', 'monthly');
        $reportDate = $request->get('date', today()->format('Y-m-d'));
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $dateRange = $this->calculateDateRange($reportType, $reportDate, $startDate, $endDate);
        $start = $dateRange['start'];
        $end = $dateRange['end'];

        // Get feedbacks in date range
        $feedbacks = Feedback::whereBetween('created_at', [$start, $end])
            ->with('booking.room')
            ->get();

        // Overall statistics
        $totalFeedbacks = $feedbacks->count();
        $averageRating = $totalFeedbacks > 0 ? round($feedbacks->avg('rating'), 2) : 0;
        
        // Rating distribution
        $ratingDistribution = $feedbacks->groupBy('rating')->map->count();
        for ($i = 1; $i <= 5; $i++) {
            if (!isset($ratingDistribution[$i])) {
                $ratingDistribution[$i] = 0;
            }
        }
        $ratingDistribution = $ratingDistribution->sortKeys();

        // Category ratings
        $categoryRatings = [
            'room_quality' => [],
            'service' => [],
            'cleanliness' => [],
            'value' => []
        ];
        
        $feedbacks->each(function($feedback) use (&$categoryRatings) {
            $categories = $feedback->categories ?? [];
            foreach ($categoryRatings as $key => &$values) {
                if (isset($categories[$key])) {
                    $values[] = $categories[$key];
                }
            }
        });

        $averageCategoryRatings = [];
        foreach ($categoryRatings as $key => $values) {
            $averageCategoryRatings[$key] = count($values) > 0 
                ? round(array_sum($values) / count($values), 2) 
                : 0;
        }

        // Satisfaction trends (monthly)
        $monthlyRatings = $feedbacks->groupBy(function($feedback) {
            return Carbon::parse($feedback->created_at)->format('Y-m');
        })->map(function($monthFeedbacks) {
            return [
                'count' => $monthFeedbacks->count(),
                'average' => round($monthFeedbacks->avg('rating'), 2)
            ];
        });

        // Satisfaction by room type
        $satisfactionByRoomType = $feedbacks->groupBy(function($feedback) {
            return $feedback->booking->room->room_type ?? 'Unknown';
        })->map(function($group) {
            return [
                'count' => $group->count(),
                'average' => round($group->avg('rating'), 2)
            ];
        });

        // Common complaints (negative feedback)
        $negativeFeedbacks = $feedbacks->where('rating', '<=', 3);
        $commonComplaints = $negativeFeedbacks->pluck('comment')
            ->filter()
            ->map(function($comment) {
                return strtolower($comment);
            })
            ->toArray();

        // Positive feedback themes
        $positiveFeedbacks = $feedbacks->where('rating', '>=', 4);
        $positiveThemes = $positiveFeedbacks->pluck('comment')
            ->filter()
            ->map(function($comment) {
                return strtolower($comment);
            })
            ->toArray();

        // Response rate
        $totalCompletedBookings = Booking::whereBetween('check_out', [$start, $end])
            ->where('status', '!=', 'cancelled')
            ->count();
        $responseRate = $totalCompletedBookings > 0 
            ? round(($totalFeedbacks / $totalCompletedBookings) * 100, 2) 
            : 0;

        $user = Auth::guard('staff')->user() ?? Auth::guard('guest')->user();
        
        return view('dashboard.reports.guest-satisfaction', [
            'role' => $user->role ?? 'manager',
            'userName' => $user->name ?? 'Manager',
            'userRole' => ucfirst($user->role ?? 'Manager'),
            'reportType' => $reportType,
            'reportDate' => $reportDate,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'dateRange' => $dateRange,
            'totalFeedbacks' => $totalFeedbacks,
            'averageRating' => $averageRating,
            'ratingDistribution' => $ratingDistribution,
            'averageCategoryRatings' => $averageCategoryRatings,
            'monthlyRatings' => $monthlyRatings,
            'satisfactionByRoomType' => $satisfactionByRoomType,
            'commonComplaints' => $commonComplaints,
            'positiveThemes' => $positiveThemes,
            'responseRate' => $responseRate,
            'feedbacks' => $feedbacks->take(20), // Recent feedbacks for display
        ]);
    }

    /**
     * Daily Operations Report
     */
    public function dailyOperations(Request $request)
    {
        $this->checkAccess();
        
        $date = $request->get('date', today()->format('Y-m-d'));
        $selectedDate = Carbon::parse($date);
        $start = $selectedDate->copy()->startOfDay();
        $end = $selectedDate->copy()->endOfDay();
        $tomorrow = $selectedDate->copy()->addDay();

        // Today's bookings
        $todayBookings = Booking::whereBetween('created_at', [$start, $end])->get();
        $todayNewBookings = $todayBookings->count();
        $todayConfirmedBookings = $todayBookings->where('status', 'confirmed')->count();

        // Today's check-ins
        $todayCheckIns = Booking::whereBetween('checked_in_at', [$start, $end])->count();
        $todayCheckOuts = Booking::whereBetween('checked_out_at', [$start, $end])->count();

        // Today's revenue (Received today)
        $todayPaidBookings = Booking::whereBetween('paid_at', [$start, $end])
            ->whereIn('payment_status', ['paid', 'partial'])
            ->whereNotNull('amount_paid')
            ->where('amount_paid', '>', 0)
            ->get();
        $todayRevenueUSD = $todayPaidBookings->sum('amount_paid');
        $todayRevenueTZS = $todayRevenueUSD * $this->exchangeRate;

        // Today's service requests
    $todayServiceRequests = ServiceRequest::with('service')->whereBetween('requested_at', [$start, $end])->get();
    $todayServiceRequestsCount = $todayServiceRequests->count();
    $todayServiceRequestsCompleted = $todayServiceRequests->where('status', 'completed')->count();
    $todayServiceRevenueTZS = $todayServiceRequests->where('status', 'completed')->sum('total_price_tsh');

    // Revenue Breakdown (Bar vs Kitchen)
    $barCategories = ['drinks', 'alcoholic_beverage', 'non_alcoholic_beverage', 'water', 'juices', 'energy_drinks', 'spirits', 'wines', 'cocktails', 'hot_beverages', 'beers', 'liquor', 'whiskey', 'bar'];
    $kitchenCategories = ['food', 'breakfast', 'lunch', 'dinner', 'dessert', 'snacks', 'soup', 'salad', 'kitchen', 'restaurant'];
    
    $barRevenueTZS = $todayServiceRequests
        ->where('status', 'completed')
        ->filter(function($req) use ($barCategories) {
             return $req->service && (in_array(strtolower($req->service->category), $barCategories) || str_contains(strtolower($req->service_type), 'bar'));
        })
        ->sum('total_price_tsh');

    $kitchenRevenueTZS = $todayServiceRequests
        ->where('status', 'completed')
        ->filter(function($req) use ($kitchenCategories, $barCategories) {
             // If not bar, and matches kitchen categories or type contains kitchen/restaurant
             return $req->service && (in_array(strtolower($req->service->category), $kitchenCategories) || str_contains(strtolower($req->service_type), 'kitchen') || str_contains(strtolower($req->service_type), 'restaurant'));
        })
        ->sum('total_price_tsh');

    $otherServiceRevenueTZS = max(0, $todayServiceRevenueTZS - $barRevenueTZS - $kitchenRevenueTZS);

        // Today's day services
    $todayDayServices = DayService::whereDate('service_date', $selectedDate)->get();
    $todayDayServicesCount = $todayDayServices->count();
    $todayDayServicesPaid = $todayDayServices->where('payment_status', 'paid')->count();
    $todayDayServiceRevenueTZS = $todayDayServices->where('payment_status', 'paid')
        ->sum(function($ds) {
             // If exchange rate is recorded (International), amount is USD -> Convert to TZS
             // If no exchange rate (Local), amount is TZS already -> Use as is
             return $ds->exchange_rate 
                ? ($ds->amount_paid ?? $ds->amount) * $ds->exchange_rate 
                : ($ds->amount_paid ?? $ds->amount);
        });

        // Today's issues
        $todayIssues = \App\Models\IssueReport::whereBetween('created_at', [$start, $end])->get();
        $todayIssuesCount = $todayIssues->count();
        $todayIssuesResolved = $todayIssues->where('status', 'resolved')->count();

        // Current occupancy
        $totalRooms = Room::count();
        $occupiedRooms = Room::where('status', 'occupied')->count();
        $availableRooms = Room::where('status', 'available')->count();
        $cleaningMaintenanceRooms = Room::whereIn('status', ['to_be_cleaned', 'maintenance'])->count();
        $occupancyRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100, 1) : 0;

        // Grand Total Revenue (Bookings + Services + Day Services)
    $grandTotalRevenueTZS = $todayRevenueTZS + $todayServiceRevenueTZS + $todayDayServiceRevenueTZS;

        // Tomorrow's forecast
        $tomorrowCheckIns = Booking::whereDate('check_in', $tomorrow)
            ->where('status', '!=', 'cancelled')
            ->count();
        $tomorrowCheckOuts = Booking::whereDate('check_out', $tomorrow)
            ->where('status', '!=', 'cancelled')
            ->count();
        
        // Expected revenue tomorrow (Total price of bookings starting tomorrow + any due payments)
        $tomorrowExpectedRevenueUSD = Booking::whereDate('check_in', $tomorrow)
            ->where('status', '!=', 'cancelled')
            ->sum('total_price');
        $tomorrowExpectedRevenueTZS = $tomorrowExpectedRevenueUSD * $this->exchangeRate;

        // Pending tasks
        $pendingServiceRequests = ServiceRequest::where('status', 'pending')->count();
        $pendingIssues = \App\Models\IssueReport::where('status', '!=', 'resolved')->count();
        $pendingPayments = Booking::where('payment_status', 'pending')->count();

        $user = Auth::guard('staff')->user() ?? Auth::guard('guest')->user();
        
        return view('dashboard.reports.daily-operations', [
            'role' => $user->role ?? 'manager',
            'userName' => $user->name ?? 'Manager',
            'userRole' => ucfirst($user->role ?? 'Manager'),
            'selectedDate' => $selectedDate,
            'exchangeRate' => $this->exchangeRate,
            'todayNewBookings' => $todayNewBookings,
            'todayConfirmedBookings' => $todayConfirmedBookings,
            'todayCheckIns' => $todayCheckIns,
            'todayCheckOuts' => $todayCheckOuts,
            'todayRevenueTZS' => $todayRevenueTZS, // Booking Revenue
        'grandTotalRevenueTZS' => $grandTotalRevenueTZS,
        'barRevenueTZS' => $barRevenueTZS,
        'kitchenRevenueTZS' => $kitchenRevenueTZS,
        'otherServiceRevenueTZS' => $otherServiceRevenueTZS,
        'todayServiceRequestsCount' => $todayServiceRequestsCount,
            'todayServiceRequestsCompleted' => $todayServiceRequestsCompleted,
            'todayServiceRevenueTZS' => $todayServiceRevenueTZS,
            'todayDayServicesCount' => $todayDayServicesCount,
            'todayDayServicesPaid' => $todayDayServicesPaid,
            'todayDayServiceRevenueTZS' => $todayDayServiceRevenueTZS,
            'todayIssuesCount' => $todayIssuesCount,
            'todayIssuesResolved' => $todayIssuesResolved,
            'totalRooms' => $totalRooms,
            'occupiedRooms' => $occupiedRooms,
            'availableRooms' => $availableRooms,
            'cleaningMaintenanceRooms' => $cleaningMaintenanceRooms,
            'occupancyRate' => $occupancyRate,
            'tomorrowCheckIns' => $tomorrowCheckIns,
            'tomorrowCheckOuts' => $tomorrowCheckOuts,
            'tomorrowExpectedRevenueTZS' => $tomorrowExpectedRevenueTZS,
            'pendingServiceRequests' => $pendingServiceRequests,
            'pendingIssues' => $pendingIssues,
            'pendingPayments' => $pendingPayments,
        ]);
    }

    /**
     * Weekly Performance Report
     */
    public function weeklyPerformance(Request $request)
    {
        $this->checkAccess();
        
        $weekStart = $request->get('week_start', Carbon::now()->startOfWeek()->format('Y-m-d'));
        $weekStartDate = Carbon::parse($weekStart)->startOfWeek();
        $weekEndDate = $weekStartDate->copy()->endOfWeek();
        $lastWeekStart = $weekStartDate->copy()->subWeek();
        $lastWeekEnd = $lastWeekStart->copy()->endOfWeek();

        // Current week stats
        $weekBookings = Booking::whereBetween('created_at', [$weekStartDate, $weekEndDate])->get();
        $weekBookingsCount = $weekBookings->count();
        $weekConfirmedBookings = $weekBookings->where('status', 'confirmed')->count();
        $weekCancelledBookings = $weekBookings->where('status', 'cancelled')->count();

        $weekPaidBookings = Booking::whereBetween('created_at', [$weekStartDate, $weekEndDate])
            ->whereIn('payment_status', ['paid', 'partial'])
            ->whereNotNull('amount_paid')
            ->where('amount_paid', '>', 0)
            ->get();
        $weekRevenueUSD = $weekPaidBookings->sum('amount_paid');
        $weekRevenueTZS = $weekRevenueUSD * $this->exchangeRate;

        $weekCheckIns = Booking::whereBetween('checked_in_at', [$weekStartDate, $weekEndDate])->count();
        $weekCheckOuts = Booking::whereBetween('checked_out_at', [$weekStartDate, $weekEndDate])->count();

        $weekServiceRequests = ServiceRequest::whereBetween('requested_at', [$weekStartDate, $weekEndDate])->get();
        $weekServiceRequestsCount = $weekServiceRequests->count();
        $weekServiceRequestsCompleted = $weekServiceRequests->where('status', 'completed')->count();
        $weekServiceRevenueTZS = $weekServiceRequests->where('status', 'completed')->sum('total_price_tsh');

        $weekDayServices = DayService::whereBetween('service_date', [$weekStartDate, $weekEndDate])->get();
        $weekDayServicesCount = $weekDayServices->count();
        $weekDayServiceRevenueTZS = $weekDayServices->where('payment_status', 'paid')
            ->sum(function($ds) {
                return ($ds->amount_paid ?? $ds->amount) * ($ds->exchange_rate ?? $this->exchangeRate);
            });

        // Last week stats (for comparison)
        $lastWeekBookings = Booking::whereBetween('created_at', [$lastWeekStart, $lastWeekEnd])->get();
        $lastWeekBookingsCount = $lastWeekBookings->count();

        $lastWeekPaidBookings = Booking::whereBetween('created_at', [$lastWeekStart, $lastWeekEnd])
            ->whereIn('payment_status', ['paid', 'partial'])
            ->whereNotNull('amount_paid')
            ->where('amount_paid', '>', 0)
            ->get();
        $lastWeekRevenueUSD = $lastWeekPaidBookings->sum('amount_paid');
        $lastWeekRevenueTZS = $lastWeekRevenueUSD * $this->exchangeRate;

        // Calculate changes
        $revenueChange = $lastWeekRevenueTZS > 0 
            ? round((($weekRevenueTZS - $lastWeekRevenueTZS) / $lastWeekRevenueTZS) * 100, 1) 
            : 0;
        $bookingsChange = $lastWeekBookingsCount > 0 
            ? round((($weekBookingsCount - $lastWeekBookingsCount) / $lastWeekBookingsCount) * 100, 1) 
            : 0;

        // Daily breakdown
        $dailyBreakdown = [];
        for ($i = 0; $i < 7; $i++) {
            $day = $weekStartDate->copy()->addDays($i);
            $dayStart = $day->copy()->startOfDay();
            $dayEnd = $day->copy()->endOfDay();
            
            $dayBookings = Booking::whereBetween('created_at', [$dayStart, $dayEnd])
                ->whereIn('payment_status', ['paid', 'partial'])
                ->whereNotNull('amount_paid')
                ->where('amount_paid', '>', 0)
                ->get();
            
            $dailyBreakdown[] = [
                'day' => $day->format('D'),
                'date' => $day->format('M d'),
                'bookings' => $dayBookings->count(),
                'revenue' => $dayBookings->sum('amount_paid') * $this->exchangeRate
            ];
        }

        // Top performing days
        $topDays = collect($dailyBreakdown)->sortByDesc('revenue')->take(3);

        // Challenges
        $challenges = [];
        if ($weekCancelledBookings > 0 && ($weekCancelledBookings / $weekBookingsCount) > 0.1) {
            $challenges[] = [
                'type' => 'warning',
                'message' => "High cancellation rate: " . round(($weekCancelledBookings / $weekBookingsCount) * 100, 1) . "%"
            ];
        }
        if ($revenueChange < -10) {
            $challenges[] = [
                'type' => 'danger',
                'message' => "Revenue decreased by " . abs($revenueChange) . "% compared to last week"
            ];
        }

        // Highlights
        $highlights = [];
        if ($revenueChange > 10) {
            $highlights[] = "Revenue increased by {$revenueChange}% compared to last week";
        }
        if ($weekServiceRequestsCompleted > 0) {
            $highlights[] = "Completed {$weekServiceRequestsCompleted} service requests";
        }
        if ($weekCheckIns > 0) {
            $highlights[] = "Checked in {$weekCheckIns} guests";
        }

        // Next week outlook
        $nextWeekStart = $weekStartDate->copy()->addWeek();
        $nextWeekBookings = Booking::whereDate('check_in', '>=', $nextWeekStart)
            ->whereDate('check_in', '<=', $nextWeekStart->copy()->endOfWeek())
            ->where('status', '!=', 'cancelled')
            ->count();
        $nextWeekExpectedRevenueUSD = Booking::whereDate('check_in', '>=', $nextWeekStart)
            ->whereDate('check_in', '<=', $nextWeekStart->copy()->endOfWeek())
            ->where('status', '!=', 'cancelled')
            ->sum('total_price');
        $nextWeekExpectedRevenueTZS = $nextWeekExpectedRevenueUSD * $this->exchangeRate;

        $user = Auth::guard('staff')->user() ?? Auth::guard('guest')->user();
        
        return view('dashboard.reports.weekly-performance', [
            'role' => $user->role ?? 'manager',
            'userName' => $user->name ?? 'Manager',
            'userRole' => ucfirst($user->role ?? 'Manager'),
            'weekStartDate' => $weekStartDate,
            'weekEndDate' => $weekEndDate,
            'exchangeRate' => $this->exchangeRate,
            'weekBookingsCount' => $weekBookingsCount,
            'weekConfirmedBookings' => $weekConfirmedBookings,
            'weekCancelledBookings' => $weekCancelledBookings,
            'weekRevenueTZS' => $weekRevenueTZS,
            'weekCheckIns' => $weekCheckIns,
            'weekCheckOuts' => $weekCheckOuts,
            'weekServiceRequestsCount' => $weekServiceRequestsCount,
            'weekServiceRequestsCompleted' => $weekServiceRequestsCompleted,
            'weekServiceRevenueTZS' => $weekServiceRevenueTZS,
            'weekDayServicesCount' => $weekDayServicesCount,
            'weekDayServiceRevenueTZS' => $weekDayServiceRevenueTZS,
            'lastWeekRevenueTZS' => $lastWeekRevenueTZS,
            'revenueChange' => $revenueChange,
            'bookingsChange' => $bookingsChange,
            'dailyBreakdown' => $dailyBreakdown,
            'topDays' => $topDays,
            'challenges' => $challenges,
            'highlights' => $highlights,
            'nextWeekBookings' => $nextWeekBookings,
            'nextWeekExpectedRevenueTZS' => $nextWeekExpectedRevenueTZS,
        ]);
    }

    /**
     * Room Occupancy Report - Booking-specific report
     */
    public function roomOccupancy(Request $request)
    {
        $this->checkAccess();
        
        $period = $request->get('period', 'month');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        
        // Map period to reportType
        $reportTypeMap = [
            'today' => 'daily',
            'week' => 'weekly',
            'month' => 'monthly',
            'year' => 'yearly',
            'custom' => 'custom'
        ];
        $reportType = $reportTypeMap[$period] ?? 'monthly';
        
        // Calculate date range
        $dateRange = $this->calculateDateRange($reportType, $period == 'today' ? today()->format('Y-m-d') : null, $startDate, $endDate);
        $start = $dateRange['start'];
        $end = $dateRange['end'];
        
        // Get all rooms
        $rooms = Room::with(['bookings' => function($query) use ($start, $end) {
            $query->whereBetween('check_in', [$start, $end])
                  ->where('status', '!=', 'cancelled');
        }])->get();
        
        // Calculate occupancy by room type
        $roomTypes = Room::select('room_type', DB::raw('count(*) as total'))
            ->groupBy('room_type')
            ->get();
        
        $occupancyByType = [];
        foreach ($roomTypes as $type) {
            $typeRooms = Room::where('room_type', $type->room_type)->pluck('id');
            $bookings = Booking::whereIn('room_id', $typeRooms)
                ->whereBetween('check_in', [$start, $end])
                ->where('status', '!=', 'cancelled')
                ->get();
            
            $totalDays = $type->total * $start->diffInDays($end);
            $occupiedDays = 0;
            foreach ($bookings as $booking) {
                $checkIn = Carbon::parse($booking->check_in);
                $checkOut = Carbon::parse($booking->check_out);
                $occupiedDays += $checkIn->diffInDays($checkOut);
            }
            
            $occupancyByType[] = [
                'type' => $type->room_type,
                'total_rooms' => $type->total,
                'occupancy_rate' => $totalDays > 0 ? round(($occupiedDays / $totalDays) * 100, 1) : 0,
                'revenue_usd' => $bookings->whereIn('payment_status', ['paid', 'partial'])->sum('amount_paid'),
            ];
        }
        
        // Top performing rooms
        $topRooms = Booking::whereBetween('check_in', [$start, $end])
            ->whereIn('payment_status', ['paid', 'partial'])
            ->whereNotNull('amount_paid')
            ->where('amount_paid', '>', 0)
            ->whereNotNull('room_id')
            ->select('room_id', DB::raw('count(*) as booking_count'), 
                     DB::raw('sum(COALESCE(amount_paid, 0)) as total_revenue_usd'))
            ->groupBy('room_id')
            ->orderBy('total_revenue_usd', 'desc')
            ->limit(10)
            ->get()
            ->map(function($item) {
                $room = Room::find($item->room_id);
                return [
                    'room' => $room,
                    'booking_count' => $item->booking_count,
                    'revenue_usd' => $item->total_revenue_usd,
                    'revenue_tzs' => $item->total_revenue_usd * $this->exchangeRate,
                ];
            })
            ->filter(function($item) {
                return $item['room'] !== null; // Filter out null rooms
            });
        
        // Overall occupancy rate
        $totalRooms = Room::count();
        $occupiedRooms = Room::where('status', 'occupied')->count();
        $overallOccupancyRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100, 1) : 0;
        
        $user = Auth::guard('staff')->user() ?? Auth::guard('guest')->user();
        
        return view('dashboard.reports.room-occupancy', [
            'role' => $user->role ?? 'manager',
            'userName' => $user->name ?? 'Manager',
            'userRole' => ucfirst($user->role ?? 'Manager'),
            'period' => $period,
            'startDate' => $start->format('Y-m-d'),
            'endDate' => $end->format('Y-m-d'),
            'exchangeRate' => $this->exchangeRate,
            'occupancyByType' => $occupancyByType,
            'topRooms' => $topRooms,
            'overallOccupancyRate' => $overallOccupancyRate,
            'totalRooms' => $totalRooms,
            'occupiedRooms' => $occupiedRooms,
        ]);
    }

    /**
     * Booking Performance Report - Booking-specific report
     */
    public function bookingPerformance(Request $request)
    {
        $this->checkAccess();
        
        $period = $request->get('period', 'month');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        
        // Map period to reportType
        $reportTypeMap = [
            'today' => 'daily',
            'week' => 'weekly',
            'month' => 'monthly',
            'year' => 'yearly',
            'custom' => 'custom'
        ];
        $reportType = $reportTypeMap[$period] ?? 'monthly';
        
        // Calculate date range
        $dateRange = $this->calculateDateRange($reportType, $period == 'today' ? today()->format('Y-m-d') : null, $startDate, $endDate);
        $start = $dateRange['start'];
        $end = $dateRange['end'];
        
        // Booking statistics
        $totalBookings = Booking::whereBetween('created_at', [$start, $end])->count();
        $confirmedBookings = Booking::whereBetween('created_at', [$start, $end])
            ->where('status', 'confirmed')
            ->count();
        $cancelledBookings = Booking::whereBetween('created_at', [$start, $end])
            ->where('status', 'cancelled')
            ->count();
        $checkedInBookings = Booking::whereBetween('checked_in_at', [$start, $end])
            ->whereNotNull('checked_in_at')
            ->count();
        $checkedOutBookings = Booking::whereBetween('checked_out_at', [$start, $end])
            ->whereNotNull('checked_out_at')
            ->count();
        
        // Revenue from bookings
        $paidBookings = Booking::whereBetween('created_at', [$start, $end])
            ->whereIn('payment_status', ['paid', 'partial'])
            ->whereNotNull('amount_paid')
            ->where('amount_paid', '>', 0)
            ->get();
        $totalRevenueUSD = $paidBookings->sum('amount_paid');
        $totalRevenueTZS = $totalRevenueUSD * $this->exchangeRate;
        
        // Average booking value
        $avgBookingValueUSD = $paidBookings->count() > 0 ? $totalRevenueUSD / $paidBookings->count() : 0;
        $avgBookingValueTZS = $avgBookingValueUSD * $this->exchangeRate;
        
        // Booking lead time (days in advance)
        $bookingsWithLeadTime = Booking::whereBetween('created_at', [$start, $end])
            ->whereNotNull('check_in')
            ->get()
            ->map(function($booking) {
                $createdAt = Carbon::parse($booking->created_at)->startOfDay();
                $checkIn = Carbon::parse($booking->check_in)->startOfDay();
                
                // If check-in is in the future relative to booking date, lead time > 0
                // If walk-in or late entry, lead time is 0
                $diff = $createdAt->diffInDays($checkIn, false);
                return $diff > 0 ? $diff : 0;
            });
        $avgLeadTime = $bookingsWithLeadTime->count() > 0 
            ? round($bookingsWithLeadTime->avg(), 1) 
            : 0;
        
        // Cancellation rate
        $cancellationRate = $totalBookings > 0 
            ? round(($cancelledBookings / $totalBookings) * 100, 1) 
            : 0;
        
        // Booking status breakdown
        $statusBreakdown = Booking::whereBetween('created_at', [$start, $end])
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();
        
        $user = Auth::guard('staff')->user() ?? Auth::guard('guest')->user();
        
        return view('dashboard.reports.booking-performance', [
            'role' => $user->role ?? 'manager',
            'userName' => $user->name ?? 'Manager',
            'userRole' => ucfirst($user->role ?? 'Manager'),
            'period' => $period,
            'startDate' => $start->format('Y-m-d'),
            'endDate' => $end->format('Y-m-d'),
            'exchangeRate' => $this->exchangeRate,
            'totalBookings' => $totalBookings,
            'confirmedBookings' => $confirmedBookings,
            'cancelledBookings' => $cancelledBookings,
            'checkedInBookings' => $checkedInBookings,
            'checkedOutBookings' => $checkedOutBookings,
            'totalRevenueUSD' => $totalRevenueUSD,
            'totalRevenueTZS' => $totalRevenueTZS,
            'avgBookingValueUSD' => $avgBookingValueUSD,
            'avgBookingValueTZS' => $avgBookingValueTZS,
            'avgLeadTime' => $avgLeadTime,
            'cancellationRate' => $cancellationRate,
            'statusBreakdown' => $statusBreakdown,
        ]);
    }

    /**
     * General Report - High-level overview for management with graphs and charts
     */
    public function general(Request $request)
    {
        $this->checkAccess();
        
        $period = $request->get('period', 'month');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        
        // Map period to reportType
        $reportTypeMap = [
            'today' => 'daily',
            'week' => 'weekly',
            'month' => 'monthly',
            'year' => 'yearly',
            'custom' => 'custom'
        ];
        $reportType = $reportTypeMap[$period] ?? 'monthly';
        
        // Calculate date range
        $dateRange = $this->calculateDateRange($reportType, $period == 'today' ? today()->format('Y-m-d') : null, $startDate, $endDate);
        $start = $dateRange['start'];
        $end = $dateRange['end'];
        
        // 1. Room Revenue
        $roomBookings = Booking::whereBetween('created_at', [$start, $end])
            ->whereIn('payment_status', ['paid', 'partial'])
            ->whereNotNull('amount_paid')
            ->where('amount_paid', '>', 0)
            ->get();
        $roomRevenueTZS = $roomBookings->sum('amount_paid') * $this->exchangeRate;
        
        // 2. Service Revenue
        $serviceRequests = ServiceRequest::whereBetween('completed_at', [$start, $end])
            ->where('status', 'completed')
            ->get();
        $serviceRevenueTZS = $serviceRequests->sum('total_price_tsh');

        // 3. Day Service Revenue
        $dayServices = DayService::whereBetween('service_date', [$start, $end])
            ->where('payment_status', 'paid')
            ->get();
        $dayServiceRevenueTZS = $dayServices->sum(function($ds) {
            return $ds->exchange_rate ? ($ds->amount_paid ?? $ds->amount) * $ds->exchange_rate : ($ds->amount_paid ?? $ds->amount);
        });

        $totalRevenueTZS = $roomRevenueTZS + $serviceRevenueTZS + $dayServiceRevenueTZS;
        $totalRevenueUSD = $this->exchangeRate > 0 ? ($totalRevenueTZS / $this->exchangeRate) : 0;
        
        $totalBookings = Booking::whereBetween('created_at', [$start, $end])->count();
        $totalRooms = Room::count();
        $occupiedRooms = Room::where('status', 'occupied')->count();
        $occupancyRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100, 1) : 0;

        // --- TREND DATA GENERATION ---
        $trendLabels = [];
        $trendRevenue = [];
        $trendBookings = [];

        // If 'year' or long custom range, group by month. Otherwise by day.
        $diffDays = $start->diffInDays($end);
        if ($reportType === 'yearly' || $diffDays > 60) {
            for ($i = 0; $i < 12; $i++) {
                $mStart = $start->copy()->addMonths($i)->startOfMonth();
                $mEnd = $mStart->copy()->endOfMonth();
                if ($mStart > $end) break;

                $trendLabels[] = $mStart->format('M Y');
                
                $mRoomRev = Booking::whereBetween('created_at', [$mStart, $mEnd])->whereIn('payment_status', ['paid', 'partial'])->sum('amount_paid') * $this->exchangeRate;
                $mServRev = ServiceRequest::whereBetween('completed_at', [$mStart, $mEnd])->where('status', 'completed')->sum('total_price_tsh');
                $mDayRev = DayService::whereBetween('service_date', [$mStart, $mEnd])->where('payment_status', 'paid')->get()->sum(function($ds) {
                    return $ds->exchange_rate ? ($ds->amount_paid ?? $ds->amount) * $ds->exchange_rate : ($ds->amount_paid ?? $ds->amount);
                });

                $trendRevenue[] = $mRoomRev + $mServRev + $mDayRev;
                $trendBookings[] = Booking::whereBetween('created_at', [$mStart, $mEnd])->count();
            }
        } else {
            // Group by Day
            $current = $start->copy();
            while ($current <= $end) {
                $dLabel = $current->format('d M');
                $trendLabels[] = $dLabel;

                $dStart = $current->copy()->startOfDay();
                $dEnd = $current->copy()->endOfDay();

                $dRoomRev = Booking::whereBetween('created_at', [$dStart, $dEnd])->whereIn('payment_status', ['paid', 'partial'])->sum('amount_paid') * $this->exchangeRate;
                $dServRev = ServiceRequest::whereBetween('completed_at', [$dStart, $dEnd])->where('status', 'completed')->sum('total_price_tsh');
                $dDayRev = DayService::whereBetween('service_date', [$dStart, $dEnd])->where('payment_status', 'paid')->get()->sum(function($ds) {
                    return $ds->exchange_rate ? ($ds->amount_paid ?? $ds->amount) * $ds->exchange_rate : ($ds->amount_paid ?? $ds->amount);
                });

                $trendRevenue[] = $dRoomRev + $dServRev + $dDayRev;
                $trendBookings[] = Booking::whereBetween('created_at', [$dStart, $dEnd])->count();

                $current->addDay();
            }
        }
        
        $user = Auth::guard('staff')->user() ?? Auth::guard('guest')->user();
        
        return view('dashboard.reports.general', [
            'role' => $user->role ?? 'manager',
            'userName' => $user->name ?? 'Manager',
            'userRole' => ucfirst($user->role ?? 'Manager'),
            'period' => $period,
            'startDate' => $start->format('Y-m-d'),
            'endDate' => $end->format('Y-m-d'),
            'exchangeRate' => $this->exchangeRate,
            'totalRevenueUSD' => $totalRevenueUSD,
            'totalRevenueTZS' => $totalRevenueTZS,
            'roomRevenueTZS' => $roomRevenueTZS,
            'serviceRevenueTZS' => $serviceRevenueTZS,
            'dayServiceRevenueTZS' => $dayServiceRevenueTZS,
            'totalBookings' => $totalBookings,
            'totalRooms' => $totalRooms,
            'occupiedRooms' => $occupiedRooms,
            'occupancyRate' => $occupancyRate,
            'trendLabels' => $trendLabels,
            'trendRevenue' => $trendRevenue,
            'trendBookings' => $trendBookings,
        ]);
    }

    /**
     * Payment Methods Report
     */
    public function paymentMethods(Request $request)
    {
        $this->checkAccess();
        return view('dashboard.reports.other.payment-methods', $this->getReportBaseData());
    }

    /**
     * Satisfaction Report
     */
    public function satisfaction(Request $request)
    {
        $this->checkAccess();
        return view('dashboard.reports.other.satisfaction', $this->getReportBaseData());
    }

    /**
     * Period Comparison Report
     */
    public function periodComparison(Request $request)
    {
        $this->checkAccess();
        return view('dashboard.reports.other.period-comparison', $this->getReportBaseData());
    }

    /**
     * Role-Based Performance Report
     */
    public function rolePerformance(Request $request)
    {
        $this->checkAccess();
        return view('dashboard.reports.other.role-performance', $this->getReportBaseData());
    }

    /**
     * Staff Activity Log Report
     */
    public function staffActivity(Request $request)
    {
        $this->checkAccess();
        return view('dashboard.reports.other.staff-activity', $this->getReportBaseData());
    }

    /**
     * Staff Productivity Report
     */
    public function staffProductivity(Request $request)
    {
        $this->checkAccess();
        return view('dashboard.reports.other.staff-productivity', $this->getReportBaseData());
    }

    /**
     * Issue Resolution Report
     */
    public function issueResolution(Request $request)
    {
        $this->checkAccess();
        return view('dashboard.reports.other.issue-resolution', $this->getReportBaseData());
    }

    /**
     * Service Response Time Report
     */
    public function serviceResponseTime(Request $request)
    {
        $this->checkAccess();
        return view('dashboard.reports.other.service-response-time', $this->getReportBaseData());
    }

    /**
     * Stock Valuation Report
     */
    public function stockValuation(Request $request)
    {
        $this->checkAccess();
        return view('dashboard.reports.other.stock-valuation', $this->getReportBaseData());
    }

    /**
     * Food Cost Analysis Report
     */
    public function foodCostAnalysis(Request $request)
    {
        $this->checkAccess();
        return view('dashboard.reports.other.food-cost-analysis', $this->getReportBaseData());
    }

    /**
     * Bar Sales Analysis Report
     */
    public function barSalesAnalysis(Request $request)
    {
        $this->checkAccess();
        return view('dashboard.reports.other.bar-sales-analysis', $this->getReportBaseData());
    }

    /**
     * Menu Performance Report
     */
    public function menuPerformance(Request $request)
    {
        $this->checkAccess();
        return view('dashboard.reports.other.menu-performance', $this->getReportBaseData());
    }

    /**
     * Guest Demographics Report
     */
    public function guestDemographics(Request $request)
    {
        $this->checkAccess();
        return view('dashboard.reports.other.guest-demographics', $this->getReportBaseData());
    }

    /**
     * Get base data for reports
     */
    private function getReportBaseData()
    {
        $user = Auth::guard('staff')->user() ?? Auth::guard('guest')->user();
        return [
            'role' => $user->role ?? 'manager',
            'userName' => $user->name ?? 'Manager',
            'userRole' => ucfirst($user->role ?? 'Manager'),
            'exchangeRate' => $this->exchangeRate,
        ];
    }
}
