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
        Schema::create('shopping_list_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shopping_list_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('set null');
            $table->string('product_name');
            $table->string('category')->nullable();
            $table->decimal('quantity', 10, 2)->default(0);
            $table->string('unit')->default('pcs');
            $table->decimal('estimated_price', 10, 2)->nullable();
            $table->boolean('is_purchased')->default(false);
            $table->decimal('purchased_quantity', 10, 2)->nullable();
            $table->decimal('purchased_cost', 10, 2)->nullable();
            $table->string('storage_location')->nullable(); // Where it was stored after purchase
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shopping_list_items');
    }
};
