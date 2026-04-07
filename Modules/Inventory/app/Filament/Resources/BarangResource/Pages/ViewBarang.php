<?php

namespace Modules\Inventory\Filament\Resources\BarangResource\Pages;

use Modules\Inventory\Filament\Resources\BarangResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;

class ViewBarang extends ViewRecord
{
    protected static string $resource = BarangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi Barang')
                ->schema([
                    Grid::make(4)->schema([
                        ImageEntry::make('foto')
                            ->label('Foto')
                            ->disk('public')
                            ->height(100)
                            ->circular(),

                        Grid::make(2)->schema([
                            TextEntry::make('kode_barang')
                                ->label('Kode Barang')
                                ->copyable()
                                ->weight('bold'),

                            TextEntry::make('nama_barang')
                                ->label('Nama Barang')
                                ->weight('bold'),

                            TextEntry::make('kategori.nama_kategori')
                                ->label('Kategori')
                                ->badge()
                                ->color('info'),

                            TextEntry::make('jenis')
                                ->label('Jenis')
                                ->badge()
                                ->color(fn($state) => match ($state) {
                                    'Aset' => 'primary',
                                    'BHP'  => 'warning',
                                    default => 'gray',
                                }),
                        ])->columnSpan(2),

                        Grid::make(2)->schema([
                            TextEntry::make('stok_saat_ini')
                                ->label('Stok Saat Ini')
                                ->weight('bold')
                                ->color(fn($record) =>
                                    $record->minimum_stok > 0 && $record->stok_saat_ini <= $record->minimum_stok
                                        ? 'danger' : 'success'
                                ),

                            TextEntry::make('minimum_stok')
                                ->label('Minimum Stok'),

                            TextEntry::make('lokasi_ruangan')
                                ->label('Lokasi / Ruangan')
                                ->placeholder('-'),

                            IconEntry::make('is_active')
                                ->label('Status Aktif')
                                ->boolean(),
                        ])->columnSpan(1),
                    ]),

                    TextEntry::make('spesifikasi')
                        ->label('Spesifikasi')
                        ->columnSpanFull()
                        ->placeholder('-'),
                ]),

            Section::make('Riwayat Transaksi Stok')
                ->schema([
                    RepeatableEntry::make('stockTransactions')
                        ->label('')
                        ->schema([
                            TextEntry::make('created_at')
                                ->label('Tanggal')
                                ->dateTime('d M Y H:i'),

                            TextEntry::make('type')
                                ->label('Jenis')
                                ->badge()
                                ->formatStateUsing(fn($state) => match ($state) {
                                    'in'     => 'Masuk',
                                    'out'    => 'Keluar',
                                    'opname' => 'Opname',
                                    default  => $state,
                                })
                                ->color(fn($state) => match ($state) {
                                    'in'     => 'success',
                                    'out'    => 'danger',
                                    'opname' => 'warning',
                                    default  => 'gray',
                                }),

                            TextEntry::make('quantity')
                                ->label('Jumlah')
                                ->numeric(),

                            TextEntry::make('createdBy.name')
                                ->label('Oleh'),

                            TextEntry::make('remarks')
                                ->label('Keterangan')
                                ->columnSpanFull()
                                ->placeholder('-'),
                        ])
                        ->columns(4)
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
