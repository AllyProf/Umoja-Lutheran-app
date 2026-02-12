<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Staff;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ChefMasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Define Permissions for Kitchen/Chef
        $permissions = [
            'view_kitchen_dashboard',
            'manage_kitchen_stock',
            'manage_shopping_lists',
            'view_recipes',
            'manage_recipes',
            'view_inventory',
            'manage_inventory',
        ];

        foreach ($permissions as $pName) {
            Permission::firstOrCreate(['name' => $pName], [
                'display_name' => ucwords(str_replace('_', ' ', $pName)),
                'description' => 'Permission to ' . str_replace('_', ' ', $pName),
                'group' => 'Kitchen',
            ]);
        }

        // 2. Create head_chef Role
        $role = Role::firstOrCreate(['name' => 'head_chef'], [
            'slug' => 'head_chef',
            'display_name' => 'Head Chef',
            'description' => 'Kitchen Manager and Head Chef',
            'is_system' => false,
        ]);

        // 3. Assign Permissions to Role
        $permIds = Permission::whereIn('name', $permissions)->pluck('id');
        $role->permissions()->syncWithoutDetaching($permIds);

        // Also ensure Manager and Super Admin have these permissions for oversight
        $adminRoles = Role::whereIn('name', ['manager', 'super_admin'])->get();
        foreach ($adminRoles as $adminRole) {
            $adminRole->permissions()->syncWithoutDetaching($permIds);
        }

        // 4. Create Chef Master Staff Account
        $chef = Staff::updateOrCreate(
            ['email' => 'chefmaster@primeland.com'],
            [
                'name' => 'Chef Master',
                'password' => Hash::make('password'),
                'role' => 'head_chef',
                'phone' => '0711223344',
                'department' => 'Kitchen',
                'position' => 'Head Chef',
                'is_active' => true,
                'hire_date' => now(),
            ]
        );

        $this->command->info('Chef Master (Head Chef) account created successfully!');
        $this->command->info('Email: chefmaster@primeland.com');
        $this->command->info('Password: password');
        $this->command->info('Role: head_chef');
    }
}
