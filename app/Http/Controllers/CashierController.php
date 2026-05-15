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
                ->sum('amount_submitted_tzs'),
            'today_reception_collected' => DayService::whereDate('paid_at', $today)
                ->whereNotNull('accountant_verified_at') // Repurposing this as collection marker
                ->sum('amount_paid'),
            'pending_restaurant_handovers' => ShiftClosure::where('status', 'pending_cashier')->count(),
            'pending_reception_collections' => DayService::where('payment_status', 'paid')
                ->whereNull('accountant_verified_at')
                ->count(),
        ];

        // Recent collections
        $recentHandovers = ShiftClosure::with('staff')
            ->whereIn('status', ['pending_cashier', 'received'])
            ->orderByDesc('closed_at')
            ->limit(5)
            ->get();

        return view('dashboard.cashier.dashboard', [
            'stats' => $stats,
            'recentHandovers' => $recentHandovers,
            'role' => 'cashier',
            'userName' => Auth::guard('staff')->user()->name ?? 'Cashier',
            'userRole' => 'Cashier',
        ]);
    }

    /**
     * View pending shift handovers from restaurant/counter
     */
    public function shiftHandovers()
    {
        $handovers = ShiftClosure::with(['staff', 'receiver'])
            ->where('status', 'pending_cashier')
            ->orderByDesc('closed_at')
            ->paginate(15);

        $history = ShiftClosure::with(['staff', 'receiver'])
            ->where('status', 'received')
            ->orderByDesc('closed_at')
            ->limit(10)
            ->get();

        return view('dashboard.cashier.shift-handovers', [
            'handovers' => $handovers,
            'history' => $history,
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

        // Build the base query: Group by date for Day Services
        $query = DayService::select(
            'service_date',
            DB::raw('COUNT(id) as total_services'),
            DB::raw('SUM(CASE WHEN payment_status = "paid" THEN amount_paid ELSE 0 END) as total_revenue'),
            DB::raw('MAX(accountant_verified_at) as verified_at'),
            DB::raw('MAX(cashier_collected_at) as collected_at'),
            DB::raw('SUM(CASE WHEN payment_status = "paid" AND cashier_collected_at IS NULL THEN 1 ELSE 0 END) as uncollected_count'),
            DB::raw('SUM(CASE WHEN payment_status = "paid" AND accountant_verified_at IS NULL THEN 1 ELSE 0 END) as unverified_count')
        )
            ->groupBy('service_date')
            ->orderByDesc('service_date');

        // Apply date range filter
        if ($dateFrom) {
            $query->where('service_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->where('service_date', '<=', $dateTo);
        }

        if ($tab === 'unverified') {
            $query->havingRaw('uncollected_count > 0');
        } elseif ($tab === 'verified') {
            $query->havingRaw('uncollected_count = 0')->havingRaw('total_revenue > 0');
        }

        $dailyRevenues = $query->paginate(15)->appends($request->only(['tab', 'date_from', 'date_to']));

        // Stats — always global (no date filter) so top widgets show full picture
        $stats = [
            'unverified_days' => DayService::select('service_date')
                ->where('payment_status', 'paid')
                ->whereNull('cashier_collected_at')
                ->groupBy('service_date')
                ->get()
                ->count(),
            'total_unverified_cash' => DayService::where('payment_status', 'paid')
                ->whereNull('cashier_collected_at')
                ->sum('amount_paid'),
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

        $affectedRows = DayService::where('service_date', $date)
            ->where('payment_status', 'paid')
            ->whereNull('cashier_collected_at')
            ->update([
                'cashier_collected_at' => now(),
                'cashier_id' => Auth::guard('staff')->id(),
            ]);

        if ($affectedRows > 0) {
            return back()->with('success', "Revenue for " . Carbon::parse($date)->format('M d, Y') . " collected from Reception. It is now ready for Accountant verification.");
        }

        return back()->with('info', "No uncollected paid services found for " . Carbon::parse($date)->format('M d, Y') . ".");
    }

    /**
     * View summary of funds ready for Accountant
     */
    public function accountantHandovers()
    {
        $shifts = ShiftClosure::with('staff')
            ->where('status', 'received')
            ->get();

        $dayServices = DayService::select(
            'service_date',
            DB::raw('SUM(amount_paid) as total_revenue')
        )
            ->where('payment_status', 'paid')
            ->whereNotNull('cashier_collected_at')
            ->whereNull('accountant_verified_at')
            ->groupBy('service_date')
            ->get();

        return view('dashboard.cashier.accountant-handovers', [
            'shifts' => $shifts,
            'dayServices' => $dayServices,
            'role' => 'cashier',
            'userName' => Auth::guard('staff')->user()->name ?? 'Cashier',
            'userRole' => 'Cashier',
        ]);
    }
}
