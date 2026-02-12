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
        Schema::create('day_services', function (Blueprint $table) {
            $table->id();
            $table->string('service_reference')->unique(); // Unique reference number
            $table->enum('service_type', ['swimming', 'restaurant', 'bar', 'other'])->default('other');
            $table->string('guest_name');
            $table->string('guest_phone')->nullable();
            $table->string('guest_email')->nullable();
            $table->integer('number_of_people')->default(1);
            $table->date('service_date');
            $table->time('service_time');
            $table->text('items_ordered')->nullable(); // For restaurant/bar items
            $table->decimal('amount', 10, 2); // Total amount
            $table->enum('payment_status', ['pending', 'paid'])->default('pending');
            $table->enum('payment_method', ['cash', 'card', 'mobile', 'bank', 'online', 'other'])->nullable();
            $table->string('payment_provider')->nullable(); // M-PESA, CRDB, etc.
            $table->string('payment_reference')->nullable(); // Transaction reference
            $table->decimal('amount_paid', 10, 2)->nullable();
            $table->decimal('exchange_rate', 10, 4)->nullable(); // For USD transactions
            $table->enum('guest_type', ['tanzanian', 'international'])->default('tanzanian');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('registered_by')->nullable(); // Staff ID
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            
            // Foreign key constraint - staffs table (note: table name is 'staffs' not 'staff')
            $table->foreign('registered_by')->references('id')->on('staffs')->onDelete('set null');
            $table->index('service_date');
            $table->index('payment_status');
            $table->index('service_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('day_services');
    }
};
