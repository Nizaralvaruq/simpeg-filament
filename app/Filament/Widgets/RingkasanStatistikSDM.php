<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Modules\Kepegawaian\Models\DataInduk;
use Modules\Leave\Models\LeaveRequest;
use Modules\Resign\Models\Resign;
use Carbon\Carbon;

class RingkasanStatistikSDM extends BaseWidget
{
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        /** @var \App\Models\User $user */
        $user = \Illuminate\Support\Facades\Auth::user();

        return $user && $user->can('View:RingkasanStatistikSDM');
    }

    protected function getColumns(): int
    {
        return 3;
    }

    protected function getStats(): array
    {
        // 1. Employee Growth Logic
        /** @var \App\Models\User $user */
        $user = \Illuminate\Support\Facades\Auth::user();

        $employeeQuery = DataInduk::whereIn('status', ['aktif', 'Aktif']);
        $leaveQuery = LeaveRequest::where('status', 'pending');
        $resignQuery = Resign::where('status', 'diajukan');

        if (!$user->hasAnyRole(['super_admin', 'ketua_psdm', 'kepala_sekolah'])) {
            $unitIds = $user->employee?->units->pluck('id')->toArray() ?? [];
            if (!empty($unitIds)) {
                $employeeQuery->whereHas('units', fn($q) => $q->whereIn('units.id', $unitIds));
                $leaveQuery->whereHas('employee.units', fn($q) => $q->whereIn('units.id', $unitIds));
                $resignQuery->whereHas('employee.units', fn($q) => $q->whereIn('units.id', $unitIds));
            } else {
                // User has no unit but is not super_admin (edge case)
                $employeeQuery->whereRaw('1=0');
                $leaveQuery->whereRaw('1=0');
                $resignQuery->whereRaw('1=0');
            }
        }

        $currentMonthEmployees = (clone $employeeQuery)->count();
        $lastMonthEmployees = (clone $employeeQuery)
            ->where('created_at', '<', now()->startOfMonth())
            ->count();

        $employeeGrowth = $currentMonthEmployees - $lastMonthEmployees;

        // Hitung Gender breakdown
        $maleCount = (clone $employeeQuery)->where('jenis_kelamin', 'Laki-laki')->count();
        $femaleCount = (clone $employeeQuery)->where('jenis_kelamin', 'Perempuan')->count();

        $employeeDescription = "{$maleCount} Laki-laki / {$femaleCount} Perempuan";
        $employeeIcon = 'heroicon-m-users';
        $employeeColor = 'primary';

        // 2. Pending Leave Logic
        $pendingLeaves = $leaveQuery->count();

        // 3. Pending Resign Logic
        $pendingResigns = $resignQuery->count();


        return [
            Stat::make('Total Pegawai Aktif', $currentMonthEmployees)
                ->description($employeeDescription)
                ->descriptionIcon($employeeIcon)
                ->chart([$lastMonthEmployees, $currentMonthEmployees]) // Simple trend line
                ->color($employeeColor),

            Stat::make('Pengajuan Cuti Pending', $pendingLeaves)
                ->description('Membutuhkan persetujuan segera')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingLeaves > 0 ? 'warning' : 'success'),

            Stat::make('Pengajuan Resign Pending', $pendingResigns)
                ->description($pendingResigns > 0 ? 'Ada pegawai ingin mengundurkan diri' : 'Tidak ada pengajuan')
                ->descriptionIcon('heroicon-m-user-minus')
                ->color($pendingResigns > 0 ? 'danger' : 'success'),
        ];
    }
}
