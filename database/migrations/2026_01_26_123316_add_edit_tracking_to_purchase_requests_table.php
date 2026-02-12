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
        Schema::table('purchase_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('edited_by')->nullable()->after('updated_at');
            $table->timestamp('last_edited_at')->nullable()->after('edited_by');
            $table->json('last_changes')->nullable()->after('last_edited_at');
            
            $table->foreign('edited_by')->references('id')->on('staffs')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_requests', function (Blueprint $table) {
            $table->dropForeign(['edited_by']);
            $table->dropColumn(['edited_by', 'last_edited_at', 'last_changes']);
        });
    }
};
