<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shift_closures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staffs')->onDelete('cascade');
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->decimal('total_cash_tzs', 15, 2)->default(0);
            $table->decimal('total_mpesa_tzs', 15, 2)->default(0);
            $table->decimal('total_other_tzs', 15, 2)->default(0);
            $table->decimal('amount_submitted_tzs', 15, 2)->default(0);
            $table->decimal('difference_tzs', 15, 2)->default(0);
            $table->enum('status', ['pending_reception', 'acknowledged', 'rejected'])->default('pending_reception');
            $table->foreignId('receiver_id')->nullable()->constrained('staffs')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shift_closures');
    }
};
