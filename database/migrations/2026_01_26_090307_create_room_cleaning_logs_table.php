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
        Schema::create('room_cleaning_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('rooms')->onDelete('cascade');
            $table->foreignId('cleaned_by')->nullable()->constrained('staffs')->onDelete('set null');
            $table->enum('status', ['needs_cleaning', 'cleaning_in_progress', 'cleaned', 'inspected'])->default('needs_cleaning');
            $table->timestamp('cleaned_at')->nullable();
            $table->timestamp('inspected_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_cleaning_logs');
    }
};
