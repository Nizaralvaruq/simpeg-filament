<?php

namespace Modules\Presensi\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Modules\Presensi\Models\AbsensiKegiatan;
use Illuminate\Support\Collection;

class AbsensiKegiatanExport implements FromCollection, WithHeadings, WithMapping
{
    protected $kegiatanId;

    public function __construct($kegiatanId)
    {
        $this->kegiatanId = $kegiatanId;
    }

    public function collection()
    {
        return AbsensiKegiatan::with('user')
            ->where('kegiatan_id', $this->kegiatanId)
            ->get();
    }

    public function headings(): array
    {
        return [
            'Nama Pegawai',
            'Jam Absen',
            'Status',
            'Keterangan',
        ];
    }

    public function map($row): array
    {
        return [
            $row->user->name ?? '-',
            $row->jam_absen,
            $row->status,
            $row->keterangan,
        ];
    }
}
