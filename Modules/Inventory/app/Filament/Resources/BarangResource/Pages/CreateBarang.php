<?php

namespace Modules\Inventory\Filament\Resources\BarangResource\Pages;

use Modules\Inventory\Filament\Resources\BarangResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBarang extends CreateRecord
{
    protected static string $resource = BarangResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        /** @var \Modules\Inventory\Models\Barang $record */
        $record = $this->record;

        if ($record->stok_saat_ini > 0) {
            \Modules\Inventory\Models\StockTransaction::create([
                'barang_id' => $record->id,
                'type' => 'in',
                'quantity' => $record->stok_saat_ini,
                'stok_sebelum_transaksi' => 0,
                'stok_setelah_transaksi' => $record->stok_saat_ini,
                'remarks' => 'Stok awal saat penambahan barang baru',
                'created_by' => \Illuminate\Support\Facades\Auth::id(),
            ]);
        }
    }
}
