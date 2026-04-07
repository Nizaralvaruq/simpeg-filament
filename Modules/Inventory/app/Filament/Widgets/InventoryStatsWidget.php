<?php

namespace Modules\Inventory\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Modules\Inventory\Models\Barang;
use Modules\Inventory\Models\KategoriBarang;
use Modules\Inventory\Models\PermintaanBarang;
use Illuminate\Support\Facades\Auth;

class InventoryStatsWidget extends BaseWidget
{
    /**
     * Widget ini hanya ditampilkan saat dipanggil secara eksplisit dari halaman
     * resource (getHeaderWidgets), bukan di dashboard global.
     */
    public static function canView(): bool
    {
        return false; // disembunyikan dari dashboard global
    }

    protected function getStats(): array
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        $barangQuery = Barang::where('is_active', true);
        $permintaanQuery = PermintaanBarang::whereIn('status', ['draft', 'diajukan', 'disetujui']);
        
        // Scope berdasar role seperti di Resource
        if (!$user->hasAnyRole(['super_admin', 'ketua_psdm'])) {
            if ($user->hasAnyRole(['admin_unit', 'koor_jenjang', 'kepala_sekolah'])) {
                $unitIds = $user->employee?->units->pluck('id')->all() ?? [];
                $barangQuery->where(function ($q) use ($unitIds) {
                    $q->whereIn('unit_id', $unitIds)->orWhereNull('unit_id');
                });
                $permintaanQuery->whereIn('unit_id', $unitIds);
            } else {
                // Untuk staff biasa (jika bisa melihat)
                $barangQuery->whereNull('unit_id');
                $permintaanQuery->where('user_id', $user->id);
            }
        }

        $totalBarang    = (clone $barangQuery)->count();
        $stokKritis     = (clone $barangQuery)
                            ->whereColumn('stok_saat_ini', '<=', 'minimum_stok')
                            ->where('minimum_stok', '>', 0)
                            ->count();
        $totalKategori  = KategoriBarang::count();
        $permintaanOpen = $permintaanQuery->count();

        return [
            Stat::make('Total Barang Aktif', $totalBarang)
                ->description('Semua barang terdaftar')
                ->icon('heroicon-o-archive-box')
                ->color('primary'),

            Stat::make('Stok Kritis', $stokKritis)
                ->description('Stok di bawah atau sama dengan minimum')
                ->icon('heroicon-o-exclamation-triangle')
                ->color($stokKritis > 0 ? 'danger' : 'success'),

            Stat::make('Kategori Barang', $totalKategori)
                ->description('Total jenis kategori')
                ->icon('heroicon-o-tag')
                ->color('info'),

            Stat::make('Permintaan Aktif', $permintaanOpen)
                ->description('Draft, Diajukan & Disetujui')
                ->icon('heroicon-o-clipboard-document-list')
                ->color('warning'),
        ];
    }
}
