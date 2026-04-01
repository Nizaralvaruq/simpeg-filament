<?php

namespace Modules\CBT\Filament\Resources;

use Modules\CBT\Models\ExamSession;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Tables;
use Modules\CBT\Filament\Resources\ExamSessionResource\Pages;

class ExamSessionResource extends Resource
{
    protected static ?string $model = ExamSession::class;
    protected static ?int $navigationSort = 60;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-clipboard-document-check';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'CBT / Ujian';
    }

    public static function getModelLabel(): string
    {
        return 'Sesi Ujian / Nilai';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Data Nilai Ujian (CBT)';
    }

    public static function canCreate(): bool
    {
        return false; // Nilai tidak dibuat manual
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            \Filament\Schemas\Components\Section::make('Detail Sesi & Nilai')
                ->schema([
                    Forms\Components\Select::make('exam_id')
                        ->label('Ujian')
                        ->relationship('exam', 'title')
                        ->disabled(),

                    Forms\Components\Select::make('user_id')
                        ->label('Peserta / Siswa')
                        ->relationship('user', 'name')
                        ->disabled(),

                    Forms\Components\DateTimePicker::make('start_time')
                        ->label('Waktu Mulai')
                        ->disabled(),

                    Forms\Components\DateTimePicker::make('end_time')
                        ->label('Waktu Selesai')
                        ->disabled(),

                    Forms\Components\TextInput::make('score')
                        ->label('Skor Berhasil')
                        ->numeric()
                        ->disabled(),

                    Forms\Components\Toggle::make('force_closed')
                        ->label('Ujian Ditutup Paksa')
                        ->disabled(),
                ])->columns([
                    'sm' => 2,
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama Peserta')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('exam.title')
                    ->label('Ujian')
                    ->searchable()
                    ->sortable()
                    ->limit(20),

                Tables\Columns\TextColumn::make('start_time')
                    ->label('Waktu Mulai')
                    ->dateTime('d M Y H:i:s')
                    ->sortable(),

                Tables\Columns\TextColumn::make('score')
                    ->label('Skor Akhir')
                    ->color(fn (string $state): string => match (true) {
                        floatval($state) >= 70 => 'success',
                        default => 'danger',
                    })
                    ->sortable(),

                Tables\Columns\IconColumn::make('force_closed')
                    ->label('Ditutup Paksa')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('exam_id')
                    ->label('Ujian')
                    ->relationship('exam', 'title'),
            ])
            ->recordActions([
                \Filament\Actions\ViewAction::make(),
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
            'index' => Pages\ListExamSessions::route('/'),
            // 'create' => Pages\CreateExamSession::route('/create'), // tidak dibuat manual
            'view' => Pages\ViewExamSession::route('/{record}'),
            // 'edit' => Pages\EditExamSession::route('/{record}/edit'), // tidak diedit manual
        ];
    }
}
