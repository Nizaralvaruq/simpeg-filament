<?php

namespace Modules\PenilaianKinerja\Filament\Resources\KpiCategoryResource\Pages;

use Modules\PenilaianKinerja\Filament\Resources\KpiCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKpiCategory extends EditRecord
{
    protected static string $resource = KpiCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
