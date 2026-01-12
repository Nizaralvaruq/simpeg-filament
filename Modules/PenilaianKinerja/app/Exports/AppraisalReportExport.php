<?php

namespace Modules\PenilaianKinerja\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Modules\PenilaianKinerja\Models\AppraisalAssignment;

class AppraisalReportExport implements FromCollection, WithHeadings, WithMapping
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
            'Sesi Penilaian',
            'Nama Pegawai',
            'Progres (Selesai/Total)',
            'Skor Akhir',
        ];
    }

    public function map($record): array
    {
        return [
            $record->session->name ?? '-',
            $record->ratee->nama ?? '-',
            $record->completed_assignments . ' / ' . $record->total_assignments,
            AppraisalAssignment::getAggregatedReport($record->session_id, $record->ratee_id) ?? '-',
        ];
    }
}
