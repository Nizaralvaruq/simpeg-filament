<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class KegiatanPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Get or create role staff
        $staffRole = Role::firstOrCreate(['name' => 'staff']);

        // Create permissions if they don't exist
        $viewPermission = Permission::firstOrCreate(['name' => 'View:Kegiatan']);
        $viewAnyPermission = Permission::firstOrCreate(['name' => 'ViewAny:Kegiatan']);

        // Assign permissions to staff role
        $staffRole->givePermissionTo([
            'View:Kegiatan',
            'ViewAny:Kegiatan',
        ]);

        // Get or create role kepala_sekolah
        $ksRole = Role::firstOrCreate(['name' => 'kepala_sekolah']);

        // Kepala Sekolah assigned same viewing permissions
        $ksRole->givePermissionTo([
            'View:Kegiatan',
            'ViewAny:Kegiatan',
        ]);

        // Get or create role admin_unit
        $adminUnitRole = Role::firstOrCreate(['name' => 'admin_unit']);

        // Admin Unit can view activities and see filtered reports
        $adminUnitRole->givePermissionTo([
            'View:Kegiatan',
            'ViewAny:Kegiatan',
        ]);

        // Get or create role ketua_psdm
        $psdmRole = Role::firstOrCreate(['name' => 'ketua_psdm']);

        // Ketua PSDM can view activities and see all reports
        $psdmRole->givePermissionTo([
            'View:Kegiatan',
            'ViewAny:Kegiatan',
        ]);

        // Get or create role koor_jenjang
        $koorRole = Role::firstOrCreate(['name' => 'koor_jenjang']);

        // Koor Jenjang can view activities and see filtered reports
        $koorRole->givePermissionTo([
            'View:Kegiatan',
            'ViewAny:Kegiatan',
        ]);

        $this->command->info('✓ Permissions assigned to staff, kepala_sekolah, admin_unit, ketua_psdm & koor_jenjang roles for Kegiatan');
    }
}
