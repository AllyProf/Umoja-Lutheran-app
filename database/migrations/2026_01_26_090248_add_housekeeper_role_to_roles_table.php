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
        // Add housekeeper role to roles table
        $exists = DB::table('roles')->where('name', 'housekeeper')->exists();
        
        if (!$exists) {
            DB::table('roles')->insert([
                'name' => 'housekeeper',
                'slug' => 'housekeeper',
                'display_name' => 'Housekeeper',
                'description' => 'Room cleaning, inventory management, and maintenance reporting',
                'is_system' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('roles')->where('name', 'housekeeper')->delete();
    }
};
