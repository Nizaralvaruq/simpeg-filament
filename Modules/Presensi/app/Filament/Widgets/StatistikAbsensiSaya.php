<?php

namespace Modules\Presensi\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Modules\Presensi\Models\Absensi;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class StatistikAbsensiSaya extends BaseWidget
{
    protected static ?int $sort = 100;
    public static function canView(): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return $user && $user->can('View:StatistikAbsensiSaya') && !$user->hasRole('super_admin');
    }
    protected function getStats(): array
    {
        $userId = Auth::id();
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $stats = Absensi::where('user_id', $userId)
            ->whereMonth('tanggal', $currentMonth)
            ->whereYear('tanggal', $currentYear)
            ->selectRaw("
                SUM(CASE WHEN status = 'hadir' THEN 1 ELSE 0 END) as hadir,
                SUM(CASE WHEN status = 'izin' THEN 1 ELSE 0 END) as izin,
                SUM(CASE WHEN status = 'sakit' THEN 1 ELSE 0 END) as sakit,
                SUM(CASE WHEN status = 'alpha' THEN 1 ELSE 0 END) as alpha
            ")
            ->first();

        return [
            Stat::make('Hadir Bulan Ini', $stats->hadir ?? 0)
                ->description('Total kehadiran')
                ->color('success'),

            Stat::make('Izin', $stats->izin ?? 0)
                ->description('Total izin')
                ->color('warning'),

            Stat::make('Sakit', $stats->sakit ?? 0)
                ->description('Total sakit')
                ->color('danger'),

            Stat::make('Alpha', $stats->alpha ?? 0)
                ->description('Total alpha')
                ->color('gray'),
        ];
    }
}
