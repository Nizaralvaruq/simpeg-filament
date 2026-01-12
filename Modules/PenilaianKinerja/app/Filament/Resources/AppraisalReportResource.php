<?php

namespace Modules\PenilaianKinerja\Filament\Resources;

use Modules\PenilaianKinerja\Models\AppraisalAssignment;
use Modules\PenilaianKinerja\Models\AppraisalSession;
use Modules\Kepegawaian\Models\DataInduk;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class AppraisalReportResource extends Resource
{
    protected static ?string $model = AppraisalAssignment::class;

    protected static ?int $navigationSort = 10;

    public static function getNavigationIcon(): string | \BackedEnum | null
    {
        return 'heroicon-o-presentation-chart-bar';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Penilaian Kinerja';
    }

    public static function getModelLabel(): string
    {
        return 'Laporan Penilaian';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Laporan Penilaian';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                AppraisalAssignment::query()
                    ->fromSub(
                        AppraisalAssignment::query()
                            ->select('session_id', 'ratee_id')
                            ->selectRaw('MAX(id) as id')
                            ->selectRaw('COUNT(*) as total_assignments')
                            ->selectRaw('SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed_assignments')
                            ->groupBy('session_id', 'ratee_id'),
                        'appraisal_assignments'
                    )
                    ->with(['session', 'ratee'])
            )
            ->columns([
                TextColumn::make('session.name')
                    ->label('Sesi')
                    ->sortable(),
                TextColumn::make('ratee.nama')
                    ->label('Pegawai')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('progress')
                    ->label('Progres')
                    ->state(fn($record) => $record->completed_assignments . ' / ' . $record->total_assignments)
                    ->badge()
                    ->color(fn($state) => str_contains($state, ' / ') && explode(' / ', $state)[0] == explode(' / ', $state)[1] ? 'success' : 'warning'),
                TextColumn::make('final_score')
                    ->label('Skor Akhir')
                    ->state(fn($record) => AppraisalAssignment::getAggregatedReport($record->session_id, $record->ratee_id) ?? '-')
                    ->weight('bold')
                    ->color('primary'),
            ])
            ->filters([
                SelectFilter::make('session_id')
                    ->label('Filter Sesi')
                    ->options(AppraisalSession::whereNotNull('name')->pluck('name', 'id')),
            ])
            ->recordActions([
                //
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user && $user->hasAnyRole(['koor_jenjang', 'admin_unit'])) {
            if ($user->employee && $user->employee->units->isNotEmpty()) {
                $unitIds = $user->employee->units->pluck('id');
                $query->whereHas('ratee.units', fn($q) => $q->whereIn('units.id', $unitIds));
            } else {
                return $query->whereRaw('1=0');
            }
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => AppraisalReportResource\Pages\ListAppraisalReports::route('/'),
        ];
    }
}
