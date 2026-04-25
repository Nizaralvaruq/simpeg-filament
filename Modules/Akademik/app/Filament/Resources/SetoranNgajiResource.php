<?php

namespace Modules\Akademik\Filament\Resources;

use Modules\Akademik\Models\SetoranNgaji;
use Modules\Akademik\Filament\Resources\SetoranNgajiResource\Pages;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class SetoranNgajiResource extends Resource
{
    protected static ?string $model = SetoranNgaji::class;

    protected static ?int $navigationSort = 2;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-book-open';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Akademik';
    }

    public static function getModelLabel(): string
    {
        return 'Setoran Ngaji';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Data Setoran Ngaji';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Identitas Setoran')
                    ->description('Data siswa dan waktu setoran dilakukan.')
                    ->schema([
                        Forms\Components\Select::make('siswa_id')
                            ->label('Siswa')
                            ->relationship('siswa', 'nama_lengkap')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->nis} — {$record->nama_lengkap}")
                            ->searchable(['nis', 'nama_lengkap'])
                            ->preload()
                            ->required()
                            ->autofocus()
                            ->helperText('Ketik atau gunakan scanner barcode untuk mencari siswa berdasarkan NIS.'),

                        Forms\Components\Hidden::make('guru_id')
                            ->default(fn () => Auth::id()),

                        Forms\Components\DatePicker::make('tanggal_setoran')
                            ->label('Tanggal Setoran')
                            ->default(now())
                            ->required(),
                    ])->columns(2),

                \Filament\Schemas\Components\Section::make('Detail Materi Setoran')
                    ->description('Isi jenis setoran, materi bacaan, dan penilaian dari guru.')
                    ->schema([
                        Forms\Components\Select::make('jenis_setoran')
                            ->label('Jenis Setoran')
                            ->options([
                                "Al-Qur'an"      => "📖 Al-Qur'an",
                                "Jilid/Iqro/Ummi"=> "📗 Jilid / Iqro / Ummi",
                                "Hafalan"        => "⭐ Hafalan",
                            ])
                            ->required(),

                        Forms\Components\Select::make('predikat_nilai')
                            ->label('Predikat Nilai')
                            ->options([
                                'A' => 'A — Sangat Lancar',
                                'B' => 'B — Lancar',
                                'C' => 'C — Kurang Lancar / Mengulang',
                            ])
                            ->required(),

                        Forms\Components\TextInput::make('nama_materi')
                            ->label('Nama Surah / Jilid / Hafalan')
                            ->required()
                            ->placeholder('Misal: Al-Baqarah / Jilid 4 / Surah Al-Ikhlas'),

                        Forms\Components\TextInput::make('ayat_halaman')
                            ->label('Ayat / Halaman')
                            ->nullable()
                            ->placeholder('Misal: Ayat 1-10 / Halaman 20'),

                        Forms\Components\Textarea::make('catatan_guru')
                            ->label('Catatan Guru')
                            ->nullable()
                            ->columnSpanFull()
                            ->rows(3)
                            ->placeholder('Tuliskan catatan atau arahan untuk perbaikan siswa...'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('tanggal_setoran')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable()
                    ->weight(\Filament\Support\Enums\FontWeight::Bold),

                Tables\Columns\TextColumn::make('siswa.nama_lengkap')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable()
                    ->description(fn (SetoranNgaji $r) => 'NIS: ' . ($r->siswa->nis ?? '-')),

                Tables\Columns\TextColumn::make('jenis_setoran')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        "Al-Qur'an"       => 'success',
                        'Jilid/Iqro/Ummi' => 'info',
                        'Hafalan'         => 'warning',
                        default           => 'gray',
                    }),

                Tables\Columns\TextColumn::make('nama_materi')
                    ->label('Materi')
                    ->searchable()
                    ->description(fn (SetoranNgaji $r) => $r->ayat_halaman ?? ''),

                Tables\Columns\TextColumn::make('predikat_nilai')
                    ->label('Nilai')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'A'     => 'success',
                        'B'     => 'info',
                        'C'     => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('guru.name')
                    ->label('Guru')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('status_notifikasi')
                    ->label('WA Dikirim')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-clock')
                    ->trueColor('success')
                    ->falseColor('warning'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('siswa_id')
                    ->label('Filter Siswa')
                    ->relationship('siswa', 'nama_lengkap')
                    ->searchable(),

                Tables\Filters\SelectFilter::make('jenis_setoran')
                    ->label('Jenis')
                    ->options([
                        "Al-Qur'an"       => "Al-Qur'an",
                        'Jilid/Iqro/Ummi' => 'Jilid/Iqro/Ummi',
                        'Hafalan'         => 'Hafalan',
                    ]),

                Tables\Filters\SelectFilter::make('predikat_nilai')
                    ->label('Nilai')
                    ->options(['A' => 'A', 'B' => 'B', 'C' => 'C']),

                Tables\Filters\Filter::make('belum_notif')
                    ->label('Belum Kirim WA')
                    ->query(fn ($query) => $query->where('status_notifikasi', false))
                    ->toggle(),
            ])
            ->recordActions([
                \Filament\Actions\ViewAction::make(),
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->toolbarActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSetoranNgajis::route('/'),
            'create' => Pages\CreateSetoranNgaji::route('/create'),
            'edit'   => Pages\EditSetoranNgaji::route('/{record}/edit'),
            'view'   => Pages\ViewSetoranNgaji::route('/{record}'),
        ];
    }
}
