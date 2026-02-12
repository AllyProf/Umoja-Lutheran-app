<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Staff;

class ResetSuperAdminPassword extends Command
{
    protected $signature = 'superadmin:reset-password {password=SuperAdmin@2024}';
    protected $description = 'Reset super admin password';

    public function handle()
    {
        $password = $this->argument('password');
        $staff = Staff::where('email', 'superadmin@primelandhotel.com')->first();
        
        if ($staff) {
            $staff->password = $password;
            $staff->save();
            $this->info('Super Admin password reset successfully!');
            $this->line('Email: superadmin@primelandhotel.com');
            $this->line('Password: ' . $password);
        } else {
            $this->error('Super Admin account not found!');
        }
    }
}
