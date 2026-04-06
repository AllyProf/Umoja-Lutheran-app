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
        Schema::table('shift_closures', function (Blueprint $table) {
            DB::statement("ALTER TABLE shift_closures MODIFY COLUMN status ENUM('active', 'pending_reception', 'acknowledged', 'rejected') DEFAULT 'active'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shift_closures', function (Blueprint $table) {
            DB::statement("ALTER TABLE shift_closures MODIFY COLUMN status ENUM('pending_reception', 'acknowledged', 'rejected') DEFAULT 'pending_reception'");
        });
    }
};
