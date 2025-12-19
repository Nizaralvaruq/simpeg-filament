<?php

namespace Modules\Kepegawaian\Exports;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Modules\Kepegawaian\Models\DataInduk;

class DataIndukExport implements WithEvents
{
    protected DataInduk $pegawai;

    public function __construct(DataInduk $pegawai)
    {
        $this->pegawai = $pegawai;
    }

    public function registerEvents(): array
    {
        return [
            BeforeExport::class => function (BeforeExport $event) {

                $spreadsheet = IOFactory::load(
                    storage_path('app/public/template/template-biodata.xlsx')
                );

                $sheet = $spreadsheet->getActiveSheet();
                $p = $this->pegawai;

                // === ISI DATA LANGSUNG KE TEMPLATE ===
                $sheet->setCellValue('C7', $p->id);
                $sheet->setCellValue('C10', $p->nama);
                $sheet->setCellValue(
                    'C11',
                    ($p->tempat_lahir ?? '-') . ', ' . optional($p->tanggal_lahir)->format('d F Y')
                );
                $sheet->setCellValue('C12', $p->nik);
                $sheet->setCellValue('C13', $p->no_hp);
                $sheet->setCellValue('C14', $p->status_perkawinan);
                $sheet->setCellValue('C15', $p->suami_istri ?: '-');
                $sheet->setCellValue('C16', $p->alamat);
                $sheet->setCellValue('C18', $p->email ?: '-');
                $sheet->setCellValue('C19', $p->no_bpjs ?: '-');
                $sheet->setCellValue('C20', $p->no_kjp_2p ?: 'Tidak ikut lembaga');
                $sheet->setCellValue('C21', $p->no_kjp_3p ?: '-');
                $sheet->setCellValue('C24', $p->pendidikan);
                $sheet->setCellValue('C25', $p->jurusan ?: '-');
                $sheet->setCellValue('C26', $p->instansi);
                $sheet->setCellValue('C27', optional($p->tmt_awal)->format('d F Y'));
                $sheet->setCellValue('C28', optional($p->golongan)->name ?: '-');

                // PENTING
                $event->writer->setSpreadsheet($spreadsheet);
            },
        ];
    }
}
