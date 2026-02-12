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
        Schema::create('service_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->text('guest_request')->nullable(); // Additional notes from guest
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price_tsh', 10, 2);
            $table->decimal('total_price_tsh', 10, 2);
            $table->enum('status', ['pending', 'approved', 'completed', 'cancelled'])->default('pending');
            $table->text('reception_notes')->nullable(); // Notes from reception staff
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('requested_at')->useCurrent();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_requests');
    }
};
