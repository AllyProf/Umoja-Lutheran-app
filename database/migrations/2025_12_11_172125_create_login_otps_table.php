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
        Schema::create('login_otps', function (Blueprint $table) {
            $table->id();
            $table->string('email')->index();
            $table->string('otp', 6);
            $table->string('user_type')->nullable(); // 'staff' or 'guest'
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamp('expires_at');
            $table->boolean('used')->default(false);
            $table->timestamps();
            
            $table->index(['email', 'otp', 'used']);
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('login_otps');
    }
};
