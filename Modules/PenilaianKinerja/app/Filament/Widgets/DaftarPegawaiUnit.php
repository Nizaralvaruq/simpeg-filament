<?php

namespace Modules\PenilaianKinerja\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Modules\Kepegawaian\Models\DataInduk;
use Modules\PenilaianKinerja\Models\PerformanceScore;
use Illuminate\Support\Facades\Auth;
use Filament\Support\Enums\FontWeight;
use Filament\Actions\Action;

class DaftarPegawaiUnit extends BaseWidget
{
    protected static ?int $sort = 10;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Daftar Pegawai Unit Saya (Siap Dinilai)';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                DataInduk::query()
                    ->select('data_induks.*')
                    ->addSelect([
                        'has_score' => PerformanceScore::selectRaw('1')
                            ->whereColumn('performance_scores.data_induk_id', 'data_induks.id')
                            ->where('periode', now()->format('Y-m'))
                            ->limit(1)
                    ])
                    ->where('status', 'Aktif')
                    ->whereHas('units', function ($q) {
                        /** @var \App\Models\User $user */
                        $user = Auth::user();

                        if ($user && !$user->hasRole(['super_admin', 'admin'])) {
                            $unitIds = $user->employee->units->pluck('id');
                            $q->whereIn('units.id', $unitIds);
                        } else {
                            $q->whereRaw('1 = 0');
                        }
                    })
            )
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Pegawai')
                    ->weight(FontWeight::Bold)
                    ->searchable()
                    ->sortable()
                    ->description(fn(DataInduk $record): string => $record->nip ?? ''),

                Tables\Columns\TextColumn::make('jabatan')
                    ->label('Amanah/Jabatan')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('status_penilaian')
                    ->label('Status')
                    ->badge()
                    ->getStateUsing(fn(DataInduk $record) => $record->has_score ? 'Sudah Dinilai' : 'Belum Dinilai')
                    ->color(fn(string $state): string => match ($state) {
                        'Sudah Dinilai' => 'success',
                        'Belum Dinilai' => 'danger',
                    }),
            ])
            ->recordActions([
                Action::make('nilai')
                    ->label('Beri Nilai')
                    ->icon('heroicon-m-pencil-square')
                    ->color('primary')
                    // Redirect ke halaman create dengan pre-filled data_induk_id
                    ->url(fn(DataInduk $record) => \Modules\PenilaianKinerja\Filament\Resources\NilaiKinerjaResource::getUrl('create') . '?data_induk_id=' . $record->id)
                    // Sembunyikan jika sudah dinilai
                    ->visible(fn($record) => !$record->has_score),
            ]);
    }

    public static function canView(): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return $user && $user->can('View:DaftarPegawaiUnit');
    }
}
