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
        $ketuaPsdm = Role::firstOrCreate(['name' => 'ketua_psdm']);

        // Permissions categories
        $modules = [
            'Absensi',
            'DataInduk',
            'AppraisalAssignment',
            'LeaveRequest',
            'Resign',
            'PerformanceScore',
            'Unit',
            'Golongan'
        ];

        $actions = ['ViewAny', 'View', 'Create', 'Update', 'Delete'];

        foreach ($modules as $module) {
            foreach ($actions as $action) {
                Permission::firstOrCreate(['name' => "{$action}:{$module}"]);
            }
        }

        // Assign permissions to roles

        // Staff: full CRUD permissions on self-related items
        $staff->syncPermissions([
            'ViewAny:Absensi',
            'View:Absensi',
            'Create:Absensi',
            'Update:Absensi',
            'Delete:Absensi',
            'ViewAny:LeaveRequest',
            'Create:LeaveRequest',
            'ViewAny:Resign',
            'Create:Resign',
        ]);

        // Kepala Sekolah: view only
        $kepalaSekolah->syncPermissions([
            'ViewAny:Absensi',
            'View:Absensi',
            'ViewAny:DataInduk',
            'ViewAny:AppraisalAssignment',
            'ViewAny:LeaveRequest',
            'ViewAny:Resign',
        ]);

        // Koor Jenjang: view only
        $koorJenjang->syncPermissions([
            'ViewAny:Absensi',
            'View:Absensi',
            'ViewAny:DataInduk',
            'ViewAny:AppraisalAssignment',
            'ViewAny:LeaveRequest',
            'ViewAny:Resign',
        ]);

        // Admin Unit: full CRUD (scoped to their unit)
        $adminUnit->syncPermissions([
            'ViewAny:Absensi',
            'View:Absensi',
            'Create:Absensi',
            'Update:Absensi',
            'Delete:Absensi',
            'ViewAny:DataInduk',
            'Update:DataInduk',
            'ViewAny:AppraisalAssignment',
            'Create:AppraisalAssignment',
            'Update:AppraisalAssignment',
            'Delete:AppraisalAssignment',
            'ViewAny:LeaveRequest',
            'Update:LeaveRequest',
            'ViewAny:Resign',
            'Update:Resign',
        ]);

        // Ketua PSDM: full CRUD (Global)
        $ketuaPsdm->syncPermissions([
            'ViewAny:Absensi',
            'View:Absensi',
            'Create:Absensi',
            'Update:Absensi',
            'Delete:Absensi',
            'ViewAny:DataInduk',
            'View:DataInduk',
            'Create:DataInduk',
            'Update:DataInduk',
            'Delete:DataInduk',
            'ViewAny:AppraisalAssignment',
            'View:AppraisalAssignment',
            'Create:AppraisalAssignment',
            'Update:AppraisalAssignment',
            'Delete:AppraisalAssignment',
            'ViewAny:PerformanceScore',
            'ViewAny:LeaveRequest',
            'View:LeaveRequest',
            'Update:LeaveRequest',
            'ViewAny:Resign',
            'View:Resign',
            'Update:Resign',
            'ViewAny:Unit',
            'ViewAny:Golongan',
        ]);

        $this->command->info('Roles and permissions created successfully!');
        $this->command->info('- super_admin: all permissions (via Shield config)');

        $this->command->info('- staff: full CRUD');
        $this->command->info('- kepala_sekolah: view only');
        $this->command->info('- koor_jenjang: view only');
        $this->command->info('- admin_unit: full CRUD (unit-scoped)');
    }
}
