<?php

namespace App\Console\Commands;

use App\Models\Staff;
use Illuminate\Console\Command;

class CheckStaffRoles extends Command
{
    protected $signature = 'check:staff-roles';
    protected $description = 'Check all staff members and their roles';

    public function handle()
    {
        $this->info("All Staff Members:");
        $this->line(str_repeat('-', 80));
        
        $staff = Staff::orderBy('id')->get(['id', 'name', 'email', 'role', 'is_active']);
        
        if ($staff->isEmpty()) {
            $this->warn('No staff members found!');
            return 1;
        }
        
        foreach ($staff as $s) {
            $status = $s->is_active ? 'Active' : 'Inactive';
            $this->line(sprintf(
                "ID: %-3d | Name: %-30s | Email: %-35s | Role: [%-20s] | Status: %s",
                $s->id,
                $s->name,
                $s->email,
                $s->role ?? 'NULL',
                $status
            ));
        }
        
        $this->line(str_repeat('-', 80));
        $this->info("\nRole Summary:");
        
        $roleCounts = Staff::selectRaw('role, COUNT(*) as count')
            ->groupBy('role')
            ->orderBy('role')
            ->get();
            
        foreach ($roleCounts as $rc) {
            $this->line("  [{$rc->role}]: {$rc->count} staff member(s)");
        }
        
        // Check for reception specifically
        $receptionStaff = Staff::whereRaw('LOWER(role) LIKE ?', ['%reception%'])->get();
        $this->info("\nReception Staff (any variation):");
        if ($receptionStaff->isEmpty()) {
            $this->warn('  No reception staff found!');
        } else {
            foreach ($receptionStaff as $rs) {
                $this->line("  ID: {$rs->id} - {$rs->name} ({$rs->email}) - Role: [{$rs->role}]");
            }
        }
        
        return 0;
    }
}










