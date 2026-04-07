<?php

use Modules\Inventory\Models\KategoriBarang;
use Modules\Inventory\Models\Barang;
use Modules\Inventory\Models\StockTransaction;
use Modules\Inventory\Models\Peminjaman;
use Modules\Inventory\Models\PeminjamanDetail;
use Modules\MasterData\Models\Unit;
use App\Models\User;
use Illuminate\Support\Facades\DB;

try {
    echo "Starting Seeding Demo Inventory...\n";

    // 1. Kategori
    $kategoriNames = ['Alat Tulis Kantor', 'Elektronik', 'Furniture', 'Ruangan', 'Alat Olahraga'];
    $categories = [];
    foreach ($kategoriNames as $name) {
        $categories[] = KategoriBarang::updateOrCreate(['nama_kategori' => $name], [
            'deskripsi' => 'Kategori untuk ' . $name
        ]);
        echo "Category created: $name\n";
    }

    // 2. Relasi
    $admin = User::first();
    $smk = Unit::where('name', 'LIKE', '%SMK%')->first();
    $sma = Unit::where('name', 'LIKE', '%SMA%')->first();

    echo "Using Admin: " . ($admin?->name ?? 'None') . "\n";
    echo "Using SMK Unit: " . ($smk?->name ?? 'None') . "\n";
    echo "Using SMA Unit: " . ($sma?->name ?? 'None') . "\n";

    // 3. Data Barang
    $barangs = [
        [
            'nama_barang' => 'Laptop ASUS Core i5 (Demo)',
            'kode_barang' => 'ELK-001-DEMO',
            'kategori_id' => $categories[1]->id,
            'unit_id' => $smk?->id,
            'jenis' => 'Aset',
            'stok_saat_ini' => 10,
            'minimum_stok' => 2,
            'lokasi_ruangan' => 'Lab Komputer SMK',
            'is_active' => true,
        ],
        [
            'nama_barang' => 'Proyektor Epson (Demo)',
            'kode_barang' => 'ELK-002-DEMO',
            'kategori_id' => $categories[1]->id,
            'unit_id' => $sma?->id,
            'jenis' => 'Aset',
            'stok_saat_ini' => 5,
            'minimum_stok' => 1,
            'lokasi_ruangan' => 'Gudang SMA',
            'is_active' => true,
        ],
        [
            'nama_barang' => 'Kertas A4 (Demo)',
            'kode_barang' => 'ATK-001-DEMO',
            'kategori_id' => $categories[0]->id,
            'unit_id' => null,
            'jenis' => 'Habis Pakai',
            'stok_saat_ini' => 50,
            'minimum_stok' => 10,
            'lokasi_ruangan' => 'Gudang Pusat',
            'is_active' => true,
        ],
        [
            'nama_barang' => 'Lab Komputer Multimedia (Demo)',
            'kode_barang' => 'RNG-001-DEMO',
            'kategori_id' => $categories[3]->id,
            'unit_id' => $smk?->id,
            'jenis' => 'Ruangan',
            'stok_saat_ini' => 1,
            'minimum_stok' => 0,
            'lokasi_ruangan' => 'Lantai 2 SMK',
            'is_active' => true,
        ],
    ];

    foreach ($barangs as $b) {
        $barangObj = Barang::updateOrCreate(['kode_barang' => $b['kode_barang']], $b);
        echo "Barang created: " . $b['nama_barang'] . "\n";

        // Initial Transaction
        StockTransaction::updateOrCreate(
            ['barang_id' => $barangObj->id, 'type' => 'in', 'remarks' => 'Initial Stock Demo'],
            [
                'quantity' => $b['stok_saat_ini'],
                'stok_sebelum_transaksi' => 0,
                'stok_setelah_transaksi' => $b['stok_saat_ini'],
                'created_by' => $admin?->id ?? 1,
            ]
        );
    }

    echo "ALL DEMO DATA SEEDED SUCCESSFULLY!\n";

} catch (\Exception $e) {
    echo "ERROR SEEDING: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
