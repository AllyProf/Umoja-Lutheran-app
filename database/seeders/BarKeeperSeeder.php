<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Staff;
use App\Models\Role;

class BarKeeperSeeder extends Seeder
{
    /**
     * Seed Counter staff (replaces Bar Keeper for this hostel).
     */
    public function run(): void
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

        Staff::updateOrCreate(
            ['email' => 'counter@umoja.test'],
            [
                'name' => 'Counter Staff',
                'password' => 'password', // Let cast handle hashing
                'role' => 'counter',
                'is_active' => true,
            ]
        );

        $this->command->info('Counter created successfully!');
        $this->command->info('Email: counter@umoja.test');
        $this->command->info('Password: password');
    }
}
