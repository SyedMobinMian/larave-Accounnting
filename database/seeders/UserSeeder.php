<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the super admin role name from the config, default to 'super,_admin'
        $superAdminRoleName = config('filament-shield.super_admin.name', 'super_admin');

        // Find or create the super admin role
        $superAdminRole = Role::firstOrCreate(['name' => $superAdminRoleName, 'guard_name' => 'web']);

        // Find or create the super admin user
        $superAdminUser = User::firstOrCreate(
            ['email' => 'super@gmail.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('sink'),
            ]
        );

        // Assign the super admin role to the user
        $superAdminUser->assignRole($superAdminRole);
    }
}