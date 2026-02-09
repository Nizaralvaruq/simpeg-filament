<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Modules\Presensi\Models\JadwalPiket;
use Illuminate\Support\Facades\Auth;

class JadwalPiketHariIni extends BaseWidget
{
    protected static ?int $sort = 60;
    protected int | string | array $columnSpan = 2;
    protected static ?string $heading = 'Petugas Piket Hari Ini';

    public static function canView(): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return $user && $user->can('View:JadwalPiketHariIni');
    }

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->query(
                JadwalPiket::query()
                    ->with('user')
                    ->today()
            )
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama')
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('shift')
                    ->label('Shift')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pagi' => 'warning',
                        'siang' => 'info',
                        'sore' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('keterangan')
                    ->label('Ket')
                    ->limit(20)
                    ->tooltip(fn($record) => $record->keterangan),
            ])
            ->paginated(false);
    }
}
