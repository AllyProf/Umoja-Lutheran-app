<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Staff;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class WaiterRoleSeeder extends Seeder
{
    /**
     * Seed Counter role (replaces Waiter / Bar Keeper for this hostel).
     */
    public function run()
    {
        Role::updateOrCreate(
            ['name' => 'counter'],
            [
                'slug' => 'counter',
                'display_name' => 'Counter',
                'description' => 'Counter staff who open shifts, take orders, and process restaurant/bar sales.',
                'is_system' => true,
            ]
        );

        // Remove legacy waiter role if still present
        Role::where('name', 'waiter')->delete();

        Staff::firstOrCreate(
            ['email' => 'counter@umoja.test'],
            [
                'name' => 'Counter Staff',
                'password' => Hash::make('password'),
                'role' => 'counter',
                'department' => 'Food & Beverage',
                'position' => 'Counter',
                'is_active' => true,
            ]
        );

        $this->command->info('Counter role ready (Waiter removed from assignable roles).');
    }
}
