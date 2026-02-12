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
        Schema::table('shopping_lists', function (Blueprint $table) {
            $table->decimal('budget_amount', 10, 2)->nullable()->after('total_estimated_cost');
            $table->decimal('amount_used', 10, 2)->default(0)->after('budget_amount');
            $table->decimal('amount_remaining', 10, 2)->nullable()->after('amount_used');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shopping_lists', function (Blueprint $table) {
            $table->dropColumn(['budget_amount', 'amount_used', 'amount_remaining']);
        });
    }
};
