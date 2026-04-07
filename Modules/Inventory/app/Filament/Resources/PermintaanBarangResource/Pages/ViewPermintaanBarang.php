<?php

namespace Modules\Inventory\Filament\Resources\PermintaanBarangResource\Pages;

use Modules\Inventory\Filament\Resources\PermintaanBarangResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;

class ViewPermintaanBarang extends ViewRecord
{
    protected static string $resource = PermintaanBarangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Informasi Permintaan')
                ->schema([
                    Grid::make(3)->schema([
                        TextEntry::make('nomor_permintaan')
                            ->label('Nomor Permintaan')
                            ->copyable()
                            ->weight('bold'),

                        TextEntry::make('user.name')
                            ->label('Pemohon'),

                        TextEntry::make('unit.name')
                            ->label('Unit / Jenjang')
                            ->badge()
                            ->color('info'),

                        TextEntry::make('tanggal_permintaan')
                            ->label('Tanggal Permintaan')
                            ->date('d M Y'),

                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn($state) => match ($state) {
                                'draft'     => 'gray',
                                'diajukan'  => 'warning',
                                'disetujui' => 'success',
                                'ditolak'   => 'danger',
                                'selesai'   => 'primary',
                                default     => 'gray',
                            })
                            ->formatStateUsing(fn($state) => match ($state) {
                                'draft'     => 'Draft',
                                'diajukan'  => 'Diajukan',
                                'disetujui' => 'Disetujui',
                                'ditolak'   => 'Ditolak',
                                'selesai'   => 'Selesai',
                                default     => $state,
                            }),

                        TextEntry::make('approvedBy.name')
                            ->label('Diproses Oleh')
                            ->placeholder('-'),

                        TextEntry::make('catatan')
                            ->label('Catatan')
                            ->columnSpanFull()
                            ->placeholder('-'),

                        TextEntry::make('alasan_penolakan')
                            ->label('Alasan Penolakan')
                            ->columnSpanFull()
                            ->color('danger')
                            ->placeholder('-')
                            ->visible(fn($record) => !empty($record->alasan_penolakan)),
                    ]),
                ]),

            Section::make('Detail Barang yang Diminta')
                ->schema([
                    RepeatableEntry::make('details')
                        ->label('')
                        ->schema([
                            TextEntry::make('barang.nama_barang')
                                ->label('Nama Barang'),

                            TextEntry::make('jumlah_diminta')
                                ->label('Diminta')
                                ->numeric(),

                            TextEntry::make('jumlah_disetujui')
                                ->label('Disetujui')
                                ->numeric()
                                ->placeholder('0'),

                            TextEntry::make('keterangan')
                                ->label('Keterangan')
                                ->placeholder('-'),
                        ])
                        ->columns(4)
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
