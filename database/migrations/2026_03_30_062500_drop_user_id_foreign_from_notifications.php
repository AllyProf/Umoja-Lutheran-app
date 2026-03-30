<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['user_id']);

            // Note: We keep the user_id column and it remains nullable
            // We just remove the hard constraint to the users table
            // since user_id can point to staffs or guests chairs
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Re-add the constraint if necessary (to the users table)
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
