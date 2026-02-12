<?php

namespace App\Console\Commands;

use App\Models\Permission;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixPermissionNames extends Command
{
    protected $signature = 'permissions:fix-names';
    protected $description = 'Fix permission names to use lowercase with underscores format';

    /**
     * Map of expected permission names that the sidebar uses
     */
    protected $expectedPermissions = [
        'view_users' => 'View Users',
        'create_users' => 'Create Users',
        'edit_users' => 'Edit Users',
        'delete_users' => 'Delete Users',
        'view_roles' => 'View Roles',
        'create_roles' => 'Create Roles',
        'edit_roles' => 'Edit Roles',
        'delete_roles' => 'Delete Roles',
        'manage_permissions' => 'Manage Permissions',
        'view_bookings' => 'View Bookings',
        'create_bookings' => 'Create Bookings',
        'edit_bookings' => 'Edit Bookings',
        'delete_bookings' => 'Delete Bookings',
        'manage_bookings' => 'Manage Bookings',
        'view_booking_calendar' => 'View Booking Calendar',
        'create_manual_bookings' => 'Create Manual Bookings',
        'manage_extensions' => 'Manage Extensions',
        'manage_checkin' => 'Manage Check In',
        'manage_checkout' => 'Manage Check Out',
        'view_active_reservations' => 'View Active Reservations',
        'view_guests' => 'View Guests',
        'manage_rooms' => 'Manage Rooms',
        'view_rooms' => 'View Rooms',
        'create_rooms' => 'Create Rooms',
        'edit_rooms' => 'Edit Rooms',
        'delete_rooms' => 'Delete Rooms',
        'view_room_status' => 'View Room Status',
        'manage_room_cleaning' => 'Manage Room Cleaning',
        'view_payments' => 'View Payments',
        'view_payment_reports' => 'View Payment Reports',
        'view_reports' => 'View Reports',
        'view_daily_reports' => 'View Daily Reports',
        'manage_services' => 'Manage Services',
        'manage_service_requests' => 'Manage Service Requests',
        'manage_issues' => 'Manage Issues',
        'manage_blog' => 'Manage Blog',
        'view_feedback' => 'View Feedback',
        'manage_exchange_rates' => 'Manage Exchange Rates',
        'manage_settings' => 'Manage Settings',
        'manage_wifi_settings' => 'Manage WiFi Settings',
        'manage_hotel_settings' => 'Manage Hotel Settings',
        'manage_room_settings' => 'Manage Room Settings',
        'manage_pricing' => 'Manage Pricing',
    ];

    public function handle()
    {
        $this->info("🔧 Fixing permission names to match sidebar expectations...\n");

        $updated = 0;
        $notFound = [];
        $alreadyCorrect = 0;

        foreach ($this->expectedPermissions as $correctName => $displayName) {
            // Try to find permission by display name first
            $permission = Permission::where('display_name', $displayName)
                ->orWhere('name', $displayName)
                ->orWhere('name', $correctName)
                ->first();

            if ($permission) {
                if ($permission->name === $correctName) {
                    $alreadyCorrect++;
                    $this->line("  ✓ '{$correctName}' already correct");
                } else {
                    $oldName = $permission->name;
                    $permission->name = $correctName;
                    
                    // Update display_name if it's empty or same as old name
                    if (empty($permission->display_name) || $permission->display_name === $oldName) {
                        $permission->display_name = $displayName;
                    }
                    
                    $permission->save();
                    $updated++;
                    $this->info("  ✓ Updated '{$oldName}' → '{$correctName}'");
                }
            } else {
                $notFound[] = $correctName;
                $this->warn("  ⚠️  Permission '{$correctName}' ({$displayName}) not found - may need to be created");
            }
        }

        // Also fix any remaining permissions that have spaces/capitals
        $allPermissions = Permission::all();
        foreach ($allPermissions as $perm) {
            // Skip if already processed
            if (in_array($perm->name, array_keys($this->expectedPermissions))) {
                continue;
            }

            // Convert name to lowercase with underscores if it has spaces or capitals
            if (preg_match('/[A-Z\s]/', $perm->name)) {
                $newName = strtolower(str_replace(' ', '_', $perm->name));
                if ($newName !== $perm->name) {
                    // Check if new name already exists
                    $exists = Permission::where('name', $newName)->where('id', '!=', $perm->id)->exists();
                    if (!$exists) {
                        $oldName = $perm->name;
                        $perm->name = $newName;
                        if (empty($perm->display_name)) {
                            $perm->display_name = $oldName;
                        }
                        $perm->save();
                        $updated++;
                        $this->info("  ✓ Auto-fixed '{$oldName}' → '{$newName}'");
                    } else {
                        $this->warn("  ⚠️  Skipped '{$perm->name}' - '{$newName}' already exists");
                    }
                }
            }
        }

        $this->newLine();
        $this->info("✅ Permission name fix completed!");
        $this->table(
            ['Action', 'Count'],
            [
                ['Updated', $updated],
                ['Already correct', $alreadyCorrect],
                ['Not found', count($notFound)],
            ]
        );

        if (!empty($notFound)) {
            $this->newLine();
            $this->warn("⚠️  The following permissions were not found and may need to be created:");
            foreach ($notFound as $perm) {
                $this->line("  • {$perm}");
            }
        }

        // Clear cache
        try {
            \Illuminate\Support\Facades\Cache::flush();
            $this->info("\n✓ Cache cleared");
        } catch (\Exception $e) {
            $this->warn("\n⚠️  Cache flush failed: " . $e->getMessage());
        }

        return 0;
    }
}











