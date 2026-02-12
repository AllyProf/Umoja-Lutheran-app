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
        Schema::table('companies', function (Blueprint $table) {
            if (!Schema::hasColumn('companies', 'guider_email')) {
                $table->string('guider_email')->nullable()->after('contact_person');
            }
            if (!Schema::hasColumn('companies', 'guider_phone')) {
                $table->string('guider_phone')->nullable()->after('guider_email');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (Schema::hasColumn('companies', 'guider_phone')) {
                $table->dropColumn('guider_phone');
            }
            if (Schema::hasColumn('companies', 'guider_email')) {
                $table->dropColumn('guider_email');
            }
        });
    }
};
