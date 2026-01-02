<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Challenge management
            'create challenge',
            'edit challenge',
            'delete challenge',
            'view challenge',

            // Submission management
            'create submission',
            'edit own submission',
            'delete own submission',
            'view own submission',

            // Submission validation (admin only)
            'validate submission',
            'view all submissions',

            // User management (super admin only)
            'manage users',
            'manage roles',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions

        // Participant role
        $participant = Role::firstOrCreate(['name' => 'participant']);
        $participant->givePermissionTo([
            'view challenge',
            'create submission',
            'edit own submission',
            'delete own submission',
            'view own submission',
        ]);

        // Admin role
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->givePermissionTo([
            'create challenge',
            'edit challenge',
            'delete challenge',
            'view challenge',
            'validate submission',
            'view all submissions',
            'manage users',
        ]);

        // Super Admin role
        $superAdmin = Role::firstOrCreate(['name' => 'super admin']);
        $superAdmin->givePermissionTo([
            'create challenge',
            'edit challenge',
            'delete challenge',
            'view challenge',
            'validate submission',
            'view all submissions',
            'manage users',
            'manage roles',
        ]);

        $this->command->info('Roles and permissions created successfully.');
    }
}
