<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\ShiftClosure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckActiveShift
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::guard('staff')->user();

        // Enforce active shift for specific roles
        if ($user && in_array($user->role, ['reception', 'bar_keeper'])) {
            $activeShift = ShiftClosure::where('staff_id', $user->id)
                ->where('status', 'active')
                ->first();

            if (!$activeShift) {
                // Return JSON response for AJAX requests
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Huna shift iliyofunguliwa. Tafadhali fungua shift kwanza kabla ya kuendelea.'
                    ], 403);
                }

                // Redirect with error message for web requests
                $redirectRoute = $user->role === 'reception' ? 'reception.shift-management' : 'bar-keeper.dashboard';
                
                return redirect()->route($redirectRoute)->with('error', 'Huwezi kufanya kazi hii mpaka ufungue shift kwanza.');
            }
        }

        return $next($request);
    }
}
