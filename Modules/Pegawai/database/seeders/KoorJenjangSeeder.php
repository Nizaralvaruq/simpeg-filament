<?php

namespace Modules\Pegawai\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Modules\Pegawai\Models\DataInduk;
use Modules\Pegawai\Models\Unit;

class KoorJenjangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Role
        Role::firstOrCreate(['name' => 'koor_jenjang', 'guard_name' => 'web']);

        // 2. Create Dummy User for Koor Jenjang (Optional, for testing)
        // We'll attach it to the first available Unit (e.g., LPI or SD)
        $unit = Unit::first();

        if ($unit) {
            $user = User::firstOrCreate(
                ['email' => 'koor@admin.com'],
                ['name' => 'Koor Jenjang Tester', 'password' => bcrypt('password')]
            );

            $user->assignRole('koor_jenjang');

            // Create Data Induk for him so he is attached to a Unit
            $employee = DataInduk::firstOrCreate(
                ['user_id' => $user->id],
                ['nama' => 'Pak Koor', 'nip' => 'K002', 'jabatan' => 'Koordinator', 'status_kepegawaian' => 'Tetap']
            );

            // Link to the Unit
            $employee->units()->syncWithoutDetaching([$unit->id]);
        }
    }
}
