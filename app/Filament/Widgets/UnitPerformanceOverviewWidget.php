<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Modules\PenilaianKinerja\Models\PerformanceScore;
use Illuminate\Support\Facades\Auth;
use Modules\PenilaianKinerja\Models\AppraisalSession;

class UnitPerformanceOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 1;
    protected static ?string $heading = 'Top Performa Guru/Pegawai';

    public static function canView(): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $user && $user->hasAnyRole(['kepala_sekolah', 'admin_unit', 'super_admin']);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                PerformanceScore::query()
                    ->select('performance_scores.*')
                    ->selectRaw('((COALESCE(kualitas_hasil,0) + COALESCE(ketelitian,0) + COALESCE(kuantitas_hasil,0) + COALESCE(ketepatan_waktu,0) + COALESCE(kehadiran,0) + COALESCE(kepatuhan_aturan,0) + COALESCE(etika_kerja,0) + COALESCE(tanggung_jawab,0) + COALESCE(komunikasi,0) + COALESCE(kerjasama_tim,0)) / 10) as average_score_raw')
                    ->whereHas(
                        'employee.units',
                        function ($query) {
                            /** @var \App\Models\User $user */
                            $user = Auth::user();
                            if ($user->hasRole('super_admin')) {
                                return $query;
                            }
                            $unitIds = $user->employee?->units->pluck('id')->all() ?? [];
                            return $query->whereIn('units.id', $unitIds);
                        }
                    )
                    ->with(['employee'])
                    ->orderByDesc('average_score_raw')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('employee.nama')
                    ->label('Nama')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('average_score')
                    ->label('Nilai Rata-rata')
                    ->numeric(2),

                Tables\Columns\TextColumn::make('grade')
                    ->label('Predikat')
                    ->badge()
                    ->color(fn($record) => $record->grade_color),
            ])
            ->paginated(false);
    }
}
