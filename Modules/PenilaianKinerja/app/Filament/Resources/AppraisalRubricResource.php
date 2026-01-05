<?php

namespace Modules\PenilaianKinerja\Filament\Resources;

use Modules\PenilaianKinerja\Filament\Resources\AppraisalRubricResource\Pages;
use Modules\PenilaianKinerja\Models\AppraisalCategory;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class AppraisalRubricResource extends Resource
{
    protected static ?string $model = AppraisalCategory::class;

    protected static ?int $navigationSort = 50;

    public static function getNavigationIcon(): string | \BackedEnum | null
    {
        return 'heroicon-o-list-bullet';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Penilaian 360';
    }

    public static function getModelLabel(): string
    {
        return 'Rubrik Penilaian';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Rubrik Penilaian';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Kategori Penilaian')
                ->schema([
                    TextInput::make('name')
                        ->label('Nama Kategori')
                        ->required()
                        ->placeholder('Contoh: Kompetensi Teknis')
                        ->maxLength(255),
                    TextInput::make('weight')
                        ->label('Bobot (%)')
                        ->numeric()
                        ->required()
                        ->placeholder('Contoh: 40'),
                ]),

            Section::make('Indikator Penilaian')
                ->schema([
                    Repeater::make('indicators')
                        ->relationship('indicators')
                        ->label('')
                        ->schema([
                            TextInput::make('indicator_text')
                                ->label('Teks Indikator')
                                ->required()
                                ->placeholder('Contoh: Mampu mengoperasikan software HRIS')
                                ->columnSpanFull(),
                        ])
                        ->grid(1)
                        ->defaultItems(1)
                ])
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Kategori')
                    ->searchable(),
                TextColumn::make('weight')
                    ->label('Bobot (%)')
                    ->sortable(),
                TextColumn::make('indicators_count')
                    ->label('Jumlah Indikator')
                    ->counts('indicators'),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAppraisalRubrics::route('/'),
            'create' => Pages\CreateAppraisalRubric::route('/create'),
            'edit' => Pages\EditAppraisalRubric::route('/{record}/edit'),
        ];
    }
}
