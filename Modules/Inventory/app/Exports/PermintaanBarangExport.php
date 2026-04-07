<?php

namespace Modules\Inventory\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Database\Eloquent\Builder;

class PermintaanBarangExport implements FromCollection, WithHeadings, WithMapping
{
    protected Builder $query;

    public function __construct(Builder $query)
    {
        $this->query = $query;
    }

    public function collection()
    {
        return $this->query->with(['user', 'unit', 'approvedBy', 'details.barang'])->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Nomor Permintaan',
            'Tanggal',
            'Pemohon',
            'Unit Pemohon',
            'Status',
            'Disetujui Oleh',
            'Tgl Setuju/Proses',
            'Catatan/Alasan',
            'Detail Barang (Nama | Diminta | Disetujui)',
        ];
    }

    public function map($permintaan): array
    {
        static $no = 0;
        $no++;

        $detailString = $permintaan->details->map(function ($detail) {
            return $detail->barang?->nama_barang . ' (' . $detail->jumlah_diminta . ' diminta | ' . $detail->jumlah_disetujui . ' disetujui)';
        })->implode('; ');

        return [
            $no,
            $permintaan->nomor_permintaan,
            $permintaan->tanggal_permintaan->format('d/m/Y'),
            $permintaan->user?->name ?? '-',
            $permintaan->unit?->name ?? '-',
            strtoupper($permintaan->status),
            $permintaan->approvedBy?->name ?? '-',
            $permintaan->approved_at ? $permintaan->approved_at->format('d/m/Y H:i') : '-',
            $permintaan->alasan_penolakan ?? $permintaan->catatan ?? '-',
            $detailString,
        ];
    }
}
