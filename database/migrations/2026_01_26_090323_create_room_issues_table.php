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
        Schema::create('room_issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('rooms')->onDelete('cascade');
            $table->foreignId('reported_by')->constrained('staffs')->onDelete('cascade');
            $table->string('issue_type'); // Bulb Problem, AC Issue, Plumbing, etc.
            $table->text('description');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', ['reported', 'in_progress', 'resolved', 'cancelled'])->default('reported');
            $table->foreignId('assigned_to')->nullable()->constrained('staffs')->onDelete('set null');
            $table->timestamp('resolved_at')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_issues');
    }
};
