<?php

namespace Modules\Inventory\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Database\Eloquent\Builder;

class MutasiStokExport implements FromCollection, WithHeadings, WithMapping
{
    protected Builder $query;

    public function __construct(Builder $query)
    {
        $this->query = $query;
    }

    public function collection()
    {
        return $this->query->with(['barang', 'createdBy'])->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'Kode Barang',
            'Nama Barang',
            'Jenis Mutasi',
            'Jumlah',
            'Sisa Stok',
            'Keterangan',
            'Dilakukan Oleh',
        ];
    }

    public function map($transaction): array
    {
        static $no = 0;
        $no++;

        $jenis = match($transaction->type) {
            'in' => 'Masuk',
            'out' => 'Keluar',
            'opname' => 'Opname/Penyesuaian',
            default => $transaction->type
        };

        return [
            $no,
            $transaction->created_at->format('d/m/Y H:i'),
            $transaction->barang?->kode_barang ?? '-',
            $transaction->barang?->nama_barang ?? '-',
            $jenis,
            $transaction->quantity,
            $transaction->stok_setelah_transaksi ?? '-', // Assuming this exists or falls back to -
            $transaction->remarks,
            $transaction->createdBy?->name ?? '-',
        ];
    }
}
