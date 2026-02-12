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
            $table->string('guest_id')->unique()->nullable()->after('booking_reference');
            $table->boolean('airport_pickup_required')->default(false)->after('checked_out_at');
            $table->string('flight_number')->nullable()->after('airport_pickup_required');
            $table->string('airline')->nullable()->after('flight_number');
            $table->datetime('arrival_time_pickup')->nullable()->after('airline');
            $table->integer('pickup_passengers')->nullable()->after('arrival_time_pickup');
            $table->text('luggage_info')->nullable()->after('pickup_passengers');
            $table->string('pickup_contact_number')->nullable()->after('luggage_info');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'guest_id',
                'airport_pickup_required',
                'flight_number',
                'airline',
                'arrival_time_pickup',
                'pickup_passengers',
                'luggage_info',
                'pickup_contact_number'
            ]);
        });
    }
};
