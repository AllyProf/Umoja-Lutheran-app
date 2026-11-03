<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Use Counter as the only front-of-house sales role.
     * Waiter is removed; existing waiter/bar_keeper staff become counter.
     */
    public function up(): void
    {
        $now = now();

        $waiter = DB::table('roles')->where('name', 'waiter')->first();
        $counter = DB::table('roles')->where('name', 'counter')->first();
        $barKeeper = DB::table('roles')->where('name', 'bar_keeper')->first();

        if ($waiter && !$counter) {
            DB::table('roles')->where('id', $waiter->id)->update([
                'name' => 'counter',
                'slug' => 'counter',
                'display_name' => 'Counter',
                'description' => 'Counter staff who open shifts, take orders, and process restaurant/bar sales.',
                'updated_at' => $now,
            ]);
            $counter = DB::table('roles')->where('name', 'counter')->first();
        } elseif (!$counter) {
            $id = DB::table('roles')->insertGetId([
                'name' => 'counter',
                'slug' => 'counter',
                'display_name' => 'Counter',
                'description' => 'Counter staff who open shifts, take orders, and process restaurant/bar sales.',
                'is_system' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $counter = DB::table('roles')->where('id', $id)->first();

            // Copy bar_keeper permissions onto counter when available
            if ($barKeeper && $counter) {
                $permIds = DB::table('role_permission')
                    ->where('role_id', $barKeeper->id)
                    ->pluck('permission_id');
                foreach ($permIds as $permissionId) {
                    $exists = DB::table('role_permission')
                        ->where('role_id', $counter->id)
                        ->where('permission_id', $permissionId)
                        ->exists();
                    if (!$exists) {
                        DB::table('role_permission')->insert([
                            'role_id' => $counter->id,
                            'permission_id' => $permissionId,
                        ]);
                    }
                }
            }
        } else {
            DB::table('roles')->where('id', $counter->id)->update([
                'display_name' => 'Counter',
                'description' => 'Counter staff who open shifts, take orders, and process restaurant/bar sales.',
                'updated_at' => $now,
            ]);
        }

        // Any leftover waiter role row (if counter already existed separately)
        if ($waiter = DB::table('roles')->where('name', 'waiter')->first()) {
            if ($counter) {
                $waiterPerms = DB::table('role_permission')->where('role_id', $waiter->id)->pluck('permission_id');
                foreach ($waiterPerms as $permissionId) {
                    $exists = DB::table('role_permission')
                        ->where('role_id', $counter->id)
                        ->where('permission_id', $permissionId)
                        ->exists();
                    if (!$exists) {
                        DB::table('role_permission')->insert([
                            'role_id' => $counter->id,
                            'permission_id' => $permissionId,
                        ]);
                    }
                }
                DB::table('role_permission')->where('role_id', $waiter->id)->delete();
            }
            DB::table('roles')->where('id', $waiter->id)->delete();
        }

        // Staff accounts: waiter + bar_keeper → counter
        DB::table('staffs')->whereIn('role', ['waiter', 'bar_keeper', 'bar keeper', 'bartender'])
            ->update(['role' => 'counter', 'updated_at' => $now]);

        // Soft-hide legacy bar_keeper role from staff forms (keep row for history)
        if ($barKeeper) {
            DB::table('roles')->where('id', $barKeeper->id)->update([
                'display_name' => 'Bar Keeper (legacy)',
                'description' => 'Legacy role — use Counter instead.',
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        $now = now();
        $counter = DB::table('roles')->where('name', 'counter')->first();

        if ($counter) {
            DB::table('roles')->where('id', $counter->id)->update([
                'name' => 'waiter',
                'slug' => 'waiter',
                'display_name' => 'Waiter',
                'description' => 'Staff responsible for taking guest orders and processing payments.',
                'updated_at' => $now,
            ]);
        }

        DB::table('staffs')->where('role', 'counter')->update([
            'role' => 'waiter',
            'updated_at' => $now,
        ]);
    }
};
