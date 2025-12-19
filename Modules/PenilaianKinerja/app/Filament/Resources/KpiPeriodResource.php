<?php

namespace Modules\PenilaianKinerja\Filament\Resources;

use Modules\PenilaianKinerja\Filament\Resources\KpiPeriodResource\Pages;
use Modules\PenilaianKinerja\Models\KpiPeriod;
use Filament\Schemas\Schema;
use Filament\Forms\Components;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class KpiPeriodResource extends Resource
{
    protected static ?string $model = KpiPeriod::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationGroup = 'Penilaian Kinerja';

    protected static ?string $modelLabel = 'Periode Penilaian';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Components\Section::make('Detail Periode')
                    ->schema([
                        Components\TextInput::make('name')
                            ->label('Nama Periode')
                            ->placeholder('e.g. Semester Ganjil 2024')
                            ->required()
                            ->maxLength(255),
                        Components\Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->default(true),
                        Components\DatePicker::make('start_date')
                            ->label('Tanggal Mulai')
                            ->required(),
                        Components\DatePicker::make('end_date')
                            ->label('Tanggal Selesai')
                            ->required(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Periode')
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
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
            'index' => Pages\ListKpiPeriods::route('/'),
            'create' => Pages\CreateKpiPeriod::route('/create'),
            'edit' => Pages\EditKpiPeriod::route('/{record}/edit'),
        ];
    }
}
