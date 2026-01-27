<?php

namespace Modules\Presensi\Filament\Resources\JadwalPiketResource\Pages;

use Modules\Presensi\Filament\Resources\JadwalPiketResource;
use Filament\Resources\Pages\ListRecords;

class ListJadwalPikets extends ListRecords
{
    protected static string $resource = JadwalPiketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('assign_list')
                ->label('Tambah via List Pegawai')
                ->icon('heroicon-o-users')
                ->url(JadwalPiketResource::getUrl('assign'))
                ->visible(fn() => JadwalPiketResource::canCreate()),
            \Filament\Actions\CreateAction::make()
                ->mutateDataUsing(function (array $data): array {
                    $data['created_by'] = \Illuminate\Support\Facades\Auth::id();
                    return $data;
                })
                ->visible(fn() => JadwalPiketResource::canCreate()),
        ];
    }
}
