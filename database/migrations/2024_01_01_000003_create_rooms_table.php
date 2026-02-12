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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('room_number')->unique();
            $table->enum('room_type', ['Single', 'Double', 'Twins']);
            $table->integer('capacity');
            $table->string('bed_type');
            $table->string('floor_location')->nullable();
            $table->string('sku_code')->nullable();
            $table->text('description')->nullable();
            
            // Pricing
            $table->decimal('price_per_night', 10, 2);
            $table->decimal('extra_guest_fee', 10, 2)->nullable();
            $table->decimal('peak_season_price', 10, 2)->nullable();
            $table->decimal('off_season_price', 10, 2)->nullable();
            $table->decimal('discount_percentage', 5, 2)->nullable();
            $table->string('promo_code')->nullable();
            
            // Amenities (stored as JSON)
            $table->json('amenities')->nullable();
            $table->string('bathroom_type')->nullable();
            $table->time('checkin_time')->nullable();
            $table->time('checkout_time')->nullable();
            $table->boolean('pet_friendly')->default(false);
            $table->boolean('smoking_allowed')->default(false);
            $table->text('special_notes')->nullable();
            
            // Images (stored as JSON array of file paths)
            $table->json('images')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};

