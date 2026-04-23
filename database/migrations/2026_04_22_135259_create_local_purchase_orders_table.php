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
        Schema::create('local_purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('spent_amount', 15, 2)->default(0);
            $table->string('status')->default('pending'); // pending, sent_to_accountant, verified_by_manager, closed, cancelled
            $table->unsignedBigInteger('storekeeper_id');
            $table->unsignedBigInteger('accountant_id')->nullable();
            $table->unsignedBigInteger('manager_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('storekeeper_id')->references('id')->on('staffs');
            $table->foreign('accountant_id')->references('id')->on('staffs');
            $table->foreign('manager_id')->references('id')->on('staffs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('local_purchase_orders');
    }
};
