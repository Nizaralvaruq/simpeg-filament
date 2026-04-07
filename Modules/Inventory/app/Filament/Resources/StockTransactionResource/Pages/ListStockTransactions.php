<?php

namespace Modules\Inventory\Filament\Resources\StockTransactionResource\Pages;

use Modules\Inventory\Filament\Resources\StockTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStockTransactions extends ListRecords
{
    protected static string $resource = StockTransactionResource::class;

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
                        ->whereBetween('created_at', [$data['start_date'] . ' 00:00:00', $data['end_date'] . ' 23:59:59']);
                    return \Maatwebsite\Excel\Facades\Excel::download(
                        new \Modules\Inventory\Exports\MutasiStokExport($query), 
                        'mutasi_stok_' . date('Ymd_His') . '.xlsx'
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
                    $transactions = $this->getFilteredTableQuery()
                        ->whereBetween('created_at', [$data['start_date'] . ' 00:00:00', $data['end_date'] . ' 23:59:59'])
                        ->with(['barang', 'createdBy'])
                        ->get();
                    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('inventory::reports.mutasi_stok_pdf', compact('transactions'));
                    return response()->streamDownload(fn () => print($pdf->output()), 'mutasi_stok_' . date('Ymd_His') . '.pdf');
                }),

            Actions\CreateAction::make(),
        ];
    }
}
