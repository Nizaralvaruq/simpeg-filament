<?php

namespace Modules\Presensi\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Modules\Presensi\Models\Absensi;
use Illuminate\Support\Facades\Auth;

class RecentAbsensiWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $user && $user->hasRole('staff');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Absensi::query()
                    ->where('user_id', Auth::id())
                    ->latest('tanggal')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => ucfirst($state))
                    ->color(fn(string $state): string => match ($state) {
                        'hadir' => 'success',
                        'izin' => 'warning',
                        'sakit' => 'danger',
                        'alpha' => 'gray',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('jam_masuk')
                    ->label('Jam Masuk')
                    ->time('H:i')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('jam_keluar')
                    ->label('Jam Keluar')
                    ->time('H:i')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('uraian_harian')
                    ->label('Aktivitas')
                    ->html()
                    ->limit(50)
                    ->wrap()
                    ->tooltip(fn($record) => $record->uraian_harian ? strip_tags($record->uraian_harian) : null)
                    ->placeholder('Tidak ada uraian'),
            ])
            ->heading('Absensi Terbaru')
            ->paginated(false);
    }
}
