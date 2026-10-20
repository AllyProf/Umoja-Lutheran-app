<?php

namespace App\Support;

use App\Models\ShiftClosure;
use App\Models\Staff;
use Illuminate\Support\Collection;

class CounterShiftContext
{
    public const SESSION_KEY = 'proxy_counter_shift_id';

    /**
     * Roles that sell on behalf of a counter (no personal shift).
     */
    public static function isProxySeller(?Staff $user): bool
    {
        if (!$user) {
            return false;
        }

        $role = strtolower(str_replace([' ', '-'], '_', trim((string) ($user->role ?? ''))));

        return in_array($role, ['restaurant_manager', 'assistant_manager'], true);
    }

    /**
     * Can view all counters' order summaries (not limited to own approved_by).
     */
    public static function canSeeAllCounterOrders(?Staff $user): bool
    {
        if (!$user) {
            return false;
        }

        $role = strtolower(str_replace([' ', '-'], '_', trim((string) ($user->role ?? ''))));

        return in_array($role, [
            'manager',
            'super_admin',
            'admin',
            'cashier',
            'restaurant_manager',
            'assistant_manager',
        ], true);
    }

    /**
     * Active shifts belonging to counter staff only.
     */
    public static function activeCounterShifts(): Collection
    {
        return ShiftClosure::with('staff')
            ->where('status', 'active')
            ->whereHas('staff', function ($q) {
                $q->where(function ($inner) {
                    $inner->where('role', 'bar_keeper')
                        ->orWhere('role', 'LIKE', '%bar%')
                        ->orWhere('role', 'LIKE', '%counter%');
                });
            })
            ->orderBy('opened_at')
            ->get();
    }

    /**
     * Shift the current user is operating under (own shift, or selected counter shift).
     */
    public static function resolveOperatingShift(Staff $user, ?int $requestedShiftId = null): ?ShiftClosure
    {
        if (self::isProxySeller($user)) {
            $shiftId = $requestedShiftId ?: session(self::SESSION_KEY);
            if (!$shiftId) {
                return null;
            }

            $shift = ShiftClosure::with('staff')
                ->where('id', $shiftId)
                ->where('status', 'active')
                ->first();

            if (!$shift || !self::isCounterStaff($shift->staff)) {
                session()->forget(self::SESSION_KEY);
                return null;
            }

            return $shift;
        }

        return ShiftClosure::with('staff')
            ->where('staff_id', $user->id)
            ->where('status', 'active')
            ->first();
    }

    public static function isCounterStaff(?Staff $staff): bool
    {
        if (!$staff) {
            return false;
        }

        $role = strtolower((string) ($staff->role ?? ''));

        return str_contains($role, 'bar') || str_contains($role, 'counter') || $role === 'bar_keeper';
    }

    /**
     * Staff id that owns the sale for shift totals (counter), not the proxy placer.
     */
    public static function saleOwnerStaffId(Staff $actor, ShiftClosure $operatingShift): int
    {
        if (self::isProxySeller($actor)) {
            return (int) $operatingShift->staff_id;
        }

        return (int) $actor->id;
    }

    /**
     * Meta + note bits when a proxy places/serves an order for a counter.
     */
    public static function proxyPlacementMeta(Staff $actor, ShiftClosure $operatingShift, array $existing = []): array
    {
        if (!self::isProxySeller($actor)) {
            return $existing;
        }

        $counter = $operatingShift->staff;

        return array_merge($existing, [
            'placed_by_staff_id' => (int) $actor->id,
            'placed_by_name' => $actor->name,
            'on_behalf_of_staff_id' => (int) $operatingShift->staff_id,
            'on_behalf_of_name' => $counter->name ?? 'Counter',
            'on_behalf_shift_id' => (int) $operatingShift->id,
        ]);
    }

    public static function proxyPlacementNote(Staff $actor, ShiftClosure $operatingShift): string
    {
        if (!self::isProxySeller($actor)) {
            return '';
        }

        $counterName = $operatingShift->staff->name ?? 'Counter';

        return " | Placed by {$actor->name} on behalf of {$counterName}";
    }

    public static function placedByLabel(?array $meta): ?string
    {
        $name = $meta['placed_by_name'] ?? null;

        return $name ? (string) $name : null;
    }
}
