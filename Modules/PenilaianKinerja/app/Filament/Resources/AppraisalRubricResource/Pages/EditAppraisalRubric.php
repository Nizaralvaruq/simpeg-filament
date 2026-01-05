<?php

namespace Modules\PenilaianKinerja\Filament\Resources\AppraisalRubricResource\Pages;

use Modules\PenilaianKinerja\Filament\Resources\AppraisalRubricResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAppraisalRubric extends EditRecord
{
    protected static string $resource = AppraisalRubricResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
