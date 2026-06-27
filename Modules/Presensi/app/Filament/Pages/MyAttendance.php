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
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;


class MyAttendance extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-calendar-days';

    protected string $view = 'presensi::filament.pages.my-attendance';

    public string $activeTab = 'harian';

    protected static string | \UnitEnum | null $navigationGroup = 'Presensi';

    protected static ?string $navigationLabel = 'Absensi Saya';

    protected static ?string $title = 'Riwayat Absensi Saya';

    protected static ?int $navigationSort = 2;

    public static function shouldRegisterNavigation(): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $user && $user->hasAnyRole(['staff', 'ketua_psdm', 'koor_jenjang', 'kepala_sekolah', 'admin_unit']);
    }


    // Table logic moved to Widgets

    public function getStats(): array
    {
        $userId = Auth::id();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $stats = Absensi::where('user_id', $userId)
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->selectRaw("
                SUM(CASE WHEN status IN ('hadir', 'dinas_luar') THEN 1 ELSE 0 END) as hadir,
                SUM(CASE WHEN status IN ('izin', 'sakit', 'cuti') THEN 1 ELSE 0 END) as izin_sakit_cuti,
                COALESCE(SUM(late_minutes), 0) as late_minutes
            ")
            ->first();

        return [
            'hadir' => $stats->hadir ?? 0,
            'late' => $stats->late_minutes ?? 0,
            'izin_sakit' => $stats->izin_sakit_cuti ?? 0,
        ];
    }
}
