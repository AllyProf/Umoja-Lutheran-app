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
        Schema::create('staffs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role'); // super_admin, manager, reception
            $table->string('profile_photo')->nullable();
            $table->string('phone')->nullable();
            $table->string('department')->nullable();
            $table->string('position')->nullable();
            $table->date('hire_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('session_token')->nullable();
            $table->string('last_session_id')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            
            $table->index('role');
            $table->index('is_active');
            $table->index('email');
        });
        
        // Migrate existing staff users (super_admin, manager, reception) from users table to staffs table
        // Check both old and new role names for compatibility
        $staffUsers = DB::table('users')
            ->whereIn('role', ['super_admin', 'admin', 'manager', 'reception'])
            ->get();
        
        foreach ($staffUsers as $user) {
            DB::table('staffs')->insert([
                'name' => $user->name,
                'email' => $user->email,
                'password' => $user->password,
                'role' => $user->role,
                'profile_photo' => $user->profile_photo ?? null,
                'is_active' => $user->is_active ?? true,
                'session_token' => $user->session_token ?? null,
                'last_session_id' => $user->last_session_id ?? null,
                'email_verified_at' => $user->email_verified_at ?? null,
                'remember_token' => $user->remember_token ?? null,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Migrate staffs back to users table before dropping
        $staffs = DB::table('staffs')->get();
        
        foreach ($staffs as $staff) {
            // Check if user already exists
            $existingUser = DB::table('users')->where('email', $staff->email)->first();
            
            if (!$existingUser) {
                DB::table('users')->insert([
                    'name' => $staff->name,
                    'email' => $staff->email,
                    'password' => $staff->password,
                    'role' => $staff->role,
                    'profile_photo' => $staff->profile_photo ?? null,
                    'is_active' => $staff->is_active ?? true,
                    'session_token' => $staff->session_token ?? null,
                    'last_session_id' => $staff->last_session_id ?? null,
                    'email_verified_at' => $staff->email_verified_at ?? null,
                    'remember_token' => $staff->remember_token ?? null,
                    'created_at' => $staff->created_at,
                    'updated_at' => $staff->updated_at,
                ]);
            }
        }
        
        Schema::dropIfExists('staffs');
    }
};
