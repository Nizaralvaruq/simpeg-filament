<?php

namespace Modules\Inventory\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Database\Eloquent\Builder;

class BarangExport implements FromCollection, WithHeadings, WithMapping
{
    protected Builder $query;

    public function __construct(Builder $query)
    {
        $this->query = $query;
    }

    public function collection()
    {
        return $this->query->with(['kategori', 'unit'])->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode Barang',
            'Nama Barang',
            'Kategori',
            'Pemilik Unit',
            'Jenis',
            'Spesifikasi',
            'Lokasi/Ruangan',
            'Stok Saat Ini',
            'Minimum Stok',
            'Status Aktif',
        ];
    }

    public function map($barang): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $barang->kode_barang,
            $barang->nama_barang,
            $barang->kategori?->nama_kategori ?? '-',
            $barang->unit?->name ?? 'Pusat/Global',
            $barang->jenis,
            $barang->spesifikasi,
            $barang->lokasi_ruangan,
            $barang->stok_saat_ini,
            $barang->minimum_stok,
            $barang->is_active ? 'Aktif' : 'Tidak Aktif',
        ];
    }
}
