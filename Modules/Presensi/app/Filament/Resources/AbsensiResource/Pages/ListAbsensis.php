<?php

namespace Modules\Presensi\Filament\Resources\AbsensiResource\Pages;

use Modules\Presensi\Filament\Resources\AbsensiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAbsensis extends ListRecords
{
    protected static string $resource = AbsensiResource::class;

    protected string $view = 'presensi::filament.pages.list-absensis';

    public ?string $activeTab = 'log';

    protected function getHeaderActions(): array
    {
        return [];
    }
}
