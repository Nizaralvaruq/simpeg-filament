<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Modules\Presensi\Models\Absensi;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class StatistikPegawaiTerlambat extends BaseWidget
{
    protected static ?int $sort = 20;
    protected int | string | array $columnSpan = 1;
    protected ?string $pollingInterval = '60s';

    public static function canView(): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return $user && $user->can('View:StatistikPegawaiTerlambat');
    }

    protected function getStats(): array
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $today = Carbon::today();
        $settings = \Modules\MasterData\Models\Setting::get();
        $standardWorkTime = $settings->office_start_time;
        $tolerance = $settings->late_tolerance ?? 0;

        $effectiveLateTime = Carbon::parse($standardWorkTime)->addMinutes($tolerance)->format('H:i:s');

        $queryAbsensi = Absensi::whereDate('tanggal', $today)
            ->where('status', 'hadir')
            ->whereNotNull('jam_masuk');

        $isGlobalAdmin = $user && $user->hasAnyRole(['super_admin', 'ketua_psdm', 'kepala_sekolah']);
        $labelContext = 'Global';

        if (!$isGlobalAdmin && $user->hasAnyRole(['admin_unit', 'koor_jenjang'])) {
            $labelContext = 'Unit';
            if ($user->employee && $user->employee->units->isNotEmpty()) {
                $unitIds = $user->employee->units->pluck('id');
                $queryAbsensi->whereHas('user.employee.units', fn($q) => $q->whereIn('units.id', $unitIds));
            } else {
                $queryAbsensi->whereRaw('1 = 0');
            }
        }

        $isWorkingDay = Absensi::isWorkingDay($today);
        $lateCount = 0;
        $totalPresentToday = 0;
        $latePercentage = 0;

        if ($isWorkingDay) {
            $lateCount = (clone $queryAbsensi)->where('jam_masuk', '>', $effectiveLateTime)->count();
            $totalPresentToday = $queryAbsensi->count();
            $latePercentage = $totalPresentToday > 0 ? ($lateCount / $totalPresentToday) * 100 : 0;
        }

        $lateColor = 'success';
        if ($isWorkingDay) {
            if ($latePercentage > 20) $lateColor = 'danger';
            elseif ($latePercentage > 10) $lateColor = 'warning';
        } else {
            $lateColor = 'gray';
        }

        return [
            Stat::make("Pegawai Terlambat ({$labelContext})", $isWorkingDay ? $lateCount : 'Libur')
                ->description($isWorkingDay
                    ? number_format($latePercentage, 1) . '% dari ' . $totalPresentToday . ' kehadiran'
                    : 'Hari ini bukan hari kerja')
                ->descriptionIcon('heroicon-m-clock')
                ->color($lateColor)
                ->chart($this->getLateChartData($user, $effectiveLateTime)),
        ];
    }

    protected function getLateChartData($user, $effectiveLateTime): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $query = Absensi::whereDate('tanggal', $date)
                ->where('status', 'hadir')
                ->whereNotNull('jam_masuk')
                ->where('jam_masuk', '>', $effectiveLateTime);

            if (!Absensi::isWorkingDay($date)) {
                $data[] = 0;
                continue;
            }

            if (
                !$user->hasAnyRole(['super_admin', 'ketua_psdm', 'kepala_sekolah'])
                && $user->hasAnyRole(['admin_unit', 'koor_jenjang'])
            ) {
                if ($user->employee && $user->employee->units->isNotEmpty()) {
                    $unitIds = $user->employee->units->pluck('id');
                    $query->whereHas('user.employee.units', fn($q) => $q->whereIn('units.id', $unitIds));
                }
            }
            $data[] = $query->count();
        }
        return $data;
    }
}
