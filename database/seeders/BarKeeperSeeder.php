<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Staff;

class BarKeeperSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Bar Keeper User (Staff)
        Staff::updateOrCreate(
            ['email' => 'barkeeper@primeland.com'],
            [
                'name' => 'Bar Keeper',
                'password' => 'password', // Let cast handle hashing
                'role' => 'bar_keeper',
                'is_active' => true,
            ]
        );

        $this->command->info('Bar Keeper created successfully!');
        $this->command->info('Email: barkeeper@primeland.com');
        $this->command->info('Password: password');
    }
}
