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
        $state = $this->form->getState();

        // ===== JABATAN =====
        if (($state['status_riwayat_jabatan'] ?? 'tetap') === 'tetap') {
            $this->record->riwayatJabatans()->delete();
            $data['jabatan'] = 'Tetap pada amanahnya';
        } else {
            if (! empty($state['riwayatJabatans'])) {
                $latest = collect($state['riwayatJabatans'])
                    ->filter(fn ($r) => ! empty($r['tanggal']) && ! empty($r['nama_jabatan']))
                    ->sortByDesc('tanggal')
                    ->first();

                if ($latest) {
                    $data['jabatan'] = $latest['nama_jabatan'];
                }
            }
        }

        // ===== GOLONGAN =====
        if (! empty($state['riwayatGolongans'])) {
            $latest = collect($state['riwayatGolongans'])
                ->filter(fn ($r) => ! empty($r['tanggal']) && ! empty($r['golongan_id']))
                ->sortByDesc('tanggal')
                ->first();

            if ($latest) {
                $data['golongan_id'] = $latest['golongan_id'];
            }
        }

        return $data;
    }

}
