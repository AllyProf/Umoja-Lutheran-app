<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        Role::firstOrCreate(
            ['name' => 'restaurant_manager'],
            [
                'slug' => 'restaurant_manager',
                'display_name' => 'Restaurant Manager (Assistant)',
                'description' => 'Assistant manager with full restaurant, kitchen and counter access only.',
                'is_system' => true,
            ]
        );
    }

    public function down(): void
    {
        Role::where('name', 'restaurant_manager')->delete();
    }
};
