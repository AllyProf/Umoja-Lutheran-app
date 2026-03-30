<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Change type from ENUM to VARCHAR
        // Using DB statement for better compatibility with existing ENUMs
        DB::statement("ALTER TABLE products MODIFY COLUMN type VARCHAR(30) DEFAULT 'drink'");

        // 2. Fix existing categories
        $housekeepingCategories = ['cleaning_supplies', 'linens', 'amenities', 'housekeeping', 'cleaning_materials'];
        DB::table('products')
            ->whereIn('category', $housekeepingCategories)
            ->update(['type' => 'housekeeping']);

        $foodCategories = ['food', 'meat_poultry', 'seafood', 'pantry', 'dairy', 'baking', 'vegetables', 'spices', 'sauces', 'bakery', 'pantry_baking', 'spices_herbs', 'oils_fats', 'snacks', 'kitchen'];
        DB::table('products')
            ->whereIn('category', $foodCategories)
            ->update(['type' => 'food']);

        $beverageCategories = ['spirits', 'wines', 'alcoholic_beverage', 'non_alcoholic_beverage', 'energy_drinks', 'juices', 'water', 'hot_beverages', 'cocktails', 'soda', 'soft_drinks'];
        DB::table('products')
            ->whereIn('category', $beverageCategories)
            ->update(['type' => 'drink']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Change back to ENUM if possible (might fail if new values exist)
        try {
            DB::statement("ALTER TABLE products MODIFY COLUMN type ENUM('drink', 'food') DEFAULT 'drink'");
        } catch (\Exception $e) {
            // Log or ignore if cannot revert
        }
    }
};
