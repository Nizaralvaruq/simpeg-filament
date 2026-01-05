<?php

namespace App\Imports;

use Modules\Kepegawaian\Models\DataInduk;
use Modules\MasterData\Models\Golongan;
use Modules\MasterData\Models\Unit;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class DataIndukImport implements ToModel, WithHeadingRow, ShouldQueue, WithChunkReading, WithBatchInserts, WithMapping
{
    public function map($row): array
    {
        // Parse dates from Excel
        $row['tanggal_lahir'] = $this->transformDate($row['tanggal_lahir'] ?? null);
        $row['tmt_awal']      = $this->transformDate($row['tmt_awal'] ?? null);
        $row['tmt_akhir']     = $this->transformDate($row['tmt_akhir'] ?? null);

        return $row;
    }

    public function model(array $row)
    {
        $nik  = trim((string) ($row['nik'] ?? ''));
        $nama = trim((string) ($row['nama'] ?? ''));

        if ($nik === '' || $nama === '') {
            return null;
        }

        // Lookup Golongan ID by Name
        $golonganId = null;
        if (!empty($row['golongan'])) {
            $golongan = Golongan::where('name', 'like', '%' . trim($row['golongan']) . '%')->first();
            $golonganId = $golongan?->id;
        }

        /** @var DataInduk $dataInduk */
        $dataInduk = DataInduk::updateOrCreate(
            ['nik' => $nik],
            [
                'nama'              => $nama,
                'no_hp'             => $row['no_hp'] ?? null,
                'tempat_lahir'      => $row['tempat_lahir'] ?? null,
                'tanggal_lahir'     => $row['tanggal_lahir'] ?? null,
                'pendidikan'        => $row['pendidikan'] ?? null,
                'jurusan'           => $row['jurusan'] ?? null,
                'instansi'          => $row['instansi'] ?? null,
                'status_perkawinan' => $row['status_perkawinan'] ?? null,
                'suami_istri'       => $row['suami_istri'] ?? null,
                'alamat'            => $row['alamat'] ?? null,
                'jabatan'           => $row['jabatan'] ?? null,
                'golongan_id'       => $golonganId,
                'tmt_awal'          => $row['tmt_awal'] ?? null,
                'status_kepegawaian' => $row['status_kepegawaian'] ?? null,
                'no_bpjs'           => $row['no_bpjs'] ?? null,
                'no_kjp_2p'         => $row['no_kjp_2p'] ?? null,
                'no_kjp_3p'         => $row['no_kjp_3p'] ?? null,
                'status'            => 'Aktif',
                'pindah_tugas'      => 'tetap',
            ]
        );

        // Sync Units if provided
        if (!empty($row['unit_kerja'])) {
            $unitNames = explode(',', $row['unit_kerja']);
            $unitIds = [];
            foreach ($unitNames as $name) {
                $unit = Unit::where('name', 'like', '%' . trim($name) . '%')->first();
                if ($unit) {
                    $unitIds[] = $unit->id;
                }
            }
            if (!empty($unitIds)) {
                $dataInduk->units()->sync($unitIds);
            }
        }

        return $dataInduk;
    }

    private function transformDate($value)
    {
        if (empty($value)) {
            return null;
        }

        try {
            if (is_numeric($value)) {
                return Date::excelToDateTimeObject($value);
            }
            return \Carbon\Carbon::parse($value);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function chunkSize(): int
    {
        return 500;
    }

    public function batchSize(): int
    {
        return 500;
    }
}
