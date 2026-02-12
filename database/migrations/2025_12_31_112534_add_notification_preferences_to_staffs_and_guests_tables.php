<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add notification_preferences to staffs table
        Schema::table('staffs', function (Blueprint $table) {
            $table->json('notification_preferences')->nullable()->after('is_active');
        });

        // Add notification_preferences to guests table
        Schema::table('guests', function (Blueprint $table) {
            $table->json('notification_preferences')->nullable()->after('is_active');
        });

        // Set default notification preferences for existing users (all enabled by default)
        $defaultPreferences = json_encode([
            'email_notifications_enabled' => true,
            'service_request_notifications' => true,
            'issue_report_notifications' => true,
            'booking_notifications' => true,
            'payment_notifications' => true,
            'check_in_out_notifications' => true,
            'extension_request_notifications' => true,
        ]);

        // Update existing staff records
        DB::table('staffs')->whereNull('notification_preferences')->update([
            'notification_preferences' => $defaultPreferences
        ]);

        // Update existing guest records
        DB::table('guests')->whereNull('notification_preferences')->update([
            'notification_preferences' => $defaultPreferences
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staffs', function (Blueprint $table) {
            $table->dropColumn('notification_preferences');
        });

        Schema::table('guests', function (Blueprint $table) {
            $table->dropColumn('notification_preferences');
        });
    }
};
