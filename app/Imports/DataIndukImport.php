<?php

namespace App\Imports;

use Modules\Kepegawaian\Models\DataInduk;
use Modules\MasterData\Models\Golongan;
use Modules\MasterData\Models\Unit;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

class DataIndukImport implements OnEachRow, WithHeadingRow, WithMapping, WithValidation, WithChunkReading
{
    protected Collection $golongans;
    protected Collection $units;

    public function __construct()
    {
        // Pre-cache all Golongans and Units to avoid repeated DB queries
        $this->golongans = Golongan::all()->keyBy('name');
        $this->units = Unit::all()->keyBy('name');
    }

    public function map($row): array
    {
        // Parse dates from Excel
        $row['tanggal_lahir'] = $this->transformDate($row['tanggal_lahir'] ?? null);
        $row['tmt_awal'] = $this->transformDate($row['tmt_awal'] ?? null);
        $row['tmt_akhir'] = $this->transformDate($row['tmt_akhir'] ?? null);

        return $row;
    }

    public function rules(): array
    {
        return [
            'nik' => 'required',
            'nama' => 'required|string',
            'nip' => 'required',
            'email' => 'nullable|email',
        ];
    }

    public function onRow(Row $row)
    {
        DB::transaction(function () use ($row) {
            $row = $row->toArray();
            $nik = trim((string) ($row['nik'] ?? ''));
            $nama = trim((string) ($row['nama'] ?? ''));
            $nip = trim((string) ($row['nip'] ?? ''));

            if ($nik === '' || $nama === '' || $nip === '') {
                return;
            }

            // Lookup Golongan from Cache
            $golonganName = trim($row['golongan'] ?? '');
            $golonganId = $this->golongans->get($golonganName)?->id;

            if (!empty($golonganName) && !$golonganId) {
                Log::warning("Import DataInduk: Golongan '{$golonganName}' tidak ditemukan untuk NIK {$nik}");
            }

            /** @var DataInduk $dataInduk */
            $dataInduk = DataInduk::updateOrCreate(
                ['nik' => $nik],
                [
                    'nama' => $nama,
                    'jenis_kelamin' => $row['jenis_kelamin'] ?? null,
                    'nip' => $row['nip'] ?? null,
                    'no_hp' => $row['no_hp'] ?? null,
                    'tempat_lahir' => $row['tempat_lahir'] ?? null,
                    'tanggal_lahir' => $row['tanggal_lahir'] ?? null,
                    'pendidikan' => $row['pendidikan'] ?? null,
                    'instansi' => $row['instansi'] ?? null,
                    'status_perkawinan' => $row['status_perkawinan'] ?? null,
                    'suami_istri' => $row['suami_istri'] ?? null,
                    'agama' => $row['agama'] ?? null,
                    'golongan_darah' => $row['golongan_darah'] ?? null,
                    'alamat' => $row['alamat'] ?? null,
                    'alamat_domisili' => $row['alamat_domisili'] ?? null,
                    'jarak_ke_kantor' => $row['jarak_ke_kantor'] ?? null,
                    'jabatan' => $row['jabatan'] ?? null,
                    'golongan_id' => $golonganId,
                    'tmt_awal' => $row['tmt_awal'] ?? null,
                    'status_kepegawaian' => $row['status_kepegawaian'] ?? null,
                    'no_bpjs' => $row['no_bpjs'] ?? null,
                    'no_kjp_2p' => $row['no_kjp_2p'] ?? null,
                    'no_kjp_3p' => $row['no_kjp_3p'] ?? null,
                    'status' => 'Aktif',
                    'pindah_tugas' => 'tetap',
                ]
            );

            // Handle User separately to minimize queries
            $email = !empty($row['email']) ? trim($row['email']) : ($dataInduk->nip ? trim($dataInduk->nip) . '@insan.id' : null);

            if ($email && !$dataInduk->user_id) {
                $user = User::firstOrCreate(
                    ['email' => $email],
                    [
                        'name' => $nama,
                        'password' => Hash::make('password'),
                    ]
                );

                if ($user->wasRecentlyCreated) {
                    $user->assignRole('staff');
                    Log::info("Import DataInduk: Akun baru dibuat untuk {$nama} ({$email})");
                }

                $dataInduk->update(['user_id' => $user->id]);
            }

            // Sync Units from Cache
            if (!empty($row['unit_kerja'])) {
                $unitNames = explode(',', $row['unit_kerja']);
                $unitIds = [];
                foreach ($unitNames as $uName) {
                    $trimmedName = trim($uName);
                    if ($unit = $this->units->get($trimmedName)) {
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
        });
    }

    public function chunkSize(): int
    {
        return 100;
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
