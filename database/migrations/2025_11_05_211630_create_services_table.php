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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('category')->default('general'); // transport, food, laundry, spa, room_service, etc.
            $table->decimal('price_tsh', 10, 2); // Price in Tanzanian Shillings
            $table->string('unit')->default('per_item'); // per_item, per_hour, per_day, etc.
            $table->boolean('is_active')->default(true);
            $table->boolean('requires_approval')->default(true); // If reception needs to approve
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
