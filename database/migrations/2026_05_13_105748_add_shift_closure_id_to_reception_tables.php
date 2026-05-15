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
            if (!Schema::hasColumn('day_services', 'shift_closure_id')) {
                // Determine placement
                $after = Schema::hasColumn('day_services', 'cashier_id') ? 'cashier_id' : 'id';
                $table->unsignedBigInteger('shift_closure_id')->nullable()->after($after);
                $table->foreign('shift_closure_id')->references('id')->on('shift_closures')->onDelete('set null');
            }
        });

        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'shift_closure_id')) {
                // Determine placement
                $after = Schema::hasColumn('bookings', 'cashier_id') ? 'cashier_id' : 'id';
                $table->unsignedBigInteger('shift_closure_id')->nullable()->after($after);
                $table->foreign('shift_closure_id')->references('id')->on('shift_closures')->onDelete('set null');
            }
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
