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
        Schema::table('bookings', function (Blueprint $table) {
            $table->date('extension_requested_to')->nullable()->after('check_out');
            $table->enum('extension_status', ['pending', 'approved', 'rejected'])->nullable()->after('extension_requested_to');
            $table->timestamp('extension_requested_at')->nullable()->after('extension_status');
            $table->timestamp('extension_approved_at')->nullable()->after('extension_requested_at');
            $table->text('extension_reason')->nullable()->after('extension_approved_at');
            $table->text('extension_admin_notes')->nullable()->after('extension_reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'extension_requested_to',
                'extension_status',
                'extension_requested_at',
                'extension_approved_at',
                'extension_reason',
                'extension_admin_notes',
            ]);
        });
    }
};



