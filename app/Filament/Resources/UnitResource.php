<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UnitResource\Pages;
use Modules\Pegawai\Models\Unit;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms;

class UnitResource extends Resource
{
    protected static ?string $model = Unit::class;

    public static function getNavigationIcon(): string | \BackedEnum | null
    {
        return 'heroicon-o-rectangle-stack';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Manajemen Unit';
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
            ->actions([
                \Filament\Actions\EditAction::make(),
            ])
            ->bulkActions([
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
