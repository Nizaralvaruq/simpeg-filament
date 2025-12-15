<?php

namespace Modules\Pegawai\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Pegawai\Models\Unit;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            'LPI',
            'TK TAKFIZ',
            'RUBAT',
            'PAUD',
            'TK',
            'TAKFIZ TK',
            'SDI',
            'SMP',
            'TAKFIZ SMP',
            'SMA',
            'TAKFIZ SMA',
            'SMK',
            'TAKFIZ SMK',
            'TK PG',
            'MI',
            'Mts PG',
            'GIZI LPI',
            'AEC',
            'PENGEMBANGAN'
        ];

        foreach ($units as $unitName) {
            // We use the name as the type as well for simplicity, 
            // as the user's list seems to be a mix of types and names.
            Unit::firstOrCreate(
                ['name' => $unitName],
                ['type' => $unitName]
            );
        }
    }
}
