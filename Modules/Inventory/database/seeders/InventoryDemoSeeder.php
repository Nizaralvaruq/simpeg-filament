<?php

namespace Modules\Inventory\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Inventory\Models\KategoriBarang;
use Modules\Inventory\Models\Barang;
use Modules\Inventory\Models\StockTransaction;
use Modules\Inventory\Models\Peminjaman;
use Modules\Inventory\Models\PeminjamanDetail;
use Modules\MasterData\Models\Unit;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class InventoryDemoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Kategori
        $kategoriNames = ['Alat Tulis Kantor', 'Elektronik', 'Furniture', 'Ruangan', 'Alat Olahraga'];
        $categories = [];
        foreach ($kategoriNames as $name) {
            $categories[] = KategoriBarang::updateOrCreate(['nama_kategori' => $name], [
                'deskripsi' => 'Kategori untuk ' . $name
            ]);
        }

        // 2. Ambil User & Unit untuk relasi
        $admin = User::role('super_admin')->first() ?? User::first();
        $smk = Unit::where('name', 'SMK')->first();
        $sma = Unit::where('name', 'SMA')->first();

        // 3. Data Barang
        $barangs = [
            [
                'nama_barang' => 'Laptop ASUS Core i5',
                'kode_barang' => 'ELK-001',
                'kategori_id' => $categories[1]->id, // Elektronik
                'unit_id' => $smk?->id,
                'jenis' => 'Elektronik',
                'stok_saat_ini' => 10,
                'minimum_stok' => 2,
                'lokasi_ruangan' => 'Lab Komputer SMK',
                'is_active' => true,
            ],
            [
                'nama_barang' => 'Proyektor Epson EB-X400',
                'kode_barang' => 'ELK-002',
                'kategori_id' => $categories[1]->id,
                'unit_id' => $sma?->id,
                'jenis' => 'Aset',
                'stok_saat_ini' => 5,
                'minimum_stok' => 1,
                'lokasi_ruangan' => 'Gudang SMA',
                'is_active' => true,
            ],
            [
                'nama_barang' => 'Kertas A4 70gr (Paper One)',
                'kode_barang' => 'ATK-001',
                'kategori_id' => $categories[0]->id, // ATK
                'unit_id' => null, // Global
                'jenis' => 'Bahan Habis Pakai',
                'stok_saat_ini' => 50,
                'minimum_stok' => 10,
                'lokasi_ruangan' => 'Gudang Pusat',
                'is_active' => true,
            ],
            [
                'nama_barang' => 'Meja Guru Kayu Jati',
                'kode_barang' => 'FUR-001',
                'kategori_id' => $categories[2]->id, // Furniture
                'unit_id' => $smk?->id,
                'jenis' => 'Aset',
                'stok_saat_ini' => 15,
                'minimum_stok' => 0,
                'lokasi_ruangan' => 'Kelas SMK',
                'is_active' => true,
            ],
            [
                'nama_barang' => 'Lab Komputer Multimedia',
                'kode_barang' => 'RNG-001',
                'kategori_id' => $categories[3]->id, // Ruangan
                'unit_id' => $smk?->id,
                'jenis' => 'Ruangan',
                'stok_saat_ini' => 1,
                'minimum_stok' => 0,
                'lokasi_ruangan' => 'Lantai 2 SMK',
                'is_active' => true,
            ],
            [
                'nama_barang' => 'Aula Utama Al-Hikmah',
                'kode_barang' => 'RNG-002',
                'kategori_id' => $categories[3]->id,
                'unit_id' => null,
                'jenis' => 'Ruangan',
                'stok_saat_ini' => 1,
                'minimum_stok' => 0,
                'lokasi_ruangan' => 'Gedung Pusat Lt. 1',
                'is_active' => true,
            ],
        ];

        foreach ($barangs as $b) {
            $barangObj = Barang::updateOrCreate(['kode_barang' => $b['kode_barang']], $b);

            // 4. Catat Transaksi Awal jika belum ada
            if (StockTransaction::where('barang_id', $barangObj->id)->count() === 0) {
                StockTransaction::create([
                    'barang_id' => $barangObj->id,
                    'type' => 'in',
                    'quantity' => $b['stok_saat_ini'],
                    'stok_sebelum_transaksi' => 0,
                    'stok_setelah_transaksi' => $b['stok_saat_ini'],
                    'remarks' => 'Saldo Awal (Demo Seeder)',
                    'created_by' => $admin?->id ?? 1,
                ]);
            }
        }

        // 5. Contoh Peminjaman (Jika ada user lain)
        $staff = User::role('staff')->first() ?? User::latest()->first();
        if ($staff && $admin && $smk) {
            $peminjaman = Peminjaman::create([
                'nomor_peminjaman' => Peminjaman::generateNomor(),
                'user_id' => $staff->id,
                'unit_id' => $smk->id,
                'tanggal_pinjam' => now()->subDays(2),
                'rencana_kembali' => now()->addDays(1),
                'keperluan' => 'Peminjaman Laptop untuk presentasi rapat kurikulum.',
                'status' => 'dipinjam',
                'approved_by' => $admin->id,
                'approved_at' => now()->subDays(2),
            ]);

            $laptop = Barang::where('kode_barang', 'ELK-001')->first();
            if ($laptop) {
                PeminjamanDetail::create([
                    'peminjaman_id' => $peminjaman->id,
                    'barang_id' => $laptop->id,
                    'jumlah_pinjam' => 1,
                    'kondisi_sebelum' => 'Baik',
                ]);
                $laptop->decrement('stok_saat_ini', 1);
            }
        }
    }
}
