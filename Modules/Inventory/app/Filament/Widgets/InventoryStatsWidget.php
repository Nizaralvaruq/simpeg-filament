<?php

namespace Modules\Inventory\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Modules\Inventory\Models\Barang;
use Modules\Inventory\Models\KategoriBarang;
use Modules\Inventory\Models\PermintaanBarang;
use Modules\Inventory\Models\Peminjaman;
use Modules\Inventory\Models\StockTransaction;
use Illuminate\Support\Facades\Auth;

class InventoryStatsWidget extends BaseWidget
{
    /**
     * Menonaktifkan auto-discovery agar widget tidak muncul di dashboard utama secara otomatis.
     * Widget ini hanya akan muncul jika dipanggil secara eksplisit (seperti di Data Barang).
     */
    protected static bool $isDiscovered = false;

    public static function canView(): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $user && $user->hasAnyRole(['super_admin', 'ketua_psdm', 'admin_unit', 'koor_jenjang', 'kepala_sekolah']);
    }

    protected function getStats(): array
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        $barangQuery = Barang::where('is_active', true);
        $permintaanQuery = PermintaanBarang::whereIn('status', ['draft', 'diajukan', 'disetujui']);
        $peminjamanQuery = Peminjaman::where('status', 'dipinjam');
        $mutasiQuery = StockTransaction::whereDate('created_at', now());
        
        // Scope berdasar role seperti di Resource
        if (!$user->hasAnyRole(['super_admin', 'ketua_psdm'])) {
            if ($user->hasAnyRole(['admin_unit', 'koor_jenjang', 'kepala_sekolah'])) {
                $unitIds = $user->employee?->units->pluck('id')->all() ?? [];
                $barangQuery->where(function ($q) use ($unitIds) {
                    $q->whereIn('unit_id', $unitIds)->orWhereNull('unit_id');
                });
                $permintaanQuery->whereIn('unit_id', $unitIds);
                $peminjamanQuery->whereIn('unit_id', $unitIds);
                $mutasiQuery->whereHas('barang', function ($q) use ($unitIds) {
                    $q->whereIn('unit_id', $unitIds)->orWhereNull('unit_id');
                });
            } else {
                // Untuk staff biasa (hanya lihat milik sendiri)
                $barangQuery->whereNull('unit_id');
                $permintaanQuery->where('user_id', $user->id);
                $peminjamanQuery->where('user_id', $user->id);
                $mutasiQuery->where('created_by', $user->id);
            }
        }

        $totalBarang    = (clone $barangQuery)->count();
        $stokKritis     = (clone $barangQuery)
                            ->whereColumn('stok_saat_ini', '<=', 'minimum_stok')
                            ->where('minimum_stok', '>', 0)
                            ->count();
        $permintaanOpen = $permintaanQuery->count();
        $terlambat      = $peminjamanQuery->whereDate('rencana_kembali', '<', now())->count();
        $mutasiHariIni  = $mutasiQuery->count();

        return [
            Stat::make('Total Barang Aktif', $totalBarang)
                ->description('Barang yang dapat digunakan')
                ->icon('heroicon-o-archive-box')
                ->color('primary'),

            Stat::make('Stok Kritis', $stokKritis)
                ->description('Perlu pengadaan segera')
                ->icon('heroicon-o-exclamation-triangle')
                ->color($stokKritis > 0 ? 'danger' : 'success'),

            Stat::make('Permintaan Aktif', $permintaanOpen)
                ->description('Menunggu persetujuan/diproses')
                ->icon('heroicon-o-clipboard-document-list')
                ->color('warning'),

            Stat::make('Peminjaman Terlambat', $terlambat)
                ->description('Melewati batas pengembalian')
                ->icon('heroicon-o-clock')
                ->color($terlambat > 0 ? 'danger' : 'success'),

            Stat::make('Mutasi Hari Ini', $mutasiHariIni)
                ->description('Transaksi stok hari ini')
                ->icon('heroicon-o-arrows-right-left')
                ->color('info'),
        ];
    }
}
