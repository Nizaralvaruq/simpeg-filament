<?php

namespace Modules\PenilaianKinerja\Filament\Resources;

use Modules\PenilaianKinerja\Filament\Resources\LaporanPenilaianResource\Pages;
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
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;

class LaporanPenilaianResource extends Resource
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
        /** @var \App\Models\User $user */
        $user = Auth::user();

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
                    ->when(
                        $user?->hasAnyRole(['koor_jenjang', 'admin_unit', 'kepala_sekolah']),
                        function ($query) use ($user) {
                            if ($user->employee && $user->employee->units->isNotEmpty()) {
                                $unitIds = $user->employee->units->pluck('id');
                                $query->whereHas('ratee.units', fn($q) => $q->whereIn('units.id', $unitIds));
                            } else {
                                $query->whereRaw('1=0');
                            }
                        }
                    )
                    ->when(
                        $user?->hasRole('staff'),
                        fn($query) => $user->employee
                            ? $query->where('ratee_id', $user->employee->id)
                            : $query->whereRaw('1=0')
                    )
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
                TextColumn::make('breakdown')
                    ->label('Detail Skor')
                    ->html()
                    ->state(function ($record) {
                        $sessionId = $record->session_id;
                        $rateeId = $record->ratee_id;
                        $session = $record->session;

                        $html = '<div class="flex gap-1 flex-wrap">';

                        // Helper to generate badge HTML
                        $getBadge = function ($label, $score, $title) {
                            $color = '#94a3b8'; // gray
                            if ($score >= 4.5) $color = '#22c55e'; // green
                            elseif ($score >= 3.5) $color = '#3b82f6'; // blue
                            elseif ($score >= 2.5) $color = '#f59e0b'; // amber
                            else $color = '#ef4444'; // red

                            return "<span title='{$title}: {$score}' class='px-1.5 py-0.5 rounded text-[10px] font-bold text-white' style='background-color: {$color}'>{$label}: {$score}</span>";
                        };

                        // Manual scores from all assignments (completed + expired)
                        $assignments = AppraisalAssignment::where('session_id', $sessionId)
                            ->where('ratee_id', $rateeId)
                            ->whereIn('status', ['completed', 'expired'])
                            ->with('results')
                            ->get();

                        $scoresByType = ['superior' => [], 'peer' => [], 'self' => []];
                        $missingByType = ['superior' => 0, 'peer' => 0, 'self' => 0];

                        foreach ($assignments as $a) {
                            if ($a->status === 'expired') {
                                $missingByType[$a->relation_type]++;
                                continue;
                            }
                            $avg = $a->results->avg('score');
                            if ($avg) $scoresByType[$a->relation_type][] = $avg;
                        }

                        if (!empty($scoresByType['superior'])) {
                            $score = round(array_sum($scoresByType['superior']) / count($scoresByType['superior']), 1);
                            $html .= $getBadge('A', $score, 'Atasan');
                        } elseif ($missingByType['superior'] > 0) {
                            $html .= "<span title='Tugas Atasan Kedaluwarsa' class='px-1.5 py-0.5 rounded text-[10px] font-bold text-white bg-slate-400 opacity-60'>A: -</span>";
                        }

                        if (!empty($scoresByType['peer'])) {
                            $score = round(array_sum($scoresByType['peer']) / count($scoresByType['peer']), 1);
                            $html .= $getBadge('R', $score, 'Rekan');
                        }

                        // Show additional indicators for missing peers
                        if ($missingByType['peer'] > 0) {
                            for ($i = 0; $i < $missingByType['peer']; $i++) {
                                $html .= "<span title='Tugas Rekan Kedaluwarsa' class='px-1.5 py-0.5 rounded text-[10px] font-bold text-white bg-slate-400 opacity-60'>R: -</span>";
                            }
                        }

                        if (!empty($scoresByType['self'])) {
                            $score = round(array_sum($scoresByType['self']) / count($scoresByType['self']), 1);
                            $html .= $getBadge('S', $score, 'Diri');
                        } elseif ($missingByType['self'] > 0) {
                            $html .= "<span title='Self Assessment Kedaluwarsa' class='px-1.5 py-0.5 rounded text-[10px] font-bold text-white bg-slate-400 opacity-60'>S: -</span>";
                        }

                        // Auto scores
                        if ($session->attendance_weight > 0) {
                            $ratee = DataInduk::find($rateeId);
                            if ($ratee && $ratee->user_id) {
                                $att = \Modules\PenilaianKinerja\Services\AutoScoreService::getAttendanceScore($ratee->user_id, $session->start_date, $session->end_date);
                                $html .= $getBadge('H', $att['score'], 'Harian');
                            }
                        }

                        if ($session->activity_weight > 0) {
                            $ratee = DataInduk::find($rateeId);
                            if ($ratee && $ratee->user_id) {
                                $act = \Modules\PenilaianKinerja\Services\AutoScoreService::getActivityScore($ratee->user_id, $session->start_date, $session->end_date);
                                $html .= $getBadge('K', $act['score'], 'Kegiatan');
                            }
                        }

                        $html .= '</div>';
                        return $html;
                    }),
            ])
            ->striped()
            ->persistFiltersInSession()
            ->filters([
                SelectFilter::make('session_id')
                    ->label('Filter Sesi')
                    ->options(AppraisalSession::whereNotNull('name')->pluck('name', 'id')),
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('print_report')
                        ->label('Cetak Raport')
                        ->icon('heroicon-o-printer')
                        ->color('info')
                        ->action(function ($record) {
                            $session = AppraisalSession::find($record->session_id);
                            $ratee = DataInduk::with('units')->find($record->ratee_id);
                            $finalScore = AppraisalAssignment::getAggregatedReport($record->session_id, $record->ratee_id);
                            $finalGrade = AppraisalAssignment::getGrade($finalScore);
                            $categoryReport = AppraisalAssignment::getCategoryReport($record->session_id, $record->ratee_id);

                            $attendanceData = null;
                            if ($session->attendance_weight > 0 && $ratee->user_id) {
                                $attendanceData = \Modules\PenilaianKinerja\Services\AutoScoreService::getAttendanceScore($ratee->user_id, $session->start_date, $session->end_date);
                            }

                            $activityData = null;
                            if ($session->activity_weight > 0 && $ratee->user_id) {
                                $activityData = \Modules\PenilaianKinerja\Services\AutoScoreService::getActivityScore($ratee->user_id, $session->start_date, $session->end_date);
                            }

                            $pdf = Pdf::loadView('penilaiankinerja::reports.performance_report', [
                                'session' => $session,
                                'ratee' => $ratee,
                                'finalScore' => $finalScore,
                                'finalGrade' => $finalGrade,
                                'categoryReport' => $categoryReport,
                                'attendanceData' => $attendanceData,
                                'activityData' => $activityData,
                            ]);

                            return response()->streamDownload(
                                fn() => print($pdf->output()),
                                "Raport_Penilaian_{$ratee->nama}.pdf"
                            );
                        }),
                ])->button()->label('Aksi'),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user && $user->hasAnyRole(['koor_jenjang', 'admin_unit', 'kepala_sekolah'])) {
            if ($user->employee && $user->employee->units->isNotEmpty()) {
                $unitIds = $user->employee->units->pluck('id');
                $query->whereHas('ratee.units', fn($q) => $q->whereIn('units.id', $unitIds));
            } else {
                return $query->whereRaw('1=0');
            }
        }

        if ($user && $user->hasRole('staff')) {
            if ($user->employee) {
                $query->where('ratee_id', $user->employee->id);
            } else {
                return $query->whereRaw('1=0');
            }
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLaporanPenilaian::route('/'),
        ];
    }
}
