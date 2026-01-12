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
