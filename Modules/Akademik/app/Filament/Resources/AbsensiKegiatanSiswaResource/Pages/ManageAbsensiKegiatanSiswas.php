<?php

namespace Modules\Akademik\Filament\Resources\AbsensiKegiatanSiswaResource\Pages;

use Modules\Akademik\Filament\Resources\AbsensiKegiatanSiswaResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\Auth;

class ManageAbsensiKegiatanSiswas extends ManageRecords
{
    protected static string $resource = AbsensiKegiatanSiswaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->visible(function () {
                    /** @var \App\Models\User $user */
                    $user = Auth::user();
                    return $user?->hasRole('super_admin');
                }),
        ];
    }
}
