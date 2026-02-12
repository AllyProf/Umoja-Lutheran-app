<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Staff;

class HousekeeperSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Housekeeper User (Staff)
        Staff::updateOrCreate(
            ['email' => 'housekeeper@primeland.com'],
            [
                'name' => 'Housekeeper',
                'password' => 'password', // Let cast handle hashing
                'role' => 'housekeeper',
                'is_active' => true,
            ]
        );

        $this->command->info('Housekeeper created successfully!');
        $this->command->info('Email: housekeeper@primeland.com');
        $this->command->info('Password: password');
    }
}
