<?php

namespace Modules\PenilaianKinerja\Filament\Resources\AppraisalReportResource\Pages;

use Modules\PenilaianKinerja\Filament\Resources\AppraisalReportResource;
use Filament\Resources\Pages\ListRecords;

class ListAppraisalReports extends ListRecords
{
    protected static string $resource = AppraisalReportResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
