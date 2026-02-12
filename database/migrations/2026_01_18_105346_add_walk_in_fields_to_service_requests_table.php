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
            // Make booking_id nullable
            $table->unsignedBigInteger('booking_id')->nullable()->change();
            
            // Add walk-in fields
            $table->boolean('is_walk_in')->default(false)->after('booking_id');
            $table->string('walk_in_name')->nullable()->after('is_walk_in');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropColumn(['is_walk_in', 'walk_in_name']);
            $table->unsignedBigInteger('booking_id')->nullable(false)->change();
        });
    }
};
