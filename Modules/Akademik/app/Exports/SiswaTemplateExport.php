<?php

namespace Modules\Akademik\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SiswaTemplateExport implements WithHeadings, FromCollection, ShouldAutoSize
{
    public function headings(): array
    {
        return [
            'nis',
            'nama_lengkap',
            'kelas',
            'nomor_wa_ortu',
        ];
    }

    public function collection()
    {
        return collect([
            [
                '123456',
                'Fulan bin Fulan',
                '10A',
                '81234567890',
            ],
            [
                '234567',
                'Budi Santoso',
                '10B',
                '82345678901',
            ],
        ]);
    }
}
