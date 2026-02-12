<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            // Add servings per pic tracking
            $table->integer('servings_per_pic')->default(1)->after('items_per_package')->comment('Number of servings (glasses/tots) per pic');
            $table->string('selling_unit')->default('pic')->after('servings_per_pic')->comment('Unit for selling: pic, glass, tot, shot, cocktail');
            $table->boolean('can_sell_as_pic')->default(true)->after('selling_unit')->comment('Can sell as whole pic');
            $table->boolean('can_sell_as_serving')->default(false)->after('can_sell_as_pic')->comment('Can sell by glass/tot/shot');
            $table->decimal('selling_price_per_pic', 10, 2)->nullable()->after('can_sell_as_serving')->comment('Selling price for whole pic');
            $table->decimal('selling_price_per_serving', 10, 2)->nullable()->after('selling_price_per_pic')->comment('Selling price per glass/tot/shot');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn([
                'servings_per_pic',
                'selling_unit',
                'can_sell_as_pic',
                'can_sell_as_serving',
                'selling_price_per_pic',
                'selling_price_per_serving'
            ]);
        });
    }
};
