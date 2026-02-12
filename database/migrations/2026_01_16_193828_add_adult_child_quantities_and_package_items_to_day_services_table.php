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
            // Add adult and child quantity fields
            $table->integer('adult_quantity')->nullable()->after('number_of_people');
            $table->integer('child_quantity')->nullable()->after('adult_quantity');
            
            // Add package items JSON field for ceremony/birthday packages
            $table->json('package_items')->nullable()->after('items_ordered');
            
            // Change service_type from enum to string to support any service_key from catalog
            // We'll do this by adding a new column and migrating data, then dropping old
            $table->string('service_type_new')->nullable()->after('service_reference');
        });
        
        // Migrate existing data
        DB::statement("UPDATE day_services SET service_type_new = service_type");
        
        Schema::table('day_services', function (Blueprint $table) {
            $table->dropColumn('service_type');
        });
        
        Schema::table('day_services', function (Blueprint $table) {
            $table->renameColumn('service_type_new', 'service_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('day_services', function (Blueprint $table) {
            // Drop new columns
            $table->dropColumn(['adult_quantity', 'child_quantity', 'package_items']);
            
            // Revert service_type back to enum
            $table->enum('service_type_enum', ['swimming', 'restaurant', 'bar', 'other'])->nullable()->after('service_reference');
        });
        
        // Migrate data back
        DB::statement("UPDATE day_services SET service_type_enum = CASE 
            WHEN service_type IN ('swimming', 'restaurant', 'bar') THEN service_type 
            ELSE 'other' 
        END");
        
        Schema::table('day_services', function (Blueprint $table) {
            $table->dropColumn('service_type');
        });
        
        Schema::table('day_services', function (Blueprint $table) {
            $table->renameColumn('service_type_enum', 'service_type');
        });
    }
};
