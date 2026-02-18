<?php

namespace App\Imports;

use Modules\Kepegawaian\Models\DataInduk;
use Modules\MasterData\Models\Golongan;
use Modules\MasterData\Models\Unit;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Log;

class DataIndukImport implements OnEachRow, WithHeadingRow, WithMapping, WithValidation
{
    public function map($row): array
    {
        // Parse dates from Excel
        $row['tanggal_lahir'] = $this->transformDate($row['tanggal_lahir'] ?? null);
        $row['tmt_awal']      = $this->transformDate($row['tmt_awal'] ?? null);
        $row['tmt_akhir']     = $this->transformDate($row['tmt_akhir'] ?? null);

        return $row;
    }

    public function rules(): array
    {
        return [
            'nik' => 'required',
            'nama' => 'required|string',
            'nip' => 'nullable',
            'email' => 'nullable|email',
        ];
    }

    public function onRow(Row $row)
    {
        $row = $row->toArray();
        $nik  = trim((string) ($row['nik'] ?? ''));
        $nama = trim((string) ($row['nama'] ?? ''));

        if ($nik === '' || $nama === '') {
            return null;
        }

        // Lookup Golongan ID by Name - EXACT MATCH
        $golonganId = null;
        if (!empty($row['golongan'])) {
            $golonganName = trim($row['golongan']);
            $golongan = Golongan::where('name', $golonganName)->first();
            $golonganId = $golongan?->id;

            if (!$golongan) {
                Log::warning("Import DataInduk: Golongan '{$golonganName}' tidak ditemukan untuk NIK {$nik}");
            }
        }

        /** @var DataInduk $dataInduk */
        $dataInduk = DataInduk::updateOrCreate(
            ['nik' => $nik],
            [
                'nama'              => $nama,
                'jenis_kelamin'     => $row['jenis_kelamin'] ?? null,
                'nip'               => $row['nip'] ?? null,
                'no_hp'             => $row['no_hp'] ?? null,
                'tempat_lahir'      => $row['tempat_lahir'] ?? null,
                'tanggal_lahir'     => $row['tanggal_lahir'] ?? null,
                'pendidikan'        => $row['pendidikan'] ?? null,
                'instansi'          => $row['instansi'] ?? null,
                'status_perkawinan' => $row['status_perkawinan'] ?? null,
                'suami_istri'       => $row['suami_istri'] ?? null,
                'agama'             => $row['agama'] ?? null,
                'golongan_darah'    => $row['golongan_darah'] ?? null,
                'alamat'            => $row['alamat'] ?? null,
                'alamat_domisili'   => $row['alamat_domisili'] ?? null,
                'jarak_ke_kantor'   => $row['jarak_ke_kantor'] ?? null,
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

        // Handle User Creation if email is provided and doesn't exist on employee
        if (!empty($row['email']) && !$dataInduk->user_id) {
            $user = User::where('email', $row['email'])->first();

            if (!$user) {
                $user = User::create([
                    'name' => $nama,
                    'email' => $row['email'],
                    'password' => Hash::make('password123'), // Default password
                ]);
                $user->assignRole('staff');
                Log::info("Import DataInduk: Akun baru dibuat untuk {$nama} ({$row['email']})");
            }

            $dataInduk->update(['user_id' => $user->id]);
        }

        // Sync Units if provided - EXACT MATCH
        if (!empty($row['unit_kerja'])) {
            $unitNames = explode(',', $row['unit_kerja']);
            $unitIds = [];
            foreach ($unitNames as $name) {
                $trimmedName = trim($name);
                $unit = Unit::where('name', $trimmedName)->first();
                if ($unit) {
                    $unitIds[] = $unit->id;
                } else {
                    Log::warning("Import DataInduk: Unit '{$trimmedName}' tidak ditemukan untuk NIK {$nik}");
                }
            }
            if (!empty($unitIds)) {
                $dataInduk->units()->sync($unitIds);
            }
        }

        Log::info("Import DataInduk: Berhasil memproses {$nama} (NIK: {$nik})");
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
}
