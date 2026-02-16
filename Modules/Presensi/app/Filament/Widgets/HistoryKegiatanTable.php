<?php

namespace Modules\Presensi\Filament\Widgets;

use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Table;
use Modules\Presensi\Models\AbsensiKegiatan;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;

class HistoryKegiatanTable extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                AbsensiKegiatan::where('user_id', Auth::id())
                    ->with('kegiatan')
                    ->latest('jam_absen')
            )
            ->columns([
                TextColumn::make('kegiatan.nama_kegiatan')
                    ->label('Nama Kegiatan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('jam_absen')
                    ->label('Waktu Absen')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                TextColumn::make('kegiatan.lokasi')
                    ->label('Lokasi')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'hadir' => 'success',
                        'tidak_hadir' => 'danger',
                        'izin' => 'warning',
                        'sakit' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => str($state)->replace('_', ' ')->title()),
                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->wrap()
                    ->limit(50)
                    ->placeholder('-'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'hadir' => 'Hadir',
                        'tidak_hadir' => 'Tidak Hadir',
                    ]),
                Filter::make('tanggal')
                    ->label('Tanggal')
                    ->schema([
                        DatePicker::make('dari_tanggal')->label('Dari Tanggal'),
                        DatePicker::make('sampai_tanggal')->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari_tanggal'],
                                fn(Builder $query, $date): Builder => $query->whereDate('jam_absen', '>=', $date),
                            )
                            ->when(
                                $data['sampai_tanggal'],
                                fn(Builder $query, $date): Builder => $query->whereDate('jam_absen', '<=', $date),
                            );
                    })
            ]);
    }
}
