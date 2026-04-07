<?php

namespace Modules\CBT\Filament\Resources;

use Modules\CBT\Models\Question;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Tables;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Facades\Auth;
use Modules\CBT\Filament\Resources\QuestionResource\Pages;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;

class QuestionResource extends Resource
{
    protected static ?string $model = Question::class;
    protected static ?int $navigationSort = 40;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-question-mark-circle';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'CBT / Ujian';
    }

    public static function getModelLabel(): string
    {
        return 'Soal';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Soal (CBT)';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('Formulir Soal')
                ->columnSpanFull()
                ->tabs([
                    Tab::make('Konfigurasi & Pertanyaan')
                        ->icon('heroicon-o-cog-6-tooth')
                        ->schema([
                            Section::make('Konfigurasi Soal')
                                ->description('Pilih bank soal, tipe, dan tentukan bobot nilai.')
                                ->schema([
                                    Forms\Components\Select::make('question_bank_id')
                                        ->label('Bank Soal')
                                        ->relationship('questionBank', 'name')
                                        ->searchable()
                                        ->preload()
                                        ->required(),

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
                                        ->minValue(0)
                                        ->step(0.1)
                                        ->required()
                                        ->prefix('Poin'),
                                ])->columns(3),

                            Section::make('Konten Pertanyaan')
                                ->description('Tuliskan teks pertanyaan secara lengkap. Gunakan toolbar untuk memformat teks.')
                                ->schema([
                                    Forms\Components\RichEditor::make('content')
                                        ->label('Pertanyaan')
                                        ->required()
                                        ->columnSpanFull()
                                        ->extraInputAttributes(['style' => 'min-height: 400px;']),
                                    
                                    Forms\Components\FileUpload::make('media')
                                        ->label('Gambar Pendukung')
                                        ->image()
                                        ->directory('cbt/questions')
                                        ->columnSpanFull()
                                        ->helperText('Unggah gambar di sini jika pertanyaan ini membutuhkan ilustrasi.'),
                                ]),
                        ]),

                    Tab::make('Pilihan Jawaban')
                        ->icon('heroicon-o-list-bullet')
                        ->visible(fn(Get $get) => $get('type') === 'multiple_choice')
                        ->schema([
                            Section::make('Daftar Pilihan')
                                ->description('Tentukan pilihan jawaban dan tandai jawaban yang benar. (Tips: Aktifkan toggle "Kunci" pada salah satu pilihan)')
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
                                                        ->label('Kunci')
                                                        ->onColor('success')
                                                        ->offColor('gray')
                                                        ->columnSpan(2),

                                                    Forms\Components\Textarea::make('content')
                                                        ->label('Teks Pilihan')
                                                        ->placeholder('Isi jawaban...')
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
                                        ->itemLabel(fn(array $state): ?string => $state['label'] ?? 'Pilihan'),
                                ]),
                        ]),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('questionBank.name')
                    ->label('Bank Soal')
                    ->sortable()
                    ->searchable(),

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
                Tables\Filters\SelectFilter::make('question_bank_id')
                    ->label('Bank Soal')
                    ->relationship('questionBank', 'name'),
            ])
            ->recordActions([
                \Filament\Actions\Action::make('preview')
                    ->label('Preview')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->modalContent(fn ($record) => view('cbt::admin.preview-question', ['record' => $record]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->slideOver(),
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->toolbarActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuestions::route('/'),
            'create' => Pages\CreateQuestion::route('/create'),
            'edit' => Pages\EditQuestion::route('/{record}/edit'),
        ];
    }
}
