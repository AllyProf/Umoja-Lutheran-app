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
        Schema::create('guests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('profile_photo')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('nationality')->nullable();
            $table->string('passport_number')->nullable();
            $table->json('room_preferences')->nullable();
            $table->text('dietary_restrictions')->nullable();
            $table->text('special_occasions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('session_token')->nullable();
            $table->string('last_session_id')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            
            $table->index('email');
            $table->index('is_active');
        });
        
        // Migrate existing guest users (guest/customer) from users table to guests table
        $guestUsers = DB::table('users')
            ->whereIn('role', ['guest', 'customer'])
            ->get();
        
        foreach ($guestUsers as $user) {
            DB::table('guests')->insert([
                'name' => $user->name,
                'email' => $user->email,
                'password' => $user->password,
                'profile_photo' => $user->profile_photo ?? null,
                'room_preferences' => $user->room_preferences ?? null,
                'dietary_restrictions' => $user->dietary_restrictions ?? null,
                'special_occasions' => $user->special_occasions ?? null,
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
        // Migrate guests back to users table before dropping
        $guests = DB::table('guests')->get();
        
        foreach ($guests as $guest) {
            // Check if user already exists
            $existingUser = DB::table('users')->where('email', $guest->email)->first();
            
            if (!$existingUser) {
                DB::table('users')->insert([
                    'name' => $guest->name,
                    'email' => $guest->email,
                    'password' => $guest->password,
                    'role' => 'guest',
                    'profile_photo' => $guest->profile_photo ?? null,
                    'room_preferences' => $guest->room_preferences ?? null,
                    'dietary_restrictions' => $guest->dietary_restrictions ?? null,
                    'special_occasions' => $guest->special_occasions ?? null,
                    'is_active' => $guest->is_active ?? true,
                    'session_token' => $guest->session_token ?? null,
                    'last_session_id' => $guest->last_session_id ?? null,
                    'email_verified_at' => $guest->email_verified_at ?? null,
                    'remember_token' => $guest->remember_token ?? null,
                    'created_at' => $guest->created_at,
                    'updated_at' => $guest->updated_at,
                ]);
            }
        }
        
        Schema::dropIfExists('guests');
    }
};
