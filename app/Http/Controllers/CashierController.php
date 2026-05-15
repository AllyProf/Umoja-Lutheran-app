<?php

namespace App\Http\Controllers;

use App\Models\ShiftClosure;
use App\Models\ServiceRequest;
use App\Models\DayService;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CashierController extends Controller
{
    /**
     * Cashier Dashboard
     */
    public function dashboard()
    {
        $today = Carbon::today();

        // Statistics for today
        $stats = [
            'today_restaurant_collected' => ShiftClosure::whereDate('closed_at', $today)
                ->where('status', 'received')
                ->whereHas('staff', function($q) {
                    $q->where('role', 'LIKE', '%bar%')->orWhere('role', 'LIKE', '%counter%');
                })
                ->sum('amount_submitted_tzs'),
            'today_reception_collected' => ShiftClosure::whereDate('closed_at', $today)
                ->where('status', 'received')
                ->whereHas('staff', function($q) {
                    $q->where('role', 'reception');
                })
                ->sum('amount_submitted_tzs'),
            'pending_restaurant_handovers' => ShiftClosure::where('status', 'pending_cashier')
                ->whereHas('staff', function($q) {
                    $q->where('role', 'LIKE', '%bar%')->orWhere('role', 'LIKE', '%counter%');
                })
                ->count(),
            'pending_reception_handovers' => ShiftClosure::where('status', 'pending_cashier')
                ->whereHas('staff', function($q) {
                    $q->where('role', 'reception');
                })
                ->count(),
            'unverified_revenue' => DayService::where('payment_status', 'paid')
                ->whereNull('accountant_verified_at')
                ->count(),
        ];

        // Recent Restaurant Handovers
        $recentRestaurantHandovers = ShiftClosure::with('staff')
            ->whereIn('status', ['pending_cashier', 'received'])
            ->whereHas('staff', function($q) {
                $q->where('role', 'LIKE', '%bar%')->orWhere('role', 'LIKE', '%counter%');
            })
            ->orderByDesc('closed_at')
            ->limit(5)
            ->get();

        // Recent Reception Handovers
        $recentReceptionHandovers = ShiftClosure::with('staff')
            ->whereIn('status', ['pending_cashier', 'received'])
            ->whereHas('staff', function($q) {
                $q->where('role', 'reception');
            })
            ->orderByDesc('closed_at')
            ->limit(5)
            ->get();

        return view('dashboard.cashier.dashboard', [
            'stats' => $stats,
            'recentRestaurantHandovers' => $recentRestaurantHandovers,
            'recentReceptionHandovers' => $recentReceptionHandovers,
            'role' => 'cashier',
            'userName' => Auth::guard('staff')->user()->name ?? 'Cashier',
            'userRole' => 'Cashier',
        ]);
    }

    /**
     * View pending shift handovers from restaurant/counter
     */
    public function shiftHandovers(Request $request)
    {
        $type = $request->get('type', 'restaurant');
        
        $query = ShiftClosure::with(['staff', 'receiver'])
            ->where('status', 'pending_cashier');
            
        if ($type === 'reception') {
            $query->whereHas('staff', function($q) {
                $q->where('role', 'reception');
            });
        } else {
            $query->whereHas('staff', function($q) {
                $q->where('role', 'LIKE', '%bar%')->orWhere('role', 'LIKE', '%counter%');
            });
        }
        
        $handovers = $query->orderByDesc('closed_at')->paginate(15);

        $historyQuery = ShiftClosure::with(['staff', 'receiver'])
            ->where('status', 'received');
            
        if ($type === 'reception') {
            $historyQuery->whereHas('staff', function($q) {
                $q->where('role', 'reception');
            });
        } else {
            $historyQuery->whereHas('staff', function($q) {
                $q->where('role', 'LIKE', '%bar%')->orWhere('role', 'LIKE', '%counter%');
            });
        }
        
        $history = $historyQuery->orderByDesc('closed_at')->limit(10)->get();

        return view('dashboard.cashier.shift-handovers', [
            'handovers' => $handovers,
            'history' => $history,
            'type' => $type,
            'role' => 'cashier',
            'userName' => Auth::guard('staff')->user()->name ?? 'Cashier',
            'userRole' => 'Cashier',
        ]);
    }

    /**
     * Acknowledge/Receive a shift handover
     */
    public function acknowledgeShiftClosure(Request $request, ShiftClosure $shiftClosure)
    {
        $shiftClosure->update([
            'status' => 'received',
            'receiver_id' => Auth::guard('staff')->id(),
            'notes' => $shiftClosure->notes . "\n[Received by Cashier at " . now() . "]"
        ]);

        return redirect()->back()->with('success', 'Shift cash handover received and acknowledged successfully. You can now submit it to the Accountant.');
    }

    /**
     * Submit received shift cash to Accountant
     */
    public function submitShiftToAccountant(ShiftClosure $shiftClosure)
    {
        if ($shiftClosure->status !== 'received') {
            return redirect()->back()->with('error', 'Only received shifts can be submitted to the Accountant.');
        }

        $shiftClosure->update([
            'status' => 'pending_accountant',
            'notes' => $shiftClosure->notes . "\n[Submitted to Accountant by Cashier at " . now() . "]"
        ]);

        return redirect()->back()->with('success', 'Shift funds submitted to Accountant for final verification.');
    }

    /**
     * View individual sales for a specific shift closure
     */
    public function viewShiftSales(ShiftClosure $shiftClosure)
    {
        $staff = $shiftClosure->staff;
        
        $sales       = collect();
        $dayServices = collect();
        $bookings    = collect();

        if ($staff->role === 'reception' || $staff->role === 'manager') {
            // For reception, get day services and bookings
            $dayServices = DayService::where('shift_closure_id', $shiftClosure->id)->get();
            $bookings    = Booking::with('room')->where('shift_closure_id', $shiftClosure->id)->get();
        } else {
            // For counter (bar/restaurant), get service requests
            $sales = ServiceRequest::with(['booking.room', 'service'])
                ->where('shift_closure_id', $shiftClosure->id)
                ->get();
        }

        return view('dashboard.cashier.shift-sales', [
            'shiftClosure' => $shiftClosure,
            'sales'        => $sales,
            'dayServices'  => $dayServices,
            'bookings'     => $bookings,
            'role'         => 'cashier',
            'userName'     => Auth::guard('staff')->user()->name ?? 'Cashier',
            'userRole'     => 'Cashier',
        ]);
    }

    /**
     * View collections from Reception (Day Services)
     */
    public function receptionCollections(Request $request)
    {
        $tab       = $request->get('tab', 'unverified');
        $dateFrom  = $request->get('date_from');
        $dateTo    = $request->get('date_to');

        // 1. Fetch Day Service Revenues by Date
        $dsQuery = DayService::select(
            'service_date as date',
            DB::raw('COUNT(id) as ds_count'),
            DB::raw('SUM(CASE WHEN payment_status = "paid" THEN amount_paid ELSE 0 END) as ds_revenue'),
            DB::raw('SUM(CASE WHEN payment_status = "paid" AND cashier_collected_at IS NULL THEN 1 ELSE 0 END) as ds_uncollected'),
            DB::raw('SUM(CASE WHEN payment_status = "paid" AND accountant_verified_at IS NULL THEN 1 ELSE 0 END) as ds_unverified')
        )
        ->where('payment_status', 'paid');

        if ($dateFrom) { $dsQuery->where('service_date', '>=', $dateFrom); }
        if ($dateTo)   { $dsQuery->where('service_date', '<=', $dateTo); }

        $dsDaily = $dsQuery->groupBy('service_date')->get()->keyBy('date');

        // 2. Fetch Booking Revenues by Date
        $bkQuery = Booking::select(
            DB::raw('DATE(paid_at) as date'),
            DB::raw('COUNT(id) as bk_count'),
            DB::raw('SUM(amount_paid) as bk_revenue'),
            DB::raw('SUM(CASE WHEN cashier_collected_at IS NULL THEN 1 ELSE 0 END) as bk_uncollected'),
            DB::raw('SUM(CASE WHEN accountant_verified_at IS NULL THEN 1 ELSE 0 END) as bk_unverified')
        )
        ->where('payment_status', 'paid')
        ->whereNotNull('paid_at');

        if ($dateFrom) { $bkQuery->whereDate('paid_at', '>=', $dateFrom); }
        if ($dateTo)   { $bkQuery->whereDate('paid_at', '<=', $dateTo); }

        $bkDaily = $bkQuery->groupBy(DB::raw('DATE(paid_at)'))->get()->keyBy('date');

        // 3. Merge All Dates
        $allDates = $dsDaily->keys()->merge($bkDaily->keys())->unique()->sortDesc();

        $mergedDaily = $allDates->map(function($date) use ($dsDaily, $bkDaily) {
            $ds = $dsDaily->get($date);
            $bk = $bkDaily->get($date);

            return (object) [
                'date' => $date,
                'ds_count' => $ds->ds_count ?? 0,
                'ds_revenue' => $ds->ds_revenue ?? 0,
                'ds_uncollected' => $ds->ds_uncollected ?? 0,
                'ds_unverified' => $ds->ds_unverified ?? 0,
                'bk_count' => $bk->bk_count ?? 0,
                'bk_revenue' => $bk->bk_revenue ?? 0,
                'bk_uncollected' => $bk->bk_uncollected ?? 0,
                'bk_unverified' => $bk->bk_unverified ?? 0,
                'total_revenue' => ($ds->ds_revenue ?? 0) + ($bk->bk_revenue ?? 0),
                'total_uncollected' => ($ds->ds_uncollected ?? 0) + ($bk->bk_uncollected ?? 0),
                'total_unverified' => ($ds->ds_unverified ?? 0) + ($bk->bk_unverified ?? 0),
            ];
        });

        // 4. Filter by Tab
        if ($tab === 'unverified') {
            $mergedDaily = $mergedDaily->filter(fn($item) => $item->total_uncollected > 0);
        } elseif ($tab === 'verified') {
            $mergedDaily = $mergedDaily->filter(fn($item) => $item->total_uncollected == 0 && $item->total_revenue > 0);
        }

        // 5. Pagination (Manual)
        $perPage = 15;
        $page = $request->get('page', 1);
        $dailyRevenues = new \Illuminate\Pagination\LengthAwarePaginator(
            $mergedDaily->forPage($page, $perPage),
            $mergedDaily->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Stats — always global (no date filter) so top widgets show full picture
        $stats = [
            'unverified_days' => $mergedDaily->filter(fn($item) => $item->total_uncollected > 0)->count(),
            'total_unverified_cash' => DayService::where('payment_status', 'paid')->whereNull('cashier_collected_at')->sum('amount_paid'),
            'total_unverified_rooms' => Booking::where('payment_status', 'paid')->whereNull('cashier_collected_at')->sum('amount_paid'),
        ];

        // Revenue breakdown by service type — filtered by date range
        $breakdownQuery = DayService::select(
                'service_type',
                DB::raw('COUNT(id) as count'),
                DB::raw('SUM(amount_paid) as revenue')
            )
            ->where('payment_status', 'paid')
            ->whereNull('cashier_collected_at');

        if ($dateFrom) { $breakdownQuery->where('service_date', '>=', $dateFrom); }
        if ($dateTo)   { $breakdownQuery->where('service_date', '<=', $dateTo); }

        $serviceBreakdown = $breakdownQuery->groupBy('service_type')->get()
            ->mapWithKeys(function ($item) {
                $label = match (true) {
                    str_contains($item->service_type, 'swimming')   => 'Swimming',
                    str_contains($item->service_type, 'parking')    => 'Parking',
                    str_contains($item->service_type, 'garden')     => 'Garden',
                    str_contains($item->service_type, 'conference') => 'Conference Room',
                    str_contains($item->service_type, 'ceremony') ||
                    str_contains($item->service_type, 'ceremory')   => 'Ceremony / Events',
                    str_contains($item->service_type, 'projector')  => 'Projector',
                    str_contains($item->service_type, 'music')      => 'Music / Sound',
                    default => ucfirst(str_replace('_', ' ', $item->service_type)),
                };
                return [$label => ['count' => $item->count, 'revenue' => $item->revenue]];
            });

        // Room booking revenue — filtered by paid_at date range
        $roomQuery = Booking::where('payment_status', 'paid')
            ->whereNull('cashier_collected_at')
            ->whereNotNull('amount_paid');

        if ($dateFrom) { $roomQuery->whereDate('paid_at', '>=', $dateFrom); }
        if ($dateTo)   { $roomQuery->whereDate('paid_at', '<=', $dateTo); }

        $roomRevenue = $roomQuery->selectRaw('COUNT(id) as count, SUM(amount_paid) as revenue')->first();

        return view('dashboard.cashier.reception-collections', [
            'dailyRevenues'    => $dailyRevenues,
            'tab'              => $tab,
            'stats'            => $stats,
            'serviceBreakdown' => $serviceBreakdown,
            'roomRevenue'      => $roomRevenue,
            'dateFrom'         => $dateFrom,
            'dateTo'           => $dateTo,
            'role'             => 'cashier',
            'userName'         => Auth::guard('staff')->user()->name ?? 'Cashier',
            'userRole'         => 'Cashier',
        ]);
    }

    /**
     * Verify/Collect revenue for a specific date from Reception
     */
    public function verifyReceptionRevenue(Request $request)
    {
        $request->validate([
            'service_date' => 'required|date',
        ]);

        $date = $request->service_date;
        $staffId = Auth::guard('staff')->id();

        // 1. Update Day Services
        $dsAffected = DayService::where('service_date', $date)
            ->where('payment_status', 'paid')
            ->whereNull('cashier_collected_at')
            ->update([
                'cashier_collected_at' => now(),
                'cashier_id' => $staffId,
            ]);

        // 2. Update Bookings (using paid_at date)
        $bkAffected = Booking::whereDate('paid_at', $date)
            ->where('payment_status', 'paid')
            ->whereNull('cashier_collected_at')
            ->update([
                'cashier_collected_at' => now(),
                'cashier_id' => $staffId,
            ]);

        if ($dsAffected > 0 || $bkAffected > 0) {
            $msg = "Revenue for " . Carbon::parse($date)->format('M d, Y') . " collected from Reception.";
            if ($dsAffected > 0) $msg .= " ($dsAffected Day Services)";
            if ($bkAffected > 0) $msg .= " ($bkAffected Room Bookings)";
            return back()->with('success', $msg . " It is now ready for Accountant verification.");
        }

        return back()->with('info', "No uncollected paid revenue found for " . Carbon::parse($date)->format('M d, Y') . ".");
    }

    /**
     * View summary of funds ready for Accountant
     */
    public function accountantHandovers(Request $request)
    {
        $tab = $request->get('tab', 'pending');

        if ($tab === 'history') {
            // 1. History: Restaurant/Counter Shifts (submitted and verified by accountant)
            $shifts = ShiftClosure::with('staff')
                ->where('status', 'verified')
                ->whereHas('staff', function($q) {
                    $q->where('role', 'LIKE', '%bar%')->orWhere('role', 'LIKE', '%counter%');
                })
                ->orderByDesc('updated_at')
                ->limit(20)
                ->get();

            // 2. History: Reception Revenue (verified by accountant)
            $dsQuery = DayService::select(
                'service_date as date',
                DB::raw('SUM(amount_paid) as ds_revenue')
            )
            ->where('payment_status', 'paid')
            ->whereNotNull('accountant_verified_at')
            ->groupBy('service_date');

            $bkQuery = Booking::select(
                DB::raw('DATE(paid_at) as date'),
                DB::raw('SUM(amount_paid) as bk_revenue')
            )
            ->where('payment_status', 'paid')
            ->whereNotNull('accountant_verified_at')
            ->groupBy(DB::raw('DATE(paid_at)'));

            $dsResults = $dsQuery->get()->keyBy('date');
            $bkResults = $bkQuery->get()->keyBy('date');
            
            $allDates = $dsResults->keys()->merge($bkResults->keys())->unique()->sortDesc();

            $receptionRevenue = $allDates->map(function($date) use ($dsResults, $bkResults) {
                return (object) [
                    'date' => $date,
                    'ds_revenue' => $dsResults->get($date)->ds_revenue ?? 0,
                    'bk_revenue' => $bkResults->get($date)->bk_revenue ?? 0,
                    'total_revenue' => ($dsResults->get($date)->ds_revenue ?? 0) + ($bkResults->get($date)->bk_revenue ?? 0),
                ];
            });
        } else {
            // 1. Pending: Restaurant/Counter Shifts (collected but not submitted to accountant)
            $shifts = ShiftClosure::with('staff')
                ->where('status', 'received')
                ->whereHas('staff', function($q) {
                    $q->where('role', 'LIKE', '%bar%')->orWhere('role', 'LIKE', '%counter%');
                })
                ->orderByDesc('updated_at')
                ->get();

            // 2. Pending: Reception Revenue (Day Services + Bookings) collected but not verified by accountant
            $dsQuery = DayService::select(
                'service_date as date',
                DB::raw('SUM(amount_paid) as ds_revenue')
            )
            ->where('payment_status', 'paid')
            ->whereNotNull('cashier_collected_at')
            ->whereNull('accountant_verified_at')
            ->groupBy('service_date');

            $bkQuery = Booking::select(
                DB::raw('DATE(paid_at) as date'),
                DB::raw('SUM(amount_paid) as bk_revenue')
            )
            ->where('payment_status', 'paid')
            ->whereNotNull('cashier_collected_at')
            ->whereNull('accountant_verified_at')
            ->groupBy(DB::raw('DATE(paid_at)'));

            $dsResults = $dsQuery->get()->keyBy('date');
            $bkResults = $bkQuery->get()->keyBy('date');
            
            $allDates = $dsResults->keys()->merge($bkResults->keys())->unique()->sortDesc();

            $receptionRevenue = $allDates->map(function($date) use ($dsResults, $bkResults) {
                return (object) [
                    'date' => $date,
                    'ds_revenue' => $dsResults->get($date)->ds_revenue ?? 0,
                    'bk_revenue' => $bkResults->get($date)->bk_revenue ?? 0,
                    'total_revenue' => ($dsResults->get($date)->ds_revenue ?? 0) + ($bkResults->get($date)->bk_revenue ?? 0),
                ];
            });
        }

        // Stats for Widgets (Always show pending for quick oversight)
        $pendingShifts = ShiftClosure::where('status', 'received')->sum('amount_submitted_tzs');
        $dsPending = DayService::where('payment_status', 'paid')->whereNotNull('cashier_collected_at')->whereNull('accountant_verified_at')->sum('amount_paid');
        $bkPending = Booking::where('payment_status', 'paid')->whereNotNull('cashier_collected_at')->whereNull('accountant_verified_at')->sum('amount_paid');

        $stats = [
            'total_shifts_cash' => $pendingShifts,
            'total_reception_cash' => $dsPending + $bkPending,
            'pending_shifts_count' => ShiftClosure::where('status', 'received')->count(),
            'pending_reception_days' => $receptionRevenue->count(), // This count might vary if tab is history, but widgets should focus on pending
        ];

        return view('dashboard.cashier.accountant-handovers', [
            'shifts' => $shifts,
            'receptionRevenue' => $receptionRevenue,
            'stats' => $stats,
            'tab' => $tab,
            'role' => 'cashier',
            'userName' => Auth::guard('staff')->user()->name ?? 'Cashier',
            'userRole' => 'Cashier',
        ]);
    }
}
