<?php

namespace Modules\Presensi\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\DatePicker;
use Modules\Presensi\Models\Absensi;
use Modules\Presensi\Filament\Pages\LocationAttendance;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;


class MyAttendance extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-calendar-days';

    protected string $view = 'presensi::filament.pages.my-attendance';

    protected static string | \UnitEnum | null $navigationGroup = 'Presensi';

    protected static ?string $navigationLabel = 'Absensi Saya';

    protected static ?string $title = 'Riwayat Absensi Saya';

    protected static ?int $navigationSort = 2;

    public static function shouldRegisterNavigation(): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $user && $user->hasRole('staff');
    }


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

    public function getStats(): array
    {
        $userId = Auth::id();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $hadir = Absensi::where('user_id', $userId)
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->whereIn('status', ['hadir', 'dinas_luar'])
            ->count();

        $lateMinutes = Absensi::where('user_id', $userId)
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->get()
            ->sum('late_minutes');

        $izinSakit = Absensi::where('user_id', $userId)
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->whereIn('status', ['izin', 'sakit'])
            ->count();

        return [
            'hadir' => $hadir,
            'late' => $lateMinutes,
            'izin_sakit' => $izinSakit,
        ];
    }
}
