<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ShoppingList;
use App\Models\ShoppingListItem;

class KitchenMasterSeeder extends Seeder
{
    public function run()
    {
        $items = [
            ['name' => 'Broiler chicken', 'category' => 'Meat & Poultry', 'unit' => 'kg'],
            ['name' => 'Local chicken', 'category' => 'Meat & Poultry', 'unit' => 'kg'],
            ['name' => 'Beef steak', 'category' => 'Meat & Poultry', 'unit' => 'kg'],
            ['name' => 'Minced meat', 'category' => 'Meat & Poultry', 'unit' => 'kg'],
            ['name' => 'Pork steak', 'category' => 'Meat & Poultry', 'unit' => 'kg'],
            ['name' => 'Pork chop', 'category' => 'Meat & Poultry', 'unit' => 'kg'],
            ['name' => 'Bacon', 'category' => 'Meat & Poultry', 'unit' => 'kg'], // 'Mbuzi' is Goat
            ['name' => 'Mbuzi (Goat)', 'category' => 'Meat & Poultry', 'unit' => 'kg'],
            ['name' => 'Beef fillet', 'category' => 'Meat & Poultry', 'unit' => 'kg'],
            ['name' => 'Fish tilapia', 'category' => 'Seafood', 'unit' => 'pcs'],
            ['name' => 'Fish fillet', 'category' => 'Seafood', 'unit' => 'kg'],
            ['name' => 'Sugar', 'category' => 'Pantry', 'unit' => 'kg'],
            ['name' => 'Salt', 'category' => 'Pantry', 'unit' => 'kg'],
            ['name' => 'Spaghetti', 'category' => 'Pantry', 'unit' => 'packets'],
            ['name' => 'Rice', 'category' => 'Pantry', 'unit' => 'kg'],
            ['name' => 'Cheese', 'category' => 'Dairy', 'unit' => 'kg'],
            ['name' => 'Wheat flour', 'category' => 'Baking', 'unit' => 'kg'],
            ['name' => 'Sweet corn', 'category' => 'Vegetables', 'unit' => 'kg'],
            ['name' => 'Corn flour', 'category' => 'Baking', 'unit' => 'kg'],
            ['name' => 'Cooking oil', 'category' => 'Pantry', 'unit' => 'litres'],
            ['name' => 'Baking powder', 'category' => 'Baking', 'unit' => 'tins'],
            ['name' => 'Pizza dough', 'category' => 'Baking', 'unit' => 'kg'],
            ['name' => 'Prestige', 'category' => 'Dairy', 'unit' => 'tubs'],
            ['name' => 'Aromat', 'category' => 'Spices', 'unit' => 'tins'],
            ['name' => 'Tomato ketchup', 'category' => 'Sauces', 'unit' => 'bottles'],
            ['name' => 'Chilli sauce', 'category' => 'Sauces', 'unit' => 'bottles'],
            ['name' => 'Honey', 'category' => 'Pantry', 'unit' => 'bottles'],
            ['name' => 'Jam', 'category' => 'Pantry', 'unit' => 'tins'],
            ['name' => 'Baked beans', 'category' => 'Pantry', 'unit' => 'tins'],
            ['name' => 'Beef cubes', 'category' => 'Spices', 'unit' => 'packets'],
            ['name' => 'Chicken cubes', 'category' => 'Spices', 'unit' => 'packets'],
            ['name' => 'Fresh milk', 'category' => 'Dairy', 'unit' => 'litres'],
            ['name' => 'Sausage beef', 'category' => 'Meat & Poultry', 'unit' => 'packets'],
            ['name' => 'Oregano', 'category' => 'Spices', 'unit' => 'tins'],
            ['name' => 'Coconut cream', 'category' => 'Pantry', 'unit' => 'tins'],
            ['name' => 'Mayonnaise', 'category' => 'Sauces', 'unit' => 'bottles'],
            ['name' => 'Mushroom', 'category' => 'Vegetables', 'unit' => 'punnets'],
            ['name' => 'Vanilla', 'category' => 'Baking', 'unit' => 'bottles'],
            ['name' => 'Tomato paste', 'category' => 'Pantry', 'unit' => 'tins'],
            ['name' => 'Butter', 'category' => 'Dairy', 'unit' => 'kg'],
            ['name' => 'Burger bread', 'category' => 'Bakery', 'unit' => 'packets'],
            ['name' => 'Eggs', 'category' => 'Dairy', 'unit' => 'trays'],
            ['name' => 'Chicken wings', 'category' => 'Meat & Poultry', 'unit' => 'kg'],
            ['name' => 'Soya sauce', 'category' => 'Sauces', 'unit' => 'bottles'],
            ['name' => 'Mama sita', 'category' => 'Sauces', 'unit' => 'bottles'],
            ['name' => 'Olive oil', 'category' => 'Pantry', 'unit' => 'bottles'],
            ['name' => 'Mustard', 'category' => 'Sauces', 'unit' => 'bottles'],
        ];

        // 0. Ensure a default Supplier exists
        $supplier = \App\Models\Supplier::firstOrCreate(
            ['name' => 'General Market'],
            [
                'phone' => 'N/A',
                'email' => 'market@local.com',
                'location' => 'Town',
                'is_active' => true
            ]
        );

        // 1. Ensure all exist as Products (for autocomplete)
        foreach ($items as $item) {
            Product::firstOrCreate(
                ['name' => $item['name']],
                [
                    'category' => 'food',
                    'type' => 'food',
                    'supplier_id' => $supplier->id,
                    'brand_or_type' => 'Kitchen Ingredient',
                    'description' => $item['category'],
                    'is_active' => true
                ]
            );
        }

        // 2. Create the Master Shopping List Template
        $list = ShoppingList::create([
            'name' => 'MASTER KITCHEN TEMPLATE',
            'status' => 'pending',
            'notes' => 'Generated from standard kitchen stock sheet',
            'created_at' => now(),
        ]);

        foreach ($items as $item) {
            // Find the product we just ensured exists
            $product = Product::where('name', $item['name'])->first();

            ShoppingListItem::create([
                'shopping_list_id' => $list->id,
                'product_id' => $product->id,
                'product_name' => $item['name'],
                'category' => $item['category'],
                'quantity' => 0, // Template starts with 0 so user can just fill in what they need
                'unit' => $item['unit'],
                'estimated_price' => 0,
            ]);
        }
    }
}
