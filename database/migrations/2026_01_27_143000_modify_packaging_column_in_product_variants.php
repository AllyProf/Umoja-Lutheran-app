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
        // Use raw SQL to modify column type to VARCHAR(50) and set default
        DB::statement("ALTER TABLE product_variants MODIFY COLUMN packaging VARCHAR(50) NOT NULL DEFAULT 'unit'");
        
        // Use raw SQL to set items_per_package default
        DB::statement("ALTER TABLE product_variants MODIFY COLUMN items_per_package INT NOT NULL DEFAULT 1");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to enum would be hard if data exists that doesn't match enum
        // We'll leave it as string for safety in rollback scenario too as it's less restrictive
    }
};
