<?php

namespace Modules\CBT\Filament\Resources;

use Modules\CBT\Models\Exam;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Tables;
use Illuminate\Support\Facades\Auth;
use Modules\CBT\Filament\Resources\ExamResource\Pages;

class ExamResource extends Resource
{
    protected static ?string $model = Exam::class;
    protected static ?int $navigationSort = 50;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-academic-cap';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'CBT / Ujian';
    }

    public static function getModelLabel(): string
    {
        return 'Ujian';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Ujian (CBT)';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            \Filament\Schemas\Components\Section::make('Detail Ujian')
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->label('Judul Ujian')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\Select::make('question_bank_id')
                        ->label('Bank Soal')
                        ->relationship('questionBank', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),

                    Forms\Components\Textarea::make('description')
                        ->label('Deskripsi / Petunjuk')
                        ->columnSpanFull(),
                ])->columns([
                    'sm' => 2,
                ]),

            \Filament\Schemas\Components\Section::make('Pengaturan Waktu & Akses')
                ->schema([
                    Forms\Components\DateTimePicker::make('start_time')
                        ->label('Waktu Mulai')
                        ->required(),

                    Forms\Components\DateTimePicker::make('end_time')
                        ->label('Waktu Berakhir')
                        ->required(),

                    Forms\Components\TextInput::make('duration_minutes')
                        ->label('Durasi (Menit)')
                        ->numeric()
                        ->required(),

                    Forms\Components\TextInput::make('token')
                        ->label('Token Ujian')
                        ->maxLength(50)
                        ->helperText('Kosongkan jika tidak memakai token (peserta langsung mengerjakan)'),

                    Forms\Components\Select::make('unit_type_id')
                        ->label('Jenjang (Khusus Jenjang)')
                        ->relationship('unitType', 'name')
                        ->searchable()
                        ->preload()
                        ->nullable(),

                    Forms\Components\Select::make('unit_id')
                        ->label('Unit (Khusus Unit)')
                        ->relationship('unit', 'name')
                        ->searchable()
                        ->preload()
                        ->nullable(),
                ])->columns([
                    'sm' => 2,
                ]),

            \Filament\Schemas\Components\Section::make('Opsi Lainnya')
                ->schema([
                    Forms\Components\Toggle::make('show_result')
                        ->label('Tampilkan Nilai ke Peserta')
                        ->default(false),

                    Forms\Components\Toggle::make('is_active')
                        ->label('Status Ujian Aktif')
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
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('questionBank.name')
                    ->label('Bank Soal')
                    ->sortable()
                    ->limit(20),

                Tables\Columns\TextColumn::make('start_time')
                    ->label('Mulai')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_time')
                    ->label('Berakhir')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('duration_minutes')
                    ->label('Durasi')
                    ->suffix(' Mnt')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('question_bank_id')
                    ->label('Bank Soal')
                    ->relationship('questionBank', 'name'),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExams::route('/'),
            'create' => Pages\CreateExam::route('/create'),
            'edit' => Pages\EditExam::route('/{record}/edit'),
        ];
    }
}
