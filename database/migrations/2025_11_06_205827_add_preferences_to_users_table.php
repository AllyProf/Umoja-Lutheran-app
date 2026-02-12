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
        Schema::table('users', function (Blueprint $table) {
            $table->json('room_preferences')->nullable()->after('profile_photo');
            $table->text('dietary_restrictions')->nullable()->after('room_preferences');
            $table->string('special_occasions')->nullable()->after('dietary_restrictions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['room_preferences', 'dietary_restrictions', 'special_occasions']);
        });
    }
};
