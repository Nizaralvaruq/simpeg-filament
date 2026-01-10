<?php

namespace Modules\Presensi\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Forms;
use Illuminate\Support\Facades\Auth;
use Modules\Presensi\Models\Absensi;
use Modules\Presensi\Exports\AbsensiExport;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Notifications\Notification;
use App\Models\User;

class LaporanAbsensi extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?int $navigationSort = 90;

    public ?array $data = [];

    public static function getNavigationIcon(): string | \BackedEnum | null
    {
        return 'heroicon-o-document-chart-bar';
    }

    public static function getNavigationGroup(): string | \UnitEnum | null
    {
        return 'Presensi';
    }

    public static function getNavigationLabel(): string
    {
        return 'Laporan Absensi';
    }

    public function getTitle(): string
    {
        return 'Laporan Rekap Absensi';
    }

    protected static string $view = 'presensi::filament.pages.laporan-absensi';

    public static function shouldRegisterNavigation(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();
        return $user?->hasAnyRole(['super_admin', 'ketua_psdm', 'admin_unit', 'koor_jenjang', 'kepala_sekolah']) ?? false;
    }

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\DatePicker::make('start_date')
                    ->label('Dari Tanggal')
                    ->default(now()->startOfMonth())
                    ->required(),
                Forms\Components\DatePicker::make('end_date')
                    ->label('Sampai Tanggal')
                    ->default(now())
                    ->required(),
                Forms\Components\Select::make('unit_id')
                    ->label('Unit')
                    ->options(\Modules\MasterData\Models\Unit::pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->visible(function () {
                        /** @var User|null $user */
                        $user = Auth::user();
                        return $user?->hasAnyRole(['super_admin', 'ketua_psdm']) ?? false;
                    })
                    ->placeholder('Semua Unit'),
            ])
            ->statePath('data')
            ->columns(3);
    }

    public function export()
    {
        $data = $this->form->getState();
        $query = Absensi::query()
            ->with(['user.employee.units'])
            ->whereDate('tanggal', '>=', $data['start_date'])
            ->whereDate('tanggal', '<=', $data['end_date']);

        /** @var User|null $user */
        $user = Auth::user();

        if (!$user) return;

        // Scope Logic
        if (!$user->hasAnyRole(['super_admin', 'ketua_psdm'])) {
            if ($user->hasAnyRole(['admin_unit', 'koor_jenjang', 'kepala_sekolah']) && $user->employee) {
                $unitIds = $user->employee->units->pluck('id');
                $query->whereHas('user.employee.units', fn($q) => $q->whereIn('units.id', $unitIds));
            } else {
                Notification::make()->title('Akses Ditolak')->danger()->send();
                return;
            }
        } elseif (!empty($data['unit_id'])) {
            // If Super Admin selected a specific unit
            $query->whereHas('user.employee.units', fn($q) => $q->where('units.id', $data['unit_id']));
        }

        $records = $query->orderBy('tanggal')->get();

        if ($records->isEmpty()) {
            Notification::make()->title('Tidak ada data pada periode ini')->warning()->send();
            return;
        }

        return Excel::download(new AbsensiExport($records), 'Laporan_Absensi_' . now()->format('Ymd_His') . '.xlsx');
    }
}
