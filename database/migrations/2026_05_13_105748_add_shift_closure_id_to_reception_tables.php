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
            $table->unsignedBigInteger('shift_closure_id')->nullable()->after('cashier_id');
            $table->foreign('shift_closure_id')->references('id')->on('shift_closures')->onDelete('set null');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->unsignedBigInteger('shift_closure_id')->nullable()->after('cashier_id');
            $table->foreign('shift_closure_id')->references('id')->on('shift_closures')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('day_services', function (Blueprint $table) {
            $table->dropForeign(['shift_closure_id']);
            $table->dropColumn('shift_closure_id');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['shift_closure_id']);
            $table->dropColumn('shift_closure_id');
        });
    }
};
