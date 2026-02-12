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
            $table->text('cancellation_reason')->nullable()->after('paid_at');
            $table->timestamp('expires_at')->nullable()->after('cancellation_reason');
            $table->timestamp('cancelled_at')->nullable()->after('expires_at');
            $table->text('admin_notes')->nullable()->after('cancelled_at');
            $table->enum('check_in_status', ['pending', 'checked_in', 'checked_out'])->default('pending')->after('admin_notes');
            $table->timestamp('checked_in_at')->nullable()->after('check_in_status');
            $table->timestamp('checked_out_at')->nullable()->after('checked_in_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'cancellation_reason',
                'expires_at',
                'cancelled_at',
                'admin_notes',
                'check_in_status',
                'checked_in_at',
                'checked_out_at'
            ]);
        });
    }
};
