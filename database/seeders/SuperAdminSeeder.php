<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Staff;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if super admin already exists in Staff table (since authentication checks Staff first)
        $superAdmin = Staff::where('email', 'superadmin@primelandhotel.com')->first();
        
        if (!$superAdmin) {
            Staff::create([
                'name' => 'Super Administrator',
                'email' => 'superadmin@primelandhotel.com',
                'password' => 'SuperAdmin@2024', // Let cast handle hashing
                'role' => 'super_admin',
                'is_active' => true,
            ]);
            
            $this->command->info('Super Admin user created successfully in Staff table!');
            $this->command->warn('Default credentials:');
            $this->command->line('Email: superadmin@primelandhotel.com');
            $this->command->line('Password: SuperAdmin@2024');
            $this->command->warn('Please change the password after first login!');
        } else {
            $this->command->info('Super Admin user already exists.');
            $this->command->line('Email: superadmin@primelandhotel.com');
            $this->command->line('If password is incorrect, you can reset it manually.');
        }
    }
}
