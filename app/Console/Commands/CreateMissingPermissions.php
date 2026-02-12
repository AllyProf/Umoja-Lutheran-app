<?php

namespace App\Console\Commands;

use App\Models\Permission;
use Illuminate\Console\Command;

class CreateMissingPermissions extends Command
{
    protected $signature = 'permissions:create-missing';
    protected $description = 'Create missing permissions that the sidebar expects';

    protected $missingPermissions = [
        ['name' => 'manage_bookings', 'display_name' => 'Manage Bookings', 'description' => 'Full access to manage all bookings', 'group' => 'Bookings', 'slug' => 'manage_bookings'],
        ['name' => 'view_booking_calendar', 'display_name' => 'View Booking Calendar', 'description' => 'View the booking calendar', 'group' => 'Bookings', 'slug' => 'view_booking_calendar'],
        ['name' => 'create_manual_bookings', 'display_name' => 'Create Manual Bookings', 'description' => 'Create bookings manually', 'group' => 'Bookings', 'slug' => 'create_manual_bookings'],
        ['name' => 'manage_extensions', 'display_name' => 'Manage Extensions', 'description' => 'Manage booking extension requests', 'group' => 'Bookings', 'slug' => 'manage_extensions'],
        ['name' => 'manage_checkin', 'display_name' => 'Manage Check In', 'description' => 'Process guest check-ins', 'group' => 'Bookings', 'slug' => 'manage_checkin'],
        ['name' => 'manage_checkout', 'display_name' => 'Manage Check Out', 'description' => 'Process guest check-outs', 'group' => 'Bookings', 'slug' => 'manage_checkout'],
        ['name' => 'view_active_reservations', 'display_name' => 'View Active Reservations', 'description' => 'View currently active reservations', 'group' => 'Bookings', 'slug' => 'view_active_reservations'],
        ['name' => 'view_guests', 'display_name' => 'View Guests', 'description' => 'View guest information', 'group' => 'Users', 'slug' => 'view_guests'],
        ['name' => 'manage_rooms', 'display_name' => 'Manage Rooms', 'description' => 'Full access to manage all rooms', 'group' => 'Rooms', 'slug' => 'manage_rooms'],
        ['name' => 'view_room_status', 'display_name' => 'View Room Status', 'description' => 'View room status information', 'group' => 'Rooms', 'slug' => 'view_room_status'],
        ['name' => 'manage_room_cleaning', 'display_name' => 'Manage Room Cleaning', 'description' => 'Manage room cleaning schedules', 'group' => 'Rooms', 'slug' => 'manage_room_cleaning'],
        ['name' => 'view_daily_reports', 'display_name' => 'View Daily Reports', 'description' => 'View daily reports', 'group' => 'Reports', 'slug' => 'view_daily_reports'],
        ['name' => 'manage_blog', 'display_name' => 'Manage Blog', 'description' => 'Manage blog posts', 'group' => 'Blog', 'slug' => 'manage_blog'],
        ['name' => 'view_feedback', 'display_name' => 'View Feedback', 'description' => 'View guest feedback and reviews', 'group' => 'Feedback & Analytics', 'slug' => 'view_feedback'],
        ['name' => 'manage_exchange_rates', 'display_name' => 'Manage Exchange Rates', 'description' => 'Manage currency exchange rates', 'group' => 'Feedback & Analytics', 'slug' => 'manage_exchange_rates'],
        ['name' => 'manage_wifi_settings', 'display_name' => 'Manage WiFi Settings', 'description' => 'Manage WiFi network settings', 'group' => 'Settings', 'slug' => 'manage_wifi_settings'],
        ['name' => 'manage_hotel_settings', 'display_name' => 'Manage Hotel Settings', 'description' => 'Manage hotel general settings', 'group' => 'Settings', 'slug' => 'manage_hotel_settings'],
        ['name' => 'manage_room_settings', 'display_name' => 'Manage Room Settings', 'description' => 'Manage room configuration settings', 'group' => 'Settings', 'slug' => 'manage_room_settings'],
        ['name' => 'manage_pricing', 'display_name' => 'Manage Pricing', 'description' => 'Manage room pricing settings', 'group' => 'Settings', 'slug' => 'manage_pricing'],
    ];

    public function handle()
    {
        $this->info("➕ Creating missing permissions...\n");

        $created = 0;
        $skipped = 0;

        foreach ($this->missingPermissions as $permData) {
            $existing = Permission::where('name', $permData['name'])->first();
            
            if ($existing) {
                $this->line("  ⊙ '{$permData['name']}' already exists - skipped");
                $skipped++;
            } else {
                // Ensure slug is set (use name if not provided)
                if (!isset($permData['slug'])) {
                    $permData['slug'] = $permData['name'];
                }
                Permission::create($permData);
                $created++;
                $this->info("  ✓ Created '{$permData['name']}' ({$permData['display_name']})");
            }
        }

        $this->newLine();
        $this->info("✅ Missing permissions creation completed!");
        $this->table(
            ['Action', 'Count'],
            [
                ['Created', $created],
                ['Skipped (already exist)', $skipped],
            ]
        );

        if ($created > 0) {
            $this->newLine();
            $this->info("💡 Tip: Run 'php artisan permissions:assign-all-to-manager --force' to assign these new permissions to the manager role.");
        }

        return 0;
    }
}

