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
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
            $table->string('guest_name');
            $table->string('guest_email');
            $table->integer('rating')->comment('Overall rating 1-5');
            $table->text('comment')->nullable();
            $table->json('categories')->nullable()->comment('Category ratings: room_quality, service, cleanliness, value');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedbacks');
    }
};


