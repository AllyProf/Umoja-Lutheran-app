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
        // Change category to varchar to allow new categories like spirits, wines
        DB::statement("ALTER TABLE products MODIFY COLUMN category VARCHAR(50) NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // tough to revert if data exists
    }
};
