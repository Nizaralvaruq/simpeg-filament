<?php

namespace Modules\Presensi\Filament\Resources\KegiatanResource\Pages;

use Modules\Presensi\Filament\Resources\KegiatanResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewKegiatan extends ViewRecord
{
    protected static string $resource = KegiatanResource::class;

    protected function getHeaderActions(): array
    {
        $user = \Illuminate\Support\Facades\Auth::user();

        // Only super_admin can edit/delete
        if ($user instanceof \App\Models\User && $user->hasRole('super_admin')) {
            return [
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ];
        }

        return [];
    }
}
