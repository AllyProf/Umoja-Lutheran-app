<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Update Items table for partial quantities
        Schema::table('supplier_weekly_order_items', function (Blueprint $table) {
            $table->decimal('qty_received', 10, 2)->default(0)->after('quantity');
        });

        // Update Orders table for payments
        Schema::table('supplier_weekly_orders', function (Blueprint $table) {
            $table->decimal('amount_paid', 15, 2)->default(0)->after('total_amount');
            $table->string('payment_status')->default('unpaid')->after('amount_paid'); // unpaid, partial, settled
        });
    }

    public function down(): void
    {
        Schema::table('supplier_weekly_order_items', function (Blueprint $table) {
            $table->dropColumn('qty_received');
        });

        Schema::table('supplier_weekly_orders', function (Blueprint $table) {
            $table->dropColumn(['amount_paid', 'payment_status']);
        });
    }
};
