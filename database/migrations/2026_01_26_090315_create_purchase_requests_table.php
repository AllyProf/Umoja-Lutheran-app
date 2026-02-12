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
        Schema::create('purchase_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requested_by')->constrained('staffs')->onDelete('cascade');
            $table->string('item_name');
            $table->string('category')->nullable();
            $table->decimal('quantity', 10, 2);
            $table->string('unit')->default('pcs');
            $table->text('reason')->nullable(); // Why they need it
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', ['pending', 'approved', 'rejected', 'purchased', 'completed'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('staffs')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->foreignId('shopping_list_id')->nullable()->constrained('shopping_lists')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_requests');
    }
};
