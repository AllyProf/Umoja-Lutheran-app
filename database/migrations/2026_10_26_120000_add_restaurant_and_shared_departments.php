<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $now = now();

        $departments = [
            [
                'name' => 'Restaurant',
                'code' => 'restaurant',
                'description' => 'Restaurant department — food and drinks combined',
            ],
            [
                'name' => 'Shared',
                'code' => 'shared',
                'description' => 'Items shared across Restaurant and Housekeeping',
            ],
            [
                'name' => 'Housekeeping',
                'code' => 'housekeeping',
                'description' => 'Housekeeping — cleaning, linens, and amenities',
            ],
            [
                'name' => 'Bar',
                'code' => 'bar',
                'description' => 'Bar section — drinks (used with Restaurant)',
            ],
            [
                'name' => 'Kitchen',
                'code' => 'kitchen',
                'description' => 'Kitchen section — food (used with Restaurant)',
            ],
        ];

        foreach ($departments as $dept) {
            $exists = DB::table('departments')->where('code', $dept['code'])->first();
            if ($exists) {
                DB::table('departments')->where('code', $dept['code'])->update([
                    'name' => $dept['name'],
                    'description' => $dept['description'],
                    'is_active' => true,
                    'updated_at' => $now,
                ]);
            } else {
                DB::table('departments')->insert([
                    'name' => $dept['name'],
                    'code' => $dept['code'],
                    'description' => $dept['description'],
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        DB::table('departments')->whereIn('code', ['restaurant', 'shared'])->delete();
    }
};
