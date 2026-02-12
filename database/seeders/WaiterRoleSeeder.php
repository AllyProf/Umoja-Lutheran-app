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
        $waiterRole = Role::firstOrCreate(
            ['name' => 'waiter'],
            ['display_name' => 'Waiter', 'description' => 'Staff responsible for taking guest orders and processing payments.']
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
