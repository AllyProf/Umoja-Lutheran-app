<?php

namespace App\Console\Commands;

use App\Models\Staff;
use Illuminate\Console\Command;

class CheckSuperAdminRole extends Command
{
    protected $signature = 'check:super-admin-role {email?}';
    protected $description = 'Check super admin user role in database';

    public function handle()
    {
        $email = $this->argument('email');
        
        if ($email) {
            $staff = Staff::where('email', $email)->first();
        } else {
            $staff = Staff::where('role', 'like', '%super%')->first();
        }
        
        if (!$staff) {
            $this->error('No super admin staff found!');
            return 1;
        }
        
        $this->info("Found Staff:");
        $this->line("  ID: {$staff->id}");
        $this->line("  Name: {$staff->name}");
        $this->line("  Email: {$staff->email}");
        $this->line("  Role (raw): '{$staff->role}'");
        $this->line("  Role (length): " . strlen($staff->role ?? ''));
        $this->line("  Role (bytes): " . bin2hex($staff->role ?? ''));
        
        $normalized = strtolower(str_replace([' ', '_'], '', trim($staff->role ?? '')));
        $this->line("  Role (normalized): '{$normalized}'");
        
        $isSuperAdmin = $staff->isSuperAdmin();
        $this->line("  isSuperAdmin(): " . ($isSuperAdmin ? 'YES ✓' : 'NO ✗'));
        
        return 0;
    }
}











