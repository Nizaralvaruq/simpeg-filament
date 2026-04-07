<?php

namespace Modules\Inventory\Filament\Resources\BarangResource\Pages;

use Modules\Inventory\Filament\Resources\BarangResource;
use Modules\Inventory\Filament\Widgets\InventoryStatsWidget;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

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
                ->visible(function () {
                    /** @var \App\Models\User $user */
                    $user = \Illuminate\Support\Facades\Auth::user();
                    return $user?->hasAnyRole(['super_admin', 'admin_unit']);
                })
                ->action(function () {
                    $query = $this->getFilteredTableQuery();
                    return \Maatwebsite\Excel\Facades\Excel::download(
                        new \Modules\Inventory\Exports\BarangExport($query), 
                        'data_barang_' . date('Ymd_His') . '.xlsx'
                    );
                }),

            Actions\Action::make('cetak_pdf')
                ->label('Cetak PDF')
                ->icon('heroicon-o-printer')
                ->color('danger')
                ->visible(function () {
                    /** @var \App\Models\User $user */
                    $user = \Illuminate\Support\Facades\Auth::user();
                    return $user?->hasAnyRole(['super_admin', 'admin_unit']);
                })
                ->action(function () {
                    $barangs = $this->getFilteredTableQuery()->with(['kategori', 'unit'])->get();
                    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('inventory::reports.barang_pdf', compact('barangs'));
                    return response()->streamDownload(fn () => print($pdf->output()), 'data_barang_' . date('Ymd_His') . '.pdf');
                }),

            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            InventoryStatsWidget::class,
        ];
    }
}
