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

        $this->command->info('✓ Permissions assigned to staff role for Kegiatan');
    }
}
