<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('supplier_weekly_orders', function (Blueprint $row) {
            $row->timestamp('received_at')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('supplier_weekly_orders', function (Blueprint $row) {
            $row->dropColumn('received_at');
        });
    }
};
