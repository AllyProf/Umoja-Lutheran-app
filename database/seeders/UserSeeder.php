<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Staff;
use App\Models\Guest;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Manager User (Staff)
        Staff::updateOrCreate(
            ['email' => 'admin@primeland.com'],
            [
                'name' => 'Manager User',
                'password' => 'password', // Let cast handle hashing
                'role' => 'manager',
                'is_active' => true,
            ]
        );

        // Reception User (Staff)
        Staff::updateOrCreate(
            ['email' => 'reception@primeland.com'],
            [
                'name' => 'Reception Staff',
                'password' => 'password', // Let cast handle hashing
                'role' => 'reception',
                'is_active' => true,
            ]
        );

        // Guest User
        Guest::updateOrCreate(
            ['email' => 'customer@primeland.com'],
            [
                'name' => 'Customer User',
                'password' => 'password', // Let cast handle hashing
                'is_active' => true,
            ]
        );
    }
}

