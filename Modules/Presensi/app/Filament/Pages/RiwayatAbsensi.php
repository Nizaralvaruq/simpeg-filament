<?php

namespace Modules\Presensi\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Support\Facades\Auth;
use Modules\Presensi\Models\Absensi;
use App\Models\User;

class RiwayatAbsensi extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?int $navigationSort = 10;

    public static function getNavigationIcon(): string | \BackedEnum | null
    {
        return 'heroicon-o-clock';
    }

    public static function getNavigationGroup(): string | \UnitEnum | null
    {
        return 'Presensi';
    }

    public static function getNavigationLabel(): string
    {
        return 'Riwayat Saya';
    }

    public function getTitle(): string
    {
        return 'Riwayat Absensi Saya';
    }

    protected static string $view = 'presensi::filament.pages.riwayat-absensi';

    public static function shouldRegisterNavigation(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();
        return $user?->hasRole('staff') ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Absensi::query()->where('user_id', Auth::id())->orderBy('tanggal', 'desc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')->date()->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'hadir' => 'success',
                        'izin' => 'warning',
                        'sakit' => 'danger',
                        'alpha' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('jam_masuk')->time(),
                Tables\Columns\TextColumn::make('jam_keluar')->time(),
                Tables\Columns\TextColumn::make('keterangan')->limit(50),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'hadir' => 'Hadir',
                        'izin' => 'Izin',
                        'sakit' => 'Sakit',
                        'alpha' => 'Alpha',
                    ]),
            ]);
    }
}
