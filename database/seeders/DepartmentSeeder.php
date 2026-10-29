<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $departments = [
            [
                'name' => 'Restaurant',
                'code' => 'restaurant',
                'description' => 'Restaurant department — food and drinks combined.',
                'is_active' => true,
            ],
            [
                'name' => 'Housekeeping',
                'code' => 'housekeeping',
                'description' => 'Housekeeping — cleaning, linens, and amenities.',
                'is_active' => true,
            ],
            [
                'name' => 'Shared',
                'code' => 'shared',
                'description' => 'Items shared across Restaurant and Housekeeping.',
                'is_active' => true,
            ],
            [
                'name' => 'Bar',
                'code' => 'bar',
                'description' => 'Bar section — drinks (linked under Restaurant).',
                'is_active' => true,
            ],
            [
                'name' => 'Kitchen',
                'code' => 'kitchen',
                'description' => 'Kitchen section — food (linked under Restaurant).',
                'is_active' => true,
            ],
        ];

        foreach ($departments as $dept) {
            Department::updateOrCreate(
                ['code' => $dept['code']],
                $dept
            );
        }
    }
}
