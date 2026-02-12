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
        Schema::table('stock_transfers', function (Blueprint $table) {
            // Add revenue tracking fields
            $table->decimal('unit_cost', 10, 2)->nullable()->after('quantity_transferred')->comment('Cost per pic from purchase');
            $table->decimal('total_cost', 10, 2)->nullable()->after('unit_cost')->comment('Total cost of transfer');
            $table->decimal('selling_price_per_pic', 10, 2)->nullable()->after('total_cost')->comment('Selling price per pic');
            $table->decimal('selling_price_per_serving', 10, 2)->nullable()->after('selling_price_per_pic')->comment('Selling price per serving (glass/tot)');
            $table->integer('servings_per_pic')->nullable()->after('selling_price_per_serving')->comment('Servings per pic for this product');
            $table->decimal('expected_revenue_pic_sale', 10, 2)->nullable()->after('servings_per_pic')->comment('Expected revenue if sold as whole pics');
            $table->decimal('expected_revenue_serving_sale', 10, 2)->nullable()->after('expected_revenue_pic_sale')->comment('Expected revenue if sold by servings');
            $table->decimal('expected_profit_pic_sale', 10, 2)->nullable()->after('expected_revenue_serving_sale')->comment('Expected profit if sold as pics');
            $table->decimal('expected_profit_serving_sale', 10, 2)->nullable()->after('expected_profit_pic_sale')->comment('Expected profit if sold by servings');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_transfers', function (Blueprint $table) {
            $table->dropColumn([
                'unit_cost',
                'total_cost',
                'selling_price_per_pic',
                'selling_price_per_serving',
                'servings_per_pic',
                'expected_revenue_pic_sale',
                'expected_revenue_serving_sale',
                'expected_profit_pic_sale',
                'expected_profit_serving_sale'
            ]);
        });
    }
};
