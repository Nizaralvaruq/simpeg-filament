<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Modules\Presensi\Models\JadwalPiket;
use Illuminate\Support\Facades\Auth;

class TodayPiketWidget extends BaseWidget
{
    protected static ?int $sort = 5;
    protected int | string | array $columnSpan = 'md';
    protected static ?string $heading = 'Petugas Piket Hari Ini';

    public static function canView(): bool
    {
        return Auth::check();
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
