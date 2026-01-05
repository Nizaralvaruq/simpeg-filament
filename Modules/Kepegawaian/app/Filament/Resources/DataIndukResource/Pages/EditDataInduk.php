<?php

namespace Modules\Kepegawaian\Filament\Resources\DataIndukResource\Pages;

use Dflydev\DotAccessData\Data;
use Filament\Resources\Pages\EditRecord;
use Modules\Kepegawaian\Filament\Resources\DataIndukResource;

class EditDataInduk extends EditRecord
{
    protected static string $resource = DataIndukResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Jika tetap, bersihkan riwayat jabatan di DB
        if (($data['pindah_tugas'] ?? 'tetap') === 'tetap') {
            $this->record->riwayatJabatans()->delete();
        }

        return $data;
    }
}
