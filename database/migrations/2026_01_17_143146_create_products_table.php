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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->string('brand_or_type')->nullable(); // Brand for drinks, type for food
            $table->enum('category', [
                'alcoholic_beverage',
                'non_alcoholic_beverage',
                'water',
                'juices',
                'energy_drinks',
                'food'
            ]);
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->enum('type', ['drink', 'food'])->default('drink');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
