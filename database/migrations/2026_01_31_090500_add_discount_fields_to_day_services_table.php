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
        Schema::table('day_services', function (Blueprint $table) {
            $table->enum('discount_type', ['none', 'percentage', 'fixed'])->default('none')->after('amount');
            $table->decimal('discount_value', 10, 2)->default(0)->after('discount_type');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('discount_value');
            $table->string('discount_reason')->nullable()->after('discount_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('day_services', function (Blueprint $table) {
            $table->dropColumn(['discount_type', 'discount_value', 'discount_amount', 'discount_reason']);
        });
    }
};
