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
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Select;
use Carbon\Carbon;
use Filament\Tables\Columns\TextColumn;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Presensi\Exports\AbsensiExport; // Re-use or create new Summary Export if needed

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
        // Visible to All Authenticated Users (Scoped by Query)
        return $user !== null;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(function () {
                $query = DataInduk::query()->where('status', 'Aktif');
                /** @var \App\Models\User $user */
                $user = Auth::user();

                if ($user->hasRole('staff')) {
                    $query->where('user_id', $user->id);
                } elseif ($user->hasAnyRole(['koor_jenjang', 'admin_unit', 'kepala_sekolah'])) {
                    if ($user->employee && $user->employee->units->isNotEmpty()) {
                        $unitIds = $user->employee->units->pluck('id');
                        $query->whereHas('units', fn($q) => $q->whereIn('units.id', $unitIds));
                    } else {
                        $query->whereRaw('1=0');
                    }
                }

                return $query;
            })
            ->columns([
                TextColumn::make('user.name')
                    ->label('Pegawai')
                    ->searchable()
                    ->sortable()
                    ->description(fn($record) => $record->user->employee->units->pluck('name')->join(', ')),

                // Calculated Columns (using state)
                TextColumn::make('attendance_summary')
                    ->label('Kehadiran Bulan Ini')
                    ->state(function (DataInduk $record) {
                        // Get filter values from Livewire if possible, or default to current month
                        // For simplicity in this column, we might need to inject the filter state or default to now()
                        // A better approach for "Resource" is to rely on the query modification, 
                        // but Filament columns don't easily access table filters directly without some wiring.
                        // We'll calculate simple stats for *Current Month* in logic, or use a custom query.

                        // Let's rely on a helper or relation
                        return 'Summary';
                    })
                    ->formatStateUsing(function ($state, DataInduk $record, $livewire) {
                        /** @var \Filament\Resources\Pages\ListRecords $livewire */
                        $filters = $livewire->tableFilters;
                        $month = $filters['period']['month'] ?? now()->month;
                        $year = $filters['period']['year'] ?? now()->year;

                        $stats = Absensi::where('user_id', $record->user_id)
                            ->whereMonth('tanggal', $month)
                            ->whereYear('tanggal', $year)
                            ->get();

                        $hadir = $stats->where('status', 'hadir')->count();
                        $sakit = $stats->where('status', 'sakit')->count();
                        $izin = $stats->where('status', 'izin')->count();
                        $alpha = $stats->where('status', 'alpha')->count();

                        // User Friendly Display: "H: 20 | S: 1 | I: 0 | A: 0"
                        // Or better, return an HTML view string if we use ->html()
                        return "H: $hadir | S: $sakit | I: $izin | A: $alpha";
                    })
                    ->description('H=Hadir, S=Sakit, I=Izin, A=Alpha'),

                TextColumn::make('attendance_percent')
                    ->label('% Kehadiran')
                    ->state(function (DataInduk $record, $livewire) {
                        /** @var \Filament\Resources\Pages\ListRecords $livewire */
                        $filters = $livewire->tableFilters;
                        $month = $filters['period']['month'] ?? now()->month;
                        $year = $filters['period']['year'] ?? now()->year;

                        // Calculate workdays so far (simplify: 25 days or days passed)
                        // For now, simple logic: Hadir / Total Days in Month (excluding Sundays)
                        // Or just based on total inputs (H+S+I+A)

                        $stats = Absensi::where('user_id', $record->user_id)
                            ->whereMonth('tanggal', $month)
                            ->whereYear('tanggal', $year)
                            ->count();

                        $hadir = Absensi::where('user_id', $record->user_id)
                            ->whereMonth('tanggal', $month)
                            ->whereYear('tanggal', $year)
                            ->where('status', 'hadir')
                            ->count();

                        if ($stats == 0) return 0;
                        return round(($hadir / $stats) * 100);
                    })
                    ->badge()
                    ->color(fn($state) => $state >= 90 ? 'success' : ($state >= 75 ? 'warning' : 'danger'))
                    ->formatStateUsing(fn($state) => $state . '%'),

            ])
            ->filters([
                Filter::make('period')
                    ->form([
                        Select::make('month')
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
                            ->default(now()->month)
                            ->label('Bulan'),
                        Select::make('year')
                            ->options(
                                range(now()->year - 2, now()->year + 1)
                            )
                            ->default(now()->year)
                            ->label('Tahun'),
                    ])
                    ->query(fn(Builder $query) => $query), // Filters don't filter the *Rows* (Employees), they filter the *Data* calculation
            ])
            ->actions([])
            ->bulkActions([
                // We'll add Export here later
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
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
