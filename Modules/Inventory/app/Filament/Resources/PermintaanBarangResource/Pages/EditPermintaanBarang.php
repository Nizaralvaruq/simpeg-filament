<?php

namespace Modules\Inventory\Filament\Resources\PermintaanBarangResource\Pages;

use Modules\Inventory\Filament\Resources\PermintaanBarangResource;
use Modules\Inventory\Models\Barang;
use Modules\Inventory\Models\StockTransaction;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class EditPermintaanBarang extends EditRecord
{
    protected static string $resource = PermintaanBarangResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $isAdmin = $user->hasAnyRole(['super_admin', 'ketua_psdm', 'admin_unit', 'koor_jenjang', 'kepala_sekolah']);

        return [
            // Tombol Ajukan — hanya untuk pemohon (draft)
            Actions\Action::make('ajukan')
                ->label('Ajukan Permintaan')
                ->icon('heroicon-o-paper-airplane')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Ajukan Permintaan?')
                ->modalDescription('Permintaan akan dikirim ke admin untuk diproses.')
                ->visible(fn() => $this->record->status === 'draft' && Auth::id() === $this->record->user_id)
                ->action(function () {
                    $this->record->update(['status' => 'diajukan']);

                    Notification::make()
                        ->title('Permintaan Barang Baru')
                        ->body("Permintaan {$this->record->nomor_permintaan} dari {$this->record->user->name} telah diajukan.")
                        ->success()
                        ->sendToDatabase(User::role(['super_admin', 'admin_unit'])->get());

                    Notification::make()
                        ->title('Permintaan berhasil diajukan!')
                        ->success()
                        ->send();

                    $this->refreshFormData(['status']);
                }),

            // Tombol Setujui — hanya untuk admin, saat status diajukan
            Actions\Action::make('setujui')
                ->label('Setujui')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Setujui Permintaan?')
                ->visible(fn() => $this->record->status === 'diajukan' && $isAdmin)
                ->action(function () {
                    $this->record->update([
                        'status'      => 'disetujui',
                        'approved_by' => Auth::id(),
                        'approved_at' => now(),
                    ]);

                    Notification::make()
                        ->title('Permintaan Disetujui')
                        ->body("Permintaan {$this->record->nomor_permintaan} Anda telah disetujui.")
                        ->success()
                        ->sendToDatabase($this->record->user);

                    Notification::make()
                        ->title('Permintaan berhasil disetujui!')
                        ->success()
                        ->send();

                    $this->refreshFormData(['status', 'approved_by', 'approved_at']);
                }),

            // Tombol Tolak — hanya untuk admin, saat status diajukan
            Actions\Action::make('tolak')
                ->label('Tolak')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->form([
                    Forms\Components\Textarea::make('alasan_penolakan')
                        ->label('Alasan Penolakan')
                        ->required()
                        ->rows(3),
                ])
                ->visible(fn() => $this->record->status === 'diajukan' && $isAdmin)
                ->action(function (array $data) {
                    $this->record->update([
                        'status'           => 'ditolak',
                        'alasan_penolakan' => $data['alasan_penolakan'],
                    ]);

                    Notification::make()
                        ->title('Permintaan Ditolak')
                        ->body("Permintaan {$this->record->nomor_permintaan} ditolak. Alasan: {$data['alasan_penolakan']}")
                        ->danger()
                        ->sendToDatabase($this->record->user);

                    Notification::make()
                        ->title('Permintaan berhasil ditolak')
                        ->success()
                        ->send();

                    $this->refreshFormData(['status', 'alasan_penolakan']);
                }),

            // Tombol Selesaikan — hanya super_admin / admin_unit, saat disetujui
            Actions\Action::make('selesaikan')
                ->label('Selesaikan & Keluarkan Stok')
                ->icon('heroicon-o-check-badge')
                ->color('primary')
                ->requiresConfirmation()
                ->modalHeading('Selesaikan Permintaan?')
                ->modalDescription('Stok barang akan dikurangi secara otomatis sesuai jumlah yang disetujui.')
                ->visible(fn() => $this->record->status === 'disetujui' && $user->hasAnyRole(['super_admin', 'admin_unit']))
                ->action(function () {
                    // Validasi stok mencukupi
                    $errors = [];
                    foreach ($this->record->details as $detail) {
                        $barang = Barang::find($detail->barang_id);
                        $jumlah = $detail->jumlah_disetujui ?? $detail->jumlah_diminta;
                        if ($barang && $barang->stok_saat_ini < $jumlah) {
                            $errors[] = "Stok {$barang->nama_barang} tidak mencukupi (tersedia: {$barang->stok_saat_ini}, dibutuhkan: {$jumlah})";
                        }
                    }

                    if (!empty($errors)) {
                        Notification::make()
                            ->title('Stok tidak mencukupi!')
                            ->body(implode("\n", $errors))
                            ->danger()
                            ->send();
                        return;
                    }

                    DB::transaction(function () {
                        foreach ($this->record->details as $detail) {
                            $barang = Barang::find($detail->barang_id);
                            $jumlah = $detail->jumlah_disetujui ?? $detail->jumlah_diminta;

                            if ($barang && $jumlah > 0) {
                                StockTransaction::create([
                                    'barang_id'      => $barang->id,
                                    'type'           => 'out',
                                    'quantity'       => $jumlah,
                                    'reference_type' => 'PermintaanBarang',
                                    'reference_id'   => $this->record->id,
                                    'remarks'        => "Pengeluaran untuk {$this->record->nomor_permintaan} — {$this->record->unit?->name}",
                                    'created_by'     => Auth::id(),
                                ]);

                                $barang->decrement('stok_saat_ini', $jumlah);
                            }
                        }

                        $this->record->update(['status' => 'selesai']);
                    });

                    Notification::make()
                        ->title('Permintaan Selesai')
                        ->body("Permintaan {$this->record->nomor_permintaan} telah diselesaikan.")
                        ->success()
                        ->sendToDatabase($this->record->user);

                    Notification::make()
                        ->title('Permintaan berhasil diselesaikan!')
                        ->success()
                        ->send();

                    $this->refreshFormData(['status']);
                }),

            Actions\DeleteAction::make(),
        ];
    }
}
