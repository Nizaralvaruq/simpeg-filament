<?php

namespace Modules\MasterData\Filament\Resources;

use Modules\MasterData\Filament\Resources\UnitTypeResource\Pages;
use Modules\MasterData\Models\UnitType;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms;
use Illuminate\Support\Facades\Auth;

class UnitTypeResource extends Resource
{
    protected static ?string $model = UnitType::class;
    protected static ?int $navigationSort = 11;
    public static function getNavigationIcon(): string | \BackedEnum | null
    {
        return 'heroicon-o-squares-2x2';
    }

    public static function getActiveNavigationIcon(): string | \BackedEnum | null
    {
        return 'heroicon-s-squares-2x2';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Data Master';
    }

    public static function getModelLabel(): string
    {
        return 'Tipe Jenjang';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Tipe Jenjang';
    }

    public static function getNavigationLabel(): string
    {
        return 'Tipe Jenjang';
    }

    public static function shouldRegisterNavigation(): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $user?->hasRole('super_admin');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('name')
                    ->label('Nama Tipe')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Tipe')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('units_count')
                    ->label('Jumlah Unit')
                    ->counts('units'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUnitTypes::route('/'),
            'create' => Pages\CreateUnitType::route('/create'),
            'edit' => Pages\EditUnitType::route('/{record}/edit'),
        ];
    }
}
