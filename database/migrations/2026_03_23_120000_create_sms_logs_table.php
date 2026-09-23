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
        Schema::create('sms_logs', function (Blueprint $table) {
            $table->id();
            $table->string('phone', 20);
            $table->text('message');
            $table->boolean('success')->default(false);
            $table->unsignedSmallInteger('http_code')->nullable();
            $table->text('response')->nullable();
            $table->text('error')->nullable();
            $table->string('sender')->nullable();
            $table->string('context', 100)->nullable();
            $table->timestamps();

            $table->index('created_at');
            $table->index(['success', 'created_at']);
            $table->index('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_logs');
    }
};
