<?php

namespace Modules\Presensi\Filament\Widgets;

use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Table;
use Modules\Presensi\Models\Absensi;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;

class HistoryAbsensiTable extends BaseWidget
{
    protected static ?int $sort = 10;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Absensi::where('user_id', Auth::id())
                    ->orderBy('tanggal', 'desc')
            )
            ->columns([
                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'hadir' => 'success',
                        'izin' => 'warning',
                        'sakit' => 'danger',
                        'alpha' => 'gray',
                        'dinas_luar' => 'info',
                        'cuti' => 'primary',
                    })
                    ->formatStateUsing(fn(string $state): string => str($state)->replace('_', ' ')->title()),
                TextColumn::make('jam_masuk')
                    ->label('Masuk')
                    ->time('H:i')
                    ->placeholder('--:--'),
                TextColumn::make('jam_keluar')
                    ->label('Pulang')
                    ->time('H:i')
                    ->placeholder('--:--'),
                TextColumn::make('late_minutes')
                    ->label('Terlambat')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state > 0 ? "{$state} mnt" : '0 mnt')
                    ->color(fn($state) => $state > 0 ? 'danger' : 'success')
                    ->alignCenter(),
                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->wrap()
                    ->limit(30)
                    ->placeholder('Tidak ada catatan'),
                ImageColumn::make('foto_verifikasi')
                    ->label('Bukti')
                    ->circular()
                    ->width(30)
                    ->visibility(fn($record) => $record->status === 'dinas_luar'),
                TextColumn::make('alamat_lokasi')
                    ->label('Lokasi')
                    ->limit(20)
                    ->tooltip(fn($state) => $state)
                    ->placeholder('-')
                    ->url(fn($record) => $record->latitude ? "https://www.google.com/maps?q={$record->latitude},{$record->longitude}" : null)
                    ->openUrlInNewTab()
                    ->color('primary'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'hadir' => 'Hadir',
                        'dinas_luar' => 'Dinas Luar',
                        'izin' => 'Izin',
                        'sakit' => 'Sakit',
                        'cuti' => 'Cuti',
                        'alpha' => 'Alpha',
                    ]),
                Filter::make('tanggal')
                    ->schema([
                        DatePicker::make('dari_tanggal')->label('Dari Tanggal'),
                        DatePicker::make('sampai_tanggal')->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari_tanggal'],
                                fn(Builder $query, $date): Builder => $query->whereDate('tanggal', '>=', $date),
                            )
                            ->when(
                                $data['sampai_tanggal'],
                                fn(Builder $query, $date): Builder => $query->whereDate('tanggal', '<=', $date),
                            );
                    })
            ]);
    }
}
