<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ShoppingList;
use App\Models\ShoppingListItem;

class ShoppingListTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $list = ShoppingList::create([
            'name' => 'Weekly Kitchen Supply Template',
            'status' => 'pending',
            'market_name' => 'Central Market',
            'shopping_date' => now()->format('Y-m-d'),
            'total_estimated_cost' => 150000,
            'notes' => 'Bulk supply for the weekend wedding ceremony.',
        ]);

        // Item 1: Cooking Oil (linked)
        ShoppingListItem::create([
            'shopping_list_id' => $list->id,
            'product_id' => 22, // Cooking oil
            'product_name' => 'Cooking oil',
            'category' => 'pantry',
            'quantity' => 20,
            'unit' => 'litres',
            'estimated_price' => 85000,
        ]);

        // Item 2: Beef Steak (linked)
        ShoppingListItem::create([
            'shopping_list_id' => $list->id,
            'product_id' => 5, // Beef steak
            'product_name' => 'Beef steak',
            'category' => 'meat_poultry',
            'quantity' => 10,
            'unit' => 'kg',
            'estimated_price' => 45000,
        ]);

        // Item 3: Fresh Tomatoes (Custom / Ad-hoc)
        ShoppingListItem::create([
            'shopping_list_id' => $list->id,
            'product_id' => null,
            'product_name' => 'Fresh Tomatoes',
            'category' => 'vegetables',
            'quantity' => 2,
            'unit' => 'sado',
            'estimated_price' => 20000,
        ]);

        $this->command->info('Shopping List template created: ' . $list->id);
    }
}
