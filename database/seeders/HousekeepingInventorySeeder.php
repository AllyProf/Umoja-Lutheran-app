<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HousekeepingInventoryItem;

class HousekeepingInventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            [
                'name' => 'Soap',
                'category' => 'cleaning_supplies',
                'unit' => 'pcs',
                'current_stock' => 100,
                'minimum_stock' => 20,
                'reorder_quantity' => 50,
                'description' => 'Bathroom soap bars',
            ],
            [
                'name' => 'Towel',
                'category' => 'linens',
                'unit' => 'pcs',
                'current_stock' => 50,
                'minimum_stock' => 10,
                'reorder_quantity' => 30,
                'description' => 'Bath towels',
            ],
            [
                'name' => 'Mosquito Repellent',
                'category' => 'cleaning_supplies',
                'unit' => 'bottles',
                'current_stock' => 25,
                'minimum_stock' => 5,
                'reorder_quantity' => 15,
                'description' => 'Dawa ya mbu - Mosquito repellent spray',
            ],
            [
                'name' => 'Drinking Water',
                'category' => 'beverages',
                'unit' => 'liters',
                'current_stock' => 200,
                'minimum_stock' => 50,
                'reorder_quantity' => 100,
                'description' => 'Bottled drinking water for rooms',
            ],
            [
                'name' => 'Toilet Paper',
                'category' => 'cleaning_supplies',
                'unit' => 'rolls',
                'current_stock' => 80,
                'minimum_stock' => 15,
                'reorder_quantity' => 40,
                'description' => 'Toilet paper rolls',
            ],
            [
                'name' => 'Shampoo',
                'category' => 'cleaning_supplies',
                'unit' => 'bottles',
                'current_stock' => 30,
                'minimum_stock' => 5,
                'reorder_quantity' => 20,
                'description' => 'Shampoo bottles',
            ],
        ];

        foreach ($items as $item) {
            HousekeepingInventoryItem::firstOrCreate(
                ['name' => $item['name']],
                $item
            );
        }
    }
}
