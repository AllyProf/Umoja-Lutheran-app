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
        Schema::table('services', function (Blueprint $table) {
            $table->enum('age_group', ['adult', 'child', 'both'])->default('both')->after('price_tsh');
            $table->decimal('child_price_tsh', 10, 2)->nullable()->after('age_group');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['age_group', 'child_price_tsh']);
        });
    }
};
