<?php

namespace Modules\Inventory\Filament\Resources\PermintaanBarangResource\Pages;

use Modules\Inventory\Filament\Resources\PermintaanBarangResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPermintaanBarangs extends ListRecords
{
    protected static string $resource = PermintaanBarangResource::class;

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
                ->schema([
                    \Filament\Forms\Components\DatePicker::make('start_date')->label('Dari Tanggal')->default(now()->startOfMonth())->required(),
                    \Filament\Forms\Components\DatePicker::make('end_date')->label('Sampai Tanggal')->default(now()->endOfMonth())->required(),
                ])
                ->action(function (array $data) {
                    /** @var \Illuminate\Database\Eloquent\Builder $query */
                    $query = $this->getFilteredTableQuery()
                        ->whereBetween('tanggal_permintaan', [$data['start_date'], $data['end_date']]);
                    return \Maatwebsite\Excel\Facades\Excel::download(
                        new \Modules\Inventory\Exports\PermintaanBarangExport($query), 
                        'rekap_permintaan_' . date('Ymd_His') . '.xlsx'
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
                ->schema([
                    \Filament\Forms\Components\DatePicker::make('start_date')->label('Dari Tanggal')->default(now()->startOfMonth())->required(),
                    \Filament\Forms\Components\DatePicker::make('end_date')->label('Sampai Tanggal')->default(now()->endOfMonth())->required(),
                ])
                ->action(function (array $data) {
                    $permintaans = $this->getFilteredTableQuery()
                        ->whereBetween('tanggal_permintaan', [$data['start_date'], $data['end_date']])
                        ->with(['user', 'unit', 'approvedBy', 'details.barang'])
                        ->get();
                    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('inventory::reports.permintaan_barang_pdf', compact('permintaans'));
                    return response()->streamDownload(fn () => print($pdf->output()), 'rekap_permintaan_' . date('Ymd_His') . '.pdf');
                }),

            Actions\CreateAction::make(),
        ];
    }
}
