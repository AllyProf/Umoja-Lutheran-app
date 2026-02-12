<?php

namespace App\Http\Controllers;

use App\Services\CurrencyExchangeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ExchangeRateController extends Controller
{
    /**
     * Show exchange rate page with trends
     */
    public function index(Request $request)
    {
        $currencyService = new CurrencyExchangeService();
        
        // Clear cache if refresh requested
        if ($request->has('refresh')) {
            $currencyService->clearCache();
            $currencyService->clearHistoryCache($request->get('days', 30));
        }
        
        // Get current rate
        $currentRate = $currencyService->getUsdToTshRate();
        
        // Get days parameter (default: 30)
        $days = $request->get('days', 30);
        $days = min(max($days, 7), 365); // Limit between 7 and 365 days
        
        // Get historical rates
        $historicalRates = $currencyService->getHistoricalRates($days);
        
        // Get statistics
        $stats = $currencyService->getExchangeRateStats($days);
        
        // Determine user role for sidebar
        $user = auth()->guard('staff')->user() ?? auth()->guard('guest')->user();
        
        // Map role correctly for sidebar
        if ($user instanceof \App\Models\Guest) {
            $role = 'customer'; // Guests see customer sidebar
        } elseif ($user instanceof \App\Models\Staff) {
            $rawRole = $user->role ?? '';
            $normalizedRole = strtolower(str_replace([' ', '_'], '', trim($rawRole)));
            
            if ($normalizedRole === 'superadmin' || $rawRole === 'super_admin' || strtolower($rawRole) === 'super admin') {
                $role = 'super_admin';
            } elseif ($normalizedRole === 'manager' || $rawRole === 'manager') {
                $role = 'manager';
            } elseif ($normalizedRole === 'reception' || $rawRole === 'reception') {
                $role = 'reception';
            } else {
                $role = $rawRole;
            }
        } else {
            $role = $user->role ?? 'guest';
        }
        
        return view('dashboard.exchange-rates', [
            'role' => $role,
            'userName' => $user->name ?? 'User',
            'userRole' => ucfirst($role === 'customer' ? 'Customer' : ($role === 'guest' ? 'Customer' : $role)),
            'currentRate' => $currentRate,
            'historicalRates' => $historicalRates,
            'stats' => $stats,
            'days' => $days,
        ]);
    }
}

