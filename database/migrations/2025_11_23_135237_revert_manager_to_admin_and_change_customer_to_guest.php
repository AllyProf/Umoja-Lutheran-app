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
        // Revert manager role back to admin in users table
        DB::table('users')->where('role', 'manager')->update(['role' => 'admin']);
        
        // Change customer role to guest in users table
        DB::table('users')->where('role', 'customer')->update(['role' => 'guest']);
        
        // Update roles table
        if (Schema::hasTable('roles')) {
            // Check if admin role already exists
            $adminExists = DB::table('roles')->where('name', 'admin')->exists();
            $managerExists = DB::table('roles')->where('name', 'manager')->exists();
            
            if ($managerExists) {
                if ($adminExists) {
                    // If admin already exists, delete manager role and update admin
                    DB::table('roles')->where('name', 'manager')->delete();
                    DB::table('roles')->where('name', 'admin')->update([
                        'display_name' => 'Administrator',
                        'description' => 'Administrative access to manage hotel operations'
                    ]);
                } else {
                    // Update manager role to admin
                    DB::table('roles')->where('name', 'manager')->update([
                        'name' => 'admin',
                        'slug' => 'admin',
                        'display_name' => 'Administrator',
                        'description' => 'Administrative access to manage hotel operations'
                    ]);
                }
            } elseif (!$adminExists) {
                // Create admin role if it doesn't exist
                DB::table('roles')->insert([
                    'name' => 'admin',
                    'slug' => 'admin',
                    'display_name' => 'Administrator',
                    'description' => 'Administrative access to manage hotel operations',
                    'is_system' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            // Check if guest role already exists
            $guestExists = DB::table('roles')->where('name', 'guest')->exists();
            $customerExists = DB::table('roles')->where('name', 'customer')->exists();
            
            if ($customerExists) {
                if ($guestExists) {
                    // If guest already exists, delete customer role and update guest
                    DB::table('roles')->where('name', 'customer')->delete();
                    DB::table('roles')->where('name', 'guest')->update([
                        'display_name' => 'Guest',
                        'description' => 'Guest access'
                    ]);
                } else {
                    // Update customer role to guest
                    DB::table('roles')->where('name', 'customer')->update([
                        'name' => 'guest',
                        'slug' => 'guest',
                        'display_name' => 'Guest',
                        'description' => 'Guest access'
                    ]);
                }
            } elseif (!$guestExists) {
                // Create guest role if it doesn't exist
                DB::table('roles')->insert([
                    'name' => 'guest',
                    'slug' => 'guest',
                    'display_name' => 'Guest',
                    'description' => 'Guest access',
                    'is_system' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert admin back to manager
        DB::table('users')->where('role', 'admin')->update(['role' => 'manager']);
        
        // Revert guest back to customer
        DB::table('users')->where('role', 'guest')->update(['role' => 'customer']);
        
        // Update roles table back
        if (Schema::hasTable('roles')) {
            DB::table('roles')->where('name', 'admin')->update([
                'name' => 'manager',
                'slug' => 'manager',
                'display_name' => 'Manager',
                'description' => 'Hotel management and operations'
            ]);
            
            DB::table('roles')->where('name', 'guest')->update([
                'name' => 'customer',
                'slug' => 'customer',
                'display_name' => 'Customer',
                'description' => 'Guest access'
            ]);
        }
    }
};
