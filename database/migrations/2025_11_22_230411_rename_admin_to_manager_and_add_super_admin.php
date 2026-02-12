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
        // Rename admin role to manager in users table
        DB::table('users')->where('role', 'admin')->update(['role' => 'manager']);
        
        // Create activity_logs table for tracking user activities (if not exists)
        if (!Schema::hasTable('activity_logs')) {
            Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_type')->nullable(); // manager, super_admin, reception, customer
            $table->string('action'); // created, updated, deleted, viewed, etc.
            $table->string('model_type')->nullable(); // User, Booking, Room, etc.
            $table->unsignedBigInteger('model_id')->nullable();
            $table->text('description')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
            $table->index(['model_type', 'model_id']);
            $table->index('action');
            });
        }
        
        // Update roles table structure
        if (Schema::hasTable('roles')) {
            Schema::table('roles', function (Blueprint $table) {
                if (!Schema::hasColumn('roles', 'name')) {
                    $table->string('name')->unique()->after('id');
                }
                if (!Schema::hasColumn('roles', 'slug')) {
                    $table->string('slug')->unique()->nullable()->after('name');
                }
                if (!Schema::hasColumn('roles', 'display_name')) {
                    $table->string('display_name')->nullable()->after('slug');
                }
                if (!Schema::hasColumn('roles', 'description')) {
                    $table->text('description')->nullable()->after('display_name');
                }
                if (!Schema::hasColumn('roles', 'is_system')) {
                    $table->boolean('is_system')->default(false)->after('description');
                }
            });
        }
        
        // Update permissions table structure
        if (Schema::hasTable('permissions')) {
            Schema::table('permissions', function (Blueprint $table) {
                if (!Schema::hasColumn('permissions', 'name')) {
                    $table->string('name')->unique()->after('id');
                }
                if (!Schema::hasColumn('permissions', 'display_name')) {
                    $table->string('display_name')->nullable()->after('name');
                }
                if (!Schema::hasColumn('permissions', 'description')) {
                    $table->text('description')->nullable()->after('display_name');
                }
                if (!Schema::hasColumn('permissions', 'group')) {
                    $table->string('group')->nullable()->after('description');
                }
            });
        }
        
        // Create role_permission pivot table
        if (!Schema::hasTable('role_permission')) {
            Schema::create('role_permission', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('role_id');
                $table->unsignedBigInteger('permission_id');
                $table->timestamps();
                
                $table->unique(['role_id', 'permission_id']);
                $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
                $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
            });
        }
        
        // Create system_logs table (if not exists)
        if (!Schema::hasTable('system_logs')) {
            Schema::create('system_logs', function (Blueprint $table) {
                $table->id();
                $table->string('level'); // info, warning, error, critical
                $table->string('channel')->nullable(); // database, auth, booking, etc.
                $table->text('message');
                $table->json('context')->nullable();
                $table->string('user_id')->nullable();
                $table->string('ip_address')->nullable();
                $table->timestamps();
                
                $table->index(['level', 'created_at']);
                $table->index('channel');
            });
        }
        
        // Seed default roles (only if they don't exist)
        $roles = [
            ['name' => 'super_admin', 'slug' => 'super-admin', 'display_name' => 'Super Administrator', 'description' => 'Full system access with all permissions', 'is_system' => true],
            ['name' => 'manager', 'slug' => 'manager', 'display_name' => 'Manager', 'description' => 'Hotel management and operations', 'is_system' => true],
            ['name' => 'reception', 'slug' => 'reception', 'display_name' => 'Reception Staff', 'description' => 'Front desk operations', 'is_system' => true],
            ['name' => 'customer', 'slug' => 'customer', 'display_name' => 'Customer', 'description' => 'Guest access', 'is_system' => true],
        ];
        
        foreach ($roles as $roleData) {
            $existsByName = DB::table('roles')->where('name', $roleData['name'])->exists();
            $existsBySlug = DB::table('roles')->where('slug', $roleData['slug'])->exists();
            
            if (!$existsByName && !$existsBySlug) {
                DB::table('roles')->insert($roleData);
            } elseif ($existsByName) {
                // Update existing role with missing fields (skip slug if it would conflict)
                $updateData = [
                    'display_name' => $roleData['display_name'],
                    'description' => $roleData['description'],
                    'is_system' => $roleData['is_system'],
                ];
                // Only update slug if it doesn't conflict
                if (!$existsBySlug) {
                    $updateData['slug'] = $roleData['slug'];
                }
                DB::table('roles')->where('name', $roleData['name'])->update($updateData);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rename manager back to admin
        DB::table('users')->where('role', 'manager')->update(['role' => 'admin']);
        
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('role_permission');
        Schema::dropIfExists('system_logs');
    }
};
