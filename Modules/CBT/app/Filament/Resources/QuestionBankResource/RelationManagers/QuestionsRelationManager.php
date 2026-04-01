<?php

namespace Modules\CBT\Filament\Resources\QuestionBankResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Support\Enums\Width;

class QuestionsRelationManager extends RelationManager
{
    protected static string $relationship = 'questions';

    protected static ?string $recordTitleAttribute = 'content';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            \Filament\Schemas\Components\Tabs::make('Formulir Soal')
                ->columnSpanFull()
                ->tabs([
                    \Filament\Schemas\Components\Tabs\Tab::make('Konfigurasi & Pertanyaan')
                        ->icon('heroicon-o-cog-6-tooth')
                        ->schema([
                            Section::make('Konten Pertanyaan')
                                ->icon('heroicon-o-pencil-square')
                                ->description('Tuliskan teks pertanyaan secara lengkap. Gunakan toolbar untuk memformat teks.')
                                ->schema([
                                    \Filament\Schemas\Components\Grid::make(2)
                                        ->schema([
                                            Forms\Components\Select::make('type')
                                                ->label('Tipe Soal')
                                                ->options([
                                                    'multiple_choice' => 'Pilihan Ganda',
                                                    'essay' => 'Essay',
                                                ])
                                                ->default('multiple_choice')
                                                ->required()
                                                ->live()
                                                ->native(false),

                                            Forms\Components\TextInput::make('score_weight')
                                                ->label('Bobot Nilai')
                                                ->numeric()
                                                ->default(1)
                                                ->required()
                                                ->prefix('Poin'),
                                        ]),

                                    Forms\Components\RichEditor::make('content')
                                        ->label('Pertanyaan')
                                        ->required()
                                        ->columnSpanFull()
                                        ->extraInputAttributes(['style' => 'min-height: 400px;']),
                                ]),
                        ]),

                    \Filament\Schemas\Components\Tabs\Tab::make('Pilihan Jawaban')
                        ->icon('heroicon-o-list-bullet')
                        ->visible(fn (\Filament\Schemas\Components\Utilities\Get $get) => $get('type') === 'multiple_choice')
                        ->schema([
                            Section::make('Daftar Pilihan')
                                ->description('Tentukan pilihan jawaban dan tandai satu sebagai jawaban yang benar.')
                                ->schema([
                                    Forms\Components\Repeater::make('options')
                                        ->relationship('options')
                                        ->schema([
                                            \Filament\Schemas\Components\Grid::make(12)
                                                ->schema([
                                                    Forms\Components\TextInput::make('label')
                                                        ->label('Label')
                                                        ->placeholder('A')
                                                        ->required()
                                                        ->columnSpan(2),

                                                    Forms\Components\Toggle::make('is_correct')
                                                        ->label('Benar')
                                                        ->onColor('success')
                                                        ->offColor('gray')
                                                        ->inline(false)
                                                        ->columnSpan(2),

                                                    Forms\Components\Textarea::make('content')
                                                        ->label('Konten Jawaban')
                                                        ->required()
                                                        ->columnSpan(8)
                                                        ->rows(2),
                                                ]),
                                        ])
                                        ->columns(1)
                                        ->defaultItems(4)
                                        ->addActionLabel('Tambah Pilihan')
                                        ->reorderableWithButtons()
                                        ->collapsible()
                                        ->itemLabel(fn (array $state): ?string => $state['label'] ?? 'Pilihan'),
                                ]),
                        ]),
                ]),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->colors([
                        'primary' => 'multiple_choice',
                        'warning' => 'essay',
                    ]),

                Tables\Columns\TextColumn::make('content')
                    ->label('Pertanyaan')
                    ->html()
                    ->limit(50),

                Tables\Columns\TextColumn::make('score_weight')
                    ->label('Bobot')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->slideOver(),
            ])
            ->actions([
                EditAction::make()
                    ->slideOver(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
