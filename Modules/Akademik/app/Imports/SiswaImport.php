<?php

namespace Modules\Akademik\Imports;

use Modules\Akademik\Models\Siswa;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Row;
use Illuminate\Support\Facades\Log;

class SiswaImport implements
    OnEachRow,
    WithHeadingRow,
    SkipsEmptyRows
{
    public int $importedCount = 0;
    public int $skippedCount  = 0;

    public function onRow(Row $row)
    {
        $cells = $row->toArray();

        // --- Normalize all keys: trim whitespace, lowercase, replace spaces with underscores ---
        $cells = collect($cells)
            ->mapWithKeys(fn($value, $key) => [
                strtolower(trim(preg_replace('/\s+/', '_', (string) $key))) => $value
            ])
            ->toArray();

        // --- Try multiple possible column name aliases ---
        $nis  = $this->resolveField($cells, ['nis', 'nomor_induk', 'no_induk', 'nisn']);
        $nama = $this->resolveField($cells, ['nama_lengkap', 'nama', 'name', 'nama_siswa']);

        // Skip row if required fields are empty
        if (empty($nis) || empty($nama)) {
            $this->skippedCount++;
            return;
        }

        // NIS: force to string, remove decimals if Excel stored as float (e.g. "123456.0")
        $nis = (string) $nis;
        $nis = preg_replace('/\.0+$/', '', $nis);
        $nis = trim($nis);

        if (empty($nis)) {
            $this->skippedCount++;
            return;
        }

        $kelas = $this->resolveField($cells, ['kelas', 'class', 'tingkat_kelas']);
        $kelas = $kelas ? trim((string) $kelas) : null;

        // WA: normalize
        $wa = $this->resolveField($cells, ['nomor_wa_ortu', 'wa_ortu', 'wa', 'nomor_hp', 'hp_ortu', 'no_wa']);
        $wa = $this->normalizePhone($wa);

        // is_active: default true
        $isActiveRaw = $this->resolveField($cells, ['is_active', 'aktif', 'status', 'active']);
        $isActive = true;
        if ($isActiveRaw !== null && $isActiveRaw !== '') {
            $isActive = in_array(strtolower(trim((string) $isActiveRaw)), ['1', 'true', 'aktif', 'ya', 'yes', 'y'], true);
        }

        try {
            Siswa::updateOrCreate(
                ['nis' => $nis],
                [
                    'nama_lengkap'  => trim((string) $nama),
                    'kelas'         => $kelas,
                    'nomor_wa_ortu' => $wa,
                    'is_active'     => $isActive,
                ]
            );
            $this->importedCount++;
        } catch (\Throwable $e) {
            Log::error('[SiswaImport] Error pada baris ' . $row->getIndex() . ': ' . $e->getMessage());
            $this->skippedCount++;
        }
    }

    /**
     * Try multiple possible field name aliases, return first non-null found.
     */
    private function resolveField(array $row, array $aliases): mixed
    {
        foreach ($aliases as $key) {
            if (isset($row[$key]) && $row[$key] !== '' && $row[$key] !== null) {
                return $row[$key];
            }
        }
        return null;
    }

    /**
     * Normalize phone number: strip non-digits, ensure starts with 62.
     */
    private function normalizePhone(mixed $raw): ?string
    {
        if ($raw === null || $raw === '') return null;

        $phone = (string) $raw;

        // Remove .0 if Excel treated it as float (e.g. 628123.0)
        $phone = preg_replace('/\.0+$/', '', $phone);

        // Remove non-digit characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (empty($phone)) return null;

        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        } elseif (str_starts_with($phone, '628')) {
            // already correct
        } elseif (str_starts_with($phone, '62')) {
            // already correct
        } elseif (str_starts_with($phone, '8')) {
            $phone = '62' . $phone;
        }

        return $phone;
    }
}
