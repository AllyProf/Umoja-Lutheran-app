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
        // Create password_resets table for tracking password reset requests
        if (!Schema::hasTable('password_resets')) {
            Schema::create('password_resets', function (Blueprint $table) {
                $table->id();
                $table->string('email');
                $table->string('user_type'); // 'staff' or 'guest'
                $table->unsignedBigInteger('user_id');
                $table->timestamps();
                
                $table->index(['email', 'user_type']);
                $table->index('created_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('password_resets');
    }
};
