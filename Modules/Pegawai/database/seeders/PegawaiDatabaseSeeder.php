<?php

namespace Modules\Pegawai\Database\Seeders;

use Illuminate\Database\Seeder;

class PegawaiDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Create Roles
        $roles = ['admin_hr', 'kepala_sekolah', 'staff'];
        foreach ($roles as $role) {
            \Spatie\Permission\Models\Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        // 2. Create Units
        $unitSD = \Modules\Pegawai\Models\Unit::firstOrCreate(['name' => 'SD IT Al-Huda', 'type' => 'SD']);
        $unitSMP = \Modules\Pegawai\Models\Unit::firstOrCreate(['name' => 'SMP IT Al-Huda', 'type' => 'SMP']);

        // 3. Create Admin HR
        $adminUser = \App\Models\User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            ['name' => 'Admin HR', 'password' => bcrypt('password')]
        );
        $adminUser->assignRole('admin_hr');

        // 4. Create Kepala Sekolah (SD)
        $kepsekUser = \App\Models\User::firstOrCreate(
            ['email' => 'kepsek@admin.com'],
            ['name' => 'Kepala Sekolah SD', 'password' => bcrypt('password')]
        );
        $kepsekUser->assignRole('kepala_sekolah');

        $kepsekEmployee = \Modules\Pegawai\Models\DataInduk::firstOrCreate(
            ['user_id' => $kepsekUser->id],
            ['nama' => 'Budi Kepsek', 'nip' => 'K001', 'jabatan' => 'Kepala Sekolah', 'status_kepegawaian' => 'Tetap']
        );
        // Link to Unit SD
        $kepsekEmployee->units()->syncWithoutDetaching([$unitSD->id]);


        // 5. Create Staff (SD)
        $staffUser = \App\Models\User::firstOrCreate(
            ['email' => 'staff@admin.com'],
            ['name' => 'Staff Biasa', 'password' => bcrypt('password')]
        );
        $staffUser->assignRole('staff');

        $staffEmployee = \Modules\Pegawai\Models\DataInduk::firstOrCreate(
            ['user_id' => $staffUser->id],
            ['nama' => 'Siti Staff', 'nip' => 'S001', 'jabatan' => 'Guru Kelas', 'status_kepegawaian' => 'Tetap']
        );
        // Link to Unit SD
        $staffEmployee->units()->syncWithoutDetaching([$unitSD->id]);

        // 6. Create Staff (SMP) - Should NOT be visible to Kepsek SD
        $staffSMPUser = \App\Models\User::firstOrCreate(
            ['email' => 'staff_smp@admin.com'],
            ['name' => 'Staff SMP', 'password' => bcrypt('password')]
        );
        $staffSMPUser->assignRole('staff');

        $staffSMPEmployee = \Modules\Pegawai\Models\DataInduk::firstOrCreate(
            ['user_id' => $staffSMPUser->id],
            ['nama' => 'Ahmad Guru SMP', 'nip' => 'S002', 'jabatan' => 'Guru Mapel', 'status_kepegawaian' => 'Tetap']
        );
        $staffSMPEmployee->units()->syncWithoutDetaching([$unitSMP->id]);
    }
}
