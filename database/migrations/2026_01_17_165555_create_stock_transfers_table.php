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
        Schema::create('stock_transfers', function (Blueprint $table) {
            $table->id();
            $table->string('transfer_reference')->unique(); // e.g., TRF-ABC12345
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('product_variant_id')->constrained('product_variants')->onDelete('cascade');
            $table->integer('quantity_transferred'); // Quantity in packages or bottles
            $table->enum('quantity_unit', ['packages', 'bottles'])->default('packages');
            $table->foreignId('transferred_by')->constrained('staffs')->onDelete('cascade'); // Manager/staff who transferred
            $table->foreignId('received_by')->nullable()->constrained('staffs')->onDelete('set null'); // Bar keeper/staff who received
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            $table->date('transfer_date');
            $table->datetime('received_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_transfers');
    }
};
