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
use Modules\Presensi\Exports\AbsensiExport; // Re-use or create new Summary Export if needed
use Filament\Actions\BulkActionGroup;
use Filament\Actions\BulkAction;


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
                    ->label('Kehadiran Periode')
                    ->formatStateUsing(function ($state, DataInduk $record, $livewire) {
                        /** @var \Filament\Resources\Pages\ListRecords $livewire */
                        $filters = $livewire->getTableFilterState('period');
                        $month = $filters['month'] ?? now()->month;
                        $year = $filters['year'] ?? now()->year;

                        $stats = Absensi::where('user_id', $record->user_id)
                            ->whereMonth('tanggal', $month)
                            ->whereYear('tanggal', $year)
                            ->get();

                        $hadir = $stats->where('status', 'hadir')->count();
                        $sakit = $stats->where('status', 'sakit')->count();
                        $izin = $stats->where('status', 'izin')->count();
                        $alpha = $stats->where('status', 'alpha')->count();

                        return "H: $hadir | S: $sakit | I: $izin | A: $alpha";
                    })
                    ->description('H:Hadir, S:Sakit, I:Izin, A:Alpha'),

                TextColumn::make('attendance_percent')
                    ->label('% Hadir')
                    ->state(function (DataInduk $record, $livewire) {
                        /** @var \Filament\Resources\Pages\ListRecords $livewire */
                        $filters = $livewire->getTableFilterState('period');
                        $month = $filters['month'] ?? now()->month;
                        $year = $filters['year'] ?? now()->year;

                        $query = Absensi::where('user_id', $record->user_id)
                            ->whereMonth('tanggal', $month)
                            ->whereYear('tanggal', $year);

                        $totalInput = (clone $query)->count();
                        $hadir = $query->where('status', 'hadir')->count();

                        if ($totalInput == 0) return 0;
                        return round(($hadir / $totalInput) * 100);
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
            ->recordActions([])
            ->toolbarActions([
                BulkActionGroup::make([
                    // We'll add Export here later
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
