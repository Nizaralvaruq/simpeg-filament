<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Modules\Presensi\Models\Absensi;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LateEmployeesTodayWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'md';
    protected ?string $pollingInterval = '60s'; // Auto-refresh setiap 1 menit

    protected function getStats(): array
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $today = Carbon::today();

        // Jam kerja standar (bisa disesuaikan)
        $standardWorkTime = '07:00:00';

        // Base query untuk absensi hari ini yang hadirz
        $query = Absensi::whereDate('tanggal', $today)
            ->where('status', 'hadir')
            ->whereNotNull('jam_masuk');

        // Filter berdasarkan role
        $isGlobalAdmin = $user && $user->hasAnyRole(['super_admin', 'ketua_psdm', 'kepala_sekolah']);
        $labelContext = 'Global';

        if (!$isGlobalAdmin && $user->hasAnyRole(['admin_unit', 'koor_jenjang'])) {
            $labelContext = 'Unit';
            if ($user->employee && $user->employee->units->isNotEmpty()) {
                $unitIds = $user->employee->units->pluck('id');
                $query->whereHas('user.employee.units', fn($q) => $q->whereIn('units.id', $unitIds));
            } else {
                return [
                    Stat::make('Pegawai Terlambat', '0')
                        ->description('Hari ini')
                        ->color('gray'),
                ];
            }
        }

        // Hitung yang terlambat (jam_masuk > jam standar)
        $lateCount = (clone $query)->where('jam_masuk', '>', $standardWorkTime)->count();
        $totalToday = $query->count();
        $latePercentage = $totalToday > 0 ? ($lateCount / $totalToday) * 100 : 0;

        // Tentukan warna berdasarkan persentase
        $color = 'success';
        if ($latePercentage > 20) $color = 'danger';
        elseif ($latePercentage > 10) $color = 'warning';

        return [
            Stat::make("Pegawai Terlambat ({$labelContext})", $lateCount)
                ->description(number_format($latePercentage, 1) . '% dari ' . $totalToday . ' kehadiran hari ini')
                ->descriptionIcon('heroicon-m-clock')
                ->color($color)
                ->chart($this->getLateChartData()),
        ];
    }

    protected function getLateChartData(): array
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $standardWorkTime = '07:00:00';

        // Ambil data 7 hari terakhir untuk trend
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $query = Absensi::whereDate('tanggal', $date)
                ->where('status', 'hadir')
                ->whereNotNull('jam_masuk')
                ->where('jam_masuk', '>', $standardWorkTime);

            // Filter unit jika bukan global admin
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
