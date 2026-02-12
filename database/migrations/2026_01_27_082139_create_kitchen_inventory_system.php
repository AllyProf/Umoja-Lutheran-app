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
        Schema::create('kitchen_inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category')->nullable();
            $table->string('unit')->default('pcs');
            $table->decimal('current_stock', 12, 2)->default(0);
            $table->decimal('minimum_stock', 12, 2)->default(0);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('kitchen_stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_item_id')->constrained('kitchen_inventory_items')->onDelete('cascade');
            $table->enum('movement_type', ['supply', 'sale', 'internal_use', 'adjustment']);
            $table->decimal('quantity', 12, 2);
            $table->decimal('unit_price', 12, 2)->default(0); // Price at which it was sold or purchased
            $table->decimal('total_amount', 12, 2)->default(0); // quantity * unit_price
            $table->foreignId('performed_by')->nullable()->constrained('staffs')->onDelete('set null');
            $table->date('movement_date'); // The day for which this movement counts in the report
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kitchen_stock_movements');
        Schema::dropIfExists('kitchen_inventory_items');
    }
};
