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
        Schema::table('supplier_weekly_orders', function (Blueprint $table) {
            $table->string('order_type')->default('weekly_order')->after('supplier_id'); // weekly_order, lpo
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supplier_weekly_orders', function (Blueprint $table) {
            $table->dropColumn('order_type');
        });
    }
};
