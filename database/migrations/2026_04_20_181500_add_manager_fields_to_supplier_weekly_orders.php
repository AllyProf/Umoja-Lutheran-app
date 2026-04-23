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
            $table->unsignedBigInteger('manager_id')->nullable()->after('accountant_id');
            $table->text('manager_notes')->nullable()->after('notes');

            $table->foreign('manager_id')->references('id')->on('staffs')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supplier_weekly_orders', function (Blueprint $table) {
            if (Schema::hasColumn('supplier_weekly_orders', 'manager_id')) {
                $table->dropForeign(['manager_id']);
                $table->dropColumn(['manager_id', 'manager_notes']);
            }
        });
    }
};
