<?php

namespace Modules\PenilaianKinerja\Filament\Resources\AppraisalSessionResource\Pages;

use Modules\PenilaianKinerja\Filament\Resources\AppraisalSessionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAppraisalSessions extends ListRecords
{
    protected static string $resource = AppraisalSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Sesi Penilaian'),
        ];
    }
}
