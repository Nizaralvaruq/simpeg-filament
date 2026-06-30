<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Modules\Kepegawaian\Models\DataInduk;
use Modules\Leave\Models\LeaveRequest;
use Modules\Resign\Models\Resign;

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
        /** @var \App\Models\User $user */
        $user = \Illuminate\Support\Facades\Auth::user();
        $isGlobalAdmin = $user->hasAnyRole(['super_admin', 'ketua_psdm']);
        $unitIds = $isGlobalAdmin ? [] : ($user->employee?->units->pluck('id')->toArray() ?? []);

        $employeeStats = DataInduk::whereIn('status', ['aktif', 'Aktif'])
            ->when(!$isGlobalAdmin && !empty($unitIds), fn($q) => $q->whereHas('units', fn($q2) => $q2->whereIn('units.id', $unitIds)))
            ->when(!$isGlobalAdmin && empty($unitIds), fn($q) => $q->whereRaw('1=0'))
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN created_at < ? THEN 1 ELSE 0 END) as last_month,
                SUM(CASE WHEN jenis_kelamin = 'Laki-laki' THEN 1 ELSE 0 END) as male,
                SUM(CASE WHEN jenis_kelamin = 'Perempuan' THEN 1 ELSE 0 END) as female
            ", [now()->startOfMonth()->toDateTimeString()])
            ->first();

        $pendingLeaves = LeaveRequest::where('status', 'pending')
            ->when(!$isGlobalAdmin && !empty($unitIds), fn($q) => $q->whereHas('employee.units', fn($q2) => $q2->whereIn('units.id', $unitIds)))
            ->when(!$isGlobalAdmin && empty($unitIds), fn($q) => $q->whereRaw('1=0'))
            ->count();

        $pendingResigns = Resign::where('status', 'diajukan')
            ->when(!$isGlobalAdmin && !empty($unitIds), fn($q) => $q->whereHas('employee.units', fn($q2) => $q2->whereIn('units.id', $unitIds)))
            ->when(!$isGlobalAdmin && empty($unitIds), fn($q) => $q->whereRaw('1=0'))
            ->count();

        $currentMonthEmployees = $employeeStats->total ?? 0;
        $lastMonthEmployees = $employeeStats->last_month ?? 0;
        $maleCount = $employeeStats->male ?? 0;
        $femaleCount = $employeeStats->female ?? 0;

        $employeeDescription = "{$maleCount} Laki-laki / {$femaleCount} Perempuan";

        return [
            Stat::make('Total Pegawai Aktif', $currentMonthEmployees)
                ->description($employeeDescription)
                ->descriptionIcon('heroicon-m-users')
                ->chart([$lastMonthEmployees, $currentMonthEmployees])
                ->color('primary'),

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
