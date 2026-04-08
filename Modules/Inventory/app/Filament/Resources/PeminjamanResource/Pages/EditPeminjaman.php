<?php

namespace Modules\Inventory\Filament\Resources\PeminjamanResource\Pages;

use Modules\Inventory\Filament\Resources\PeminjamanResource;
use Modules\Inventory\Models\Barang;
use Modules\Inventory\Models\StockTransaction;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class EditPeminjaman extends EditRecord
{
    protected static string $resource = PeminjamanResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return [
            Actions\Action::make('ajukan')
                ->label('Ajukan Peminjaman')
                ->color('warning')
                ->icon('heroicon-o-paper-airplane')
                ->visible(fn () => $this->record->status === 'draft')
                ->action(function () {
                    $this->record->update(['status' => 'diajukan']);
                    Notification::make()->title('Peminjaman diajukan')->success()->send();
                }),

            Actions\Action::make('setujui')
                ->label('Setujui & Pinjamkan')
                ->color('info')
                ->icon('heroicon-o-check-circle')
                ->visible(fn () => $this->record->status === 'diajukan' && $user->hasAnyRole(['super_admin', 'admin_unit']))
                ->requiresConfirmation()
                ->action(function () use ($user) {
                    // Validasi stok
                    foreach ($this->record->details as $detail) {
                        $barang = Barang::find($detail->barang_id);
                        if ($barang->stok_saat_ini < $detail->jumlah_pinjam) {
                            Notification::make()->title("Stok {$barang->nama_barang} tidak mencukupi!")->danger()->send();
                            return;
                        }
                    }

                    // Potong stok
                    foreach ($this->record->details as $detail) {
                        $barang = Barang::find($detail->barang_id);
                        
                        StockTransaction::create([
                            'barang_id' => $barang->id,
                            'type' => 'out',
                            'quantity' => $detail->jumlah_pinjam,
                            'stok_sebelum_transaksi' => $barang->stok_saat_ini,
                            'stok_setelah_transaksi' => $barang->stok_saat_ini - $detail->jumlah_pinjam,
                            'remarks' => "Dipinjam: " . $this->record->nomor_peminjaman,
                            'created_by' => $user->id,
                        ]);

                        $barang->decrement('stok_saat_ini', $detail->jumlah_pinjam);
                    }

                    $this->record->update([
                        'status' => 'dipinjam',
                        'approved_by' => $user->id,
                        'approved_at' => now(),
                    ]);

                    Notification::make()->title('Pemesanan disetujui, Stok telah diperbarui.')->success()->send();
                    $this->refreshFormData(['status']);
                }),

            Actions\Action::make('tolak')
                ->label('Tolak')
                ->color('danger')
                ->icon('heroicon-o-x-circle')
                ->visible(fn () => $this->record->status === 'diajukan' && $user->hasAnyRole(['super_admin', 'admin_unit']))
                ->form([
                    \Filament\Forms\Components\Textarea::make('alasan_penolakan')
                        ->label('Alasan Penolakan')
                        ->required(),
                ])
                ->action(function (array $data) use ($user) {
                    $this->record->update([
                        'status' => 'ditolak',
                        'alasan_penolakan' => $data['alasan_penolakan'],
                        'approved_by' => $user->id,
                        'approved_at' => now(),
                    ]);
                    Notification::make()->title('Peminjaman ditolak')->success()->send();
                }),

            Actions\Action::make('ajukan_pengembalian')
                ->label('Ajukan Pengembalian')
                ->color('warning')
                ->icon('heroicon-o-arrow-uturn-left')
                ->visible(fn () => $this->record->status === 'dipinjam')
                ->form([
                    \Filament\Forms\Components\Textarea::make('catatan_pengembalian')
                        ->label('Catatan Kondisi Barang (Opsional)')
                        ->placeholder('Misal: Baterai proyektor sudah lemah..')
                ])
                ->action(function (array $data) {
                    $this->record->update([
                        'status' => 'menunggu_pengecekan',
                        'catatan_pengembalian' => $data['catatan_pengembalian'] ?? null,
                    ]);
                    Notification::make()->title('Pengembalian diajukan, menunggu pengecekan Admin.')->success()->send();
                    $this->refreshFormData(['status', 'catatan_pengembalian']);
                }),

            Actions\Action::make('terima_baik')
                ->label('Terima & Kondisi Baik')
                ->color('success')
                ->icon('heroicon-o-check-badge')
                ->visible(fn () => $this->record->status === 'menunggu_pengecekan' && $user->hasAnyRole(['super_admin', 'admin_unit']))
                ->requiresConfirmation()
                ->modalDescription('Pastikan Anda telah memeriksa fisik barang. Stok ini akan dikembalikan ke dalam inventaris.')
                ->action(function () use ($user) {
                    // Kembalikan stok
                    foreach ($this->record->details as $detail) {
                        $barang = Barang::find($detail->barang_id);
                        
                        StockTransaction::create([
                            'barang_id' => $barang->id,
                            'type' => 'in',
                            'quantity' => $detail->jumlah_pinjam,
                            'stok_sebelum_transaksi' => $barang->stok_saat_ini,
                            'stok_setelah_transaksi' => $barang->stok_saat_ini + $detail->jumlah_pinjam,
                            'remarks' => "Dikembalikan (Baik): " . $this->record->nomor_peminjaman,
                            'created_by' => $user->id,
                        ]);

                        $barang->increment('stok_saat_ini', $detail->jumlah_pinjam);
                    }

                    $this->record->update([
                        'status' => 'dikembalikan_baik',
                        'tanggal_kembali' => now(),
                    ]);

                    Notification::make()->title('Pengembalian diterima. Stok dipulihkan.')->success()->send();
                }),

            Actions\Action::make('terima_rusak')
                ->label('Terima & Kondisi Rusak')
                ->color('danger')
                ->icon('heroicon-o-exclamation-circle')
                ->visible(fn () => $this->record->status === 'menunggu_pengecekan' && $user->hasAnyRole(['super_admin', 'admin_unit']))
                ->requiresConfirmation()
                ->modalDescription('PERINGATAN: Barang dinyatakan rusak. Stok TIDAK AKAN DIPULIHKAN (tidak bertambah kembali) agar tidak bisa dipinjam selanjutnya.')
                ->action(function () use ($user) {
                    // Tidak mengembalikan stok. Catat bahwa ada kehilangan/kerusakan fisik.
                    foreach ($this->record->details as $detail) {
                        $barang = Barang::find($detail->barang_id);
                        
                        StockTransaction::create([
                            'barang_id' => $barang->id,
                            'type' => 'opname',
                            'quantity' => 0, // No stock restored
                            'stok_sebelum_transaksi' => $barang->stok_saat_ini,
                            'stok_setelah_transaksi' => $barang->stok_saat_ini,
                            'remarks' => "Insiden Pengembalian Rusak: " . $this->record->nomor_peminjaman,
                            'created_by' => $user->id,
                        ]);
                    }

                    $this->record->update([
                        'status' => 'dikembalikan_rusak',
                        'tanggal_kembali' => now(),
                    ]);

                    Notification::make()->title('Pengembalian diterima sebagai RUSAK. Aset dicatat.')->success()->send();
                }),

            Actions\DeleteAction::make(),
        ];
    }
}
