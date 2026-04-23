<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('supplier_weekly_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_weekly_order_id')->constrained('supplier_weekly_orders')->onDelete('cascade');
            $table->string('item_name');
            $table->decimal('quantity', 15, 2);
            $table->string('unit')->nullable();
            $table->decimal('unit_price', 15, 2);
            $table->decimal('total_price', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_weekly_order_items');
    }
};
