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
        Schema::create('purchase_deadlines', function (Blueprint $table) {
            $table->id();
            $table->string('day_of_week'); // monday, tuesday, etc. or 'friday' as default
            $table->time('deadline_time')->default('17:00'); // Default 5 PM
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Insert default Friday deadline
        DB::table('purchase_deadlines')->insert([
            'day_of_week' => 'friday',
            'deadline_time' => '17:00',
            'notes' => 'Default purchase day - every Friday',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_deadlines');
    }
};
