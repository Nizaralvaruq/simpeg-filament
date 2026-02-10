<?php

namespace Modules\MasterData\Filament\Resources;

use Modules\MasterData\Filament\Resources\UnitResource\Pages;
use Modules\MasterData\Models\Unit;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Illuminate\Support\Facades\Auth;
use Filament\Actions\Action;

class UnitResource extends Resource
{
    protected static ?string $model = Unit::class;
    protected static ?int $navigationSort = 10;

    public static function getNavigationIcon(): string | \BackedEnum | null
    {
        return 'heroicon-o-rectangle-stack';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Data Master';
    }

    public static function getModelLabel(): string
    {
        return 'Jenjang';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Jenjang';
    }

    public static function getNavigationLabel(): string
    {
        return 'Jenjang';
    }

    public static function shouldRegisterNavigation(): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $user?->hasAnyRole(['super_admin', 'admin_unit']);
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->hasRole('super_admin')) {
            return $query;
        }

        if ($user->hasRole('admin_unit') && $user->employee) {
            $unitIds = $user->employee->units->pluck('id');
            return $query->whereIn('id', $unitIds);
        }

        return $query->whereRaw('1=0');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('type')
                    ->options([
                        'LPI' => 'LPI',
                        'TK TAKFIZ' => 'TK TAKFIZ',
                        'RUBAT' => 'RUBAT',
                        'PAUD' => 'PAUD',
                        'TK' => 'TK',
                        'TAKFIZ TK' => 'TAKFIZ TK',
                        'SDI' => 'SDI',
                        'SMP' => 'SMP',
                        'TAKFIZ SMP' => 'TAKFIZ SMP',
                        'SMA' => 'SMA',
                        'TAKFIZ SMA' => 'TAKFIZ SMA',
                        'SMK' => 'SMK',
                        'TAKFIZ SMK' => 'TAKFIZ SMK',
                        'TK PG' => 'TK PG',
                        'MI' => 'MI',
                        'Mts PG' => 'Mts PG',
                        'GIZI LPI' => 'GIZI LPI',
                        'AEC' => 'AEC',
                        'PENGEMBANGAN' => 'PENGEMBANGAN',
                    ])
                    ->required(),

                Section::make('Pengaturan Lokasi (Geofencing)')
                    ->description('Tentukan koordinat dan radius untuk pembatasan absensi di unit ini. Kosongkan jika ingin mengikuti pengaturan global.')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('latitude')
                                    ->numeric()
                                    ->minValue(-90)
                                    ->maxValue(90)
                                    ->step(0.00000001)
                                    ->placeholder('-6.xxxx')
                                    ->suffixActions([
                                        Action::make('getLocation')
                                            ->icon('heroicon-m-map-pin')
                                            ->color('warning')
                                            ->tooltip('Ambil lokasi saat ini')
                                            ->action(fn($livewire) => $livewire->dispatch('get-current-location')),
                                        Action::make('openMap')
                                            ->icon('heroicon-m-globe-alt')
                                            ->color('info')
                                            ->tooltip('Lihat di Peta')
                                            ->url(fn($get) => $get('latitude') && $get('longitude')
                                                ? 'https://www.google.com/maps/search/?api=1&query=' . $get('latitude') . ',' . $get('longitude')
                                                : null)
                                            ->openUrlInNewTab()
                                            ->visible(fn($get) => $get('latitude') && $get('longitude')),
                                    ]),
                                Forms\Components\TextInput::make('longitude')
                                    ->numeric()
                                    ->minValue(-180)
                                    ->maxValue(180)
                                    ->step(0.00000001)
                                    ->placeholder('106.xxxx'),
                                Forms\Components\TextInput::make('radius')
                                    ->label('Radius (Meter)')
                                    ->numeric()
                                    ->suffix('m')
                                    ->placeholder('Contoh: 100'),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('type')->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                \Filament\Actions\EditAction::make(),
            ])
            ->toolbarActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUnits::route('/'),
            'create' => Pages\CreateUnit::route('/create'),
            'edit' => Pages\EditUnit::route('/{record}/edit'),
        ];
    }
}
