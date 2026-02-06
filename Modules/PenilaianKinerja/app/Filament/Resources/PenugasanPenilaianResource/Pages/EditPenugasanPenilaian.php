<?php

namespace Modules\PenilaianKinerja\Filament\Resources\PenugasanPenilaianResource\Pages;

use Modules\PenilaianKinerja\Filament\Resources\PenugasanPenilaianResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPenugasanPenilaian extends EditRecord
{
    protected static string $resource = PenugasanPenilaianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
