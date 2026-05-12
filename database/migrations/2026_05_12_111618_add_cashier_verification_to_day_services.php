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
        Schema::table('day_services', function (Blueprint $table) {
            $table->timestamp('cashier_collected_at')->nullable()->after('accountant_verified_at');
            $table->unsignedBigInteger('cashier_id')->nullable()->after('accountant_id');
            
            $table->foreign('cashier_id')->references('id')->on('staffs')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('day_services', function (Blueprint $table) {
            $table->dropForeign(['cashier_id']);
            $table->dropColumn(['cashier_collected_at', 'cashier_id']);
        });
    }
};
