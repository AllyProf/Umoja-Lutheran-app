<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AssignAllPermissionsToManager extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:assign-all-to-manager 
                            {--role=manager : The role name to assign permissions to}
                            {--force : Force assignment even if role already has permissions}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign all system permissions to the manager role (or specified role)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $roleName = $this->option('role');
        $force = $this->option('force');

        $this->info("🔐 Assigning all permissions to role: {$roleName}");

        // Find the role
        $role = Role::where('name', $roleName)->first();

        if (!$role) {
            $this->error("❌ Role '{$roleName}' not found!");
            $this->info("Available roles:");
            $roles = Role::all(['name', 'display_name']);
            foreach ($roles as $r) {
                $this->line("  - {$r->name} ({$r->display_name})");
            }
            return 1;
        }

        $this->info("✓ Found role: {$role->display_name} (ID: {$role->id})");

        // Get all permissions
        $allPermissions = Permission::all();
        
        if ($allPermissions->isEmpty()) {
            $this->error("❌ No permissions found in the system!");
            $this->warn("Please create permissions first using the super-admin panel.");
            return 1;
        }

        $this->info("✓ Found {$allPermissions->count()} permissions in the system");

        // Check current permissions
        $currentPermissions = DB::table('role_permission')
            ->where('role_id', $role->id)
            ->pluck('permission_id')
            ->toArray();
        $currentCount = count($currentPermissions);

        if ($currentCount > 0 && !$force) {
            $this->warn("⚠️  Role already has {$currentCount} permissions assigned.");
            if (!$this->confirm("Do you want to replace them with all permissions?", true)) {
                $this->info("Operation cancelled.");
                return 0;
            }
        }

        // Get all permission IDs
        $permissionIds = $allPermissions->pluck('id')->toArray();

        // Sync permissions
        $this->info("📝 Syncing permissions...");
        
        try {
            $synced = $role->permissions()->sync($permissionIds);
            
            $attached = count($synced['attached'] ?? []);
            $detached = count($synced['detached'] ?? []);
            $updated = count($synced['updated'] ?? []);

            // Verify the permissions were saved
            $savedCount = DB::table('role_permission')
                ->where('role_id', $role->id)
                ->count();

            // Clear cache
            try {
                Cache::flush();
                $this->info("✓ Cache cleared");
            } catch (\Exception $e) {
                $this->warn("⚠️  Cache flush failed: " . $e->getMessage());
            }

            // Log the action
            Log::info("Assigned all permissions to role '{$roleName}' via artisan command. Total: {$savedCount} permissions");

            // Display results
            $this->newLine();
            $this->info("✅ Successfully assigned permissions!");
            $this->table(
                ['Action', 'Count'],
                [
                    ['Attached (new)', $attached],
                    ['Detached (removed)', $detached],
                    ['Updated (existing)', $updated],
                    ['Total assigned', $savedCount],
                ]
            );

            $this->newLine();
            $this->info("📋 Permission groups assigned:");
            $groups = $allPermissions->groupBy('group');
            foreach ($groups as $groupName => $permissions) {
                $this->line("  • " . ($groupName ?: 'General') . ": {$permissions->count()} permissions");
            }

            $this->newLine();
            $this->info("🎉 Done! The '{$role->display_name}' role now has access to all system permissions.");
            $this->info("   Users with this role should see all menu items in the dashboard sidebar.");

            return 0;

        } catch (\Exception $e) {
            $this->error("❌ Error assigning permissions: " . $e->getMessage());
            Log::error("Failed to assign permissions to role '{$roleName}': " . $e->getMessage());
            return 1;
        }
    }
}

