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
        // MySQL doesn't support direct enum modification, so we use raw SQL
        DB::statement("ALTER TABLE `day_services` MODIFY COLUMN `payment_status` ENUM('pending', 'paid', 'partial') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum values (without 'partial')
        DB::statement("ALTER TABLE `day_services` MODIFY COLUMN `payment_status` ENUM('pending', 'paid') DEFAULT 'pending'");
    }
};
