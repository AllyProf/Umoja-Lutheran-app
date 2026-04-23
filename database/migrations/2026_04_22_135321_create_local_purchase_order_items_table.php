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
        Schema::create('local_purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lpo_id');
            $table->string('item_name');
            $table->unsignedBigInteger('product_variant_id')->nullable();
            $table->decimal('quantity', 15, 2);
            $table->decimal('qty_received', 15, 2)->default(0);
            $table->decimal('unit_price', 15, 2);
            $table->decimal('total_price', 15, 2);
            $table->string('unit')->nullable(); // e.g. kg, pcs
            $table->timestamps();

            $table->foreign('lpo_id')->references('id')->on('local_purchase_orders')->onDelete('cascade');
            $table->foreign('product_variant_id')->references('id')->on('product_variants')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('local_purchase_order_items');
    }
};
