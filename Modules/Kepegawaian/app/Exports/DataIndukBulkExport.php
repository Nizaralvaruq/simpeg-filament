<?php

namespace Modules\Kepegawaian\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Database\Eloquent\Collection;

class DataIndukBulkExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected Collection $records;

    public function __construct(Collection $records)
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
            'ID',
            'NIP',
            'Nama',
            'Jenis Kelamin',
            'Unit Kerja',
            'Jabatan',
            'Golongan',
            'Status Kepegawaian',
            'Status Aktif',
            'Email',
            'No. HP',
            'Pendidikan',
        ];
    }

    public function map($p): array
    {
        return [
            $p->id,
            $p->nip,
            $p->nama,
            $p->jenis_kelamin,
            $p->units->pluck('name')->join(', '),
            $p->jabatan,
            optional($p->golongan)->name,
            $p->status_kepegawaian,
            $p->status,
            $p->user?->email,
            $p->no_hp,
            $p->pendidikan,
        ];
    }
}
