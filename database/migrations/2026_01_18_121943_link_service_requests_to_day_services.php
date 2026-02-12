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
        Schema::table('service_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('day_service_id')->nullable()->after('booking_id');
            $table->foreign('day_service_id')->references('id')->on('day_services')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropForeign(['day_service_id']);
            $table->dropColumn('day_service_id');
        });
    }
};
