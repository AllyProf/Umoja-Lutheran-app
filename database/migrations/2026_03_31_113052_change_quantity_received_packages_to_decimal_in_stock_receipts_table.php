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
        Schema::table('stock_receipts', function (Blueprint $table) {
            $table->decimal('quantity_received_packages', 15, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_receipts', function (Blueprint $table) {
            $table->integer('quantity_received_packages')->change();
        });
    }
};
