<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ServiceCatalog;

class ServiceCatalogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Swimming service if it doesn't exist
        ServiceCatalog::firstOrCreate(
            ['service_key' => 'swimming'],
            [
                'service_name' => 'Swimming Pool Access',
                'description' => 'Access to swimming pool facility',
                'pricing_type' => 'fixed',
                'price_tanzanian' => 10000,
                'price_international' => 5,
                'payment_required_upfront' => true,
                'requires_items' => false,
                'is_active' => true,
                'display_order' => 1,
            ]
        );

        // Create Restaurant service if it doesn't exist
        ServiceCatalog::firstOrCreate(
            ['service_key' => 'restaurant'],
            [
                'service_name' => 'Restaurant',
                'description' => 'Restaurant dining service',
                'pricing_type' => 'custom',
                'price_tanzanian' => 0,
                'price_international' => 0,
                'payment_required_upfront' => false,
                'requires_items' => true,
                'is_active' => true,
                'display_order' => 2,
            ]
        );
    }
}
