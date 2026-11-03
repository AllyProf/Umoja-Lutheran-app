<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Rename Waiter role label to Counter (staff still use role key "waiter" for routes).
     */
    public function up(): void
    {
        DB::table('roles')
            ->where('name', 'waiter')
            ->update([
                'display_name' => 'Counter',
                'description' => 'Counter staff who take guest orders and process sales at the restaurant/bar counter.',
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        DB::table('roles')
            ->where('name', 'waiter')
            ->update([
                'display_name' => 'Waiter',
                'description' => 'Staff responsible for taking guest orders and processing payments.',
                'updated_at' => now(),
            ]);
    }
};
