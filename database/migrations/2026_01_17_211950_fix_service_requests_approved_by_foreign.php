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
        Schema::table('service_requests', function (Blueprint $table) {
            // Drop old constraint if it exists
            // MySQL naming is usually table_column_foreign
            $table->dropForeign(['approved_by']);
            
            // Re-add constraint referencing staffs table
            $table->foreign('approved_by')
                  ->references('id')
                  ->on('staffs')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            
            $table->foreign('approved_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }
};
