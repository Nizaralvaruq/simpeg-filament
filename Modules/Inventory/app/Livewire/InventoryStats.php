<?php

namespace Modules\Inventory\Livewire;

use Livewire\Component;
use Modules\Inventory\Models\Barang;
use Modules\Inventory\Models\Peminjaman;
use Modules\Inventory\Models\PermintaanBarang;
use Modules\Inventory\Models\StockTransaction;
use Illuminate\Support\Facades\Auth;

class InventoryStats extends Component
{
    public int $totalBarang    = 0;
    public int $stokKritis     = 0;
    public int $permintaanOpen = 0;
    public int $terlambat      = 0;
    public int $mutasiHariIni  = 0;

    public function mount(): void
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $barangQuery     = Barang::where('is_active', true);
        $permintaanQuery = PermintaanBarang::whereIn('status', ['draft', 'diajukan', 'disetujui']);
        $peminjamanQuery = Peminjaman::where('status', 'dipinjam');
        $mutasiQuery     = StockTransaction::whereDate('created_at', now());

        if (!$user->hasAnyRole(['super_admin', 'ketua_psdm'])) {
            if ($user->hasAnyRole(['admin_unit', 'koor_jenjang', 'kepala_sekolah'])) {
                $unitIds = $user->employee?->units->pluck('id')->all() ?? [];
                $barangQuery->where(fn ($q) => $q->whereIn('unit_id', $unitIds)->orWhereNull('unit_id'));
                $permintaanQuery->whereIn('unit_id', $unitIds);
                $peminjamanQuery->whereIn('unit_id', $unitIds);
                $mutasiQuery->whereHas('barang', fn ($q) => $q->whereIn('unit_id', $unitIds)->orWhereNull('unit_id'));
            } else {
                $barangQuery->whereNull('unit_id');
                $permintaanQuery->where('user_id', $user->id);
                $peminjamanQuery->where('user_id', $user->id);
                $mutasiQuery->where('created_by', $user->id);
            }
        }

        $this->totalBarang    = (clone $barangQuery)->count();
        $this->stokKritis     = (clone $barangQuery)->whereColumn('stok_saat_ini', '<=', 'minimum_stok')->where('minimum_stok', '>', 0)->count();
        $this->permintaanOpen = $permintaanQuery->count();
        $this->terlambat      = $peminjamanQuery->whereDate('rencana_kembali', '<', now())->count();
        $this->mutasiHariIni  = $mutasiQuery->count();
    }

    public function render()
    {
        return view('inventory::livewire.inventory-stats');
    }

    public function exportExcel()
    {
        $query = Barang::query();
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if (!$user->hasAnyRole(['super_admin', 'ketua_psdm'])) {
            if ($user->hasAnyRole(['admin_unit', 'koor_jenjang', 'kepala_sekolah'])) {
                $unitIds = $user->employee?->units->pluck('id')->all() ?? [];
                $query->where(fn ($q) => $q->whereIn('unit_id', $unitIds)->orWhereNull('unit_id'));
            } else {
                $query->whereNull('unit_id');
            }
        }

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \Modules\Inventory\Exports\BarangExport($query), 
            'data_barang_' . date('Ymd_His') . '.xlsx'
        );
    }

    public function exportPdf()
    {
        $query = Barang::with(['kategori', 'unit']);
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if (!$user->hasAnyRole(['super_admin', 'ketua_psdm'])) {
            if ($user->hasAnyRole(['admin_unit', 'koor_jenjang', 'kepala_sekolah'])) {
                $unitIds = $user->employee?->units->pluck('id')->all() ?? [];
                $query->where(fn ($q) => $q->whereIn('unit_id', $unitIds)->orWhereNull('unit_id'));
            } else {
                $query->whereNull('unit_id');
            }
        }
        
        $barangs = $query->get();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('inventory::reports.barang_pdf', compact('barangs'));
        return response()->streamDownload(fn () => print($pdf->output()), 'data_barang_' . date('Ymd_His') . '.pdf');
    }
}
