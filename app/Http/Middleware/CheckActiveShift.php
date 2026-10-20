<?php

namespace App\Http\Middleware;

use App\Support\CounterShiftContext;
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

        if (!$user) {
            return $next($request);
        }

        // Restaurant assistant sells under a counter's open shift (no personal shift)
        if (CounterShiftContext::isProxySeller($user)) {
            $operating = CounterShiftContext::resolveOperatingShift($user);
            if (!$operating) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Please select a counter with an open shift before selling.',
                    ], 403);
                }

                return redirect()
                    ->route('bar-keeper.dashboard')
                    ->with('error', 'Please select a counter with an open shift before selling.');
            }

            return $next($request);
        }

        // Enforce active shift for reception / counter
        if (in_array($user->role, ['reception', 'bar_keeper'], true)) {
            $activeShift = ShiftClosure::where('staff_id', $user->id)
                ->where('status', 'active')
                ->first();

            if (!$activeShift) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You do not have an open shift. Please open a shift first before continuing.'
                    ], 403);
                }

                $redirectRoute = $user->role === 'reception' ? 'reception.shift-management' : 'bar-keeper.dashboard';

                return redirect()->route($redirectRoute)->with('error', 'You cannot do this until you open a shift first.');
            }
        }

        return $next($request);
    }
}
