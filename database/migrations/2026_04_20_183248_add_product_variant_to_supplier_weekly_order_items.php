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
        Schema::table('supplier_weekly_order_items', function (Blueprint $table) {
            $table->unsignedBigInteger('product_variant_id')->nullable()->after('supplier_weekly_order_id');
            $table->timestamp('received_at')->nullable()->after('total_price');

            $table->foreign('product_variant_id', 'swoi_pv_foreign')->references('id')->on('product_variants')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supplier_weekly_order_items', function (Blueprint $table) {
            $table->dropForeign('swoi_pv_foreign');
            $table->dropColumn(['product_variant_id', 'received_at']);
        });
    }
};
