<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles if they don't exist
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);

        $staff = Role::firstOrCreate(['name' => 'staff']);
        $kepalaSekolah = Role::firstOrCreate(['name' => 'kepala_sekolah']);
        $koorJenjang = Role::firstOrCreate(['name' => 'koor_jenjang']);
        $adminUnit = Role::firstOrCreate(['name' => 'admin_unit']);

        // Absensi permissions
        $absensiPermissions = [
            'Absensi:viewAny',
            'Absensi:view',
            'Absensi:create',
            'Absensi:update',
            'Absensi:delete',
        ];

        foreach ($absensiPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to roles

        // Admin HR: only view permissions


        // Staff: full CRUD permissions
        $staff->syncPermissions([
            'Absensi:viewAny',
            'Absensi:view',
            'Absensi:create',
            'Absensi:update',
            'Absensi:delete',
        ]);

        // Kepala Sekolah: view only
        $kepalaSekolah->syncPermissions([
            'Absensi:viewAny',
            'Absensi:view',
        ]);

        // Koor Jenjang: view only
        $koorJenjang->syncPermissions([
            'Absensi:viewAny',
            'Absensi:view',
        ]);

        // Admin Unit: full CRUD (scoped to their unit)
        $adminUnit->syncPermissions([
            'Absensi:viewAny',
            'Absensi:view',
            'Absensi:create',
            'Absensi:update',
            'Absensi:delete',
        ]);

        $this->command->info('Roles and permissions created successfully!');
        $this->command->info('- super_admin: all permissions (via Shield config)');

        $this->command->info('- staff: full CRUD');
        $this->command->info('- kepala_sekolah: view only');
        $this->command->info('- koor_jenjang: view only');
        $this->command->info('- admin_unit: full CRUD (unit-scoped)');
    }
}
