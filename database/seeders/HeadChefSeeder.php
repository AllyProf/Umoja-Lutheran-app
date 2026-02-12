<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Staff;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\Hash;

class HeadChefSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Permissions
        $permissions = [
            'view_kitchen_dashboard',
            'manage_kitchen_stock',
            'manage_shopping_lists',
            'view_recipes',
            'manage_recipes',
        ];

        foreach ($permissions as $pName) {
            Permission::firstOrCreate(['name' => $pName], [
                'slug' => \Illuminate\Support\Str::slug($pName),
                'display_name' => ucwords(str_replace('_', ' ', $pName)),
                'description' => 'Permission to ' . str_replace('_', ' ', $pName),
            ]);
        }

        // 2. Create Role
        $role = Role::firstOrCreate(['name' => 'head_chef'], [
            'slug' => 'head_chef',
            'display_name' => 'Head Chef',
            'description' => 'Kitchen Manager and Head Chef',
            'is_system' => false,
        ]);

        // 3. Assign Permissions to Role
        // Get all permission IDs
        $permIds = Permission::whereIn('name', $permissions)->pluck('id');
        $role->permissions()->sync($permIds);

        // Also assign 'manage_shopping_lists' to Manager and Super Admin if they exist
        $managerRole = Role::where('name', 'manager')->first();
        if ($managerRole) {
            $managerRole->assignPermission('manage_shopping_lists');
            $managerRole->assignPermission('view_kitchen_dashboard');
            $managerRole->assignPermission('manage_kitchen_stock');
        }

        // 4. Create Staff Account
        $chef = Staff::updateOrCreate(
            ['email' => 'chef@primeland.com'],
            [
                'name' => 'Chef Master',
                'password' => Hash::make('password'),
                'role' => 'head_chef',
                'phone' => '0700000001',
                'department' => 'Kitchen',
                'position' => 'Head Chef',
                'is_active' => true,
                'hire_date' => now(),
            ]
        );

        $this->command->info('Head Chef (Chef Master) created successfully!');
        $this->command->info('Email: chef@primeland.com');
        $this->command->info('Password: password');
    }
}
