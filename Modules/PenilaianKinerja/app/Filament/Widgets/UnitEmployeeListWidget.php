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

class UnitEmployeeListWidget extends BaseWidget
{
    protected static ?int $sort = 10;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Daftar Pegawai Unit Saya (Siap Dinilai)';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                DataInduk::query()
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
                    ->label('Jabatan')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('status_penilaian')
                    ->label('Status')
                    ->badge()
                    ->getStateUsing(function (DataInduk $record) {
                        $isAssessed = PerformanceScore::where('data_induk_id', $record->id)
                            ->where('periode', now()->format('Y-m'))
                            ->exists();

                        return $isAssessed ? 'Sudah Dinilai' : 'Belum Dinilai';
                    })
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
                    ->url(fn(DataInduk $record) => \Modules\PenilaianKinerja\Filament\Resources\PerformanceScoreResource::getUrl('create') . '?data_induk_id=' . $record->id)
                    // Sembunyikan jika sudah dinilai
                    ->visible(fn($record) => !PerformanceScore::where('data_induk_id', $record->id)->where('periode', now()->format('Y-m'))->exists()),
            ]);
    }

    public static function canView(): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $user && $user->hasRole('koor_jenjang');
    }
}
