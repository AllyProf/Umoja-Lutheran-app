<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Ensure we have a default supplier
        $supplier = Supplier::firstOrCreate(
            ['name' => 'General Supplier'],
            [
                'phone' => '000-000-0000',
                'email' => 'supplier@example.com',
                'location' => 'Dar es Salaam',
                'is_active' => true
            ]
        );

        // Define Categories and Complete Product List
        $categories = [
            'non_alcoholic_beverage' => [ // Soft Drinks
                'type' => 'drink',
                'brands' => [
                    'Coca Cola' => [
                        ['name' => 'Coke', 'size' => '350 ml', 'method' => 'pic'],
                        ['name' => 'Coke Zero', 'size' => '350 ml', 'method' => 'pic'],
                        ['name' => 'Fanta Orange', 'size' => '350 ml', 'method' => 'pic'],
                        ['name' => 'Fanta Pineapple', 'size' => '350 ml', 'method' => 'pic'],
                        ['name' => 'Fanta Passion', 'size' => '350 ml', 'method' => 'pic'],
                        ['name' => 'Sprite', 'size' => '350 ml', 'method' => 'pic'],
                        ['name' => 'Stoney Tangawizi', 'size' => '350 ml', 'method' => 'pic'],
                        ['name' => 'Krest Bitter Lemon', 'size' => '300 ml', 'method' => 'pic'],
                        ['name' => 'Novida', 'size' => '330 ml', 'method' => 'pic'],
                    ],
                    'Pepsi' => [
                        ['name' => 'Pepsi', 'size' => '350 ml', 'method' => 'pic'],
                        ['name' => 'Mirinda Orange', 'size' => '350 ml', 'method' => 'pic'],
                        ['name' => 'Mirinda Fruity', 'size' => '350 ml', 'method' => 'pic'],
                        ['name' => '7UP', 'size' => '350 ml', 'method' => 'pic'],
                        ['name' => 'Mountain Dew', 'size' => '500 ml', 'method' => 'pic'],
                    ],
                    'Malt' => [
                        ['name' => 'Malt', 'size' => '330 ml', 'method' => 'pic'],
                    ]
                ]
            ],
            'water' => [
                'type' => 'drink',
                'brands' => [
                    'Kilimanjaro Water' => [
                        ['name' => 'Small', 'size' => '500 ml', 'method' => 'pic'],
                        ['name' => 'Large', 'size' => '1.5 l', 'method' => 'pic'],
                    ]
                ]
            ],
            'energy_drinks' => [
                'type' => 'drink',
                'brands' => [
                    'Red Bull' => [
                        ['name' => 'Standard', 'size' => '250 ml', 'method' => 'pic'],
                    ],
                    'Mo Faya' => [
                        ['name' => 'Standard', 'size' => '500 ml', 'method' => 'pic'],
                    ]
                ]
            ],
            'juices' => [
                'type' => 'drink',
                'brands' => [
                    'Azam Juice' => [
                        ['name' => 'Small Box', 'size' => '250 ml', 'method' => 'pic'],
                    ],
                    'Ceres Invoice' => [
                        ['name' => 'Carton', 'size' => '1 l', 'method' => 'pic'], // Sold as whole usually
                    ]
                ]
            ],
            'alcoholic_beverage' => [ // Beers & Ciders
                'type' => 'drink',
                'brands' => [
                    'TBL Beers' => [
                        ['name' => 'Safari Lager', 'size' => '500 ml', 'method' => 'pic'],
                        ['name' => 'Kilimanjaro Lager', 'size' => '500 ml', 'method' => 'pic'],
                        ['name' => 'Castle Lite', 'size' => '500 ml', 'method' => 'pic'],
                        ['name' => 'Castle Lager', 'size' => '500 ml', 'method' => 'pic'],
                        ['name' => 'Balimi', 'size' => '500 ml', 'method' => 'pic'],
                    ],
                    'Serengeti Breweries' => [
                        ['name' => 'Serengeti Lager', 'size' => '500 ml', 'method' => 'pic'],
                        ['name' => 'Serengeti Lite', 'size' => '500 ml', 'method' => 'pic'],
                        ['name' => 'Pilsner', 'size' => '500 ml', 'method' => 'pic'],
                        ['name' => 'Guinness', 'size' => '500 ml', 'method' => 'pic'],
                    ],
                    'Imported Beers' => [
                        ['name' => 'Heineken', 'size' => '330 ml', 'method' => 'pic'],
                        ['name' => 'Windhoek', 'size' => '330 ml', 'method' => 'pic'],
                        ['name' => 'Savanna Dry', 'size' => '330 ml', 'method' => 'pic'],
                        ['name' => 'Hunters Gold', 'size' => '330 ml', 'method' => 'pic'],
                    ]
                ]
            ],
            'spirits' => [ // Hard Drinks
                'type' => 'drink',
                'brands' => [
                    'Jack Daniels' => [
                         // 750ml bottle = approx 25 tots of 30ml
                        ['name' => 'No. 7', 'size' => '750 ml', 'method' => 'mixed', 'servings' => 25],
                        ['name' => 'No. 7', 'size' => '1 l', 'method' => 'mixed', 'servings' => 33],
                        ['name' => 'Honey', 'size' => '750 ml', 'method' => 'mixed', 'servings' => 25],
                    ],
                    'Jameson' => [
                        ['name' => 'Irish Whiskey', 'size' => '750 ml', 'method' => 'mixed', 'servings' => 25],
                        ['name' => 'Black Barrel', 'size' => '750 ml', 'method' => 'mixed', 'servings' => 25],
                    ],
                    'Johnnie Walker' => [
                        ['name' => 'Red Label', 'size' => '750 ml', 'method' => 'mixed', 'servings' => 25],
                        ['name' => 'Black Label', 'size' => '750 ml', 'method' => 'mixed', 'servings' => 25],
                        ['name' => 'Double Black', 'size' => '750 ml', 'method' => 'mixed', 'servings' => 25],
                        ['name' => 'Gold Label', 'size' => '750 ml', 'method' => 'mixed', 'servings' => 25],
                    ],
                    'Vodka' => [
                        ['name' => 'Smirnoff Red', 'size' => '750 ml', 'method' => 'mixed', 'servings' => 25],
                        ['name' => 'Absolut Blue', 'size' => '750 ml', 'method' => 'mixed', 'servings' => 25],
                        ['name' => 'Ciroc', 'size' => '750 ml', 'method' => 'mixed', 'servings' => 25],
                        ['name' => 'Konyagi', 'size' => '500 ml', 'method' => 'mixed', 'servings' => 16],
                    ],
                    'Gin' => [
                        ['name' => 'Gordons', 'size' => '750 ml', 'method' => 'mixed', 'servings' => 25],
                        ['name' => 'Bombay Sapphire', 'size' => '750 ml', 'method' => 'mixed', 'servings' => 25],
                        ['name' => 'Tanqueray', 'size' => '750 ml', 'method' => 'mixed', 'servings' => 25],
                        ['name' => 'K-Vant', 'size' => '750 ml', 'method' => 'mixed', 'servings' => 25], 
                        ['name' => 'K-Vant', 'size' => '200 ml', 'method' => 'pic'], // Pocket size usually sold full
                    ],
                    'Tequila' => [
                        ['name' => 'Jose Cuervo Silver', 'size' => '750 ml', 'method' => 'mixed', 'servings' => 25],
                        ['name' => 'Jose Cuervo Gold', 'size' => '750 ml', 'method' => 'mixed', 'servings' => 25],
                        ['name' => 'Camino', 'size' => '750 ml', 'method' => 'mixed', 'servings' => 25],
                    ],
                    'Amarula' => [
                        ['name' => 'Cream', 'size' => '750 ml', 'method' => 'mixed', 'servings' => 15], // Larger servings (50ml) typically
                    ],
                    'Hennessey' => [
                        ['name' => 'VS', 'size' => '750 ml', 'method' => 'mixed', 'servings' => 25],
                        ['name' => 'VSOP', 'size' => '750 ml', 'method' => 'mixed', 'servings' => 25],
                    ]
                ]
            ],
            'wines' => [
                'type' => 'drink',
                'brands' => [
                    'Red Wine' => [
                        ['name' => 'Sweet Red', 'size' => '750 ml', 'method' => 'pic'], 
                        ['name' => 'Dry Red', 'size' => '750 ml', 'method' => 'pic'],
                    ],
                    'White Wine' => [
                        ['name' => 'Sweet White', 'size' => '750 ml', 'method' => 'pic'],
                        ['name' => 'Dry White', 'size' => '750 ml', 'method' => 'pic'],
                    ]
                ]
            ]
        ];

        DB::beginTransaction();

        try {
            // First clear existing if any (optional, safe for re-seeding if we use firstOrCreate)
            // But user asked to "create to create our products", implying filling it up.
            
            foreach ($categories as $categoryCode => $catData) {
                foreach ($catData['brands'] as $brandName => $variants) {
                    // Create Parent Product (Brand)
                    $product = Product::firstOrCreate(
                        ['name' => $brandName],
                        [
                            'category' => $categoryCode,
                            'type' => $catData['type'],
                            'supplier_id' => null, // Optional
                            'brand_or_type' => $brandName,
                            'description' => 'Standard Bar Inventory',
                            'is_active' => true
                        ]
                    );

                    foreach ($variants as $index => $variantData) {
                        
                        $method = $variantData['method']; // pic, glass, mixed
                        
                        $canSellPic = ($method === 'pic' || $method === 'mixed');
                        $canSellServing = ($method === 'glass' || $method === 'mixed');
                        $servings = $variantData['servings'] ?? 1;

                        ProductVariant::firstOrCreate(
                            [
                                'product_id' => $product->id,
                                'variant_name' => $variantData['name'],
                                'measurement' => $variantData['size']
                            ],
                            [
                                'servings_per_pic' => $servings,
                                'packaging' => 'unit',
                                'items_per_package' => 1,
                                'display_order' => $index,
                                'is_active' => true,
                                'selling_unit' => $canSellServing ? 'glass' : 'pic',
                                'can_sell_as_serving' => $canSellServing,
                                'can_sell_as_pic' => $canSellPic,
                            ]
                        );
                    }
                }
            }
            
            DB::commit();
            $this->command->info('Products seeded successfully!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Error seeding products: ' . $e->getMessage());
        }
    }
}
