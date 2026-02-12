<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Staff;
use App\Models\Guest;

class MigrateUsersToStaffsAndGuests extends Command
{
    protected $signature = 'users:migrate-to-staffs-guests';
    protected $description = 'Migrate users from users table to staffs and guests tables';

    public function handle()
    {
        $this->info('Starting user migration...');
        
        // Get all users from users table
        $users = DB::table('users')->get();
        $this->info("Found {$users->count()} users in users table");
        
        $staffCount = 0;
        $guestCount = 0;
        $skippedCount = 0;
        
        foreach ($users as $user) {
            // Check if user already exists in staffs or guests
            $existsInStaffs = Staff::where('email', $user->email)->exists();
            $existsInGuests = Guest::where('email', $user->email)->exists();
            
            if ($existsInStaffs || $existsInGuests) {
                $this->warn("User {$user->email} already exists in staffs/guests, skipping...");
                $skippedCount++;
                continue;
            }
            
            // Determine if user is staff or guest based on role
            if (in_array($user->role, ['super_admin', 'admin', 'manager', 'reception'])) {
                // Normalize role: admin -> manager
                $role = $user->role === 'admin' ? 'manager' : $user->role;
                
                Staff::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'password' => $user->password,
                    'role' => $role,
                    'profile_photo' => $user->profile_photo ?? null,
                    'is_active' => $user->is_active ?? true,
                    'session_token' => $user->session_token ?? null,
                    'last_session_id' => $user->last_session_id ?? null,
                    'email_verified_at' => $user->email_verified_at ?? null,
                    'remember_token' => $user->remember_token ?? null,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                ]);
                $staffCount++;
                $this->info("Migrated staff: {$user->email} (role: {$role})");
            } elseif (in_array($user->role, ['customer', 'guest'])) {
                Guest::create([
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
                $guestCount++;
                $this->info("Migrated guest: {$user->email}");
            } else {
                $this->warn("Unknown role for user {$user->email}: {$user->role}, skipping...");
                $skippedCount++;
            }
        }
        
        $this->info("\nMigration complete!");
        $this->info("Staff migrated: {$staffCount}");
        $this->info("Guests migrated: {$guestCount}");
        $this->info("Skipped: {$skippedCount}");
        
        return 0;
    }
}
