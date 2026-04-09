<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Recipe;
use App\Models\RecipeIngredient;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductionRecipeSeeder extends Seeder
{
    public function run()
    {
        DB::transaction(function () {
            $recipe = Recipe::updateOrCreate(['name' => 'Wali  nyama'], array (
  'name' => 'Wali  nyama',
  'description' => NULL,
  'category' => 'lunch',
  'prep_time' => 15,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '7000.00',
  'is_available' => true,
  'created_by' => 1,
));

            $recipe = Recipe::updateOrCreate(['name' => 'wali kuku'], array (
  'name' => 'wali kuku',
  'description' => NULL,
  'category' => 'dinner',
  'prep_time' => 10,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '9000.00',
  'is_available' => true,
  'created_by' => 1,
));

            $recipe = Recipe::updateOrCreate(['name' => 'wali samaki'], array (
  'name' => 'wali samaki',
  'description' => NULL,
  'category' => 'lunch',
  'prep_time' => 15,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '15000.00',
  'is_available' => true,
  'created_by' => 1,
));

            $recipe = Recipe::updateOrCreate(['name' => 'wali kuku'], array (
  'name' => 'wali kuku',
  'description' => NULL,
  'category' => 'lunch',
  'prep_time' => 12,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '9000.00',
  'is_available' => true,
  'created_by' => 1,
));

            $recipe = Recipe::updateOrCreate(['name' => 'ugali kuku'], array (
  'name' => 'ugali kuku',
  'description' => NULL,
  'category' => 'lunch',
  'prep_time' => 30,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '9000.00',
  'is_available' => true,
  'created_by' => 6,
));

            $recipe = Recipe::updateOrCreate(['name' => 'wali kuku kienyeji'], array (
  'name' => 'wali kuku kienyeji',
  'description' => NULL,
  'category' => 'lunch',
  'prep_time' => 10,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '12000.00',
  'is_available' => true,
  'created_by' => 1,
));

            $recipe = Recipe::updateOrCreate(['name' => 'ugali nyama'], array (
  'name' => 'ugali nyama',
  'description' => NULL,
  'category' => 'lunch',
  'prep_time' => 30,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '7000.00',
  'is_available' => true,
  'created_by' => 6,
));

            $recipe = Recipe::updateOrCreate(['name' => 'wali mboga za majani'], array (
  'name' => 'wali mboga za majani',
  'description' => NULL,
  'category' => 'lunch',
  'prep_time' => 5,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '4000.00',
  'is_available' => true,
  'created_by' => 1,
));

            $recipe = Recipe::updateOrCreate(['name' => 'Wali  nyama'], array (
  'name' => 'Wali  nyama',
  'description' => NULL,
  'category' => 'dinner',
  'prep_time' => NULL,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '7000.00',
  'is_available' => true,
  'created_by' => 1,
));

            $recipe = Recipe::updateOrCreate(['name' => 'wali kuku kienyeji'], array (
  'name' => 'wali kuku kienyeji',
  'description' => NULL,
  'category' => 'dinner',
  'prep_time' => 15,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '12000.00',
  'is_available' => true,
  'created_by' => 1,
));

            $recipe = Recipe::updateOrCreate(['name' => 'ugali samaki'], array (
  'name' => 'ugali samaki',
  'description' => NULL,
  'category' => 'lunch',
  'prep_time' => 30,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '15000.00',
  'is_available' => true,
  'created_by' => 6,
));

            $recipe = Recipe::updateOrCreate(['name' => 'wali samaki'], array (
  'name' => 'wali samaki',
  'description' => NULL,
  'category' => 'dinner',
  'prep_time' => 15,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '15000.00',
  'is_available' => true,
  'created_by' => 1,
));

            $recipe = Recipe::updateOrCreate(['name' => 'wali mboga za majani'], array (
  'name' => 'wali mboga za majani',
  'description' => NULL,
  'category' => 'dinner',
  'prep_time' => 5,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '4000.00',
  'is_available' => true,
  'created_by' => 1,
));

            $recipe = Recipe::updateOrCreate(['name' => 'ugali vegetable'], array (
  'name' => 'ugali vegetable',
  'description' => NULL,
  'category' => 'lunch',
  'prep_time' => 30,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '4000.00',
  'is_available' => true,
  'created_by' => 6,
));

            $recipe = Recipe::updateOrCreate(['name' => 'ugali vegetable'], array (
  'name' => 'ugali vegetable',
  'description' => NULL,
  'category' => 'lunch',
  'prep_time' => 30,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '4000.00',
  'is_available' => true,
  'created_by' => 6,
));

            $recipe = Recipe::updateOrCreate(['name' => 'pilau nyama'], array (
  'name' => 'pilau nyama',
  'description' => NULL,
  'category' => 'dinner',
  'prep_time' => 15,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '7000.00',
  'is_available' => true,
  'created_by' => 1,
));

            $recipe = Recipe::updateOrCreate(['name' => 'pilau kuku'], array (
  'name' => 'pilau kuku',
  'description' => NULL,
  'category' => 'dinner',
  'prep_time' => 12,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '9000.00',
  'is_available' => true,
  'created_by' => 1,
));

            $recipe = Recipe::updateOrCreate(['name' => 'pilau samaki'], array (
  'name' => 'pilau samaki',
  'description' => NULL,
  'category' => 'dinner',
  'prep_time' => 15,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '15000.00',
  'is_available' => true,
  'created_by' => 1,
));

            $recipe = Recipe::updateOrCreate(['name' => 'pilau kuku kienyeji'], array (
  'name' => 'pilau kuku kienyeji',
  'description' => NULL,
  'category' => 'dinner',
  'prep_time' => 12,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '12000.00',
  'is_available' => true,
  'created_by' => 1,
));

            $recipe = Recipe::updateOrCreate(['name' => 'pilau kuku kienyeji'], array (
  'name' => 'pilau kuku kienyeji',
  'description' => NULL,
  'category' => 'dinner',
  'prep_time' => 12,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '12000.00',
  'is_available' => true,
  'created_by' => 1,
));

            $recipe = Recipe::updateOrCreate(['name' => 'pilau vegetable'], array (
  'name' => 'pilau vegetable',
  'description' => NULL,
  'category' => 'dinner',
  'prep_time' => 10,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '4000.00',
  'is_available' => true,
  'created_by' => 1,
));

            $recipe = Recipe::updateOrCreate(['name' => 'ugali kuku kienyeji'], array (
  'name' => 'ugali kuku kienyeji',
  'description' => NULL,
  'category' => 'lunch',
  'prep_time' => 30,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '12000.00',
  'is_available' => true,
  'created_by' => 6,
));

            $recipe = Recipe::updateOrCreate(['name' => 'Tea with milk'], array (
  'name' => 'Tea with milk',
  'description' => 'milk,ginger,spices',
  'category' => 'breakfast',
  'prep_time' => 10,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '1500.00',
  'is_available' => true,
  'created_by' => 6,
));

            $recipe = Recipe::updateOrCreate(['name' => 'black tea'], array (
  'name' => 'black tea',
  'description' => 'spices',
  'category' => 'breakfast',
  'prep_time' => 10,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '1000.00',
  'is_available' => true,
  'created_by' => 6,
));

            $recipe = Recipe::updateOrCreate(['name' => 'black coffee'], array (
  'name' => 'black coffee',
  'description' => 'coffe,',
  'category' => 'chai',
  'prep_time' => 15,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '2000.00',
  'is_available' => true,
  'created_by' => 6,
));

            $recipe = Recipe::updateOrCreate(['name' => 'hot lemon'], array (
  'name' => 'hot lemon',
  'description' => NULL,
  'category' => 'chai',
  'prep_time' => 20,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '4000.00',
  'is_available' => true,
  'created_by' => 6,
));

            $recipe = Recipe::updateOrCreate(['name' => 'Coffee with Milk'], array (
  'name' => 'Coffee with Milk',
  'description' => 'coffee,milk',
  'category' => 'breakfast',
  'prep_time' => 15,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '3000.00',
  'is_available' => true,
  'created_by' => 6,
));

            $recipe = Recipe::updateOrCreate(['name' => 'pilau nyama'], array (
  'name' => 'pilau nyama',
  'description' => NULL,
  'category' => 'lunch',
  'prep_time' => NULL,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '7000.00',
  'is_available' => true,
  'created_by' => 1,
));

            $recipe = Recipe::updateOrCreate(['name' => 'pilau samaki'], array (
  'name' => 'pilau samaki',
  'description' => NULL,
  'category' => 'lunch',
  'prep_time' => 12,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '15000.00',
  'is_available' => true,
  'created_by' => 1,
));

            $recipe = Recipe::updateOrCreate(['name' => 'pilau kuku'], array (
  'name' => 'pilau kuku',
  'description' => NULL,
  'category' => 'lunch',
  'prep_time' => NULL,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '9000.00',
  'is_available' => true,
  'created_by' => 1,
));

            $recipe = Recipe::updateOrCreate(['name' => 'pilau kuku'], array (
  'name' => 'pilau kuku',
  'description' => NULL,
  'category' => 'lunch',
  'prep_time' => NULL,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '9000.00',
  'is_available' => true,
  'created_by' => 1,
));

            $recipe = Recipe::updateOrCreate(['name' => 'pilau kuku kienyeji'], array (
  'name' => 'pilau kuku kienyeji',
  'description' => NULL,
  'category' => 'lunch',
  'prep_time' => 3,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '12000.00',
  'is_available' => true,
  'created_by' => 1,
));

            $recipe = Recipe::updateOrCreate(['name' => 'pilau vegetable'], array (
  'name' => 'pilau vegetable',
  'description' => NULL,
  'category' => 'lunch',
  'prep_time' => NULL,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '4000.00',
  'is_available' => true,
  'created_by' => 1,
));

            $recipe = Recipe::updateOrCreate(['name' => 'Beef bite'], array (
  'name' => 'Beef bite',
  'description' => NULL,
  'category' => 'bites',
  'prep_time' => 5,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '3000.00',
  'is_available' => true,
  'created_by' => 1,
));

            $recipe = Recipe::updateOrCreate(['name' => 'fish bites'], array (
  'name' => 'fish bites',
  'description' => NULL,
  'category' => 'bites',
  'prep_time' => 5,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '12000.00',
  'is_available' => true,
  'created_by' => 1,
));

            $recipe = Recipe::updateOrCreate(['name' => 'chicken bites'], array (
  'name' => 'chicken bites',
  'description' => NULL,
  'category' => 'bites',
  'prep_time' => 5,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '7000.00',
  'is_available' => true,
  'created_by' => 1,
));

            $recipe = Recipe::updateOrCreate(['name' => 'chapati'], array (
  'name' => 'chapati',
  'description' => NULL,
  'category' => 'bites',
  'prep_time' => 2,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '1000.00',
  'is_available' => true,
  'created_by' => 1,
));

            $recipe = Recipe::updateOrCreate(['name' => 'chapati'], array (
  'name' => 'chapati',
  'description' => NULL,
  'category' => 'breakfast',
  'prep_time' => 2,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '1000.00',
  'is_available' => true,
  'created_by' => 1,
));

            $recipe = Recipe::updateOrCreate(['name' => 'chapati'], array (
  'name' => 'chapati',
  'description' => NULL,
  'category' => 'breakfast',
  'prep_time' => 2,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '1000.00',
  'is_available' => true,
  'created_by' => 1,
));

            $recipe = Recipe::updateOrCreate(['name' => 'Samosa'], array (
  'name' => 'Samosa',
  'description' => NULL,
  'category' => 'bites',
  'prep_time' => 3,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '1000.00',
  'is_available' => true,
  'created_by' => 1,
));

            $recipe = Recipe::updateOrCreate(['name' => 'kababu'], array (
  'name' => 'kababu',
  'description' => NULL,
  'category' => 'bites',
  'prep_time' => 3,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '1000.00',
  'is_available' => true,
  'created_by' => 1,
));

            $recipe = Recipe::updateOrCreate(['name' => 'donati'], array (
  'name' => 'donati',
  'description' => NULL,
  'category' => 'bites',
  'prep_time' => 3,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '1000.00',
  'is_available' => true,
  'created_by' => 1,
));

            $recipe = Recipe::updateOrCreate(['name' => 'andazi'], array (
  'name' => 'andazi',
  'description' => NULL,
  'category' => 'bites',
  'prep_time' => 3,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '1000.00',
  'is_available' => true,
  'created_by' => 1,
));

            $recipe = Recipe::updateOrCreate(['name' => 'SHANKELI'], array (
  'name' => 'SHANKELI',
  'description' => NULL,
  'category' => 'bites',
  'prep_time' => 3,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '1000.00',
  'is_available' => true,
  'created_by' => 1,
));

            $recipe = Recipe::updateOrCreate(['name' => 'SAUSAGE'], array (
  'name' => 'SAUSAGE',
  'description' => NULL,
  'category' => 'bites',
  'prep_time' => 3,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '1000.00',
  'is_available' => true,
  'created_by' => 1,
));

            $recipe = Recipe::updateOrCreate(['name' => 'MAYAI'], array (
  'name' => 'MAYAI',
  'description' => NULL,
  'category' => 'bites',
  'prep_time' => 3,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '1000.00',
  'is_available' => true,
  'created_by' => 1,
));

            $recipe = Recipe::updateOrCreate(['name' => 'KOKOTO'], array (
  'name' => 'KOKOTO',
  'description' => NULL,
  'category' => 'bites',
  'prep_time' => 3,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '1000.00',
  'is_available' => true,
  'created_by' => 1,
));

            $recipe = Recipe::updateOrCreate(['name' => 'MKATE'], array (
  'name' => 'MKATE',
  'description' => NULL,
  'category' => 'bites',
  'prep_time' => 3,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '1000.00',
  'is_available' => true,
  'created_by' => 1,
));

            $recipe = Recipe::updateOrCreate(['name' => 'SOUP KIENYEJI'], array (
  'name' => 'SOUP KIENYEJI',
  'description' => NULL,
  'category' => 'food',
  'prep_time' => 10,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '10000.00',
  'is_available' => true,
  'created_by' => 1,
));

            $recipe = Recipe::updateOrCreate(['name' => 'MTORI'], array (
  'name' => 'MTORI',
  'description' => NULL,
  'category' => 'traditional',
  'prep_time' => 4,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '3000.00',
  'is_available' => true,
  'created_by' => 1,
));

            $recipe = Recipe::updateOrCreate(['name' => 'NGARARIMO'], array (
  'name' => 'NGARARIMO',
  'description' => NULL,
  'category' => 'traditional',
  'prep_time' => 10,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '4000.00',
  'is_available' => true,
  'created_by' => 1,
));

            $recipe = Recipe::updateOrCreate(['name' => 'KIBURU'], array (
  'name' => 'KIBURU',
  'description' => NULL,
  'category' => 'traditional',
  'prep_time' => 3,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '4000.00',
  'is_available' => true,
  'created_by' => 1,
));

            $recipe = Recipe::updateOrCreate(['name' => 'NGANDE MAHARAGE'], array (
  'name' => 'NGANDE MAHARAGE',
  'description' => NULL,
  'category' => 'traditional',
  'prep_time' => 4,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '4000.00',
  'is_available' => true,
  'created_by' => 1,
));

            $recipe = Recipe::updateOrCreate(['name' => 'NGANDE CHOROKO'], array (
  'name' => 'NGANDE CHOROKO',
  'description' => NULL,
  'category' => 'traditional',
  'prep_time' => 10,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '4000.00',
  'is_available' => true,
  'created_by' => 1,
));

            $recipe = Recipe::updateOrCreate(['name' => 'UFUTA'], array (
  'name' => 'UFUTA',
  'description' => NULL,
  'category' => 'bites',
  'prep_time' => 2,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '1500.00',
  'is_available' => true,
  'created_by' => 1,
));

            $recipe = Recipe::updateOrCreate(['name' => 'Chipsi nyama'], array (
  'name' => 'Chipsi nyama',
  'description' => NULL,
  'category' => 'food',
  'prep_time' => NULL,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '7000.00',
  'is_available' => true,
  'created_by' => NULL,
));

            $recipe = Recipe::updateOrCreate(['name' => 'Chipsi kuku'], array (
  'name' => 'Chipsi kuku',
  'description' => NULL,
  'category' => 'food',
  'prep_time' => NULL,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '9000.00',
  'is_available' => true,
  'created_by' => NULL,
));

            $recipe = Recipe::updateOrCreate(['name' => 'Chipsi samaki'], array (
  'name' => 'Chipsi samaki',
  'description' => NULL,
  'category' => 'food',
  'prep_time' => NULL,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '15000.00',
  'is_available' => true,
  'created_by' => NULL,
));

            $recipe = Recipe::updateOrCreate(['name' => 'Chipsi kuku kienyeji'], array (
  'name' => 'Chipsi kuku kienyeji',
  'description' => NULL,
  'category' => 'food',
  'prep_time' => NULL,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '12000.00',
  'is_available' => true,
  'created_by' => NULL,
));

            $recipe = Recipe::updateOrCreate(['name' => 'Chipsi vegetable'], array (
  'name' => 'Chipsi vegetable',
  'description' => NULL,
  'category' => 'food',
  'prep_time' => NULL,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '4000.00',
  'is_available' => true,
  'created_by' => NULL,
));

            $recipe = Recipe::updateOrCreate(['name' => 'Ndizi nyama'], array (
  'name' => 'Ndizi nyama',
  'description' => NULL,
  'category' => 'food',
  'prep_time' => NULL,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '7000.00',
  'is_available' => true,
  'created_by' => NULL,
));

            $recipe = Recipe::updateOrCreate(['name' => 'Ndizi kuku'], array (
  'name' => 'Ndizi kuku',
  'description' => NULL,
  'category' => 'food',
  'prep_time' => NULL,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '9000.00',
  'is_available' => true,
  'created_by' => NULL,
));

            $recipe = Recipe::updateOrCreate(['name' => 'Ndizi samaki'], array (
  'name' => 'Ndizi samaki',
  'description' => NULL,
  'category' => 'food',
  'prep_time' => NULL,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '15000.00',
  'is_available' => true,
  'created_by' => NULL,
));

            $recipe = Recipe::updateOrCreate(['name' => 'Ndizi kuku kienyeji'], array (
  'name' => 'Ndizi kuku kienyeji',
  'description' => NULL,
  'category' => 'food',
  'prep_time' => NULL,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '12000.00',
  'is_available' => true,
  'created_by' => NULL,
));

            $recipe = Recipe::updateOrCreate(['name' => 'Ndizi vegetable'], array (
  'name' => 'Ndizi vegetable',
  'description' => NULL,
  'category' => 'food',
  'prep_time' => NULL,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '4000.00',
  'is_available' => true,
  'created_by' => NULL,
));

            $recipe = Recipe::updateOrCreate(['name' => 'Tambi nyama'], array (
  'name' => 'Tambi nyama',
  'description' => NULL,
  'category' => 'food',
  'prep_time' => NULL,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '7000.00',
  'is_available' => true,
  'created_by' => NULL,
));

            $recipe = Recipe::updateOrCreate(['name' => 'Tambi kuku'], array (
  'name' => 'Tambi kuku',
  'description' => NULL,
  'category' => 'food',
  'prep_time' => NULL,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '9000.00',
  'is_available' => true,
  'created_by' => NULL,
));

            $recipe = Recipe::updateOrCreate(['name' => 'Tambi samaki'], array (
  'name' => 'Tambi samaki',
  'description' => NULL,
  'category' => 'food',
  'prep_time' => NULL,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '15000.00',
  'is_available' => true,
  'created_by' => NULL,
));

            $recipe = Recipe::updateOrCreate(['name' => 'Tambi kuku kienyeji'], array (
  'name' => 'Tambi kuku kienyeji',
  'description' => NULL,
  'category' => 'food',
  'prep_time' => NULL,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '12000.00',
  'is_available' => true,
  'created_by' => NULL,
));

            $recipe = Recipe::updateOrCreate(['name' => 'Tambi vegetable'], array (
  'name' => 'Tambi vegetable',
  'description' => NULL,
  'category' => 'food',
  'prep_time' => NULL,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '4000.00',
  'is_available' => true,
  'created_by' => NULL,
));

            $recipe = Recipe::updateOrCreate(['name' => 'Chapati nyama'], array (
  'name' => 'Chapati nyama',
  'description' => NULL,
  'category' => 'food',
  'prep_time' => NULL,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '7000.00',
  'is_available' => true,
  'created_by' => NULL,
));

            $recipe = Recipe::updateOrCreate(['name' => 'Chapati kuku'], array (
  'name' => 'Chapati kuku',
  'description' => NULL,
  'category' => 'food',
  'prep_time' => NULL,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '9000.00',
  'is_available' => true,
  'created_by' => NULL,
));

            $recipe = Recipe::updateOrCreate(['name' => 'Chapati samaki'], array (
  'name' => 'Chapati samaki',
  'description' => NULL,
  'category' => 'food',
  'prep_time' => NULL,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '15000.00',
  'is_available' => true,
  'created_by' => NULL,
));

            $recipe = Recipe::updateOrCreate(['name' => 'Chapati kuku kienyeji'], array (
  'name' => 'Chapati kuku kienyeji',
  'description' => NULL,
  'category' => 'food',
  'prep_time' => NULL,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '12000.00',
  'is_available' => true,
  'created_by' => NULL,
));

            $recipe = Recipe::updateOrCreate(['name' => 'Chapati vegetable'], array (
  'name' => 'Chapati vegetable',
  'description' => NULL,
  'category' => 'food',
  'prep_time' => NULL,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '4000.00',
  'is_available' => true,
  'created_by' => NULL,
));

            $recipe = Recipe::updateOrCreate(['name' => 'Karanga'], array (
  'name' => 'Karanga',
  'description' => NULL,
  'category' => 'bites',
  'prep_time' => NULL,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '1500.00',
  'is_available' => true,
  'created_by' => NULL,
));

            $recipe = Recipe::updateOrCreate(['name' => 'Fruit Salad'], array (
  'name' => 'Fruit Salad',
  'description' => NULL,
  'category' => 'juices',
  'prep_time' => NULL,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '3000.00',
  'is_available' => true,
  'created_by' => NULL,
));

            $recipe = Recipe::updateOrCreate(['name' => 'Juice Mchanganyiko'], array (
  'name' => 'Juice Mchanganyiko',
  'description' => NULL,
  'category' => 'juices',
  'prep_time' => NULL,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '2000.00',
  'is_available' => true,
  'created_by' => NULL,
));

            $recipe = Recipe::updateOrCreate(['name' => 'Tende+Korosho+Maziwa'], array (
  'name' => 'Tende+Korosho+Maziwa',
  'description' => NULL,
  'category' => 'juices',
  'prep_time' => NULL,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '4000.00',
  'is_available' => true,
  'created_by' => NULL,
));

            $recipe = Recipe::updateOrCreate(['name' => 'Fruit Salad'], array (
  'name' => 'Fruit Salad',
  'description' => NULL,
  'category' => 'juices',
  'prep_time' => NULL,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '3000.00',
  'is_available' => true,
  'created_by' => NULL,
));

            $recipe = Recipe::updateOrCreate(['name' => 'Chai Maziwa'], array (
  'name' => 'Chai Maziwa',
  'description' => NULL,
  'category' => 'chai',
  'prep_time' => NULL,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '1500.00',
  'is_available' => true,
  'created_by' => NULL,
));

            $recipe = Recipe::updateOrCreate(['name' => 'Chai Rangi'], array (
  'name' => 'Chai Rangi',
  'description' => NULL,
  'category' => 'chai',
  'prep_time' => NULL,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '1000.00',
  'is_available' => true,
  'created_by' => NULL,
));

            $recipe = Recipe::updateOrCreate(['name' => 'Black Coffee with Milk'], array (
  'name' => 'Black Coffee with Milk',
  'description' => NULL,
  'category' => 'chai',
  'prep_time' => NULL,
  'cook_time' => NULL,
  'servings' => NULL,
  'selling_price' => '3000.00',
  'is_available' => true,
  'created_by' => NULL,
));

        });
    }
}
