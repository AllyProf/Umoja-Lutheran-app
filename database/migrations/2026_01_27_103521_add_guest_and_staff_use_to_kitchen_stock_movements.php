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
        // For MySQL, we update the ENUM using raw SQL to include new values
        DB::statement("ALTER TABLE kitchen_stock_movements MODIFY COLUMN movement_type ENUM('supply', 'sale', 'internal_use', 'adjustment', 'guest_use', 'staff_use') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original ENUM values (be careful if data exists with new types)
        DB::statement("ALTER TABLE kitchen_stock_movements MODIFY COLUMN movement_type ENUM('supply', 'sale', 'internal_use', 'adjustment') NOT NULL");
    }
};
