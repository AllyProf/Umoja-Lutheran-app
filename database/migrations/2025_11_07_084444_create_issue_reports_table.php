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
        Schema::create('issue_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('booking_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('room_id')->nullable()->constrained()->onDelete('set null');
            $table->string('issue_type'); // e.g., 'room_issue', 'service_issue', 'technical_issue', 'other'
            $table->string('priority')->default('medium'); // 'low', 'medium', 'high', 'urgent'
            $table->string('subject');
            $table->text('description');
            $table->enum('status', ['pending', 'in_progress', 'resolved', 'closed'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            
            $table->index(['status', 'priority']);
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issue_reports');
    }
};
