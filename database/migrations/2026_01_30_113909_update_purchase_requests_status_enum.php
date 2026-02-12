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
        Schema::table('purchase_requests', function (Blueprint $table) {
            // Updating ENUM values in Laravel migrations usually requires a raw SQL statement for best compatibility with MySQL
            DB::statement("ALTER TABLE purchase_requests MODIFY COLUMN status ENUM('pending', 'approved', 'on_list', 'rejected', 'purchased', 'completed', 'received') DEFAULT 'pending'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_requests', function (Blueprint $table) {
            DB::statement("ALTER TABLE purchase_requests MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'purchased', 'completed') DEFAULT 'pending'");
        });
    }
};
