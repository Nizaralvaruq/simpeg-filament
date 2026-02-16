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


    // Table logic moved to Widgets

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

        $izinSakitCuti = Absensi::where('user_id', $userId)
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->whereIn('status', ['izin', 'sakit', 'cuti'])
            ->count();

        return [
            'hadir' => $hadir,
            'late' => $lateMinutes,
            'izin_sakit' => $izinSakitCuti,
        ];
    }
}
