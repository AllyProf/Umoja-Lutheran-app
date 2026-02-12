<?php

namespace App\Console\Commands;

use App\Models\Role;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixRoleDisplayNames extends Command
{
    protected $signature = 'roles:fix-display-names';
    protected $description = 'Fix missing display names for system roles';

    protected $defaultDisplayNames = [
        'super_admin' => 'Super Administrator',
        'Super Admin' => 'Super Administrator',
        'SuperAdmin' => 'Super Administrator',
        'manager' => 'Manager',
        'reception' => 'Reception Staff',
        'customer' => 'Customer',
        'guest' => 'Guest',
    ];

    public function handle()
    {
        $this->info("🔧 Fixing role display names...\n");

        $updated = 0;
        $alreadyCorrect = 0;

        foreach ($this->defaultDisplayNames as $roleName => $displayName) {
            $role = Role::where('name', $roleName)->first();
            
            if (!$role) {
                $this->warn("  ⚠️  Role '{$roleName}' not found - skipping");
                continue;
            }

            if (empty($role->display_name) || $role->display_name === null || $role->display_name === $role->name) {
                // Update if empty, null, or if display_name is same as name (needs proper display name)
                $role->display_name = $displayName;
                $role->save();
                $updated++;
                $this->info("  ✓ Updated '{$roleName}' → '{$displayName}'");
            } elseif ($role->display_name === 'Super Admin' && $displayName === 'Super Administrator') {
                // Special case: Update "Super Admin" to "Super Administrator"
                $role->display_name = $displayName;
                $role->save();
                $updated++;
                $this->info("  ✓ Updated '{$roleName}' display_name from 'Super Admin' → 'Super Administrator'");
            } else {
                $alreadyCorrect++;
                $this->line("  ⊙ '{$roleName}' already has display_name: '{$role->display_name}'");
            }
        }

        // Also fix any other roles with missing display names
        $allRoles = Role::whereNull('display_name')
            ->orWhere('display_name', '')
            ->get();

        foreach ($allRoles as $role) {
            if (!isset($this->defaultDisplayNames[$role->name])) {
                // Generate a display name from the role name
                $displayName = ucfirst(str_replace('_', ' ', $role->name));
                $role->display_name = $displayName;
                $role->save();
                $updated++;
                $this->info("  ✓ Auto-generated display name for '{$role->name}' → '{$displayName}'");
            }
        }

        $this->newLine();
        $this->info("✅ Display name fix completed!");
        $this->table(
            ['Action', 'Count'],
            [
                ['Updated', $updated],
                ['Already correct', $alreadyCorrect],
            ]
        );

        return 0;
    }
}

