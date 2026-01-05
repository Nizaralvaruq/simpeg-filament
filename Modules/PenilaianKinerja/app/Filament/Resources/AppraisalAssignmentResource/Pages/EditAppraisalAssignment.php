<?php

namespace Modules\PenilaianKinerja\Filament\Resources\AppraisalAssignmentResource\Pages;

use Modules\PenilaianKinerja\Filament\Resources\AppraisalAssignmentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAppraisalAssignment extends EditRecord
{
    protected static string $resource = AppraisalAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
