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
        Schema::table('bookings', function (Blueprint $table) {
            // Check if accountant columns exist first (to avoid double adding if they are partially there)
            if (!Schema::hasColumn('bookings', 'accountant_verified_at')) {
                $table->timestamp('accountant_verified_at')->nullable()->after('amount_paid');
            }
            if (!Schema::hasColumn('bookings', 'accountant_id')) {
                $table->unsignedBigInteger('accountant_id')->nullable()->after('accountant_verified_at');
                $table->foreign('accountant_id')->references('id')->on('staffs')->onDelete('set null');
            }

            // Add cashier columns
            if (!Schema::hasColumn('bookings', 'cashier_collected_at')) {
                $table->timestamp('cashier_collected_at')->nullable()->after('accountant_id');
            }
            if (!Schema::hasColumn('bookings', 'cashier_id')) {
                $table->unsignedBigInteger('cashier_id')->nullable()->after('cashier_collected_at');
                $table->foreign('cashier_id')->references('id')->on('staffs')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['accountant_id']);
            $table->dropForeign(['cashier_id']);
            $table->dropColumn(['accountant_verified_at', 'accountant_id', 'cashier_collected_at', 'cashier_id']);
        });
    }
};
