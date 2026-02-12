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
        if (Schema::hasTable('activity_logs')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                // Check if column doesn't exist before adding
                if (!Schema::hasColumn('activity_logs', 'user_id')) {
                    $table->unsignedBigInteger('user_id')->nullable()->after('id');
                }
                if (!Schema::hasColumn('activity_logs', 'user_type')) {
                    $table->string('user_type')->nullable()->after('user_id');
                }
                if (!Schema::hasColumn('activity_logs', 'action')) {
                    $table->string('action')->after('user_type');
                }
                if (!Schema::hasColumn('activity_logs', 'model_type')) {
                    $table->string('model_type')->nullable()->after('action');
                }
                if (!Schema::hasColumn('activity_logs', 'model_id')) {
                    $table->unsignedBigInteger('model_id')->nullable()->after('model_type');
                }
                if (!Schema::hasColumn('activity_logs', 'description')) {
                    $table->text('description')->nullable()->after('model_id');
                }
                if (!Schema::hasColumn('activity_logs', 'old_values')) {
                    $table->json('old_values')->nullable()->after('description');
                }
                if (!Schema::hasColumn('activity_logs', 'new_values')) {
                    $table->json('new_values')->nullable()->after('old_values');
                }
                if (!Schema::hasColumn('activity_logs', 'ip_address')) {
                    $table->string('ip_address')->nullable()->after('new_values');
                }
                if (!Schema::hasColumn('activity_logs', 'user_agent')) {
                    $table->string('user_agent')->nullable()->after('ip_address');
                }
            });
            
            // Add indexes if they don't exist (check columns exist first)
            try {
                if (Schema::hasColumn('activity_logs', 'user_id') && Schema::hasColumn('activity_logs', 'created_at')) {
                    Schema::table('activity_logs', function (Blueprint $table) {
                        try {
                            $table->index(['user_id', 'created_at'], 'activity_logs_user_id_created_at_index');
                        } catch (\Exception $e) {
                            // Index might already exist
                        }
                    });
                }
                if (Schema::hasColumn('activity_logs', 'model_type') && Schema::hasColumn('activity_logs', 'model_id')) {
                    Schema::table('activity_logs', function (Blueprint $table) {
                        try {
                            $table->index(['model_type', 'model_id'], 'activity_logs_model_type_model_id_index');
                        } catch (\Exception $e) {
                            // Index might already exist
                        }
                    });
                }
                if (Schema::hasColumn('activity_logs', 'action')) {
                    Schema::table('activity_logs', function (Blueprint $table) {
                        try {
                            $table->index('action', 'activity_logs_action_index');
                        } catch (\Exception $e) {
                            // Index might already exist
                        }
                    });
                }
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
        // Don't drop columns in down migration to avoid data loss
    }
};
