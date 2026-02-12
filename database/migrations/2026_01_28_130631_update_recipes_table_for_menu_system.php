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
        Schema::table('recipes', function (Blueprint $table) {
            // Drop columns that are no longer needed for simple menu system
            if (Schema::hasColumn('recipes', 'instructions')) {
                $table->dropColumn('instructions');
            }
            if (Schema::hasColumn('recipes', 'cook_time')) {
                $table->dropColumn('cook_time');
            }
            if (Schema::hasColumn('recipes', 'servings')) {
                $table->dropColumn('servings');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recipes', function (Blueprint $table) {
            $table->text('instructions')->nullable();
            $table->integer('cook_time')->nullable();
            $table->integer('servings')->nullable();
        });
    }
};
