<?php

namespace Modules\PenilaianKinerja\Filament\Resources;

use Modules\PenilaianKinerja\Filament\Resources\RubrikPenilaianResource\Pages;
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
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class RubrikPenilaianResource extends Resource
{
    protected static ?string $model = AppraisalCategory::class;

    protected static ?int $navigationSort = 50;

    public static function getNavigationIcon(): string | \BackedEnum | null
    {
        return 'heroicon-o-list-bullet';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Penilaian Kinerja';
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
                ]),

            Section::make('Indikator Penilaian')
                ->collapsible()
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
                TextColumn::make('indicators_count')
                    ->label('Jumlah Indikator')
                    ->counts('indicators'),
            ])
            ->striped()
            ->persistFiltersInSession()
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()
                        ->label('Ubah'),
                    DeleteAction::make()
                        ->label('Hapus'),
                ])->label('Aksi'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRubrikPenilaian::route('/'),
            'create' => Pages\CreateRubrikPenilaian::route('/create'),
            'edit' => Pages\EditRubrikPenilaian::route('/{record}/edit'),
        ];
    }
}
