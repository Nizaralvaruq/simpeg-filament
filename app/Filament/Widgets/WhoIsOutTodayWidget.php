<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Modules\Presensi\Models\Absensi;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class WhoIsOutTodayWidget extends BaseWidget
{
    protected static ?int $sort = 6;
    protected int | string | array $columnSpan = 2;
    protected static ?string $heading = 'Siapa yang Tidak Masuk Hari Ini?';

    public static function canView(): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $user && $user->hasAnyRole(['super_admin', 'ketua_psdm', 'kepala_sekolah', 'admin_unit']);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Absensi::query()
                    ->with('user')
                    ->whereDate('tanggal', Carbon::today())
                    ->whereIn('status', ['sakit', 'izin', 'cuti', 'alpha'])
            )
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'sakit' => 'danger',
                        'izin' => 'warning',
                        'cuti' => 'info',
                        'alpha' => 'gray',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('keterangan')
                    ->limit(30)
                    ->tooltip(fn($record) => $record->keterangan),
            ])
            ->paginated(false);
    }
}
