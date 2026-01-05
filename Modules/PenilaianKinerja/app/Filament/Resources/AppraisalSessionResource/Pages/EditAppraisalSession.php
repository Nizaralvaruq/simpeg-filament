<?php

namespace Modules\PenilaianKinerja\Filament\Resources\AppraisalSessionResource\Pages;

use Modules\PenilaianKinerja\Filament\Resources\AppraisalSessionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAppraisalSession extends EditRecord
{
    protected static string $resource = AppraisalSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
