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
        Schema::create('inventory_stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_item_id')->constrained('housekeeping_inventory_items')->onDelete('cascade');
            $table->enum('movement_type', ['supply', 'consumption', 'adjustment', 'transfer']); // supply to room, consumption, adjustment, transfer
            $table->decimal('quantity', 10, 2);
            $table->foreignId('room_id')->nullable()->constrained('rooms')->onDelete('set null'); // For room supply
            $table->foreignId('performed_by')->constrained('staffs')->onDelete('cascade');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_stock_movements');
    }
};
