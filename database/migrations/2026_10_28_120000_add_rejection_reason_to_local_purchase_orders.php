<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('local_purchase_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('local_purchase_orders', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('notes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('local_purchase_orders', function (Blueprint $table) {
            if (Schema::hasColumn('local_purchase_orders', 'rejection_reason')) {
                $table->dropColumn('rejection_reason');
            }
        });
    }
};
