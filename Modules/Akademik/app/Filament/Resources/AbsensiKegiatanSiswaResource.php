<?php

namespace Modules\Akademik\Filament\Resources;

use Modules\Akademik\Models\AbsensiKegiatanSiswa;
use Modules\Akademik\Filament\Resources\AbsensiKegiatanSiswaResource\Pages;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Schemas\Schema;

class AbsensiKegiatanSiswaResource extends Resource
{
    protected static ?string $model = AbsensiKegiatanSiswa::class;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-clipboard-document-check';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Akademik';
    }

    public static function getNavigationSort(): ?int
    {
        return 4;
    }

    public static function getModelLabel(): string
    {
        return 'Kehadiran Siswa';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Kehadiran Kegiatan Siswa';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('kegiatan_id')
                    ->relationship('kegiatan', 'nama_kegiatan')
                    ->required(),
                Forms\Components\Select::make('siswa_id')
                    ->relationship('siswa', 'nama_lengkap')
                    ->required(),
                Forms\Components\DateTimePicker::make('jam_absen')
                    ->default(now())
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options([
                        'hadir' => 'Hadir',
                        'tidak_hadir' => 'Tidak Hadir',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('keterangan')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('jam_absen', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('kegiatan.nama_kegiatan')
                    ->label('Kegiatan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('siswa.nama_lengkap')
                    ->label('Siswa')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jam_absen')
                    ->label('Waktu Absen')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'hadir' => 'success',
                        'tidak_hadir' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('keterangan')
                    ->limit(20),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('kegiatan_id')
                    ->label('Kegiatan')
                    ->relationship('kegiatan', 'nama_kegiatan'),
            ])
            ->recordActions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->toolbarActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageAbsensiKegiatanSiswas::route('/'),
        ];
    }
}
