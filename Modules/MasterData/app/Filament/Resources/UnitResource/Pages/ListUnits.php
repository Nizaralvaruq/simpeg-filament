<?php

namespace Modules\MasterData\Filament\Resources\UnitResource\Pages;

use Modules\MasterData\Filament\Resources\UnitResource;
use Modules\MasterData\Filament\Widgets\TipeJenjangWidget;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListUnits extends ListRecords
{
    protected static string $resource = UnitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Jenjang'),
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            TipeJenjangWidget::class,
        ];
    }
}
