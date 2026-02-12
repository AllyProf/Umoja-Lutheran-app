<?php

namespace App\Console\Commands;

use App\Models\Staff;
use Illuminate\Support\Facades\Hash;
use Illuminate\Console\Command;

class CreateReceptionStaff extends Command
{
    protected $signature = 'create:reception-staff {name} {email} {password}';
    protected $description = 'Create a reception staff member';

    public function handle()
    {
        $name = $this->argument('name');
        $email = $this->argument('email');
        $password = $this->argument('password');
        
        // Check if email already exists
        if (Staff::where('email', $email)->exists()) {
            $this->error("Email {$email} already exists!");
            return 1;
        }
        
        try {
            $staff = Staff::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'role' => 'reception',
                'is_active' => true,
            ]);
            
            $this->info("Reception staff created successfully!");
            $this->line("ID: {$staff->id}");
            $this->line("Name: {$staff->name}");
            $this->line("Email: {$staff->email}");
            $this->line("Role: {$staff->role}");
            
            return 0;
        } catch (\Exception $e) {
            $this->error("Failed to create reception staff: " . $e->getMessage());
            return 1;
        }
    }
}










