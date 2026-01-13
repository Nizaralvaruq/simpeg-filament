<?php

namespace Modules\PenilaianKinerja\Filament\Resources\AppraisalReportResource\Pages;

use Modules\PenilaianKinerja\Filament\Resources\AppraisalReportResource;
use Filament\Resources\Pages\ListRecords;

use Modules\PenilaianKinerja\Exports\AppraisalReportExport;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Actions\Action;

class ListAppraisalReports extends ListRecords
{
    protected static string $resource = AppraisalReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export')
                ->label('Export Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->visible(function () {
                    /** @var \App\Models\User $user */
                    $user = \Illuminate\Support\Facades\Auth::user();
                    // Only visible for super_admin and ketua_psdm
                    return $user && $user->hasAnyRole(['super_admin', 'ketua_psdm']);
                })
                ->action(function () {
                    $records = $this->getFilteredTableQuery()->get();

                    return Excel::download(
                        new AppraisalReportExport($records),
                        'Laporan_Penilaian_' . now()->format('Y-m-d_H-i') . '.xlsx'
                    );
                }),
        ];
    }
}
