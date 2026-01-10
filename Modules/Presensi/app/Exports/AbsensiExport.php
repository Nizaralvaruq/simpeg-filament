<?php

namespace Modules\Presensi\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Collection;

class AbsensiExport implements FromCollection, WithHeadings, WithMapping
{
    protected $records;

    public function __construct($records)
    {
        $this->records = $records;
    }

    public function collection()
    {
        return $this->records;
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Nama Pegawai',
            'Unit',
            'Status',
            'Jam Masuk',
            'Jam Keluar',
            'Keterangan',
        ];
    }

    public function map($record): array
    {
        return [
            $record->tanggal->format('d/m/Y'),
            $record->user->name ?? '-',
            $record->user->employee->units->pluck('name')->join(', ') ?? '-',
            ucfirst($record->status),
            $record->jam_masuk,
            $record->jam_keluar,
            $record->keterangan,
        ];
    }
}
