<?php

namespace Modules\MasterData\Filament\Resources;

use Modules\MasterData\Filament\Resources\UnitResource\Pages;
use Modules\MasterData\Models\Unit;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms;
use Illuminate\Support\Facades\Auth;

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
        return $user?->hasAnyRole(['super_admin']);
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
