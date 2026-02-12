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
        Schema::create('service_catalog', function (Blueprint $table) {
            $table->id();
            $table->string('service_name'); // e.g., "Swimming Pool Access", "Restaurant", "Bar"
            $table->string('service_key')->unique(); // e.g., "swimming", "restaurant", "bar" (for code reference)
            $table->text('description')->nullable();
            $table->enum('pricing_type', ['per_person', 'per_hour', 'fixed', 'custom'])->default('fixed');
            $table->decimal('price_tanzanian', 10, 2)->default(0); // Price in TZS for Tanzanian guests
            $table->decimal('price_international', 10, 2)->nullable(); // Price in USD for International guests (if different)
            $table->boolean('payment_required_upfront')->default(true); // true for swimming, false for restaurant/bar
            $table->boolean('requires_items')->default(false); // true for restaurant/bar (items need to be entered)
            $table->boolean('is_active')->default(true);
            $table->integer('display_order')->default(0); // For sorting in dropdowns
            $table->text('notes')->nullable(); // Internal notes
            $table->timestamps();
            
            $table->index('service_key');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_catalog');
    }
};
