<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('system_logs')) {
            Schema::table('system_logs', function (Blueprint $table) {
                // Check if columns don't exist before adding
                if (!Schema::hasColumn('system_logs', 'level')) {
                    $table->string('level')->after('id'); // info, warning, error, critical
                }
                if (!Schema::hasColumn('system_logs', 'channel')) {
                    $table->string('channel')->nullable()->after('level'); // database, auth, booking, etc.
                }
                if (!Schema::hasColumn('system_logs', 'message')) {
                    $table->text('message')->after('channel');
                }
                if (!Schema::hasColumn('system_logs', 'context')) {
                    $table->json('context')->nullable()->after('message');
                }
                if (!Schema::hasColumn('system_logs', 'user_id')) {
                    $table->unsignedBigInteger('user_id')->nullable()->after('context');
                }
                if (!Schema::hasColumn('system_logs', 'ip_address')) {
                    $table->string('ip_address')->nullable()->after('user_id');
                }
                if (!Schema::hasColumn('system_logs', 'user_agent')) {
                    $table->string('user_agent')->nullable()->after('ip_address');
                }
            });
            
            // Add indexes (will fail silently if they already exist)
            try {
                Schema::table('system_logs', function (Blueprint $table) {
                    $table->index(['level', 'created_at']);
                    $table->index('channel');
                });
            } catch (\Exception $e) {
                // Indexes might already exist, ignore
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('system_logs')) {
            Schema::table('system_logs', function (Blueprint $table) {
                $table->dropIndex(['level', 'created_at']);
                $table->dropIndex(['channel']);
                $table->dropColumn(['level', 'channel', 'message', 'context', 'user_id', 'ip_address', 'user_agent']);
            });
        }
    }
};
