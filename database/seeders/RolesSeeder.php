<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define Permissions
        $permissions = [
            'view student dashboard',
            'view instructor dashboard',
            'view ta dashboard',
            'view all routes', // For TA and Instructor
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Define Roles
        $studentRole = Role::firstOrCreate(['name' => 'Student']);
        $instructorRole = Role::firstOrCreate(['name' => 'Instructor']);
        $taRole = Role::firstOrCreate(['name' => 'TA']);

        // Assign Permissions to Roles
        $studentRole->givePermissionTo('view student dashboard');
        $instructorRole->givePermissionTo(['view instructor dashboard', 'view all routes']);
        $taRole->givePermissionTo(['view ta dashboard', 'view all routes']); // TA should have more permissions
    }
}
