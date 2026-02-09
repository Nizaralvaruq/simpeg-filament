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
            'PenugasanPenilaian',
            'LeaveRequest',
            'Resign',
            'NilaiKinerja',
            'SesiPenilaian',
            'RubrikPenilaian',
            'LaporanPenilaian',
            'TugasPenilaianSaya',
            'Unit',
            'Golongan'
        ];

        $actions = ['ViewAny', 'View', 'Create', 'Update', 'Delete'];

        foreach ($modules as $module) {
            foreach ($actions as $action) {
                Permission::firstOrCreate(['name' => "{$action}:{$module}"]);
            }
        }

        // Widget Permissions
        $widgets = [
            'DaftarIzinMenunggu',
            'DaftarTidakMasukHariIni',
            'GrafikDistribusiPegawai',
            'GrafikProgresPenilaian',
            'GrafikStatistikGender',
            'GrafikTrenKehadiran',
            'JadwalPiketHariIni',
            'RingkasanPerformaUnit',
            'RingkasanStatistikSDM',
            'RingkasanOperasionalUnit', // Consolidated widget (Pegawai Terlambat + Penilaian Kinerja)
            'StatistikPegawaiTerlambat', // Legacy - kept for backward compatibility
            'StatistikPenilaianKinerja', // Legacy - kept for backward compatibility
            'DaftarPegawaiUnit',
            'RiwayatAbsensiTerbaru',
            'StatistikAbsensiSaya',
            'ProfilSayaWidget',
        ];

        foreach ($widgets as $widget) {
            Permission::firstOrCreate(['name' => "View:{$widget}"]);
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
            'View:ProfilSayaWidget',
            'View:StatistikAbsensiSaya',
            'View:RiwayatAbsensiTerbaru',
        ]);

        // Kepala Sekolah: view only
        $kepalaSekolah->syncPermissions([
            'ViewAny:Absensi',
            'View:Absensi',
            'ViewAny:DataInduk',
            'ViewAny:PenugasanPenilaian',
            'ViewAny:LeaveRequest',
            'ViewAny:Resign',
            'ViewAny:LaporanPenilaian',
            'ViewAny:NilaiKinerja',
            'View:StatistikPegawaiTerlambat',
            'View:StatistikPenilaianKinerja',
            'View:GrafikTrenKehadiran',
            'View:RingkasanStatistikSDM',
        ]);

        // Koor Jenjang: view only
        $koorJenjang->syncPermissions([
            'ViewAny:Absensi',
            'View:Absensi',
            'ViewAny:DataInduk',
            'ViewAny:PenugasanPenilaian',
            'ViewAny:LeaveRequest',
            'ViewAny:Resign',
            'View:DaftarPegawaiUnit',
            'View:StatistikPenilaianKinerja',
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
            'ViewAny:PenugasanPenilaian',
            'Create:PenugasanPenilaian',
            'Update:PenugasanPenilaian',
            'Delete:PenugasanPenilaian',
            'ViewAny:LeaveRequest',
            'Update:LeaveRequest',
            'ViewAny:Resign',
            'Update:Resign',
            'View:DaftarPegawaiUnit',
            'View:StatistikPegawaiTerlambat',
            'View:StatistikPenilaianKinerja',
        ]);

        // Ketua PSDM: full CRUD (Global)
        $ketuaPsdm->syncPermissions([
            'ViewAny:Absensi',
            'ViewAny:Absensi',
            'View:Absensi',
            'ViewAny:DataInduk',
            'View:DataInduk',
            'ViewAny:PenugasanPenilaian',
            'View:PenugasanPenilaian',
            'Create:PenugasanPenilaian',
            'Update:PenugasanPenilaian',
            'Delete:PenugasanPenilaian',
            'ViewAny:NilaiKinerja',
            'ViewAny:SesiPenilaian',
            'ViewAny:RubrikPenilaian',
            'ViewAny:LaporanPenilaian',
            'ViewAny:LeaveRequest',
            'View:LeaveRequest',
            'Update:LeaveRequest',
            'ViewAny:Resign',
            'View:Resign',
            'Update:Resign',
            'ViewAny:Unit',
            'ViewAny:Golongan',
            'View:StatistikPegawaiTerlambat',
            'View:StatistikPenilaianKinerja',
            'View:GrafikTrenKehadiran',
            'View:RingkasanStatistikSDM',
            'View:GrafikDistribusiPegawai',
            'View:GrafikStatistikGender',
            'View:DaftarIzinMenunggu',
            'View:DaftarTidakMasukHariIni',
            'View:JadwalPiketHariIni',
            'View:RingkasanPerformaUnit',
            'View:GrafikProgresPenilaian',
        ]);

        $this->command->info('Roles and permissions created successfully!');
        $this->command->info('- super_admin: all permissions (via Shield config)');

        $this->command->info('- staff: full CRUD');
        $this->command->info('- kepala_sekolah: view only');
        $this->command->info('- koor_jenjang: view only');
        $this->command->info('- admin_unit: full CRUD (unit-scoped)');
    }
}
