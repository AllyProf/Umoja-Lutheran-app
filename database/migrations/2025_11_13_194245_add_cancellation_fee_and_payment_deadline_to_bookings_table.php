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
            $table->decimal('cancellation_fee', 10, 2)->nullable()->after('cancelled_at');
            $table->decimal('cancellation_fee_percentage', 5, 2)->nullable()->after('cancellation_fee');
            $table->timestamp('payment_deadline')->nullable()->after('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['cancellation_fee', 'cancellation_fee_percentage', 'payment_deadline']);
        });
    }
};
