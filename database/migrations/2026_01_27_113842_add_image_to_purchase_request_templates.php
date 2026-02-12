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
        Schema::table('purchase_request_templates', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_request_templates', 'product_image')) {
                $table->string('product_image')->nullable()->comment('Product image for bar drinks');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_request_templates', function (Blueprint $table) {
            $table->dropColumn('product_image');
        });
    }
};
