<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class PegawaiTemplateExport implements WithHeadings, FromCollection
{
    public function headings(): array
    {
        return [
            'nip',
            'nama',
            'jabatan',
            'jenjang', // Separated by comma if multiple
            'email',   // Optional for creating user
        ];
    }

    public function collection()
    {
        return collect([
            [
                '123456789',
                'Contoh Nama Pegawai',
                'Guru',
                'SD, SMP',
                'contoh@sekolah.id',
            ]
        ]);
    }
}
