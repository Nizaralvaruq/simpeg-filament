<?php

namespace Modules\Inventory\Filament\Resources\StockTransactionResource\Pages;

use Modules\Inventory\Filament\Resources\StockTransactionResource;
use Modules\Inventory\Models\Barang;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateStockTransaction extends CreateRecord
{
    protected static string $resource = StockTransactionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $barang = Barang::find($data['barang_id']);
        if ($barang) {
            $data['stok_sebelum_transaksi'] = $barang->stok_saat_ini;
            if ($data['type'] === 'in') {
                $data['stok_setelah_transaksi'] = $barang->stok_saat_ini + $data['quantity'];
            } elseif ($data['type'] === 'out') {
                $data['stok_setelah_transaksi'] = $barang->stok_saat_ini - $data['quantity'];
            } elseif ($data['type'] === 'opname') {
                $data['stok_setelah_transaksi'] = $data['quantity'];
            }
        }
        $data['created_by'] = Auth::id();
        return $data;
    }

    protected function afterCreate(): void
    {
        // Update stok barang setelah transaksi dibuat
        $transaction = $this->record;
        $barang = Barang::find($transaction->barang_id);

        if ($barang) {
            if ($transaction->type === 'in') {
                $barang->increment('stok_saat_ini', $transaction->quantity);
            } elseif ($transaction->type === 'out') {
                $barang->decrement('stok_saat_ini', $transaction->quantity);
            } elseif ($transaction->type === 'opname') {
                $barang->update(['stok_saat_ini' => $transaction->quantity]);
            }
        }
    }
}
