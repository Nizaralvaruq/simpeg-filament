<?php

namespace Modules\Presensi\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Modules\Presensi\Models\Absensi;
use Illuminate\Support\Facades\Auth;
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
                    }),
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
                    ->suffix(' mnt')
                    ->color(fn($state) => $state > 0 ? 'danger' : 'success')
                    ->alignCenter(),
                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->wrap()
                    ->limit(50),
            ]);
    }

    public function getStats(): array
    {
        $userId = Auth::id();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $hadir = Absensi::where('user_id', $userId)
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->where('status', 'hadir')
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
