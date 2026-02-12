<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockReceipt;
use App\Models\Recipe;
use App\Models\RecipeIngredient;
use App\Models\Staff;
use Illuminate\Support\Facades\DB;

class RecipeDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::transaction(function () {
            $user = Staff::first(); // Assign creator to first staff found
            $userId = $user ? $user->id : 1;

            // Ensure a Supplier exists
            $supplier = \App\Models\Supplier::firstOrCreate(
                ['name' => 'General Market Supplier'],
                ['phone' => '0700000000', 'location' => 'Local Market', 'is_active' => true]
            );

            // 1. Create Ingredients (Products)
            $ingredients = [
                [
                    'name' => 'Premium Beef',
                    'category' => 'food', // Corrected from meat_poultry
                    'unit' => 'kg',
                    'price' => 12000,
                    'stock' => 50
                ],
                [
                    'name' => 'Basmati Rice',
                    'category' => 'food', // Corrected from pantry
                    'unit' => 'kg',
                    'price' => 3500,
                    'stock' => 100
                ],
                [
                    'name' => 'Cooking Oil',
                    'category' => 'food', // Corrected from pantry
                    'unit' => 'litres',
                    'price' => 4500,
                    'stock' => 20
                ],
                [
                    'name' => 'Pilau Masala',
                    'category' => 'food', // Corrected from spices
                    'unit' => 'kg',
                    'price' => 20000,
                    'stock' => 5
                ]
            ];

            $createdProducts = [];

            foreach ($ingredients as $item) {
                // Create Product
                $product = Product::firstOrCreate(
                    ['name' => $item['name']],
                    [
                        'category' => $item['category'],
                        'type' => 'food',
                        'is_active' => true,
                        'description' => 'Demo ingredient for recipe testing',
                        'supplier_id' => $supplier->id
                    ]
                );

                // Determine correct packaging enum
                $packaging = 'packets'; // Default for most
                if ($item['unit'] == 'litres') $packaging = 'carton'; // Oil
                if ($item['name'] == 'Basmati Rice') $packaging = 'bags';
                if ($item['unit'] == 'kg' && $item['name'] != 'Basmati Rice') $packaging = 'packets';

                // Create Variant (Single Unit)
                $variant = ProductVariant::firstOrCreate(
                    ['product_id' => $product->id, 'measurement' => '1 ' . $item['unit']],
                    [
                        'packaging' => $packaging,
                        'items_per_package' => 1,
                        'is_active' => true
                    ]
                );

                // Add Stock (Receipt) so it shows up in Recipe Creator
                StockReceipt::create([
                    'product_id' => $product->id,
                    'product_variant_id' => $variant->id,
                    'supplier_id' => 1, // Assuming supplier ID 1 exists, otherwise null or create one
                    'quantity_received_packages' => $item['stock'],
                    'buying_price_per_bottle' => $item['price'], // Price per unit
                    'selling_price_per_bottle' => $item['price'] * 1.5,
                    'received_date' => now(),
                    'received_by' => $userId,
                    'notes' => 'Initial stock for recipe demo'
                ]);

                $createdProducts[$item['name']] = $product;
            }

            // 2. Create Recipe: Pilau (1 Plate)
            $recipe = Recipe::create([
                'name' => 'Swahili Pilau',
                'description' => 'Traditional coastal rice dish seasoned with spices and served with beef.',
                'category' => 'Lunch',
                'prep_time' => 20,
                'cook_time' => 40,
                'servings' => 1,
                'selling_price' => 8000,
                'is_available' => true,
                'created_by' => $userId
            ]);

            // 3. Link Ingredients to Recipe (Per 1 Plate Rules)
            $recipeIngredients = [
                ['name' => 'Premium Beef', 'qty' => 0.15, 'unit' => 'kg'],
                ['name' => 'Basmati Rice', 'qty' => 0.12, 'unit' => 'kg'],
                ['name' => 'Cooking Oil', 'qty' => 0.02, 'unit' => 'litres'],
                ['name' => 'Pilau Masala', 'qty' => 0.005, 'unit' => 'kg'],
            ];

            foreach ($recipeIngredients as $ing) {
                if (isset($createdProducts[$ing['name']])) {
                    RecipeIngredient::create([
                        'recipe_id' => $recipe->id,
                        'product_id' => $createdProducts[$ing['name']]->id,
                        'quantity' => $ing['qty'],
                        'unit' => $ing['unit'],
                        'notes' => 'Standard portion'
                    ]);
                }
            }

            $this->command->info('Recipe Demo Data Seeded Successfully!');
            $this->command->info('Created Recipe: Swahili Pilau');
            $this->command->info('Created Ingredients: Beef, Rice, Oil, Spices');
        });
    }
}
