<?php

namespace Modules\PenilaianKinerja\Filament\Resources;

use Modules\PenilaianKinerja\Filament\Resources\KpiCategoryResource\Pages;
use Modules\PenilaianKinerja\Models\KpiCategory;
use Filament\Schemas\Schema;
use Filament\Forms\Components;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class KpiCategoryResource extends Resource
{
    protected static ?string $model = KpiCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Penilaian Kinerja';

    protected static ?string $modelLabel = 'Kategori KPI';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Components\Section::make('Detail Kategori')
                    ->schema([
                        Components\TextInput::make('name')
                            ->label('Nama Kategori')
                            ->required()
                            ->maxLength(255),
                        Components\TextInput::make('weight')
                            ->label('Bobot (%)')
                            ->numeric()
                            ->default(0)
                            ->suffix('%'),
                    ])->columns(2),

                Components\Section::make('Indikator Penilaian')
                    ->schema([
                        Components\Repeater::make('indicators')
                            ->relationship()
                            ->schema([
                                Components\TextInput::make('name')
                                    ->label('Nama Indikator')
                                    ->required(),
                                Components\Textarea::make('description')
                                    ->label('Keterangan'),
                                Components\TextInput::make('max_score')
                                    ->label('Skor Maksimal')
                                    ->numeric()
                                    ->default(5)
                                    ->required(),
                            ])
                            ->columns(3)
                            ->itemLabel(fn(array $state): ?string => $state['name'] ?? null),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Kategori')
                    ->searchable(),
                Tables\Columns\TextColumn::make('weight')
                    ->label('Bobot')
                    ->suffix('%')
                    ->sortable(),
                Tables\Columns\TextColumn::make('indicators_count')
                    ->label('Jumlah Indikator')
                    ->counts('indicators'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKpiCategories::route('/'),
            'create' => Pages\CreateKpiCategory::route('/create'),
            'edit' => Pages\EditKpiCategory::route('/{record}/edit'),
        ];
    }
}
