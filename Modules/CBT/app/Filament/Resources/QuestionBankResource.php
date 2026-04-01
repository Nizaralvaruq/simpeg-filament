<?php

namespace Modules\CBT\Filament\Resources;

use Modules\CBT\Models\QuestionBank;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Tables;
use Illuminate\Support\Facades\Auth;
use Modules\CBT\Filament\Resources\QuestionBankResource\Pages;
use Modules\CBT\Filament\Resources\QuestionBankResource\RelationManagers;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Utilities\Get;

class QuestionBankResource extends Resource
{
    protected static ?string $model = QuestionBank::class;
    protected static ?int $navigationSort = 30;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-folder-open';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'CBT / Ujian';
    }

    public static function getModelLabel(): string
    {
        return 'Bank Soal';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Bank Soal (CBT)';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            \Filament\Schemas\Components\Section::make('Informasi Bank Soal')
                ->schema([
                    Forms\Components\TextInput::make('code')
                        ->label('Kode Bank Soal')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),

                    Forms\Components\TextInput::make('name')
                        ->label('Nama Bank Soal')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\Select::make('subject_id')
                        ->label('Mata Pelajaran')
                        ->relationship('subject', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->live()
                        ->afterStateUpdated(function (Set $set, $state) {
                            if ($state) {
                                $subject = \Modules\CBT\Models\Subject::find($state);
                                if ($subject) {
                                    $set('unit_type_id', $subject->unit_type_id);
                                }
                            } else {
                                $set('unit_type_id', null);
                            }
                        }),

                    Forms\Components\Select::make('unit_type_id')
                        ->label('Jenjang')
                        ->relationship('unitType', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->helperText('Otomatis terisi dari mata pelajaran')
                        ->disabled()
                        ->dehydrated(),

                    Forms\Components\Toggle::make('is_active')
                        ->label('Status Aktif')
                        ->default(true),
                ])->columns([
                    'sm' => 2,
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Bank Soal')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('subject.name')
                    ->label('Mata Pelajaran')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('unitType.name')
                    ->label('Jenjang')
                    ->sortable()
                    ->badge(),

                Tables\Columns\TextColumn::make('questions_count')
                    ->label('Jumlah Soal')
                    ->counts('questions')
                    ->sortable()
                    ->badge()
                    ->color('info'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('subject_id')
                    ->label('Mata Pelajaran')
                    ->relationship('subject', 'name'),

                Tables\Filters\SelectFilter::make('unit_type_id')
                    ->label('Jenjang')
                    ->relationship('unitType', 'name'),
            ])
            ->recordActions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->toolbarActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\QuestionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuestionBanks::route('/'),
            'create' => Pages\CreateQuestionBank::route('/create'),
            'edit' => Pages\EditQuestionBank::route('/{record}/edit'),
        ];
    }
}
