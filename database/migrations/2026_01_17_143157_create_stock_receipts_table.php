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
        Schema::create('stock_receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('product_variant_id')->constrained('product_variants')->onDelete('cascade');
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->integer('quantity_received_packages'); // Number of packages (crates, cartons, etc.)
            $table->decimal('buying_price_per_bottle', 10, 2);
            $table->decimal('selling_price_per_bottle', 10, 2);
            $table->enum('discount_type', ['percentage', 'fixed', 'none'])->default('none');
            $table->decimal('discount_amount', 10, 2)->nullable();
            $table->date('received_date');
            $table->date('expiry_date')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('received_by')->constrained('staffs')->onDelete('cascade'); // Manager who received
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_receipts');
    }
};
