<?php

namespace App\Imports;

use Modules\Kepegawaian\Models\DataInduk;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class DataIndukImport implements ToModel, WithHeadingRow, ShouldQueue, WithChunkReading, WithBatchInserts
{
    public function model(array $row)
    {
        $nik  = trim((string) ($row['nik'] ?? ''));
        $nama = trim((string) ($row['nama'] ?? ''));

        if ($nik === '' || $nama === '') {
            return null; 
        }

        return DataInduk::updateOrCreate(
            ['nik' => $row['nik'] ?? null],
            [
                'nama'              => $row['nama'] ?? null,
                'nik'               => $row['nik'] ?? null,
                'no_hp'             => $row['no_hp'] ?? null,
                'tempat_lahir'      => $row['tempat_lahir'] ?? null,
                'tanggal_lahir'     => $row['tanggal_lahir'] ?? null,
                'pendidikan'        => $row['pendidikan'] ?? null,
                'instansi'          => $row['instansi'] ?? null,
                'status_perkawinan' => $row['status_perkawinan'] ?? null,
                'suami_istri'       => $row['suami_istri'] ?? null,
                'alamat'            => $row['alamat'] ?? null,
                'tmt_awal'          => $row['tmt_awal'] ?? null,
                'status_kepegawaian'=> $row['status_kepegawaian'] ?? null,
            ]
        );
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
