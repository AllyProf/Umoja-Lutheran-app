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
        // Revert admin role back to manager in staffs table
        DB::table('staffs')->where('role', 'admin')->update(['role' => 'manager']);
        
        // Revert admin role back to manager in roles table
        if (Schema::hasTable('roles')) {
            DB::table('roles')->where('name', 'admin')->update([
                'name' => 'manager',
                'slug' => 'manager',
                'display_name' => 'Manager',
                'description' => 'Hotel management and operations'
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert manager back to admin
        DB::table('staffs')->where('role', 'manager')->update(['role' => 'admin']);
        
        if (Schema::hasTable('roles')) {
            DB::table('roles')->where('name', 'manager')->update([
                'name' => 'admin',
                'slug' => 'admin',
                'display_name' => 'Administrator',
                'description' => 'Administrative access to manage hotel operations'
            ]);
        }
    }
};
