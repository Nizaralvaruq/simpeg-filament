<?php

namespace Modules\Presensi\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Modules\Presensi\Models\Absensi;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AbsensiStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $userId = Auth::id();
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Count attendance by status for current month
        $hadir = Absensi::where('user_id', $userId)
            ->whereMonth('tanggal', $currentMonth)
            ->whereYear('tanggal', $currentYear)
            ->where('status', 'hadir')
            ->count();

        $izin = Absensi::where('user_id', $userId)
            ->whereMonth('tanggal', $currentMonth)
            ->whereYear('tanggal', $currentYear)
            ->where('status', 'izin')
            ->count();

        $sakit = Absensi::where('user_id', $userId)
            ->whereMonth('tanggal', $currentMonth)
            ->whereYear('tanggal', $currentYear)
            ->where('status', 'sakit')
            ->count();

        $alpha = Absensi::where('user_id', $userId)
            ->whereMonth('tanggal', $currentMonth)
            ->whereYear('tanggal', $currentYear)
            ->where('status', 'alpha')
            ->count();

        // Check if already absent today
        $todayAbsent = Absensi::where('user_id', $userId)
            ->whereDate('tanggal', Carbon::today())
            ->exists();

        return [
            Stat::make('Hadir Bulan Ini', $hadir)
                ->description('Total kehadiran')
                ->color('success'),

            Stat::make('Izin', $izin)
                ->description('Total izin')
                ->color('warning'),

            Stat::make('Sakit', $sakit)
                ->description('Total sakit')
                ->color('danger'),

            Stat::make('Alpha', $alpha)
                ->description('Total alpha')
                ->color('gray'),
        ];
    }
}
