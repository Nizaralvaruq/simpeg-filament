<?php

namespace Modules\Kepegawaian\Exports;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeWriting;
use Maatwebsite\Excel\Files\LocalTemporaryFile;
use Maatwebsite\Excel\Excel;
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
            BeforeWriting::class => function (BeforeWriting $event) {
                $templatePath = storage_path('app/public/template/template-biodata.xlsx');
                $p = $this->pegawai;

                if (file_exists($templatePath)) {
                    // Pakai Reopen agar template terbaca oleh writer
                    $event->writer->reopen(new LocalTemporaryFile($templatePath), Excel::XLSX);
                    $sheet = $event->writer->getDelegate()->getActiveSheet();

                    // === ISI DATA LANGSUNG KE TEMPLATE ASLI ===
                    $sheet->setCellValue('C7', $p->id);
                    $sheet->setCellValue('C10', $p->nama);
                    $sheet->setCellValue(
                        'C11',
                        ($p->tempat_lahir ?? '-') . ', ' . ($p->tanggal_lahir instanceof \Carbon\Carbon ? $p->tanggal_lahir->format('d F Y') : '-')
                    );
                    $sheet->setCellValue('C12', $p->nik);
                    $sheet->setCellValue('C13', $p->no_hp);
                    $sheet->setCellValue('C14', $p->status_perkawinan);
                    $sheet->setCellValue('C15', $p->suami_istri ?: '-');
                    $sheet->setCellValue('C16', $p->alamat);
                    $sheet->setCellValue('C17', $p->alamat_domisili ?: '-');
                    $sheet->setCellValue('C18', $p->email ?: '-');
                    $sheet->setCellValue('C19', $p->no_bpjs ?: '-');
                    $sheet->setCellValue('C20', $p->no_kjp_2p ?: 'Tidak ikut lembaga');
                    $sheet->setCellValue('C21', $p->no_kjp_3p ?: '-');
                    $sheet->setCellValue('C24', $p->pendidikan);
                    $sheet->setCellValue('C25', $p->jurusan ?: '-');
                    $sheet->setCellValue('C26', $p->instansi);
                    $sheet->setCellValue('C27', $p->tmt_awal instanceof \Carbon\Carbon ? $p->tmt_awal->format('d F Y') : '-');
                    $sheet->setCellValue('C28', optional($p->golongan)->name ?: '-');
                } else {
                    // FALLBACK: Jika template tidak ada, buat tampilan menyeluruh
                    $sheet = $event->writer->getDelegate()->getActiveSheet();
                    $sheet->setTitle('Biodata Pegawai');

                    // Set basic headers for fallback
                    $sheet->setCellValue('B2', 'BIODATA PEGAWAI');
                    $sheet->getStyle('B2')->getFont()->setBold(true)->setSize(16);

                    // --- DATA PERSONAL ---
                    $sheet->setCellValue('B4', 'DATA PERSONAL');
                    $sheet->getStyle('B4')->getFont()->setBold(true);

                    $sheet->setCellValue('B5', 'ID Pegawai');
                    $sheet->setCellValue('C5', $p->id);
                    $sheet->setCellValue('B6', 'Nama Lengkap');
                    $sheet->setCellValue('C6', $p->nama);
                    $sheet->setCellValue('B7', 'NIK');
                    $sheet->setCellValue('C7', $p->nik ?: '-');
                    $sheet->setCellValue('B8', 'Tempat, Tgl Lahir');
                    $sheet->setCellValue('C8', ($p->tempat_lahir ?? '-') . ', ' . ($p->tanggal_lahir instanceof \Carbon\Carbon ? $p->tanggal_lahir->format('d F Y') : '-'));
                    $sheet->setCellValue('B9', 'Agama');
                    $sheet->setCellValue('C9', $p->agama ?: '-');
                    $sheet->setCellValue('B10', 'Golongan Darah');
                    $sheet->setCellValue('C10', $p->golongan_darah ?: '-');
                    $sheet->setCellValue('B11', 'No. HP');
                    $sheet->setCellValue('C11', $p->no_hp ?: '-');
                    $sheet->setCellValue('B12', 'Email');
                    $sheet->setCellValue('C12', $p->email ?: '-');
                    $sheet->setCellValue('B13', 'Alamat KTP');
                    $sheet->setCellValue('C13', $p->alamat ?: '-');
                    $sheet->setCellValue('B14', 'Alamat Domisili');
                    $sheet->setCellValue('C14', $p->alamat_domisili ?: '-');
                    $sheet->setCellValue('B15', 'Jarak ke Kantor');
                    $sheet->setCellValue('C15', ($p->jarak_ke_kantor ?? '0') . ' KM');
                    $sheet->setCellValue('B16', 'Status Nikah');
                    $sheet->setCellValue('C16', $p->status_perkawinan ?: '-');

                    // --- DATA KEPEGAWAIAN ---
                    $sheet->setCellValue('B18', 'DATA KEPEGAWAIAN');
                    $sheet->getStyle('B18')->getFont()->setBold(true);

                    $sheet->setCellValue('B19', 'Unit Kerja');
                    $sheet->setCellValue('C19', $p->units->pluck('name')->join(', ') ?: '-');
                    $sheet->setCellValue('B20', 'Amanah/Jabatan');
                    $sheet->setCellValue('C20', $p->jabatan ?: '-');
                    $sheet->setCellValue('B21', 'Golongan');
                    $sheet->setCellValue('C21', optional($p->golongan)->name ?: '-');
                    $sheet->setCellValue('B22', 'TMT Awal');
                    $sheet->setCellValue('C22', $p->tmt_awal instanceof \Carbon\Carbon ? $p->tmt_awal->format('d F Y') : '-');
                    $sheet->setCellValue('B23', 'Status Kerja');
                    $sheet->setCellValue('C23', $p->status_kepegawaian ?: '-');

                    // --- DATA BPJS & KJP ---
                    $sheet->setCellValue('B25', 'JAMINAN SOSIAL');
                    $sheet->getStyle('B25')->getFont()->setBold(true);

                    $sheet->setCellValue('B26', 'No. BPJS');
                    $sheet->setCellValue('C26', $p->no_bpjs ?: '-');
                    $sheet->setCellValue('B27', 'No. KJP 2P');
                    $sheet->setCellValue('C27', $p->no_kjp_2p ?: '-');
                    $sheet->setCellValue('B28', 'No. KJP 3P');
                    $sheet->setCellValue('C28', $p->no_kjp_3p ?: '-');

                    // --- DATA PENDIDIKAN ---
                    $sheet->setCellValue('B30', 'PENDIDIKAN TERAKHIR');
                    $sheet->getStyle('B30')->getFont()->setBold(true);

                    $sheet->setCellValue('B31', 'Pendidikan');
                    $sheet->setCellValue('C31', $p->pendidikan ?: '-');
                    $sheet->setCellValue('B32', 'Jurusan');
                    $sheet->setCellValue('C32', $p->jurusan ?: '-');
                    $sheet->setCellValue('B33', 'Instansi');
                    $sheet->setCellValue('C33', $p->instansi ?: '-');

                    // Auto-size columns
                    foreach (range('B', 'C') as $columnID) {
                        $sheet->getColumnDimension($columnID)->setAutoSize(true);
                    }
                }
            },
        ];
    }
}
