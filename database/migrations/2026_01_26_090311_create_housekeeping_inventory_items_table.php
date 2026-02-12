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
        Schema::create('housekeeping_inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Soap, Towel, Mosquito repellent, Drinking water, etc.
            $table->string('category'); // cleaning_supplies, linens, beverages, etc.
            $table->string('unit')->default('pcs'); // pcs, liters, kg, etc.
            $table->decimal('current_stock', 10, 2)->default(0);
            $table->decimal('minimum_stock', 10, 2)->default(0); // Alert threshold
            $table->decimal('reorder_quantity', 10, 2)->default(0);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('housekeeping_inventory_items');
    }
};
