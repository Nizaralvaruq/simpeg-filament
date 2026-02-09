<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Modules\MasterData\Models\Unit;
use Modules\Kepegawaian\Models\DataInduk;
use Modules\Presensi\Models\Absensi;
use Modules\Leave\Models\LeaveRequest;

class DemoDataSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // Truncate tables (optional, commented out)
        // User::truncate();
        // Unit::truncate();
        // DataInduk::truncate();

        // User
        User::create([
            'id' => 39,
            'name' => 'Kepala Sekolah',
            'email' => 'kepala.sekolah@domain.com',
            'email_verified_at' => null,
            'password' => '$2y$12$cjgAqmPfU26scV5PgwxKI.wGUmFHSn1haDOsIqLpNLUrergCMDjKC',
            'two_factor_confirmed_at' => null,
            'remember_token' => null,
            'created_at' => '2026-01-06T15:39:45.000000Z',
            'updated_at' => '2026-01-06T15:39:45.000000Z',
        ]);
        User::create([
            'id' => 40,
            'name' => 'staff',
            'email' => 'staff@domain.com',
            'email_verified_at' => null,
            'password' => '$2y$12$GguwUXUXnp5G3utzzqSwxue50GEiW6j8WHpyo9QMxnGsJbp4boRvG',
            'two_factor_confirmed_at' => null,
            'remember_token' => null,
            'created_at' => '2026-01-07T01:28:55.000000Z',
            'updated_at' => '2026-01-07T01:28:55.000000Z',
        ]);
        User::create([
            'id' => 41,
            'name' => 'Koor jenjang',
            'email' => 'koor.jenjang@domain.com',
            'email_verified_at' => null,
            'password' => '$2y$12$tS3PfbpCAvrtkHgUqHLvaOhH7vaE2m8Sn4GitowbgjOZH3LBlXWHq',
            'two_factor_confirmed_at' => null,
            'remember_token' => null,
            'created_at' => '2026-01-07T03:05:25.000000Z',
            'updated_at' => '2026-01-07T03:05:25.000000Z',
        ]);
        User::create([
            'id' => 43,
            'name' => 'Guru SDI',
            'email' => 'guru.sdi@domain.com',
            'email_verified_at' => null,
            'password' => '$2y$12$Upy5dkBEgnuD12OwxJpru.FyDUj/HMqNiiDbFIOW1a5MfuCttIIWO',
            'two_factor_confirmed_at' => null,
            'remember_token' => null,
            'created_at' => '2026-01-07T04:29:17.000000Z',
            'updated_at' => '2026-01-07T04:29:17.000000Z',
        ]);
        User::create([
            'id' => 46,
            'name' => 'Staff LPI',
            'email' => 'staff.lpi@domain.com',
            'email_verified_at' => null,
            'password' => '$2y$12$EUp2twMJhCJDRJWf/qekhOxRWVg7iKNT5rycNuqVfdLcAO1I9SW/e',
            'two_factor_confirmed_at' => null,
            'remember_token' => null,
            'created_at' => '2026-01-07T09:22:21.000000Z',
            'updated_at' => '2026-01-07T09:22:21.000000Z',
        ]);
        User::create([
            'id' => 47,
            'name' => 'Ketua PSDM',
            'email' => 'ketua.psdm@domain.com',
            'email_verified_at' => null,
            'password' => '$2y$12$TA3GbyKQdRSME74kF6G9Z.s3pvEYwgzBCfjiv/7rqHZJO6DVJOFeO',
            'two_factor_confirmed_at' => null,
            'remember_token' => null,
            'created_at' => '2026-01-08T01:14:53.000000Z',
            'updated_at' => '2026-01-08T06:48:03.000000Z',
        ]);
        User::create([
            'id' => 48,
            'name' => 'Super Admin',
            'email' => 'super.admin@domain.com',
            'email_verified_at' => null,
            'password' => '$2y$12$22q1jjJSDPknulTqlSGV0.w3c7ami/66C4joKFTVuyl8jMp0xWHza',
            'two_factor_confirmed_at' => null,
            'remember_token' => null,
            'created_at' => '2026-01-08T01:17:13.000000Z',
            'updated_at' => '2026-01-08T01:34:19.000000Z',
        ]);
        User::create([
            'id' => 50,
            'name' => 'Admin Unit',
            'email' => 'admin.unit@domain.com',
            'email_verified_at' => null,
            'password' => '$2y$12$RESWCe3LhdwYBP.lGtznROik1nqg6ROKGCy.pS/SNSrljx3ZR03ye',
            'two_factor_confirmed_at' => null,
            'remember_token' => null,
            'created_at' => '2026-01-08T01:46:47.000000Z',
            'updated_at' => '2026-01-08T01:47:34.000000Z',
        ]);

        // Unit
        Unit::create([
            'id' => 1,
            'name' => 'LPI',
            'type' => 'LPI',
            'created_at' => '2025-12-14T07:23:42.000000Z',
            'updated_at' => '2025-12-14T11:37:50.000000Z',
        ]);
        Unit::create([
            'id' => 2,
            'name' => 'SMP IT Al-Huda',
            'type' => 'SMP',
            'created_at' => '2025-12-14T07:23:42.000000Z',
            'updated_at' => '2025-12-14T07:23:42.000000Z',
        ]);
        Unit::create([
            'id' => 4,
            'name' => 'LPI',
            'type' => 'LPI',
            'created_at' => '2025-12-14T11:36:52.000000Z',
            'updated_at' => '2025-12-14T11:36:52.000000Z',
        ]);
        Unit::create([
            'id' => 5,
            'name' => 'TK TAKFIZ',
            'type' => 'TK TAKFIZ',
            'created_at' => '2025-12-14T11:36:52.000000Z',
            'updated_at' => '2025-12-14T11:36:52.000000Z',
        ]);
        Unit::create([
            'id' => 6,
            'name' => 'RUBAT',
            'type' => 'RUBAT',
            'created_at' => '2025-12-14T11:36:52.000000Z',
            'updated_at' => '2025-12-14T11:36:52.000000Z',
        ]);
        Unit::create([
            'id' => 7,
            'name' => 'PAUD',
            'type' => 'PAUD',
            'created_at' => '2025-12-14T11:36:52.000000Z',
            'updated_at' => '2025-12-14T11:36:52.000000Z',
        ]);
        Unit::create([
            'id' => 8,
            'name' => 'TK',
            'type' => 'TK',
            'created_at' => '2025-12-14T11:36:52.000000Z',
            'updated_at' => '2025-12-14T11:36:52.000000Z',
        ]);
        Unit::create([
            'id' => 9,
            'name' => 'TAKFIZ TK',
            'type' => 'TAKFIZ TK',
            'created_at' => '2025-12-14T11:36:52.000000Z',
            'updated_at' => '2025-12-14T11:36:52.000000Z',
        ]);
        Unit::create([
            'id' => 10,
            'name' => 'SDI',
            'type' => 'SDI',
            'created_at' => '2025-12-14T11:36:52.000000Z',
            'updated_at' => '2025-12-14T11:36:52.000000Z',
        ]);
        Unit::create([
            'id' => 11,
            'name' => 'SMP',
            'type' => 'SMP',
            'created_at' => '2025-12-14T11:36:52.000000Z',
            'updated_at' => '2025-12-14T11:36:52.000000Z',
        ]);
        Unit::create([
            'id' => 12,
            'name' => 'TAKFIZ SMP',
            'type' => 'TAKFIZ SMP',
            'created_at' => '2025-12-14T11:36:52.000000Z',
            'updated_at' => '2025-12-14T11:36:52.000000Z',
        ]);
        Unit::create([
            'id' => 13,
            'name' => 'SMA',
            'type' => 'SMA',
            'created_at' => '2025-12-14T11:36:52.000000Z',
            'updated_at' => '2025-12-14T11:36:52.000000Z',
        ]);
        Unit::create([
            'id' => 14,
            'name' => 'TAKFIZ SMA',
            'type' => 'TAKFIZ SMA',
            'created_at' => '2025-12-14T11:36:52.000000Z',
            'updated_at' => '2025-12-14T11:36:52.000000Z',
        ]);
        Unit::create([
            'id' => 15,
            'name' => 'SMK',
            'type' => 'SMK',
            'created_at' => '2025-12-14T11:36:52.000000Z',
            'updated_at' => '2025-12-14T11:36:52.000000Z',
        ]);
        Unit::create([
            'id' => 16,
            'name' => 'TAKFIZ SMK',
            'type' => 'TAKFIZ SMK',
            'created_at' => '2025-12-14T11:36:52.000000Z',
            'updated_at' => '2025-12-14T11:36:52.000000Z',
        ]);
        Unit::create([
            'id' => 17,
            'name' => 'TK PG',
            'type' => 'TK PG',
            'created_at' => '2025-12-14T11:36:52.000000Z',
            'updated_at' => '2025-12-14T11:36:52.000000Z',
        ]);
        Unit::create([
            'id' => 18,
            'name' => 'MI',
            'type' => 'MI',
            'created_at' => '2025-12-14T11:36:52.000000Z',
            'updated_at' => '2025-12-14T11:36:52.000000Z',
        ]);
        Unit::create([
            'id' => 19,
            'name' => 'Mts PG',
            'type' => 'Mts PG',
            'created_at' => '2025-12-14T11:36:52.000000Z',
            'updated_at' => '2025-12-14T11:36:52.000000Z',
        ]);
        Unit::create([
            'id' => 20,
            'name' => 'GIZI LPI',
            'type' => 'GIZI LPI',
            'created_at' => '2025-12-14T11:36:52.000000Z',
            'updated_at' => '2025-12-14T11:36:52.000000Z',
        ]);
        Unit::create([
            'id' => 21,
            'name' => 'AEC',
            'type' => 'AEC',
            'created_at' => '2025-12-14T11:36:52.000000Z',
            'updated_at' => '2025-12-14T11:36:52.000000Z',
        ]);
        Unit::create([
            'id' => 22,
            'name' => 'PENGEMBANGAN',
            'type' => 'PENGEMBANGAN',
            'created_at' => '2025-12-14T11:36:52.000000Z',
            'updated_at' => '2025-12-14T11:36:52.000000Z',
        ]);
        Unit::create([
            'id' => 23,
            'name' => 'TK TAHFIDZ',
            'type' => null,
            'created_at' => '2025-12-15T12:12:14.000000Z',
            'updated_at' => '2025-12-15T12:12:14.000000Z',
        ]);
        Unit::create([
            'id' => 24,
            'name' => 'TAHFIDZ TK',
            'type' => null,
            'created_at' => '2025-12-15T12:12:14.000000Z',
            'updated_at' => '2025-12-15T12:12:14.000000Z',
        ]);
        Unit::create([
            'id' => 25,
            'name' => 'TAHFIDZ SD',
            'type' => null,
            'created_at' => '2025-12-15T12:12:14.000000Z',
            'updated_at' => '2025-12-15T12:12:14.000000Z',
        ]);
        Unit::create([
            'id' => 26,
            'name' => 'TAHFIDZ SMP',
            'type' => null,
            'created_at' => '2025-12-15T12:12:14.000000Z',
            'updated_at' => '2025-12-15T12:12:14.000000Z',
        ]);
        Unit::create([
            'id' => 27,
            'name' => 'TAHFIDZ SMA',
            'type' => null,
            'created_at' => '2025-12-15T12:12:14.000000Z',
            'updated_at' => '2025-12-15T12:12:14.000000Z',
        ]);
        Unit::create([
            'id' => 28,
            'name' => 'TAHFIDZ SMK',
            'type' => null,
            'created_at' => '2025-12-15T12:12:15.000000Z',
            'updated_at' => '2025-12-15T12:12:15.000000Z',
        ]);
        Unit::create([
            'id' => 29,
            'name' => 'GIZI PG',
            'type' => null,
            'created_at' => '2025-12-15T12:12:15.000000Z',
            'updated_at' => '2025-12-15T12:12:15.000000Z',
        ]);

        // DataInduk
        DataInduk::create([
            'id' => 267,
            'user_id' => 39,
            'nip' => null,
            'nama' => 'Kepala Sekolah',
            'jenis_kelamin' => 'Laki-laki',
            'jabatan' => 'Kepala Sekolah',
            'status_kepegawaian' => 'Tetap',
            'created_at' => '2026-01-06T15:39:44.000000Z',
            'updated_at' => '2026-01-06T15:39:45.000000Z',
            'golongan_id' => 19,
            'nik' => null,
            'no_bpjs' => null,
            'no_kjp_2p' => null,
            'no_kjp_3p' => null,
            'no_hp' => null,
            'tempat_lahir' => null,
            'tanggal_lahir' => null,
            'status_perkawinan' => null,
            'suami_istri' => null,
            'alamat' => null,
            'pendidikan' => null,
            'instansi' => null,
            'tmt_awal' => null,
            'tmt_akhir' => null,
            'pindah_tugas' => 'tetap',
            'status' => 'Aktif',
            'keterangan' => null,
        ]);
        DataInduk::create([
            'id' => 268,
            'user_id' => 40,
            'nip' => null,
            'nama' => 'staff',
            'jenis_kelamin' => 'Laki-laki',
            'jabatan' => 'Satpam ',
            'status_kepegawaian' => null,
            'created_at' => '2026-01-07T01:28:55.000000Z',
            'updated_at' => '2026-01-07T01:28:55.000000Z',
            'golongan_id' => 19,
            'nik' => null,
            'no_bpjs' => null,
            'no_kjp_2p' => null,
            'no_kjp_3p' => null,
            'no_hp' => null,
            'tempat_lahir' => null,
            'tanggal_lahir' => null,
            'status_perkawinan' => null,
            'suami_istri' => null,
            'alamat' => null,
            'pendidikan' => null,
            'instansi' => null,
            'tmt_awal' => null,
            'tmt_akhir' => null,
            'pindah_tugas' => 'tetap',
            'status' => 'Aktif',
            'keterangan' => null,
        ]);
        DataInduk::create([
            'id' => 269,
            'user_id' => 41,
            'nip' => null,
            'nama' => 'Koor jenjang',
            'jenis_kelamin' => 'Perempuan',
            'jabatan' => 'Koor Jenjang ',
            'status_kepegawaian' => null,
            'created_at' => '2026-01-07T03:05:25.000000Z',
            'updated_at' => '2026-01-07T03:05:25.000000Z',
            'golongan_id' => 19,
            'nik' => null,
            'no_bpjs' => null,
            'no_kjp_2p' => null,
            'no_kjp_3p' => null,
            'no_hp' => null,
            'tempat_lahir' => null,
            'tanggal_lahir' => null,
            'status_perkawinan' => null,
            'suami_istri' => null,
            'alamat' => null,
            'pendidikan' => null,
            'instansi' => null,
            'tmt_awal' => null,
            'tmt_akhir' => null,
            'pindah_tugas' => 'tetap',
            'status' => 'Aktif',
            'keterangan' => null,
        ]);
        DataInduk::create([
            'id' => 271,
            'user_id' => 43,
            'nip' => null,
            'nama' => 'Guru SDI',
            'jenis_kelamin' => 'Laki-laki',
            'jabatan' => 'Guru SDI',
            'status_kepegawaian' => null,
            'created_at' => '2026-01-07T04:29:17.000000Z',
            'updated_at' => '2026-01-07T09:22:38.000000Z',
            'golongan_id' => 19,
            'nik' => null,
            'no_bpjs' => null,
            'no_kjp_2p' => null,
            'no_kjp_3p' => null,
            'no_hp' => null,
            'tempat_lahir' => null,
            'tanggal_lahir' => null,
            'status_perkawinan' => null,
            'suami_istri' => null,
            'alamat' => null,
            'pendidikan' => null,
            'instansi' => null,
            'tmt_awal' => null,
            'tmt_akhir' => null,
            'pindah_tugas' => 'tetap',
            'status' => 'Aktif',
            'keterangan' => null,
        ]);
        DataInduk::create([
            'id' => 272,
            'user_id' => 46,
            'nip' => null,
            'nama' => 'Staff LPI',
            'jenis_kelamin' => 'Perempuan',
            'jabatan' => 'staff',
            'status_kepegawaian' => null,
            'created_at' => '2026-01-07T09:22:21.000000Z',
            'updated_at' => '2026-01-07T09:23:39.000000Z',
            'golongan_id' => 19,
            'nik' => null,
            'no_bpjs' => null,
            'no_kjp_2p' => null,
            'no_kjp_3p' => null,
            'no_hp' => null,
            'tempat_lahir' => null,
            'tanggal_lahir' => null,
            'status_perkawinan' => null,
            'suami_istri' => null,
            'alamat' => null,
            'pendidikan' => null,
            'instansi' => null,
            'tmt_awal' => null,
            'tmt_akhir' => null,
            'pindah_tugas' => 'tetap',
            'status' => 'Aktif',
            'keterangan' => null,
        ]);
        DataInduk::create([
            'id' => 273,
            'user_id' => 47,
            'nip' => null,
            'nama' => 'Ketua PSDM',
            'jenis_kelamin' => 'Perempuan',
            'jabatan' => 'ketua psdm',
            'status_kepegawaian' => null,
            'created_at' => '2026-01-08T01:14:53.000000Z',
            'updated_at' => '2026-01-08T01:14:53.000000Z',
            'golongan_id' => 19,
            'nik' => null,
            'no_bpjs' => null,
            'no_kjp_2p' => null,
            'no_kjp_3p' => null,
            'no_hp' => null,
            'tempat_lahir' => null,
            'tanggal_lahir' => null,
            'status_perkawinan' => null,
            'suami_istri' => null,
            'alamat' => null,
            'pendidikan' => null,
            'instansi' => null,
            'tmt_awal' => null,
            'tmt_akhir' => null,
            'pindah_tugas' => 'tetap',
            'status' => 'Aktif',
            'keterangan' => null,
        ]);
        DataInduk::create([
            'id' => 274,
            'user_id' => 48,
            'nip' => null,
            'nama' => 'Super Admin',
            'jenis_kelamin' => null,
            'jabatan' => 'Super Admin',
            'status_kepegawaian' => null,
            'created_at' => '2026-01-08T01:17:13.000000Z',
            'updated_at' => '2026-01-08T01:17:13.000000Z',
            'golongan_id' => 19,
            'nik' => null,
            'no_bpjs' => null,
            'no_kjp_2p' => null,
            'no_kjp_3p' => null,
            'no_hp' => null,
            'tempat_lahir' => null,
            'tanggal_lahir' => null,
            'status_perkawinan' => null,
            'suami_istri' => null,
            'alamat' => null,
            'pendidikan' => null,
            'instansi' => null,
            'tmt_awal' => null,
            'tmt_akhir' => null,
            'pindah_tugas' => 'tetap',
            'status' => 'Aktif',
            'keterangan' => null,
        ]);
        DataInduk::create([
            'id' => 275,
            'user_id' => 50,
            'nip' => null,
            'nama' => 'Admin Unit',
            'jenis_kelamin' => null,
            'jabatan' => 'Admin Unit',
            'status_kepegawaian' => null,
            'created_at' => '2026-01-08T01:46:46.000000Z',
            'updated_at' => '2026-01-08T01:46:47.000000Z',
            'golongan_id' => 19,
            'nik' => null,
            'no_bpjs' => null,
            'no_kjp_2p' => null,
            'no_kjp_3p' => null,
            'no_hp' => null,
            'tempat_lahir' => null,
            'tanggal_lahir' => null,
            'status_perkawinan' => null,
            'suami_istri' => null,
            'alamat' => null,
            'pendidikan' => null,
            'instansi' => null,
            'tmt_awal' => null,
            'tmt_akhir' => null,
            'pindah_tugas' => 'tetap',
            'status' => 'Aktif',
            'keterangan' => null,
        ]);

        // Sync Roles
        if ($user = User::find(39)) $user->assignRole('kepala_sekolah');
        if ($user = User::find(40)) $user->assignRole('staff');
        if ($user = User::find(41)) $user->assignRole('koor_jenjang');
        if ($user = User::find(43)) $user->assignRole('staff');
        if ($user = User::find(46)) $user->assignRole('staff');
        if ($user = User::find(47)) $user->assignRole('ketua_psdm');
        if ($user = User::find(48)) $user->assignRole('super_admin');
        if ($user = User::find(50)) $user->assignRole('admin_unit');

        // Sync Employee Units
        if ($emp = DataInduk::find(267)) $emp->units()->sync([10]);
        if ($emp = DataInduk::find(268)) $emp->units()->sync([8]);
        if ($emp = DataInduk::find(269)) $emp->units()->sync([1]);
        if ($emp = DataInduk::find(271)) $emp->units()->sync([10]);
        if ($emp = DataInduk::find(272)) $emp->units()->sync([1]);
        if ($emp = DataInduk::find(275)) $emp->units()->sync([10]);

        // Absensi
        Absensi::create([
            'id' => 7,
            'user_id' => 43,
            'tanggal' => '2026-01-07T00:00:00.000000Z',
            'status' => 'hadir',
            'jam_masuk' => '04:31:49',
            'jam_keluar' => '15:31:51',
            'keterangan' => null,
            'uraian_harian' => '<p></p>',
            'created_at' => '2026-01-07T04:32:09.000000Z',
            'updated_at' => '2026-01-07T04:32:09.000000Z',
        ]);
        Absensi::create([
            'id' => 9,
            'user_id' => 43,
            'tanggal' => '2026-01-08T00:00:00.000000Z',
            'status' => 'hadir',
            'jam_masuk' => '07:27:24',
            'jam_keluar' => '15:27:33',
            'keterangan' => null,
            'uraian_harian' => '<p></p>',
            'created_at' => '2026-01-08T06:27:38.000000Z',
            'updated_at' => '2026-01-08T06:27:38.000000Z',
        ]);
        Absensi::create([
            'id' => 10,
            'user_id' => 48,
            'tanggal' => '2026-01-10T00:00:00.000000Z',
            'status' => 'hadir',
            'jam_masuk' => '08:05:23',
            'jam_keluar' => '16:08:57',
            'keterangan' => null,
            'uraian_harian' => null,
            'created_at' => '2026-01-10T08:05:23.000000Z',
            'updated_at' => '2026-01-10T16:08:57.000000Z',
        ]);
        Absensi::create([
            'id' => 12,
            'user_id' => 46,
            'tanggal' => '2026-01-10T00:00:00.000000Z',
            'status' => 'hadir',
            'jam_masuk' => null,
            'jam_keluar' => null,
            'keterangan' => null,
            'uraian_harian' => '<p></p>',
            'created_at' => '2026-01-10T16:15:15.000000Z',
            'updated_at' => '2026-01-10T16:15:15.000000Z',
        ]);

        // LeaveRequest
        LeaveRequest::create([
            'id' => 6,
            'data_induk_id' => 272,
            'start_date' => '2026-01-12T00:00:00.000000Z',
            'end_date' => '2026-01-19T00:00:00.000000Z',
            'reason' => 'umroh',
            'upload_file' => null,
            'status' => 'pending',
            'note' => null,
            'keterangan_kembali' => 'belum kembali',
            'approved_by' => null,
            'created_at' => '2026-01-07T13:50:21.000000Z',
            'updated_at' => '2026-01-07T13:50:21.000000Z',
        ]);

        // AppraisalSession
        $session = \Modules\PenilaianKinerja\Models\AppraisalSession::create([
            'name' => 'Penilaian Semester Genap 2025/2026',
            'start_date' => now()->startOfMonth(),
            'end_date' => now()->endOfMonth(),
            'is_active' => true,
            'superior_weight' => 50,
            'peer_weight' => 30,
            'self_weight' => 20,
        ]);

        // Sample Assignments for Demo Data
        // Assign to some existing staff for visualization
        $demoEmployees = DataInduk::limit(5)->get();
        foreach ($demoEmployees as $emp) {
            \Modules\PenilaianKinerja\Models\AppraisalAssignment::create([
                'session_id' => $session->id,
                'ratee_id' => $emp->id,
                'rater_id' => 39, // Kepala Sekolah
                'relation_type' => 'superior',
                'status' => $emp->id % 2 == 0 ? 'completed' : 'pending',
            ]);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
