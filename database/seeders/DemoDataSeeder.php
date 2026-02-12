<?php

namespace Database\Seeders;

use App\Models\User;
use Modules\MasterData\Models\Unit;
use Modules\Kepegawaian\Models\DataInduk;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan unit SMP sudah ada
        $unitSMP = Unit::firstOrCreate(
            ['name' => 'SMP']
        );

        // 1. Super Admin
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $superAdmin->assignRole('super_admin');

        DataInduk::firstOrCreate(
            ['user_id' => $superAdmin->id],
            [
                'nip' => '199001012015011001',
                'nama' => 'Super Administrator',
                'jenis_kelamin' => 'Laki-laki',
            ]
        );

        // 2. Ketua PSDM
        $ketuaPSDM = User::firstOrCreate(
            ['email' => 'psdm@example.com'],
            [
                'name' => 'Ketua PSDM',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $ketuaPSDM->assignRole('ketua_psdm');

        DataInduk::firstOrCreate(
            ['user_id' => $ketuaPSDM->id],
            [
                'nip' => '198505102010011002',
                'nama' => 'Dr. Ahmad Fauzi, M.Pd',
                'jenis_kelamin' => 'Laki-laki',
            ]
        );

        // 3. Kepala Sekolah SMP
        $kepalaSekolah = User::firstOrCreate(
            ['email' => 'kepsek.smp@example.com'],
            [
                'name' => 'Kepala Sekolah SMP',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $kepalaSekolah->assignRole('kepala_sekolah');

        $kepsekData = DataInduk::firstOrCreate(
            ['user_id' => $kepalaSekolah->id],
            [
                'nip' => '197803152005011001',
                'nama' => 'Drs. Bambang Suryanto, M.Pd',
                'jenis_kelamin' => 'Laki-laki',
            ]
        );
        $kepsekData->units()->sync([$unitSMP->id]);

        // 4. Koor Jenjang SMP
        $koorJenjang = User::firstOrCreate(
            ['email' => 'koor.smp@example.com'],
            [
                'name' => 'Koordinator Jenjang SMP',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $koorJenjang->assignRole('koor_jenjang');

        $koorData = DataInduk::firstOrCreate(
            ['user_id' => $koorJenjang->id],
            [
                'nip' => '198207202008012001',
                'nama' => 'Siti Nurhaliza, S.Pd, M.Pd',
                'jenis_kelamin' => 'Perempuan',
            ]
        );
        $koorData->units()->sync([$unitSMP->id]);

        // 5. Admin Unit SMP
        $adminUnit = User::firstOrCreate(
            ['email' => 'admin.smp@example.com'],
            [
                'name' => 'Admin Unit SMP',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $adminUnit->assignRole('admin_unit');

        $adminData = DataInduk::firstOrCreate(
            ['user_id' => $adminUnit->id],
            [
                'nip' => '199012152015012001',
                'nama' => 'Rina Wati, S.Kom',
                'jenis_kelamin' => 'Perempuan',
            ]
        );
        $adminData->units()->sync([$unitSMP->id]);

        // 6-8. Guru SMP (3 orang)
        $guruData = [
            [
                'email' => 'guru1.smp@example.com',
                'name' => 'Guru Matematika SMP',
                'nip' => '198506102010012001',
                'nama' => 'Budi Santoso, S.Pd',
                'jenis_kelamin' => 'Laki-laki',
            ],
            [
                'email' => 'guru2.smp@example.com',
                'name' => 'Guru Bahasa Indonesia SMP',
                'nip' => '198709152012012002',
                'nama' => 'Dewi Lestari, S.Pd',
                'jenis_kelamin' => 'Perempuan',
            ],
            [
                'email' => 'guru3.smp@example.com',
                'name' => 'Guru IPA SMP',
                'nip' => '199001202015011003',
                'nama' => 'Andi Wijaya, S.Si',
                'jenis_kelamin' => 'Laki-laki',
            ],
        ];

        foreach ($guruData as $guru) {
            $user = User::firstOrCreate(
                ['email' => $guru['email']],
                [
                    'name' => $guru['name'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );
            $user->assignRole('staff');

            $dataInduk = DataInduk::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'nip' => $guru['nip'],
                    'nama' => $guru['nama'],
                    'jenis_kelamin' => $guru['jenis_kelamin'],
                ]
            );
            $dataInduk->units()->sync([$unitSMP->id]);
        }

        $this->command->info('✅ Demo data seeder completed successfully!');
        $this->command->info('📧 All users password: password');
        $this->command->info('👥 Created:');
        $this->command->info('   - 1 Super Admin (superadmin@example.com)');
        $this->command->info('   - 1 Ketua PSDM (psdm@example.com)');
        $this->command->info('   - 1 Kepala Sekolah SMP (kepsek.smp@example.com)');
        $this->command->info('   - 1 Koordinator Jenjang SMP (koor.smp@example.com)');
        $this->command->info('   - 1 Admin Unit SMP (admin.smp@example.com)');
        $this->command->info('   - 3 Guru SMP (guru1-3.smp@example.com)');
    }
}
