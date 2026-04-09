<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockRequest;
use App\Models\Staff;
use Illuminate\Support\Facades\DB;

class ProductionStockSyncSeeder extends Seeder
{
    public function run()
    {
        DB::transaction(function () {
            // 1. Sync Products and Variants
            $product = Product::updateOrCreate(['name' => 'BONITE'], array (
  'name' => 'BONITE',
  'category' => 'non_alcoholic_beverage',
  'type' => 'drink',
  'description' => NULL,
  'is_active' => true,
  'supplier_id' => NULL,
));
            ProductVariant::updateOrCreate(['product_id' => $product->id, 'measurement' => '350 ml'], array (
  'measurement' => '350 ml',
  'packaging' => 'crates',
  'items_per_package' => 24,
  'buying_price_per_bottle' => NULL,
  'selling_price_per_bottle' => NULL,
  'selling_price_per_pic' => '1000.00',
  'is_active' => true,
  'stock_quantity' => NULL,
));
            ProductVariant::updateOrCreate(['product_id' => $product->id, 'measurement' => '350 ml'], array (
  'measurement' => '350 ml',
  'packaging' => 'crates',
  'items_per_package' => 24,
  'buying_price_per_bottle' => NULL,
  'selling_price_per_bottle' => NULL,
  'selling_price_per_pic' => '1000.00',
  'is_active' => true,
  'stock_quantity' => NULL,
));
            ProductVariant::updateOrCreate(['product_id' => $product->id, 'measurement' => '350 ml'], array (
  'measurement' => '350 ml',
  'packaging' => 'crates',
  'items_per_package' => 24,
  'buying_price_per_bottle' => NULL,
  'selling_price_per_bottle' => NULL,
  'selling_price_per_pic' => '1000.00',
  'is_active' => true,
  'stock_quantity' => NULL,
));

            $product = Product::updateOrCreate(['name' => 'Madiko'], array (
  'name' => 'Madiko',
  'category' => 'food',
  'type' => 'food',
  'description' => NULL,
  'is_active' => true,
  'supplier_id' => NULL,
));
            ProductVariant::updateOrCreate(['product_id' => $product->id, 'measurement' => '0 ml'], array (
  'measurement' => '0 ml',
  'packaging' => 'unit',
  'items_per_package' => 1,
  'buying_price_per_bottle' => NULL,
  'selling_price_per_bottle' => NULL,
  'selling_price_per_pic' => '0.00',
  'is_active' => true,
  'stock_quantity' => NULL,
));
            ProductVariant::updateOrCreate(['product_id' => $product->id, 'measurement' => '0 ml'], array (
  'measurement' => '0 ml',
  'packaging' => 'unit',
  'items_per_package' => 1,
  'buying_price_per_bottle' => NULL,
  'selling_price_per_bottle' => NULL,
  'selling_price_per_pic' => '0.00',
  'is_active' => true,
  'stock_quantity' => NULL,
));
            ProductVariant::updateOrCreate(['product_id' => $product->id, 'measurement' => 'ml'], array (
  'measurement' => 'ml',
  'packaging' => 'unit',
  'items_per_package' => 1,
  'buying_price_per_bottle' => NULL,
  'selling_price_per_bottle' => NULL,
  'selling_price_per_pic' => '0.00',
  'is_active' => true,
  'stock_quantity' => NULL,
));

            $product = Product::updateOrCreate(['name' => 'Usafi'], array (
  'name' => 'Usafi',
  'category' => 'cleaning_supplies',
  'type' => 'housekeeping',
  'description' => NULL,
  'is_active' => true,
  'supplier_id' => NULL,
));
            ProductVariant::updateOrCreate(['product_id' => $product->id, 'measurement' => 'ml'], array (
  'measurement' => 'ml',
  'packaging' => 'unit',
  'items_per_package' => 1,
  'buying_price_per_bottle' => NULL,
  'selling_price_per_bottle' => NULL,
  'selling_price_per_pic' => '0.00',
  'is_active' => true,
  'stock_quantity' => NULL,
));
            ProductVariant::updateOrCreate(['product_id' => $product->id, 'measurement' => 'ml'], array (
  'measurement' => 'ml',
  'packaging' => 'unit',
  'items_per_package' => 1,
  'buying_price_per_bottle' => NULL,
  'selling_price_per_bottle' => NULL,
  'selling_price_per_pic' => '0.00',
  'is_active' => true,
  'stock_quantity' => NULL,
));

            $product = Product::updateOrCreate(['name' => 'Clean'], array (
  'name' => 'Clean',
  'category' => 'cleaning_supplies',
  'type' => 'housekeeping',
  'description' => NULL,
  'is_active' => true,
  'supplier_id' => NULL,
));
            ProductVariant::updateOrCreate(['product_id' => $product->id, 'measurement' => 'ml'], array (
  'measurement' => 'ml',
  'packaging' => 'unit',
  'items_per_package' => 1,
  'buying_price_per_bottle' => NULL,
  'selling_price_per_bottle' => NULL,
  'selling_price_per_pic' => '0.00',
  'is_active' => true,
  'stock_quantity' => NULL,
));

            $product = Product::updateOrCreate(['name' => 'viungo'], array (
  'name' => 'viungo',
  'category' => 'food',
  'type' => 'food',
  'description' => NULL,
  'is_active' => true,
  'supplier_id' => NULL,
));
            ProductVariant::updateOrCreate(['product_id' => $product->id, 'measurement' => 'ml'], array (
  'measurement' => 'ml',
  'packaging' => 'unit',
  'items_per_package' => 1,
  'buying_price_per_bottle' => NULL,
  'selling_price_per_bottle' => NULL,
  'selling_price_per_pic' => '0.00',
  'is_active' => true,
  'stock_quantity' => NULL,
));
            ProductVariant::updateOrCreate(['product_id' => $product->id, 'measurement' => 'ml'], array (
  'measurement' => 'ml',
  'packaging' => 'unit',
  'items_per_package' => 1,
  'buying_price_per_bottle' => NULL,
  'selling_price_per_bottle' => NULL,
  'selling_price_per_pic' => '0.00',
  'is_active' => true,
  'stock_quantity' => NULL,
));
            ProductVariant::updateOrCreate(['product_id' => $product->id, 'measurement' => 'ml'], array (
  'measurement' => 'ml',
  'packaging' => 'unit',
  'items_per_package' => 1,
  'buying_price_per_bottle' => NULL,
  'selling_price_per_bottle' => NULL,
  'selling_price_per_pic' => '0.00',
  'is_active' => true,
  'stock_quantity' => NULL,
));

            $product = Product::updateOrCreate(['name' => 'Pepsi'], array (
  'name' => 'Pepsi',
  'category' => 'non_alcoholic_beverage',
  'type' => 'drink',
  'description' => NULL,
  'is_active' => true,
  'supplier_id' => NULL,
));
            ProductVariant::updateOrCreate(['product_id' => $product->id, 'measurement' => '350 ml'], array (
  'measurement' => '350 ml',
  'packaging' => 'unit',
  'items_per_package' => 24,
  'buying_price_per_bottle' => NULL,
  'selling_price_per_bottle' => NULL,
  'selling_price_per_pic' => '700.00',
  'is_active' => true,
  'stock_quantity' => NULL,
));

            $product = Product::updateOrCreate(['name' => 'Mtindi'], array (
  'name' => 'Mtindi',
  'category' => 'non_alcoholic_beverage',
  'type' => 'drink',
  'description' => NULL,
  'is_active' => true,
  'supplier_id' => NULL,
));
            ProductVariant::updateOrCreate(['product_id' => $product->id, 'measurement' => ''], array (
  'measurement' => NULL,
  'packaging' => 'Bottle',
  'items_per_package' => 1,
  'buying_price_per_bottle' => NULL,
  'selling_price_per_bottle' => NULL,
  'selling_price_per_pic' => '2500.00',
  'is_active' => true,
  'stock_quantity' => NULL,
));

            $product = Product::updateOrCreate(['name' => 'Soda'], array (
  'name' => 'Soda',
  'category' => 'non_alcoholic_beverage',
  'type' => 'drink',
  'description' => NULL,
  'is_active' => true,
  'supplier_id' => NULL,
));
            ProductVariant::updateOrCreate(['product_id' => $product->id, 'measurement' => ''], array (
  'measurement' => NULL,
  'packaging' => 'Bottle',
  'items_per_package' => 1,
  'buying_price_per_bottle' => NULL,
  'selling_price_per_bottle' => NULL,
  'selling_price_per_pic' => '1000.00',
  'is_active' => true,
  'stock_quantity' => NULL,
));

            $product = Product::updateOrCreate(['name' => 'Soda Take Away'], array (
  'name' => 'Soda Take Away',
  'category' => 'non_alcoholic_beverage',
  'type' => 'drink',
  'description' => NULL,
  'is_active' => true,
  'supplier_id' => NULL,
));
            ProductVariant::updateOrCreate(['product_id' => $product->id, 'measurement' => ''], array (
  'measurement' => NULL,
  'packaging' => 'Bottle',
  'items_per_package' => 1,
  'buying_price_per_bottle' => NULL,
  'selling_price_per_bottle' => NULL,
  'selling_price_per_pic' => '1500.00',
  'is_active' => true,
  'stock_quantity' => NULL,
));

            $product = Product::updateOrCreate(['name' => 'Potable'], array (
  'name' => 'Potable',
  'category' => 'non_alcoholic_beverage',
  'type' => 'drink',
  'description' => NULL,
  'is_active' => true,
  'supplier_id' => NULL,
));
            ProductVariant::updateOrCreate(['product_id' => $product->id, 'measurement' => ''], array (
  'measurement' => NULL,
  'packaging' => 'Bottle',
  'items_per_package' => 1,
  'buying_price_per_bottle' => NULL,
  'selling_price_per_bottle' => NULL,
  'selling_price_per_pic' => '1000.00',
  'is_active' => true,
  'stock_quantity' => NULL,
));

            $product = Product::updateOrCreate(['name' => 'M/Water'], array (
  'name' => 'M/Water',
  'category' => 'non_alcoholic_beverage',
  'type' => 'drink',
  'description' => NULL,
  'is_active' => true,
  'supplier_id' => NULL,
));
            ProductVariant::updateOrCreate(['product_id' => $product->id, 'measurement' => '0 ml'], array (
  'measurement' => '0 ml',
  'packaging' => 'Bottle',
  'items_per_package' => 12,
  'buying_price_per_bottle' => NULL,
  'selling_price_per_bottle' => NULL,
  'selling_price_per_pic' => '1000.00',
  'is_active' => true,
  'stock_quantity' => NULL,
));

            $product = Product::updateOrCreate(['name' => 'L/Water'], array (
  'name' => 'L/Water',
  'category' => 'non_alcoholic_beverage',
  'type' => 'drink',
  'description' => NULL,
  'is_active' => true,
  'supplier_id' => NULL,
));
            ProductVariant::updateOrCreate(['product_id' => $product->id, 'measurement' => '0 ml'], array (
  'measurement' => '0 ml',
  'packaging' => 'Bottle',
  'items_per_package' => 12,
  'buying_price_per_bottle' => NULL,
  'selling_price_per_bottle' => NULL,
  'selling_price_per_pic' => '1500.00',
  'is_active' => true,
  'stock_quantity' => NULL,
));

            $product = Product::updateOrCreate(['name' => 'Mifuko Kaki'], array (
  'name' => 'Mifuko Kaki',
  'category' => 'supplies',
  'type' => 'drink',
  'description' => NULL,
  'is_active' => true,
  'supplier_id' => NULL,
));
            ProductVariant::updateOrCreate(['product_id' => $product->id, 'measurement' => ''], array (
  'measurement' => NULL,
  'packaging' => 'Unit',
  'items_per_package' => 1,
  'buying_price_per_bottle' => NULL,
  'selling_price_per_bottle' => NULL,
  'selling_price_per_pic' => '0.00',
  'is_active' => true,
  'stock_quantity' => NULL,
));

            $product = Product::updateOrCreate(['name' => 'Napkin'], array (
  'name' => 'Napkin',
  'category' => 'supplies',
  'type' => 'drink',
  'description' => NULL,
  'is_active' => true,
  'supplier_id' => NULL,
));
            ProductVariant::updateOrCreate(['product_id' => $product->id, 'measurement' => ''], array (
  'measurement' => NULL,
  'packaging' => 'Unit',
  'items_per_package' => 1,
  'buying_price_per_bottle' => NULL,
  'selling_price_per_bottle' => NULL,
  'selling_price_per_pic' => '0.00',
  'is_active' => true,
  'stock_quantity' => NULL,
));

            $product = Product::updateOrCreate(['name' => 'Lunch Box'], array (
  'name' => 'Lunch Box',
  'category' => 'supplies',
  'type' => 'drink',
  'description' => NULL,
  'is_active' => true,
  'supplier_id' => NULL,
));
            ProductVariant::updateOrCreate(['product_id' => $product->id, 'measurement' => ''], array (
  'measurement' => NULL,
  'packaging' => 'Unit',
  'items_per_package' => 1,
  'buying_price_per_bottle' => NULL,
  'selling_price_per_bottle' => NULL,
  'selling_price_per_pic' => '0.00',
  'is_active' => true,
  'stock_quantity' => NULL,
));

            $product = Product::updateOrCreate(['name' => 'Table Salt'], array (
  'name' => 'Table Salt',
  'category' => 'supplies',
  'type' => 'drink',
  'description' => NULL,
  'is_active' => true,
  'supplier_id' => NULL,
));
            ProductVariant::updateOrCreate(['product_id' => $product->id, 'measurement' => ''], array (
  'measurement' => NULL,
  'packaging' => 'Unit',
  'items_per_package' => 1,
  'buying_price_per_bottle' => NULL,
  'selling_price_per_bottle' => NULL,
  'selling_price_per_pic' => '0.00',
  'is_active' => true,
  'stock_quantity' => NULL,
));

            $product = Product::updateOrCreate(['name' => 'Tooth Stick'], array (
  'name' => 'Tooth Stick',
  'category' => 'supplies',
  'type' => 'drink',
  'description' => NULL,
  'is_active' => true,
  'supplier_id' => NULL,
));
            ProductVariant::updateOrCreate(['product_id' => $product->id, 'measurement' => ''], array (
  'measurement' => NULL,
  'packaging' => 'Unit',
  'items_per_package' => 1,
  'buying_price_per_bottle' => NULL,
  'selling_price_per_bottle' => NULL,
  'selling_price_per_pic' => '0.00',
  'is_active' => true,
  'stock_quantity' => NULL,
));

            $product = Product::updateOrCreate(['name' => 'Tomato Sauce'], array (
  'name' => 'Tomato Sauce',
  'category' => 'sauces',
  'type' => 'drink',
  'description' => NULL,
  'is_active' => true,
  'supplier_id' => NULL,
));
            ProductVariant::updateOrCreate(['product_id' => $product->id, 'measurement' => ''], array (
  'measurement' => NULL,
  'packaging' => 'Unit',
  'items_per_package' => 1,
  'buying_price_per_bottle' => NULL,
  'selling_price_per_bottle' => NULL,
  'selling_price_per_pic' => '0.00',
  'is_active' => true,
  'stock_quantity' => NULL,
));

            $product = Product::updateOrCreate(['name' => 'Chilli Sauce'], array (
  'name' => 'Chilli Sauce',
  'category' => 'sauces',
  'type' => 'drink',
  'description' => NULL,
  'is_active' => true,
  'supplier_id' => NULL,
));
            ProductVariant::updateOrCreate(['product_id' => $product->id, 'measurement' => ''], array (
  'measurement' => NULL,
  'packaging' => 'Unit',
  'items_per_package' => 1,
  'buying_price_per_bottle' => NULL,
  'selling_price_per_bottle' => NULL,
  'selling_price_per_pic' => '0.00',
  'is_active' => true,
  'stock_quantity' => NULL,
));

            $product = Product::updateOrCreate(['name' => 'Blue Band'], array (
  'name' => 'Blue Band',
  'category' => 'food',
  'type' => 'drink',
  'description' => NULL,
  'is_active' => true,
  'supplier_id' => NULL,
));
            ProductVariant::updateOrCreate(['product_id' => $product->id, 'measurement' => ''], array (
  'measurement' => NULL,
  'packaging' => 'Unit',
  'items_per_package' => 1,
  'buying_price_per_bottle' => NULL,
  'selling_price_per_bottle' => NULL,
  'selling_price_per_pic' => '0.00',
  'is_active' => true,
  'stock_quantity' => NULL,
));

            $product = Product::updateOrCreate(['name' => 'Vim'], array (
  'name' => 'Vim',
  'category' => 'supplies',
  'type' => 'drink',
  'description' => NULL,
  'is_active' => true,
  'supplier_id' => NULL,
));
            ProductVariant::updateOrCreate(['product_id' => $product->id, 'measurement' => ''], array (
  'measurement' => NULL,
  'packaging' => 'Unit',
  'items_per_package' => 1,
  'buying_price_per_bottle' => NULL,
  'selling_price_per_bottle' => NULL,
  'selling_price_per_pic' => '0.00',
  'is_active' => true,
  'stock_quantity' => NULL,
));

            $product = Product::updateOrCreate(['name' => 'Asali'], array (
  'name' => 'Asali',
  'category' => 'food',
  'type' => 'drink',
  'description' => NULL,
  'is_active' => true,
  'supplier_id' => NULL,
));
            ProductVariant::updateOrCreate(['product_id' => $product->id, 'measurement' => ''], array (
  'measurement' => NULL,
  'packaging' => 'Unit',
  'items_per_package' => 1,
  'buying_price_per_bottle' => NULL,
  'selling_price_per_bottle' => NULL,
  'selling_price_per_pic' => '0.00',
  'is_active' => true,
  'stock_quantity' => NULL,
));

            $product = Product::updateOrCreate(['name' => 'Matunda'], array (
  'name' => 'Matunda',
  'category' => 'food',
  'type' => 'drink',
  'description' => NULL,
  'is_active' => true,
  'supplier_id' => NULL,
));
            ProductVariant::updateOrCreate(['product_id' => $product->id, 'measurement' => ''], array (
  'measurement' => NULL,
  'packaging' => 'Unit',
  'items_per_package' => 1,
  'buying_price_per_bottle' => NULL,
  'selling_price_per_bottle' => NULL,
  'selling_price_per_pic' => '0.00',
  'is_active' => true,
  'stock_quantity' => NULL,
));

            $product = Product::updateOrCreate(['name' => 'Tende'], array (
  'name' => 'Tende',
  'category' => 'food',
  'type' => 'drink',
  'description' => NULL,
  'is_active' => true,
  'supplier_id' => NULL,
));
            ProductVariant::updateOrCreate(['product_id' => $product->id, 'measurement' => ''], array (
  'measurement' => NULL,
  'packaging' => 'Unit',
  'items_per_package' => 1,
  'buying_price_per_bottle' => NULL,
  'selling_price_per_bottle' => NULL,
  'selling_price_per_pic' => '0.00',
  'is_active' => true,
  'stock_quantity' => NULL,
));

            $product = Product::updateOrCreate(['name' => 'Rozela'], array (
  'name' => 'Rozela',
  'category' => 'food',
  'type' => 'drink',
  'description' => NULL,
  'is_active' => true,
  'supplier_id' => NULL,
));
            ProductVariant::updateOrCreate(['product_id' => $product->id, 'measurement' => ''], array (
  'measurement' => NULL,
  'packaging' => 'Unit',
  'items_per_package' => 1,
  'buying_price_per_bottle' => NULL,
  'selling_price_per_bottle' => NULL,
  'selling_price_per_pic' => '0.00',
  'is_active' => true,
  'stock_quantity' => NULL,
));

            $product = Product::updateOrCreate(['name' => 'Kahawa'], array (
  'name' => 'Kahawa',
  'category' => 'food',
  'type' => 'drink',
  'description' => NULL,
  'is_active' => true,
  'supplier_id' => NULL,
));
            ProductVariant::updateOrCreate(['product_id' => $product->id, 'measurement' => ''], array (
  'measurement' => NULL,
  'packaging' => 'Unit',
  'items_per_package' => 1,
  'buying_price_per_bottle' => NULL,
  'selling_price_per_bottle' => NULL,
  'selling_price_per_pic' => '0.00',
  'is_active' => true,
  'stock_quantity' => NULL,
));

            $product = Product::updateOrCreate(['name' => 'Sukari'], array (
  'name' => 'Sukari',
  'category' => 'food',
  'type' => 'drink',
  'description' => NULL,
  'is_active' => true,
  'supplier_id' => NULL,
));
            ProductVariant::updateOrCreate(['product_id' => $product->id, 'measurement' => ''], array (
  'measurement' => NULL,
  'packaging' => 'Unit',
  'items_per_package' => 1,
  'buying_price_per_bottle' => NULL,
  'selling_price_per_bottle' => NULL,
  'selling_price_per_pic' => '0.00',
  'is_active' => true,
  'stock_quantity' => NULL,
));

            $product = Product::updateOrCreate(['name' => 'Mirija ya Juice'], array (
  'name' => 'Mirija ya Juice',
  'category' => 'supplies',
  'type' => 'drink',
  'description' => NULL,
  'is_active' => true,
  'supplier_id' => NULL,
));
            ProductVariant::updateOrCreate(['product_id' => $product->id, 'measurement' => ''], array (
  'measurement' => NULL,
  'packaging' => 'Unit',
  'items_per_package' => 1,
  'buying_price_per_bottle' => NULL,
  'selling_price_per_bottle' => NULL,
  'selling_price_per_pic' => '0.00',
  'is_active' => true,
  'stock_quantity' => NULL,
));

            $product = Product::updateOrCreate(['name' => 'Kitchen Foil'], array (
  'name' => 'Kitchen Foil',
  'category' => 'supplies',
  'type' => 'drink',
  'description' => NULL,
  'is_active' => true,
  'supplier_id' => NULL,
));
            ProductVariant::updateOrCreate(['product_id' => $product->id, 'measurement' => ''], array (
  'measurement' => NULL,
  'packaging' => 'Unit',
  'items_per_package' => 1,
  'buying_price_per_bottle' => NULL,
  'selling_price_per_bottle' => NULL,
  'selling_price_per_pic' => '0.00',
  'is_active' => true,
  'stock_quantity' => NULL,
));

            $product = Product::updateOrCreate(['name' => 'Clean Foil'], array (
  'name' => 'Clean Foil',
  'category' => 'supplies',
  'type' => 'drink',
  'description' => NULL,
  'is_active' => true,
  'supplier_id' => NULL,
));
            ProductVariant::updateOrCreate(['product_id' => $product->id, 'measurement' => ''], array (
  'measurement' => NULL,
  'packaging' => 'Unit',
  'items_per_package' => 1,
  'buying_price_per_bottle' => NULL,
  'selling_price_per_bottle' => NULL,
  'selling_price_per_pic' => '0.00',
  'is_active' => true,
  'stock_quantity' => NULL,
));

            $product = Product::updateOrCreate(['name' => 'Sahani'], array (
  'name' => 'Sahani',
  'category' => 'equipment',
  'type' => 'drink',
  'description' => NULL,
  'is_active' => true,
  'supplier_id' => NULL,
));
            ProductVariant::updateOrCreate(['product_id' => $product->id, 'measurement' => ''], array (
  'measurement' => NULL,
  'packaging' => 'Unit',
  'items_per_package' => 1,
  'buying_price_per_bottle' => NULL,
  'selling_price_per_bottle' => NULL,
  'selling_price_per_pic' => '0.00',
  'is_active' => true,
  'stock_quantity' => NULL,
));

            // 2. Sync Stock Requests (Mapping by name/reference where possible)
            $p = Product::where('name', 'BONITE')->first();
            $v = ProductVariant::where('product_id', $p->id)->where('measurement', '350 ml')->first();
            $u = Staff::where('name', 'BAR KEEPER')->first();
            if ($v && $u) {
                StockRequest::updateOrCreate(
                    ['product_variant_id' => $v->id, 'batch_reference' => '', 'requested_by' => $u->id],
                    array (
  'batch_id' => NULL,
  'batch_reference' => NULL,
  'quantity' => '1.00',
  'unit' => 'packages',
  'status' => 'completed',
  'accountant_approved_at' => 
  \Illuminate\Support\Carbon::__set_state(array(
     'endOfTime' => false,
     'startOfTime' => false,
     'constructedObjectId' => '0000000000001c410000000000000000',
     'clock' => NULL,
     'localMonthsOverflow' => NULL,
     'localYearsOverflow' => NULL,
     'localStrictModeEnabled' => NULL,
     'localHumanDiffOptions' => NULL,
     'localToStringFormat' => NULL,
     'localSerializer' => NULL,
     'localMacros' => NULL,
     'localGenericMacros' => NULL,
     'localFormatFunction' => NULL,
     'localTranslator' => NULL,
     'dumpProperties' => 
    array (
      0 => 'date',
      1 => 'timezone_type',
      2 => 'timezone',
    ),
     'dumpLocale' => NULL,
     'dumpDateProperties' => NULL,
     'date' => '2026-03-24 16:30:09.000000',
     'timezone_type' => 3,
     'timezone' => 'Africa/Dar_es_Salaam',
  )),
  'manager_approved_at' => 
  \Illuminate\Support\Carbon::__set_state(array(
     'endOfTime' => false,
     'startOfTime' => false,
     'constructedObjectId' => '0000000000001c430000000000000000',
     'clock' => NULL,
     'localMonthsOverflow' => NULL,
     'localYearsOverflow' => NULL,
     'localStrictModeEnabled' => NULL,
     'localHumanDiffOptions' => NULL,
     'localToStringFormat' => NULL,
     'localSerializer' => NULL,
     'localMacros' => NULL,
     'localGenericMacros' => NULL,
     'localFormatFunction' => NULL,
     'localTranslator' => NULL,
     'dumpProperties' => 
    array (
      0 => 'date',
      1 => 'timezone_type',
      2 => 'timezone',
    ),
     'dumpLocale' => NULL,
     'dumpDateProperties' => NULL,
     'date' => '2026-03-24 16:31:15.000000',
     'timezone_type' => 3,
     'timezone' => 'Africa/Dar_es_Salaam',
  )),
  'distributed_at' => 
  \Illuminate\Support\Carbon::__set_state(array(
     'endOfTime' => false,
     'startOfTime' => false,
     'constructedObjectId' => '0000000000001c4e0000000000000000',
     'clock' => NULL,
     'localMonthsOverflow' => NULL,
     'localYearsOverflow' => NULL,
     'localStrictModeEnabled' => NULL,
     'localHumanDiffOptions' => NULL,
     'localToStringFormat' => NULL,
     'localSerializer' => NULL,
     'localMacros' => NULL,
     'localGenericMacros' => NULL,
     'localFormatFunction' => NULL,
     'localTranslator' => NULL,
     'dumpProperties' => 
    array (
      0 => 'date',
      1 => 'timezone_type',
      2 => 'timezone',
    ),
     'dumpLocale' => NULL,
     'dumpDateProperties' => NULL,
     'date' => '2026-03-24 16:32:05.000000',
     'timezone_type' => 3,
     'timezone' => 'Africa/Dar_es_Salaam',
  )),
  'notes' => NULL,
  'rejection_reason' => NULL,
  'unit_cost' => NULL,
  'total_cost' => NULL,
)
                );
            }

            $p = Product::where('name', 'BONITE')->first();
            $v = ProductVariant::where('product_id', $p->id)->where('measurement', '350 ml')->first();
            $u = Staff::where('name', 'BAR KEEPER')->first();
            if ($v && $u) {
                StockRequest::updateOrCreate(
                    ['product_variant_id' => $v->id, 'batch_reference' => '', 'requested_by' => $u->id],
                    array (
  'batch_id' => NULL,
  'batch_reference' => NULL,
  'quantity' => '1.00',
  'unit' => 'packages',
  'status' => 'completed',
  'accountant_approved_at' => 
  \Illuminate\Support\Carbon::__set_state(array(
     'endOfTime' => false,
     'startOfTime' => false,
     'constructedObjectId' => '0000000000001c430000000000000000',
     'clock' => NULL,
     'localMonthsOverflow' => NULL,
     'localYearsOverflow' => NULL,
     'localStrictModeEnabled' => NULL,
     'localHumanDiffOptions' => NULL,
     'localToStringFormat' => NULL,
     'localSerializer' => NULL,
     'localMacros' => NULL,
     'localGenericMacros' => NULL,
     'localFormatFunction' => NULL,
     'localTranslator' => NULL,
     'dumpProperties' => 
    array (
      0 => 'date',
      1 => 'timezone_type',
      2 => 'timezone',
    ),
     'dumpLocale' => NULL,
     'dumpDateProperties' => NULL,
     'date' => '2026-03-24 17:39:33.000000',
     'timezone_type' => 3,
     'timezone' => 'Africa/Dar_es_Salaam',
  )),
  'manager_approved_at' => 
  \Illuminate\Support\Carbon::__set_state(array(
     'endOfTime' => false,
     'startOfTime' => false,
     'constructedObjectId' => '0000000000001c410000000000000000',
     'clock' => NULL,
     'localMonthsOverflow' => NULL,
     'localYearsOverflow' => NULL,
     'localStrictModeEnabled' => NULL,
     'localHumanDiffOptions' => NULL,
     'localToStringFormat' => NULL,
     'localSerializer' => NULL,
     'localMacros' => NULL,
     'localGenericMacros' => NULL,
     'localFormatFunction' => NULL,
     'localTranslator' => NULL,
     'dumpProperties' => 
    array (
      0 => 'date',
      1 => 'timezone_type',
      2 => 'timezone',
    ),
     'dumpLocale' => NULL,
     'dumpDateProperties' => NULL,
     'date' => '2026-03-24 17:40:16.000000',
     'timezone_type' => 3,
     'timezone' => 'Africa/Dar_es_Salaam',
  )),
  'distributed_at' => 
  \Illuminate\Support\Carbon::__set_state(array(
     'endOfTime' => false,
     'startOfTime' => false,
     'constructedObjectId' => '0000000000001c400000000000000000',
     'clock' => NULL,
     'localMonthsOverflow' => NULL,
     'localYearsOverflow' => NULL,
     'localStrictModeEnabled' => NULL,
     'localHumanDiffOptions' => NULL,
     'localToStringFormat' => NULL,
     'localSerializer' => NULL,
     'localMacros' => NULL,
     'localGenericMacros' => NULL,
     'localFormatFunction' => NULL,
     'localTranslator' => NULL,
     'dumpProperties' => 
    array (
      0 => 'date',
      1 => 'timezone_type',
      2 => 'timezone',
    ),
     'dumpLocale' => NULL,
     'dumpDateProperties' => NULL,
     'date' => '2026-03-24 17:40:35.000000',
     'timezone_type' => 3,
     'timezone' => 'Africa/Dar_es_Salaam',
  )),
  'notes' => NULL,
  'rejection_reason' => NULL,
  'unit_cost' => NULL,
  'total_cost' => NULL,
)
                );
            }

            $p = Product::where('name', 'Madiko')->first();
            $v = ProductVariant::where('product_id', $p->id)->where('measurement', '0 ml')->first();
            $u = Staff::where('name', 'Chef Master')->first();
            if ($v && $u) {
                StockRequest::updateOrCreate(
                    ['product_variant_id' => $v->id, 'batch_reference' => '', 'requested_by' => $u->id],
                    array (
  'batch_id' => NULL,
  'batch_reference' => NULL,
  'quantity' => '3.00',
  'unit' => 'kg',
  'status' => 'completed',
  'accountant_approved_at' => NULL,
  'manager_approved_at' => 
  \Illuminate\Support\Carbon::__set_state(array(
     'endOfTime' => false,
     'startOfTime' => false,
     'constructedObjectId' => '0000000000001c410000000000000000',
     'clock' => NULL,
     'localMonthsOverflow' => NULL,
     'localYearsOverflow' => NULL,
     'localStrictModeEnabled' => NULL,
     'localHumanDiffOptions' => NULL,
     'localToStringFormat' => NULL,
     'localSerializer' => NULL,
     'localMacros' => NULL,
     'localGenericMacros' => NULL,
     'localFormatFunction' => NULL,
     'localTranslator' => NULL,
     'dumpProperties' => 
    array (
      0 => 'date',
      1 => 'timezone_type',
      2 => 'timezone',
    ),
     'dumpLocale' => NULL,
     'dumpDateProperties' => NULL,
     'date' => '2026-03-25 18:00:19.000000',
     'timezone_type' => 3,
     'timezone' => 'Africa/Dar_es_Salaam',
  )),
  'distributed_at' => 
  \Illuminate\Support\Carbon::__set_state(array(
     'endOfTime' => false,
     'startOfTime' => false,
     'constructedObjectId' => '0000000000001c430000000000000000',
     'clock' => NULL,
     'localMonthsOverflow' => NULL,
     'localYearsOverflow' => NULL,
     'localStrictModeEnabled' => NULL,
     'localHumanDiffOptions' => NULL,
     'localToStringFormat' => NULL,
     'localSerializer' => NULL,
     'localMacros' => NULL,
     'localGenericMacros' => NULL,
     'localFormatFunction' => NULL,
     'localTranslator' => NULL,
     'dumpProperties' => 
    array (
      0 => 'date',
      1 => 'timezone_type',
      2 => 'timezone',
    ),
     'dumpLocale' => NULL,
     'dumpDateProperties' => NULL,
     'date' => '2026-03-25 18:04:36.000000',
     'timezone_type' => 3,
     'timezone' => 'Africa/Dar_es_Salaam',
  )),
  'notes' => NULL,
  'rejection_reason' => NULL,
  'unit_cost' => NULL,
  'total_cost' => NULL,
)
                );
            }

            $p = Product::where('name', 'Madiko')->first();
            $v = ProductVariant::where('product_id', $p->id)->where('measurement', '0 ml')->first();
            $u = Staff::where('name', 'Chef Master')->first();
            if ($v && $u) {
                StockRequest::updateOrCreate(
                    ['product_variant_id' => $v->id, 'batch_reference' => '', 'requested_by' => $u->id],
                    array (
  'batch_id' => NULL,
  'batch_reference' => NULL,
  'quantity' => '3.00',
  'unit' => 'kg',
  'status' => 'completed',
  'accountant_approved_at' => NULL,
  'manager_approved_at' => 
  \Illuminate\Support\Carbon::__set_state(array(
     'endOfTime' => false,
     'startOfTime' => false,
     'constructedObjectId' => '0000000000001c410000000000000000',
     'clock' => NULL,
     'localMonthsOverflow' => NULL,
     'localYearsOverflow' => NULL,
     'localStrictModeEnabled' => NULL,
     'localHumanDiffOptions' => NULL,
     'localToStringFormat' => NULL,
     'localSerializer' => NULL,
     'localMacros' => NULL,
     'localGenericMacros' => NULL,
     'localFormatFunction' => NULL,
     'localTranslator' => NULL,
     'dumpProperties' => 
    array (
      0 => 'date',
      1 => 'timezone_type',
      2 => 'timezone',
    ),
     'dumpLocale' => NULL,
     'dumpDateProperties' => NULL,
     'date' => '2026-03-26 10:43:34.000000',
     'timezone_type' => 3,
     'timezone' => 'Africa/Dar_es_Salaam',
  )),
  'distributed_at' => 
  \Illuminate\Support\Carbon::__set_state(array(
     'endOfTime' => false,
     'startOfTime' => false,
     'constructedObjectId' => '0000000000001c400000000000000000',
     'clock' => NULL,
     'localMonthsOverflow' => NULL,
     'localYearsOverflow' => NULL,
     'localStrictModeEnabled' => NULL,
     'localHumanDiffOptions' => NULL,
     'localToStringFormat' => NULL,
     'localSerializer' => NULL,
     'localMacros' => NULL,
     'localGenericMacros' => NULL,
     'localFormatFunction' => NULL,
     'localTranslator' => NULL,
     'dumpProperties' => 
    array (
      0 => 'date',
      1 => 'timezone_type',
      2 => 'timezone',
    ),
     'dumpLocale' => NULL,
     'dumpDateProperties' => NULL,
     'date' => '2026-03-26 10:44:24.000000',
     'timezone_type' => 3,
     'timezone' => 'Africa/Dar_es_Salaam',
  )),
  'notes' => NULL,
  'rejection_reason' => NULL,
  'unit_cost' => NULL,
  'total_cost' => NULL,
)
                );
            }

            $p = Product::where('name', 'Madiko')->first();
            $v = ProductVariant::where('product_id', $p->id)->where('measurement', '0 ml')->first();
            $u = Staff::where('name', 'Chef Master')->first();
            if ($v && $u) {
                StockRequest::updateOrCreate(
                    ['product_variant_id' => $v->id, 'batch_reference' => '', 'requested_by' => $u->id],
                    array (
  'batch_id' => NULL,
  'batch_reference' => NULL,
  'quantity' => '2.00',
  'unit' => 'kg',
  'status' => 'completed',
  'accountant_approved_at' => NULL,
  'manager_approved_at' => 
  \Illuminate\Support\Carbon::__set_state(array(
     'endOfTime' => false,
     'startOfTime' => false,
     'constructedObjectId' => '0000000000001c410000000000000000',
     'clock' => NULL,
     'localMonthsOverflow' => NULL,
     'localYearsOverflow' => NULL,
     'localStrictModeEnabled' => NULL,
     'localHumanDiffOptions' => NULL,
     'localToStringFormat' => NULL,
     'localSerializer' => NULL,
     'localMacros' => NULL,
     'localGenericMacros' => NULL,
     'localFormatFunction' => NULL,
     'localTranslator' => NULL,
     'dumpProperties' => 
    array (
      0 => 'date',
      1 => 'timezone_type',
      2 => 'timezone',
    ),
     'dumpLocale' => NULL,
     'dumpDateProperties' => NULL,
     'date' => '2026-03-26 10:43:38.000000',
     'timezone_type' => 3,
     'timezone' => 'Africa/Dar_es_Salaam',
  )),
  'distributed_at' => 
  \Illuminate\Support\Carbon::__set_state(array(
     'endOfTime' => false,
     'startOfTime' => false,
     'constructedObjectId' => '0000000000001c430000000000000000',
     'clock' => NULL,
     'localMonthsOverflow' => NULL,
     'localYearsOverflow' => NULL,
     'localStrictModeEnabled' => NULL,
     'localHumanDiffOptions' => NULL,
     'localToStringFormat' => NULL,
     'localSerializer' => NULL,
     'localMacros' => NULL,
     'localGenericMacros' => NULL,
     'localFormatFunction' => NULL,
     'localTranslator' => NULL,
     'dumpProperties' => 
    array (
      0 => 'date',
      1 => 'timezone_type',
      2 => 'timezone',
    ),
     'dumpLocale' => NULL,
     'dumpDateProperties' => NULL,
     'date' => '2026-03-26 10:44:29.000000',
     'timezone_type' => 3,
     'timezone' => 'Africa/Dar_es_Salaam',
  )),
  'notes' => NULL,
  'rejection_reason' => NULL,
  'unit_cost' => NULL,
  'total_cost' => NULL,
)
                );
            }

            $p = Product::where('name', 'Madiko')->first();
            $v = ProductVariant::where('product_id', $p->id)->where('measurement', 'ml')->first();
            $u = Staff::where('name', 'Chef Master')->first();
            if ($v && $u) {
                StockRequest::updateOrCreate(
                    ['product_variant_id' => $v->id, 'batch_reference' => '', 'requested_by' => $u->id],
                    array (
  'batch_id' => NULL,
  'batch_reference' => NULL,
  'quantity' => '1.00',
  'unit' => 'kg',
  'status' => 'completed',
  'accountant_approved_at' => NULL,
  'manager_approved_at' => 
  \Illuminate\Support\Carbon::__set_state(array(
     'endOfTime' => false,
     'startOfTime' => false,
     'constructedObjectId' => '0000000000001c410000000000000000',
     'clock' => NULL,
     'localMonthsOverflow' => NULL,
     'localYearsOverflow' => NULL,
     'localStrictModeEnabled' => NULL,
     'localHumanDiffOptions' => NULL,
     'localToStringFormat' => NULL,
     'localSerializer' => NULL,
     'localMacros' => NULL,
     'localGenericMacros' => NULL,
     'localFormatFunction' => NULL,
     'localTranslator' => NULL,
     'dumpProperties' => 
    array (
      0 => 'date',
      1 => 'timezone_type',
      2 => 'timezone',
    ),
     'dumpLocale' => NULL,
     'dumpDateProperties' => NULL,
     'date' => '2026-03-26 11:12:38.000000',
     'timezone_type' => 3,
     'timezone' => 'Africa/Dar_es_Salaam',
  )),
  'distributed_at' => 
  \Illuminate\Support\Carbon::__set_state(array(
     'endOfTime' => false,
     'startOfTime' => false,
     'constructedObjectId' => '0000000000001c400000000000000000',
     'clock' => NULL,
     'localMonthsOverflow' => NULL,
     'localYearsOverflow' => NULL,
     'localStrictModeEnabled' => NULL,
     'localHumanDiffOptions' => NULL,
     'localToStringFormat' => NULL,
     'localSerializer' => NULL,
     'localMacros' => NULL,
     'localGenericMacros' => NULL,
     'localFormatFunction' => NULL,
     'localTranslator' => NULL,
     'dumpProperties' => 
    array (
      0 => 'date',
      1 => 'timezone_type',
      2 => 'timezone',
    ),
     'dumpLocale' => NULL,
     'dumpDateProperties' => NULL,
     'date' => '2026-03-26 11:13:36.000000',
     'timezone_type' => 3,
     'timezone' => 'Africa/Dar_es_Salaam',
  )),
  'notes' => NULL,
  'rejection_reason' => NULL,
  'unit_cost' => NULL,
  'total_cost' => NULL,
)
                );
            }

            $p = Product::where('name', 'Madiko')->first();
            $v = ProductVariant::where('product_id', $p->id)->where('measurement', '0 ml')->first();
            $u = Staff::where('name', 'Chef Master')->first();
            if ($v && $u) {
                StockRequest::updateOrCreate(
                    ['product_variant_id' => $v->id, 'batch_reference' => '', 'requested_by' => $u->id],
                    array (
  'batch_id' => NULL,
  'batch_reference' => NULL,
  'quantity' => '4.00',
  'unit' => 'kg',
  'status' => 'completed',
  'accountant_approved_at' => NULL,
  'manager_approved_at' => 
  \Illuminate\Support\Carbon::__set_state(array(
     'endOfTime' => false,
     'startOfTime' => false,
     'constructedObjectId' => '0000000000001c410000000000000000',
     'clock' => NULL,
     'localMonthsOverflow' => NULL,
     'localYearsOverflow' => NULL,
     'localStrictModeEnabled' => NULL,
     'localHumanDiffOptions' => NULL,
     'localToStringFormat' => NULL,
     'localSerializer' => NULL,
     'localMacros' => NULL,
     'localGenericMacros' => NULL,
     'localFormatFunction' => NULL,
     'localTranslator' => NULL,
     'dumpProperties' => 
    array (
      0 => 'date',
      1 => 'timezone_type',
      2 => 'timezone',
    ),
     'dumpLocale' => NULL,
     'dumpDateProperties' => NULL,
     'date' => '2026-03-26 11:14:48.000000',
     'timezone_type' => 3,
     'timezone' => 'Africa/Dar_es_Salaam',
  )),
  'distributed_at' => 
  \Illuminate\Support\Carbon::__set_state(array(
     'endOfTime' => false,
     'startOfTime' => false,
     'constructedObjectId' => '0000000000001c430000000000000000',
     'clock' => NULL,
     'localMonthsOverflow' => NULL,
     'localYearsOverflow' => NULL,
     'localStrictModeEnabled' => NULL,
     'localHumanDiffOptions' => NULL,
     'localToStringFormat' => NULL,
     'localSerializer' => NULL,
     'localMacros' => NULL,
     'localGenericMacros' => NULL,
     'localFormatFunction' => NULL,
     'localTranslator' => NULL,
     'dumpProperties' => 
    array (
      0 => 'date',
      1 => 'timezone_type',
      2 => 'timezone',
    ),
     'dumpLocale' => NULL,
     'dumpDateProperties' => NULL,
     'date' => '2026-03-26 11:15:05.000000',
     'timezone_type' => 3,
     'timezone' => 'Africa/Dar_es_Salaam',
  )),
  'notes' => NULL,
  'rejection_reason' => NULL,
  'unit_cost' => NULL,
  'total_cost' => NULL,
)
                );
            }

            $p = Product::where('name', 'Madiko')->first();
            $v = ProductVariant::where('product_id', $p->id)->where('measurement', '0 ml')->first();
            $u = Staff::where('name', 'Chef Master')->first();
            if ($v && $u) {
                StockRequest::updateOrCreate(
                    ['product_variant_id' => $v->id, 'batch_reference' => '', 'requested_by' => $u->id],
                    array (
  'batch_id' => NULL,
  'batch_reference' => NULL,
  'quantity' => '4.00',
  'unit' => 'kg',
  'status' => 'completed',
  'accountant_approved_at' => NULL,
  'manager_approved_at' => 
  \Illuminate\Support\Carbon::__set_state(array(
     'endOfTime' => false,
     'startOfTime' => false,
     'constructedObjectId' => '0000000000001c410000000000000000',
     'clock' => NULL,
     'localMonthsOverflow' => NULL,
     'localYearsOverflow' => NULL,
     'localStrictModeEnabled' => NULL,
     'localHumanDiffOptions' => NULL,
     'localToStringFormat' => NULL,
     'localSerializer' => NULL,
     'localMacros' => NULL,
     'localGenericMacros' => NULL,
     'localFormatFunction' => NULL,
     'localTranslator' => NULL,
     'dumpProperties' => 
    array (
      0 => 'date',
      1 => 'timezone_type',
      2 => 'timezone',
    ),
     'dumpLocale' => NULL,
     'dumpDateProperties' => NULL,
     'date' => '2026-03-27 09:05:59.000000',
     'timezone_type' => 3,
     'timezone' => 'Africa/Dar_es_Salaam',
  )),
  'distributed_at' => 
  \Illuminate\Support\Carbon::__set_state(array(
     'endOfTime' => false,
     'startOfTime' => false,
     'constructedObjectId' => '0000000000001c400000000000000000',
     'clock' => NULL,
     'localMonthsOverflow' => NULL,
     'localYearsOverflow' => NULL,
     'localStrictModeEnabled' => NULL,
     'localHumanDiffOptions' => NULL,
     'localToStringFormat' => NULL,
     'localSerializer' => NULL,
     'localMacros' => NULL,
     'localGenericMacros' => NULL,
     'localFormatFunction' => NULL,
     'localTranslator' => NULL,
     'dumpProperties' => 
    array (
      0 => 'date',
      1 => 'timezone_type',
      2 => 'timezone',
    ),
     'dumpLocale' => NULL,
     'dumpDateProperties' => NULL,
     'date' => '2026-03-27 09:06:52.000000',
     'timezone_type' => 3,
     'timezone' => 'Africa/Dar_es_Salaam',
  )),
  'notes' => NULL,
  'rejection_reason' => NULL,
  'unit_cost' => NULL,
  'total_cost' => NULL,
)
                );
            }

            $p = Product::where('name', 'Madiko')->first();
            $v = ProductVariant::where('product_id', $p->id)->where('measurement', '0 ml')->first();
            $u = Staff::where('name', 'Chef Master')->first();
            if ($v && $u) {
                StockRequest::updateOrCreate(
                    ['product_variant_id' => $v->id, 'batch_reference' => '', 'requested_by' => $u->id],
                    array (
  'batch_id' => NULL,
  'batch_reference' => NULL,
  'quantity' => '4.00',
  'unit' => 'kg',
  'status' => 'completed',
  'accountant_approved_at' => NULL,
  'manager_approved_at' => 
  \Illuminate\Support\Carbon::__set_state(array(
     'endOfTime' => false,
     'startOfTime' => false,
     'constructedObjectId' => '0000000000001c410000000000000000',
     'clock' => NULL,
     'localMonthsOverflow' => NULL,
     'localYearsOverflow' => NULL,
     'localStrictModeEnabled' => NULL,
     'localHumanDiffOptions' => NULL,
     'localToStringFormat' => NULL,
     'localSerializer' => NULL,
     'localMacros' => NULL,
     'localGenericMacros' => NULL,
     'localFormatFunction' => NULL,
     'localTranslator' => NULL,
     'dumpProperties' => 
    array (
      0 => 'date',
      1 => 'timezone_type',
      2 => 'timezone',
    ),
     'dumpLocale' => NULL,
     'dumpDateProperties' => NULL,
     'date' => '2026-03-27 09:06:03.000000',
     'timezone_type' => 3,
     'timezone' => 'Africa/Dar_es_Salaam',
  )),
  'distributed_at' => 
  \Illuminate\Support\Carbon::__set_state(array(
     'endOfTime' => false,
     'startOfTime' => false,
     'constructedObjectId' => '0000000000001c430000000000000000',
     'clock' => NULL,
     'localMonthsOverflow' => NULL,
     'localYearsOverflow' => NULL,
     'localStrictModeEnabled' => NULL,
     'localHumanDiffOptions' => NULL,
     'localToStringFormat' => NULL,
     'localSerializer' => NULL,
     'localMacros' => NULL,
     'localGenericMacros' => NULL,
     'localFormatFunction' => NULL,
     'localTranslator' => NULL,
     'dumpProperties' => 
    array (
      0 => 'date',
      1 => 'timezone_type',
      2 => 'timezone',
    ),
     'dumpLocale' => NULL,
     'dumpDateProperties' => NULL,
     'date' => '2026-03-27 09:06:57.000000',
     'timezone_type' => 3,
     'timezone' => 'Africa/Dar_es_Salaam',
  )),
  'notes' => NULL,
  'rejection_reason' => NULL,
  'unit_cost' => NULL,
  'total_cost' => NULL,
)
                );
            }

            $p = Product::where('name', 'Madiko')->first();
            $v = ProductVariant::where('product_id', $p->id)->where('measurement', 'ml')->first();
            $u = Staff::where('name', 'Chef Master')->first();
            if ($v && $u) {
                StockRequest::updateOrCreate(
                    ['product_variant_id' => $v->id, 'batch_reference' => '', 'requested_by' => $u->id],
                    array (
  'batch_id' => NULL,
  'batch_reference' => NULL,
  'quantity' => '3.00',
  'unit' => 'kg',
  'status' => 'completed',
  'accountant_approved_at' => NULL,
  'manager_approved_at' => 
  \Illuminate\Support\Carbon::__set_state(array(
     'endOfTime' => false,
     'startOfTime' => false,
     'constructedObjectId' => '0000000000001c410000000000000000',
     'clock' => NULL,
     'localMonthsOverflow' => NULL,
     'localYearsOverflow' => NULL,
     'localStrictModeEnabled' => NULL,
     'localHumanDiffOptions' => NULL,
     'localToStringFormat' => NULL,
     'localSerializer' => NULL,
     'localMacros' => NULL,
     'localGenericMacros' => NULL,
     'localFormatFunction' => NULL,
     'localTranslator' => NULL,
     'dumpProperties' => 
    array (
      0 => 'date',
      1 => 'timezone_type',
      2 => 'timezone',
    ),
     'dumpLocale' => NULL,
     'dumpDateProperties' => NULL,
     'date' => '2026-03-27 09:06:07.000000',
     'timezone_type' => 3,
     'timezone' => 'Africa/Dar_es_Salaam',
  )),
  'distributed_at' => 
  \Illuminate\Support\Carbon::__set_state(array(
     'endOfTime' => false,
     'startOfTime' => false,
     'constructedObjectId' => '0000000000001c400000000000000000',
     'clock' => NULL,
     'localMonthsOverflow' => NULL,
     'localYearsOverflow' => NULL,
     'localStrictModeEnabled' => NULL,
     'localHumanDiffOptions' => NULL,
     'localToStringFormat' => NULL,
     'localSerializer' => NULL,
     'localMacros' => NULL,
     'localGenericMacros' => NULL,
     'localFormatFunction' => NULL,
     'localTranslator' => NULL,
     'dumpProperties' => 
    array (
      0 => 'date',
      1 => 'timezone_type',
      2 => 'timezone',
    ),
     'dumpLocale' => NULL,
     'dumpDateProperties' => NULL,
     'date' => '2026-03-27 09:07:01.000000',
     'timezone_type' => 3,
     'timezone' => 'Africa/Dar_es_Salaam',
  )),
  'notes' => NULL,
  'rejection_reason' => NULL,
  'unit_cost' => NULL,
  'total_cost' => NULL,
)
                );
            }

            $p = Product::where('name', 'Usafi')->first();
            $v = ProductVariant::where('product_id', $p->id)->where('measurement', 'ml')->first();
            $u = Staff::where('name', 'Benjamini Bufumbe')->first();
            if ($v && $u) {
                StockRequest::updateOrCreate(
                    ['product_variant_id' => $v->id, 'batch_reference' => '', 'requested_by' => $u->id],
                    array (
  'batch_id' => NULL,
  'batch_reference' => NULL,
  'quantity' => '5.00',
  'unit' => 'other',
  'status' => 'completed',
  'accountant_approved_at' => NULL,
  'manager_approved_at' => 
  \Illuminate\Support\Carbon::__set_state(array(
     'endOfTime' => false,
     'startOfTime' => false,
     'constructedObjectId' => '0000000000001c410000000000000000',
     'clock' => NULL,
     'localMonthsOverflow' => NULL,
     'localYearsOverflow' => NULL,
     'localStrictModeEnabled' => NULL,
     'localHumanDiffOptions' => NULL,
     'localToStringFormat' => NULL,
     'localSerializer' => NULL,
     'localMacros' => NULL,
     'localGenericMacros' => NULL,
     'localFormatFunction' => NULL,
     'localTranslator' => NULL,
     'dumpProperties' => 
    array (
      0 => 'date',
      1 => 'timezone_type',
      2 => 'timezone',
    ),
     'dumpLocale' => NULL,
     'dumpDateProperties' => NULL,
     'date' => '2026-03-26 17:51:54.000000',
     'timezone_type' => 3,
     'timezone' => 'Africa/Dar_es_Salaam',
  )),
  'distributed_at' => 
  \Illuminate\Support\Carbon::__set_state(array(
     'endOfTime' => false,
     'startOfTime' => false,
     'constructedObjectId' => '0000000000001c430000000000000000',
     'clock' => NULL,
     'localMonthsOverflow' => NULL,
     'localYearsOverflow' => NULL,
     'localStrictModeEnabled' => NULL,
     'localHumanDiffOptions' => NULL,
     'localToStringFormat' => NULL,
     'localSerializer' => NULL,
     'localMacros' => NULL,
     'localGenericMacros' => NULL,
     'localFormatFunction' => NULL,
     'localTranslator' => NULL,
     'dumpProperties' => 
    array (
      0 => 'date',
      1 => 'timezone_type',
      2 => 'timezone',
    ),
     'dumpLocale' => NULL,
     'dumpDateProperties' => NULL,
     'date' => '2026-03-26 17:53:19.000000',
     'timezone_type' => 3,
     'timezone' => 'Africa/Dar_es_Salaam',
  )),
  'notes' => NULL,
  'rejection_reason' => NULL,
  'unit_cost' => NULL,
  'total_cost' => NULL,
)
                );
            }

            $p = Product::where('name', 'Madiko')->first();
            $v = ProductVariant::where('product_id', $p->id)->where('measurement', 'ml')->first();
            $u = Staff::where('name', 'Chef Master')->first();
            if ($v && $u) {
                StockRequest::updateOrCreate(
                    ['product_variant_id' => $v->id, 'batch_reference' => '', 'requested_by' => $u->id],
                    array (
  'batch_id' => NULL,
  'batch_reference' => NULL,
  'quantity' => '4.00',
  'unit' => 'kg',
  'status' => 'completed',
  'accountant_approved_at' => NULL,
  'manager_approved_at' => 
  \Illuminate\Support\Carbon::__set_state(array(
     'endOfTime' => false,
     'startOfTime' => false,
     'constructedObjectId' => '0000000000001c410000000000000000',
     'clock' => NULL,
     'localMonthsOverflow' => NULL,
     'localYearsOverflow' => NULL,
     'localStrictModeEnabled' => NULL,
     'localHumanDiffOptions' => NULL,
     'localToStringFormat' => NULL,
     'localSerializer' => NULL,
     'localMacros' => NULL,
     'localGenericMacros' => NULL,
     'localFormatFunction' => NULL,
     'localTranslator' => NULL,
     'dumpProperties' => 
    array (
      0 => 'date',
      1 => 'timezone_type',
      2 => 'timezone',
    ),
     'dumpLocale' => NULL,
     'dumpDateProperties' => NULL,
     'date' => '2026-03-27 14:47:34.000000',
     'timezone_type' => 3,
     'timezone' => 'Africa/Dar_es_Salaam',
  )),
  'distributed_at' => 
  \Illuminate\Support\Carbon::__set_state(array(
     'endOfTime' => false,
     'startOfTime' => false,
     'constructedObjectId' => '0000000000001c400000000000000000',
     'clock' => NULL,
     'localMonthsOverflow' => NULL,
     'localYearsOverflow' => NULL,
     'localStrictModeEnabled' => NULL,
     'localHumanDiffOptions' => NULL,
     'localToStringFormat' => NULL,
     'localSerializer' => NULL,
     'localMacros' => NULL,
     'localGenericMacros' => NULL,
     'localFormatFunction' => NULL,
     'localTranslator' => NULL,
     'dumpProperties' => 
    array (
      0 => 'date',
      1 => 'timezone_type',
      2 => 'timezone',
    ),
     'dumpLocale' => NULL,
     'dumpDateProperties' => NULL,
     'date' => '2026-03-27 14:49:32.000000',
     'timezone_type' => 3,
     'timezone' => 'Africa/Dar_es_Salaam',
  )),
  'notes' => NULL,
  'rejection_reason' => NULL,
  'unit_cost' => '1250.00',
  'total_cost' => '5000.00',
)
                );
            }

            $p = Product::where('name', 'BONITE')->first();
            $v = ProductVariant::where('product_id', $p->id)->where('measurement', '350 ml')->first();
            $u = Staff::where('name', 'BAR KEEPER')->first();
            if ($v && $u) {
                StockRequest::updateOrCreate(
                    ['product_variant_id' => $v->id, 'batch_reference' => '', 'requested_by' => $u->id],
                    array (
  'batch_id' => NULL,
  'batch_reference' => NULL,
  'quantity' => '1.00',
  'unit' => 'packages',
  'status' => 'completed',
  'accountant_approved_at' => 
  \Illuminate\Support\Carbon::__set_state(array(
     'endOfTime' => false,
     'startOfTime' => false,
     'constructedObjectId' => '0000000000001c410000000000000000',
     'clock' => NULL,
     'localMonthsOverflow' => NULL,
     'localYearsOverflow' => NULL,
     'localStrictModeEnabled' => NULL,
     'localHumanDiffOptions' => NULL,
     'localToStringFormat' => NULL,
     'localSerializer' => NULL,
     'localMacros' => NULL,
     'localGenericMacros' => NULL,
     'localFormatFunction' => NULL,
     'localTranslator' => NULL,
     'dumpProperties' => 
    array (
      0 => 'date',
      1 => 'timezone_type',
      2 => 'timezone',
    ),
     'dumpLocale' => NULL,
     'dumpDateProperties' => NULL,
     'date' => '2026-03-27 17:03:29.000000',
     'timezone_type' => 3,
     'timezone' => 'Africa/Dar_es_Salaam',
  )),
  'manager_approved_at' => 
  \Illuminate\Support\Carbon::__set_state(array(
     'endOfTime' => false,
     'startOfTime' => false,
     'constructedObjectId' => '0000000000001c430000000000000000',
     'clock' => NULL,
     'localMonthsOverflow' => NULL,
     'localYearsOverflow' => NULL,
     'localStrictModeEnabled' => NULL,
     'localHumanDiffOptions' => NULL,
     'localToStringFormat' => NULL,
     'localSerializer' => NULL,
     'localMacros' => NULL,
     'localGenericMacros' => NULL,
     'localFormatFunction' => NULL,
     'localTranslator' => NULL,
     'dumpProperties' => 
    array (
      0 => 'date',
      1 => 'timezone_type',
      2 => 'timezone',
    ),
     'dumpLocale' => NULL,
     'dumpDateProperties' => NULL,
     'date' => '2026-03-27 17:04:21.000000',
     'timezone_type' => 3,
     'timezone' => 'Africa/Dar_es_Salaam',
  )),
  'distributed_at' => 
  \Illuminate\Support\Carbon::__set_state(array(
     'endOfTime' => false,
     'startOfTime' => false,
     'constructedObjectId' => '0000000000001c4e0000000000000000',
     'clock' => NULL,
     'localMonthsOverflow' => NULL,
     'localYearsOverflow' => NULL,
     'localStrictModeEnabled' => NULL,
     'localHumanDiffOptions' => NULL,
     'localToStringFormat' => NULL,
     'localSerializer' => NULL,
     'localMacros' => NULL,
     'localGenericMacros' => NULL,
     'localFormatFunction' => NULL,
     'localTranslator' => NULL,
     'dumpProperties' => 
    array (
      0 => 'date',
      1 => 'timezone_type',
      2 => 'timezone',
    ),
     'dumpLocale' => NULL,
     'dumpDateProperties' => NULL,
     'date' => '2026-03-27 17:05:07.000000',
     'timezone_type' => 3,
     'timezone' => 'Africa/Dar_es_Salaam',
  )),
  'notes' => NULL,
  'rejection_reason' => NULL,
  'unit_cost' => '375.00',
  'total_cost' => '9000.00',
)
                );
            }

            $p = Product::where('name', 'Madiko')->first();
            $v = ProductVariant::where('product_id', $p->id)->where('measurement', '0 ml')->first();
            $u = Staff::where('name', 'Chef Master')->first();
            if ($v && $u) {
                StockRequest::updateOrCreate(
                    ['product_variant_id' => $v->id, 'batch_reference' => '', 'requested_by' => $u->id],
                    array (
  'batch_id' => NULL,
  'batch_reference' => NULL,
  'quantity' => '2.00',
  'unit' => 'kg',
  'status' => 'completed',
  'accountant_approved_at' => NULL,
  'manager_approved_at' => 
  \Illuminate\Support\Carbon::__set_state(array(
     'endOfTime' => false,
     'startOfTime' => false,
     'constructedObjectId' => '0000000000001c430000000000000000',
     'clock' => NULL,
     'localMonthsOverflow' => NULL,
     'localYearsOverflow' => NULL,
     'localStrictModeEnabled' => NULL,
     'localHumanDiffOptions' => NULL,
     'localToStringFormat' => NULL,
     'localSerializer' => NULL,
     'localMacros' => NULL,
     'localGenericMacros' => NULL,
     'localFormatFunction' => NULL,
     'localTranslator' => NULL,
     'dumpProperties' => 
    array (
      0 => 'date',
      1 => 'timezone_type',
      2 => 'timezone',
    ),
     'dumpLocale' => NULL,
     'dumpDateProperties' => NULL,
     'date' => '2026-03-27 17:15:00.000000',
     'timezone_type' => 3,
     'timezone' => 'Africa/Dar_es_Salaam',
  )),
  'distributed_at' => 
  \Illuminate\Support\Carbon::__set_state(array(
     'endOfTime' => false,
     'startOfTime' => false,
     'constructedObjectId' => '0000000000001c410000000000000000',
     'clock' => NULL,
     'localMonthsOverflow' => NULL,
     'localYearsOverflow' => NULL,
     'localStrictModeEnabled' => NULL,
     'localHumanDiffOptions' => NULL,
     'localToStringFormat' => NULL,
     'localSerializer' => NULL,
     'localMacros' => NULL,
     'localGenericMacros' => NULL,
     'localFormatFunction' => NULL,
     'localTranslator' => NULL,
     'dumpProperties' => 
    array (
      0 => 'date',
      1 => 'timezone_type',
      2 => 'timezone',
    ),
     'dumpLocale' => NULL,
     'dumpDateProperties' => NULL,
     'date' => '2026-03-27 17:15:11.000000',
     'timezone_type' => 3,
     'timezone' => 'Africa/Dar_es_Salaam',
  )),
  'notes' => NULL,
  'rejection_reason' => NULL,
  'unit_cost' => '1125.00',
  'total_cost' => '2250.00',
)
                );
            }

            $p = Product::where('name', 'Clean')->first();
            $v = ProductVariant::where('product_id', $p->id)->where('measurement', 'ml')->first();
            $u = Staff::where('name', 'Benjamini Bufumbe')->first();
            if ($v && $u) {
                StockRequest::updateOrCreate(
                    ['product_variant_id' => $v->id, 'batch_reference' => '', 'requested_by' => $u->id],
                    array (
  'batch_id' => NULL,
  'batch_reference' => NULL,
  'quantity' => '10.00',
  'unit' => 'other',
  'status' => 'completed',
  'accountant_approved_at' => NULL,
  'manager_approved_at' => 
  \Illuminate\Support\Carbon::__set_state(array(
     'endOfTime' => false,
     'startOfTime' => false,
     'constructedObjectId' => '0000000000001c430000000000000000',
     'clock' => NULL,
     'localMonthsOverflow' => NULL,
     'localYearsOverflow' => NULL,
     'localStrictModeEnabled' => NULL,
     'localHumanDiffOptions' => NULL,
     'localToStringFormat' => NULL,
     'localSerializer' => NULL,
     'localMacros' => NULL,
     'localGenericMacros' => NULL,
     'localFormatFunction' => NULL,
     'localTranslator' => NULL,
     'dumpProperties' => 
    array (
      0 => 'date',
      1 => 'timezone_type',
      2 => 'timezone',
    ),
     'dumpLocale' => NULL,
     'dumpDateProperties' => NULL,
     'date' => '2026-03-27 17:20:10.000000',
     'timezone_type' => 3,
     'timezone' => 'Africa/Dar_es_Salaam',
  )),
  'distributed_at' => 
  \Illuminate\Support\Carbon::__set_state(array(
     'endOfTime' => false,
     'startOfTime' => false,
     'constructedObjectId' => '0000000000001c4e0000000000000000',
     'clock' => NULL,
     'localMonthsOverflow' => NULL,
     'localYearsOverflow' => NULL,
     'localStrictModeEnabled' => NULL,
     'localHumanDiffOptions' => NULL,
     'localToStringFormat' => NULL,
     'localSerializer' => NULL,
     'localMacros' => NULL,
     'localGenericMacros' => NULL,
     'localFormatFunction' => NULL,
     'localTranslator' => NULL,
     'dumpProperties' => 
    array (
      0 => 'date',
      1 => 'timezone_type',
      2 => 'timezone',
    ),
     'dumpLocale' => NULL,
     'dumpDateProperties' => NULL,
     'date' => '2026-03-27 17:20:58.000000',
     'timezone_type' => 3,
     'timezone' => 'Africa/Dar_es_Salaam',
  )),
  'notes' => NULL,
  'rejection_reason' => NULL,
  'unit_cost' => '3000.00',
  'total_cost' => '30000.00',
)
                );
            }

            $p = Product::where('name', 'L/Water')->first();
            $v = ProductVariant::where('product_id', $p->id)->where('measurement', '0 ml')->first();
            $u = Staff::where('name', 'BAR KEEPER')->first();
            if ($v && $u) {
                StockRequest::updateOrCreate(
                    ['product_variant_id' => $v->id, 'batch_reference' => '', 'requested_by' => $u->id],
                    array (
  'batch_id' => NULL,
  'batch_reference' => NULL,
  'quantity' => '2.00',
  'unit' => 'packages',
  'status' => 'completed',
  'accountant_approved_at' => NULL,
  'manager_approved_at' => 
  \Illuminate\Support\Carbon::__set_state(array(
     'endOfTime' => false,
     'startOfTime' => false,
     'constructedObjectId' => '0000000000001c430000000000000000',
     'clock' => NULL,
     'localMonthsOverflow' => NULL,
     'localYearsOverflow' => NULL,
     'localStrictModeEnabled' => NULL,
     'localHumanDiffOptions' => NULL,
     'localToStringFormat' => NULL,
     'localSerializer' => NULL,
     'localMacros' => NULL,
     'localGenericMacros' => NULL,
     'localFormatFunction' => NULL,
     'localTranslator' => NULL,
     'dumpProperties' => 
    array (
      0 => 'date',
      1 => 'timezone_type',
      2 => 'timezone',
    ),
     'dumpLocale' => NULL,
     'dumpDateProperties' => NULL,
     'date' => '2026-04-06 12:13:39.000000',
     'timezone_type' => 3,
     'timezone' => 'Africa/Dar_es_Salaam',
  )),
  'distributed_at' => 
  \Illuminate\Support\Carbon::__set_state(array(
     'endOfTime' => false,
     'startOfTime' => false,
     'constructedObjectId' => '0000000000001c410000000000000000',
     'clock' => NULL,
     'localMonthsOverflow' => NULL,
     'localYearsOverflow' => NULL,
     'localStrictModeEnabled' => NULL,
     'localHumanDiffOptions' => NULL,
     'localToStringFormat' => NULL,
     'localSerializer' => NULL,
     'localMacros' => NULL,
     'localGenericMacros' => NULL,
     'localFormatFunction' => NULL,
     'localTranslator' => NULL,
     'dumpProperties' => 
    array (
      0 => 'date',
      1 => 'timezone_type',
      2 => 'timezone',
    ),
     'dumpLocale' => NULL,
     'dumpDateProperties' => NULL,
     'date' => '2026-04-06 12:14:10.000000',
     'timezone_type' => 3,
     'timezone' => 'Africa/Dar_es_Salaam',
  )),
  'notes' => NULL,
  'rejection_reason' => NULL,
  'unit_cost' => '7000.00',
  'total_cost' => '14000.00',
)
                );
            }

            $p = Product::where('name', 'M/Water')->first();
            $v = ProductVariant::where('product_id', $p->id)->where('measurement', '0 ml')->first();
            $u = Staff::where('name', 'BAR KEEPER')->first();
            if ($v && $u) {
                StockRequest::updateOrCreate(
                    ['product_variant_id' => $v->id, 'batch_reference' => '', 'requested_by' => $u->id],
                    array (
  'batch_id' => NULL,
  'batch_reference' => NULL,
  'quantity' => '1.00',
  'unit' => 'packages',
  'status' => 'completed',
  'accountant_approved_at' => NULL,
  'manager_approved_at' => 
  \Illuminate\Support\Carbon::__set_state(array(
     'endOfTime' => false,
     'startOfTime' => false,
     'constructedObjectId' => '0000000000001c430000000000000000',
     'clock' => NULL,
     'localMonthsOverflow' => NULL,
     'localYearsOverflow' => NULL,
     'localStrictModeEnabled' => NULL,
     'localHumanDiffOptions' => NULL,
     'localToStringFormat' => NULL,
     'localSerializer' => NULL,
     'localMacros' => NULL,
     'localGenericMacros' => NULL,
     'localFormatFunction' => NULL,
     'localTranslator' => NULL,
     'dumpProperties' => 
    array (
      0 => 'date',
      1 => 'timezone_type',
      2 => 'timezone',
    ),
     'dumpLocale' => NULL,
     'dumpDateProperties' => NULL,
     'date' => '2026-04-06 12:13:43.000000',
     'timezone_type' => 3,
     'timezone' => 'Africa/Dar_es_Salaam',
  )),
  'distributed_at' => 
  \Illuminate\Support\Carbon::__set_state(array(
     'endOfTime' => false,
     'startOfTime' => false,
     'constructedObjectId' => '0000000000001c4e0000000000000000',
     'clock' => NULL,
     'localMonthsOverflow' => NULL,
     'localYearsOverflow' => NULL,
     'localStrictModeEnabled' => NULL,
     'localHumanDiffOptions' => NULL,
     'localToStringFormat' => NULL,
     'localSerializer' => NULL,
     'localMacros' => NULL,
     'localGenericMacros' => NULL,
     'localFormatFunction' => NULL,
     'localTranslator' => NULL,
     'dumpProperties' => 
    array (
      0 => 'date',
      1 => 'timezone_type',
      2 => 'timezone',
    ),
     'dumpLocale' => NULL,
     'dumpDateProperties' => NULL,
     'date' => '2026-04-06 12:15:06.000000',
     'timezone_type' => 3,
     'timezone' => 'Africa/Dar_es_Salaam',
  )),
  'notes' => NULL,
  'rejection_reason' => NULL,
  'unit_cost' => '4000.00',
  'total_cost' => '4000.00',
)
                );
            }

            $p = Product::where('name', 'M/Water')->first();
            $v = ProductVariant::where('product_id', $p->id)->where('measurement', '0 ml')->first();
            $u = Staff::where('name', 'BAR KEEPER')->first();
            if ($v && $u) {
                StockRequest::updateOrCreate(
                    ['product_variant_id' => $v->id, 'batch_reference' => 'REQ-260406-001', 'requested_by' => $u->id],
                    array (
  'batch_id' => '03def9ec-3831-4e9b-a550-50ea2ef73a93',
  'batch_reference' => 'REQ-260406-001',
  'quantity' => '1.00',
  'unit' => 'packages',
  'status' => 'completed',
  'accountant_approved_at' => NULL,
  'manager_approved_at' => 
  \Illuminate\Support\Carbon::__set_state(array(
     'endOfTime' => false,
     'startOfTime' => false,
     'constructedObjectId' => '0000000000001c430000000000000000',
     'clock' => NULL,
     'localMonthsOverflow' => NULL,
     'localYearsOverflow' => NULL,
     'localStrictModeEnabled' => NULL,
     'localHumanDiffOptions' => NULL,
     'localToStringFormat' => NULL,
     'localSerializer' => NULL,
     'localMacros' => NULL,
     'localGenericMacros' => NULL,
     'localFormatFunction' => NULL,
     'localTranslator' => NULL,
     'dumpProperties' => 
    array (
      0 => 'date',
      1 => 'timezone_type',
      2 => 'timezone',
    ),
     'dumpLocale' => NULL,
     'dumpDateProperties' => NULL,
     'date' => '2026-04-06 12:35:14.000000',
     'timezone_type' => 3,
     'timezone' => 'Africa/Dar_es_Salaam',
  )),
  'distributed_at' => 
  \Illuminate\Support\Carbon::__set_state(array(
     'endOfTime' => false,
     'startOfTime' => false,
     'constructedObjectId' => '0000000000001c410000000000000000',
     'clock' => NULL,
     'localMonthsOverflow' => NULL,
     'localYearsOverflow' => NULL,
     'localStrictModeEnabled' => NULL,
     'localHumanDiffOptions' => NULL,
     'localToStringFormat' => NULL,
     'localSerializer' => NULL,
     'localMacros' => NULL,
     'localGenericMacros' => NULL,
     'localFormatFunction' => NULL,
     'localTranslator' => NULL,
     'dumpProperties' => 
    array (
      0 => 'date',
      1 => 'timezone_type',
      2 => 'timezone',
    ),
     'dumpLocale' => NULL,
     'dumpDateProperties' => NULL,
     'date' => '2026-04-06 12:36:26.000000',
     'timezone_type' => 3,
     'timezone' => 'Africa/Dar_es_Salaam',
  )),
  'notes' => NULL,
  'rejection_reason' => NULL,
  'unit_cost' => '4000.00',
  'total_cost' => '4000.00',
)
                );
            }

            $p = Product::where('name', 'L/Water')->first();
            $v = ProductVariant::where('product_id', $p->id)->where('measurement', '0 ml')->first();
            $u = Staff::where('name', 'BAR KEEPER')->first();
            if ($v && $u) {
                StockRequest::updateOrCreate(
                    ['product_variant_id' => $v->id, 'batch_reference' => 'REQ-260406-001', 'requested_by' => $u->id],
                    array (
  'batch_id' => '03def9ec-3831-4e9b-a550-50ea2ef73a93',
  'batch_reference' => 'REQ-260406-001',
  'quantity' => '1.00',
  'unit' => 'packages',
  'status' => 'completed',
  'accountant_approved_at' => NULL,
  'manager_approved_at' => 
  \Illuminate\Support\Carbon::__set_state(array(
     'endOfTime' => false,
     'startOfTime' => false,
     'constructedObjectId' => '0000000000001c430000000000000000',
     'clock' => NULL,
     'localMonthsOverflow' => NULL,
     'localYearsOverflow' => NULL,
     'localStrictModeEnabled' => NULL,
     'localHumanDiffOptions' => NULL,
     'localToStringFormat' => NULL,
     'localSerializer' => NULL,
     'localMacros' => NULL,
     'localGenericMacros' => NULL,
     'localFormatFunction' => NULL,
     'localTranslator' => NULL,
     'dumpProperties' => 
    array (
      0 => 'date',
      1 => 'timezone_type',
      2 => 'timezone',
    ),
     'dumpLocale' => NULL,
     'dumpDateProperties' => NULL,
     'date' => '2026-04-06 12:35:23.000000',
     'timezone_type' => 3,
     'timezone' => 'Africa/Dar_es_Salaam',
  )),
  'distributed_at' => 
  \Illuminate\Support\Carbon::__set_state(array(
     'endOfTime' => false,
     'startOfTime' => false,
     'constructedObjectId' => '0000000000001c4e0000000000000000',
     'clock' => NULL,
     'localMonthsOverflow' => NULL,
     'localYearsOverflow' => NULL,
     'localStrictModeEnabled' => NULL,
     'localHumanDiffOptions' => NULL,
     'localToStringFormat' => NULL,
     'localSerializer' => NULL,
     'localMacros' => NULL,
     'localGenericMacros' => NULL,
     'localFormatFunction' => NULL,
     'localTranslator' => NULL,
     'dumpProperties' => 
    array (
      0 => 'date',
      1 => 'timezone_type',
      2 => 'timezone',
    ),
     'dumpLocale' => NULL,
     'dumpDateProperties' => NULL,
     'date' => '2026-04-06 12:36:26.000000',
     'timezone_type' => 3,
     'timezone' => 'Africa/Dar_es_Salaam',
  )),
  'notes' => NULL,
  'rejection_reason' => NULL,
  'unit_cost' => '7000.00',
  'total_cost' => '7000.00',
)
                );
            }

        });
    }
}
