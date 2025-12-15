<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Modules\Kepegawaian\Models\DataInduk;
use Modules\MasterData\Models\Unit;
use Illuminate\Support\Facades\Log;

class PegawaiImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Expected columns: nip, nama, jenjang, jabatan, email (optional)

        $nama = $row['nama'] ?? null;
        if (!$nama) return null; // Skip empty rows

        $nip = $row['nip'] ?? null;

        // Find or create Unit/Jenjang
        $unitIds = [];
        if (!empty($row['jenjang'])) {
            $jenjangNames = explode(',', $row['jenjang']); // Handle multiple: "SD, SMP"
            foreach ($jenjangNames as $name) {
                $name = trim($name);
                $unit = Unit::firstOrCreate(['name' => $name]);
                $unitIds[] = $unit->id;
            }
        }

        $employee = DataInduk::updateOrCreate(
            ['nip' => $nip], // Unique key
            [
                'nama' => $nama,
                'jabatan' => $row['jabatan'] ?? null,
                // Add other fields as needed
            ]
        );

        // Sync Units/Jenjang
        if (!empty($unitIds)) {
            $employee->units()->syncWithoutDetaching($unitIds);
        }

        return $employee;
    }
}
