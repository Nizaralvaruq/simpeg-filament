<?php

namespace Modules\Presensi\Filament\Resources;

use Modules\Presensi\Filament\Resources\AbsensiReportResource\Pages;
use Modules\Kepegawaian\Models\DataInduk;
use Modules\Presensi\Models\Absensi;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Select;
use Carbon\Carbon;
use Filament\Tables\Columns\TextColumn;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Modules\Presensi\Exports\AbsensiExport;


class AbsensiReportResource extends Resource
{
    protected static ?string $model = DataInduk::class; // We look at Employees
    protected static ?string $navigationLabel = 'Laporan Absensi';
    protected static ?string $modelLabel = 'Laporan Absensi';
    protected static ?int $navigationSort = 5;

    public static function getNavigationIcon(): string | \BackedEnum | null
    {
        return 'heroicon-o-chart-bar';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Presensi';
    }

    public static function shouldRegisterNavigation(): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) return false;

        // Hide from Staff
        if ($user->hasRole('staff')) {
            return false;
        }

        return true;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                // Get current filter values
                $month = request('tableFilters.period.month', now()->month);
                $year = request('tableFilters.period.year', now()->year);

                // Eager load with aggregates to prevent N+1 queries
                return $query
                    ->with(['user', 'units'])
                    ->withCount([
                        'absensis as hadir_count' => function ($q) use ($month, $year) {
                            $q->where('status', 'hadir')
                                ->whereMonth('tanggal', $month)
                                ->whereYear('tanggal', $year);
                        },
                        'absensis as dinas_luar_count' => function ($q) use ($month, $year) {
                            $q->where('status', 'dinas_luar')
                                ->whereMonth('tanggal', $month)
                                ->whereYear('tanggal', $year);
                        },
                        'absensis as sakit_count' => function ($q) use ($month, $year) {
                            $q->where('status', 'sakit')
                                ->whereMonth('tanggal', $month)
                                ->whereYear('tanggal', $year);
                        },
                        'absensis as izin_count' => function ($q) use ($month, $year) {
                            $q->where('status', 'izin')
                                ->whereMonth('tanggal', $month)
                                ->whereYear('tanggal', $year);
                        },
                        'absensis as alpha_count' => function ($q) use ($month, $year) {
                            $q->where('status', 'alpha')
                                ->whereMonth('tanggal', $month)
                                ->whereYear('tanggal', $year);
                        },
                    ])
                    ->withSum([
                        'absensis as total_late_minutes' => function ($q) use ($month, $year) {
                            $q->whereMonth('tanggal', $month)
                                ->whereYear('tanggal', $year);
                        }
                    ], 'late_minutes');
            })
            ->columns([
                TextColumn::make('user.name')
                    ->label('Pegawai')
                    ->searchable()
                    ->sortable()
                    ->description(function (DataInduk $record) {
                        return $record->units->pluck('name')->join(', ');
                    }),

                // Calculated Columns (using state)
                TextColumn::make('attendance_summary')
                    ->label('Ringkasan Kehadiran')
                    ->view('presensi::filament.tables.columns.attendance-summary'),

                TextColumn::make('total_late_minutes')
                    ->label('Total Terlambat')
                    ->suffix(' mnt')
                    ->badge()
                    ->color(fn($state) => $state > 0 ? 'danger' : 'success')
                    ->description('Total menit keterlambatan bulan ini')
                    ->default(0),

                TextColumn::make('attendance_percent')
                    ->label('% Hadir')
                    ->state(function (DataInduk $record) {
                        $total = ($record->hadir_count ?? 0) +
                            ($record->dinas_luar_count ?? 0) +
                            ($record->sakit_count ?? 0) +
                            ($record->izin_count ?? 0) +
                            ($record->alpha_count ?? 0);

                        $present = ($record->hadir_count ?? 0) + ($record->dinas_luar_count ?? 0);

                        if ($total == 0) return 0;
                        return round(($present / $total) * 100);
                    })
                    ->badge()
                    ->color(fn($state) => $state >= 90 ? 'success' : ($state >= 75 ? 'warning' : 'danger'))
                    ->formatStateUsing(fn($state) => $state . '%'),

            ])
            ->filters([
                Filter::make('period')
                    ->schema([
                        Select::make('month')
                            ->label('Bulan')
                            ->options([
                                1 => 'Januari',
                                2 => 'Februari',
                                3 => 'Maret',
                                4 => 'April',
                                5 => 'Mei',
                                6 => 'Juni',
                                7 => 'Juli',
                                8 => 'Agustus',
                                9 => 'September',
                                10 => 'Oktober',
                                11 => 'November',
                                12 => 'Desember'
                            ])
                            ->default(now()->month),
                        Select::make('year')
                            ->label('Tahun')
                            ->options(array_combine(
                                range(now()->year - 2, now()->year + 1),
                                range(now()->year - 2, now()->year + 1)
                            ))
                            ->default(now()->year),
                    ])
                    ->query(fn(Builder $query) => $query),

                Tables\Filters\SelectFilter::make('unit')
                    ->label('Unit Kerja')
                    ->relationship('units', 'name')
                    ->searchable()
                    ->preload()
                    ->visible(fn() => !($user = Auth::user()) || !($user instanceof User) || !$user->hasRole('staff')),
            ])
            ->recordActions([
                Action::make('view_details')
                    ->label('Lihat Detail')
                    ->icon('heroicon-o-eye')
                    ->modalHeading(fn(DataInduk $record) => "Detail Presensi: " . $record->nama)
                    ->modalWidth('4xl')
                    ->slideOver()
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->modalContent(function (DataInduk $record, $livewire) {
                        $filters = $livewire->getTableFilterState('period');
                        $month = $filters['month'] ?? now()->month;
                        $year = $filters['year'] ?? now()->year;

                        $absensis = Absensi::where('user_id', $record->user_id)
                            ->whereMonth('tanggal', $month)
                            ->whereYear('tanggal', $year)
                            ->orderBy('tanggal', 'asc')
                            ->get();

                        return view('presensi::filament.resources.absensi-report.detail-modal', [
                            'absensis' => $absensis,
                            'month' => $month,
                            'year' => $year,
                        ]);
                    })
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('export')
                        ->label('Export Terpilih')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function (mixed $records) {
                            return Excel::download(new AbsensiExport($records), 'Laporan_Absensi_Terpilih.xlsx');
                        }),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->where('status', 'Aktif');
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user) {
            return $query->whereRaw('1=0');
        }

        // 1. Staff: Can only see THEMSELVES
        if ($user->hasRole('staff')) {
            return $query->where('user_id', $user->id);
        }

        // 2. Unit Admins / Coordinators: Can see Employees in their UNITS
        if ($user->hasAnyRole(['koor_jenjang', 'admin_unit', 'kepala_sekolah'])) {
            if ($user->employee && $user->employee->units->isNotEmpty()) {
                $unitIds = $user->employee->units->pluck('id');
                $query->whereHas('units', fn($q) => $q->whereIn('units.id', $unitIds));
            } else {
                return $query->whereRaw('1=0');
            }
        }

        // 3. Super Admin / Ketua PSDM: Can see ALL (No extra filter needed)

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAbsensiReports::route('/'),
        ];
    }
}
