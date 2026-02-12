<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call UserSeeder to create admin, reception, and customer users
        $this->call([
            UserSeeder::class,
            BarKeeperSeeder::class,
            ServiceSeeder::class,
        ]);
    }
}
