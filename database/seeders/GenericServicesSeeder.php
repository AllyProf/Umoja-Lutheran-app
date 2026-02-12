<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;

class GenericServicesSeeder extends Seeder
{
    public function run()
    {
        // Create Generic Bar Order service if it doesn't exist
        Service::firstOrCreate(
            ['name' => 'Generic Bar Order'],
            [
                'category' => 'bar',
                'description' => 'Bar and drinks order from the bar menu',
                'price_tsh' => 0,
                'is_active' => true,
                'is_free_for_internal' => false,
            ]
        );

        // Create Generic Food Order service if it doesn't exist
        Service::firstOrCreate(
            ['name' => 'Generic Food Order'],
            [
                'category' => 'restaurant',
                'description' => 'Food order from restaurant menu',
                'price_tsh' => 0,
                'is_active' => true,
                'is_free_for_internal' => false,
            ]
        );

        $this->command->info('Generic services created successfully!');
    }
}
