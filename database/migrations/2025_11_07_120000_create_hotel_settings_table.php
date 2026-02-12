<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('hotel_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, text, boolean, number
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Insert default WiFi password setting
        DB::table('hotel_settings')->insert([
            'key' => 'wifi_password',
            'value' => null,
            'type' => 'string',
            'description' => 'Hotel WiFi password for guests',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insert WiFi network name setting
        DB::table('hotel_settings')->insert([
            'key' => 'wifi_network_name',
            'value' => 'PrimeLand_Hotel',
            'type' => 'string',
            'description' => 'Hotel WiFi network name (SSID)',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_settings');
    }
};

