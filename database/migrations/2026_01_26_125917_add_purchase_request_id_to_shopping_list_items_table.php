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
        Schema::table('shopping_list_items', function (Blueprint $table) {
            $table->unsignedBigInteger('purchase_request_id')->nullable()->after('shopping_list_id');
            $table->unsignedBigInteger('transferred_to_department')->nullable()->after('purchase_request_id');
            $table->boolean('is_received_by_department')->default(false)->after('transferred_to_department');
            $table->timestamp('received_by_department_at')->nullable()->after('is_received_by_department');
            
            $table->foreign('purchase_request_id')->references('id')->on('purchase_requests')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shopping_list_items', function (Blueprint $table) {
            $table->dropForeign(['purchase_request_id']);
            $table->dropColumn(['purchase_request_id', 'transferred_to_department', 'is_received_by_department', 'received_by_department_at']);
        });
    }
};
