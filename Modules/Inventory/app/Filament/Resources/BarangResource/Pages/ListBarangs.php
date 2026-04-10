<?php

namespace Modules\Inventory\Filament\Resources\BarangResource\Pages;

use Modules\Inventory\Filament\Resources\BarangResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;


class ListBarangs extends ListRecords
{
    protected static string $resource = BarangResource::class;


    protected function getHeaderWidgets(): array
    {
        return [
            \Modules\Inventory\Filament\Widgets\InventoryStatsOverview::class,
        ];
    }
}
