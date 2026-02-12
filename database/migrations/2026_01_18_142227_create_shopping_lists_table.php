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
        Schema::create('shopping_lists', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('status')->default('pending'); // pending, completed, cancelled
            $table->decimal('total_estimated_cost', 10, 2)->nullable();
            $table->decimal('total_actual_cost', 10, 2)->nullable();
            $table->string('market_name')->nullable();
            $table->date('shopping_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shopping_lists');
    }
};
