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
            // Personal information fields
            $table->string('first_name')->nullable()->after('guest_name');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('country')->nullable()->after('guest_email');
            $table->string('country_code', 10)->nullable()->after('guest_phone');
            
            // Booking for someone else
            $table->enum('booking_for', ['me', 'someone'])->default('me')->after('country_code');
            $table->string('guest_first_name')->nullable()->after('booking_for');
            $table->string('guest_last_name')->nullable()->after('guest_first_name');
            $table->string('main_guest_name')->nullable()->after('guest_last_name');
            
            // Arrival time
            $table->string('arrival_time')->nullable()->after('special_requests');
            
            // Payment fields for PayPal
            $table->string('payment_status')->default('pending')->after('status'); // pending, paid, failed, refunded
            $table->string('payment_method')->nullable()->after('payment_status'); // paypal, cash, etc
            $table->string('payment_transaction_id')->nullable()->after('payment_method');
            $table->decimal('amount_paid', 10, 2)->nullable()->after('payment_transaction_id');
            $table->timestamp('paid_at')->nullable()->after('amount_paid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'first_name',
                'last_name',
                'country',
                'country_code',
                'booking_for',
                'guest_first_name',
                'guest_last_name',
                'main_guest_name',
                'arrival_time',
                'payment_status',
                'payment_method',
                'payment_transaction_id',
                'amount_paid',
                'paid_at'
            ]);
        });
    }
};
