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
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'company_id')) {
                $table->foreignId('company_id')->nullable()->after('guest_id');
            }
            if (!Schema::hasColumn('bookings', 'payment_responsibility')) {
                $table->enum('payment_responsibility', ['company', 'self', 'mixed'])->nullable()->after('company_id');
            }
            if (!Schema::hasColumn('bookings', 'is_corporate_booking')) {
                $table->boolean('is_corporate_booking')->default(false)->after('payment_responsibility');
            }
        });
        
        // Add foreign key constraint separately if companies table exists
        if (Schema::hasTable('companies')) {
            try {
                Schema::table('bookings', function (Blueprint $table) {
                    $table->foreign('company_id')->references('id')->on('companies')->onDelete('set null');
                });
            } catch (\Exception $e) {
                // Foreign key might already exist, ignore
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn(['company_id', 'payment_responsibility', 'is_corporate_booking']);
        });
    }
};
