<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class PegawaiTemplateExport implements WithHeadings, FromCollection
{
    public function headings(): array
    {
        return [
            'nik',
            'nama',
            'jenis_kelamin',
            'nip',
            'no_hp',
            'tempat_lahir',
            'tanggal_lahir',
            'agama',
            'golongan_darah',
            'pendidikan',
            'instansi',
            'status_perkawinan',
            'suami_istri',
            'alamat',
            'alamat_domisili',
            'jarak_ke_kantor',
            'jabatan',
            'golongan',
            'tmt_awal',
            'status_kepegawaian',
            'no_bpjs',
            'no_kjp_2p',
            'no_kjp_3p',
            'unit_kerja',
            'email',
        ];
    }

    public function collection()
    {
        return collect([
            [
                '3171010101010001',
                'Budi Santoso',
                'Laki-laki',
                'NPA-001',
                '08123456789',
                'Jakarta',
                '1990-01-01',
                'Islam',
                'O',
                'S1',
                'Universitas Indonesia',
                'Menikah',
                'Siti Aminah',
                'Jl. Merdeka No. 1',
                'Jl. Keadilan No. 2',
                '5.5',
                'Guru Dasar',
                'IV/a',
                '2020-01-01',
                'Tetap',
                '00012345678',
                'KJP-2P-001',
                'KJP-3P-001',
                'SD, SMP',
                'budi.santoso@sekolah.id',
            ]
        ]);
    }
}
