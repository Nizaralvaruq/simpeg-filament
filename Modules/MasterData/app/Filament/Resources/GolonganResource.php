<?php

namespace Modules\MasterData\Filament\Resources;

use Modules\MasterData\Filament\Resources\GolonganResource\Pages;
use Modules\MasterData\Models\Golongan;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Tables;

class GolonganResource extends Resource
{
    protected static ?string $model = Golongan::class;

    public static function getNavigationIcon(): string | \BackedEnum | null
    {
        return 'heroicon-o-list-bullet';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Data Master';
    }

    public static function getModelLabel(): string
    {
        return 'Golongan';
    }

    public static function shouldRegisterNavigation(): bool
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        return $user->hasRole('super_admin');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Detail Golongan')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Golongan')
                            ->placeholder('Contoh: III/a')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->label('Keterangan')
                            ->maxLength(255),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Golongan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Keterangan'),
            ])
            ->filters([
                //
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
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
            'index' => \Modules\MasterData\Filament\Resources\GolonganResource\Pages\ListGolongans::route('/'),
            'create' => \Modules\MasterData\Filament\Resources\GolonganResource\Pages\CreateGolongan::route('/create'),
            'edit' => \Modules\MasterData\Filament\Resources\GolonganResource\Pages\EditGolongan::route('/{record}/edit'),
        ];
    }
}
