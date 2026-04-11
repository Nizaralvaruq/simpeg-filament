<?php

namespace Modules\Inventory\Filament\Resources\BarangResource\Pages;

use Modules\Inventory\Filament\Resources\BarangResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;


use Modules\Inventory\Models\Barang;
use Illuminate\Support\Facades\Auth;

class ListBarangs extends ListRecords
{
    protected static string $resource = BarangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export_excel')
                ->label('Export Excel')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->visible(fn() => Auth::user()?->hasAnyRole(['super_admin', 'admin_unit']))
                ->action(function () {
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
                }),

            Actions\Action::make('export_pdf')
                ->label('Cetak PDF')
                ->icon('heroicon-o-printer')
                ->color('danger')
                ->visible(fn() => Auth::user()?->hasAnyRole(['super_admin', 'admin_unit']))
                ->action(function () {
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
                }),

            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \Modules\Inventory\Filament\Widgets\InventoryStatsOverview::class,
        ];
    }

    public function getHeaderWidgetsColumns(): int | array
    {
        return 1;
    }
}
