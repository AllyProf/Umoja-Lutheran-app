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
            $table->decimal('total_service_charges_tsh', 10, 2)->default(0)->after('total_price');
            $table->decimal('total_bill_tsh', 10, 2)->nullable()->after('total_service_charges_tsh'); // Total room + services in TZS
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['total_service_charges_tsh', 'total_bill_tsh']);
        });
    }
};
