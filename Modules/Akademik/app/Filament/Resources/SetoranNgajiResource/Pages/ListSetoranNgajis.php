<?php

namespace Modules\Akademik\Filament\Resources\SetoranNgajiResource\Pages;

use Modules\Akademik\Filament\Resources\SetoranNgajiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListSetoranNgajis extends ListRecords
{
    protected static string $resource = SetoranNgajiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Input Setoran Baru')
                ->visible(function () {
                    /** @var \App\Models\User $user */
                    $user = Auth::user();
                    return $user?->hasRole('super_admin');
                }),
        ];
    }
}
