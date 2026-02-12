<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;

class CheckAllStaff extends Command
{
    protected $signature = 'check:all-staff';
    protected $description = 'Check all staff directly from database';

    public function handle()
    {
        $this->info("All Staff from Database:");
        $this->line(str_repeat('-', 100));
        
        $staff = DB::table('staffs')
            ->select('id', 'name', 'email', 'role', 'is_active', 'created_at')
            ->orderBy('id')
            ->get();
        
        if ($staff->isEmpty()) {
            $this->warn('No staff members found in database!');
            return 1;
        }
        
        $this->table(
            ['ID', 'Name', 'Email', 'Role', 'Active', 'Created'],
            $staff->map(function($s) {
                return [
                    $s->id,
                    $s->name,
                    $s->email,
                    $s->role ?? 'NULL',
                    $s->is_active ? 'Yes' : 'No',
                    $s->created_at
                ];
            })->toArray()
        );
        
        $this->line(str_repeat('-', 100));
        $this->info("Total: " . $staff->count() . " staff member(s)");
        
        return 0;
    }
}










