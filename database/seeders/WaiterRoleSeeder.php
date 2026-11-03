<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Staff;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class WaiterRoleSeeder extends Seeder
{
    public function run()
    {
        // 1. Create Waiter Role
        $waiterRole = Role::updateOrCreate(
            ['name' => 'waiter'],
            [
                'display_name' => 'Counter',
                'description' => 'Counter staff who take guest orders and process sales at the restaurant/bar counter.',
            ]
        );

        // 2. Create a Test Waiter Account
        Staff::firstOrCreate(
            ['email' => 'waiter@primelandhotel.co.tz'],
            [
                'name' => 'John Waiter',
                'password' => Hash::make('password'),
                'role' => 'waiter',
                'department' => 'Food & Beverage',
                'position' => 'Senior Waiter',
                'is_active' => true,
            ]
        );

        $this->command->info('Waiter role and test account created successfully.');
    }
}
