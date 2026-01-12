<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Modules\Kepegawaian\Models\DataInduk;
use Modules\Leave\Models\LeaveRequest;
use Modules\Resign\Models\Resign;
use Carbon\Carbon;

class HRStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        /** @var \App\Models\User $user */
        $user = \Illuminate\Support\Facades\Auth::user();
        return $user && $user->hasAnyRole(['super_admin', 'ketua_psdm']);
    }

    protected function getColumns(): int
    {
        return 3;
    }

    protected function getStats(): array
    {
        // 1. Employee Growth Logic
        $currentMonthEmployees = DataInduk::whereIn('status', ['aktif', 'Aktif'])->count();
        $lastMonthEmployees = DataInduk::whereIn('status', ['aktif', 'Aktif'])
            ->where('created_at', '<', now()->startOfMonth())
            ->count();

        $employeeGrowth = $currentMonthEmployees - $lastMonthEmployees;

        // Hitung Gender breakdown
        $maleCount = DataInduk::whereIn('status', ['aktif', 'Aktif'])->where('jenis_kelamin', 'Laki-laki')->count();
        $femaleCount = DataInduk::whereIn('status', ['aktif', 'Aktif'])->where('jenis_kelamin', 'Perempuan')->count();

        $employeeDescription = "{$maleCount} Laki-laki / {$femaleCount} Perempuan";
        $employeeIcon = 'heroicon-m-users';
        $employeeColor = 'primary';

        // 2. Pending Leave Logic
        $pendingLeaves = LeaveRequest::where('status', 'pending')->count();

        // 3. Pending Resign Logic
        $pendingResigns = Resign::where('status', 'diajukan')->count();


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
